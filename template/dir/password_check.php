<?php

    $project = json_decode(file_get_contents('./metadata.json'));

    if($project->password === $_POST['password']) {
        echo 'OK';
        exit();
    }

    echo 'ERROR';