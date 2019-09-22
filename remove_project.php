<?php

    session_start();

    if($_SESSION['logged_in'] != true) {
        header('Location: index.php');
        exit();
    }

    function rrmdir($dir) { 
        if (is_dir($dir)) { 
            $objects = scandir($dir); 

            foreach ($objects as $object) { 
                if ($object != "." && $object != "..") { 
                    if (filetype($dir."/".$object) == "dir")
                        rrmdir($dir."/".$object);
                    else
                        unlink($dir."/".$object); 
                } 
            } 

            reset($objects); 
            rmdir($dir); 
        } 
    } 

    rrmdir('p/'.$_GET['directory']);

    header('Location: panel.php');