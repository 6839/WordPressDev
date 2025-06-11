<?php
/*
Plugin Name: plugin-jz06
Version: 1.0
Description: jz06
Author: Jamie Zhi
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: plugin
*/

// set timezone
date_default_timezone_set('Australia/Melbourne');

class jamie_change_font_style
{
    var $option_group = "jamie_jz06_group";
    var $setting_section = "jamie_jz06_setting_section";

    //构造方法，创建类的时候调用
    public function __construct()
    {
        // 钩子1--创建插件菜单并渲染内容
        add_action('admin_menu', array($this, 'jamie_create_menu'));

        // 钩子2--注册相关内容
        add_action('admin_init', array($this, 'register_jamie_jz06_setting'));

        // 调用插件
        add_action('wp_head', array($this, 'jamie_jz06_head_fun'));

        // 只在当前插件页面加载
        add_action('current_screen', array($this, 'load_screen'));

        // 编写Ajax处理方法
        // wp_ajax_  是固定写法，后面的action值和js中调用的action值相同，因为用户在界面操作会触发post请求，这个请求给到服务器会触发一个含请求中action值的钩子
        add_action('wp_ajax_jamie_color_check', array($this, 'color_check_action_fun'));

        // 新增ajax实现：用户【不论是否登录】点击首页描述后会增加一段说明文字
        add_action('wp_ajax_nopriv_jamie_description', array($this, 'jamie_description_fun'));
        add_action('wp_ajax_jamie_description', array($this, 'jamie_description_fun'));

        // 本地化处理
        add_action('init', array($this, 'jamie_load_textdomain'));

        //添加一个 jzcode 短标签，调用 jzcode_shortcode 方法进行处理
        add_shortcode('jzcode', array($this, 'jz_shortcode')); 
        add_shortcode('jzcodeclose', array($this, 'jz_shortcode_close'));
    }

    function jz_shortcode_close($atts, $content = ""){
        $output = "
            <h3><strong><em>这是闭合简码内容：{$content}</em></strong></h3>
        ";
        return $output;
    }

    function jz_shortcode($atts)
    {
        // 添加默认属性值，当然用户可以编写的时候重新赋值
        $atts = shortcode_atts(array(
            'title' => '测试简码',
            'url' => 'http://www.baidu.com',
            'img' => plugin_dir_url(__FILE__).'images/shortcode_demo.jpg'
        ), $atts, 'jzcode_shortcode');

        $output = "<a href='{$atts['url']}' title='{$atts['title']}'>
                <div class='file-box'>
                    <b>【{$atts['title']}】</b>
                    <div class='clr'></div>
                    <img src='{$atts['img']}' />
                    <div class='clr'></div>
                    <i>谢谢大家支持！</i>
                    <div class='clr'></div>
                </div>
            </a>";

        return $output;
    }

    function load_screen()
    {
        // 只在当前插件页面加载
        $screen = get_current_screen();
        // var_dump($screen);exit;
        if (is_object($screen) && $screen->id == 'toplevel_page_jamie_jz06') {
            // Ajax--前后端引用自己的js文件
            add_action('wp_enqueue_scripts', array($this, 'jamie_enqueue'));
            add_action('admin_enqueue_scripts', array($this, 'jamie_enqueue'));
        }
    }

    function jamie_load_textdomain()
    {
        //加载 languages 目录下的翻译文件
        $currentLocale = get_locale();
        // echo $currentLocale;exit; // 当前是zh_CN中文
        if (!empty($currentLocale)) {
            $moFile = dirname(__FILE__) . "/languages/{$currentLocale}.mo";
            if (@file_exists($moFile) && is_readable($moFile)) load_textdomain('jamie-translate-tag', $moFile);
        }
    }

