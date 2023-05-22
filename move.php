<?php

include_once "db.php";
$database = new db("localhost","root","Albion@123","fileManeger");

if (isset($_POST["old"]) && isset($_POST["new"])) {
    $oldName = $_POST["old"];
    $newName = $_POST["new"];

    $patternArr = [];
    $file = glob("./*/$oldName");

    if (preg_match('/.*(?<=\/)/',$file[0],$patternArr)) {
        $oldFile = $file[0];
        $newFile = $patternArr[0].$newName;
        
        echo json_encode($oldFile." ".$newFile);

        if (rename($oldFile,$newFile)) {
            $database->update($oldName,$newName);
        }
    }    
}

?>