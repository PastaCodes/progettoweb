<?php

class Database {
    private $db;

    public function __construct(string $host, string $username, string $password, string $dbname) {
        // Connect to the database using the parameters
        $this->db = new mysqli($host, $username, $password, $dbname);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

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

    public function findOne(string $table, array $filter = [], array $options = []): ?array {
        $result = $this->find($table, $filter, array_merge($options, ['limit' => 1]));
        return $result[0] ?? null;
    }

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

    public function __destruct() {
        $this->db->close();
    }

    private function buildWhereClause(array $filter): string {
        if (empty($filter)) {
            return "";
        }
        // Create a WHERE "key" = ? AND "key" = ? AND ... statement
        $conditions = array_map(fn($key) => "$key = ?", array_keys($filter));
        return "WHERE " . implode(" AND ", $conditions);
    }

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
