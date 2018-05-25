/**
 * All Types Meta Box Class JS
 *
 * JS used for the custom metaboxes and other form items.
 *
 * Copyright 2011 - 2013 Ohad Raz (admin@bainternet.info)
 * Modifications 2013 - 2014 by ShapingRain (support@shapingrain.com)
 * @since 1.0
 */

var $ =jQuery.noConflict();

var e_d_count = 0;
var Ed_array = Array;
//fix editor on window resize
jQuery(document).ready(function($) {
	//editor resize fix
	$(window).resize(function() {
		$.each(Ed_array, function() {
			var ee = this;
			$(ee.getScrollerElement()).width(100); // set this low enough
			width = $(ee.getScrollerElement()).parent().width();
			$(ee.getScrollerElement()).width(width); // set it to
			ee.refresh();
		});
	});
});

function update_repeater_fields(){
	_metabox_fields.init();

}
//metabox fields object
var _metabox_fields = {
	oncefancySelect: false,
	init: function(){

		if (!this.oncefancySelect){
			this.fancySelect();
			this.oncefancySelect = true;
		}
		this.load_conditional();
		this.load_time_picker();
		this.load_date_picker();
		this.load_color_picker();
		this.load_icon_picker();
		this.load_font_picker();
		this.load_group_selectors();
		this.load_tooltips();

		// repeater Field
		$(".at-re-toggle").on('click', function() {
			$(this).parent().find('.repeater-table').slideToggle();
		});

		// repeater sortable
		$('.repeater-sortable').sortable(
				{
					handle: ".at-repeater-block-title",
					opacity: 0.6,
					cursor: 'move'
				}
		);

	},
	load_group_selectors: function() {
		$( ".group-selector" ).each(function() {
			var group_name = ( $( this ).attr('name') );
			$('.group-' + group_name).hide();
			$(this).on('change', function(e) {
				e.preventDefault();
				if ( $(this).is(':checkbox') ) {
					var group_val =  $(this).attr('checked');
				} else {
					var group_val =  $(this).find('option:selected').val();
				}
				$('.group-' + group_name).hide().each(function() {
					var group_val_options = $(this).data('group-value');
					if ( group_val_options.indexOf( group_val ) != -1 ) {
						$(this).show();
					}
				});

			}).change();
		});
	},
	load_tooltips: function() {
		$('.has-tip').frosty({
		});
	},
	fancySelect: function(){
		if ($().select2){
			$(".at-select, .at-posts-select, .at-tax-select").each(function (){
				if(! $(this).hasClass('no-fancy'))
					$(this).select2();
			});
		}
	},
	get_query_var: function(name){
		var match = RegExp('[?&]' + name + '=([^&#]*)').exec(location.href);
		return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
	},
	load_conditional: function(){
		$(".conditinal_control").click(function(){
			if($(this).is(':checked')){
				$(this).next().show('fast');
			}else{
				$(this).next().hide('fast');
			}
		});
	},
	load_time_picker: function(){
		$('.at-time').each( function() {

			var $this   = $(this),
					format   = $this.attr('rel'),
					aampm    = $this.attr('data-ampm');
			if ('true' == aampm)
				aampm = true;
			else
				aampm = false;

			$this.timepicker( { showSecond: true, timeFormat: format, ampm: aampm } );

		});
	},

	load_date_picker: function() {
		$('.at-date').each( function() {

			var $this  = $(this),
					format = $this.attr('rel');

			$this.datepicker( { showButtonPanel: true, dateFormat: format } );

		});
	},

	load_icon_picker: function() {
		$('.fontawesome-picker').fontawesomePicker();
	},

	load_font_picker: function() {
		$('.at-font-select').fontPicker();
	},

	load_color_picker: function(){
		if ($('.at-color-iris').length>0)
			$('.at-color-iris').wpColorPicker();
	}
};
//call object init in delay
window.setTimeout('_metabox_fields.init();',200);

