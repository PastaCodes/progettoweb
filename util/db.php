<?php
if (SETTINGS['hosted-locally']) {
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'isifitgems';
} else {
    // Not implemented
}
$db = new mysqli($servername, $username, $password, $dbname);

require __DIR__ . "/../classes/DatabaseObject.php";

/**
 * @param DatabaseObject[] $objectInfos The information of the query to run,
 * any parameter contained in the objects will be checked by the function's query.
 * @return string The query string to run on the database.
 */
function buildQuery(array &$objectInfos): ?string {
    global $db;
    // No tables provided
    if (empty($objectInfos)) {
        return null;
    }
    $joins = [];
    $conditions = [];
    foreach ($objectInfos as $index => $objectInfo) {
        // Handle join conditions
        if ($objectInfo->join_conditions) {
            foreach ($objectInfo->join_conditions as $targetTable => $columns) {
                $fromColumn = key($columns);
                $toColumn = $columns[$fromColumn];
                $joins[] = "INNER JOIN {$targetTable} ON $targetTable.$toColumn = $objectInfo->table.$fromColumn";
            }
        }
        // Add conditions for this table
        foreach ($objectInfo->properties as $column => $value) {
            $value = $db->real_escape_string($value);
            $conditions[] = "$objectInfo->table.$column = '$value'";
        }
    }
    // Build the final query
    $query = "SELECT * FROM " . $objectInfos[0]->table . " " . implode(" ", $joins);
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    return $query;
}

/**
 * @param DatabaseObject[] $objectInfos The information of the query to run,
 * any parameter contained in the objects will be checked by the function's query.
 * @return array[] | null A list of associative arrays containing the [column] => value
 * pairs of the query result. 
 */
function dbFind(array $objectInfos): ?array {
    global $db;
    // Get the query from the info
    $query = buildQuery($objectInfos);
    if (!$query) {
        return null;
    }
    // Run the query
    $result = $db->query($query);
    if ($result === false) {
        return null;
    }
    // Fetch and return results as associative arrays
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

/**
 * @param DatabaseObject[] $objectInfos The information of the query to run,
 * any parameter contained in the objects will be checked by the function's query.
 * @return array[] | null A single associative array containing the first object found 
 * from the database. 
 */
function dbFindOne(array $objectInfos): ?array {
    global $db;
    // Get the query from the info
    $query = buildQuery($objectInfos);
    if (!$query) {
        return null;
    }
    // Run the query
    $result = $db->query($query);
    if ($result === false) {
        return null;
    }
    // Fetch and return results as associative arrays
    while ($row = $result->fetch_assoc()) {
        return $row;
    }
    return null;
}
/**
 * Exaxmples:
 *
 * These two snippets do the same thing:
 * SELECT * FROM product_base INNER JOIN price_range ON price_range.product = product_base.code_name
 *
 * $querySearch = new DatabaseObject("product_base");
 * $querySearch->join_conditions["price_range"] = ["code_name" => "product"];
 * $results = dbFind([$querySearch]);
 *
 * $results = dbFind([new DatabaseObject("product_base", [], ["price_range" => ["code_name" => "product"]])]);
 */
?>
