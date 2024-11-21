<?php
class ORM_table
{
    static PDO_SQL|null $DB=null;
    private string $table;
    private array $properties;

    public function __construct(string $table, $properties)
    {
        if (!self::$DB)
            self::$DB = ORM::$DB;
        $this->table = $table;
        $this->properties = $properties;
        return $this;
    }

    public function create($replace=true): bool
    {
        return self::$DB->create_table($this->table, $this->properties, $replace);
    }
    public function delete(): bool
    {
        return self::$DB->delete_table($this->table);
    }
    public function update(): bool
    {
        if (!self::$DB->exist_table($this->table)){
            return self::create();
        }
        $exist = self::$DB->column_of_table($this->table);
        $forDelete = array_diff($exist, $this->properties);
//        var_dump($exist, $this->properties);
//        exit();
        if (($key = array_search('ID', $forDelete)) != false)
            unset($forDelete[$key]);
        $forAdd = array_diff($this->properties, $exist);
        return self::$DB->update_table($this->table, $forAdd, $forDelete);
    }
    public function rename($lastName, $newName): bool
    {
        return self::$DB->rename_table($lastName, $newName);
    }
}