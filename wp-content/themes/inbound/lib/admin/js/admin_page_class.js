/**
 * Admin page class
 *
 * JS used for the admin pages class and other form items.
 *
 * Copyright 2011 Ohad Raz (admin@bainternet.info)
 * Modifications for ShapingRain Copyright 2013-2014 ShapingRain (support@shapingrain.com)
 */

var $ = jQuery.noConflict();
//code editor
var Ed_array = Array;
//upload button
var formfield1;
var formfield2;
var file_frame;

var theme_select_is_open = false;

jQuery(document).ready(function($) {

	apc_init();
	//editor rezise fix
	$(window).resize(function() {
		$.each(Ed_array, function() {
			var ee = this;
			$(ee.getScrollerElement()).width(100); // set this low enough
			width = $(ee.getScrollerElement()).parent().width();
			$(ee.getScrollerElement()).width(width); // set it to
			ee.refresh();
		});
	});
}); //end ready

function uniqid(prefix, more_entropy) {
	//  discuss at: http://phpjs.org/functions/uniqid/
	// original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	//  revised by: Kankrelune (http://www.webfaktory.info/)
	//        note: Uses an internal counter (in php_js global) to avoid collision
	//        test: skip
	//   example 1: uniqid();
	//   returns 1: 'a30285b160c14'
	//   example 2: uniqid('foo');
	//   returns 2: 'fooa30285b1cd361'
	//   example 3: uniqid('bar', true);
	//   returns 3: 'bara20285b23dfd1.31879087'

	if (typeof prefix === 'undefined') {
		prefix = '';
	}

	var retId;
	var formatSeed = function (seed, reqWidth) {
		seed = parseInt(seed, 10)
				.toString(16); // to hex str
		if (reqWidth < seed.length) {
			// so long we split
			return seed.slice(seed.length - reqWidth);
		}
		if (reqWidth > seed.length) {
			// so short we pad
			return Array(1 + (reqWidth - seed.length))
					.join('0') + seed;
		}
		return seed;
	};

	// BEGIN REDUNDANT
	if (!this.php_js) {
		this.php_js = {};
	}
	// END REDUNDANT
	if (!this.php_js.uniqidSeed) {
		// init seed with big random int
		this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
	}
	this.php_js.uniqidSeed++;

	// start with prefix, add current milliseconds hex string
	retId = prefix;
	retId += formatSeed(parseInt(new Date()
			.getTime() / 1000, 10), 8);
	// add seed hex string
	retId += formatSeed(this.php_js.uniqidSeed, 5);
	if (more_entropy) {
		// for more entropy we add a float lower to 10
		retId += (Math.random() * 10)
				.toFixed(8)
				.toString();
	}

	return retId;
}

function update_repeater_fields() {
	loadColorPicker();
	loadDatePicker();
	loadTimePicker();
	loadTooltips();
	loadGroups();
	loadImagePicker();
	loadUidFields();
	loadFontPicker();
	loadRepeaterUI();
}


function apc_init(){
	/*
	 Interface Init
	 */
	loadRepeaterUI();
	loadDatePicker();
	loadTimePicker();
	loadColorPicker();
	loadTooltips();
	loadGroups();
	loadImagePicker();
	loadUidFields();
	loadFontPicker();

	// add file
	$('.at-add-file').click( function() {
		var $first = $(this).parent().find('.file-input:first');
		$first.clone().insertAfter($first).show();
		return false;
	});

	// delete file
	$('.at-upload').delegate( '.at-delete-file', 'click' , function() {

		var $this   = $(this),
				$parent = $this.parent(),
				data     = $this.attr('rel');

		$.post( ajaxurl, { action: 'at_delete_file', data: data }, function(response) {
			response == '0' ? ( alert( 'File has been successfully deleted.' ), $parent.remove() ) : alert( 'You do NOT have permission to delete this file.' );
		});

		return false;
	});

	$(".repeater-sortable").sortable(
			{
				handle : ".at-repeater-block-title"
			}
	);
	/*  initiate sortable fields option */
	$(".at-sortable").sortable({
		'placeholder' : "ui-state-highlight"
	});

	//new image upload field
	load_images_muploader();
	//delete img button
	$('body').on('click', '.at-delete_image_button', function(event){
		event.preventDefault();
		remove_image($(this));
		return false;
	});
	//upload images
	$('body').on('click','.at-upload_image_button', function(event){
		event.preventDefault();
		image_upload($(this));
		return false;
	});
	/**
	 * listen for import button click
	 * @since 0.8
	 * @return void
	 */
	$("#apc_import_b").on("click",function(){do_ajax_import_export('import');});
	/**
	 * listen for export button click
	 * @since 0.8
	 * @return void
	 */
	$("#apc_export_b").on("click",function(){do_ajax_import_export('export');});

	$("#apc_import_defaults").on("click",function(){do_ajax_import_export('defaults');});


	//refresh page
	$(document).on("click", "#apc_refresh_page_b", function(){refresh_page();});
	//status alert dismiss
	$('[data-dismiss="alert"]').on("click",function(){$(this).parent().remove()});
	/* Fonts Refresh */
	$("#web_fonts_refresh").on("click",function(){do_ajax_fonts_refresh();});

	$(document).on("click", ".clean_form", function() {do_ajax_clean_form($(this));});
}

