<?php

class User {
    private $_db,
        $_data,
        $_sessionName,
        $_cookieName,
        $isLoggedIn;

    public function __construct($user = null) {
        $this->_db = DB::getInstance();
        $this->_sessionName = Config::get('session/session_name');
        $this->_cookieName = Config::get("remember/cookie_name");
        if(!$user) {
            if(Session::exists($this->_sessionName)) {
                $user = Session::get($this->_sessionName);
                $data = $this->_db->get("users", array("id", "=", $user));
                if($data->count()){
                    $this->_data = $data->get_data();
                    $this->isLoggedIn = true;
                }

            }
        }
        else{
            $data = $this->_db->get("users", array("id", "=", $user));
            $this->_data = $data->get_data();

        }

    }
    public function create($fields = array()) {
        if(!$this->_db->insert('users', $fields)) {
            throw new Exception('Sorry, there was a problem creating your account;');
        }
    }

    public function login($username = null, $password = null, $remember = false){


        if(!$username && !$password && $this->exists()){
            Session::set($this->_sessionName, $this->data()->id);
        }
        else{

            $data = $this->_db->get("users", array("username", "=", $username));
            if($data->count()){
                $this->_data = $data->get_data();
            }


            if($this->_data){
                if($this->data()->password === Hash::make($password, $this->data()->salt)){
                    Session::set($this->_sessionName, $this->data()->id);

                    if($remember){
                        $hash = Hash::unique();

                        $hashCheck = $this->_db->get("users_sessions", array("user_id", "=", $this->data()->id));

                        if(!$hashCheck->count()){

                            $this->_db->insert("users_sessions", array("user_id" => $this->data()->id, "hash" => $hash));

                        }
                        else{
                            $hash = $hashCheck->get_data()->hash;
                        }

                        Cookie::set($this->_cookieName, $hash, Config::get("remember/cookie_expiry"));


                    }

                    return true;
                }
            }
        }

        return false;

    }

    public function logout(){

        $this->_db->delete("users_sessions", array("user_id", "=", $this->data()->id));

        Session::delete($this->_sessionName);
        Cookie::delete($this->_cookieName);

    }

    public function update($table, $fields = array(), $id = null){
        if(!$id && $this->isLoggedIn()) {
            $id = $this->data()->id;
        }
        $this->_db->update($table, $fields, $id);
    }

    public function permission($key){
        if($this->data()){
            $group = $this->_db->get("groups", array("id", "=", $this->data()->group));
            if($group->count()){
                $permissions = json_decode($group->get_data()->permissions, true);

                if($permissions[$key] == true){
                    return true;
                }
            }
        }
    }

    public function exists() {
        return (!empty($this->_data)) ? true : false;
    }

    public function data(){
        return $this->_data;
    }
    public function isLoggedIn() {
        return $this->isLoggedIn;
    }
}
