<?php
/*
Plugin Name: jz-image-style
Version: 1.0
Description: Display image in hover carousel.
Author: Jamie Zhi
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: plugin
*/

// 在用到简码的文章或页面中引入css
function jz_enqueue_scripts()
{
    global $post;
    $has_shortcode = has_shortcode($post->post_content, 'jz_image_style_1') || has_shortcode($post->post_content,'box');

    if ((is_a($post, 'WP_Post') || is_a($page, 'WP_Page')) && has_shortcode($post->post_content, 'jz_image_style_1')) {
        wp_register_style('jz-stylesheet',  plugin_dir_url(__FILE__) . 'css/style.css');
        wp_enqueue_style('jz-stylesheet');
    }
}
add_action('wp_enqueue_scripts', 'jz_enqueue_scripts');

// 创建简码
add_shortcode('jz_image_style_1', 'style_1');
function style_1()
{
    $output = '<div class="container-jz">';

    for ($i = 1; $i <= 5; $i++) {

        $id = get_theme_mod('video_poster' . $i);
        $output .= '<div alt="missing content" class="box-jz"><img class="w-100 img-fluid" 
        src="' . wp_get_attachment_url($id) . '"><span>' . get_theme_mod('video_header' . $i) . '</span></div>';
    }
    return $output;
}

add_action('customize_register', 'video_frontpage_theme_customizer');
function video_frontpage_theme_customizer($wp_customize)
{
    // Video Section
    // 创建一个名为"Video Section"的面板，用于组织所有视频相关设置
    $wp_customize->add_panel('videosection_panel', array(
        'priority'        => 200,  // 面板在自定义器中的显示顺序（数字越大越靠后）
        'theme_supports'  => '',   // 主题支持参数（此处为空）
        'title'           => __('1. Video Section (Front Page)', 'framework'),  // 面板标题
        'description'     => __('Set editable text for certain content.', 'framework'),  // 面板描述
    ));

    // 使用循环创建5个视频设置区域，每个区域包含视频上传、封面图和标题设置
    for ($i = 1; $i <= 5; $i++) {
        // 添加视频设置区域（Section）,5个区域
        $wp_customize->add_section('video_panel' . $i, array(
            'title'    => __('Video ' . $i, 'framework'),  // 区域标题（如"Video 1"）
            'panel'    => 'videosection_panel',  // 将此区域放入前面创建的面板中
            'priority' => 130  // 区域在面板中的显示顺序
        ));

        // 每个区域界面中，第一个按钮--select video
        // 添加视频上传设置
        $wp_customize->add_setting(
            'video_upload_' . $i,  // 设置ID（如"video_upload_1"）
            array(
                'default'           => '',  // 默认值（空）
                'transport'         => 'refresh',  // 设置更改后刷新页面
                'sanitize_callback' => 'absint',  // 验证回调：确保值为正整数（媒体ID）
                'type'              => 'theme_mod',  // 数据存储类型
            )
            );
        // 添加视频上传控件（媒体选择器）
        $wp_customize->add_control(new WP_Customize_Media_Control(
            $wp_customize,
            'video_upload_' . $i,  // 关联前面的设置ID
            array(
                'label'         => __('Default Media Control'),   // 控件名字
                'description'   => esc_html__('Video on front page'), // 控件描述
                'section'       => 'video_panel' . $i, // 所属区域
                'settings'      => 'video_upload_' . $i,  // 关联设置
                'priority'      => 1,  // 控件显示顺序
                'mime_type'     => 'video', // Required. Can be image, audio, video, application, text  // 媒体类型限制为视频
                'button_labels' => array( // Optional   // 自定义按钮名字
                    'select'       => __('Select Video'),
                    'change'       => __('Change Video'),
                    'default'      => __('Default'),
                    'remove'       => __('Remove Video'),
                    'placeholder'  => __('No video selected'),
                    'frame_title'  => __('Select File'),
                    'frame_button' => __('Choose File'),
                )
            )
        ));

        // 每个区域界面中，第二个按钮--选择图片
        //add poster image
        // 添加视频封面图设置
        $wp_customize->add_setting('video_poster' . $i, array(
            'default'   => plugin_dir_url(__FILE__) . '/images/default.jpg',  // 默认封面图路径
            'transport' => 'refresh',  // 设置更改后刷新页面
            'type'      => 'theme_mod',  // 设置类型：主题选项
        ));
         // 添加封面图控件（裁剪图像选择器）
        $wp_customize->add_control(new WP_Customize_Cropped_Image_Control($wp_customize, 'video_poster' . $i, array(
            'label'       => __('Poster image for Video', 'framework'),  // 控件名字
            'height'      => 48,  // 推荐裁剪高度
            'width'       => 70,  // 推荐裁剪宽度
            'flex_width'  => true,  // 允许宽度灵活调整
            'flex_height' => true,  // 允许高度灵活调整
            'section'     => 'video_panel' . $i,  // 所属区域
            'settings'    => 'video_poster' . $i, // 关联设置
            'priority'    => 1  // 控件显示顺序
        )));


        // 每个区域界面中，第二个输入框--设置header
        // Add header setting
        // 添加视频标题设置
        $wp_customize->add_setting('video_header' . $i, array(
            'default' => __('header', 'framework'),  // 默认标题文本
            'type'    => 'theme_mod'  // 设置类型：主题选项
        ));
        // Add control
        // 添加标题控件（文本输入框）
        $wp_customize->add_control(new WP_Customize_Control(
            $wp_customize,
            'video_header' . $i,  // 关联前面的设置ID
            array(
                'label'    => __('Header', 'framework'),  // 控件名字
                'section'  => 'video_panel' . $i,  // 所属区域
                'settings' => 'video_header' . $i,  // 关联设置
                'priority' => 1,  // 控件显示顺序
                'type'     => 'text'  // 控件类型：文本输入框
            )
        ));
    }
}


add_shortcode('current_year', 'jz_year');
function jz_year()
{
    return "<div class='ml-1'>" . getdate()['year'] . "</div>";
}

add_action('init', 'jz_year');
