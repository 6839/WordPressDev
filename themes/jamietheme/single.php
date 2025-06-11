<?php

get_header();
?>

<main class="container">
    <div class="main-box">
        <div class="card-box container-main">
            <div id="contents">
                <?php
                the_post();  // 不填这个的话，作者信息取不到
                $cateArray = get_the_category((get_the_ID()));

                the_title('<h1>', '</h1>');
                $author_url = get_author_posts_url(get_the_author_meta('ID'));
                echo '<a href="' . $author_url . '">' . get_the_author() . '</a>';

                echo get_the_date('Y-m-d H:i:s');
                ?>

                <div>
                    Viewed (<?php
                            echo set_views(get_the_ID());
                            ?>) times.
                </div>

                <div class="line">
                    <?php
                    foreach ($cateArray as $key => $value) {
                        echo $value->cat_name . ' ';
                    }
                    ?>
                </div>
                <?php

                the_content();

                wp_link_pages();
                ?>
            </div>

            <div class="navs">
                <?php
                the_post_navigation(
                    array(
                        'prev_text' => '<span class="nav-subtitle">Last post：</span> <span class="nav-title">%title</span>',
                        'next_text' => '<span class="nav-subtitle">Next post：</span> <span class="nav-title">%title</span>',
                        'screen_reader_text' => 'Jamie Recommend',
                        'class'     => 'post-navigation-jamie',
                    )
                );
                ?>
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

            ?>
            <div class="card-box">
                <h2>Featured</h2>
                <ul>
            <?php

                foreach ($posts as $key => $value) {
                    echo '
                    <li style="list-style-type: disc;">
                    <div class="list-line">
                     <a href="' . get_permalink($value->ID) . '" target="_blank">' . $value->post_title . '</a>
                    </div> 
                    </li>              
                ';
                }

            ?>
            </ul>
            </div>
            <?php



            ?>
        </div>
    </div>
</main>



<?php

get_footer();

?>