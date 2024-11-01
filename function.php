<?php

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

function wpcsp_ajaxprocess()
{
	if ($_POST["fucname"] == "check_upload_nonce")
	{
		if (!wp_verify_nonce($_POST['nonce_value'], 'wpcsp_upload_nonce')) {
			echo "0";
			wp_nonce_ays('');
			exit();
		}
	}
	else if ($_POST["fucname"] == "file_upload")
	{
		$msg = wpcsp_file_upload($_POST);
		$upload_list = get_wpcsp_uploadfile_list();
		$data = [
			"message" => $msg,
			"list" => $upload_list,
		];
		echo wp_json_encode($data);
	}
	else if ($_POST["fucname"] == "file_search")
	{
		$data = wpcsp_file_search($_POST);
		echo wp_kses($data, wpcsp_kses_allowed_options());
	}
	else if ($_POST["fucname"] == "setting_save")
	{
		$data = wpcsp_setting_save($_POST);
		echo wp_kses($data, wpcsp_kses_allowed_options());
	}
	else if ($_POST["fucname"] == "get_parameters")
	{
		$data = wpcsp_get_parameters($_POST);
		echo wp_kses($data, wpcsp_kses_allowed_options());
	}

	exit();
}

function wpcsp_get_parameters($param)
{
	$default_settings = [];
	$postid   = isset($param["post_id"]) ? (int)$param["post_id"] : 0;
	$filename = isset($param["filename"]) ? trim(sanitize_text_field($param["filename"])) : '';
	$settings = wpcsp_get_first_class_settings();

	$options = get_option("wpcsp_settings");
	if(isset($options["classsetting"][$postid][$filename]))
	{
		$settings = wp_parse_args($options["classsetting"][$postid][$filename], $default_settings);
	}

	extract($settings);

	$bgwidth = sanitize_text_field($bgwidth);
	$bgheight = sanitize_text_field($bgheight);

	$prints_allowed = ($prints_allowed) ? $prints_allowed : 0;
	$print_anywhere = ($print_anywhere) ? 1 : 0;
	$allow_capture = ($allow_capture) ? 1 : 0;
	$allow_remote = ($allow_remote) ? 1 : 0;

	$background = sanitize_text_field($background);

	$params = " bgwidth='" . $bgwidth . "'" .
		" bgheight='" . $bgheight . "'" .
		" prints_allowed='" . $prints_allowed . "'" .
		" print_anywhere='" . $print_anywhere . "'" .
		" allow_capture='" . $allow_capture . "'" .
		" allow_remote='" . $allow_remote . "'" .
		" background='" . $background . "'";
	
	return $params;
}

function wpcsp_get_first_class_settings()
{
	$settings = [
		'bgwidth' => '600',
		'bgheight' => '600',
		'prints_allowed' => 0,
		'print_anywhere' => 0,
		'allow_capture' => 0,
		'allow_remote' => 0,
		'background' => 'CCCCCC',
	];
	return $settings;
}

function wpcsp_file_upload($param)
{
	$file_error = $param["error"];

	$file_errors = [
		0 => __("There is no error, the file uploaded with success"),
		1 => __("The uploaded file exceeds the upload_max_filesize directive in php.ini"),
		2 => __("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form"),
		3 => __("The uploaded file was only partially uploaded"),
		4 => __("No file was uploaded"),
		6 => __("Missing a temporary folder"),
		7 => __("Upload directory is not writable"),
		8 => __("User not logged in"),
	];

	if ($file_error == 0)
	{
		$msg = '<div class="updated"><p><strong>' . esc_html(__('File Uploaded. You must save "File Details" to insert post', 'wp-copysafe-pdf')) . '</strong></p></div>';
	}
	else
	{
		$msg = '<div class="error"><p><strong>' . esc_html(__('Error', 'wp-copysafe-pdf')) . '!</strong></p><p>' . esc_html($file_errors[$file_error]) . '</p></div>';
	}

	return $msg;
}

