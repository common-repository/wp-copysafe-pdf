<?php defined('ABSPATH') or exit;

//Check for nonce validation
$wpcsp_media_upload_nonce = sanitize_key(@$_GET['wpcsp_token']);

$allow_uploads = FALSE;

if ( ! wp_verify_nonce($wpcsp_media_upload_nonce, 'wpcsp_token'))
{
	wp_nonce_ays("Error");
}
else
{
	if (current_user_can('administrator'))
	{
		$timestamp = time();
		$token = md5('unique_salt' . $timestamp);

		$post_id       = (int)$_GET["post_id"];
		$wpcsp_options = get_option("wpcsp_settings");

		$wp_upload_dir = wp_upload_dir();
		$wp_upload_dir_path = str_replace("\\", "/", $wp_upload_dir['basedir']);

		$upload_path = $wp_upload_dir_path . '/' . $wpcsp_options["settings"]["upload_path"];

		$max_upload_size = wp_max_upload_size();
		if (!$max_upload_size)
		{
			$max_upload_size = 0;
		}
		$max_size = esc_html(size_format($max_upload_size));

		$_SESSION['token'] = $token;

		$session_id = session_id();
		$token_session = "{$token}-{$session_id}";

		$admin_only = $wpcsp_options["settings"]["admin_only"];
		$allow_uploads = TRUE;
		if ($admin_only)
		{
			global $current_user;

			$allow_uploads = FALSE;
			$user_roles = "|" . implode("|", $current_user->roles) . "|";
			if (strpos($user_roles, "administrator") > 0)
			{
				$allow_uploads = TRUE;
			}
		}
	}

	$display_upload_form = !is_dir($upload_path) ? FALSE : TRUE;
}
?>
<div class="wrap" id="wpcsp_div" title="SecureImage">
    <div id="wpcsp_message"></div>
    <div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
        <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
          <?php if ($allow_uploads) { ?>
              <li class="ui-state-default ui-corner-top ui-state-active"><a
                          href="#" class="ui-tabs-anchor" id="tabs-1-bt">Add
                      New</a></li>
          <?php } ?>
            <li class="ui-state-default ui-corner-top <?php echo esc_attr($allow_uploads ? "" : "ui-state-active"); ?>">
                <a href="#" class="ui-tabs-anchor" id="tabs-2-bt">Search</a>
            </li>
            <li class="ui-state-default ui-corner-top"><a href="#"
                                                          class="ui-tabs-anchor"
                                                          id="tabs-3-bt">Existing Files</a></li>
        </ul>
      <?php if ($allow_uploads) { ?>
          <div id="tabs-1"
               class="wpcsp_addnew ui-tabs-panel ui-widget-content ui-corner-bottom">
              <div class="icon32" id="icon-addnew"><br/></div>
              <h2>Add New Class File</h2>
              <div class="wpcsp_upload_content">
                  <div id="upload-queue"></div>
                <?php if ($display_upload_form): ?>
                    <table>
                        <tr>
                            <td>
                                <div class="mfu-plugin-uploader multiple">
                                    <input id="mfu-plugin-uploader-button"
                                           type="button"
                                           value="<?php esc_attr_e('Select Files'); ?>"
                                           class="mfu-plugin-uploader-button button">
                                    <span class="ajaxnonce"
                                          id="<?php echo esc_attr(wp_create_nonce('wpcsp_upload_nonce')); ?>"></span>
                                </div>
                            </td>
                            <td>
                                <div id="upload-filename"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div id="upload-status"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="button" value="Add File"
                                       class="button button-primary"
                                       id="upload"/>
                                <a class="button-secondary"
                                   onclick="try{top.tb_remove();}catch(e){}; return false;"
                                   href="#" id="close1">Close</a>
                            </td>
                        </tr>
                    </table>
                <?php else: ?>
                    <div class="updated"><p><strong>Upload directory doesn\'t
                                exist. Please configure upload directory to
                                upload class files.</strong></p></div>
                <?php endif; ?>
              </div>
              <div id="wpcsp-progress-bar"></div>
              <p>Maximum upload size: <?php echo esc_html($max_size); ?></p>
              <p>You can choose file options after file is uploaded.</p>
              <p>If you use same name with uploaded class file, it will be
                  overwritten.</p>
              <input type="hidden" value="<?php echo esc_attr($post_id); ?>" name="postid"
                     id="postid"/>
              <input type="hidden" value="<?php echo esc_attr(WPCSP_PLUGIN_URL); ?>"
                     id="plugin-url"/>
              <input type="hidden" value="<?php echo esc_attr(WPCSP_PLUGIN_PATH); ?>"
                     id="plugin-dir"/>
              <input type="hidden" value="<?php echo esc_attr(WPCSP_UPLOAD_PATH); ?>"
                     id="upload-path"/>
              <input type="hidden" value="<?php echo esc_attr($max_upload_size); ?>"
                     id="upload-max-size"/>
              <input type="hidden" value="<?php echo esc_attr($timestamp); ?>"
                     id="token_timestamp"/>
              <input type="hidden" value="<?php echo esc_attr($token_session); ?>"
                     id="token"/>
              <div class="clear"></div>
          </div>
      <?php } ?>
        <div id="tabs-2"
             class="wpcsp_search ui-tabs-panel ui-widget-content ui-corner-bottom" <?php echo ($allow_uploads) ? "style=\"display:none;\"" : ""; ?> >
            <div class="icon32" id="icon-search"><br/></div>
            <h2>Search File</h2>
            <p>
                File name : <input type="text" id="wpcsp_searchfile"
                                   name="wpcsp_searchfile"
                                   class="regular-text"/>
                <input type="hidden" value="<?php echo esc_attr($post_id); ?>" name="postid"
                       id="postid"/>
                <input type="button" value="Search"
                       class="button button-primary" id="search" name="search"/>
                <a class="button-secondary"
                   onclick="try{top.tb_remove();}catch(e){}; return false;"
                   href="#" id="close2">Close</a>
            </p>
            <div id="file_details"></div>
            <div class="clear"></div>
        </div>

        <div id="tabs-3"
             class="wpcsp_filelist ui-tabs-panel ui-widget-content ui-corner-bottom"
             style="display:none;">
            <div class="icon32" id="icon-file"><br/></div>
            <h2>Uploaded Class Files</h2>
            <table class="wp-list-table widefat">
                <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th>File</th>
                    <th>Size</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody id="wpcsp_upload_list">
                <?php echo wp_kses(get_wpcsp_uploadfile_list(), wpcsp_kses_allowed_options()); ?>
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
            <div class="clear"></div>
        </div>
    </div>
    <div id="wpcsp_ajax_process">
        <div class="wpcsp_ajax_process"></div>
    </div>
</div>