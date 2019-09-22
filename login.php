<?php

    session_start();

    // Remove that ↓
    $_SESSION['logged_in'] = true;

    if($_SESSION['logged_in'] == true) {
        header('Location: panel.php');
    }