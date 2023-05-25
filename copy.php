<?php

include_once "db.php";
include_once "recursion.php";

$database = new db("localhost","root","Albion@123","fileManeger");
$recFunc = new rec();

$json = file_get_contents('php://input');
if(!empty($json)){
    $data = json_decode($json);
    $pattern = "~copy[1-9]~";
    $patternArr = [];
    $allFiles;
        
    foreach ($data as $files) {
        if (preg_match($pattern,$files)) {
            $searchFiles = preg_replace($pattern,"",$files);
            if ($database->selectType($files) === "file") {
                $allFiles = $recFunc->searchFiles("./files","$searchFiles*","file");//glob("./*/$searchFiles*");
            
            } else {
                $allFiles = $recFunc->searchFiles("./files","$searchFiles*","directory");

            }

        } else {
            if ($database->selectType($files) === "file") {
                $allFiles = $recFunc->searchFiles("./files","$files*","file");//glob("./*/$files*");
            
            } else {
                $allFiles = $recFunc->searchFiles("./files","$files*","directory");

            }

        }    

        if (!empty($allFiles)) {
            $lastFileFound = end($allFiles);

            if (preg_match($pattern,$lastFileFound)) {
                $newPattern = "~[1-9]~";
                preg_match($newPattern,$lastFileFound,$patternArr);

                $copiedPath = preg_replace($newPattern,"",$lastFileFound);
                $newPath = $copiedPath."".$patternArr[0] + 1;
                $newFile = preg_replace('/.*(?<=\/)/',"",$newPath);

                if ($database->selectType($files) === "file") {
                    if (copy($lastFileFound,$newPath)) {
                        $file = fopen($newPath,"r");
                        $fileSize = filesize($newPath);
                        $database->insert($newFile,"$fileSize byte","file");
                        fclose($file);
                    }                
                } else {
                    $recFunc->copyDirectory($lastFileFound,$newPath);
                    $fileSize = filesize($newPath);
                    $database->insert($newFile,"$fileSize byte","directory");
                    $database->insertToDir($newFile,$fileSize." byte");
                }

            } else {
                $newPath = $lastFileFound."copy1";
                $newFile = preg_replace('/.*(?<=\/)/',"",$newPath);

                if ($database->selectType($files) === "file") {
                    if (copy($lastFileFound,$newPath)) {
                        $file = fopen($newPath,"r");
                        $fileSize = filesize($newPath);
                        $database->insert($newFile,"$fileSize byte","file");
                        fclose($file);
                    }
                } else {
                    $recFunc->copyDirectory($lastFileFound,$newPath);
                    $fileSize = filesize($newPath);
                    $database->insert($newFile,"$fileSize byte","directory");
                    $database->insertToDir($newFile,$fileSize." byte");
                }
            }
        } 
    }    
}

?>