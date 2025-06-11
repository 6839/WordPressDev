<?php
/*
Plugin Name: plugin-jz02
Version: 1.0
Description: jz02
Author: Jamie Zhi
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: plugin
*/

// set timezone
date_default_timezone_set('Australia/Melbourne');


//定义插件启动时候调用的方法
register_activation_hook(__FILE__, 'jamie_jz02_install');


// 在插件启动时创建自己的数据表
function jamie_jz02_install()
{
    global $wpdb;
    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}jz02'") != "{$wpdb->prefix}jz02") {
        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}jz02` (
        `id` int(11) NOT NULL auto_increment,
        `color` varchar(10) DEFAULT '',
        `size` varchar(10) DEFAULT '',
        PRIMARY KEY  (`id`)
    ) DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;";
        $wpdb->query($sql);

        $sql = "REPLACE INTO `{$wpdb->prefix}jz02` VALUES (1, '#FF0000', '20');";
        $wpdb->query($sql);
    }
}

// 创建插件菜单
add_action('admin_menu', 'jamie_create_menu02');
function jamie_create_menu02()
{
    // 创建顶级菜单
    add_menu_page(
        'Jamie Plugin Page02',
        'Jamie Top Plugin02',
        'manage_options',
        'jamie_copyright02',
        'jamie_settings_page02',
    );
}

// 设置插件菜单界面
function jamie_settings_page02()
{
    global $wpdb;

    //当提交了，并且验证信息正确
    if (!empty($_POST) && check_admin_referer('jamie_jz02_nonce')) {

        //更新加粗设置到option表
        if (isset($_POST['jamie_jz02_bold'])) {
            update_option('jamie_jz02_bold', $_POST['jamie_jz02_bold']);
        } else {
            update_option('jamie_jz02_bold', 0); 
        }

        //更新颜色、字号设置到自定义表
        $wpdb->update("{$wpdb->prefix}jz02", array('color' => $_POST['color'], 'size' => $_POST['size']), array('id' => 1));
?>
        <div id="message" class="updated">
            <p><strong>保存成功！</strong></p>
        </div>
    <?php
    }

    $sql = "SELECT * FROM `{$wpdb->prefix}jz02`";
    $row = $wpdb->get_row($sql, ARRAY_A);

    $color = $row['color'];
    $size = $row['size'];
    ?>

    <div class="wrap">
        <h2>插件顶级菜单</h2>
        <!-- 这个action表示提交给当前页面处理 -->
        <form action="" method="post">
            <p><label for="color">字体颜色：</label><input type="text" name="color" value="<?php echo $color; ?>" /></p>
            <p><label for="size">字体大小：</label>
                <select name="size">
                    <option value="12" <?php selected('12', $size); ?>>12</option>
                    <option value="14" <?php selected('14', $size); ?>>14</option>
                    <option value="16" <?php selected('16', $size); ?>>16</option>
                    <option value="18" <?php selected('18', $size); ?>>18</option>
                    <option value="20" <?php selected('20', $size); ?>>20</option>
                </select>
            </p>
            <p>
                <label for="jamie_jz02_obold">字体加粗：</label>
                <input name="jamie_jz02_bold" type="checkbox" value="1" <?php checked(1, get_option('jamie_jz02_bold')); ?> /> 加粗
            </p>
            <p><input type="submit" name="submit" value="保存设置" /></p>
            <?php
            //输出一个验证信息
            wp_nonce_field('jamie_jz02_nonce');
            ?>
        </form>
    </div>

<?php
}

// 调用插件
add_action('wp_head', 'jamie_jz02_head_fun');

function jamie_jz02_head_fun()
{
    global $wpdb;

    //获取自定义数据库中的设置
    $sql = "SELECT * FROM `{$wpdb->prefix}jz02`";
    $row = $wpdb->get_row($sql, ARRAY_A);

    //获取options表中的设置选项
    $bold = get_option("jamie_jz02_bold") == 1 ? "bold" : "normal";

    ?>
        <style>
            body {
                color: <?php echo $row["color"] ?>;
                font-size: <?php echo $row["size"] ?>px;
                font-weight: <?php echo $bold; ?>;
            }
        </style>
    <?php
}
