<?php
get_header();
?>

<main class="container">
    <div class="main-box">
        <div class="card-box container-main">
            <div id="contents">
                <?php
                the_title('<h1>', '</h1>');
                ?>
                <p>Book Link:
                    <em><a style="text-decoration: underline; color: blue;" href="<?php echo get_post_meta(get_the_ID(), 'my_book_url', true); ?>">
                            <?php echo get_post_meta(get_the_ID(), 'my_book_url', true); ?>
                        </a>
                    </em>
                </p>
            </div>

            <div class="card-box comments">
                <?php
                comments_template();
                ?>

            </div>

        </div>
        <div class="sider-bar">
            <?php
            get_sidebar();
            get_sidebar('02');

            // 增加一个侧边栏，文章推荐
            $posts = get_posts(array(
                'numberposts' => 30,
                'posts_per_page' => 10,
                // 'category' => get_the_category()[0]->cat_ID,
                'orderby' => 'meta_value_num',
                'meta_key' => 'views',
                'order' => 'DESC',
                'exclude' => array(get_the_ID())
            ));

            foreach ($posts as $key => $value) {
                echo '
                    <div class="list-line">
                     <a href="' . get_permalink($value->ID) . '" target="_blank">' . $value->post_title . '</a></div>               
                ';
            }



            ?>
        </div>
    </div>
</main>



<?php

get_footer();

?>