function wpcsp_file_search($param)
{
	// get selected file details
	if( ! empty($param['search']) && ! empty($param['post_id']))
	{
		$postid = (int)$param['post_id'];
		$search = trim(sanitize_text_field($param["search"]));

		$files = _get_wpcsp_uploadfile_list();

		$result = FALSE;
		foreach ($files as $file)
		{
			if ($search == trim($file["filename"]))
			{
				$result = TRUE;
			}
		}

		if ( ! $result)
		{
			return "<hr /><h2>No found file</h2>";
		}

		$file_options = wpcsp_get_first_class_settings();

		$wpcsp_options = get_option('wpcsp_settings');
		if ($wpcsp_options["classsetting"][$postid][$search])
		{
			$file_options = $wpcsp_options["classsetting"][$postid][$search];
		}

		extract($file_options, EXTR_OVERWRITE);

		$bgwidth = sanitize_text_field($bgwidth);
		$bgheight = sanitize_text_field($bgheight);
		$prints_allowed = sanitize_text_field($prints_allowed);
		$print_anywhere = sanitize_text_field($print_anywhere);
		$allow_capture = sanitize_text_field($allow_capture);
		$allow_remote = sanitize_text_field($allow_remote);
		$background = sanitize_text_field($background);

		$str = "<hr />
			<div class='icon32' id='icon-file'><br /></div>
		        <h2>PDF Class Settings</h2>
		        <div>
	    			<table cellpadding='0' cellspacing='0' border='0' >
	  					<tbody id='wpcsp_setting_body'> 
							  <tr> 
							    <td align='left' width='50'>&nbsp;</td>
							    <td align='left' width='40'><img src='" . esc_attr(WPCSP_PLUGIN_URL) . "images/help-24-30.png' border='0' alt='Number of prints allowed per session. For no printing set 0.'></td>
							    <td align='left' width='120'>Viewer Width:&nbsp;&nbsp;</td>
							    <td> 
							      <input name='bgwidth' type='text' value='" . esc_attr($bgwidth) . "' size='3'>
							    </td>
							  </tr>
							  <tr> 
							    <td align='left' width='50'>&nbsp;</td>
							    <td align='left' width='40'><img src='" . esc_attr(WPCSP_PLUGIN_URL) . "images/help-24-30.png' border='0' alt='Number of prints allowed per session. For no printing set 0.'></td>
							    <td align='left'>Viewer Height:&nbsp;&nbsp;</td>
							    <td> 
							      <input name='bgheight' type='text' value='" . esc_attr($bgheight) . "' size='3'>
							    </td>
							  </tr>
	  						  <tr> 
							    <td align='left' width='50'>&nbsp;</td>
							    <td align='left' width='40'><img src='" . esc_attr(WPCSP_PLUGIN_URL) . "images/help-24-30.png' border='0' alt='Number of prints allowed per session. For no printing set 0.'></td>
							    <td align='left'>Prints Allowed:&nbsp;&nbsp;</td>
							    <td> 
							      <input name='prints_allowed' type='text' value='" . esc_attr($prints_allowed) . "' size='3'>
							    </td>
							  </tr>
							  <tr> 
							    <td align='left'>&nbsp;</td>
							    <td align='left'><img src='" . esc_attr(WPCSP_PLUGIN_URL) . "images/help-24-30.png' border='0' alt='Check this box to disable Printscreen and screen capture when the class image loads.'></td>
							    <td align='left'>Print Anywhere:</td>
							    <td> 
							      <input name='print_anywhere' type='checkbox' value='1' " . esc_attr($print_anywhere ? 'checked' : '') . ">
							    </td>
							  </tr>
							  <tr> 
							    <td align='left'>&nbsp;</td>
							    <td align='left'><img src='" . esc_attr(WPCSP_PLUGIN_URL) . "images/help-24-30.png' border='0' alt='Check this box to disable Printscreen and screen capture when the class image loads.'></td>
							    <td align='left'>Allow Capture:</td>
							    <td> 
							      <input name='allow_capture' type='checkbox' value='1' " . esc_attr($allow_capture ? 'checked' : '') . ">
							    </td>
							  </tr>
							  <tr> 
							    <td align='left'>&nbsp;</td>
							    <td align='left'><img src='" . esc_attr(WPCSP_PLUGIN_URL) . "images/help-24-30.png' border='0' alt='Check this box to prevent viewing by remote or virtual computers when the class image loads.'></td>
							    <td align='left'>Allow Remote:</td>
							    <td> 
							      <input name='allow_remote' type='checkbox' value='1' " . esc_attr($allow_remote ? 'checked' : '') . ">
							    </td>
							  </tr>
							  <tr> 
							    <td align='left'>&nbsp;</td>
							    <td align='left'><img src='" . esc_attr(WPCSP_PLUGIN_URL) . "images/help-24-30.png' border='0' alt='Check this box to prevent viewing by remote or virtual computers when the class image loads.'></td>
							    <td align='left'>Background:</td>
							    <td> 
							    	<input name='background' type='text' value='" . esc_attr($background) . "' size='10'>
							    </td>
							  </tr>
						</tbody> 
					</table>
			        <p class='submit'>
			            <input type='button' value='Save' class='button-primary cstm_setting_save' id='setting_save' name='submit' />
			            <input type='button' value='Cancel' class='button-primary' id='cancel' />
			        </p>
			</div>";
		return $str;
	}
}

