<?php
include_once "db.php";
$database = new db("localhost","root","Albion@123","fileManeger");

if(isset($_POST["name"])){
    $fileName = $_POST["name"];
    $pattern = "/\./";
    if(preg_match($pattern,$fileName)){
        $fileType = "file";
        $file = fopen("./files/$fileName","x+");
        $fileSize = filesize("./files/$fileName");
        $database->insert($fileName,"$fileSize byte",$fileType);
    }else{
        $fileType = "directory";
        $dir = "./files/$fileName";
        $fileSize = filesize("./files/$fileName");
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
            $database->insert($fileName,"$fileSize byte",$fileType);
        }
    }

    fclose($file);
}

?>