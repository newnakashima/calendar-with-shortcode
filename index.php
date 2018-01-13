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
