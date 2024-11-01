//////////////////////////////////////////////////////////////
//This file handles the AJAX based uploading of class files.//
//////////////////////////////////////////////////////////////
jQuery(document).ready(function () {

  var file_name = null;
  var clicktrigger = false;

  jQuery("#TB_ajaxContent").css({"width": "650px", "height": "100%"});

  //jQuery("#cancel").on("click", function () {
  jQuery(document).on("click", "#cancel" , function() {
    jQuery('#file_details').html("");
  });

  //jQuery('.sendtoeditor').on("click", function () {
  jQuery(document).on("click", ".sendtoeditor" , function() {

    if (clicktrigger) return;
    clicktrigger = true;
    jQuery(this).attr("disabled", true);
    var nname = jQuery(this).attr('data-name');
    wpcsp_process_setting('sendeditor', 'start');
    ajaxdata = {
      action: 'wpcsp_ajaxprocess',
      fucname: 'get_parameters',
      filename: nname,
      post_id: jQuery("#postid").val()
    };
    jQuery.post(ajaxurl, ajaxdata, function (param) {
      var file = "[copysafepdf name='" + nname + "'" + param + "]";
      send_to_editor(file);
      wpcsp_process_setting('sendeditor', 'end');
      clicktrigger = true;
    });
    return false;
  });

  jQuery("#wpcsp_div .ui-tabs-anchor").click(function () {
    var iid = jQuery(this).attr("id");
    iid = iid.substring(0, iid.length - 3);
    jQuery("#wpcsp_div .ui-tabs-panel").hide();
    jQuery("#" + iid).show();
    jQuery(this).parents(".ui-tabs-nav").children(".ui-state-default").removeClass("ui-state-active");
    jQuery(this).parent().addClass("ui-state-active");
  });

  //----------------------------------------
  var wpcsp_string_adjust = function (s, n) {
    var s_length = s.length;
    if (s_length <= n) return s;
    var c_n = Math.floor(n / 2);
    var e_n = s_length - n + 3 + c_n;
    s = s.substr(0, c_n) + "..." + s.substr(e_n);
    return s;
  }
  var pluginurl = jQuery("#plugin-url").val();
  var plugindir = jQuery("#plugin-dir").val();
  var upload_path = jQuery("#upload-path").val();
  var max_size = jQuery("#upload-max-size").val();
  var prequeue = "";

  var wpcsp_process_setting = function (frm, status) {
    if (status == "start") jQuery("#wpcsp_ajax_process").show();
    if (status == "end") jQuery("#wpcsp_ajax_process").hide();
    if (frm == "load") {
      if (status == "start") {
        jQuery("#wpcsp_message").html("");
        jQuery('input:button').attr("disabled", true);
      }
      if (status == "end") {

        prequeue = "";
        jQuery("#custom-queue").html("No file chosen");
        jQuery('input:button').attr("disabled", false);
      }
    }

    if (frm == "search") {
      if (status == "start") {
        jQuery("#search").attr("disabled", true);
      }
      if (status == "end") {
        jQuery("#search").attr("disabled", false);
      }
    }

    if (frm == "setting") {
      if (status == "start") {

      }
      if (status == "end") {

      }
    }
  }

  if (jQuery('.mfu-plugin-uploader').length > 0) {
    var options = false;
    var container = jQuery('.mfu-plugin-uploader');
    options = JSON.parse(JSON.stringify(global_uploader_options));
    var wpcsp_upload_nonce_value = container.find('.ajaxnonce').attr('id');


    console.log('Progress', 'Checking nonce...', wpcsp_upload_nonce_value);


    if (container.hasClass('multiple')) {
      options['multi_selection'] = true;
    }

    //Here we will check if the nonce is valid or not!

    ajaxdata = {
      action: 'wpcsp_ajaxprocess',
      fucname: 'check_upload_nonce',
      nonce_value: wpcsp_upload_nonce_value,

    };
    var nonce_result = "1";
    jQuery.post(ajaxurl, ajaxdata, function (param) {
      //console.log("The nonce is verified");
      nonce_result = param;
    });


    if (nonce_result == "1") {
      var new_url = window.location.host;

      var uploader = new plupload.Uploader({
        browse_button: 'mfu-plugin-uploader-button', // this can be an id of a DOM element or the DOM element itself
        runtimes: 'html5,flash,silverlight,gears,html4',
        flash_swf_url: '/wp-includes/js/plupload/plupload.flash.swf',
        silverlight_xap_url: '/wp-includes/js/plupload/plupload.silverlight.xap',
        max_file_size: max_size + 'b',
        urlstream_upload: true,
        file_data_name: 'async-upload',
        multipart: true,
        multi_selection: false,
        resize: {width: 300, height: 300, quality: 90},
        multipart_params: {_ajax_nonce: '', action: 'my-plugin-upload-action'},
        url: 'admin-ajax.php',
        filters: [{title: "Class files", extensions: "class"}]
      });
      uploader.init();
      console.log(window.location.host);


      // EVENTS
      // init
      uploader.bind('Init', function (up) {
        console.log('Init', up);
        jQuery('#wpcsp-progress-bar').progressbar({
          value: 0
        });
      });
	  
      // error
      uploader.bind('Error', function (up, args) {
			if( args["code"] == '-600' ){
				jQuery("#wpcsp_message").html('<div class="error"><p>'+args["message"]+' <b>Please upload file less than '+global_uploader_options.max_file_size+' of size.</b></p></div>');
			}
			if( args["code"] == '-601' ){
				jQuery("#wpcsp_message").html('<div class="error"><p>'+args["message"]+' <b>Please upload only .class file.</b></p></div>');
			}
      });

      // file added
      uploader.bind('FilesAdded', function (up, files) {
        jQuery.each(files, function (i, file) {
          console.log('File is Added', i, file);
          jQuery("#upload-filename").html(file.name);
          jQuery("#upload-status").html("Upload Started");
        });

        up.refresh();
        up.start();
      });

      // upload progress
      uploader.bind('UploadProgress', function (up, file) {
        jQuery( "#wpcsp-progress-bar" ).progressbar({
          value: file.percent
        });
        console.log('Progress', up, file)
      });

      // file uploaded
      uploader.bind('FileUploaded', function (up, file, response) {
        response = jQuery.parseJSON(response.response);
        console.log(response);
        if (response['status'] == 'success') {
          console.log('Success', up, file, response);
          jQuery("#upload-status").html("Upload Complete");

          var file_name = file.name;
          ajaxdata = {
            action: 'wpcsp_ajaxprocess',
            fucname: 'file_upload'
          };
          jQuery.post(ajaxurl, ajaxdata, function (param) {
            wpcsp_process_setting("load", "end");
            var contents = jQuery.parseJSON(param);
            jQuery("#wpcsp_message").html(contents["message"]);
            jQuery("#wpcsp_upload_list").html(contents["list"]);
            jQuery("#tabs-2-bt").trigger("click");
            jQuery("#wpcsp_searchfile").val(file_name);
            jQuery("#search").trigger("click");
          });

        }
        else {
          console.log('Error', up, file, response);
          jQuery("#upload-status").html("Error Uploading File");
        }

      });
    }
    else {
      console.log('Error Uploading File');
      jQuery("#upload-status").html("Error Uploading File");
    }
  }

  //jQuery("#upload").click(function () {
  jQuery(document).on("click", "#upload" , function() {

    wpcsp_process_setting("load", "end");
    //var contents = jQuery.parseJSON(param);
    //jQuery("#wpcsp_message").html(contents["message"]);
    //jQuery("#wpcsp_upload_list").html(contents["list"]);
    jQuery("#tabs-2-bt").trigger("click");
    jQuery("#wpcsp_searchfile").val(file_name);
    jQuery("#search").trigger("click");
  });

  //jQuery("#search").click(function () {
  jQuery(document).on("click", "#search" , function() {
    file_name = jQuery("#wpcsp_searchfile").val();
    var postid = jQuery("#postid").val();
    if (!file_name) {
      alert('Type a file name');
      jQuery("#wpcsp_searchfile").focus();
    } else {
      ajaxdata = {
        action: 'wpcsp_ajaxprocess',
        fucname: 'file_search',
        search: file_name,
        post_id: postid
      };
      wpcsp_process_setting("search", "start");
      jQuery.post(ajaxurl, ajaxdata, function (param) {
        wpcsp_process_setting("search", "end");
        jQuery('#file_details').html(param);
      });
    }
  });

  //jQuery('.setdetails').on("click", function () {
  jQuery(document).on("click", ".setdetails" , function() {
    jQuery("#tabs-2-bt").trigger("click");
    jQuery("#wpcsp_searchfile").val(jQuery(this).attr('alt'));
    jQuery("#search").trigger("click");
    return false;
  });

  //jQuery("#setting_save").on("click", function () {
  jQuery(document).on("click", "#setting_save" , function() {
    var setData = {};
    jQuery("#wpcsp_setting_body input").each(function () {
      var nname = jQuery(this).attr("name");
      if (nname == "print_anywhere" || nname == "allow_capture" || nname == "allow_remote") {
        setData[nname] = (jQuery(this).attr("checked") == "checked") ? "checked" : "";
      } else {
        setData[nname] = jQuery(this).val();
      }
    });
    ajaxdata = {
      action: 'wpcsp_ajaxprocess',
      fucname: 'setting_save',
      post_id: jQuery("#postid").val(),
      nname: jQuery("#wpcsp_searchfile").val(),
      set_data: jQuery.toJSON(setData)
    };
    wpcsp_process_setting("setting", "start");

    
    jQuery.post(ajaxurl, ajaxdata, function (param) {
      jQuery("#wpcsp_message").html(param);
      wpcsp_process_setting("setting", "end");

      jQuery('a.sendtoeditor').click();
    });
  });

  jQuery("#wpcsp_setting_body img").on("click", function () {
    alert(jQuery(this).attr("alt"));
  });

});