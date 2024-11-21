<?php
class PDO_SQL
{
    public string|null $DB_ERRORS=null;
    protected array $CONFIG;
    static PDO|null $PDO;
    static PDO|null $LAST_PDO;

    function __construct($DB_Config)
    {
        if (!in_array($DB_Config['mode'],PDO::getAvailableDrivers())){
            $this->DB_ERRORS="Database mode is not supported.".EOL;;
        }elseif(isset($DB_Config['hostname'],$DB_Config['username'],$DB_Config['password'],$DB_Config['dbname'])){
            $this->CONFIG=$DB_Config;
        }else{$this->DB_ERRORS="Database config is not set!".EOL;}
    }
    function __destruct()
    {
        self::$PDO = null;
    }
    function connect(int $timeout=null): bool
    {
        if (!$this->DB_ERRORS){
            try {
                self::$PDO = new PDO(@$this->CONFIG['mode'].':host='.@$this->CONFIG['hostname'].((@$this->CONFIG['port'])?':'.$this->CONFIG['port']:':3306').';dbname='.$this->CONFIG['dbname'], $this->CONFIG['username'], $this->CONFIG['password']);
                self::$PDO->exec('SET NAMES utf8mb4');
                if (!is_null($timeout))
                    self::$PDO->exec('SET session wait_timeout='.$timeout);
                return true;
            } catch(PDOException $e) {
                echo 'We have ERROR in connection!' . EOL;
                $this->DB_ERRORS .= $e->getMessage(). EOL;
            }
        }
        return false;
    }
    function exist_table($tableName): bool
    {
        try {
            @self::$PDO->query("SELECT 1 FROM `$tableName` LIMIT 1;")->fetchAll();
            return true;
        } catch (PDOException $e) {
            $this->DB_ERRORS .= $e.EOL;
        }
        return false;
    }
    function run_query($query): bool|PDOStatement
    {
        try {
            return self::$PDO->query($query);
        } catch (PDOException $e) {
            $this->DB_ERRORS .= $e.EOL;
        }
        return false;
    }
    function run_exec($query): bool|int
    {
        try {
            return self::$PDO->exec($query);
        } catch (PDOException $e) {
            $this->DB_ERRORS .= $e.EOL;
        }
        return false;
    }
    function all_data($table)
    {
        try {
            return self::$PDO->query("SELECT * FROM `$table`");
        } catch (PDOException $e) {
            $this->DB_ERRORS .= $e.EOL;
        }
        return false;
    }

    // Convert data to array
    function data_to_array($data):array
    {
        if ($data)
            return $data->fetchAll(PDO::FETCH_ASSOC);
        return [];
    }
    function column_to_array($data):array
    {
        return $data->fetchAll(PDO::FETCH_COLUMN);
    }
    function data_to_array_num($data):array
    {
        return $data->fetchAll();
    }
    function row_to_array($data):array
    {
        return $data->fetch(PDO::FETCH_ASSOC);
    }
    function row_to_array_num($data):array
    {
        return $data->fetch();
    }

