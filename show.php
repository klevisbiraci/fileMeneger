<?php

include_once "db.php";
$database = new db("localhost","root","Albion@123","fileManeger");

if(isset($_GET["show"])){
    $response = $database->select();
    echo json_encode($response->fetch_all());
}

?>