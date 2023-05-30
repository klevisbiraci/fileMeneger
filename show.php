<?php

include_once "db.php";
$database = new db("localhost","root","Albion@123","fileManeger");

if (isset($_GET["show"])) {
    $response = $database->select();
    echo json_encode($response->fetch_all());

}

if (isset($_GET["showDir"])) {
    $exeQ = $database->selectToDir();
    $allDir = $exeQ->fetch_all();
    $response = [];

    foreach($allDir as $dir) {
        $dirFound = glob("./files/$dir[1]");

        if (sizeof($dirFound) !== 0) {
            array_push($response,$dir[1]);

        }

    }
    
    echo json_encode($response);

}

?>