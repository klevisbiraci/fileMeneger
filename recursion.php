<?php

class rec {
    
    // public function searchFiles($directory, $pattern, $fileType) {
    //     $matchingFiles = [];
    
    //     $files = glob($directory . '/*');
    
    //     if ($fileType === "file") {
    //         foreach ($files as $file) {
    //             if (is_dir($file)) {
    //                 $matchingFiles = array_merge($matchingFiles, $this->searchFiles($file, $pattern ,$fileType));
        
    //             } elseif (is_file($file) && fnmatch($pattern, basename($file))) {
    //                 $matchingFiles[] = $file;
                    
    //             }
    //         }
    
    //     } else {
    //         foreach ($files as $file) {
    //             if (!is_file($file) && !fnmatch($pattern, basename($file))) {
    //                 $matchingFiles = array_merge($matchingFiles, $this->searchFiles($file, $pattern ,$fileType));
    
    //             } else {
    //                 $matchingFiles[] = $file;
    
    //             }
    //         }
    //     }
    
    //     return $matchingFiles;
    // }


    public function searchFiles($directory, $pattern, $fileType) {
        $matchingFiles = [];

        $files = glob($directory . '/*');

        if ($fileType === "file") {
            foreach ($files as $file) {
                if (is_dir($file)) {
                    $matchingFiles = array_merge($matchingFiles, $this->searchFiles($file, $pattern ,$fileType));
        
                } elseif (is_file($file) && fnmatch($pattern, basename($file))) {
                    $matchingFiles[] = $file;
                    
                }
            }

        } else {
            foreach ($files as $file) {
                if (is_dir($file) && !fnmatch($pattern, basename($file))) {
                    $matchingFiles = array_merge($matchingFiles, $this->searchFiles($file, $pattern ,$fileType));
                    
                } elseif (is_dir($file) && fnmatch($pattern, basename($file))) {
                    $matchingFiles[] = $file;

                }
            }
        }

        return $matchingFiles;
    }

    public function copyDirectory($source, $destination) {
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
                $this->copyDirectory($sourceFile, $destinationFile);
    
            } else {
                copy($sourceFile, $destinationFile);
    
            }
        }
        closedir($dir);
    }

    public function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return;
        }
        
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteDirectory($file);
    
            } else {
                unlink($file);
            }
        }
        
        rmdir($dir);
    }
    
}

?>