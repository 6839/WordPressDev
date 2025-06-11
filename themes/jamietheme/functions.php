<?php

register_nav_menus(array(
    'primary' => 'Main Menu',
    'footer' => 'Footer Menu',
    'menus123' => 'Menu 666',
    'menukey01' => 'menuvalue01',
    'menukey02' => 'menuvalue02',
    'menukey03' => 'menuvalue03',
));

function custom_excerpt_length($length)
{
    return 20; // 修改文章摘要长度为20个单词
}
add_filter('excerpt_length', 'custom_excerpt_length');

add_theme_support('post-thumbnails'); // 启用特色图片
add_theme_support('post-formats', array('aside', 'gallery', 'quote', 'image', 'video')); // 启用文章格式
add_theme_support('automatic-feed-links'); //添加自动Feed链接到`<head>`中，使用户能够订阅文章和评论的RSS Feed。

// 启用小工具
// add_theme_support('widgets');

//functions 自定义侧边栏
function my_custom_sidebar()
{
    register_sidebar(  // register_sidebar自带已经启用了小工具，所以上面不用再通过add_theme_support启用
        array(
            'name' => ' Testing sidebar01 ', // 侧边栏名称
            'id' => 'test-side-bar01', // 侧边栏 ID
            'description' => ' Here is the description of sidebar ', // 侧边栏描述
            'before_widget' => '<div class="widget-content">', // 侧边栏前面的代码
            'after_widget' => "</div>", // 侧边栏后面的代码
            'before_title' => '<h3 class="widget-title">', // 侧边栏标题的前面的代码
            'after_title' => '</h3>', // 侧边栏标题的后面的代码
        )
    );

    // 可同时注册多个小工具
    register_sidebar(
        array(
            'name' => ' Testing sidebar02 ', // 侧边栏名称
            'id' => 'test-side-bar02', // 侧边栏 ID
            'description' => ' Here is the description of sidebar ', // 侧边栏描述
            'before_widget' => '<div class="widget-content2">', // 侧边栏前面的代码
            'after_widget' => "</div>", // 侧边栏后面的代码
            'before_title' => '<h3 class="widget-title2">', // 侧边栏标题的前面的代码
            'after_title' => '</h3>', // 侧边栏标题的后面的代码
        )
    );
}
add_action('widgets_init', 'my_custom_sidebar');

// 解决评论头像不显示问题
if (! function_exists('get_cravatar_url')) {
    /**
     * 替换 Gravatar 头像为 Cravatar 头像
     *
     * Cravatar 是 Gravatar 在中国的完美替代方案，您可以在 https://cravatar.com 更新您的头像
     */
    function get_cravatar_url($url)
    {
        $sources = array(
            'www.gravatar.com',
            '0.gravatar.com',
            '1.gravatar.com',
            '2.gravatar.com',
            'secure.gravatar.com',
            'cn.gravatar.com',
            'gravatar.com',
        );
        return str_replace($sources, 'cravatar.cn', $url);
    }
    add_filter('um_user_avatar_url_filter', 'get_cravatar_url', 1);
    add_filter('bp_gravatar_url', 'get_cravatar_url', 1);
    add_filter('get_avatar_url', 'get_cravatar_url', 1);
}
if (! function_exists('set_defaults_for_cravatar')) {
    /**
     * 替换 WordPress 讨论设置中的默认头像
     */
    function set_defaults_for_cravatar($avatar_defaults)
    {
        $avatar_defaults['gravatar_default'] = 'Cravatar 标志';
        return $avatar_defaults;
    }
    add_filter('avatar_defaults', 'set_defaults_for_cravatar', 1);
}
if (! function_exists('set_user_profile_picture_for_cravatar')) {
    /**
     * 替换个人资料卡中的头像上传地址
     */
    function set_user_profile_picture_for_cravatar()
    {
        return '<a href="https://cravatar.com" target="_blank"> 您可以在 Cravatar 修改您的资料图片</a>';
    }
    add_filter('user_profile_picture_description', 'set_user_profile_picture_for_cravatar', 1);
}


//评论模板
function custom_comment($comment, $args, $depth)
{
    $GLOBALS['comment'] = $comment;
    $ava = get_avatar($comment, '48'); //获取用户的头像，第一个参数是用户评论对象，第二个参数设置头像大小
    $author_link = get_comment_author_link(); // 获取评论者
    //评论分页（在后台可设置）
    //获取当前评论列表页码
    // $cpage = get_query_var('cpage');
    //获取每页评论显示数量
    // $cpp = get_option('comments_per_page');
    // $author = get_comment_author();
    // echo $author;

    echo '
    <li class="comment-list">
        <div class="avatar">
        ' . $ava . '
        </div>
        <div class="comment_content">
            <div class="comment_author">
                ' . $author_link . '
            </div>
            
            <div class="comment_time">
           ' . get_comment_time('Y-m-d H:i:s') . '
            </div>
        ';
    // echo 结束后，'问号大于号' 表示当前 PHP 代码块结束，后续内容作为纯 HTML处理（无需 PHP 解析）
?>
    <div class="edit-line">
        <?php
        // 显示评论的编辑链接 
        edit_comment_link('Edit', '<p class="edit-link">', '</p>');
        ?>
        <div class="reply">
            <?php
            // 显示评论的回复链接 
            comment_reply_link(array_merge($args, array(
                'reply_text' =>  'Reply',
                'after'      =>  ' <span>&darr;</span>',
                'depth'      =>  $depth,
                'max_depth'  =>  $args['max_depth']
            )));
            ?>
        </div>
    </div>
    <?php
    comment_text()
    ?>
    </div>
    </li>
<?php
    // 最后一行 <?php 用于开启新的 PHP 代码块
}



// 修改文章阅读次数
function set_views($post_id){
    $key = 'views';
    $current_count = get_post_meta($post_id,$key,true);
    if($current_count == ''){
        delete_post_meta($post_id,$key);
        add_post_meta($post_id,$key,1);
    }else {
        update_post_meta($post_id,$key,$current_count+1);
    }
    $new_count = get_post_meta($post_id,$key,true);
    return $new_count;
}

define('_MS_VERSION','v1.0.0');
function ms1_scripts() {  
	wp_enqueue_style( 'style', get_stylesheet_uri(),array(),_MS_VERSION);
	wp_enqueue_style( 'index_style', get_template_directory_uri() . '/assets/css/index.css',array(),_MS_VERSION);
	wp_enqueue_script('test-jq',get_template_directory_uri() . '/assets/js/jquery.js',array(),_MS_VERSION,array(
        'in_footer' => true
    ));

    if(is_single()){  // 只有在single.php文章详情页才会高亮
        wp_enqueue_style( 'high_style', 'https://cdn.bootcdn.net/ajax/libs/highlight.js/11.11.1/styles/atom-one-dark.min.css',array());
        wp_enqueue_script('high_js','https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js');
    }
}
add_action( 'wp_enqueue_scripts', 'ms1_scripts' );



?>