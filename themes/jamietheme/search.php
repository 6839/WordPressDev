<?php

get_header();
?>

<main class="container">
    <div class="main-box">

        The search results of "<?php echo get_search_query() ?>":
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


</main>



<?php

get_footer();

?>