function loadRepeaterUI() {
	$(".at-re-toggle").on('click', function(e) {
		e.stopPropagation();
		e.preventDefault();

		if(e.handled !== true) // This will prevent event triggering more then once
		{
			$(e.target).prev().slideToggle();
			e.handled = true;
		}
		return false;
	});

	$('.at-block-title-input').on('keyup', function(e) {
		e.stopPropagation();
		e.preventDefault();
		$(e.target).parent().parent().parent().find('.at-repeater-block-title').html($(this).val());
	});
}


function updateRepeaterUI() {
	/*
	$( ".at-repeater-new-item" ).each(function() {
		$(this).find(".at-re-toggle").on('click', function() {
			$(this).prev().slideToggle();
		});

		$(this).find('.at-block-title-input').on('keyup', function(e) {
			$(this).parent().parent().parent().find('.at-repeater-block-title').html($(this).val());
		});

		$(this).removeClass('.at-repeater-new-item');
	});
	*/
}




function loadUidFields() {
	$( ".at-field-uuid" ).each(function() {
		if ( $(this).val() == "") {
			$(this).val(uniqid());
		}
	});
}

function loadTooltips() {
	$('.has-tip').frosty({

	});
}

function loadImagePicker() {
	$('.image-picker').imagepicker({
		hide_select: true,
		show_label: true
	});
}

function loadGroups() {
	$( ".group-selector" ).each(function() {
		var group_name = ( $( this ).attr('id') );
		$('.group-' + group_name).hide();
		$(this).on('change', function(e) {
			e.preventDefault();
			if ( $(this).is(':checkbox') ) {
				var group_val = $(this).attr('checked');
			} else {
				var group_val = $(this).find('option:selected').val();
			}
			$('.group-' + group_name).hide().each(function() {
				var group_val_options = $(this).data('group-value');
				if ( group_val_options.indexOf( group_val ) != -1 ) {
					$(this).show();
				}
			});

		}).change();
	});
}

function loadFontPicker() {
	$('.at-font-select').fontPicker();
}

function loadColorPicker(){
	if ($('.at-color-iris').length>0){
		$('.at-color-iris').wpColorPicker();
	}
}

function loadDatePicker(){
	$('.at-date').each( function() {
		var $this  = $(this),
				format = $this.attr('rel');
		$this.datepicker( { showButtonPanel: true, dateFormat: format } );
	});
}

function loadTimePicker(){
	$('.at-time').each( function() {
		var $this = $(this),
				format   =  $this.attr('rel');
		$this.timepicker( { showSecond: true, timeFormat: format } );
	});
}

/**
 * jQuery iphone style checkbox enable function
 * @since 1.1.5
 */
function fancyCheckbox(){
	$(':checkbox').each(function (){
		var $el = $(this);
		if(! $el.hasClass('no-toggle')){
			$el.FancyCheckbox();
			if ($el.hasClass("conditional_control")){
				$el.on('change', function() {
					var $el = $(this);
					if($el.is(':checked'))
						$el.next().next().show('fast');
					else
						$el.next().next().hide('fast');
				});
			}
		}else{
			if ($el.hasClass("conditional_control")){
				$el.on('change', function() {
					var $el = $(this);
					if($el.is(':checked'))
						$el.next().show('fast');
					else
						$el.next().hide('fast');
				});
			}
		}
	});
}

/**
 * Select 2 enable function
 * @since 1.1.5
 */
function fancySelect(){
	$("select").each(function (){
		if(! $(this).hasClass('no-fancy'))
			$(this).select2();
	});
}

/**
 * remove_image description
 * @since 1.2.2
 * @param  jQuery element object
 * @return void
 */
