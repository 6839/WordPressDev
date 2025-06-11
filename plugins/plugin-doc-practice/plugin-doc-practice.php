<?php
/*
Plugin Name: plugin-doc-practice
Version: 1.0
Description: 以下是https://www.wpzhiku.com/document/wordpress-plugin-basics/案例
Author: Jamie Zhi
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: plugin
*/

// set timezone
date_default_timezone_set('Australia/Melbourne');

// short code
function wporg_shortcode($atts = [], $content = null, $tag = '')
{
    // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);

    // override default attributes with user attributes
    $wporg_atts = shortcode_atts([
        'title' => 'WordPress.org',
    ], $atts, $tag);

    // start output
    $o = '';

    // start box
    $o .= '<div class="wporg-box">';

    // title
    $o .= '<h2>' . esc_html__($wporg_atts['title'], 'wporg') . '</h2>';

    // enclosing tags
    if (!is_null($content)) {
        // secure output by executing the_content filter hook on $content
        $o .= apply_filters('the_content', $content);

        // run shortcode parser recursively
        $o .= do_shortcode($content);
    }

    // end box
    $o .= '</div>';

    // return output
    return $o;
}

function wporg_shortcodes_init()
{
    add_shortcode('wporg', 'wporg_shortcode');
}

add_action('init', 'wporg_shortcodes_init');





// -----------设置、选项--------

function wporg_settings_init01()
{
    // 为 阅读 页面注册新设置
    register_setting('reading', 'wporg_setting_name');

    // 在阅读页面上注册新分节
    add_settings_section(
        'wporg_settings_section',
        'WPOrg Settings Section',
        'wporg_settings_section_cb',
        'reading'
    );

    // 阅读页面中，在 the wporg_settings_section 分节上注册新设置
    add_settings_field(
        'wporg_settings_field',
        'WPOrg Setting',
        'wporg_settings_field_cb',
        'reading',
        'wporg_settings_section'
    );
}

/**
 * 注册 wporg_settings_init 到 admin_init Action 钩子
 */
add_action('admin_init', 'wporg_settings_init01');


/**
 * 回调函数
 */
// 分节内容回调。该函数输出的 HTML 会显示在区域标题下方，作为简要说明
function wporg_settings_section_cb()
{
    echo '<p>WPOrg Section Introduction.</p>';
}

// 字段内容回调
function wporg_settings_field_cb()
{
    // 获取我们使用 register_setting() 注册的字段的值
    $setting = get_option('wporg_setting_name');

    // 输出字段
    // 通过 add_settings_field 将输入字段（如文本框）与 register_setting 的选项关联。当用户提交表单时，WordPress 自动将字段值保存到 wp_options 表（底层通过 update_option 实现，属于选项 API）
?>
    <input type=text name=wporg_setting_name value=<?php echo isset($setting) ? esc_attr($setting) : ''; ?>>
<?php
}


// 自定义设置案例
function wporg_options_page()
{
    // add top level menu page
    add_menu_page(
        'WPOrg',
        'WPOrg Options',
        'manage_options',
        'wporg-jamie',
        'wporg_options_page_html'
    );
}

function wporg_options_page_html()
{
    // check user capabilities
    if (! current_user_can('manage_options')) {
        return;
    }

    // add error/update messages

    // check if the user have submitted the settings
    // wordpress will add the settings-updated $_GET parameter to the url
    if (isset($_GET['settings-updated'])) {
        // add settings saved message with the class of updated
        add_settings_error('wporg_messages', 'wporg_message', __('Settings Saved', 'wporg'), 'updated');
    }

    // show error/update messages
    settings_errors('wporg_messages');
?>
    <div class=wrap>
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action=options.php method=post>
            <?php
            // output security fields for the registered setting wporg
            // settings_fields('wporg');
            // output setting sections and their fields
            // (sections are registered for wporg, each field is registered to a specific section)
            do_settings_sections('wporg');
            // output save settings button
            submit_button('Save Settings');
            ?>
        </form>
    </div>
<?php
}
add_action('admin_menu', 'wporg_options_page');


