<?php

/**
 * @property null do
 */
class ORM
{
    static ?PDO_SQL $DB=null;

    private string $table;
    private array $con=[];
    private array $limit=[];
    private array $columns=[];
    private bool $reverse=false;
    private array $notNULL=[];
    private array $isNULL=[];
    private array $executes=[];
    private ?object $objectForSet=null;
    private string $action='';


    ##############
    public function set_condition($conditions, string $action='select', bool $final=false): static|array|int
    {
        $this->action = $action;
        $this->con = $conditions;
        if ($final){
            $query = $this->create_query();
            return match ($this->action) {
                'select' => self::$DB->search_where($query, $this->executes),
                'count' => (int)self::$DB->search_where($query, $this->executes)[0]['COUNT(*)'],
                default => null,
            };
        }
        return $this;
    }

    /**
     * @param int $length_start length we want OR start (from zero)
     * @param int $count When use start
     * @return $this
     */
    public function limit(int $length_start=0, int $count=0): static
    {
        if ($count>0){
            $this->limit[0] = $length_start;
            $this->limit[1] = $count;
        } else{
            $this->limit[0] = 0;
            $this->limit[1] = $length_start;
        }
        return $this;
    }
    public function select(...$columns): static // Select some columns
    {
        if (is_array(@$columns[0]))
            $columns = array_merge(...$columns);

        if (is_array($columns))
            $this->columns = $columns;
        return $this;
    }
    public function reverse(): static
    {
        $this->reverse = true;
        return $this;
    }
    public function table(string $table): static
    {
        $this->table = $table;
        return $this;
    }
    public function has(...$columns): static
    {
        if (is_array(@$columns[0]))
            $columns = array_merge(...$columns);
        if (is_array($columns))
            $this->notNULL = $columns;
        return $this;
    }
    public function null(...$columns): static
    {
        if (is_array(@$columns[0]))
            $columns = array_merge(...$columns);
        if (is_array($columns))
            $this->isNULL = $columns;
        return $this;
    }

    public function create_query():string
    {
        $query = match($this->action){
            'select'=> "SELECT ",
            'count'=> "SELECT COUNT(*) ",
        };
        if ($this->action=='select'){
            if ($this->columns){
                $query .= '`'.implode('`,`', $this->columns).'` ';
            }
            else
                $query .= "* ";
        }
        $query .= "FROM `$this->table` ";

        if ($this->con || $this->notNULL || $this->isNULL){
            $query .= "WHERE ";
            if ($this->con){
                $this->main_query($query, $this->con);
            }
            if ($this->notNULL){
                foreach ($this->notNULL as $item)
                    $query .= "`$item` != '' AND ";
            }
            if ($this->isNULL){
                foreach ($this->isNULL as $item)
                    $query .= "(`$item` IS NULL OR `$item` = '') AND ";
            }
            if (str_ends_with($query, "AND "))
                $query = substr($query, 0, strlen($query)-4);
        }
        $query = rtrim($query, "WHERE ")." ";
        if (!$this->reverse)
            $query .= "ORDER BY `ID` ";
        else
            $query .= "ORDER BY `ID` DESC ";
        if ($this->limit)
            $query .= "LIMIT ".$this->limit[0].",".$this->limit[1];
//        echo $query;
        return $query;
    }

