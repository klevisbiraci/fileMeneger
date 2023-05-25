<?php
include_once "db.php";
$database = new db("localhost","root","Albion@123","fileManeger");

if(isset($_POST["name"])){
    $fileName = $_POST["name"];
    $pattern = "/\./";

    if(preg_match($pattern,$fileName)){
        $fileType = "file";
        $filePath = "./files/$fileName";

        if (!file_exists($filePath)) {
            $file = fopen($filePath,"x+");
            $fileSize = filesize($filePath);
            $database->insert($fileName,$fileSize." byte",$fileType);
            echo json_encode("success");
            fclose($file);

        }else {
            echo json_encode("file exists");
            
        }

    }else{
        $fileType = "directory";
        $dirPath = "./files/$fileName";

        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777);
            $fileSize = filesize($dirPath);
            $database->insert($fileName,$fileSize." byte",$fileType);
            $database->insertToDir($fileName,$fileSize." byte");
            echo json_encode("success");

        }else {
            echo json_encode("directory exists");
        }
    }
}

?>