<script src="../../js/jquery.js"></script>
<?php

    $project = json_decode(file_get_contents('metadata.json'));

    if(strlen($project->password) > 0) :
?>
    <script>
        var pwd_input = prompt('Podaj hasło');

        $.ajax({
            url: 'password_check.php',
            type: 'POST',
            data: {
                password: pwd_input
            },
            success: function(res) {
                if(res != 'OK')
                    window.location.href = '../../index.php';
            }
        });
    </script>
<?php 
    endif;
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $project->name; ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <script src="../../js/aframe.js"></script>

        <link href="../../css/project_view.css" rel="stylesheet">
        <link href="../../css/fontface.css" rel="stylesheet">
        <link href="../../css/fontawesome.css" rel="stylesheet">
        <link href="../../css/modal.css" rel="stylesheet">

        <script>
            var project = JSON.parse('<?php echo json_encode($project); ?>');
        </script>
    </head>
    <body>
        <?php

            $append_html = '';

            $append_html .= '<div class="link-nav layer';
            
            if(count($project->tags) > 2)
                $append_html .= ' foldable"><div class="hamburger"><span></span><span></span><span></span></div>';

            else
                $append_html .= '">';

            foreach($project->tags as $tag)
                $append_html .= '<div class="link" name="'.$tag->name.'">'.$tag->name.'</div>';

            $append_html .= '</div>';

        ?>
        <div class="map">
            <img src="<?php echo $project->plan->url; ?>" class="plan contain">
        </div>
        <div class="infoc logo">
            <!--<img src="../../images/brand-logo-white.png">-->
            <img src="../../images/brand-logo.png">
        </div>
        <div class="modal">
            <div class="modal-content">
                <div class="modal-body">
                    <span class="close-modal"><i class="fas fa-times"></i></span>
                    <center>
                        <br><br>
                        <img src="../../images/brand-logo.png" width=400>
                        <br>
                        <h2 style="font-family: 'Roboto Light', sans-serif; font-weight: lighter;">Robimy wizualizacje!</h4>
                        <br>
                    </center>
                </div>
            </div>
        </div>
    </body>
    <script>
        const fadingTime = 600;

        $('body').on('click', '.tag, .link', function() {
            for(var i = 0; i < project.tags.length; i++) {
                if(project.tags[i].name == $(this).attr('name')) {
                    $('.layer').fadeOut(fadingTime);
                    $('.layer').remove();

                    $('body').append('<a-scene background="color: #FAFAFA" class="layer" style="display: none;"><a-entity camera look-controls="reverseMouseDrag: true"></a-entity><a-sky src="' + project.tags[i].image + '" rotation="0 0 0"></a-sky></a-scene><div class="layer close"><i class="fas fa-times"></i></div><?php echo $append_html; ?><div class="layer info infoc"><i class="fas fa-info"></i></div>');
                    $('.layer').fadeIn(fadingTime);
                    return;
                }
            }
        });

        $('body').on('click', '.close.layer', function() {
            $('.layer').fadeOut(fadingTime);
            setTimeout(function() {
                $('.layer').remove();
            }, fadingTime);
        });

        function renderTags() {
            $('.tag').remove();

            var k = $('.plan').width() / project.plan.width;
            var offset = $('.plan').offset();

            for(var i = 0; i < project.tags.length; i++)
                $('.map').append('<div class="tag" title="' + project.tags[i].name + '" style="left: ' + (project.tags[i].x * k * 8 + offset.left) + 'px; top: ' + (project.tags[i].y * k * 8 + offset.top) + 'px;" name="' + project.tags[i].name + '"></div>');
        }

        renderTags();
        window.onresize = renderTags;

        $('body').on('click', '.infoc', function() {
            $('.modal').css('display', 'block');
        });

        $('.close-modal').on('click', function() {
            $('.modal').css('display', 'none');
        });

        $(window).on('click', function(event) {
            if(event.target == document.getElementsByClassName('modal')[0]) {
                $('.modal').css('display', 'none');
            }
        });
    </script>
</html>