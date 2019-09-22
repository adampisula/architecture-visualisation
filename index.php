<?php

    session_start();

    if($_SESSION['logged_in'] == true) {
        header('Location: panel.php');
        exit();
    }

    // Redirect to home page
    //header();

    echo 'homepage/index.php<br>';

?>

<a href="login.php">Zaloguj się</a>