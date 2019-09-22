<?php

    //ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

    session_start();

    if($_SESSION['logged_in'] != true) {
        header('Location: index.php');
        exit();
    }

    $target_dir = $_POST['projdir'];
    $target_file = $target_dir.uniqid().'.'.end(explode('.', $_FILES['image_upload']['name']));
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if(move_uploaded_file($_FILES['image_upload']['tmp_name'], $target_file))
        echo $target_file;
    
    else
        echo "ERROR";

?>