function wpcsp_setting_save($param)
{
	$postid = (int)$param["post_id"];
	$name   = trim(sanitize_text_field($param["nname"]));
	$data   = (array) json_decode(stripcslashes($param["set_data"]));

	// escape user inputs
	$data = array_map("esc_attr", $data);
	extract($data);

	$wpcsp_settings = get_option('wpcsp_settings');
	if (!is_array($wpcsp_settings))
	{
		$wpcsp_settings = [];
	}

	$bgwidth = sanitize_text_field($bgwidth);
	$bgheight = sanitize_text_field($bgheight);
	$prints_allowed = sanitize_text_field($prints_allowed);
	$print_anywhere = sanitize_text_field($print_anywhere);
	$allow_capture = sanitize_text_field($allow_capture);
	$allow_remote = sanitize_text_field($allow_remote);
	$background = sanitize_text_field($background);

	$datas = [
		'bgwidth' => "$bgwidth",
		'bgheight' => "$bgheight",
		'prints_allowed' => "$prints_allowed",
		'print_anywhere' => "$print_anywhere",
		'allow_capture' => "$allow_capture",
		'allow_remote' => "$allow_remote",
		'background' => "$background",
	];

	$wpcsp_settings["classsetting"][$postid][$name] = $datas;

	update_option('wpcsp_settings', $wpcsp_settings);

	$msg = '<div class="updated fade">
		<strong>' . esc_html(__('File Options Are Saved')) . '</strong><br />
		<div style="margin-top:5px;"><a href="#" alt="' . esc_attr($name) . '" class="button-secondary sendtoeditor"><strong>Insert file to editor</strong></a></div>
	</div>';

	return $msg;
}

function _get_wpcsp_uploadfile_list()
{
	$listdata = [];

	if (!is_dir(WPCSP_UPLOAD_PATH)) {
		return $listdata;
	}

	$file_list = scandir(WPCSP_UPLOAD_PATH);

	foreach ($file_list as $file)
	{
		if ($file == "." || $file == "..")
		{
			continue;
		}

		$file_path = WPCSP_UPLOAD_PATH . $file;
		if (filetype($file_path) != "file")
		{
			continue;
		}

		$file_arr = explode('.', $file);
		$ext = end($file_arr);
		if ($ext != "class")
		{
			continue;
		}

		$file_path = WPCSP_UPLOAD_PATH . $file;
		$file_name = $file;
		$file_size = filesize($file_path);
		$file_date = filemtime($file_path);

		if (round($file_size / 1024, 0) > 1)
		{
			$file_size = round($file_size / 1024, 0);
			$file_size = "$file_size KB";
		}
		else
		{
			$file_size = "$file_size B";
		}

		$file_date = gmdate("n/j/Y g:h A", $file_date);

		$listdata[] = [
			"filename" => $file_name,
			"filesize" => $file_size,
			"filedate" => $file_date,
		];
	}

	return $listdata;
}

