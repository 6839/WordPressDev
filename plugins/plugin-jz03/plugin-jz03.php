<?php
/*
Plugin Name: plugin-jz03
Version: 1.0
Description: jz03
Author: Jamie Zhi
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: plugin
*/

// set timezone
date_default_timezone_set('Australia/Melbourne');


// 钩子1--创建插件菜单并渲染内容
add_action('admin_menu', 'jamie_create_menu');

function jamie_create_menu()
{
    //创建顶级菜单
    add_menu_page(
        'jamie plugin home',
        'Jamie的插件fun实现',
        'manage_options',
        'jamie_jz03',
        'jamie_settings_page'
    );
}

function jamie_settings_page()
{
?>
    <div class="wrap">
        <h2>插件顶级菜单</h2>
        <form action="options.php" method="post">
            <?php
            $option_group = "jamie_jz03_group";

            //输出一些隐藏的、必要的字段，包括验证信息等
            // 示例：
            // <input type="hidden" name="option_page" value="jamie_jz03_group" />
            // <input type="hidden" name="action" value="update" />
            // <input type="hidden" id="_wpnonce" name="_wpnonce" value="1234567890" />
            // <input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=jamie_jz03">
            settings_fields($option_group);

            //输出选项设置区域，渲染通过 add_settings_section() 和 add_settings_field() 注册的所有设置字段和部分
            do_settings_sections($option_group);

            //输出按钮
            submit_button();
            ?>
        </form>
    </div>
<?php
}


// 钩子2--注册相关内容
add_action('admin_init', 'register_jamie_jz03_setting');

//使用register_setting()注册要存储的字段
function register_jamie_jz03_setting()
{

    //注册一个选项，用于装载所有插件设置项，注册设置时添加默认值
    $option_group = "jamie_jz03_group";
    register_setting(
        $option_group,
        'jamie_jz03_option',
        array(
            'default' => array(
                'size'  => '12',   // 默认字体大小
                'color' => '#FF0000',    // 默认字体颜色（空字符串）
                'bold'  => 0      // 默认不加粗（0为未选中，1为选中）
            )
        )
    );

    //添加选项设置区域
    $setting_section = "jamie_jz03_setting_section";
    add_settings_section(
        $setting_section, //设置区域ID
        "设置", //标题，区域显示名称
        "jamie_jz03_setting_section_function", //回调函数
        $option_group //属于哪个分组
    );

    // 设置颜色字段
    add_settings_field(
        'jamie_jz03_color', //设置字段ID
        ' 字体颜色 ', //设置标题
        'jamie_jz03_color_function', //回调函数
        $option_group, //属于哪个分组
        $setting_section  //属于哪个区域
    );

    // 设置字体大字段
    add_settings_field(
        'jamie_jz03_size',
        ' 字体大小 ',
        'jamie_jz03_size_function',
        $option_group,
        $setting_section
    );

    // 设置字体加粗字段
    add_settings_field(
        'jamie_jz03_bold',
        ' 字体加粗 ',
        'jamie_jz03_bold_function',
        $option_group,
        $setting_section
    );
}

//输出选项设置区域
function jamie_jz03_setting_section_function()
{
?>
    <p>设置字体样式</p>
<?php
}

function jamie_jz03_size_function()
{
    $jamie_jz03_option = get_option('jamie_jz03_option');
    $size = $jamie_jz03_option['size'];
?>
    <select name="jamie_jz03_option[size]">
        <option value="12" <? selected('12', $size); ?>>12</option>
        <option value="14" <? selected('14', $size); ?>>14</option>
        <option value="16" <? selected('16', $size); ?>>16</option>
        <option value="18" <? selected('18', $size); ?>>18</option>
        <option value="20" <? selected('20', $size); ?>>20</option>
    </select>
<?php
}

function jamie_jz03_color_function()
{
    $jamie_jz03_option = get_option('jamie_jz03_option');
?>
    <input name="jamie_jz03_option[color]" type="text" value="<? echo $jamie_jz03_option['color']; ?>" />
<?php
}

function jamie_jz03_bold_function()
{
    $options = get_option('jamie_jz03_option');
    $options = wp_parse_args($options, array('bold' => 0)); // 确保 bold 键存在
    ?>
    <input
        name="jamie_jz03_option[bold]"
        type="checkbox"
        value="1"
        <?php checked(1, $options['bold']); ?> /> 加粗
    <?php
}


// 调用插件
add_action('wp_head', 'jamie_jz03_head_fun');

function jamie_jz03_head_fun()
{
    $jamie_jz03_option = get_option("jamie_jz03_option");
    $bold = $jamie_jz03_option["bold"] == 1 ? "bold" : "normal";
?>
    <style>
        body {
            color: <? echo $jamie_jz03_option["color"] ?>;
            font-size: <? echo $jamie_jz03_option["size"] ?>px;
            font-weight: <? echo $bold; ?>;
        }
    </style>
<?php
}
