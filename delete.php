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
            $filePath = $recFunc->searchFiles("./files",$files,"file");
            unlink($filePath[0]);
            $database->delete($files);

        }else {
            $dirPath = $recFunc->searchFiles("./files",$files,"directory");
            $dirID = $database->selectDirID($files);
            $recFunc->deleteDirectory($dirPath[0]);
            $database->delete($files);
            $database->deleteToDir($files);
            $database->deleteToFilesInside($dirID);
            
            $allDirInfo = $database->selectToDir();
            $allDir = $allDirInfo->fetch_all();
            
            foreach($allDir as $dirName) {
                $find = $recFunc->searchFiles("./files",$dirName[1],"directory");
                if (!is_dir($find[0])) {
                    $database->deleteToDir($dirName[1]);

                }
            }
        }
    }
}

?>