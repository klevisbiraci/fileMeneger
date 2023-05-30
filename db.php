<?php

class db {
    public $con;
    public $tableName = "files";
    public $tableName2 = "dir";
    public $tableName3 = "filesInside";

    public function __construct($hostname,$username,$password,$dbName) {
        $this->con = new mysqli($hostname,$username,$password,$dbName);

    }

    public function insert($filename,$filesize,$filetype) {
        $time = time();
        $date = date("Y-m-d H-i-s",$time);
        $query = "INSERT INTO $this->tableName(filename,filesize,date,filetype)
            VALUES('$filename','$filesize','$date','$filetype')";
        
        $this->con->query($query); 

    }

    public function select() {
        $query = "SELECT * FROM $this->tableName";
        return $this->con->query($query); 

    }

    public function selectType($filename) {
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

    public function delete($filename) {
        $query = "DELETE FROM $this->tableName WHERE filename = '$filename'";
        $this->con->query($query);

    }

    public function update($oldName,$newName) {
        $query = "UPDATE $this->tableName SET filename = '$newName' WHERE filename = '$oldName'";
        $this->con->query($query);

    }

    public function insertToDir($dirName,$dirSize,$dirPath) {
        $time = time();
        $date = date("Y-m-d H-i-s",$time);
        $query = "INSERT INTO $this->tableName2(dirName,fileSize,date,dirpath)
            VALUES('$dirName','$dirSize','$date','$dirPath')";

        $this->con->query($query); 
        
    }

    public function deleteToDir($dirName) {
        $query = "DELETE FROM $this->tableName2 WHERE dirName = '$dirName'";
        $this->con->query($query);

    }

    public function selectToDir() {
        $query = "SELECT * FROM $this->tableName2";
        return $this->con->query($query); 

    }

    public function selectDirID($dirName) {
        $id = null;
        $query = "SELECT id FROM $this->tableName2 WHERE dirName = '$dirName'";
        $exeQ = $this->con->query($query); 
        $idArr = $exeQ->fetch_all();
        
        foreach($idArr as $firstWrap) {
            foreach($firstWrap as $secondWrap) {
                $id = $secondWrap;

            }
        }
        return $id;
    }

    public function insertToFilesInside($dirID,$fileName,$fileSize,$fileType) {
        $time = time();
        $date = date("Y-m-d H-i-s",$time);
        $query = "INSERT INTO $this->tableName3(filename,filesize,date,dirID,filetype)
            VALUES('$fileName','$fileSize','$date','$dirID','$fileType')";
        
        $this->con->query($query); 
       
    }

    public function deleteToFilesInside($dirID) {
        $query = "DELETE FROM $this->tableName3 WHERE dirID = '$dirID'";
        $this->con->query($query);

    }

    public function updateDirPath($dirName,$newPath) {
        $query = "UPDATE $this->tableName2 SET dirpath = '$newPath'
            WHERE dirName = '$dirName'";
        $this->con->query($query);
    }
    
}

?>