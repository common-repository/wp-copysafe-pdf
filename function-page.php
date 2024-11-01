<?php defined('ABSPATH') OR exit;

// ============================================================================================================================
# "List" Page
function wpcsp_admin_page_list()
{
	$msg = '';
	$table = '';
	$files = _get_wpcsp_uploadfile_list();

	if( ! empty($_POST))
	{
		if (wp_verify_nonce($_POST['wpcopysafepdf_wpnonce'], 'wpcopysafepdf_settings'))
		{
			$wpcsp_options = get_option('wpcsp_settings');

			$wp_upload_dir = wp_upload_dir();
			$wp_upload_dir_path = str_replace("\\", "/", $wp_upload_dir['basedir']);

			if (!empty($wpcsp_options['settings']['upload_path'])) {
				$target_dir = $wp_upload_dir_path . '/' . $wpcsp_options['settings']['upload_path'];
			} else {
				$target_dir = $wp_upload_dir_path;
			}

			$target_file = $target_dir . basename($_FILES["copysafe-pdf-class"]["name"]);
			$uploadOk    = 1;
			$file_type   = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

			// Check if image file is a actual image or fake image
			if (isset($_POST["copysafe-pdf-class-submit"]))
			{
				// Allow only .class file formats
				if ($_FILES["copysafe-pdf-class"]["name"] == "")
				{
					$msg .= '<div class="error"><p><strong>' . esc_html(__('Please upload file to continue.', 'wp-copysafe-pdf')) . '</strong></p></div>';
					$uploadOk = 0;
				}
				else if ($file_type != "class")
				{
					$msg .= '<div class="error"><p><strong>' . esc_html(__('Sorry, only .class files are allowed.', 'wp-copysafe-pdf')) . '</strong></p></div>';
					$uploadOk = 0;
				}
				
				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0)
				{
					$msg .= '<div class="error"><p><strong>' . esc_html(__('Sorry, your file was not uploaded.', 'wp-copysafe-pdf')) . '</strong></p></div>';
					// if everything is ok, try to upload file
				}
				else
				{
					$upload_file = $_FILES["copysafe-pdf-class"];

					//Register path override
					add_filter('upload_dir', 'wpcsp_upload_dir');

					//Move file
					$movefile = wp_handle_upload($upload_file, [
						'test_form' => false,
						'test_type' => false,
						'mimes' => [
							'class' => 'application/octet-stream'
						],
					]);

					//Remove path override
					remove_filter('upload_dir', 'wpcsp_upload_dir');

					if($movefile && ! isset($movefile['error']))
					{
						$text_the_file = sprintf(
							/* translators: %1s: file name, %2s: base url */
							__('The file %1$s has been uploaded. Click <a href="%2$s/wp-admin/admin.php?page=wpcsp_list">here</a> to update below list.', 'wp-copysafe-pdf'),
							basename($_FILES["copysafe-pdf-class"]["name"]),
							get_site_url()
						);
						
						$msg .= '<div class="updated"><p><strong>' . wp_kses($text_the_file, wpcsp_kses_allowed_options()) . '</strong></p></div>';
					}
					else
					{
						$msg .= '<div class="error"><p><strong>' . esc_html(__('Sorry, there was an error uploading your file.', 'wp-copysafe-pdf')) . '</strong></p></div>';
					}
				}
			}
		}
	}

	if ( ! empty($files))
	{
		foreach ($files as $file)
		{
			$bare_url = 'admin.php?page=wpcsp_list&cspfilename=' . $file["filename"] . '&action=cspdel';

			$complete_url = wp_nonce_url($bare_url, 'cspdel', 'cspdel_nonce');

			$link = "<div class='row-actions'>
					<span><a href='" . esc_attr($complete_url) . "' title=''>Delete</a></span>
				</div>";
			
			// prepare table row
			$table .= "<tr><td></td><td>" . esc_html($file["filename"]) . " {$link}</td><td>" . esc_html($file["filesize"]) . "</td><td>" . esc_html($file["filedate"]) . "</td></tr>";
		}
	}


	if (!$table) {
		$table .= '<tr><td colspan="3">' . esc_html(__('No file uploaded yet.', 'wp-copysafe-pdf')) . '</td></tr>';
	}

	$wpcsp_options = get_option('wpcsp_settings');
	if ($wpcsp_options["settings"])
	{
		extract($wpcsp_options["settings"], EXTR_OVERWRITE);
	}

	$wp_upload_dir      = wp_upload_dir();
	$wp_upload_dir_path = str_replace("\\", "/", $wp_upload_dir['basedir']);
	$upload_dir         = $wp_upload_dir_path . '/' . $upload_path;

	$display_upload_form = !is_dir($upload_dir) ? FALSE : TRUE;

	if (!$display_upload_form) {
		$msg = '<div class="updated"><p><strong>' . __('Upload directory doesn\'t exist. Please configure upload directory to upload class files.', 'wp-copysafe-pdf') . '</strong></p></div>';
	}
	?>
    <div class="wrap">
        <div class="icon32" id="icon-file"><br/></div>
        <?php echo wp_kses($msg, wpcsp_kses_allowed_options()); ?>
        <h2>List PDF Class Files</h2>
        <?php if ($display_upload_form): ?>
            <form action="" method="post" enctype="multipart/form-data">
                <?php echo wp_kses(wp_nonce_field('wpcopysafepdf_settings', 'wpcopysafepdf_wpnonce'), wpcsp_kses_allowed_options()); ?>
                <input type="file" name="copysafe-pdf-class" value=""/>
                <input type="submit" name="copysafe-pdf-class-submit" value="Upload"/>
            </form>
        <?php endif; ?>
        <div id="col-container" style="width:700px;">
            <div class="col-wrap">
                <h3>Uploaded PDF Class Files</h3>
                <table class="wp-list-table widefat">
                    <thead>
                    <tr>
                        <th width="5px">&nbsp;</th>
                        <th>File</th>
                        <th>Size</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php echo wp_kses($table, wpcsp_kses_allowed_options()); ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>&nbsp;</th>
                        <th>File</th>
                        <th>Size</th>
                        <th>Date</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="clear"></div>
    </div>
	<?php
}

