<?php
/*
Plugin Name: plugin-jz
Version: 1.0
Description: jz01
Author: Jamie Zhi
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: plugin
*/

// set timezone
date_default_timezone_set('Australia/Melbourne');

// 定义插件启动时候调用的方法
// 作用：当插件被激活时，触发 jamie_copyright_install 函数。
// 参数：__FILE__ 指向当前插件文件，jamie_copyright_install 是激活时的回调函数
register_activation_hook(__FILE__, 'jamie_copyright_install');

// function register_activation_hook( $file, $callback ) {
// 	$file = plugin_basename( $file );
// 	add_action( 'activate_' . $file, $callback );
// }

// add_action( 'activate_' . plugin_basename( __FILE__ ), 'jamie_copyright_install' );

function jamie_copyright_install()
{
    // 插件启动，添加一个默认的版权信息
    $value = '<div id="footer" style="text-align: center; padding: 20px 0;">Copyright @ Jamie 2025 ' . home_url() . ' ' . bloginfo('name') . '</div>';
    // 使用 update_option 将版权信息保存到 WordPress 选项表，键为 jamie_copyright_text
    update_option("jamie_copyright_text", $value);
}


// 作用：在 WordPress 页面的 </body> 标签前（wp_footer 钩子位置），执行 jamie_copyright_insert 函数，输出版权信息
// 在footer.php中，有wp_footer()，而这个wp_footer()函数里面代码是do_action( 'wp_footer' );，所以就实现了调用这个钩子上挂载的函数jamie_copyright_insert
add_action("wp_footer", "jamie_copyright_insert");
function jamie_copyright_insert()
{
    // 通过 get_option 获取之前保存的版权信息（jamie_copyright_text），并输出到页面底部
    echo get_option("jamie_copyright_text");
}



// 当新建一篇文章时，点击保存草稿后，就会触发do_action('save_post')从而执行save_post_meta函数，在页面的自定义查询字段中出现一个save-time的字段和时间值
add_action('save_post', 'save_post_meta', 10, 2);
function save_post_meta($post_id, $post)
{
    update_post_meta($post_id, "save-time", "update time：" . date("Y-m-d h:i:s"));
}


//在输出内容之前，给页面管理添加摘要功能
add_action('init', 'jamie_add_excerpts_to_pages');
function jamie_add_excerpts_to_pages()
{
    //给页面管理添加摘要的功能
    add_post_type_support('page', array('excerpt'));
}

//wp_head钩子
add_action('wp_head', 'jamie_wp_head');
function jamie_wp_head()
{
    //只有首页输出描述
    if (is_home()) {
?>
        <meta name="description" content="<?php bloginfo('description'); ?>" />
    <?php
    }
}


//评论被添加的时候触发
add_action('wp_insert_comment', 'comment_inserted', 10, 2);
function comment_inserted($comment_id, $comment_object)
{
    // 获取该评论所在文章的评论总数
    $comments_count = wp_count_comments($comment_object->comment_post_ID);

    $commentarr = array();
    $commentarr['comment_ID'] = $comment_id;
    $commentarr['comment_content'] = "No.{$comments_count->total_comments} comment: " . $comment_object->comment_content;
    // output: 第24个评论：test11

    wp_update_comment($commentarr);
}


add_action('user_register', 'myplugin_registration_save', 10, 1);
function myplugin_registration_save($user_id)
{

    //将新用户的个人说明，设置为注册时间
    wp_update_user(array('ID' => $user_id, 'description' => "register time：" . date("Y-m-d H:i:s")));
}



// 限制首页显示文章内容长度
function jamie_wp_mul_excerpt($content){
    if(is_home()){
        $myexcerpt = substr($content,0,255);
        return $myexcerpt;
    }else {
        return $content;
    }
}
add_filter('the_content','jamie_wp_mul_excerpt');


// 给所有文章内容插入前后缀
// function jamie_filter_fun_preffix($value)
// {
//     return date("Y-m-d H:i:s") . '<br>' . $value;
// }
// function jamie_filter_fun_suffix($value)
// {
//     return $value . '<br>' . ' welcom to my wordpress.';
// }
// add_filter("the_content", "jamie_filter_fun_preffix");
// add_filter("the_content", "jamie_filter_fun_suffix");



// 自定义小工具，使用phpstudy和local，效果都出不来，后面有空再研究
class Jamie_New_Widget01 extends WP_Widget
{

    // function __construct()
    public function __construct()
    {
        $widget_ops = array(
            'class_name' => 'jamie_new_widget',
            'description' => 'show profile',
        );
        parent::__construct('jamie_new_widget', 'profile', $widget_ops);
    }

    // 前台显示
    function widget($args, $instance)
    {
        extract($args);

        $title = apply_filters('widget_title', $instance['title']);
        $xingming = empty($instance['xingming']) ? '' : $instance['xingming'];
        $book = empty($instance['book']) ? '' : $instance['book'];

        echo $before_widget;
        echo '<p>标题：' . $title . '</p>';
        echo '<p>姓名：' . $xingming . '</p>';
        echo '<p>著作：' . $book . '</p>';
        echo $after_widget;
    }

