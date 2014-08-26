<?php
/*
 *	Plugin Name: Minecraft Query - Server stats
 *	Plugin URI: magicraft.creepy.cz/plugin
 *	Description: Provides widgets to show if your server is online and how many players are there.
 *	Version: 1.0
 *	Author: xPaw, Zdenek Studeny
 *	Author URI: https://github.com/xPaw/PHP-Minecraft-Query/blob/master/README.md
 *	License: Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 *  To view a copy of this license, visit http://creativecommons.org/licenses/by-nc-sa/3.0/
 *
*/

/*
 *	Assign global variables
 *
*/

$plugin_url = WP_PLUGIN_URL . '/minecraft-query';
$options = array();
$display_json = false;


function mcq_menu() {

	/*
	 * Use the add_option_page function
	 * <?php add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position ); ?>
	 *
	*/
	add_menu_page(
		'Minecraft Server Settings',
		'Server Settings',
		'manage_options',
		'mcq-menu',
		'mcq_options_page'
	);

}

function mcq_register_script_menu_page(){
	wp_enqueue_script( 'options-page', get_template_directory_uri() . '/library/js/options-page.js', array('jquery'), '', true );
}
add_action( 'admin_enqueue_scripts' , 'mcq_register_script_menu_page');

function mcq_register_css_menu_page(){
	wp_enqueue_style( 'options-page-css', get_template_directory_uri() . '/library/css/options-page-wrapper.css' );
}
add_action( 'admin_head' , 'mcq_register_css_menu_page');


// Get all stats of server
function mcq_get_server_stats( $adress ){

	require_once('library/minecraft-server-status-master/MinecraftServerStatus.class.php');

	$Server = new MinecraftServerStatus( $adress );
	$server_stats = $Server->Get();

	return $server_stats;
}

function mcq_refresh_server() {

	$options = array();
	$options = get_option('mcq_server_stats');
	$last_updated = $options['last_updated'];

	$update_difference = time() - $last_updated;

	if( $update_difference > 60 ) {

		$server_info = $options['server_info'];

		$server_list = array();
		foreach ($server_info as $server) {

			$server_list[] = mcq_get_server_stats( $server['server_ip']);
		}

		$options['server_list'] = $server_list;
		$options['last_updated'] = time();

		update_option( 'mcq_server_stats', $options);
	}

	die();
}
add_action( 'wp_ajax_mcq_refresh_server', 'mcq_refresh_server');

function mcq_server_enable_frontend_ajax() {
?>
	<script>

		var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

	</script>
<?php

}
add_action( 'wp_head', 'mcq_server_enable_frontend_ajax' );

// Get rid of whitespaces on start and end
function mcq_trim_value(&$value) 
{ 
    $value = trim($value); 
}



add_action( 'admin_menu' , 'mcq_menu' );
// Render options page for plugin
function mcq_options_page() {

	if ( !current_user_can( 'manage_options' ) ) {
		wp_die( 'You do not have suggificient permissions to acces this page.' );
	}

	global $plugin_url;
	global $options;
	global $servers;

	$debug = true;


	if ( isset($_POST['save']) ) {

		$server_names = $_POST['server_name'];
		$server_ips = $_POST['server_ip'];
		$short_description = $_POST['short_description'];

		if ( is_array($server_names) ) {	array_walk($server_names, 'mcq_trim_value');	}else {	trim($server_names);}
		if ( is_array($server_ips) ) {	array_walk($server_ips, 'mcq_trim_value');	}else {	trim($server_ips);}
		if ( is_array($short_description) ) {	array_walk($short_description, 'mcq_trim_value');	}else {	trim($short_description);}

		// now edit them

		$count = 0;
		$server_list = array();
		$server_info = array();

		foreach ($server_names as $name) {

			// If is set ip and name for server, save name, ip and server info.
			if ( $server_ips[$count] != '' && isset( $server_ips[$count] ) ){

				// Aditionaly set name and ip of server
				$server_info[$count]['server_name'] = esc_html( $name );
				$server_info[$count]['server_ip'] = esc_html( $server_ips[$count] );
				$server_info[$count]['short_description'] = esc_html( $short_description[$count] );

				$server_list[] = mcq_get_server_stats( $server_ips[$count] );

				/*$server_list[] = array(
					'name' => esc_html( $name ),
					'server_ip' => esc_html( $server_ips[$count] )
				); */
			}

			$count++;
		}

		// Actual data from server
		$options['server_list'] = $server_list;
		//user input about server
		$options['server_info'] = $server_info;

		$options['last_updated'] = time();

		// Update options was saving it like a string when had multiple servers and made whitespace at start
		// I know... WTF?
		if ( add_option('mcq_server_stats', $options, '','yes') == false ){
			delete_option('mcq_server_stats');
			add_option('mcq_server_stats', $options, '','yes');
		}

		//update_option('mcq_server_stats', $options);	

	}

	if ( !isset($options) ) {
		$options = array();
		$options = get_option('mcq_server_stats');
	}

	$server_list = $options['server_list'];
	$server_info = $options['server_info'];


	require ( 'library/options-page-wrapper.php' );

}