function remove_image(ele){
	var $el = $(ele);
	var field_id = $el.attr("rel");
	var at_id = $el.prev().prev();
	var at_src = $el.prev();
	var t_button = $el;
	data = {
		action: 'apc_delete_mupload',
		_wpnonce: $('#nonce-delete-mupload_' + field_id).val(),
		field_id: field_id,
		attachment_id: jQuery(at_id).val()
	};

	$(t_button).val("...").attr('disabled','disabled');

	//clear existing image preview and values
	$(at_id).val('');
	$(at_src).val('');
	$(at_id).prev().html('');


	$.getJSON(ajaxurl, data, function(response) {
		if ('success' == response.status){
			$(t_button).val("Upload Image").removeAttr('disabled');
			$(t_button).removeClass('at-delete_image_button').addClass('at-upload_image_button');
			load_images_muploader();
		}else{
			alert(response.message);
		}
	});
}

/**
 * image_upload handle image upload
 * @since 1.2.2
 * @param  jquery element object
 * @return void
 */
function image_upload(ele){
	var $el = $(ele);
	formfield1 = $el.prev();
	formfield2 = $el.prev().prev();
	// Uploading files since WordPress 3.5
	// If the media frame already exists, reopen it.
	if ( file_frame ) {
		file_frame.open();
		return;
	}
	// Create the media frame.
	file_frame = wp.media.frames.file_frame = wp.media({
		title: $el.data( 'uploader_title' ),
		button: {
			text: $el.data( 'uploader_button_text' ),
		},
		multiple: false  // Set to true to allow multiple files to be selected
	});
	// When an image is selected, run a callback.
	file_frame.on( 'select', function() {
		// We set multiple to false so only get one image from the uploader
		attachment = file_frame.state().get('selection').first().toJSON();
		// Do something with attachment.id and/or attachment.url here
		jQuery(formfield2).val(attachment.id);
		jQuery(formfield1).val(attachment.url);
		load_images_muploader();
	});
	// Finally, open the modal
	file_frame.open();
}

/**
 * load_images_muploader
 * load images after upload
 * @return void
 */
function load_images_muploader(){
	$(".mupload_img_holder").each(function(i,v){
		if ($(this).next().next().val() != ''){
			if (!$(this).children().size() > 0){
				var h = $(this).attr('data-he');
				var w = $(this).attr('data-wi');
				$(this).append('<img src="' + $(this).next().next().val() + '" />');
				$(this).next().next().next().val("Delete");
				$(this).next().next().next().removeClass('at-upload_image_button').addClass('at-delete_image_button');
			}
		}
	});
}

/*
 * AJAX Functions
 */

/**
 * do_ajax
 *
 * @author Ohad Raz <admin@bainternet.info>
 * @since 0.8
 * @param  string which  (import|export)
 *
 * @return void
 */
function do_ajax_import_export(which){
	before_ajax_import_export(which);
	var group = jQuery("#option_group_name").val();
	var seq_selector = "#apc_" + which + "_nonce";
	var action_selector = "apc_" + which + "_" + group;
	jQuery.ajaxSetup({ cache: false });
	if (which == 'export')
		export_ajax_call(action_selector,group,seq_selector,which);
	if (which == 'import')
		import_ajax_call(action_selector,group,seq_selector,which);
	if (which == 'defaults') {
		jQuery.getJSON(ajaxurl,
				{
					group: group,
					rnd: microtime(false), //hack to avoid request cache
					action: action_selector,
					seq: jQuery(seq_selector).val()
				},
				function(data) {
					if (data && data.code){
						jQuery('#import_code').val(data.code);
						jQuery(".import_status").hide("fast");
					}else{
						alert("Something Went Wrong, try again later");
					}
				}
		);
	}

	jQuery.ajaxSetup({ cache: true });
}

/**
 * export_ajax_call make export ajax call
 *
 * @author Ohad Raz <admin@bainternet.info>
 * @since 0.8
 *
 * @param  string action
 * @param  string group
 * @param  string seq_selector
 * @param  string which
 * @return void
 */
function export_ajax_call(action,group,seq_selector,which){
	jQuery.getJSON(ajaxurl,
			{
				group: group,
				rnd: microtime(false), //hack to avoid request cache
				action: action,
				seq: jQuery(seq_selector).val()
			},
			function(data) {
				if (data){
					export_response(data);
				}else{
					alert("Something Went Wrong, try again later");
				}
				after_ajax_import_export(which);
			}
	);
}

/**
 * import_ajax_call make import ajax call
 *
 * @author Ohad Raz <admin@bainternet.info>
 * @since 0.8
 *
 * @param  string action
 * @param  string group
 * @param  string seq_selector
 * @param  string which
 * @return void
 */
function import_ajax_call(action,group,seq_selector,which){
	jQuery.post(ajaxurl,
			{
				group: group,
				rnd: microtime(false), //hack to avoid request cache
				action: action,
				seq: jQuery(seq_selector).val(),
				imp: jQuery("#import_code").val(),
			},
			function(data) {
				if (data){
					import_response(data);
				}else{
					alert("Something Went Wrong, try again later");
				}
				after_ajax_import_export(which);
			},
			"json"
	);
}

