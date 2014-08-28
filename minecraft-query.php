<?php
/*
 *	Plugin Name: Minecraft Query - Server stats
 *	Plugin URI: magicraft.creepy.cz/plugin
 *	Description: Provides widgets to show if your Minecraft server is online and how many players are there.
 *	Version: 1.0
 *	Author: xPaw, Ztudeny
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
$display_json = false;


function mcq_menu() {

	/*
	 * Use the add_option_page function
	 * <?php add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position ); ?>
	 *
	*/
	add_menu_page(
		'Minecraft Query Settings',
		'Server Settings',
		'manage_options',
		'mcq-menu',
		'mcq_options_page'
	);

}

function mcq_register_script_menu_page(){
	wp_enqueue_script( 'mcq_options-page', plugins_url( 'minecraft-query/inc/js/options-page.js' ), array('jquery'), '', true );
}
add_action( 'admin_enqueue_scripts' , 'mcq_register_script_menu_page');

function mcq_register_css_menu_page(){
	wp_enqueue_style( 'mcq_options-page-css', plugins_url( 'minecraft-query/inc/css/options-page-wrapper.css' ) );
}
add_action( 'admin_head' , 'mcq_register_css_menu_page');


// Get all stats of server
function mcq_get_server_stats( $adress ){

	// Edit this ->
	if ( !defined('MQ_SERVER_PORT') ) {
	define( 'MQ_SERVER_PORT', 25565 );
	}
	if ( !defined('MCQ_TIMEOUT') ) {
	define( 'MCQ_TIMEOUT', 1 );
	}
	
	// Display everything in browser, because some people can't look in logs for errors
	Error_Reporting( E_ALL | E_STRICT );
	Ini_Set( 'display_errors', true );
	
	require_once('inc/query-by-xpaw/MinecraftServerPing.php');

	$Timer = MicroTime( true );
	
	$Info = false;
	$Query = null;

	try
	{
		$Query = new MinecraftPing( $adress, MQ_SERVER_PORT, MCQ_TIMEOUT );
		
		$Info = $Query->Query( );
		
		if( $Info === false )
		{
			/*
			 * If this server is older than 1.7, we can try querying it again using older protocol
			 * This function returns data in a different format, you will have to manually map
			 * things yourself if you want to match 1.7's output
			 *
			 * If you know for sure that this server is using an older version,
			 * you then can directly call QueryOldPre17 and avoid Query() and then reconnection part
			 */
			
			$Query->Close( );
			$Query->Connect( );
			
			$Info = $Query->QueryOldPre17( );
		}
	}
	catch( MinecraftPingException $e )
	{
		$Exception = $e;
	}
	
	if( $Query !== null )
	{
		$Query->Close( );
	}
	
	$Timer = Number_Format( MicroTime( true ) - $Timer, 4, '.', '' );
	
	if ($Info) {
		//is set
		$server_stats = $Info;
		$server_stats['error'] = 0;
	}else{
		// $Info is false
		$server_stats['error'] = 1;
	}

	return $server_stats;
}


function mcq_refresh_server() {

	$options = array();
	$options = get_option('mcq_server_stats');
	$last_updated = $options['last_updated'];

	$update_difference = time() - $last_updated;

	if( $update_difference > 60 ) {

		$server_info = $options['server_info'];

		$server_stats = array();
		foreach ($server_info as $server) {

			$server_stats[] = mcq_get_server_stats( $server['server_ip']);
		}

		$options['server_stats'] = $server_stats;
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

	$debug = false;


	if ( isset($_POST['save']) ) {

		$server_names = $_POST['server_name'];
		$server_ips = $_POST['server_ip'];
		$short_description = $_POST['short_description'];

		if ( is_array($server_names) ) {	array_walk($server_names, 'mcq_trim_value');	}else {	trim($server_names);}
		if ( is_array($server_ips) ) {	array_walk($server_ips, 'mcq_trim_value');	}else {	trim($server_ips);}
		if ( is_array($short_description) ) {	array_walk($short_description, 'mcq_trim_value');	}else {	trim($short_description);}

		// now edit them

		$count = 0;
		$server_info = array();

		foreach ($server_names as $name) {

			// If is set ip and name for server, save name, ip and server info.
			if ( $server_ips[$count] != '' && isset( $server_ips[$count] ) ){

				// Save data about servers (user input)
				$server_info[$count]['server_name'] = esc_html( $name );
				$server_info[$count]['server_ip'] = esc_html( $server_ips[$count] );
				$server_info[$count]['short_description'] = esc_html( $short_description[$count] );

				// Call query (call to actual server to get this)
				$server_stats[] = mcq_get_server_stats( $server_ips[$count] );

			}

			$count++;
		}

		// Actual data from server
		$options['server_stats'] = $server_stats;
		//user input about server
		$options['server_info'] = $server_info;

		$options['last_updated'] = time();

		// Save it to the database
		update_option('mcq_server_stats', $options);	

	}

	if ( !isset($options) ) {
		$options = array();
		$options = get_option('mcq_server_stats');
	}

	$server_stats = $options['server_stats'];
	$server_info = $options['server_info'];

	require('inc/options-page-wrapper.php' );

}





/***************
Server status 
*****************/
// Single server
class Mcqsolo_Server_Status extends WP_Widget {

	function mcqsolo_server_status() {
		// Instantiate the parent object
		parent::__construct( false, 'Minecraft Server - single' );

	}

	function widget( $args, $instance ) {
		// Widget output
		global $options;

		extract( $args );
		$title = apply_filters( 'widget_title' , $instance['title'] );
		$display_title = $instance['display_title'];
		$chosen_server = $instance['server'];

		$options = get_option( 'mcq_server_stats' );


		if ( $options != ''){
			$server_stats = $options['server_stats'];
			$server_info = $options['server_info'];


			$server_stats = $server_stats[$chosen_server];
			$server_info = $server_info[$chosen_server];
		}
		if ( !isset($server_info) ){

			echo "<p>You need to set your server info first.<p>";
			
		}else{

		require( 'inc/widget/mcqsolo-front-end.php' );
		
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
		global $options;

		$title = trim( esc_attr($instance['title']) );
		$display_title = esc_attr($instance['display_title']);
		$chosen_server = $instance['server'];

		$options = get_option('mcq_server_stats');
		$server_info = $options['server_info'];

		if ( !isset($server_info)){
			echo "<p>You need to set your server info first.<p>";
		}else{
		
		require( 'inc/widget/mcqsolo-widget-fields.php' );
		
		}

	}
}

// Multi server widget
class Mcqmulti_Server_Status extends WP_Widget {

	function mcqmulti_server_status() {
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
			$server_stats = $options['server_stats'];
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
		
		require( 'inc/widget/mcqmulti-front-end.php' );
		
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
		global $options;

		$options = get_option('mcq_server_stats');
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
		
		require( 'inc/widget/mcqmulti-widget-fields.php' );
		
		}
	}
}


function minecraft_server_stats_register_widget() {
	register_widget( 'Mcqsolo_Server_Status' );
	register_widget( 'Mcqmulti_Server_Status' );
}

add_action( 'widgets_init', 'minecraft_server_stats_register_widget' );

function mcq_frontend_scripts_and_styles() {

	wp_enqueue_style( 'mcq_frontend_css', plugins_url( 'minecraft-query/inc/css/minecraft-query.css' ) );
	wp_enqueue_script( 'mcq_frontend_js', plugins_url( 'minecraft-query/minecraft-query.js' ), array('jquery'), '', true );

}
add_action( 'wp_enqueue_scripts', 'mcq_frontend_scripts_and_styles' );


?>