    function search_byID($table,$ID): bool|PDOStatement
    {
        return $this->run_query("SELECT * FROM $table WHERE ID=`$ID`");
    }
    public function create_table($table_name, $vars, $replace=true): bool
    {
        if ($replace)
            $query = "DROP TABLE IF EXISTS `".$table_name."`; ";
        @$query .= "CREATE TABLE `".$table_name."`  (
                    `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,";
        foreach ($vars as $var){
            if ($var === "ID")
                continue;
            $query.="`".$var."` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci NULL,";
        }
        $query .= "  PRIMARY KEY (`ID`) USING BTREE );" ;
        $result = $this->run_exec($query);
        if ($result === false)
            return false;
        return true;
    }
    public function update_table($table_name, $adds=[], $removes=[]): bool
    {
        $query = "ALTER TABLE `".$table_name."` ";
        if ($adds){
            $query .= "ADD COLUMN (";
            foreach ($adds as $add){
                $query .= "`".$add."` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci NULL,";
            }
            $query = rtrim($query, ",");
            $query .= ") ,";
        }
        foreach ($removes as $remove){
            $query .= " DROP COLUMN IF EXISTS `".$remove."`, ";
        }
        $query = rtrim($query, ", ").";";
        $result = $this->run_exec($query);
        if ($result === false)
            return false;
        return true;
    }
    public function delete_table($table_name): bool
    {
        $query = "DROP TABLE IF EXISTS `".$table_name."`; ";
        $result = $this->run_exec($query);
        if ($result === false)
            return false;
        return true;
    }
    public function rename_table($last_table_name, $new_table_name): bool
    {
        $query = "ALTER TABLE `".$last_table_name."` RENAME `".$new_table_name."`;";
        $result = $this->run_exec($query);
        if ($result === false)
            return false;
        return true;
    }
    public function describe_table($table_name): array
    {
        $query = "DESCRIBE `".$table_name."` ;";
        return $this->data_to_array($this->run_query($query));
    }
    public function column_of_table($table_name): array
    {
        $query = "DESCRIBE `".$table_name."`;";
        return $this->column_to_array($this->run_query($query));
    }
    function search($table,$column,$value): array
    {
        try {
            $statement = self::$PDO->prepare("SELECT * FROM $table WHERE `$column` =:value");
            $statement->bindValue(':value', $value);
            $statement->execute();
            return $this->data_to_array($statement);
        }catch (Exception $e){
            $this->DB_ERRORS .= $e.EOL;
        }
        return [];
    }
    function search_where($query, $executes=[]): array
    {
        try {
            $statement = self::$PDO->prepare($query);
            $statement->execute($executes);
            return $this->data_to_array($statement);
        } catch(PDOException $e) {
            $this->DB_ERRORS .= $e->getMessage().EOL;
            return [];
        }
    }
    function insert_multiple_data($query, $executes=[]): bool
    {
        try {
            $statement = self::$PDO->prepare($query);
            $statement->execute($executes);
            if (empty($statement->errorInfo()))
                return false;
            return true;
        } catch(PDOException $e) {
            $this->DB_ERRORS .= $e->getMessage().EOL;
            return false;
        }
    }

    public function insert_row($table,$vars): bool|int
    {
        $columns=""; $values=""; $valuesArray=[];
        foreach ($vars as $item=>$value){
            $columns .= "`".$item."`,";
            $values .= "?,";
            $valuesArray[] = $value;
        }
        $columns = rtrim($columns,",");
        $values = rtrim($values,",");
        try {
            self::$PDO->beginTransaction();
            $statement = self::$PDO->prepare("INSERT INTO `$table` (".$columns.") VALUES (".$values.");");
            $statement->execute($valuesArray);
            $insertedID = self::$PDO->lastInsertId();
            self::$PDO->commit();
            return (int)$insertedID;
        } catch(PDOException $e) {
            if (self::$PDO->inTransaction()) {
                self::$PDO->rollback();
            }
            echo 'We have ERROR in set data!' . '<br>';
            $this->DB_ERRORS .= $e->getMessage();
            return false;
        }
    }
    public function update_row($table,$vars): bool|int
    {
        $string="";
        $ID = $vars['ID'];
        unset($vars['ID']);
        $valuesArray = [];
        foreach ($vars as $item=>$value){
            $string .= "`".$item."` = ?,";
            $valuesArray[] = $value;
        }
        $string = rtrim($string,",");
        try {
            $statement = self::$PDO->prepare("UPDATE `$table` SET $string WHERE `ID` = $ID");
            $statement->execute($valuesArray);
            return (int)$ID;
        } catch(PDOException $e) {
            echo 'We have ERROR in set data!' . '<br>';
            $this->DB_ERRORS .= $e->getMessage();
            return false;
        }
    }
    public function delete_row($table,$ID): bool
    {
        try {
            $statement = self::$PDO->prepare("DELETE FROM `$table` WHERE `ID` = $ID");
            $statement->execute();
            return true;
        } catch(PDOException $e) {
            echo 'We have ERROR on delete data!' . '<br>';
            $this->DB_ERRORS .= $e->getMessage();
            return false;
        }
    }
    public function update_one_value($table,$ID,$attribute,$value): bool
    {
        try {
            $statement = self::$PDO->prepare("UPDATE `$table` SET `$attribute` = :value WHERE `ID` = $ID");
            $statement->bindValue(':value', $value);
            $statement->execute();
            return true;
        } catch(PDOException $e) {
            $this->DB_ERRORS .=  'We have ERROR in update data!' .EOL;
            $this->DB_ERRORS .= $e->getMessage().EOL;
            return false;
        }
    }
    public function last_id(string $table):int|bool
    {
        try {
            $statement = self::$PDO->prepare("SELECT MAX(ID) FROM `$table` ");
            $statement->execute();
            return (int) $statement->fetch()[0];
        } catch(PDOException $e) {
            $this->DB_ERRORS .= 'We have ERROR in count data!' .EOL;
            $this->DB_ERRORS .= $e->getMessage() .EOL;
            return false;
        }
    }





    public function restore_backup_database($file): bool
    {
        $backupFile = file_get_contents($file);
        if ($this->run_query($backupFile)) {
            return true;
        }else{
            return false;
        }
    }
    public function get_backup_database($BACKUP_PATH,$tables=""): bool|int
    {
        self::$PDO->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
        //Script Variables
        $compression = false;
        $nowTime = time();
        //create/open files
        if ($compression) {
            $zp = gzopen($BACKUP_PATH ."/". $nowTime . '.sql.gz', "a9");
        } else {
            $handle = fopen($BACKUP_PATH ."/". $nowTime . '.sqlh', "w+");
        }
        //array of all database field types which just take numbers
        $numtypes = array('tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'float', 'double', 'decimal', 'real');
        //get all the tables
        if (empty($tables)) {
            $pstm1 = self::$PDO->query('SHOW TABLES');
            $pstm1 = $this->data_to_array_num($pstm1);
            $tables = array();
            foreach ($pstm1 as $tbl) {
                $tables[] = $tbl[0];
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }

        //cycle through the table(s)
        foreach ($tables as $table) {
            $result = self::$PDO->query("SELECT * FROM $table");
            $num_fields = $result->columnCount();
            $num_rows = $result->rowCount();
//            uncomment below if you want 'DROP TABLE IF EXISTS' displayed
            $return = "";
            $return.= 'DROP TABLE IF EXISTS `'.$table.'`;';

            //table structure
            $pstm2 = self::$PDO->query("SHOW CREATE TABLE $table");
            $row2 = $pstm2->fetch(PDO::FETCH_NUM);
            $ifnotexists = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $row2[1]);
            $return .= "\n\n" . $ifnotexists . ";\n\n";

            if ($compression) {
                gzwrite($zp, $return);
            } else {
                fwrite($handle, $return);
            }
            $return = "";
            //insert values
            if ($num_rows) {
                $return = 'INSERT INTO `' . $table . '` (';
                $pstm3 = self::$PDO->query("SHOW COLUMNS FROM $table");
                $count = 0;
                $type = array();

                while ($rows = $pstm3->fetch(PDO::FETCH_NUM)) {
                    if (stripos($rows[1], '(')) {
                        $type[$table][] = stristr($rows[1], '(', true);
                    } else {
                        $type[$table][] = $rows[1];
                    }

                    $return .= "`" . $rows[0] . "`";
                    $count++;
                    if ($count < ($pstm3->rowCount())) {
                        $return .= ", ";
                    }
                }

                $return .= ")" . ' VALUES';

                if ($compression) {
                    gzwrite($zp, $return);
                } else {
                    fwrite($handle, $return);
                }
                $return = "";
            }
            $count = 0;
            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $return = "\n\t(";
                for ($j = 0; $j < $num_fields; $j++) {
                    //$row[$j] = preg_replace("\n","\\n",$row[$j]);
                    if (isset($row[$j])) {
                        //if number, take away "". else leave as string
                        if ((in_array($type[$table][$j], $numtypes)) && (!empty($row[$j]))) {
                            $return .= $row[$j];
                        } else {
                            $return .= self::$PDO->quote($row[$j]);
                        }
                    } else {
                        $return .= 'NULL';
                    }
                    if ($j < ($num_fields - 1)) {
                        $return .= ',';
                    }
                }
                $count++;
                if ($count < ($result->rowCount())) {
                    $return .= "),";
                } else {
                    $return .= ");";
                }
                if ($compression) {
                    gzwrite($zp, $return);
                } else {
                    fwrite($handle, $return);
                }
                $return = "";
            }
            $return = "\n\n-- ------------------------------------------------ \n\n";
            if ($compression) {
                gzwrite($zp, $return);
            } else {
                fwrite($handle, $return);
            }
            $return = "";
        }
        if ($compression) {
            gzclose($zp);
        } else {
            fclose($handle);
        }
        $error1 = $pstm2->errorInfo();
        $error2 = $pstm3->errorInfo();
        $error3 = $result->errorInfo();

        echo $error1[2];
        echo $error2[2];
        echo $error3[2];
        if (!($error1[2]||$error2[2]||$error3[2])){return $nowTime;}
        return false;
    }

}