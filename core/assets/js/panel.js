jQuery.noConflict()(function($){

	"use strict";

/* ===============================================
   Get template preview
   =============================================== */

	function ilrc_get_template_preview(option) {

		var $template = $('#ilrc_template');
		
		if (!$template.next().hasClass('ilrc_template_preview')) {
			$('<div>').addClass('ilrc_template_preview').insertAfter($template);
		}
		
		$('.ilrc_template_preview').html('<img src="' + ilrc_pluginData.path + 'images/template-previews/' + option + '.png">');

	}

    $('#ilrc_template').on('change',function() {
		var option = $(this).val();
		ilrc_get_template_preview(option);
	});

    $('#ilrc_template').each(function() {
		var option = $(this).val();
		ilrc_get_template_preview(option);
	});

/* ===============================================
   ColorPicker
   =============================================== */

	$('.ilrc_panel_color').wpColorPicker();

/* ===============================================
   Message, after save options
   =============================================== */

	$('.ilrc_panel_message').delay(1000).fadeOut(1000);

/* ===============================================
   RESTORE PLUGIN SETTINGS CONFIRM
   =============================================== */

	$('.ilrc_restore_settings').on("click", function(){

    	if (!window.confirm('Do you want to restore the plugin settings？')) {

			return false;

		}

	});

});
