<?php

    session_start();

    if($_SESSION['logged_in'] != true) {
        header('Location: index.php');
        exit();
    }

    $missing_values = [];

    if($_POST['name'] == '')
        $missing_values[] = 'Nazwa projektu';
    
    if($_POST['id'] == '')
        $missing_values[] = 'ID';

    if($_POST['plan'] == '')
        $missing_values[] = 'Rzut';

    if($_POST['tags'] == '')
        $missing_values[] = 'Znaczniki';

    $name_link = $_POST['name_link'];

    if($name_link == '')
        $name_link = preg_replace('/[[:^print:]]/', '', str_replace(' ', '', $_POST['name']));

    if(file_exists('p/'.$name_link))
        $missing_values[] = 'Projekt o tym linku już istnieje';

    if(count($missing_values) > 0) {
        echo 'ERROR - '.implode(', ', $missing_values);
        exit();
    }

    $dir = $_POST['projdir'];
    $target_dir = 'p/'.$name_link;

    $json_plan = json_decode($_POST['plan']);
    $plan = (object) ['url' => end(explode('/', $json_plan->url)), 'width' => $json_plan->width, 'height' => $json_plan->height];

    $tags = [];

    foreach(json_decode($_POST['tags']) as $tag) {
        $tag->image = end(explode('/', $tag->image));
        $tags[] = $tag;
    }

    $project = (object) ['id' => $_POST['id'],
                         'plan' => $plan,
                         'name' => $_POST['name'],
                         'name_link' => $name_link,
                         'password' => $_POST['password'],
                         'tags' => $tags,
                         'last_modified' => date('c')];

    file_put_contents($dir.'/metadata.json', json_encode($project, JSON_PRETTY_PRINT));
    file_put_contents($dir.'/password_check.php', file_get_contents('template/dir/password_check.php'));
    file_put_contents($dir.'/index.php', file_get_contents('template/dir/index.php'));

    rename($dir, $target_dir);