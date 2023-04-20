<?php

include_once "db.php";
$database = new db("localhost","root","Albion@123","fileManeger");

$json = file_get_contents('php://input');
if(!empty($json)){
    $data = json_decode($json);
    foreach($data as $filename){
        $filetype = $database->selectType($filename);
        if($filetype == "directory"){
            $dir = scandir("./files/$filename");
            if ($dir !== false && count($dir) > 2) {
                foreach ($dir as $file) {
                    $filepath = "./files/$filename". '/' . $file;
                    if (is_file($filepath)) {
                        unlink($filepath);
                    }
                }
                $database->delete($filename);
            }else{
                rmdir("./files/$filename");
                $database->delete($filename);
            }
        }else{
            $database->delete($filename);
            unlink("./files/$filename");
        }
    }
}

?>