    private function main_query(&$query, $conditions, $referred=false)
    {
        if ($referred)  // When set som conditions on array and must replace by ()
            $query .= "( ";
        foreach ($conditions as $item => $value) {
            if (is_array($value)){
                $this->main_query($query, $value, true);
                continue;
            }
            $item = str_replace('^','',$item);  // Example ['mode!='=>1, 'mode^!='=>2]
            if (!$value) {
                if (str_contains($item, '~')) {
                    $this->reverse = true;
                    continue;
                }
                if (str_contains($item, '@')) {   // Example @100@2000@
                    $limit = explode('@', $item);
                    array_shift($limit);
                    $this->limit = $limit;
                    continue;
                }
            }
            if ($value === null) {
                if (str_ends_with($item, '!=')) {
                    $item = str_replace('!=', '', $item);
                    $this->notNULL[] = $item;
                } else {
                    $item = str_replace('==', '', $item);
                    $this->isNULL[] = $item;
                }
            } else {
                if (str_starts_with($item, '|')) {  // Or ||
                    $item = str_replace('|', '', $item);
                    if (str_ends_with($query, "AND ")){
                        $query = substr($query,0, strlen($query)-4);
                    }
                    $query .= "OR ";
                }

                if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $item)) {
                    if ($value === '')
                        $query .= "(`$item` = '' OR `$item` IS NULL) AND ";
                    else{
                        $query .= "`$item` = ? AND ";
                        $this->executes[] = $value;
                    }
                } else {
                    // Has condition
                    $decimalDigitsCount = (str_contains($value, '.') ?strlen($value) - strpos($value, '.') - 1:0);

                    if (str_ends_with($item, '==')) {
                        $item = str_replace('=', '', $item);
                        $query .= "`$item` = ? AND ";
                        $this->executes[] = $value;
                    } elseif (str_ends_with($item, '>=')) {
                        $item = str_replace('>=', '', $item);
                        $query .= "CAST(`$item` AS DECIMAL(10, ".$decimalDigitsCount.") ) >= ? AND ";
                        $this->executes[] = $value;
                    } elseif (str_ends_with($item, '<=')) {
                        $item = str_replace('<=', '', $item);
                        $query .= "CAST(`$item` AS DECIMAL(10, ".$decimalDigitsCount.") ) <= ? AND ";
                        $this->executes[] = $value;
                    } elseif (str_ends_with($item, '>')) {
                        $item = str_replace('>', '', $item);
                        $query .= "CAST(`$item` AS DECIMAL(10, ".$decimalDigitsCount.") ) > ? AND ";
                        $this->executes[] = $value;
                    } elseif (str_ends_with($item, '<')) {
                        $item = str_replace('<', '', $item);
                        $query .= "CAST(`$item` AS DECIMAL(10, ".$decimalDigitsCount.") ) < ? AND ";
                        $this->executes[] = $value;
                    } elseif (str_ends_with($item, '!=')) {
                        $item = str_replace('!=', '', $item);
                        $query .= "`$item` <> ? AND ";
                        $this->executes[] = $value;
                    }
                }
            }
        }
        if ($referred){
            if (str_ends_with($query, "AND "))
                $query = substr($query, 0, strlen($query)-4);
            $query .= " ) AND ";
        }
    }


    public function insert_array(array $data): bool
    {
        if (count($data)==0)
            return false;
        $keys = array_keys($data[0]);
        if (!is_array($keys))
            return false;
        $keys_number= array_filter($keys, function ($i){return is_numeric($i);});

        $query = "INSERT INTO `$this->table` (";

        if ($keys_number){  // First row = column  ||  Second row = data
            if (!(count($data))>1)
                return false;
            $keys = array_values($data[0]);
            array_shift($data);
        }else{
            // Columns as keys of all rows
            // Just for document
        }
        $query .= implode(", ", $keys);
        $query = rtrim($query, ", ");
        $query .= ") VALUES";
        foreach ($data as $datum){
            $query .= "(";
            $query .= str_repeat("?,", count($datum));
            $query = rtrim($query, ",");
            $query .= "), ";
            $datum = array_values($datum);
            array_push($this->executes, ...$datum);
        }
        $query = substr_replace($query,  ";", -2, 2);
        return self::$DB->insert_multiple_data($query, $this->executes);
    }

    public function set_object(core $object): static
    {
        $this->objectForSet = $object;
        return $this;
    }
    ####################################################################

    public function __construct(string $table)
    {
        $this->table = $table;
    }
    public function __get(string $name) // when use ->do
    {
        // TODO: Implement __get() method.
        // Make and send query
        $query = $this->create_query();
        return match ($this->action) {
            'select' => self::$DB->search_where($query, $this->executes),
            'count' => (int)self::$DB->search_where($query, $this->executes)[0]['COUNT(*)'],
            default => [],
        };
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        if ($this->objectForSet){
            $query = $this->create_query();
            $data = self::$DB->search_where($query, $this->executes);
            if (count($data)>0)
                return $this->objectForSet->load($data[0]);
            $this->objectForSet->clear();
            return false;
        }
        return null;
    }
    public function __toString(): string
    {
        // TODO: Implement __toString() method.
        return $this->create_query();
    }
}