/**
 * before_ajax_import_export
 *
 * @author Ohad Raz <admin@bainternet.info>
 * @since 0.8
 * @param  string which  (import|export)
 *
 * @return void
 */
function before_ajax_import_export(which){
	jQuery(".import_status").hide("fast");
	jQuery(".export_status").hide("fast");
	jQuery(".export_results").html('').removeClass('alert-success').hide();
	jQuery(".import_results").html('').removeClass('alert-success').hide();
	if (which == 'import')
		jQuery(".import_status").show("fast");
	if (which == 'export')
		jQuery(".export_status").show("fast");
	if (which == 'defaults')
		jQuery(".import_status").show("fast");
}

/**
 * after_ajax_import_export
 *
 * @author Ohad Raz <admin@bainternet.info>
 * @since 0.8
 * @param  string which  (import|export)
 *
 * @return void
 */
function after_ajax_import_export(which){
	if (which == 'import')
		jQuery(".import_status").hide("fast");
	else
		jQuery(".export_status").hide("fast");
}

/**
 * export_reponse
 *
 * @author Ohad Raz <admin@bainternet.info>
 * @since 0.8
 * @param  json data ajax response
 * @return void
 */
function export_response(data){
	if (data.code)
		jQuery('#export_code').val(data.code);
	if (data.nonce)
		jQuery("#apc_export_nonce").val(data.nonce);
	if(data.err)
		jQuery(".export_results").html(data.err).show('slow');
}

/**
 * import_reponse
 *
 * @author Ohad Raz <admin@bainternet.info>
 * @since 0.8
 * @param  json data ajax response
 *
 * @return void
 */
function import_response(data){
	if (data.nonce)
		jQuery("#apc_import_nonce").val(data.nonce);
	if(data.err)
		jQuery(".import_results").html(data.err);
	if (data.success)
		jQuery(".import_results").html(data.success).addClass('alert-success').show('slow');
}

/*
Update Fonts Database
 */
function do_ajax_fonts_refresh() {
	var group = jQuery("#option_group_name").val();
	var seq_selector = "#web_fonts_refresh_nonce";
	jQuery("#web_fonts_refresh_results").hide("fast");
	jQuery("#web_fonts_refresh_status").show("fast");
	jQuery.post(ajaxurl,
			{
				group: group,
				rnd: microtime(false), //hack to avoid request cache
				action: 'apc_fonts_' + group,
				seq: jQuery(seq_selector).val()
			},
			function(data) {
				if (data){
					if (data.nonce)
						jQuery("#web_fonts_refresh_nonce").val(data.nonce);
					if(data.err)
						jQuery("#web_fonts_refresh_results").html(data.err);
					if (data.success)
						jQuery("#web_fonts_refresh_results").html(data.success).addClass('alert-success').show('slow');
				}else{
					alert("Error refreshing fonts database. Try again later.");
				}
				jQuery("#web_fonts_refresh_status").hide("fast");

			},
			"json"
	);
}


function do_ajax_clean_form(buttonObj) {

	var origcode_textarea = buttonObj.prev('.at-textarea');
	var group = jQuery("#option_group_name").val();
	var seq_selector = "#clean_form_nonce";

	jQuery.post(ajaxurl,
			{
				group: group,
				rnd: microtime(false), //hack to avoid request cache
				action: 'clean_form_' + group,
				seq: jQuery(seq_selector).val(),
				code: origcode_textarea.val()
			},
			function(data) {
				if (data) {

					if (data.nonce)
						jQuery(seq_selector).val(data.nonce);

					if (data.code)
						var new_code = jQuery('<div />').text(data.code).html();
						origcode_textarea.val(data.code);
				}
			},
			"json"
	);
}

/********************
 * Helper Functions *
 *******************/

/**
 * refresh_page
 * @since 0.8
 * @return void
 */
function refresh_page(){

	location.reload();
}

/**
 * microtime used as hack to avoid ajax cache
 *
 * @author Ohad Raz <admin@bainternet.info>
 * @since 0.8
 * @param  boolean get_as_float
 *
 * @return microtime as int or float
 */
function microtime(get_as_float) {
	var now = new Date().getTime() / 1000;
	var s = parseInt(now);
	return (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + " " + s;
}

/**
 * Helper Function
 *
 * Get Query string value by name.
 *
 * @since 1.0
 */
function get_query_var( name ) {

	var match = RegExp('[?&]' + name + '=([^&#]*)').exec(location.href);
	return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
}