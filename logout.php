<?php

    session_start();

    $_SESSION['logged_in'] = false;
    unset($_SESSION['logged_in']);

    header('Location: index.php');
    exit();