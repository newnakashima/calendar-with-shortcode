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
    ];
    register_post_type('event', [
        'label' => 'イベント',
        'public' => true,
        'has_archive' => true,
        'menu_position' => 5,
        'supports' => $exampleSupports
    ]);
}
add_action('init', 'create_post_type');

// カスタムフィールド
function add_book_fields() {
    add_meta_box('book_setting', '本の情報', 'insert_book_fields', 'event', 'normal');
}
add_action('admin_menu', 'add_book_fields');

function insert_book_fields() {
    global $post;

    echo '題名： <input type="text" name="book_name" value="'.get_post_meta($post->ID, 'book_name', true).'" size="50" /><br>';
	echo '作者： <input type="text" name="book_author" value="'.get_post_meta($post->ID, 'book_author', true).'" size="50" /><br>';
	echo '価格： <input type="text" name="book_price" value="'.get_post_meta($post->ID, 'book_price', true).'" size="50" />　<br>';

    $book_label_check = '';
    if (get_post_meta($post->ID, 'book_label', true) == 'is_on') {
        $book_label_check = 'checked';
    }
    echo 'ベストセラーラベル: <input type="checkbox" name="book_label" value="is_on"' . $book_label_check. '><br>';
}

// カスタムフィールドの値を保存
function save_book_fields( $post_id ) {
    if(!empty($_POST['book_name'])){ //題名が入力されている場合
        update_post_meta($post_id, 'book_name', $_POST['book_name'] ); //値を保存
    }else{ //題名未入力の場合
        delete_post_meta($post_id, 'book_name'); //値を削除
    }

    if(!empty($_POST['book_author'])){
        update_post_meta($post_id, 'book_author', $_POST['book_author'] );
    }else{
        delete_post_meta($post_id, 'book_author');
    }

    if(!empty($_POST['book_price'])){
        update_post_meta($post_id, 'book_price', $_POST['book_price'] );
    }else{
        delete_post_meta($post_id, 'book_price');
    }

    if(!empty($_POST['book_label'])){
        update_post_meta($post_id, 'book_label', $_POST['book_label'] );
    }else{
        delete_post_meta($post_id, 'book_label');
    }
}
add_action('save_post', 'save_book_fields');

