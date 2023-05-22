<?php

class db{
    public $con;
    public $tableName = "files";

    public function __construct($hostname,$username,$password,$dbName)
    {
        $this->con = new mysqli($hostname,$username,$password,$dbName);
    }

    public function insert($filename,$filesize,$filetype){
        $time = time();
        $date = date("Y-m-d H-i-s",$time);
        $query = "INSERT INTO $this->tableName(filename,filesize,date,filetype)
            VALUES('$filename','$filesize','$date','$filetype')";
        $this->con->query($query); 
    }

    public function select(){
        $query = "SELECT * FROM $this->tableName";
        return $this->con->query($query); 
    }

    public function selectType($filename){
        $typeReturn = null;
        $query = "SELECT filetype FROM $this->tableName WHERE filename = '$filename'";
        $fileType = $this->con->query($query);
        $type = $fileType->fetch_all();
        foreach($type as $data){
            foreach($data as $returnData){
                $typeReturn = $returnData;
            }
        }
        return $typeReturn;
    }

    public function delete($filename){
        $query = "DELETE FROM $this->tableName WHERE filename = '$filename'";
        $this->con->query($query);
    }

    public function update($oldName,$newName){
        $query = "UPDATE $this->tableName SET filename = '$newName' WHERE filename = '$oldName'";
        $this->con->query($query);
    }
}

?>