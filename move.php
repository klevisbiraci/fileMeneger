<?php

include_once "db.php";
include_once "recursion.php";

$database = new db("localhost","root","Albion@123","fileManeger");
$recFunc = new rec();

if (isset($_POST["old"]) && isset($_POST["new"])) {
    $oldName = $_POST["old"];
    $newName = $_POST["new"];

    $patternArr = [];

    if ($database->selectType($file) === "file") {
        $file = $recFunc->searchFiles("./files",$oldName,"file");
        
    } else {
        $file = $recFunc->searchFiles("./files",$oldName,"directory");

    }

    if (preg_match('/.*(?<=\/)/',$file[0],$patternArr)) {
        $oldFile = $file[0];
        $newFile = $patternArr[0].$newName;
            
        if (rename($oldFile,$newFile)) {
            $database->update($oldName,$newName);
                
        }
    }
}

if (isset($_POST["dir"]) && isset($_POST["files"])) {
    $files = explode(",",$_POST["files"]);
    $dir = $_POST["dir"];

    foreach($files as $file){
        if ($database->selectType($file) === "file") {
            $filePath = $recFunc->searchFiles("./files",$file,"file");
            $dirPath = $recFunc->searchFiles("./files",$dir,"directory");
            $destination = $dirPath[0]."/$file";

            if (rename($filePath[0],$destination)) {
                $dirId = $database->selectDirID($dir);
                $fileOpened = fopen($destination,"r");
                $fileSize = filesize($destination);
                $database->insertToFilesInside($dirId,$file,$fileSize,"file");
                $database->delete($file);
                fclose($fileOpened);

            }

        } else {
            $dirPath = $recFunc->searchFiles("./files",$file,"directory");
            $dirInsert = $recFunc->searchFiles("./files",$dir,"directory");
            $destination = $dirInsert[0]."/$file";

            $recFunc->copyDirectory($dirPath[0],$destination);
            $recFunc->deleteDirectory($dirPath[0]);

            $dirId = $database->selectDirID($dir);
            $fileSize = filesize($destination);
            $database->insertToFilesInside($dirId,$file,$fileSize,"directory");
            $database->updateDirPath($file,$destination);
            $database->delete($file);

        }
    }
}

?>