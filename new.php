<?php

    session_start();

    if($_SESSION['logged_in'] != true) {
        header('Location: index.php');
        exit();
    }

    if(isset($_POST['id'])) {
        $project = (object) ['id' => $_POST['id'], 'name' => $_POST['name'], 'name_link' => $_POST['password'], 'last_modified' => time()];

        print_r($project);
    }

    $projid = uniqid();
    $projdir = 'temp/'.$projid.'/';

    $oldmask = umask(0);
    mkdir($projdir, 0777, true);
    umask($oldmask);

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Wizualizacje</title>
        <meta charset="utf-8">

        <link href="css/new.css" rel="stylesheet">

        <script src="js/jquery.js"></script>
        <script src="js/swal2.js"></script>
        <script src="js/lightbox.js"></script>

        <link href="css/sweetalert2.css" rel="stylesheet">
        <link href="css/lightbox.css" rel="stylesheet">
    </head>
    <body>
        <div class="side-bar">
            <!--<input type="text" class="project_id" value="<?php echo $projid; ?>" disabled>-->
            <input type="text" class="project_name" placeholder="Nazwa projektu (wyświetlana)">
            <input type="text" class="project_name_link" placeholder="Krótka nazwa (w adresie)">
            <input type="text" class="project_password" placeholder="Hasło (zostaw puste jeśli brak)">
            <table class="tags">
                <thead>Znaczniki:</thead>
                <tbody>
                </tbody>
            </table>
            <button class="save">Zapisz</button>
        </div>
        <div class="container">
            <input type="file" accept="image/jpeg, image/png" class="plan_upload" value="Dodaj rzut">
        </div>
    </body>
    <script>
        var k;
        var tags = [];
        var new_width = 0, new_height = 0;
        var planImage, planURL;  
        var planWidth, planHeight;

        var wipe = false;

        const tagRadius = 3;

        // Render all tags
        function updateTags() {
            var html = '';
            var canvas = document.getElementById('plan');
            var ctx = canvas.getContext('2d');
            
            ctx.clearRect(0, 0, new_width, new_width);
            ctx.drawImage(planImage, 0, 0, new_width, new_height);

            for(var i = 0; i < tags.length; i++) {
                // Preview tag
                html += '<tr><td class="name"><a href="' + tags[i].image + '" data-lightbox="' + tags[i].image.split('/').pop().split('.')[0] + '" data-title="' + tags[i].name + '">' + tags[i].name + '</a></td><td class="remove">✕</td></tr>';

                ctx.beginPath();
                ctx.arc(tags[i].x * k - tagRadius / 2, tags[i].y * k - tagRadius / 2, tagRadius, 0, Math.PI * 2);
                ctx.fillStyle = 'red';
                ctx.fill();
            }

            $('.tags').html(html);
        }

        // Remove tag
        $('.side-bar').on('click', '.tags td.remove', function() {
            Swal.fire({
                title: 'Usunąć znacznik?',
                text: 'Tej akcji nie można odwrócić!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Potwierdź',
                cancelButtonText: 'Zamknij'
            }).then((result) => {
                if (result.value) {
                    for(var i = 0; i < tags.length; i++) {
                        if(tags[i].name == $(this).parent().children('.name').text())
                            tags.splice(i, 1);
                    }

                    updateTags();
                }
            });
        });

        $('.plan_upload').on('change', function() {
            var formData = new FormData();
            formData.append('image_upload', $('.plan_upload')[0].files[0]);
            formData.append('projdir', '<?php echo $projdir; ?>');

            $.ajax({
                url: "upload_temp_image.php",
                type: "POST",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(url) {
                    if(url == 'ERROR') {
                        alert('Błąd przy uploadowaniu pliku.');
                        return;
                    }

                    var img = new Image();
                    var width, height;

                    img.onload = function() {
                        planImage = this;

                        width = this.width;
                        height = this.height;

                        planWidth = width;
                        planHeight = height;

                        var ratio = width / height;
                        var pratio = $('.container').width() / $('.container').height();

                        if (ratio < pratio) {
                            new_width = width * $('.container').height() / height;
                            new_height = $('.container').height();
                        }

                        else {
                            new_height = height * $('.container').width() / width;
                            new_width = $('.container').width();
                        }

                        k = new_width / width;

                        $('<canvas>').attr({
                            id: 'plan',
                            width: new_width,
                            height: new_height
                        }).appendTo('.container');

                        document.getElementById('plan').getContext('2d').drawImage(this, 0, 0, new_width, new_height);

                        function getCursorPosition(canvas, event) {
                            const rect = canvas.getBoundingClientRect();
                            
                            const x = Math.floor((event.clientX - rect.left) / k);
                            const y = Math.floor((event.clientY - rect.top) / k);

                            // Add new tag
                            Swal.fire({
                                title: 'Dodaj znacznik',
                                html:
                                '<input type="text" id="swal-input1" class="swal2-input" placeholder="Nazwa znacznika">' +
                                '<p>Panorama:</p><input type="file" id="swal-input2">',
                                showCancelButton: true,
                                cancelButtonText: 'Zamknij',
                                preConfirm: function () {
                                    return new Promise(function (resolve) {
                                        var formData = new FormData();
                                        formData.append('image_upload', $('#swal-input2')[0].files[0]);
                                        formData.append('projdir', '<?php echo $projdir; ?>');

                                        $.ajax({
                                            url: "upload_temp_image.php",
                                            type: "POST",
                                            data: formData,
                                            contentType: false,
                                            cache: false,
                                            processData: false,
                                            success: function(url) {
                                                if(url == 'ERROR') {
                                                    alert('Błąd przy uploadowaniu pliku.');
                                                    return;
                                                }

                                                resolve([
                                                    $('#swal-input1').val(),
                                                    url
                                                ]);
                                            }
                                        });
                                    });
                                },
                                onOpen: function () {
                                    $('#swal-input1').focus()
                                }
                            }).then(function (result) {
                                for(var i = 0; i < tags.length; i++) {
                                    if(tags[i].name == result.value[0]) {
                                        alert('Znacznik o tej nazwie już istnieje!');
                                        return;
                                    }
                                }

                                if(result.value[0] != '') {
                                    tags.push({ name: result.value[0], image: result.value[1], x: x, y: y });
                                    updateTags();
                                }
                            }).catch(swal.noop);
                        }

                        const canvas = document.querySelector('canvas');
                        canvas.addEventListener('mousedown', function(e) {
                            getCursorPosition(canvas, e);
                        });
                    }

                    img.src = url;
                    planURL = url;

                    $('.plan_upload').remove();
                }
            });
        });

        $('.save').on('click', function() {
            var project = {};

            project.id = '<?php echo $projid; ?>';
            project.plan = { url: planURL, width: planWidth, height: planHeight };
            project.tags = tags;
            project.name = $('.project_name').val();
            project.name_link = $('.project_name_link').val();
            project.password = $('.project_password').val();

            $.ajax({
                url: "save_project.php",
                type: "POST",
                data: {
                    id: project.id,
                    plan: JSON.stringify(project.plan),
                    tags: JSON.stringify(project.tags),
                    name: project.name,
                    name_link: project.name_link,
                    password: project.password,
                    projdir: '<?php echo $projdir; ?>'
                },
                success: function(data) {
                    if(data.split(' ')[0] == 'ERROR') {
                        alert(data);
                        return;
                    }

                    window.location.replace('panel.php');
                }
            });
        });
    </script>
</html>