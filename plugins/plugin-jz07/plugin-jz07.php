<?php
/*
Plugin Name: plugin-jz07
Version: 1.0
Description: jz07
Author: Jamie Zhi
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: plugin
*/

// set timezone
date_default_timezone_set('Australia/Melbourne');

class jamie_change_font_style07
{
    public function __construct()
    {
        // 创建自定义文章类型--作品
        add_action('init', array($this, 'create_my_book'));

        // 在作品编辑页面添加元数据框
        add_action('add_meta_boxes', array($this, 'add_meta_boxes_my_book'));

        // 保存作品时，元数据框内内容保存到数据库
        add_action('save_post', array($this, 'save_my_book_fields'), 10, 2);
    }

    function save_my_book_fields($post_id, $post)
    {
        if ($post->post_type == 'my_book') {
            if (isset($_POST['my_book_url'])) {
                $my_book_url = $_POST['my_book_url'];
                update_post_meta($post_id, 'my_book_url', $my_book_url);
            }
        }
    }

    function add_meta_boxes_my_book()
    {
        add_meta_box(
            'my_book_admin_meta_box', //ID
            'Book link', //标题
            array($this, 'display_my_book_meta_box'), //显示HTML代码的回调函数
            'my_book', //这里设置，在哪个界面中展示该元数据框，这里表示只在作品编辑页面显示该元数据框
            'side'
        );
    }

    function display_my_book_meta_box($post)
    {
        $my_book_url = get_post_meta($post->ID, 'my_book_url', true);
        echo '<input type="text" id="my_book_url" name="my_book_url" value="' . $my_book_url . '" />';
    }

    function create_my_book()
    {
        register_post_type(
            'my_book',
            array(
                'labels' => array(
                    'name' => 'My Book',
                    'add_new' => 'Add Book',
                    'add_new_item' => 'Add A New Book',
                    'edit' => 'edit',
                    'edit_item' => 'edit book',
                    'new_item' => 'new book',
                    'view' => 'view',
                    'view_item' => 'view book',
                    'search_items' => 'search book',
                    'not_found' => 'no book found',
                    'not_found_in_trash' => 'not book found in trash'
                ),
                'public' => true, //可见性
                'menu_position' => 15, //菜单的位置
                'supports' => array('title', 'editor', 'comments', 'thumbnail', 'custom-fields'), //显示哪些自定义文章类型的功能
                'taxonomies' => array('') //自定义分类。在这里没有定义
            )
        );
    }
}


new jamie_change_font_style07();