    function jamie_enqueue($hook)
    {
        wp_enqueue_script('jamie_jz', plugins_url('/js/jamie_jz.js', __FILE__), array('jquery'));
        wp_localize_script('jamie_jz', 'jamie_ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('jamie_ajax_nonce') // 添加 nonce
        ));
    }

    function color_check_action_fun()
    {
        check_ajax_referer('jamie_ajax_nonce', 'nonce');
        if (trim($_POST['color']) != "") {
            echo "ok";
        } else {
            echo "error";
        }
        wp_die();
    }

    function jamie_description_fun()
    {
        echo "Jamie's 笔记本：" . $_POST['description'];
        wp_die();
    }

    function jamie_create_menu()
    {

        //创建顶级菜单
        add_menu_page(
            'jamie plugin home',
            'Jamie Plugin By Class&Ajax',
            'read',
            'jamie_jz06',
            array($this, 'jamie_settings_page')
        );
    }


    function jamie_settings_page()
    {
?>
        <div class="wrap">
            <h2>Top Menu</h2>
            <form action="options.php" method="post">
                <?php
                //输出一些隐藏的、必要的字段，包括验证信息等
                // 示例：
                // <input type="hidden" name="option_page" value="jamie_jz06_group" />
                // <input type="hidden" name="action" value="update" />
                // <input type="hidden" id="_wpnonce" name="_wpnonce" value="1234567890" />
                // <input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=jamie_jz06">
                settings_fields($this->option_group);

                //输出选项设置区域，渲染通过 add_settings_section() 和 add_settings_field() 注册的所有设置字段和部分
                do_settings_sections($this->option_group);

                //输出按钮
                submit_button();
                ?>
            </form>
        </div>
    <?php
    }

    //使用register_setting()注册要存储的字段
    function register_jamie_jz06_setting()
    {
        //注册一个选项，用于装载所有插件设置项，注册设置时添加默认值
        register_setting(
            $this->option_group,
            'jamie_jz06_option',
            array(
                'default' => array(
                    'size'  => '12',   // 默认字体大小
                    'color' => '#FF0000',    // 默认字体颜色（空字符串）
                    'bold'  => 0      // 默认不加粗（0为未选中，1为选中）
                )
            )
        );

        //添加选项设置区域

        add_settings_section(
            $this->setting_section, //设置区域ID
            __('setting', 'jamie-translate-tag'), //标题，区域显示名称
            array($this, 'jamie_jz06_setting_section_function'), //回调函数
            $this->option_group //属于哪个分组
        );

        if (current_user_can("edit_posts")) {
            // 设置颜色字段
            add_settings_field(
                'jamie_jz06_color', //设置字段ID
                __('font-color', 'jamie-translate-tag'), //设置标题
                array($this, 'jamie_jz06_color_function'), //回调函数
                $this->option_group, //属于哪个分组
                $this->setting_section  //属于哪个区域
            );
        }

        // 设置字体大字段
        add_settings_field(
            'jamie_jz06_size',
            __('font-size', 'jamie-translate-tag'),
            array($this, 'jamie_jz06_size_function'),
            $this->option_group,
            $this->setting_section
        );

        // 设置字体加粗字段
        add_settings_field(
            'jamie_jz06_bold',
            __('font-bold', 'jamie-translate-tag'),
            array($this, 'jamie_jz06_bold_function'),
            $this->option_group,
            $this->setting_section
        );
    }




    //输出选项设置区域
    function jamie_jz06_setting_section_function()
    {
    ?>
        <p>Setting font style</p>
        
    <?php
        // echo do_shortcode("[jzcode]");
    }

    function jamie_jz06_size_function()
    {
        $jamie_jz06_option = get_option('jamie_jz06_option');
        $size = $jamie_jz06_option['size'];
    ?>
        <select name="jamie_jz06_option[size]">
            <option value="12" <? selected('12', $size); ?>>12</option>
            <option value="14" <? selected('14', $size); ?>>14</option>
            <option value="16" <? selected('16', $size); ?>>16</option>
            <option value="18" <? selected('18', $size); ?>>18</option>
            <option value="20" <? selected('20', $size); ?>>20</option>
        </select>
    <?php
    }

    function jamie_jz06_color_function()
    {
        $jamie_jz06_option = get_option('jamie_jz06_option');
    ?>
        <input name="jamie_jz06_option[color]" class="jamie_jz06_class" type="text" value="<? echo $jamie_jz06_option['color']; ?>" />
        <span id="error_color"></span>

    <?php
    }

    function jamie_jz06_bold_function()
    {
        $options = get_option('jamie_jz06_option');
        $options = wp_parse_args($options, array('bold' => 0)); // 确保 bold 键存在
    ?>
        <input
            name="jamie_jz06_option[bold]"
            type="checkbox"
            value="1"
            <?php checked(1, $options['bold']); ?> /> Bold
    <?php
    }

    function jamie_jz06_head_fun()
    {
        $jamie_jz06_option = get_option("jamie_jz06_option");
        $bold = $jamie_jz06_option["bold"] == 1 ? "bold" : "normal";
    ?>
        <style>
            body {
                color: <? echo $jamie_jz06_option["color"] ?>;
                font-size: <? echo $jamie_jz06_option["size"] ?>px;
                font-weight: <? echo $bold; ?>;
            }
        </style>
<?php
    }
}

new jamie_change_font_style();
