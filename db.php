<?php

    class Db {
        
        private $conn;
        private $db_host;
        private $db_user;
        private $db_pass;
        private $db_name;

        function __construct() {
            $this->db_host = "localhost";
            $this->db_user = "root";
            $this->db_pass = "";
            $this->db_name = "internship";

            $this->conn = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
            if ($this->conn->connect_error) {
                die("Connection failed: " . "<br>" . $this->conn->connect_error);
            }
        }

        public function select($sql){
            $query_select = $this->conn->query($sql);
            $row = $query_select->fetch_all();
            return $row;
        } 

        public function insert($table_name, $data){
            $query_insert = "INSERT INTO $table_name (".implode(",", array_keys($data)).") 
                                VALUES ('".implode("','",array_values($data))."')";
            $query_run = $this->conn->query($query_insert);
            return $query_insert;
        }

        public function update($table_name, $updated_data, $update_condition){
            $data = '';
            $condition = '';

            foreach($updated_data as $key => $value) {  
                $data .= $key . "='".$value."', ";  
            }
            $data = substr($data, 0, -2);
            
            foreach($update_condition as $key => $value) {  
                $condition .= $key . "='".$value."' AND ";
            }
            $condition = substr($condition, 0, -5);

            $query_update = "UPDATE $table_name SET ".$data." WHERE ".$condition."";
            $query_run = $this->conn->query($query_update);
            return $query_run;
        }

        public function delete($table_name, $delete_condition){
            $condition = '';

            foreach($delete_condition as $key => $value) {  
                $condition .= $key . "='".$value."' AND "; 
            }
            $condition = substr($condition, 0, -5);

            $query_delete = "DELETE FROM $table_name WHERE ".$condition."";
            $query_run = $this->conn->query($query_delete);
            return $query_run;
        }
    }

    $db = new Db();

    // Select test
    $result_select = $db->select("SELECT * FROM user_reg");

    // Insert test
    $data = [
        "name" => "john",
        "email" => "john@gmail.com",
        "password" => "123"
    ];
    $result_insert = $db->insert('user_reg', $data);
    if($result_insert) {
        echo "New data inserted";
    }
    else {
        echo "error";
    }

    // Update test
    $update_data = [
		"name" => "Jake"
	];
    $update_condition = [
		"id" => "10"
	];
    $result_update = $db->update('user_reg', $update_data, $update_condition);
    if($result_update) {
        echo "Data updated";
    }
    else {
        echo "error";
    }

    // Delete test
    $delete_condition = [
		"id" => "11"
	];
    $result_delete = $db->delete('user_reg', $delete_condition);
    if($result_delete) {
        echo "Data deleted";
    }
    else {
        echo "error";
    }