/***************
Server status 
*****************/
// Single server
class Mcsolo_Server_Status extends WP_Widget {

	function mcsolo_server_status() {
		// Instantiate the parent object
		parent::__construct( false, 'Minecraft Server - single' );

	}

	function widget( $args, $instance ) {
		// Widget output

		extract( $args );
		$title = apply_filters( 'widget_title' , $instance['title'] );
		$display_title = $instance['display_title'];
		$chosen_server = $instance['server'];

		$options = get_option( 'mcq_server_stats' );

		if ( $options != ''){
			$server_list = $options['server_list'];
			$server_info = $options['server_info'];

			$server_stats = $server_list[$chosen_server];
			$server_info = $server_info[$chosen_server];

		}

		if ( !isset($server_info) ){

			echo "<p>You need to set your server info first.<p>";
		}else{

		require( 'library/widget/mcsolo-front-end.php' );
		
		}

	}

	function update( $new_instance, $old_instance ) {
		// Save widget options

		$instance = $old_instance;
		$instance['title'] = trim( strip_tags($new_instance['title']) );
		$instance['display_title'] = trim( strip_tags($new_instance['display_title']) );
		$instance['server'] = trim( strip_tags($new_instance['server']) );
		
		return $instance;
	}

	function form( $instance ) {
		// Output admin widget options form

		$title = trim( esc_attr($instance['title']) );
		$display_title = esc_attr($instance['display_title']);
		$chosen_server = $instance['server'];

		$options = get_option('mcq_server_stats');
		$server_list = $options['server_list'];
		$server_info = $options['server_info'];

		if ( !isset($server_info)){
			echo "<p>You need to set your server info first.<p>";
		}else{
		
		require( 'library/widget/mcsolo-widget-fields.php' );
		
		}

	}
}

// Multi server widget
class Mcmulti_Server_Status extends WP_Widget {

	function mcmulti_server_status() {
		// Instantiate the parent object
		parent::__construct( false, 'Minecraft Server - Multi' );

	}

	function widget( $args, $instance ) {
		// Widget output

		extract( $args );
		$title = apply_filters( 'widget_title' , $instance['title'] );
		$display_title = $instance['display_title'];

		$options = get_option( 'mcq_server_stats' );

		if ( $options != ''){
			$server_list = $options['server_list'];
			$server_info = $options['server_info'];
		}

		if ( !isset($server_info) ){

			echo "<p>You need to set your server info first.<p>";
		}else{
		
		$chosen_servers = array();
		$count = 0;
		// save each checked elemen into $chosen_servers array;
		foreach ($server_info as $server) {
			if ( $instance['server'.$count] == 1){
				$chosen_servers[] = $count;
			}
			$count++;
		}
		
		
		require( 'library/widget/mcmulti-front-end.php' );
		
		}

	}

	function update( $new_instance, $old_instance ) {
		// Save widget options

		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['display_title'] = strip_tags($new_instance['display_title']);

		$options = get_option( 'mcq_server_stats' );
		$server_info = $options['server_info'];
		$count = 0;
		foreach ($server_info as $server) {
			$instance['server'.$count] = strip_tags($new_instance['server'.$count]);
			$count++;
		}

		return $instance;
	}

	function form( $instance ) {
		// Output admin widget options form

		$options = get_option('mcq_server_stats');
		$server_list = $options['server_list'];
		$server_info = $options['server_info'];


		$title = esc_attr($instance['title']);
		$display_title = esc_attr($instance['display_title']);
		
		if ( !isset($server_info) ){

			echo "<p>You need to set your servers info first.<p>";
		}else{


		$chosen_servers = array();
		$count = 0;
		// save each checked elemen into $chosen_servers array;
		foreach ($server_info as $server) {
			if ( $instance['server'.$count] == 1){
				$chosen_servers[] = $count;
			}
			$count++;
		}
		
		require( 'library/widget/mcmulti-widget-fields.php' );
		
		}
	}
}


function minecraft_server_stats_register_widget() {
	register_widget( 'Mcsolo_Server_Status' );
	register_widget( 'Mcmulti_Server_Status' );
}

add_action( 'widgets_init', 'minecraft_server_stats_register_widget' );






?>