//upload fields handler
var simplePanelmedia;
jQuery(document).ready(function($){
	var simplePanelupload =(function(){
		var inited;
		var file_id;
		var file_url;
		var file_type;
		function init (){
			return {
				image_frame: [],
				file_frame: [],
				hooks:function(){
					$(document).on('click','.simplePanelimageUpload,.simplePanelfileUpload', function( event ){
						event.preventDefault();
						if ($(this).hasClass('simplePanelfileUpload'))
							inited.upload($(this),'file');
						else
							inited.upload($(this),'image');
					});

					$(document).on('click', '.simplePanelimageUploadclear,.simplePanelfileUploadclear' ,function( event ){
						event.preventDefault();
						inited.set_fields($(this));
						$(inited.file_url).val("");
						$(inited.file_id).val("");
						if ($(this).hasClass('simplePanelimageUploadclear')){
							inited.set_preview('image',false);
							inited.replaceImageUploadClass($(this));
						}else{
							inited.set_preview('file',false);
							inited.replaceFileUploadClass($(this));
						}
					});
				},
				set_fields: function (el){
					inited.file_url = $(el).prev();
					inited.file_id = $(inited.file_url).prev();
				},
				upload:function(el,utype){
					inited.set_fields(el);
					if (utype == 'image')
						inited.upload_Image($(el));
					else
						inited.upload_File($(el));
				},
				upload_File: function(el){
					// If the media frame already exists, reopen it.
					var mime = $(el).attr('data-mime_type') || '';
					var ext = $(el).attr("data-ext") || false;
					var name = $(el).attr('id');
					var multi = ($(el).hasClass("multiFile")? true: false);

					if ( typeof inited.file_frame[name] !== "undefined")  {
						if (ext){
							inited.file_frame[name].uploader.uploader.param( 'uploadeType', ext);
							inited.file_frame[name].uploader.uploader.param( 'uploadeTypecaller', 'my_meta_box' );
						}
						inited.file_frame[name].open();
						return;
					}
					// Create the media frame.

					inited.file_frame[name] = wp.media({
						library: {
							type: mime
						},
						title: jQuery( this ).data( 'uploader_title' ),
						button: {
							text: jQuery( this ).data( 'uploader_button_text' )
						},
						multiple: multi  // Set to true to allow multiple files to be selected
					});


					// When an image is selected, run a callback.
					inited.file_frame[name].on( 'select', function() {
						// We set multiple to false so only get one image from the uploader
						attachment = inited.file_frame[name].state().get('selection').first().toJSON();
						// Do something with attachment.id and/or attachment.url here
						$(inited.file_id).val(attachment.id);
						$(inited.file_url).val(attachment.url);
						inited.replaceFileUploadClass(el);
						inited.set_preview('file',true);
					});
					// Finally, open the modal

					inited.file_frame[name].open();
					if (ext){
						inited.file_frame[name].uploader.uploader.param( 'uploadeType', ext);
						inited.file_frame[name].uploader.uploader.param( 'uploadeTypecaller', 'my_meta_box' );
					}
				},
				upload_Image:function(el){
					var name = $(el).attr('id');
					var multi = ($(el).hasClass("multiFile")? true: false);
					// If the media frame already exists, reopen it.
					if ( typeof inited.image_frame[name] !== "undefined")  {
						inited.image_frame[name].open();
						return;
					}
					// Create the media frame.
					inited.image_frame[name] =  wp.media({
						library: {
							type: 'image'
						},
						title: jQuery( this ).data( 'uploader_title' ),
						button: {
							text: jQuery( this ).data( 'uploader_button_text' )
						},
						multiple: multi  // Set to true to allow multiple files to be selected
					});
					// When an image is selected, run a callback.
					inited.image_frame[name].on( 'select', function() {
						// We set multiple to false so only get one image from the uploader
						attachment = inited.image_frame[name].state().get('selection').first().toJSON();
						// Do something with attachment.id and/or attachment.url here
						$(inited.file_id).val(attachment.id);
						$(inited.file_url).val(attachment.url);
						inited.replaceImageUploadClass(el);
						inited.set_preview('image',true);
					});
					// Finally, open the modal
					inited.image_frame[name].open();
				},
				replaceImageUploadClass: function(el){
					if ($(el).hasClass("simplePanelimageUpload")){
						$(el).removeClass("simplePanelimageUpload").addClass('simplePanelimageUploadclear').val('Remove Image');
					}else{
						$(el).removeClass("simplePanelimageUploadclear").addClass('simplePanelimageUpload').val('Upload Image');
					}
				},
				replaceFileUploadClass: function(el){
					if ($(el).hasClass("simplePanelfileUpload")){
						$(el).removeClass("simplePanelfileUpload").addClass('simplePanelfileUploadclear').val('Remove File');
					}else{
						$(el).removeClass("simplePanelfileUploadclear").addClass('simplePanelfileUpload').val('Upload File');
					}
				},
				set_preview: function(stype,ShowFlag){
					ShowFlag = ShowFlag || false;
					var fileuri = $(inited.file_url).val();
					if (stype == 'image'){
						if (ShowFlag) {
							$(inited.file_id).prev('span.simplePanelImagePreview').html('<img src="' + fileuri + '" class="preview-image">').removeClass('no-image').addClass('has-image').show();
						}
						else {
							$(inited.file_id).prev('span.simplePanelImagePreview').html('').removeClass('has-image').addClass('no-image').hide();
						}
					}else{
						if (ShowFlag)
							$(inited.file_id).prev().find('ul').append('<li><a href="' + fileuri + '" target="_blank">'+fileuri+'</a></li>');
						else
							$(inited.file_id).prev().find('ul').children().remove();
					}
				}
			}
		}
		return {
			getInstance :function(){
				if (!inited){
					inited = init();
				}
				return inited;
			}
		}
	})();
	simplePanelmedia = simplePanelupload.getInstance();
	simplePanelmedia.hooks();
});