function wporg_settings_init02()
{
    // 将选项 wporg_options 注册到选项组 wporg，允许后续通过 do_settings_sections('wporg') 渲染。
    register_setting('wporg', 'wporg_options');

    // function add_settings_section( $id, $title, $callback, $page, $args = array() ) {
    add_settings_section(
        'wporg_section_developers',
        __('The Matrix has you.', 'wporg'),
        'wporg_section_developers_cb',
        'wporg'
    );
    // function add_settings_field( $id, $title, $callback, $page, $section = 'default', $args = array() ) {
    add_settings_field(
        'wporg_field_pill',
        __('Pill', 'wporg'),
        'wporg_field_pill_cb',
        'wporg',
        'wporg_section_developers',
        [
            'label_for'         => 'wporg_field_pill',
            'class'             => 'wporg_row',
            'wporg_custom_data' => 'custom',
        ]
    );
}

function wporg_section_developers_cb($args)
{
?>
    <p id=<?php echo esc_attr($args['id']); ?>><?php esc_html_e('Follow the white rabbit.', 'wporg'); ?></p>
<?php
}

function wporg_field_pill_cb($args)
{
    $options = get_option('wporg_options');
?>
    <select id=<?php echo esc_attr($args['label_for']); ?>
        data-custom=<?php echo esc_attr($args['wporg_custom_data']); ?>
        name=wporg_options[<?php echo esc_attr($args['label_for']); ?>]>
        <option value=red <?php echo isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], 'red', false)) : (''); ?>>
            <?php esc_html_e('red pill', 'wporg'); ?>
        </option>
        <option value=blue <?php echo isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], 'blue', false)) : (''); ?>>
            <?php esc_html_e('blue pill', 'wporg'); ?>
        </option>
    </select>
    <p class=description>
        <?php esc_html_e('You take the blue pill and the story ends. You wake in your bed and you believe whatever you want to believe.', 'wporg'); ?>
    </p>
    <p class=description>
        <?php esc_html_e('You take the red pill and you stay in Wonderland and I show you how deep the rabbit-hole goes.', 'wporg'); ?>
    </p>
<?php
}
add_action('admin_init', 'wporg_settings_init02');

// 自定义元数据盒子
function wporg_add_custom_box()
{
    $screens = ['post', 'wporg_cpt'];
    foreach ($screens as $screen) {
        add_meta_box(
            'wporg_box_id',           // Unique ID
            'Custom Meta Box Title',  // Box title
            'wporg_custom_box_html',  // Content callback, must be of type callable
            $screen                   // Post type
        );
    }
}

function wporg_custom_box_html($post)
{
    $value = get_post_meta($post->ID, '_wporg_meta_key', true);
?>
    <label for=wporg_field>Description for this field</label>
    <select name=wporg_field id=wporg_field class=postbox>
        <option value=>Select something...</option>
        <option value=something <?php selected($value, 'something'); ?>>Something</option>
        <option value=else <?php selected($value, 'else'); ?>>Else</option>
    </select>
<?php
}

add_action('add_meta_boxes', 'wporg_add_custom_box');

function wporg_save_postdata($post_id)
{
    if (array_key_exists('wporg_field', $_POST)) {
        update_post_meta(
            $post_id,
            '_wporg_meta_key',
            $_POST['wporg_field']
        );
    }
}
add_action('save_post', 'wporg_save_postdata');


// 自定义文章类型
function wporg_custom_post_type()
{
    register_post_type(
        'wporg_product',
        [
            'labels'      => [
                'name'          => __('Products'),
                'singular_name' => __('Product'),
            ],
            'public'      => true,
            'has_archive' => true,
        ]
    );
}

add_action('init', 'wporg_custom_post_type');


// 自定义分类法
function wporg_register_taxonomy_course()
{
    $labels = [
        'name'              => _x('Courses', 'taxonomy general name'),
        'singular_name'     => _x('Course', 'taxonomy singular name'),
        'search_items'      => __('Search Courses'),
        'all_items'         => __('All Courses'),
        'parent_item'       => __('Parent Course'),
        'parent_item_colon' => __('Parent Course:'),
        'edit_item'         => __('Edit Course'),
        'update_item'       => __('Update Course'),
        'add_new_item'      => __('Add New Course'),
        'new_item_name'     => __('New Course Name'),
        'menu_name'         => __('Course'),
    ];
    $args   = [
        'hierarchical'      => true, // make it hierarchical (like categories)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'course'],
        'show_in_rest'      => true, // 新增，支持块编辑器和 REST API
    ];
    register_taxonomy('course', ['post'], $args);
}

