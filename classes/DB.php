<?php

class DB{
    private static $_instance = null;
    private $_count_params, $params, $in;

    private $_mysql,
            $_query,
            $_error = false,
            $_results,
            $_count;
    private function __construct(){
        $this->_mysql = new mysqli(Config::get("mysql/host"),Config::get("mysql/username"),Config::get("mysql/password"),
                            Config::get("mysql/db"));
    }

    public static function getInstance(){
        if(!isset(self::$_instance)){
            self::$_instance = new DB;
        }
        return self::$_instance;
    }

    public function count_p($niz = array()){
        $this->_count_params = null;
        $this->in = null;
        $this->params = null;
        $this->in = str_repeat("?,", count($niz));
        $this->in = trim($this->in, ",");
        foreach($niz as $k => $v){
            $this->params[$k] = $niz[$k];
        }

        return $this->in;
    }

    public function query($sql){
        $this->_count = null;
        $this->_error = false;
        if($this->_query = $this->_mysql->prepare($sql)){
            $count = count($this->params);

            if($count != 0){
                $in = str_repeat("?,", $count);
                $in = trim($in, ",");
                $type = str_repeat("s", $count);
                $ref_param = array();
                foreach($this->params as $k=>$v){
                    $ref_param[$k] = &$this->params[$k];
                }
                call_user_func_array(array($this->_query, "bind_param"), array_merge(array($type), $ref_param));
            }
            if($this->_query->execute()){

                if(strpos($sql, "SELECT") !== false){
                    $this->_results = $this->_query->get_result();

                    while($row = $this->_results->fetch_row()){
                        $this->_count++;
                    }
                    $this->_results->data_seek(0);
                }
            }
            else{
                $this->_error = true;
            }
        }
        return $this;
    }

    public function action($action, $table, $where = array()){
        if(count($where) == 3){
            $operators = array("=", "in", ">", "<", ">=", "<=");
            $field = $where[0];
            $operator = $where[1];
            DB::count_p(array($where[2]));

            if(in_array($operator, $operators)){
                $sql = "$action FROM $table WHERE $field $operator (".$this->in.")";

                if($this->query($sql)){
                    return $this;
                }

            }
        }
        return false;
    }

    public function get($table, $where){
        return $this->action("SELECT * ", $table, $where);
    }


    public function insert($table, $values){
        $this->count_p($values);
        $val = null;
        foreach($values as $k=>$v){
            $val = $val.","."`$k`";
        }
        $val = trim($val, ",");
        $sql = "INSERT INTO ".$table." ($val) VALUES (".$this->in.")";


        if($this->query($sql)){
            return $this;
        }
        else{
            echo "Greška";
        }

        /*
        $val = "`username`, `password`,`salt`, `name`, `joined`, `group`";
        $sql = "INSERT INTO ".$table." ($val) VALUES (".self::$in.")";
        if($this->query($sql))
            return $this;
        else
            echo "greska";
        */

    }

    public function update($table, $column = array(), $id){
        $this->count_p($column);
        $col = null;
        foreach($column as $k => $v){
            $col = $col."$k=?,";
        }
        $col = trim($col, ",");

        $sql = "UPDATE $table SET $col WHERE id=".$id;

        if($this->query($sql))
            return $this;
        else
            echo "greska";

    }

    public function delete($table, $where){
        return $this->action("DELETE ", $table, $where);
    }

    public function count(){
        return $this->_count;
    }

    public function results(){
        return $this->_results;
    }

    public function error(){
        return $this->_error;
    }

    public function get_data(){
        $data = $this->results();
        return $data->fetch_object();
    }

}