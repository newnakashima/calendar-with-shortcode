<?php
/**
 * @package My_Plugin
 * @version 1.0
 */
/*
Plugin Name: My Plugin
Plugin URI: http://example.com
Description: This is my first plugin.
Version: 1.0
Author: Tsuyoshi Nakashima
Author URI: http://example.com
License: GPL2
*/


/** 上のテキストのステップ2 */
add_action( 'admin_menu', 'my_plugin_menu' );
add_action( 'admin_menu', 'my_submenu' );

// require_once(__DIR__ . '/My_Widget.php');
// add_action( 'widgets_init', function() {
//     register_widget( 'My_Widget' );
// });

/** ステップ1 */
function my_plugin_menu() {
	add_menu_page( 'My Plugin Options', 'My Plugin', 'manage_options', 'my-plugin-identifier', 'my_plugin_options', '', 6 );
}

function my_submenu() {
    add_submenu_page( 'my-plugin-identifier', 'My Plugins Sub Menu', 'My Sub Menu', 'manage_options', 'my-plugin-sub-menu', 'my_plugin_submenu');
}

/** ステップ3 */
function my_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<p>オプション用のフォームをここに表示する。</p>';
	echo '</div>';
}

function my_plugin_submenu() {
    echo '<h2>this is my submenu.</h2>';
}

// [calendar]
function calendar_function( $atts ) {
    ob_start();
    require(__DIR__ . '/template/calendar.php');
    $buffer = ob_get_contents();
    ob_end_clean();
    return $buffer;
}
add_shortcode( 'calendar', 'calendar_function' );

// css
add_action('wp_enqueue_scripts', 'plugin_enqueue_styles');
function plugin_enqueue_styles() {
    wp_enqueue_style('calendar_style', plugins_url('', __FILE__) . '/css/style.css');
}

// カスタム投稿タイプ
function create_post_type() {
    $exampleSupports = [
        'title',
        'editor',
        'thumbnail',
        'revisions',
        'excerpt',
    ];
    register_post_type('event', [
        'label' => 'イベント',
        'public' => true,
        'has_archive' => true,
        'menu_position' => 5,
        'supports' => $exampleSupports,
        'show_in_rest' => true,
        'rest_base' => 'events',
    ]);
    register_taxonomy('event_type', 'event', ['label' => 'イベントタイプ', 'hierarchical' => true]);
}
add_action('init', 'create_post_type');

// jquery
function plugin_enqueue_jqueryUi() {
    wp_enqueue_style('jquery-ui.min.css', plugins_url('', __FILE__) . '/lib/jquery-ui/jquery-ui.min.css');
    // wp_enqueue_script('jquery-3.2.1.min.js', plugins_url('', __FILE__) . '/lib/jquery/jquery-3.2.1.min.js');
    // wp_enqueue_script('jquery-ui.min.js', plugins_url('', __FILE__) . '/lib/jquery-ui/jquery-ui.min.js');
}
add_action('admin_head', 'plugin_enqueue_jqueryUi');

// カスタムフィールド
function add_event_fields() {
    add_meta_box('event_setting', 'イベントの情報', 'insert_event_fields', 'event', 'normal');
}
add_action('admin_menu', 'add_event_fields');

add_action('rest_api_init', function () {
    register_rest_field(
        'event',
        'event_meta',
        [
            'get_callback' => function ($object, $field_name, $request) {
                $meta_fields = [
                    'cws_event_date',
                ];
                $meta = [];
                foreach ($meta_fields as $field) {
                    $meta[$field] = get_post_meta($object['id'], $field, true);
                }
                return $meta;
            },
            'update_callback' => null,
            'schema'          => null,
        ]
    );
});

function insert_event_fields() {

    echo '<script src="' . plugins_url('', __FILE__) . '/lib/jquery/jquery-3.2.1.min.js"></script>';
    echo '<script src="' . plugins_url('', __FILE__) . '/lib/jquery-ui/jquery-ui.min.js"></script>';

    global $post;

    echo '<table>' .
        '<tr><td><label for="cws_event_date">日付</label></td><td><input type="text" id="cws_event_date" name="cws_event_date" value="' . get_post_meta($post->ID, 'cws_event_date', true) . '" /></td></tr>' .
        '</table>';

    echo <<<__JS__
        <script>
            $('#cws_event_date').datepicker({
                dateFormat: "yy-mm-dd"
            });
        </script>
__JS__;
}

// カスタムフィールドの値を保存
function save_event_fields( $post_id ) {
    if(!empty($_POST['cws_event_date'])){ //日付が入力されている場合
        update_post_meta($post_id, 'cws_event_date', $_POST['cws_event_date'] ); //値を保存
    } else { //日付未入力の場合
        delete_post_meta($post_id, 'cws_event_date'); //値を削除
    }
}
add_action('save_post', 'save_event_fields');

