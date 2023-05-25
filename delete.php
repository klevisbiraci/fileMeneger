<?php

include_once "db.php";
include_once "recursion.php";

$database = new db("localhost","root","Albion@123","fileManeger");
$recFunc = new rec();

$json = file_get_contents('php://input');

if(!empty($json)){
    $data = json_decode($json);

    foreach($data as $files){
        if ($database->selectType($files) === "file") {
            $filePath = $recFunc->searchFiles("./files",$files,"file");// $filePath = glob("./*/$files");
            unlink($filePath[0]);
            $database->delete($files);

        }else {
            $dirPath = $recFunc->searchFiles("./files",$files,"directory");//glob("./*/$files");
            echo json_encode($dirPath[0]);
            $recFunc->deleteDirectory($dirPath[0]);
            $database->delete($files);
            $database->deleteToDir($files);
            
        }
    }
}

?>