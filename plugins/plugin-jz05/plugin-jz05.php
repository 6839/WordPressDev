<?php
/*
Plugin Name: plugin-jz05
Version: 1.0
Description: jz05
Author: Jamie Zhi
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: plugin
*/

// set timezone
date_default_timezone_set('Australia/Melbourne');

class jamie_change_font_style
{
    var $option_group = "general";

    //构造方法，创建类的时候调用
    public function __construct()
    {

        // 钩子2--注册相关内容
        add_action('admin_init', array($this, 'register_jamie_jz05_setting'));

        // 调用插件
        add_action('wp_head', array($this, 'jamie_jz05_head_fun'));
    }

   

    //使用register_setting()注册要存储的字段
    function register_jamie_jz05_setting()
    {
        //注册一个选项，用于装载所有插件设置项，注册设置时添加默认值
        register_setting(
            $this->option_group,
            'jamie_jz05_option',
            array(
                'default' => array(
                    'size'  => '12',   // 默认字体大小
                    'color' => '#FF0000',    // 默认字体颜色（空字符串）
                    'bold'  => 0      // 默认不加粗（0为未选中，1为选中）
                )
            )
        );


        // 设置颜色字段
        add_settings_field(
            'jamie_jz05_color', //设置字段ID
            ' 字体颜色 ', //设置标题
            array($this, 'jamie_jz05_color_function'), //回调函数
            $this->option_group, //属于哪个分组
        );

        // 设置字体大字段
        add_settings_field(
            'jamie_jz05_size',
            ' 字体大小 ',
            array($this, 'jamie_jz05_size_function'),
            $this->option_group,
        );

        // 设置字体加粗字段
        add_settings_field(
            'jamie_jz05_bold',
            ' 字体加粗 ',
            array($this, 'jamie_jz05_bold_function'),
            $this->option_group,
        );
    }




    //输出选项设置区域
    function jamie_jz05_setting_section_function()
    {
    ?>
        <p>设置字体样式</p>
    <?php
    }

    function jamie_jz05_size_function()
    {
        $jamie_jz05_option = get_option('jamie_jz05_option');
        $size = $jamie_jz05_option['size'];
    ?>
        <select name="jamie_jz05_option[size]">
            <option value="12" <? selected('12', $size); ?>>12</option>
            <option value="14" <? selected('14', $size); ?>>14</option>
            <option value="16" <? selected('16', $size); ?>>16</option>
            <option value="18" <? selected('18', $size); ?>>18</option>
            <option value="20" <? selected('20', $size); ?>>20</option>
        </select>
    <?php
    }

    function jamie_jz05_color_function()
    {
        $jamie_jz05_option = get_option('jamie_jz05_option');
    ?>
        <input name="jamie_jz05_option[color]" type="text" value="<? echo $jamie_jz05_option['color']; ?>" />
    <?php
    }

    function jamie_jz05_bold_function()
    {
        $options = get_option('jamie_jz05_option');
        $options = wp_parse_args($options, array('bold' => 0)); // 确保 bold 键存在
        ?>
        <input
            name="jamie_jz05_option[bold]"
            type="checkbox"
            value="1"
            <?php checked(1, $options['bold']); ?> /> 加粗
         <?php
    }

    function jamie_jz05_head_fun()
    {
        $jamie_jz05_option = get_option("jamie_jz05_option");
        $bold = $jamie_jz05_option["bold"] == 1 ? "bold" : "normal";
    ?>
        <style>
            body {
                color: <? echo $jamie_jz05_option["color"] ?>;
                font-size: <? echo $jamie_jz05_option["size"] ?>px;
                font-weight: <? echo $bold; ?>;
            }
        </style>
<?php
    }
}

new jamie_change_font_style();
