<?php

class Dbase
{

    private $_host = "localhost";
    private $_user = "root";
    private $_password = "";
    private $_name = "ecommerce";

    private $_conndb = false;
    public $_last_query = null;
    public $_affected_rows = 0;

    public $_insert_keys = array();
    public $_insert_values = array();
    public $_update_sets = array();

    public $_id;


    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        try {
            $this->_conndb = new PDO("mysql:host=" . $this->_host . ";dbname=" . $this->_name, $this->_user, $this->_password);
            $this->_conndb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_conndb->exec("SET NAMES 'utf8'");
        } catch (Exception $e) {
            echo "Could not connect to the database";
            exit;
        }
    }

    public function close()
    {
        $this->_conndb = null;
        if ($this->_conndb != null) {
            die("Closing connection failed.");
        }
    }

    public function escape($value)
    {
        /*if(function_exists("mysql_real_escape_string")){
            if(get_magic_quotes_gpc()){
                $value = stripcslashes($value);
            }
            $value = mysqli_real_escape_string($this->_conndb,$value);
        } else {
            if(!get_magic_quotes_gpc()){
                $value = addcslashes($value);
            }
        }*/
        return $value;
    }

    public function query($sql)
    {
        $this->_last_query = $sql;

        try {
            $result = $this->_conndb->query($sql);
        } catch (Exception $e) {
            echo "Data could not be retrieved from database - query";
            exit;
        }

        $this->displayQuery($result);
        return $result;
    }

    public function displayQuery($result)
    {
        if (!$result) {
            $output = "Database query failed: <br>";
            $output .= "Last SQL query was: " . $this->_last_query;
            die($output);
        } else {
            $this->_affected_rows = $result->rowCount();
        }

    }

    public function fetchAll($sql)
    {
        $result = $this->query($sql);
        $out = array();

        $out = $result->fetchAll(PDO::FETCH_ASSOC);

        unset($result);// or: $result = null;
        return $out;
    }

    public function fetchOne($sql)
    {
        $out = $this->fetchAll($sql);
        return array_shift($out);
    }

    public function lastId()
    {
        return mysqli_insert_id($this->_conndb);
    }


    // prepares to insert into database a new user( or product ? )
    public function prepareInsert($array = null)
    {
        if (!empty($array)) {
            foreach ($array as $key => $value) {
                $this->_insert_keys[] = $key;
                $this->_insert_values[] = $this->escape($value);
            }
        }
    }


    // insert new user( or product ) into the database
    public function insert($table = null)
    {
        if (!empty($table) && !empty($this->_insert_keys) && !empty($this->_insert_values)) {
            $sql = "INSERT INTO `{$table}` (`";
            $sql .= implode("`, `", $this->_insert_keys);
            $sql .= "`) VALUES ('";
            $sql .= implode("', '", $this->_insert_values);
            $sql .= "')";

            if ($this->query($sql)) {
                $this->_id = $this->lastId();
                return true;
            }
            return false;
        }
    }


    // prepare update in database
    public function prepareUpdate($array = null)
    {
        if (!empty($array)) {
            foreach ($array as $key => $value) {
                $this->_update_sets[] = "`{$key}` = '" . $this->escape($value) . "'";
            }
        }
    }


    // updates the records of an user with $id from $table
    public function update($table = null, $id = null)
    {
        if (!empty($table) && !empty($id) && !empty($this->_update_sets)) {
            $sql = "UPDATE `{$table}` SET ";
            $sql .= implode(", ", $this->_update_sets);
            $sql .= " WHERE `id` = '" . $this->escape($id) . "'";
            return $this->query($sql);
        }
    }


}