function get_wpcsp_uploadfile_list()
{
	$table = '';
	$files = _get_wpcsp_uploadfile_list();

	foreach ($files as $file)
	{
		//$link = "<div class='row-actions'>
		//			<span><a href='#' alt='{$file["filename"]}' class='setdetails row-actionslink' title=''>Setting</a></span>&nbsp;|&nbsp;
		//			<span><a href='#' alt='{$file["filename"]}' class='sendtoeditor row-actionslink' title=''>Insert to post</a></span>
		//		</div>" ;
		// prepare table row
		$table .=
			"<tr><td></td><td><a href='#' data-name='" . esc_attr($file["filename"]) . "' class='sendtoeditor row-actionslink'>" . esc_html($file["filename"]) .
			"</a></td><td width='50px'>" . esc_html($file["filesize"]) . "</td><td width='130px'>" . esc_html($file["filedate"]) . "</td></tr>";
	}

	if( ! $table)
	{
		$table .= '<tr><td colspan="3">' . esc_html(__('No file uploaded yet.', 'wp-copysafe-pdf')) . '</td></tr>';
	}

	return $table;
}

function get_wpcsp_browser_info()
{
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$bname = 'Unknown';
	$platform = 'Unknown';
	$version= "";

	//First get the platform?
	if (preg_match('/linux/i', $u_agent)) {
		$platform = 'linux';
	}
	else if (preg_match('/macintosh|mac os x/i', $u_agent)) {
		$platform = 'mac';
	}
	else if (preg_match('/windows|win32/i', $u_agent)) {
		$platform = 'windows';
	}

	// Next get the name of the useragent yes seperately and for good reason
	if(preg_match('/Firefox/i',$u_agent)){
		$bname = 'Mozilla Firefox';
		$ub = "Firefox";
	}
	else if(preg_match('/Chrome/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
		$bname = 'Google Chrome';
		$ub = "Chrome";
	}

	// finally get the correct version number
	$known = array('Version', @$ub, 'other');
	$pattern = '#(?<browser>' . join('|', $known) .')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	if (!preg_match_all($pattern, $u_agent, $matches)) {
		// we have no matching number just continue
	}
	// see how many we have
	$i = count($matches['browser']);
	if ($i != 1) {
		//we will have two since we are not using 'other' argument yet
		//see if version is before or after the name
		if (strripos($u_agent,"Version") < strripos($u_agent,@$ub)){
			$version= $matches['version'][0];
		}
		else {
			$version = $matches['version'][1];
		}
	}
	else {
		$version = $matches['version'][0];
	}

	// check if we have a number
	if( $version == null || $version == "" ){ 
		$version = "?";
	}

	return array(
		'userAgent' => $u_agent,
		'name'      => $bname,
		'version'   => $version,
		'platform'  => $platform,
		'pattern'   => $pattern
	);
} 

function wpcsp_check_artis_browser_version()
{
	$wpcsv_current_browser = get_wpcsp_browser_info();
	$wpcsv_current_browser_data = $wpcsv_current_browser['userAgent'];

	if( $wpcsv_current_browser_data != "" )
	{
		$wpcsv_browser_data = explode("/", $wpcsv_current_browser_data);
		$wpcsv_data_count = count($wpcsv_browser_data);
		if (strpos($wpcsv_current_browser_data, 'ArtisBrowser') !== false)
		{
			$current_version = end($wpcsv_browser_data);
			$wpcsp_settings = get_option('wpcsp_settings');
			$latest_version = $wpcsp_settings["settings"]["latest_version"];
			if( $current_version < $latest_version )
			{
				$ref_url = get_permalink(get_the_ID());
?>
				<script>
				document.location = '<?php echo esc_js(WPCSP_PLUGIN_URL."download-update.html?ref=".$ref_url); ?>';
				</script>
				<?php
			}
		}
	}
}