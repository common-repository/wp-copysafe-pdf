<?php defined('ABSPATH') OR exit;

// ============================================================================================================================
# convert shortcode to html output
function wpcsp_shortcode($atts)
{
	wpcsp_check_artis_browser_version();
	
	global $post;
	
	$postid   = $post->ID;
	$filename = $atts["name"];

	if (!file_exists(WPCSP_UPLOAD_PATH . $filename))
	{
		return "<div style='padding:5px 10px;background-color:#fffbcc'><strong>File(" . esc_html($filename) . ") don't exist</strong></div>";
	}

	$settings = wpcsp_get_first_class_settings();

	// get plugin options
	$wpcsp_options = get_option('wpcsp_settings');
	if ($wpcsp_options["settings"])
	{
		$settings = wp_parse_args($wpcsp_options["settings"], $settings);
	}

	if ($wpcsp_options["classsetting"][$postid][$filename])
	{
		$settings = wp_parse_args($wpcsp_options["classsetting"][$postid][$filename], $settings);
	}

	$settings = wp_parse_args($atts, $settings);

	extract($settings);

	$asps = ($asps) ? '1' : '0';
	$firefox = ($ff) ? '1' : '0';
	$chrome = ($ch) ? '1' : '0';

	$print_anywhere = ($print_anywhere) ? '1' : '0';
	$allow_capture = ($allow_capture) ? '1' : '0';
	$allow_remote = ($allow_remote) ? '1' : '0';

	$plugin_url = WPCSP_PLUGIN_URL;
	$upload_url = WPCSP_UPLOAD_URL;

	$script_tag = 'script';

	ob_start();
	?>
	<script type="text/javascript">
		var wpcsp_plugin_url = "<?php echo esc_js($plugin_url); ?>";
		var wpcsp_upload_url = "<?php echo esc_js($upload_url); ?>";
	</script>
	<script type="text/javascript">
	<!-- hide JavaScript from non-JavaScript browsers
		var m_bpDebugging = false;
		var m_szMode = "<?php echo esc_js($mode); ?>";
		var m_szClassName = "<?php echo esc_js($name); ?>";
		var m_szImageFolder = "<?php echo esc_js($upload_url); ?>"; //  path from root with / on both ends
		var m_bpPrintsAllowed = "<?php echo esc_js($prints_allowed); ?>";
		var m_bpPrintAnywhere = "<?php echo esc_js($print_anywhere); ?>";
		var m_bpAllowCapture = "<?php echo esc_js($allow_capture); ?>";
		var m_bpAllowRemote = "<?php echo esc_js($allow_remote); ?>";
		var m_bpLanguage = "<?php echo esc_js($language); ?>";
		var m_bpBackground = "<?php echo esc_js($background); ?>"; // background colour without the #
		var m_bpWidth = "<?php echo esc_js($bgwidth); ?>"; // width of PDF display in pixels
		var m_bpHeight = "<?php echo esc_js($bgheight); ?>"; // height of PDF display in pixels

		var m_bpASPS = "<?php echo esc_js($asps); ?>";
		var m_bpChrome = "<?php echo esc_js($chrome); ?>";
		var m_bpFx = "<?php echo esc_js($firefox); ?>"; //all firefox browsers from version 5 and later

		if (m_szMode == "debug") {
			m_bpDebugging = true;
		}
		// -->
	</script>
	<<?php echo esc_html($script_tag); ?> src="<?php echo esc_attr(WPCSP_PLUGIN_URL . 'js/wp-copysafe-pdf.js?v=' . urlencode(WPCSP_ASSET_VERSION)); ?>"></<?php echo esc_html($script_tag); ?>>
	<div>
		<script type="text/javascript">
			<!-- hide JavaScript from non-JavaScript browsers
			if ((m_szMode == "licensed") || (m_szMode == "debug")) {
				insertCopysafePDF("<?php echo esc_js($name); ?>");
			}
			else {
				document.writeln("<img src='<?php echo esc_js($plugin_url); ?>images/demo_placeholder.jpg' border='0' alt='Demo mode'>");
			}
			// -->
		</script>
	</div>
	<?php
	$output = ob_get_clean();

	return $output;
}

// ============================================================================================================================
# delete short code
function wpcsp_delete_shortcode()
{
	// get all posts
	$posts_array = get_posts();
	foreach ($posts_array as $post)
	{
		// delete short code
		$post->post_content = wpcsp_deactivate_shortcode($post->post_content);
		// update post
		wp_update_post($post);
	}
}

// ============================================================================================================================
# deactivate short code
function wpcsp_deactivate_shortcode($content)
{
	// delete short code
	$content = preg_replace('/\[copysafepdf name="[^"]+"\]\[\/copysafepdf\]/s', '', $content);
	return $content;
}

// ============================================================================================================================
# search short code in post content and get post ids
function wpcsp_search_shortcode($file_name)
{
	// get all posts
	$posts = get_posts();
	$IDs = FALSE;
	foreach ($posts as $post)
	{
		$file_name = preg_quote($file_name, '\\');
		preg_match('/\[copysafepdf name="' . $file_name . '"\]\[\/copysafepdf\]/s', $post->post_content, $matches);
		if (is_array($matches) && isset($matches[1])) {
			$IDs[] = $post->ID;
		}
	}
	
	return $IDs;
}