    // 后台设置的值保存到数据库中，然后供前台展示
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        $instance['title'] = strip_tags(trim($new_instance['title']));
        $instance['xingming'] = strip_tags(trim($new_instance['xingming']));
        $instance['book'] = strip_tags(trim($new_instance['book']));

        return $instance;
    }
    // 后台表单
    function form($instance)
    {
        $defaults = array('title' => '黄聪的个人信息', 'xingming' => '黄聪', 'book' => '《SEO的道与术》、《跟黄聪学wordpress主题开发》');
        $instance = wp_parse_args((array) $instance, $defaults);

        $title = $instance['title'];
        $xingming = $instance['xingming'];
        $book = $instance['book'];

    ?>
        <p>标题：<input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
        <p>姓名：<input class="widefat" name="<?php echo $this->get_field_name('xingming'); ?>" type="text" value="<?php echo esc_attr($xingming); ?>" /></p>
        <p>著作：<textarea class="widefat" name="<?php echo $this->get_field_name('book'); ?>"><?php echo esc_attr($book); ?></textarea></p>
    <?php
    }
}

add_action('widgets_init', 'jamie_register_widgets');

function jamie_register_widgets()
{
    register_widget('Jamie_New_Widget01');
}

add_theme_support('widgets');


// 创建菜单+自定义页面【涉及到wrap类、消息、按钮、表单、表格、分页】
add_action('admin_menu', 'jamie_create_menu');
function jamie_create_menu()
{
    // 创建顶级菜单
    add_menu_page(
        'Jamie Plugin Page',
        'Jamie Top Plugin',
        'manage_options',
        'jamie_copyright',
        'jamie_settings_page',
    );

    // 创建子菜单
    add_submenu_page(
        'jamie_copyright',
        'About Jamie Plugin',
        'Jamie Sub Plugin',
        'manage_options',
        'jamie_copyright_about',
        'jamie_create_submenu_menu'
    );
}

function jamie_settings_page()
{
    ?>
    <div class="wrap">
        <h2>Plugin Top Menu</h2>
        <div id="message" class="updated">
            <p><strong>Setting saved successfully</strong></p>
        </div>
        <div id="message" class="error">
            <p><strong>Error saving</strong></p>
        </div>
        <p>
            <input type="submit" name="Save" value="save setting" />
            <input type="submit" name="Save" value="save setting" class="button" />
            <input type="submit" name="Save" value="save setting" class="button button-primary" />
            <input type="submit" name="Save" value="save setting" class="button button-secondary" />
            <input type="submit" name="Save" value="save setting" class="button button-large" />
            <input type="submit" name="Save" value="save setting" class="button button-hero" />
        </p>


        <p>
            <a href="#">Search</a>
            <a href="#" class="button">Search</a>
            <a href="#" class="button button-primary">Search</a>
            <a href="#" class="button button-secondary">Search</a>
            <a href="#" class="button button-large">Search</a>
            <a href="#" class="button button-small">Search</a>
            <a href="#" class="button button-hero">Search</a>
        </p>

        <form method="POST" action="">
            <table class="form-table">
                <tr valign="top">
                    <th><label for="xingming">Name：</label></th>
                    <td><input id="xingming" name="xingming" /></td>
                </tr>
                <tr valign="top">
                    <th><label for="shenfen">ID：</label></th>
                    <td>
                        <select name="shenfen">
                            <option value="在校">School</option>
                            <option value="毕业">Graduation</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th><label for="tongyi">Agree to register</label></th>
                    <td><input type="checkbox" name="tongyi" /></td>
                </tr>
                <tr valign="top">
                    <th><label for="xingbie">Gender</label></th>
                    <td>
                        <input type="radio" name="xingbie" value="Male" /> Male
                        <input type="radio" name="xingbie" value="Female" /> Female
                    </td>
                </tr>
                <tr valign="top">
                    <th><label for="beizhu">Note</label></th>
                    <td><textarea name="beizhu"></textarea></td>
                </tr>
                <tr valign="top">
                    <td>
                        <input type="submit" name="save" value="Save" class="button-primary" />
                        <input type="submit" name="reset" value="Reset" class="button-secondary" />
                    </td>
                </tr>
            </table>
        </form>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Jamie01</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Jamie02</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Jamie03</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                </tr>
            </tfoot>
        </table>

        <div class="tablenav">
            <div class="tablenav-pages">
                <span class="displaying-num">Page 1 of 458</span>
                <span class="page-numbers current">1</span>
                <a href="#" class="page-numbers">2</a>
                <a href="#" class="page-numbers">3</a>
                <a href="#" class="page-numbers">4</a>
                <a href="#" class="next page-numbers">>></a>
            </div>
        </div>
    </div>
<?php
}

function jamie_create_submenu_menu()
{
?>
    <h2>Plugin Sub Menu</h2>
<?php
}






?>