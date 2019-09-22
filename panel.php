<?php

    session_start();

    if($_SESSION['logged_in'] == false) {
        header('Location: index.php');
        exit();
    }

    $files = glob('p/*', 0);
    $projects = array();

    foreach($files as $file)
        array_push($projects, json_decode(file_get_contents($file.'/metadata.json')));

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Wizualizacje</title>
        <meta charset="utf-8">

        <script src="js/jquery.js"></script>
        <script src="js/swal2.js"></script>

        <link href="css/sweetalert2.css" rel="stylesheet">
    </head>
    <body>
        <div class="container">
            <table class="projects" border="1">
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>Nazwa</td>
                        <td>Link</td>
                        <td>Hasło</td>
                        <td>Data modyfikacji</td>
                        <td>Usuń</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($projects as $project): ?>
                    <tr>
                        <td class="id"><?php echo $project->id; ?></td>
                        <td class="name" class="editable"><?php echo $project->name; ?></td>
                        <td class="name_link" class="editable"><a href="<?php echo 'p/'.$project->name_link; ?>" target="_blank"><?php echo $project->name_link; ?></a></td>
                        <td class="password" class="editable"><?php echo $project->password; ?></td>
                        <td class="last_modified"><?php echo $project->last_modified; ?></td>
                        <td class="remove">✕</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="new.php">Nowy projekt</a>
        <br>
        <a href="logout.php">Wyloguj się</a>
    </body>
    <script>
        $('.projects .remove').on('click', function() {
            var project_name = $(this).parent().children('.name').text();

            Swal.fire({
                title: project_name,
                text: 'Czy na pewno chcesz usunąć ten projekt?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Tak!'
            }).then((result) => {
                if (result.value) {
                    var project_link = $(this).parent().children('.name_link').text();

                    window.location.href = 'remove_project.php?directory=' + project_link;
                }
            });
        });
    </script>
</html>