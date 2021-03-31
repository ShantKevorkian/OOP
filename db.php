<?php

    class Db {
        
        private $conn;

        private $where_condition = '';

        function __construct($db_host = "localhost", $db_user = "root", $db_pass = "", $db_name = "internship") {
            $this->conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
            if ($this->conn->connect_errno) {
                die("Connection failed: " . "<br>" . $this->conn->connect_error);
            }
        }

        function __destruct() {
		    $this->conn->close();
	    }

        public function select($sql, $all = true){
            $data = [];
            $query_select = $this->conn->query($sql);

            if(!$all) {
                $row = $query_select->fetch_assoc();
                return $row;
            }
            while($row = $query_select->fetch_assoc()) {
                $data[] = $row;
            }
            return $data;
        } 

        public function insert($table_name, $data){
            $value_data = '';
            foreach($data as $key => $value) {  
                $value_data .="'".$this->conn->real_escape_string(htmlspecialchars($value))."', ";  
            }
            $value_data = substr($value_data, 0, -2);
            $query_insert = "INSERT INTO $table_name (".implode(",", array_keys($data)).") 
                                VALUES ($value_data)";
            return $this->conn->query($query_insert);
        }

        public function update($table_name, $updated_data){
            $data = '';

            foreach($updated_data as $key => $value) {  
                $data .= $key . "='".$this->conn->real_escape_string(htmlspecialchars($value))."', ";  
            }
            $data = substr($data, 0, -2);
            
            $where = $this->where_condition;
            $this->where_condition = '';

            $query_update = "UPDATE $table_name SET " . $data . $where;
            return $this->conn->query($query_update);
        }

        public function delete($table_name){
            $where = $this->where_condition;
            $this->where_condition = '';

            $query_delete = "DELETE FROM $table_name" . $where;
            return $this->conn->query($query_delete);
        }

        public function where($column, $column_value, $oper = '=') {
            if($this->where_condition) {
                $this->where_condition .= " AND ". $column. " " . $oper. " " . "'$column_value'";
            }
            else {
                $this->where_condition .= " WHERE " . $column. " " . $oper. " " . "'$column_value'";
            }
            return $this;
        }

        public function orWhere($column, $column_value, $oper = '=') {
            if($this->where_condition) {
                $this->where_condition .= " OR ". $column. " " . $oper. " " . "'$column_value'";
            }
            else {
                $this->where_condition .= " WHERE " . $column. " " . $oper. " " . "'$column_value'";
            }
            return $this;
        }
            
    }

    $db = new Db();

    // Select test
    $result_select = $db->select("SELECT * FROM user_reg");

    // Insert test
    $data = [
        "name" => "nazo",
        "email" => "nazo@gmail.com",
        "password" => "123"
    ];
    $result_insert = $db->insert('user_reg', $data);
    if($result_insert) {
        echo "New data inserted";
    }
    else {
        echo "error";
    }

    // Update tests
    $update_data = [
		"name" => "shant"
	];
    $update_data_test = [
		"email" => "shant@gmail.com"
	];
    $result_update = $db->where("id", "23")->orWhere("id", "25")->update('user_reg', $update_data);
    $result_update_test = $db->where("id", "23")->orWhere("id", "25")->update('user_reg', $update_data_test);
    if($result_update) {
        echo "Data updated";
    }
    else {
        echo "error";
    }
    if($result_update_test) {
        echo "Data updated";
    }
    else {
        echo "error";
    }

    // Delete test
    $result_delete = $db->where("email", "nazo@gmail.com")->where("password", "1%", "LIKE")->delete('user_reg');
    if($result_delete) {
        echo "Data deleted";
    }
    else {
        echo "error";
    }