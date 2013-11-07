(function($) {

	
	/*
	*  acf/setup_fields
	*
	*  This event is triggered when ACF adds any new elements to the DOM. 
	*
	*  @type	function
	*  @since	1.0.0
	*  @date	01/01/12
	*
	*  @param	event		e: an event object. This can be ignored
	*  @param	Element		postbox: An element which contains the new HTML
	*
	*  @return	N/A
	*/
	
	$(document).live('acf/setup_fields', function(e, postbox) {
	
		// display buttons dependiing on field value on load
		$(postbox).find('.field_type-external_media').each(function() {
			var val = $(this).children(".acf-field-external-media").val();
			if (val) {
				$(this).children(".has-external-media").show();
				$(this).find(".hover .acf-button-delete").show().css("display", "block");
				$(this).children(".has-external-media.meta").css("display", "block");
			} else {
				$(this).children(".no-external-media").show();
			}
		});
		
		// delete button event handler
		$(".field_type-external_media .acf-button-delete").click(function(e) {
			e.preventDefault();
			var parent = $(this).parents(".field_type-external_media");
			$(parent).children(".acf-external-media-image").attr("src", "");
			$(parent).children(".has-external-media").fadeOut();
			$(parent).children(".no-external-media").fadeIn();
			$(parent).children(".acf-field-external-media").val("");
			$(parent).find(".hover .acf-button-delete").slideUp();
		});
		
		// add button event handler
		$(".acf-external-media-image, .button.add-external-media, .field_type-external_media .acf-button-edit").click(function(e) {
			e.preventDefault();
			var parent = $(this).parents(".field_type-external_media");
			
			// input prompt
			var old_url = $(parent).children(".acf-field-external-media").val();
			var new_url = prompt("URL", old_url);
			if (new_url === null) {
				return false;
			}
			
			// ajax-validate new input
			$(parent).children(".meta").fadeOut();
			$(parent).children(".acf-field-external-media").val(new_url);
			
			var data = {
				action: 'acf_external_media_get_link_data',
				url: new_url
			};
			
			$.post(ajaxurl, data, function(response) {
				var external_media_data = $.parseJSON(response);
				
				// show new thumbnail and meta data
				$(parent).find(".acf-external-media-image").attr("src", external_media_data.thumb);
				$(parent).find(".acf-button-delete").slideDown().css("display", "block");
				meta = '<p><strong><a href="' + external_media_data.url + '" alt="" target="' + external_media_data.title + '">' + external_media_data.title + '</a></strong><br><code>' + external_media_data.url + '</code></p>';
				$(parent).children(".has-external-media.meta").removeClass("error").html(meta);
				$(parent).children(".has-external-media.meta").find("code").html(external_media_data.url);
				$(parent).children(".no-external-media").fadeOut();
				$(parent).children(".has-external-media, .has-external-media.meta").fadeIn();
				
				// server side error
				if (external_media_data.type == "error") {
					$(parent).children(".has-external-media.meta").html(external_media_data.title).addClass("error");
				}
			});
			
		});
		
	});
	
})(jQuery);