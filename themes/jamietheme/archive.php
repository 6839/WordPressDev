<?php

get_header();
?>

<main class="container">
    <div class="main-box">
        <div class="container-main">


            <?php 
                the_archive_title('<h1>','</h1>');
            ?>
            <div id="lists">
                <?php
                while (have_posts()) {
                    the_post();
                    get_template_part('templates/cons');
                }

                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => 'Previous',
                    'next_text' => 'Next',
                    'screen_reader_text' => ' '
                ));
                ?>
            </div>
        </div>
        <div class="sider-bar">
            <?php get_sidebar() ?>
            <?php get_sidebar('02') ?>
        

        </div>
    </div>
</main>



<?php

get_footer();

?>