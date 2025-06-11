<div class="comments-box">
    <h3>Total <span><?php echo get_comments_number(); ?></span></h3>

    <!-- 评论列表内容 -->
    <?php
    // wp_list_comments(); 
    wp_list_comments(array(
        // 'walker'            => null,
        // 'max_depth'         => '',
        'style'             => 'ul',
        'callback'          => 'custom_comment',
        // 'end-callback'      => null,
        'type'              => 'all',
        // 'page'              => '',
        'per_page'          => '',
        // 'avatar_size'       => 20,
        'reverse_top_level' => true,
        'reverse_children'  => true,
        // 'format'            => current_theme_supports( 'html5', 'comment-list' ) ? 'html5' : 'xhtml',
        // 'short_ping'        => false,
        // 'echo'              => true,
    ));

    ?>

    <!-- 评论分页，需要网页后台设置--讨论--评论分页，勾选开关，并设置每页显示数量 -->
    <?php the_comments_navigation(); ?>

    <!-- 评论输入框 -->
    <?php comment_form(array(
        "fields" => array(
            "author" => "Please input your name: <input name='author'>",
            'email' => '<input type="email" id="email" name="email" class="form-control" placeholder="email*" required>',
            'others' => 'other note',
            'others2' => 'other note02',
        ),
        "logged_in_as" => ' '

    )); ?>

</div>