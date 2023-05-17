<?php

function copyDirectory($source, $destination) {
    if (!is_dir($destination)) {
        mkdir($destination);
    }
    
    $dir = opendir($source);
    while (($file = readdir($dir)) !== false) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        $sourceFile = $source . '/' . $file;
        $destinationFile = $destination . '/' . $file;
        if (is_dir($sourceFile)) {
            copyDirectory($sourceFile, $destinationFile);

        } else {
            copy($sourceFile, $destinationFile);

        }
    }
    closedir($dir);
}

include_once "db.php";
$database = new db("localhost","root","Albion@123","fileManeger");

$json = file_get_contents('php://input');
if(!empty($json)){
    $data = json_decode($json);
    $pattern = "~copy[1-9]~";
    $patternArr = [];
    $allFiles;
        
    foreach ($data as $files) {
        if (preg_match($pattern,$files)) {
            $searchFiles = preg_replace($pattern,"",$files);
            $allFiles = glob("./*/$searchFiles*");

        } else {
            $allFiles = glob("./*/$files*");

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
                    copyDirectory($lastFileFound,$newPath);
                    $fileSize = filesize($newPath);
                    $database->insert($newFile,"$fileSize byte","directory");
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
                    copyDirectory($lastFileFound,$newPath);
                    $fileSize = filesize($newPath);
                    $database->insert($newFile,"$fileSize byte","directory");
                }
            }
        } 
    }    
}

?>