<?php

/**
 * Database class for simplified interaction with a MySQL database.
 * Provides methods for common operations like SELECT, INSERT, UPDATE, and DELETE.
 */
class Database {
    /**
     * @var mysqli $db The database connection instance.
     */
    private $db;

    /**
     * Constructor for the Database class.
     *
     * @param string $host The hostname of the database server.
     * @param string $username The username for the database connection.
     * @param string $password The password for the database connection.
     * @param string $dbname The name of the database.
     * @throws Exception If the connection fails.
     */
    public function __construct(string $host, string $username, string $password, string $dbname) {
        // Connect to the database using the parameters
        $this->db = new mysqli($host, $username, $password, $dbname);
        if ($this->db->connect_error) {
            die('Connection failed: ' . $this->db->connect_error);
        }
    }

    /**
     * Executes a SELECT query and returns multiple rows.
     *
     * @param string $table The name of the table to query.
     * @param array $joins An array of JOIN clauses. Each element should be an associative array with keys:
     * - 'type' => The type of join (INNER, LEFT, RIGHT, etc.).
     * - 'table' => The name of the table to join.
     * - 'on' => The ON condition for the join.
     * - 'using' => The single column to join on, must have same name on both tables, alternative to the 'on' keyword.
     * @param array  $filters An associative array of conditions for the WHERE clause.
     * Example: ['id' => 1, 'status' => 'active', 'id' => [1, 2, 3]]
     * @param array $options An array of additional options for the query.
     * - 'limit' => int (limits the number of results)
     * - 'offset' => int (offsets the starting point of the query)
     * - 'distinct' => bool (selects distinct rows)
     * - 'order_by' => array (specifies sorting, e.g., ['column' => 'ASC'])
     * @return array An array of associative arrays representing the rows.
     */
    public function find(string $table, array $joins = [], array $filters = [], array $options = []): array {
        // Create the sql query
        $sql = sprintf(
            "SELECT %s * FROM %s %s %s %s %s",
            $options['distinct'] ?? false ? 'DISTINCT' : '',
            $table,
            $this->build_join_clause($joins),
            $this->build_where_clause($filters),
            $this->build_order_by_clause($options['order_by'] ?? []),
            isset($options['limit']) ? 'LIMIT ' . intval($options['limit']) : '',
            isset($options['offset']) ? 'OFFSET ' . intval($options['offset']) : ''
        );
        // Execute the query 
        $stmt = $this->prepare_statement($sql, $filters);
        $stmt->execute();
        // Get the results from the operation
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Executes a SELECT query and returns a single row.
     *
     * @param string $table The name of the table to query.
     * @param array $joins An array of JOIN clauses (see `find` method for structure).
     * @param array $filters An associative array of conditions for the WHERE clause.
     * @param array $options Additional options for the query (see `find` method).
     * @return array|null An associative array representing the row, or null if no rows match.
     */
    public function find_one(string $table, array $joins = [], array $filters = [], array $options = []): ?array {
        $result = $this->find($table, $joins, $filters, array_merge($options, ['limit' => 1]));
        return $result[0] ?? null;
    }

    /**
     * Inserts a row into the specified table.
     *
     * @param string $table The name of the table to insert into.
     * @param array $data  An associative array of column-value pairs.
     * @return int The ID of the newly inserted row.
     */
    public function insert(string $table, array $data): int {
        // Use the data keys as the columns to insert
        $columns = implode(', ', array_keys($data));
        // Create placeholders per column
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        // Create the query 
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        // Execute the query 
        $stmt = $this->prepare_statement($sql, $data);
        $stmt->execute();
        // Return the id of the updated value
        return $this->db->insert_id;
    }

    /**
     * Updates rows in the specified table.
     *
     * @param string $table The name of the table to update.
     * @param array $filters An associative array of conditions for the WHERE clause.
     * @param array $data An associative array of column-value pairs to update.
     * @return int The number of affected rows.
     */
    public function update(string $table, array $filters = [], array $data): int {
        // Use the data keys to create a series of "column = ?, ..." components of the query
        $setClause = implode(', ', array_map(fn($key) => "$key = ?", array_keys($data)));
        // Create the base query
        $sql = "UPDATE $table SET $setClause " . $this->build_where_clause($filters);
        // Use both the data and the filter to replace the ?
        $stmt = $this->prepare_statement($sql, array_merge($data, $filters));
        // Execute the query
        $stmt->execute();
        // Return the count of modified rows
        return $stmt->affected_rows;
    }
    
    /**
     * Deletes rows from the specified table.
     *
     * @param string $table The name of the table to delete from.
     * @param array $filters An associative array of conditions for the WHERE clause.
     * @return int The number of affected rows.
     */
    public function delete(string $table, array $filters = []): int {
        // Create the base query
        $sql = "DELETE FROM $table " . $this->build_where_clause($filters);;
        $stmt = $this->prepare_statement($sql, $filters);
        // Execute the query
        $stmt->execute();
        // Return the count of modified rows
        return $stmt->affected_rows;
    }

    /**
     * Builds the WHERE clause for a query from a filter array.
     *
     * @param array $filters An associative array of conditions.
     * @return string The WHERE clause, or an empty string if no conditions are provided.
     */
    private function build_where_clause(array $filters): string {
        if (empty($filters)) {
            return '';
        }
        $conditions = [];
        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                // If array, create IN clause
                $placeholders = implode(', ', array_fill(0, count($value), '?'));
                $conditions[] = "$key IN ($placeholders)";
            } else {
                // Else write a simple statement
                $conditions[] = "$key = ?";
            }
        }
        return 'WHERE ' . implode(' AND ', $conditions);
    }

    /**
     * Builds the JOIN clause for a query from an array of joins.
     *
     * @param array $joins An array of JOIN clauses, both using ON and USING clauses.
     * @return string The JOIN clause, or an empty string if no joins are provided.
     */
    private function build_join_clause(array $joins): string {
        if (empty($joins))
            return '';
        return implode(' ', array_map(function ($join) {
            $type = strtoupper($join['type'] ?? 'INNER');
            $table = $join['table'];
            if (isset($join['using']))
                return "$type JOIN $table USING ({$join['using']})";
            if (isset($join['on']))
                return "$type JOIN $table ON {$join['on']}";
            die('Join must specify either \'on\' or \'using\'.');
        }, $joins));
    }

    /**
     * Builds the ORDER BY clause for a query from an options array.
     *
     * @param array $orderBy An associative array specifying columns and sorting directions.
     * @return string The ORDER BY clause, or an empty string if no sorting is specified.
     */
    private function build_order_by_clause($orderBy): string {
        if (empty($orderBy))
            return '';
        return 'ORDER BY ' . implode(', ', array_map(fn($col, $dir) => "$col " . strtoupper($dir), array_keys($orderBy), $orderBy));
    }

    /**
     * Prepares a SQL statement and binds parameters.
     *
     * @param string $sql The SQL query string.
     * @param array $params The parameters to bind to the query.
     * @return mysqli_stmt The prepared statement.
     * @throws Exception If statement preparation fails.
     */
    private function prepare_statement(string $sql, array $params): object {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            die('Failed to prepare a statement: ' . $this->mysqli->error);
        }
        if (!empty($params)) {
            // Helper function to determine parameter type
            $getType = function($param) {
                if (is_array($param)) {
                    return implode('', array_map(function($p) {
                        return is_int($p) ? 'i' : (is_float($p) ? 'd' : 's');
                    }, $param));
                }
                return is_int($param) ? 'i' : (is_float($param) ? 'd' : 's');
            };
            // Flatten params and determine their types
            $types = implode('', array_map($getType, $params));
            // https://stackoverflow.com/questions/1319903/how-to-flatten-a-multidimensional-array
            $values = [];
            array_walk_recursive($params, function($v) use (&$values) { $values[] = $v; });
            // Bind the parameters to the statement
            $stmt->bind_param($types, ...$values);
        }
        return $stmt;
    }

    /**
     * Destructor for the Database class. Closes the database connection.
     */
    public function __destruct() {
        $this->db->close();
    }
}

?>
