<?php

include_once "db.php";
include_once "recursion.php";

$database = new db("localhost","root","Albion@123","fileManeger");
$recFunc = new rec();

$json = file_get_contents('php://input');
if (!empty($json)) {
    $data = json_decode($json);
    $pattern = "/copy(.*)/i";
    $allFiles;

    foreach ($data as $files) {
        if (preg_match($pattern,$files)) {
            $searchFiles = preg_replace($pattern,"",$files);
            if ($database->selectType($files) === "file") {
                $allFiles = $recFunc->searchFiles("./files","$searchFiles*","file");
            
            } else {
                $allFiles = $recFunc->searchFiles("./files","$searchFiles*","directory");

            }

        } else {
            if ($database->selectType($files) === "file") {
                $allFiles = $recFunc->searchFiles("./files","$files*","file");
            
            } else {
                $allFiles = $recFunc->searchFiles("./files","$files*","directory");

            }

        }    

        if (!empty($allFiles)) {
            $patternArr = [];
            $maxCpy = 0;
            $lastFileFound;
            $newPattern = "/\b\d{1,}\b/";

            foreach($allFiles as $filesFound) {
                if (preg_match($newPattern,$filesFound,$patternArr)) {
                    if ($maxCpy < $patternArr[0]) {
                        $maxCpy = $patternArr[0];
                        $lastFileFound = $filesFound;
    
                    }

                } else {
                    $lastFileFound = $filesFound; 

                }
            }

            if (preg_match($pattern,$lastFileFound)) {
                preg_match($newPattern,$lastFileFound,$patternArr);
                $copiedPath = preg_replace($newPattern,"",$lastFileFound);
                $newPath = $copiedPath."".(int)$patternArr[0] + 1;
                $newFile = basename($newPath);

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
                $newPath = $lastFileFound." copy 1";
                $newFile = basename($newPath);

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