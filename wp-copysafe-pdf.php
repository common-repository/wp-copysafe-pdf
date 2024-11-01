<?php
/*
  Plugin Name: CopySafe PDF Protection
  Plugin URI: https://artistscope.com/copysafe_pdf_protection_wordpress_plugin.asp
  Description: This Wordpress plugin enables sites using CopySafe PDF to easily add protected PDF for display in the ArtisBrowser.
  Author: ArtistScope
  Text Domain: wp-copysafe-pdf
  Version: 1.31
  License: GPLv2
  Author URI: https://artistscope.com/

  Copyright 2024 ArtistScope Pty Limited


  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// ================================================================================ //
//                                                                                  //
//  WARNING : DON'T CHANGE ANYTHING BELOW IF YOU DONT KNOW WHAT YOU ARE DOING        //
//                                                                                  //
// ================================================================================ //

if (!defined('ABSPATH')) {
  exit;
} // Exit if accessed directly

# set script max execution time to 5mins
set_time_limit(300);

define('WPCSP_ASSET_VERSION', 1.04);

require_once __DIR__ . "/function-common.php";
require_once __DIR__ . "/function-shortcode.php";
require_once __DIR__ . "/function-page.php";
require_once __DIR__ . "/function.php";

function wpcsp_enable_extended_upload($mime_types = []) {
	// You can add as many MIME types as you want.
	$mime_types['class'] = 'application/octet-stream';
	// If you want to forbid specific file types which are otherwise allowed,
	// specify them here.  You can add as many as possible.
	return $mime_types;
}
add_filter('upload_mimes', 'wpcsp_enable_extended_upload');

// ============================================================================================================================
# register WordPress menus
function wpcsp_admin_menus() {
	add_menu_page('CopySafe PDF', 'CopySafe PDF', 'publish_posts', 'wpcsp_list');
	add_submenu_page('wpcsp_list', 'CopySafe PDF List Files', 'List Files', 'publish_posts', 'wpcsp_list', 'wpcsp_admin_page_list');
	add_submenu_page('wpcsp_list', 'CopySafe PDF Settings', 'Settings', 'publish_posts', 'wpcsp_settings', 'wpcsp_admin_page_settings');
}

// ============================================================================================================================
# delete file options
function wpcsp_delete_file_options($file_name) {
	$file_name = trim($file_name);
	$wpcsp_options = get_option('wpcsp_settings');

	if(isset($wpcsp_options["classsetting"]) && is_array($wpcsp_options["classsetting"]))
	{
		foreach ($wpcsp_options["classsetting"] as $k => $arr)
		{
			if (@$wpcsp_options["classsetting"][$k][$file_name]) {
				unset($wpcsp_options["classsetting"][$k][$file_name]);
				if (!count($wpcsp_options["classsetting"][$k])) {
					unset($wpcsp_options["classsetting"][$k]);
				}
			}
		}
	}
	update_option('wpcsp_settings', $wpcsp_options);
}

// ============================================================================================================================
# install media buttons
function wpcsp_media_buttons($context)
{
	global $post_ID;
	// generate token for links
	$token = wp_create_nonce('wpcsp_token');
	$url = admin_url('?wpcsp-popup=file_upload&wpcsp_token=' . $token . '&post_id=' . $post_ID);

	echo
		"<a href='" . esc_attr($url) . "' class='thickbox' id='wpcsp_link' data-body='no-overflow' title='CopySafe PDF'>" .
		"<img src='" . esc_attr(plugin_dir_url(__FILE__)) . "/images/copysafepdfbutton.png'></a>";
}

// ============================================================================================================================
# browser detector js file
function wpcsp_load_js() {
	// load custom JS file
	wp_register_script('wp-copysafe-pdf', WPCSP_PLUGIN_URL . 'js/wp-copysafe-pdf.js', [], WPCSP_ASSET_VERSION, ['in_footer' => false]);
}

// ============================================================================================================================
# admin page styles
function wpcsp_admin_load_styles() {
	// register custom CSS file & load
	wp_register_style('wpcsp-style', plugins_url('/css/wp-copysafe-pdf.css', __FILE__), [], WPCSP_ASSET_VERSION);
	wp_enqueue_style('wpcsp-style');
}

function wpcsp_is_admin_postpage()
{
	$is_admin_postpage = FALSE;

	$script_name = explode("/", $_SERVER["SCRIPT_NAME"]);
	$ppage = end($script_name);
	if ($ppage == "post-new.php" || $ppage == "post.php") {
		$is_admin_postpage = TRUE;
	}

	return $is_admin_postpage;
}

function wpcsp_includecss_js()
{
	if (!wpcsp_is_admin_postpage()) {
		return;
	}

	global $wp_popup_upload_lib;

	if ($wp_popup_upload_lib) {
		return;
	}

	$wp_popup_upload_lib = TRUE;
	
	wp_enqueue_style('jquery-ui-1.9');
	
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-progressbar');
	wp_enqueue_script('jquery.json');
}

function wpcsp_load_admin_scripts()
{
	wp_register_style('jquery-ui-1.9', '//code.jquery.com/ui/1.9.2/themes/redmond/jquery-ui.css', [], WPCSP_ASSET_VERSION);
	wp_register_script('jquery.json', WPCSP_PLUGIN_URL . 'lib/jquery.json-2.3.js', ['plupload-all'], WPCSP_ASSET_VERSION, ['in_footer' => true]);
}

// ============================================================================================================================
# setup plugin
function wpcsp_setup()
{
	//----add codding----
	$options = get_option("wpcsp_settings");
	define('WPCSP_PLUGIN_PATH', str_replace("\\", "/", plugin_dir_path(__FILE__))); //use for include files to other files
	define('WPCSP_PLUGIN_URL', plugins_url('/', __FILE__));

	$wp_upload_dir = wp_upload_dir();
	$wp_upload_dir_path = str_replace("\\", "/", $wp_upload_dir['basedir']);
	$upload_path = $wp_upload_dir_path . '/' . $options["settings"]["upload_path"];
	define('WPCSP_UPLOAD_PATH', $upload_path); //use for include files to other files

	$wp_upload_dir = wp_upload_dir();
	$wp_upload_dir_url = str_replace("\\", "/", $wp_upload_dir['baseurl']);
	$upload_url = $wp_upload_dir_url . '/' . $options["settings"]["upload_path"];
	define('WPCSP_UPLOAD_URL', $upload_url);

	add_action('admin_head', 'wpcsp_includecss_js');
	add_action('wp_ajax_wpcsp_ajaxprocess', 'wpcsp_ajaxprocess');

	//Sanitize the GET input variables
	$pagename = !empty(@$_GET['page']) ? sanitize_key(@$_GET['page']) : '';

	$cspfilename = !empty(@$_GET['cspfilename']) ? sanitize_file_name(@$_GET['cspfilename']) : '';

	$action = !empty(@$_GET['action']) ? sanitize_key(@$_GET['action']) : '';

	$cspdel_nonce = !empty(@$_GET['cspdel_nonce']) ? sanitize_key(@$_GET['cspdel_nonce']) : '';

	if ($pagename == 'wpcsp_list' && $cspfilename != '' && $action == 'cspdel')
	{
		//check that nonce is valid and user is administrator
		if (current_user_can('administrator') && wp_verify_nonce($cspdel_nonce, 'cspdel'))
		{
			echo esc_html(__("Nonce has been verified", "wp-copysafe-pdf"));
			wpcsp_delete_file_options($cspfilename);
			if (file_exists(WPCSP_UPLOAD_PATH . $cspfilename))
			{
				wp_delete_file(WPCSP_UPLOAD_PATH . $cspfilename);
			}
			wp_redirect('admin.php?page=wpcsp_list');
		}
		else {
			wp_nonce_ays('');
		}
	}

	if (isset($_GET['wpcsp-popup']) && @$_GET["wpcsp-popup"] == "file_upload") {
		require_once(WPCSP_PLUGIN_PATH . "popup_load.php");
		exit();
	}
	//=============================
	// load js file
	add_action('wp_enqueue_scripts', 'wpcsp_load_js');

	//Register admin scripts
	add_action('admin_enqueue_scripts', 'wpcsp_load_admin_scripts');

	// load admin CSS
	add_action('admin_print_styles', 'wpcsp_admin_load_styles');

	// add short code
	add_shortcode('copysafepdf', 'wpcsp_shortcode');

	// if user logged in
	if (is_user_logged_in()) {
		// install admin menu
		add_action('admin_menu', 'wpcsp_admin_menus');

		// check user capability
		if (current_user_can('edit_posts'))
		{
			// load media button
			add_action('media_buttons', 'wpcsp_media_buttons');
		}
	}
}

// ============================================================================================================================
# runs when plugin activated
function wpcsp_activate() {
	// if this is first activation, setup plugin options
	if( ! get_option('wpcsp_settings'))
	{
		$wp_upload_dir = wp_upload_dir();
		$wp_upload_dir_path = str_replace("\\", "/", $wp_upload_dir['basedir']);

		// set plugin folder
		$upload_dir = 'copysafe-pdf/';
		$upload_path = $wp_upload_dir_path . '/' . $upload_dir;

		// set default options
		$wpcsp_options['settings'] = [
			'admin_only' => "checked",
			'upload_path' => $upload_dir,
			'mode' => "demo",
			'language' => "",
			'background' => "EEEEEE",
			'asps' => "checked",
			'ff' => "",
			'ch' => "",
		];

		update_option('wpcsp_settings', $wpcsp_options);

		if( ! is_dir($upload_path))
		{
			wp_mkdir_p($upload_path, 0, TRUE);
		}
		// create upload directory if it is not exist
	}
}

// ============================================================================================================================
# runs when plugin deactivated
function wpcsp_deactivate() {
	// remove text editor short code
	remove_shortcode('copysafepdf');
}

// ============================================================================================================================
# runs when plugin deleted.
function wpcsp_uninstall()
{
	global $wp_filesystem;

	require_once ABSPATH . 'wp-admin/includes/file.php';
	WP_Filesystem();

	// delete all uploaded files
	$wp_upload_dir = wp_upload_dir();
	$wp_upload_dir_path = str_replace("\\", "/", $wp_upload_dir['basedir']);

	$default_upload_dir = $wp_upload_dir_path . '/copysafe-pdf/';

	if (is_dir($default_upload_dir))
	{
		$dir = scandir($default_upload_dir);
		foreach ($dir as $file)
		{
			if ($file != '.' || $file != '..')
			{
				wp_delete_file($default_upload_dir . $file);
			}
		}

		$wp_filesystem->rmdir($default_upload_dir);
	}

	// delete upload directory
	$options = get_option("wpcsp_settings");

	if ($options["settings"]["upload_path"])
	{
		$upload_path = $wp_upload_dir_path . '/' . $options["settings"]["upload_path"];
		if (is_dir($upload_path))
		{
			$dir = scandir($upload_path);
			foreach ($dir as $file)
			{
				if ($file != '.' || $file != '..')
				{
					wp_delete_file($upload_path . '/' . $file);
				}
			}

			// delete upload directory
			$wp_filesystem->rmdir($upload_path);
		}
	}

	// delete plugin options
	delete_option('wpcsp_settings');

	// unregister short code
	remove_shortcode('copysafepdf');

	// delete short code from post content
	wpcsp_delete_shortcode();
}

// ============================================================================================================================
# register plugin hooks
register_activation_hook(__FILE__, 'wpcsp_activate'); // run when activated
register_deactivation_hook(__FILE__, 'wpcsp_deactivate'); // run when deactivated
register_uninstall_hook(__FILE__, 'wpcsp_uninstall'); // run when uninstalled

add_action('init', 'wpcsp_setup');
//Imaster Coding

function wpcsp_admin_js() {
	wp_register_script('wp-copysafe-pdf-uploader', WPCSP_PLUGIN_URL . 'js/copysafepdf_media_uploader.js', [
		'jquery',
		'plupload-all',
	],
	WPCSP_ASSET_VERSION,
	['in_footer' => true]
	);
}

function wpcsp_admin_head() {
	$uploader_options = [
		'runtimes' => 'html5,silverlight,flash,html4',
		'browse_button' => 'mfu-plugin-uploader-button',
		'container' => 'mfu-plugin-uploader',
		'drop_element' => 'mfu-plugin-uploader',
		'file_data_name' => 'async-upload',
		'multiple_queues' => TRUE,
		'max_file_size' => wp_max_upload_size() . 'b',
		'url' => admin_url('admin-ajax.php'),
		'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
		'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
		'filters' => [
			[
			'title' => __('Allowed Files'),
			'extensions' => '*',
			],
		],
		'multipart' => TRUE,
		'urlstream_upload' => TRUE,
		'multi_selection' => TRUE,
		'multipart_params' => [
			'_ajax_nonce' => '',
			'action' => 'my-plugin-upload-action',
		],
	];
	?>
<script type="text/javascript">
	var global_uploader_options =<?php echo wp_json_encode($uploader_options); ?>;
</script>
	<?php
}

add_action('admin_head', 'wpcsp_admin_head');

function wpcsp_includecss_js_to_footer(){
	if ( ! wpcsp_is_admin_postpage())
		return;
	
	?>
	<script>
	if( jQuery("#wpcsp_link").length > 0 ){
		if( jQuery("#wpcsp_link").data("body") == "no-overflow" ){
			jQuery("body").addClass("wps-no-overflow");
			
		}
	}
	</script>
	<?php
}
add_action('admin_footer', 'wpcsp_includecss_js_to_footer') ;

function wpcsp_ajax_action()
{
	add_filter('upload_dir', 'wpcsp_upload_dir');
	// check ajax nonce
	//check_ajax_referer( __FILE__ );
	if (current_user_can('upload_files'))
	{
		$response = [];
		// handle file upload
		$id = media_handle_upload(
			'async-upload',
			0,
			[
				'test_form' => TRUE,
				'action' => 'my-plugin-upload-action',
			]
		);

		// send the file' url as response
		if (is_wp_error($id)) {
			$response['status'] = 'error22';
			$response['error'] = $id->get_error_messages();
		}
		else
		{
			$response['status'] = 'success';

			$src = wp_get_attachment_image_src($id, 'thumbnail');
			$response['attachment'] = [];
			$response['attachment']['id'] = $id;
			$response['attachment']['src'] = $src[0];
		}
	}
	remove_filter('upload_dir', 'wpcsp_upload_dir');
	echo wp_json_encode($response);
	exit;
}

add_action('wp_ajax_my-plugin-upload-action', 'wpcsp_ajax_action');


$upload = wp_upload_dir();

remove_filter('upload_dir', 'wpcsp_upload_dir');
function wpcsp_upload_dir($upload) {
	$upload['subdir'] = '/copysafe-pdf';
	$upload['path'] = $upload['basedir'] . $upload['subdir'];
	$upload['url'] = $upload['baseurl'] . $upload['subdir'];
	return $upload;
}