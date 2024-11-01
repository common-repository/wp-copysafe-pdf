<?php

/**
 * Popup Class.
 */
if (!class_exists('WPCSPPOPUP'))
{
	/**
	 * 
	 */
	class WPCSPPOPUP
	{
		function __construct()
		{
			WPCSPPOPUP::add_popup_script() ;
			call_user_func_array( array( 'WPCSPPOPUP','set_media_upload'), array() ) ;
		}

		public function header_html()
		{?><!DOCTYPE html>
		   <html <?php language_attributes(); ?>>
		   <head>
				<meta charset="<?php bloginfo( 'charset' ); ?>" />
				<title><?php echo esc_html(__("Step Setting"));?></title>
		   </head>
		   <body>
		   <div id="wrapper" class="hfeed">
		   		<ul>
		       <?php
		}

		public function footer_html()
		{
	             ?>
		       </ul>
		    </div>
		    </body>
		<?php
		}
		
		public function set_media_upload()
		{
			include( WPCSP_PLUGIN_PATH . "media-upload.php" );
		}
		
		public function add_popup_script()
		{
			$script_tag = 'script';
			$tag = "<" . $script_tag . " type='text/javascript' src='" . esc_attr(WPCSP_PLUGIN_URL) . "js/copysafepdf_media_uploader.js?v=" . urlencode(WPCSP_ASSET_VERSION) . "'></" . $script_tag . ">" ;

			echo wp_kses($tag, ['script' => [
				'src' => 1,
				'type' => 1,
			]]);
		}
	}

	$popup = new WPCSPPOPUP ();
}