add_action('init', 'wporg_register_taxonomy_course');





//  用户、角色和能力
function wporg_usermeta_form_field_birthday($user)
{
?>
    <h3>It's Your Birthday</h3>
    <table class=form-table>
        <tr>
            <th>
                <label for=birthday>Birthday</label>
            </th>
            <td>
                <input type=date
                    class=regular-text ltr
                    id=birthday
                    name=birthday
                    value=<?= esc_attr(get_user_meta($user->ID, 'birthday', true)); ?>
                    title=Please use YYYY-MM-DD as the date format.
                    pattern=(19[0-9][0-9]|20[0-9][0-9])-(1[0-2]|0[1-9])-(3[01]|[21][0-9]|0[1-9])
                    required>
                <p class=description>
                    Please enter your birthday date.
                </p>
            </td>
        </tr>
    </table>
<?php
}


function wporg_usermeta_form_field_birthday_update($user_id)
{
    // check that the current user have the capability to edit the $user_id
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    // create/update user meta for the $user_id
    return update_user_meta(
        $user_id,
        'birthday',
        $_POST['birthday']
    );
}

add_action('edit_user_profile', 'wporg_usermeta_form_field_birthday');
add_action('show_user_profile', 'wporg_usermeta_form_field_birthday');

add_action('personal_options_update', 'wporg_usermeta_form_field_birthday_update');
add_action('edit_user_profile_update', 'wporg_usermeta_form_field_birthday_update');


// jQuery+Ajax
function show_test_ajax()
{
?>
    <h3>Test jQuery+Ajax:</h3>
    <form id=radioform>
        <table>
            <tbody>
                <tr>
                    <td><label>Jamie:</label></td>
                    <td><input class=pref checked=checked name=book type=radio value=Jamie />Jamie</td>
                </tr>
                <tr>
                    <td><label>themedemos:</label></td>
                    <td><input class=pref name=book type=radio value=themedemos />themedemos</td>
                </tr>
            </tbody>
        </table>
    </form>
<?php
}
add_action('show_user_profile', 'show_test_ajax');


function my_enqueue($hook)
{
    // if ('myplugin_settings.php' != $hook) return;
    wp_enqueue_script(
        'ajax-script',
        plugins_url('/js/myjquery.js', __FILE__),
        array('jquery')
    );
    $title_nonce = wp_create_nonce('title_example');
    wp_localize_script('ajax-script', 'my_ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => $title_nonce,
    ));
}
add_action('admin_enqueue_scripts', 'my_enqueue');



function my_ajax_handler()
{
    check_ajax_referer('title_example');
    update_user_meta(get_current_user_id(), 'title_preference', $_POST['title']);

    $args = array(
        'author_name'    => sanitize_text_field($_POST['title']),
        'post_type'      => 'any',           // 查询所有内容类型
        'post_status'    => 'publish',       // 仅统计已发布文章（可改为 'any' 包含草稿）
        'posts_per_page' => -1,              // 获取全部结果
        'fields'         => 'ids',           // 仅返回文章ID（提升性能）
    );

    $posts = get_posts($args);  // 使用 get_posts() 避免 WP_Query 缓存问题
    $count = count($posts);     // 直接统计文章数量
    echo $_POST['title'] . ' (' . $count . ') ';
    wp_die(); // all ajax handlers should die when finished
}
add_action('wp_ajax_my_tag_count', 'my_ajax_handler');


// 自定义菜单，最简单案例
add_action('admin_menu', function() {
    add_menu_page(
        'My Plugin Page',
        'My Simplest Top Menu',
        'manage_options',
        'my-plugin-slug',
        'showName_page'
    );
});

function showName_page() {
    echo "<div class='wrap'>"; // 添加WordPress后台包裹类，确保样式正确
    echo "Menu<br/>";
    echo date('Y-m-d H:i:s');
    echo "plugin demo";
    echo "</div>";
}














?>