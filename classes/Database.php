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
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    /**
     * Executes a SELECT query and returns multiple rows.
     *
     * @param string $table The name of the table to query.
     * @param array  $filter An associative array of conditions for the WHERE clause.
     * Example: ['id' => 1, 'status' => 'active']
     * @param array $options An array of additional options for the query.
     * - 'limit' => int (limits the number of results)
     * - 'distinct' => bool (selects distinct rows)
     * - 'order_by' => array (specifies sorting, e.g., ['column' => 'ASC'])
     * @return array An array of associative arrays representing the rows.
     */
    public function find(string $table, array $filter = [], array $options = []): array {
        $whereClause = $this->buildWhereClause($filter);
        // Add the other options like limit, distinct, etc..
        $limit = isset($options['limit']) ? 'LIMIT ' . (int)$options['limit'] : '';
        $distinct = (isset($options['distinct']) && $options['distinct'] == true) ? 'DISTINCT ' : '';
        $orderBy = isset($options['order_by']) ? 'ORDER BY ' . $this->buildOrderByClause($options['order_by']) : '';
        // Create the base statement
        $sql = "SELECT $distinct * FROM $table $whereClause $orderBy $limit";
        $stmt = $this->prepareStatement($sql, $filter);
        // Execute the statement
        $stmt->execute();
        $result = $stmt->get_result();
        // Get the results from the operation
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Executes a SELECT query and returns a single row.
     *
     * @param string $table The name of the table to query.
     * @param array $filter An associative array of conditions for the WHERE clause.
     * @param array $options Additional options for the query (see `find` method).
     * @return array|null An associative array representing the row, or null if no rows match.
     */
    public function findOne(string $table, array $filter = [], array $options = []): ?array {
        $result = $this->find($table, $filter, array_merge($options, ['limit' => 1]));
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
        // Create the base statement
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->prepareStatement($sql, $data);
        // Execute the statement
        $stmt->execute();
        // Return the id of the updated value
        return $this->db->insert_id;
    }

    /**
     * Updates rows in the specified table.
     *
     * @param string $table The name of the table to update.
     * @param array $filter An associative array of conditions for the WHERE clause.
     * @param array $data An associative array of column-value pairs to update.
     * @return int The number of affected rows.
     */
    public function update(string $table, array $filter = [], array $data): int {
        // Use the data keys to create a series of "column = ?, ..." components of the query
        $setClause = implode(', ', array_map(fn($key) => "$key = ?", array_keys($data)));
        // Create the base query
        $whereClause = $this->buildWhereClause($filter);
        $sql = "UPDATE $table SET $setClause $whereClause";
        // Use both the data and the filter to replace the ?
        $stmt = $this->prepareStatement($sql, array_merge($data, $filter));
        // Execute the query
        $stmt->execute();
        // Return the count of modified rows
        return $stmt->affected_rows;
    }
    
    /**
     * Deletes rows from the specified table.
     *
     * @param string $table The name of the table to delete from.
     * @param array $filter An associative array of conditions for the WHERE clause.
     * @return int The number of affected rows.
     */
    public function delete(string $table, array $filter = []): int {
        // Create the base query
        $whereClause = $this->buildWhereClause($filter);
        $sql = "DELETE FROM $table $whereClause";
        $stmt = $this->prepareStatement($sql, $filter);
        // Execute the query
        $stmt->execute();
        // Return the count of modified rows
        return $stmt->affected_rows;
    }

    /**
     * Destructor for the Database class. Closes the database connection.
     */
    public function __destruct() {
        $this->db->close();
    }

    /**
     * Builds the WHERE clause for a query from a filter array.
     *
     * @param array $filter An associative array of conditions.
     * @return string The WHERE clause, or an empty string if no conditions are provided.
     */
    private function buildWhereClause(array $filter): string {
        if (empty($filter)) {
            return "";
        }
        // Create a WHERE "key" = ? AND "key" = ? AND ... statement
        $conditions = array_map(fn($key) => "$key = ?", array_keys($filter));
        return "WHERE " . implode(" AND ", $conditions);
    }

    /**
     * Builds the ORDER BY clause for a query from an options array.
     *
     * @param array $orderBy An associative array specifying columns and sorting directions.
     * @return string The ORDER BY clause, or an empty string if no sorting is specified.
     */
    private function buildOrderByClause($orderBy): string {
        if (is_array($orderBy)) {
            $orderByClauses = [];
            // Create a string containing "column ASC, column DESC, ...."
            foreach ($orderBy as $column => $direction) {
                $orderByClauses[] = "$column " . strtoupper($direction);
            }
            return implode(', ', $orderByClauses);
        }
        return '';
    }

    /**
     * Prepares a SQL statement and binds parameters.
     *
     * @param string $sql The SQL query string.
     * @param array $params The parameters to bind to the query.
     * @return mysqli_stmt The prepared statement.
     * @throws Exception If statement preparation fails.
     */
    private function prepareStatement(string $sql, array $params): object {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            die("Failed to prepare a statement: " . $this->mysqli->error);
        }
        if (!empty($params)) {
            // TODO: maybe change this so parameters can have a type (probably not required though)
            $types = str_repeat("s", count($params));
            // Get all values from the array and set them inside the query as strings
            $stmt->bind_param($types, ...array_values($params));
        }
        return $stmt;
    }
}

?>