// ============================================================================================================================
# "Settings" page
function wpcsp_admin_page_settings()
{
	$msg = '';
	$wp_upload_dir = wp_upload_dir();
	$wp_upload_dir_path = str_replace("\\", "/", $wp_upload_dir['basedir']);

	if( ! empty($_POST))
	{
		if (wp_verify_nonce($_POST['wpcsp_wpnonce'], 'wpcsp_settings'))
		{
			$wpcsp_options = get_option('wpcsp_settings');
			extract($_POST, EXTR_OVERWRITE);

			if (!$upload_path) {
				$upload_path = 'copysafe-pdf/';
			}
			else {
				$upload_path = sanitize_text_field($upload_path);
			}

			$upload_path = str_replace("\\", "/", stripcslashes($upload_path));
			if (substr($upload_path, -1) != "/") {
				$upload_path .= "/";
			}

			$wpcsp_options['settings'] = [
				'admin_only' => sanitize_text_field($admin_only),
				'upload_path' => $upload_path,
				'mode' => $mode,
				'language' => sanitize_text_field($language),
				'background' => $background,
				'asps' => !empty(sanitize_text_field($asps))  ? 'checked' : '',
				'ff' => !empty(sanitize_text_field($ff)) ? 'checked' : '',
				'ch' => !empty(sanitize_text_field($ch)) ? 'checked' : '',
				'latest_version' => $latest_version,
			];

			$max_upload_size = wp_max_upload_size();
			if ( ! $max_upload_size )
			{
				$max_upload_size = 0;
			}

			$wpcsp_options['settings']['max_size'] = esc_html(size_format($max_upload_size));

			$upload_path = $wp_upload_dir_path . '/' . $upload_path;
			if( ! is_dir($upload_path))
			{
				wp_mkdir_p($upload_path);
			}

			update_option('wpcsp_settings', $wpcsp_options);
			$msg = '<div class="updated"><p><strong>' . esc_html(__('Settings Saved', 'wp-copysafe-pdf')) . '</strong></p></div>';
		}
	}

	$wpcsp_options = get_option('wpcsp_settings');
	if ($wpcsp_options["settings"]) {
		extract($wpcsp_options["settings"], EXTR_OVERWRITE);
	}

	$upload_dir = $wp_upload_dir_path . '/' . $upload_path;

	if (!is_dir($upload_dir)) {
		$msg = '<div class="updated"><p><strong>' . esc_html(__('Upload directory doesn\'t exist.', 'wp-copysafe-pdf')) . '</strong></p></div>';
	}

	$select = '<option value="demo">Demo Mode</option><option value="licensed">Licensed</option><option value="debug">Debugging Mode</option>';
	$select = str_replace('value="' . $mode . '"', 'value="' . $mode . '" selected', $select);

	$lnguageOptions = [
		"0c01" => "Arabic",
		"0004" => "Chinese (simplified)",
		"0404" => "Chinese (traditional)",
		"041a" => "Croatian",
		"0405" => "Czech",
		"0413" => "Dutch",
		"" => "English",
		"0464" => "Filipino",
		"000c" => "French",
		"0007" => "German",
		"0408" => "Greek",
		"040d" => "Hebrew",
		"0439" => "Hindi",
		"000e" => "Hungarian",
		"0421" => "Indonesian",
		"0410" => "Italian",
		"0411" => "Japanese",
		"0412" => "Korean",
		"043e" => "Malay",
		"0415" => "Polish",
		"0416" => "Portuguese (BR)",
		"0816" => "Portuguese (PT)",
		"0419" => "Russian",
		"0c0a" => "Spanish",
		"041e" => "Thai",
		"041f" => "Turkish",
		"002a" => "Vietnamese",
	];
	$lnguageOptionStr = '';
	foreach ($lnguageOptions as $k => $v)
	{
		$chk = str_replace("value='$language'", "value='$language' selected", "value='$k'");
		$lnguageOptionStr .= "<option $chk >$v</option>";
	}
	?>
    <style type="text/css">#wpcsp_page_setting img {cursor: pointer;}</style>
    <div class="wrap">
        <div class="icon32" id="icon-settings"><br/></div>
        <?php echo wp_kses($msg, wpcsp_kses_allowed_options()); ?>
        <h2>Default Settings</h2>
        <form action="" method="post">
          <?php echo wp_kses(wp_nonce_field('wpcsp_settings', 'wpcsp_wpnonce'), wpcsp_kses_allowed_options()); ?>
            <table cellpadding='1' cellspacing='0' border='0'
                   id='wpcsp_page_setting'>
                <p><strong>Default settings applied to all protected PDF
                        pages:</strong></p>
                <tbody>
                <tr>
                    <td align='left' width='50'>&nbsp;</td>
                    <td align='left' width='30'><img
                                src='<?php echo esc_attr(WPCSP_PLUGIN_URL); ?>images/help-24-30.png'
                                border='0'
                                alt='Allow admin only for new uploads.'></td>
                    <td align="left" nowrap>Allow Admin Only:</td>
                    <td align="left"><input name="admin_only" type="checkbox"
                                            value="checked" <?php echo esc_attr($admin_only); ?>>
                    </td>
                </tr>
                <tr>
                    <td align='left' width='50'>&nbsp;</td>
                    <td align='left' width='30'><img
                                src='<?php echo esc_attr(WPCSP_PLUGIN_URL); ?>images/help-24-30.png'
                                border='0'
                                alt='Path to the upload folder for PDF.'>
                    <td align="left" nowrap>Upload Folder:</td>
                    <td align="left"><input value="<?php echo esc_attr($upload_path); ?>"
                                            name="upload_path"
                                            class="regular-text code"
                                            type="text"><br />
                        Only specify the folder name. It will be located in site's upload directory, <?php echo esc_attr($wp_upload_dir_path); ?>.
                    </td>
                </tr>
                <tr>
                    <td align='left' width='50'>&nbsp;</td>
                    <td align='left' width='30'><img
                                src='<?php echo esc_attr(WPCSP_PLUGIN_URL); ?>images/help-24-30.png'
                                border='0'
                                alt='Set the mode to use. Use Licensed if you have licensed images. Otherwise set for Demo or Debug mode.'>
                    </td>
                    <td align="left">Mode</td>
                    <td align="left"><select name="mode">
                        <?php echo wp_kses($select, wpcsp_kses_allowed_options()); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td align='left' width='50'>&nbsp;</td>
                    <td align='left' width='30'><img
                                src='<?php echo esc_attr(WPCSP_PLUGIN_URL); ?>images/help-24-30.png'
                                border='0'
                                alt='Enter minimum version for ArtisBrowser to check.'>
                    </td>
                    <td align="left">Latest Version</td>
                    <td align="left">
                        <input type="text" name="latest_version" size="8" value="<?php echo esc_attr($latest_version ? $latest_version : 27.11); ?>" />
                        <br />
                        Enter minimum version for ArtisBrowser to check.
                    </td>
                </tr>
                <tr>
                    <td align='left' width='50'>&nbsp;</td>
                    <td align='left' width='30'><img
                                src='<?php echo esc_attr(WPCSP_PLUGIN_URL); ?>images/help-24-30.png'
                                border='0'
                                alt='Set the language that is used in the viewer toolbar and messages. Default is English.'>
                    <td align="left">Language:</td>
                    <td align="left"><select name="language">
                        <?php echo wp_kses($lnguageOptionStr, wpcsp_kses_allowed_options()); ?>
                        </select></td>
                </tr>
                <tr>
                    <td align='left' width='50'>&nbsp;</td>
                    <td align='left' width='30'><img
                                src='<?php echo esc_attr(WPCSP_PLUGIN_URL); ?>images/help-24-30.png'
                                border='0'
                                alt='Set the color for the unused space in the PDF viewer.'>
                    </td>
                    <td align="left">Page color:</td>
                    <td align="left"><input value="<?php echo esc_attr($background); ?>"
                                            name="background" type="text"
                                            size="8"></td>
                </tr>
                <tr class="copysafe-video-browsers">
                    <td colspan="5"><h2 class="title">Browser allowed</h2></td>
                </tr>
                <tr>
                    <td align='left' width='50'>&nbsp;</td>
                    <td align='left' width='30'><img
                                src='<?php echo esc_attr(WPCSP_PLUGIN_URL); ?>images/help-24-30.png'
                                border='0'
                                alt='Allow visitors using the ArtisBrowser to access this page.'>
                    </td>
                    <td align="left" nowrap>Allow ArtisBrowser:</td>
                    <td align="left"><input name="asps" type="checkbox"
                                            value="checked" <?php echo esc_attr($asps); ?>>
                    </td>
                </tr>
                <tr>
                    <td align='left' width='50'>&nbsp;</td>
                    <td align='left' width='30'><img
                                src='<?php echo esc_attr(WPCSP_PLUGIN_URL); ?>images/help-24-30.png'
                                border='0'
                                alt='Allow visitors using the Firefox web browser to access this page.'>
                    </td>
                    <td align="left">Allow Firefox:</td>
                    <td align="left"><input name="ff"
                                            type="checkbox" <?php echo esc_attr($ff); ?>> ( for testing only by admin )
                    </td>
                </tr>
                <tr>
                    <td align='left' width='50'>&nbsp;</td>
                    <td align='left' width='30'><img
                                src='<?php echo esc_attr(WPCSP_PLUGIN_URL); ?>images/help-24-30.png'
                                border='0'
                                alt='Allow visitors using the Chrome web browser to access this page.'>
                    </td>
                    <td align="left">Allow Chrome:</td>
                    <td align="left"><input name="ch"
                                            type="checkbox" <?php echo esc_attr($ch); ?>> ( for testing only by admin )
                    </td>
                </tr>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" value="Save Settings"
                       class="button-primary" id="submit" name="submit">
            </p>
        </form>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
    <script type='text/javascript'>
      jQuery(document).ready(function () {
        jQuery("#wpcsp_page_setting img").click(function () {
          alert(jQuery(this).attr("alt"));
        });
      });
    </script>
  <?php
}