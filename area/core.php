
<?php
/*
trait core
{

}
##write (use core,...;) in childes if use trait;
*/
abstract class core             ## can't create object from abstract directly
{
    static PDO|null $db=null;
    public function set_db(PDO $db): void
    {
        self::$db = PDO_SQL::$PDO;
        PDO_SQL::$PDO = $db;
    }
    public function restore_db():void
    {
        if (self::$db instanceof PDO)
            PDO_SQL::$PDO = self::$db;
    }
    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        $this->restore_db();
    }
    public function get_vars($valued=true): array
    {
        return array_filter((array) $this, static fn(string $key): bool => !str_starts_with($key, "\0"), ARRAY_FILTER_USE_KEY,);
//        return ((new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC));
    }
    static function properties():array
    {
//        $properties = get_class_vars(get_called_class()); // Return public|protected
//        $properties = get_object_vars(new static()); // Return public   # new static() => create object from called class  // don`t work when class has constructor
        $class = new ReflectionClass(get_called_class());
        $publicProperties = $class->getProperties(ReflectionProperty::IS_PUBLIC);
        $properties = array_map(fn($v)=>$v->name,$publicProperties);
        $staticProperties = $class->getProperties(ReflectionProperty::IS_STATIC);
        $staticProperties = array_map(fn($v)=>$v->name,$staticProperties);
//        if (($key = array_search('db', $properties)) !== false) {
//            unset($properties[$key]);
//        }
        $properties = array_diff($properties, $staticProperties);
        return $properties;
    }
    public function load($array): bool
    {
        if (!is_array($array))
            return false;
        $properties = array_keys($this->get_vars());
        foreach ($array as $item=>$value){
            if (in_array($item, $properties))
                @$this->{$item}=$value;
        }
        return true;
    }
    public function value_of($attribute){
        return $this->{$attribute};
    }
    public function clear():void
    {
        foreach ($this as $item=>$value)
            @$this->{$item}=null;
    }
    static function table(string $tableName=null): ORM_table
    {
        $tableName ??= strtolower(get_called_class());
        $properties = self::properties();
//        if (($key = array_search('ID', $properties)) != false)
//            unset($properties[$key]);
        return new ORM_table($tableName, $properties);
    }

    static function insert(array $data, string $tableName=null)
    {
        $tableName ??= strtolower(get_called_class());
        $orm = new ORM($tableName);
        return $orm->insert_array($data);
    }

    static function get(...$con): ORM|array
    {
        if (is_array(@$con[0]))
            $con = array_merge(...$con);
        $table = strtolower(get_called_class());
        $orm = new ORM($table);
        if (@$con[0] === '_'){  // Foresee get function without ->do  get('_');
            unset($con[0]);
            return $orm->set_condition($con, 'select', true);
        }
        if (isset($con['_'])){  // Foresee get function without ->do  get(_:0);
            unset($con['_']);
            return $orm->set_condition($con, 'select', true);
        }
        /*  // has interference when first value == 0
        if (@$con[0]===0){  // For use get function without ->do  get(0);
            unset($con[0]);
            return $orm->set_condition($con, 'select', true);
        }

        // before that used get() without ->do , so when need set null, has, limit & etc was used by get('...')->has()
        if (in_array('...', $con)){ //
            unset($con[0]);
        }
        */

        return $orm->set_condition($con);
    }

    static function count_rows(...$con): ORM|int
    {
        if (is_array(@$con[0]))
            $con = array_merge(...$con);
        $table = strtolower(get_called_class());
        $orm = new ORM($table);

        if (isset($con['_'])){  // Foruse get function without ->do  get(_:0);
            unset($con['_']);
            return $orm->set_condition($con, 'count', true);
        }
        if (@$con[0]===0){  // Foruse get function without ->do  get(0);
            unset($con[0]);
            return $orm->set_condition($con, 'count', true);
        }
        if (in_array('...', $con)){ // When use get function on ->set() and want not be empty args // Not matter
            unset($con[0]);
        }

        return $orm->set_condition($con, 'count');
    }
    static function last_ID(string $table=null): bool|int
    {
        $table ??= strtolower(get_called_class());
        return ORM::$DB->last_id($table);
    }

    public function set(...$con): ORM
    {
//        if (!$con)
//            $con = ['...'];
        return self::get(...$con)->limit(1)->set_object($this);
    }
    public function set_byID(int $ID=0): ORM|bool|int
    {
        if (!$ID)
            if ($this->ID)
                $ID = $this->ID;
            else{
                $this->clear();
                return false;
            }
        return self::get(["ID"=>$ID])->limit(1)->set_object($this);
    }
    public function check():bool
    {
        return (bool)$this->ID;
    }
    public function save(string|null $table='',bool $newRow=false): bool|int
    {
        if (!$table) $table = strtolower(get_class($this));
        $vars = $this->get_vars(false);
        if (@$vars['ID'] && !$newRow){
            return $this->ID = ORM::$DB->update_row($table,$vars);
        }else{
            return $this->ID = ORM::$DB->insert_row($table,$vars);
        }
    }
    public function delete(string $table=''): bool
    {
        if (!$table){$table = strtolower(get_class($this));}
        $vars = $this->get_vars(false);
        if (@$vars['ID']){
            return ORM::$DB->delete_row($table,$vars['ID']);
        }
        return false;
    }
    public function change($attribute,$value, $ID=null, $table=null): bool
    {
        if (!$table){$table = strtolower(get_class($this));}
        if (!$ID){$ID = $this->ID;}
        return ORM::$DB->update_one_value($table,$ID,$attribute,$value);
    }
    public function __call(string $name, array $arguments):bool
    {
        // TODO: Implement __call() method.
        $this->$name = $arguments[0];
        if (in_array($name, self::properties()))
            return $this->change($name, $this->$name);
        return false;
    }
}