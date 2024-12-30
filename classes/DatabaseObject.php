<?php
class DatabaseObject {
    public string $table;
    public array $properties;
    public ?array $join_conditions;
    
    /**
     * @param string $table The name of the table
     * @param array $join_conditions List of join conditions in the format ['target_table' => ['from_column' => 'to_column']]
     * @param array $properties Associative array of [column => value] to filter this table
     */
    function __construct(string $table, array $properties = [], array $join_conditions = []) {
        $this->table = $table;
        $this->properties = $properties;
        $this->join_conditions = $join_conditions;
    }
}
?>
