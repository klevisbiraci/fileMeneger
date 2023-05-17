<?php

function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return;
    }
    
    $files = glob($dir . '/*');
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDirectory($file);

        } else {
            unlink($file);
        }
    }
    
    rmdir($dir);
}

include_once "db.php";
$database = new db("localhost","root","Albion@123","fileManeger");

$json = file_get_contents('php://input');

if(!empty($json)){
    $data = json_decode($json);

    foreach($data as $files){
        if ($database->selectType($files) === "file") {
            $filePath = glob("./*/$files");
            unlink($filePath[0]);
            $database->delete($files);

        }else {
            $dirPath = glob("./*/$files");
            deleteDirectory($dirPath[0]);
            $database->delete($files);
        }
    }
}

?>