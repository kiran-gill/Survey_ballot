/*
JavaScript for Widgets
 */

/*
Initialize Dynamically Added Widgets for WordPress Sidebar Editor
 */
( function( $ ){

	// init for widgets editor
	function initDynamicWidgetControls( widget ) {

		widget.find( '.at-datepicker' ).datepicker({
			dateFormat : 'yy/mm/dd'
		});

		widget.find( '.at-color-iris' ).wpColorPicker( {
			change: _.throttle( function() { // For Customizer
				$(this).trigger( 'change' );
			}, 3000 )
		});

		widget.find( '.at-sortable' ).sortable( {
			'placeholder' : "ui-state-highlight"
		});

		widget.find ( '.widget-groups').each(function() {
			var group = $(this).data('group-first');
			widget.find ('.group').hide();
			widget.find ('.group-' + group).show();
		});

		widget.find ( '.group-selector' ).each(function() {
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

		widget.find ( '.group-tab-link').click(function(e) {
			var group = $(this).data('group-target');
			widget.find ( '.widget-groups li').removeClass('tabs');
			$(this).closest('li').addClass('tabs');
			widget.find ('.group').hide();
			widget.find ('.group-' + group).show();
			widget.find ('.group-selector').each(function() {
				$(this).change();
			});
		});

		widget.find( '.fontawesome-picker' ).fontawesomePicker();
	}

	function onWidgetFormUpdate( event, widget ) {
		initDynamicWidgetControls( widget );
	}

	$( document ).on( 'widget-added widget-updated', onWidgetFormUpdate );

	$( document ).ready( function() {
		$( '#widgets-right .widget' ).each( function () {
			initDynamicWidgetControls( $( this  ) );
		} );

	} );

	// init for page builder
	$(document).on('panelsopen', function(e) {
		var dialog = $(e.target);
		initDynamicWidgetControls(dialog);
	});

}( jQuery ) );

/*
 Social Media Profiles Presets
 */
var social_media_presets = {
	facebook: {
		title: 'Facebook',
		link: 'https://www.facebook.com/username',
		icon: 'fa-facebook',
		color_background: '#415e9b'
	},
	twitter: {
		title: 'Twitter',
		link: 'http://www.twitter.com/username',
		icon: 'fa-twitter',
		color_background: '#1bb2e9'
	},
	googleplus: {
		title: 'Google+',
		link: 'http://google.com/+username',
		icon: 'fa-google-plus',
		color_background: '#d13d2f'
	},
	youtube: {
		title: 'YouTube',
		link: 'https://www.youtube.com/user/username',
		icon: 'fa-youtube',
		color_background: '#de332c'
	},
	linkedin: {
		title: 'LinkedIn',
		link: 'http://www.linkedin.com/in/username/',
		icon: 'fa-linkedin',
		color_background: '#006dc0'
	},
	instagram: {
		title: 'Instagram',
		link: 'http://www.instagram.com/username',
		icon: 'fa-instagram',
		color_background: '#46769e'
	},
	pinterest: {
		title: 'Pinterest',
		link: 'http://www.pinterest.com/username',
		icon: 'fa-pinterest',
		color_background: '#c61118'
	},
	flickr: {
		title: 'Flickr',
		link: 'http://www.flickr.com/photos/username/',
		icon: 'fa-flickr',
		color_background: '#ed1983'
	},
	tumblr: {
		title: 'Tumblr',
		link: 'http://username.tumblr.com/',
		icon: 'fa-tumblr',
		color_background: '#2f4962'
	},
	foursquare: {
		title: 'Foursquare',
		link: 'https://foursquare.com/user/userid',
		icon: 'fa-foursquare',
		color_background: '#2d5be3'
	},
	vimeo: {
		title: 'Vimeo',
		link: 'http://vimeo.com/username',
		icon: 'fa-vimeo-square',
		color_background: '#4daacc'
	},
	lastfm: {
		title: 'last.fm',
		link: 'http://www.last.fm/user/username',
		icon: 'fa-lastfm-square',
		color_background: '#e3152b'
	},
	soundcloud: {
		title: 'Soundcloud',
		link: 'https://soundcloud.com/username',
		icon: 'fa-soundcloud',
		color_background: '#fe4700'
	},
	yelp: {
		title: 'Yelp',
		link: 'http://www.yelp.com/user_details?userid=',
		icon: 'fa-yelp',
		color_background: '#c31202'
	},
	slideshare: {
		title: 'Slideshare',
		link: 'http://www.slideshare.net/username',
		icon: 'fa-slideshare',
		color_background: '#0077b5'
	},
	dribbble: {
		title: 'Dribbble',
		link: 'https://dribbble.com/username',
		icon: 'fa-dribbble',
		color_background: '#f26798'
	},
	behance: {
		title: 'Behance',
		link: 'https://www.behance.net/username',
		icon: 'fa-behance',
		color_background: '#0093fa'
	},
	github: {
		title: 'GitHub',
		link: 'https://github.com/username',
		icon: 'fa-github',
		color_background: '#323131'
	},
	reddit: {
		title: 'reddit',
		link: 'http://www.reddit.com/user/username',
		icon: 'fa-reddit',
		color_background: '#cfe4f9'
	},
	weibo: {
		title: 'Weibo',
		link: 'http://www.weibo.com/u/userid',
		icon: 'fa-weibo',
		color_background: '#d72928'
	},
	deviantart: {
		title: 'DeviantArt',
		link: 'htt://username.deviantart.com',
		icon: 'fa-deviantart',
		color_background: '#009544'
	},
	skype: {
		title: 'Skype',
		link: '',
		icon: 'fa-skype',
		color_background: '#00aff0'
	},
	spotify: {
		title: 'Spotify',
		link: '',
		icon: 'fa-spotify',
		color_background: '#7cc110'
	},
	xing: {
		title: 'Xing',
		link: '',
		icon: 'fa-xing',
		color_background: '#005a5f'
	},
	vine: {
		title: 'Vine',
		link: 'https://vine.co/u/userid',
		icon: 'fa-vine',
		color_background: '#02a379'
	},
	digg: {
		title: 'Digg',
		link: '',
		icon: 'fa-digg',
		color_background: '#1b5891'
	}

};

jQuery(function($) {
	$(document).on('change', '.social-service-select', function (event) {
		var this_service = $(this).val();
		if (this_service != '') {
			var this_block_prefix = $(this).parent().closest('.at-repeater-block').data('field-prefix');

			$.each( social_media_presets[this_service], function( key, value ) {
				var el = $('[name="' + this_block_prefix + '[' + key + ']' + '"]');
				$(el).val(value);
				if ( $(el).hasClass('wp-color-picker') ) {
					$(el).change();
				}
			});


		}
	});
});


/*
Profile Editor Tabs
 */
var profile_tabs = {
	general: ['#inbound_profile_design'],
	header: ['#inbound_profile_header', '#inbound_profile_multipurpose', '#inbound_profile_sub_header'],
	footer: ['#inbound_profile_footer', '#inbound_profile_subfooter'],
	buttons: ['#inbound_profile_navigation', '#inbound_profile_call_to_action'],
	typography: ['#inbound_profile_fonts'],
	social: ['#inbound_profile_social', '#inbound_profile_twitter'],
	advanced: ['#inbound_profile_advanced']
};

jQuery(function($) {
	$('#postbox-container-2 .postbox').show();

	$(document).on('click', '.group-tab', function (event) {
		// select active tab
		$('.group-tab').removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active');

		// handle hide/display metaboxes
		$('#postbox-container-2 .postbox').hide();
		var this_group = $(this).data('group');
		if (this_group == "all") {
			$('#postbox-container-2 .postbox').show();
			$('#slugdiv').hide();
		} else {
			var tabs_to_show = profile_tabs[this_group].join();
			$(tabs_to_show).show();
		}
	});
});

/*
 Image selector for built-in widgets
 */
var file_frame;
jQuery(function($){

	$(document).on('click', 'input.clear-img-widget', function(event){
		event.preventDefault();
		image_field = $('#' + $(this).data('target'));
		image_field.val('');
		preview = image_field.prev().find('.inbound-widget-preview-image');
		$(preview).attr('src', $(this).data('preview' )).addClass('empty');
	});


	$(document).on('click', 'input.select-img-widget', function(event){
		event.preventDefault();

		image_field = $('#' + $(this).data('target'));

		if ( file_frame ) {
			file_frame.open();
			return;
		}

		file_frame = wp.media.frames.file_frame = wp.media({
			title: jQuery( this ).data( 'uploader_title' ),
			button: {
				text: jQuery( this ).data( 'uploader_button_text' )
			},
			multiple: false
		});

		file_frame.on( 'select', function() {
			attachment = file_frame.state().get('selection').first().toJSON();

			url = '';
			if (!!attachment.sizes && !!attachment.sizes.thumbnail ) {
				url = attachment.sizes.thumbnail.url;
			} else {
				url = attachment.url;
			}

			if ( url  != '' ) {
				preview = image_field.prev().find('.inbound-widget-preview-image');
				$(preview).attr('src', url).removeClass('empty');
			}

			image_field.val(attachment.id);
		});
		file_frame.open();
	});
});


/*
 FontAwesome Picker
 adapted from Dashicons Picker by Brad Vincent; http://themergency.com
 */

(function($) {
	$.fn.fontawesomePicker = function() {
		var icons = ['glass', 'music', 'search', 'envelope-o', 'heart', 'star', 'star-o', 'user', 'film', 'th-large', 'th', 'th-list', 'check', 'times', 'search-plus', 'search-minus', 'power-off', 'signal', 'cog', 'trash-o', 'home', 'file-o', 'clock-o', 'road', 'download', 'arrow-circle-o-down', 'arrow-circle-o-up', 'inbox', 'play-circle-o', 'repeat', 'refresh', 'list-alt', 'lock', 'flag', 'headphones', 'volume-off', 'volume-down', 'volume-up', 'qrcode', 'barcode', 'tag', 'tags', 'book', 'bookmark', 'print', 'camera', 'font', 'bold', 'italic', 'text-height', 'text-width', 'align-left', 'align-center', 'align-right', 'align-justify', 'list', 'outdent', 'indent', 'video-camera', 'picture-o', 'pencil', 'map-marker', 'adjust', 'tint', 'pencil-square-o', 'share-square-o', 'check-square-o', 'arrows', 'step-backward', 'fast-backward', 'backward', 'play', 'pause', 'stop', 'forward', 'fast-forward', 'step-forward', 'eject', 'chevron-left', 'chevron-right', 'plus-circle', 'minus-circle', 'times-circle', 'check-circle', 'question-circle', 'info-circle', 'crosshairs', 'times-circle-o', 'check-circle-o', 'ban', 'arrow-left', 'arrow-right', 'arrow-up', 'arrow-down', 'share', 'expand', 'compress', 'plus', 'minus', 'asterisk', 'exclamation-circle', 'gift', 'leaf', 'fire', 'eye', 'eye-slash', 'exclamation-triangle', 'plane', 'calendar', 'random', 'comment', 'magnet', 'chevron-up', 'chevron-down', 'retweet', 'shopping-cart', 'folder', 'folder-open', 'arrows-v', 'arrows-h', 'bar-chart', 'twitter-square', 'facebook-square', 'camera-retro', 'key', 'cogs', 'comments', 'thumbs-o-up', 'thumbs-o-down', 'star-half', 'heart-o', 'sign-out', 'linkedin-square', 'thumb-tack', 'external-link', 'sign-in', 'trophy', 'github-square', 'upload', 'lemon-o', 'phone', 'square-o', 'bookmark-o', 'phone-square', 'twitter', 'facebook', 'github', 'unlock', 'credit-card', 'rss', 'hdd-o', 'bullhorn', 'bell', 'certificate', 'hand-o-right', 'hand-o-left', 'hand-o-up', 'hand-o-down', 'arrow-circle-left', 'arrow-circle-right', 'arrow-circle-up', 'arrow-circle-down', 'globe', 'wrench', 'tasks', 'filter', 'briefcase', 'arrows-alt', 'users', 'link', 'cloud', 'flask', 'scissors', 'files-o', 'paperclip', 'floppy-o', 'square', 'bars', 'list-ul', 'list-ol', 'strikethrough', 'underline', 'table', 'magic', 'truck', 'pinterest', 'pinterest-square', 'google-plus-square', 'google-plus', 'money', 'caret-down', 'caret-up', 'caret-left', 'caret-right', 'columns', 'sort', 'sort-desc', 'sort-asc', 'envelope', 'linkedin', 'undo', 'gavel', 'tachometer', 'comment-o', 'comments-o', 'bolt', 'sitemap', 'umbrella', 'clipboard', 'lightbulb-o', 'exchange', 'cloud-download', 'cloud-upload', 'user-md', 'stethoscope', 'suitcase', 'bell-o', 'coffee', 'cutlery', 'file-text-o', 'building-o', 'hospital-o', 'ambulance', 'medkit', 'fighter-jet', 'beer', 'h-square', 'plus-square', 'angle-double-left', 'angle-double-right', 'angle-double-up', 'angle-double-down', 'angle-left', 'angle-right', 'angle-up', 'angle-down', 'desktop', 'laptop', 'tablet', 'mobile', 'circle-o', 'quote-left', 'quote-right', 'spinner', 'circle', 'reply', 'github-alt', 'folder-o', 'folder-open-o', 'smile-o', 'frown-o', 'meh-o', 'gamepad', 'keyboard-o', 'flag-o', 'flag-checkered', 'terminal', 'code', 'reply-all', 'star-half-o', 'location-arrow', 'crop', 'code-fork', 'chain-broken', 'question', 'info', 'exclamation', 'superscript', 'subscript', 'eraser', 'puzzle-piece', 'microphone', 'microphone-slash', 'shield', 'calendar-o', 'fire-extinguisher', 'rocket', 'maxcdn', 'chevron-circle-left', 'chevron-circle-right', 'chevron-circle-up', 'chevron-circle-down', 'html5', 'css3', 'anchor', 'unlock-alt', 'bullseye', 'ellipsis-h', 'ellipsis-v', 'rss-square', 'play-circle', 'ticket', 'minus-square', 'minus-square-o', 'level-up', 'level-down', 'check-square', 'pencil-square', 'external-link-square', 'share-square', 'compass', 'caret-square-o-down', 'caret-square-o-up', 'caret-square-o-right', 'eur', 'gbp', 'usd', 'inr', 'jpy', 'rub', 'krw', 'btc', 'file', 'file-text', 'sort-alpha-asc', 'sort-alpha-desc', 'sort-amount-asc', 'sort-amount-desc', 'sort-numeric-asc', 'sort-numeric-desc', 'thumbs-up', 'thumbs-down', 'youtube-square', 'youtube', 'xing', 'xing-square', 'youtube-play', 'dropbox', 'stack-overflow', 'instagram', 'flickr', 'adn', 'bitbucket', 'bitbucket-square', 'tumblr', 'tumblr-square', 'long-arrow-down', 'long-arrow-up', 'long-arrow-left', 'long-arrow-right', 'apple', 'windows', 'android', 'linux', 'dribbble', 'skype', 'foursquare', 'trello', 'female', 'male', 'gratipay', 'sun-o', 'moon-o', 'archive', 'bug', 'vk', 'weibo', 'renren', 'pagelines', 'stack-exchange', 'arrow-circle-o-right', 'arrow-circle-o-left', 'caret-square-o-left', 'dot-circle-o', 'wheelchair', 'vimeo-square', 'try', 'plus-square-o', 'space-shuttle', 'slack', 'envelope-square', 'wordpress', 'openid', 'university', 'graduation-cap', 'yahoo', 'google', 'reddit', 'reddit-square', 'stumbleupon-circle', 'stumbleupon', 'delicious', 'digg', 'pied-piper-pp', 'pied-piper-alt', 'drupal', 'joomla', 'language', 'fax', 'building', 'child', 'paw', 'spoon', 'cube', 'cubes', 'behance', 'behance-square', 'steam', 'steam-square', 'recycle', 'car', 'taxi', 'tree', 'spotify', 'deviantart', 'soundcloud', 'database', 'file-pdf-o', 'file-word-o', 'file-excel-o', 'file-powerpoint-o', 'file-image-o', 'file-archive-o', 'file-audio-o', 'file-video-o', 'file-code-o', 'vine', 'codepen', 'jsfiddle', 'life-ring', 'circle-o-notch', 'rebel', 'empire', 'git-square', 'git', 'hacker-news', 'tencent-weibo', 'qq', 'weixin', 'paper-plane', 'paper-plane-o', 'history', 'circle-thin', 'header', 'paragraph', 'sliders', 'share-alt', 'share-alt-square', 'bomb', 'futbol-o', 'tty', 'binoculars', 'plug', 'slideshare', 'twitch', 'yelp', 'newspaper-o', 'wifi', 'calculator', 'paypal', 'google-wallet', 'cc-visa', 'cc-mastercard', 'cc-discover', 'cc-amex', 'cc-paypal', 'cc-stripe', 'bell-slash', 'bell-slash-o', 'trash', 'copyright', 'at', 'eyedropper', 'paint-brush', 'birthday-cake', 'area-chart', 'pie-chart', 'line-chart', 'lastfm', 'lastfm-square', 'toggle-off', 'toggle-on', 'bicycle', 'bus', 'ioxhost', 'angellist', 'cc', 'ils', 'meanpath', 'buysellads', 'connectdevelop', 'dashcube', 'forumbee', 'leanpub', 'sellsy', 'shirtsinbulk', 'simplybuilt', 'skyatlas', 'cart-plus', 'cart-arrow-down', 'diamond', 'ship', 'user-secret', 'motorcycle', 'street-view', 'heartbeat', 'venus', 'mars', 'mercury', 'transgender', 'transgender-alt', 'venus-double', 'mars-double', 'venus-mars', 'mars-stroke', 'mars-stroke-v', 'mars-stroke-h', 'neuter', 'genderless', 'facebook-official', 'pinterest-p', 'whatsapp', 'server', 'user-plus', 'user-times', 'bed', 'viacoin', 'train', 'subway', 'medium', 'y-combinator', 'optin-monster', 'opencart', 'expeditedssl', 'battery-full', 'battery-three-quarters', 'battery-half', 'battery-quarter', 'battery-empty', 'mouse-pointer', 'i-cursor', 'object-group', 'object-ungroup', 'sticky-note', 'sticky-note-o', 'cc-jcb', 'cc-diners-club', 'clone', 'balance-scale', 'hourglass-o', 'hourglass-start', 'hourglass-half', 'hourglass-end', 'hourglass', 'hand-rock-o', 'hand-paper-o', 'hand-scissors-o', 'hand-lizard-o', 'hand-spock-o', 'hand-pointer-o', 'hand-peace-o', 'trademark', 'registered', 'creative-commons', 'gg', 'gg-circle', 'tripadvisor', 'odnoklassniki', 'odnoklassniki-square', 'get-pocket', 'wikipedia-w', 'safari', 'chrome', 'firefox', 'opera', 'internet-explorer', 'television', 'contao', '500px', 'amazon', 'calendar-plus-o', 'calendar-minus-o', 'calendar-times-o', 'calendar-check-o', 'industry', 'map-pin', 'map-signs', 'map-o', 'map', 'commenting', 'commenting-o', 'houzz', 'vimeo', 'black-tie', 'fonticons', 'reddit-alien', 'edge', 'credit-card-alt', 'codiepie', 'modx', 'fort-awesome', 'usb', 'product-hunt', 'mixcloud', 'scribd', 'pause-circle', 'pause-circle-o', 'stop-circle', 'stop-circle-o', 'shopping-bag', 'shopping-basket', 'hashtag', 'bluetooth', 'bluetooth-b', 'percent', 'gitlab', 'wpbeginner', 'wpforms', 'envira', 'universal-access', 'wheelchair-alt', 'question-circle-o', 'blind', 'audio-description', 'volume-control-phone', 'braille', 'assistive-listening-systems', 'american-sign-language-interpreting', 'deaf', 'glide', 'glide-g', 'sign-language', 'low-vision', 'viadeo', 'viadeo-square', 'snapchat', 'snapchat-ghost', 'snapchat-square', 'pied-piper', 'first-order', 'yoast', 'themeisle', 'google-plus-official', 'font-awesome', 'handshake-o', 'envelope-open', 'envelope-open-o', 'linode', 'address-book', 'address-book-o', 'address-card', 'address-card-o', 'user-circle', 'user-circle-o', 'user-o', 'id-badge', 'id-card', 'id-card-o', 'quora', 'free-code-camp', 'telegram', 'thermometer-full', 'thermometer-three-quarters', 'thermometer-half', 'thermometer-quarter', 'thermometer-empty', 'shower', 'bath', 'podcast', 'window-maximize', 'window-minimize', 'window-restore', 'window-close', 'window-close-o', 'bandcamp', 'grav', 'etsy', 'imdb', 'ravelry', 'eercast', 'microchip', 'snowflake-o', 'superpowers', 'wpexplorer', 'meetup'];

		return this.each( function() {

			var $button = $(this);

			$button.unbind().on('click.fontawesomePicker', function( event ) {
				event.preventDefault();
				createPopup($button);
			});

			function createPopup($button) {

				$target = $($button.data('target'));

				$popup = $('<div class="fontawesome-picker-container"> \
						<div class="fontawesome-picker-control" /> \
						<ul class="fontawesome-picker-list" /> \
					</div>')
						.css({
							'top': $button.offset().top,
							'left': $button.offset().left
						});

				var $list = $popup.find('.fontawesome-picker-list');
				for (var i in icons) {
					$list.append('<li data-icon="'+icons[i]+'"><a href="#" title="'+icons[i]+'"><span class="fa fa-'+icons[i]+'"></span></a></li>');
				}
				$('a', $list).click(function(e) {
					e.preventDefault();
					var title = $(this).attr("title");
					$target.val("fa-"+title);
					removePopup();
				});

				var $control = $popup.find('.fontawesome-picker-control');
				$control.html('<a data-direction="back" href="#"><span class="fa fa-arrow-left"></span></a> \
				<input type="text" class="" placeholder="Search" /> \
				<a data-direction="forward" href="#"><span class="fa fa-arrow-right"></span></a>');

				$('a', $control).click(function(e) {
					e.preventDefault();
					if ($(this).data('direction') === 'back') {
						//move last 25 elements to front
						$('li:gt(' + (icons.length - 26) + ')', $list).each(function() {
							$(this).prependTo($list);
						});
					} else {
						//move first 25 elements to the end
						$('li:lt(25)', $list).each(function() {
							$(this).appendTo($list);
						});
					}
				});

				$popup.appendTo('body');


				$('input', $control).focus();

				$('input', $control).on('keyup', function(e) {
					if (e.keyCode == 27) {
						removePopup();
					}

					var search = $(this).val();
					if (search === '') {
						//show all again
						$('li:lt(25)', $list).show();
					} else {
						$('li', $list).each(function() {
							if ($(this).data('icon').toLowerCase().indexOf(search.toLowerCase()) !== -1) {
								$(this).show();
							} else {
								$(this).hide();
							}
						});
					}
				});

				$(document).mouseup(function (e){
					if (!$popup.is(e.target) && $popup.has(e.target).length === 0) {
						removePopup();
					}
				});
			}

			function removePopup(){
				$(".fontawesome-picker-container").remove();
			}
		});
	};

	$(function() {
		$('.fontawesome-picker').fontawesomePicker();
	});

}(jQuery));



/*
 Font Selector
 */
(function($) {
	$.fn.fontPicker = function() {

		return this.each( function() {

			var $button = $(this);

			$button.unbind().on('click.fontPicker', function( event ) {
				event.preventDefault();
				createPopup($button);
			});

			function createPopup($button) {

				var target = $button.data('target');
				var init_slug = $('#' + target + '_face').val();

				if (init_slug == "" || init_slug == undefined) init_slug = "arial";
				var this_item = jQuery.grep(fonts_data, function (font) { return font.slug == init_slug });


				$popup = $('<div class="font-picker-container"> \
						<div class="font-picker-control"> \
						</div> \
						</div>').css({
							'top': $button.offset().top,
							'left': $button.offset().left
						});

				var $control = $popup.find('.font-picker-control');

				$control.append('<select class="font-picker-sizes"></select> \
					<select class="font-picker-list"></select> \
					<select class="font-picker-styles"></select>');


				var $sizes = $popup.find ('.font-picker-sizes');
				var selected = '';
				var selected_size = $('#' + target + '_size').val();
				for (var i=9; i<121; i++)
				{
					selected = '';
					if ( selected_size === i + 'px' ) selected = ' selected="selected"';
					$sizes.append('<option value="' + i + 'px"' + selected + '>' + i + 'px</option>');
				}

				var $styles = $popup.find ('.font-picker-styles');
				if ( this_item[0].variants.length != 0 )
				{
					jQuery.each( this_item[0].variants , function(index, variant) {
						$styles.append('<option value="' + variant + '">' + variant + '</option>');
					});
					$styles.val( $('#' + target + '_weight').val() );
				}
				else {
					$styles.append('<option value="regular">regular</option>' +
							'<option value="bold">bold</option>' +
							'<option value="italic">italic</option>' +
							'<option value="bolditalic">bold italic</option>');
					$styles.val( $('#' + target + '_weight').val() );
				}



				var $list = $popup.find('.font-picker-list');
				jQuery.each( fonts_data , function(index, item) {
					$list.append('<option value="' + item.slug + '">' + item.name + '</option>');
				});
				$list.val( init_slug );

				$list.on ('change', function(e) {
					e.preventDefault();
					var slug = $(this).val();
					this_item = jQuery.grep(fonts_data, function (font) { return font.slug == slug });

					$styles.empty();
					if ( this_item[0].variants.length != 0 )
					{
						jQuery.each( this_item[0].variants , function(index, variant) {
							$styles.append('<option value="' + variant + '">' + variant + '</option>');
						});
						$styles.val( 'regular' );
					}
					else {
						$styles.append('<option value="regular">regular</option>' +
								'<option value="bold">bold</option>' +
								'<option value="italic">italic</option>' +
								'<option value="bolditalic">bold italic</option>');
					}

					//removePopup();
				});

				$control.append('<div class="font-picker-submit-container"><a class="button button-primary font-picker-submit">' + font_picker_select + '</a></div>');

				var $submit = $popup.find('.font-picker-submit');
				$submit.on('click', function(e) {
					e.preventDefault();

					var fo_size = $popup.find ('.font-picker-sizes').val();
					var fo_face = this_item[0].slug;
					var fo_name = this_item[0].name;
					var fo_weight = $popup.find ('.font-picker-styles').val();

					$('#' + target + '_size').val( fo_size );
					$('#' + target + '_face').val( fo_face );
					$('#' + target + '_weight').val( fo_weight );
					$('#' + target + '_preview').val( fo_size + ' ' +  fo_name  + '/' +  fo_weight );
					removePopup();
				});


				$popup.appendTo('body');

				$list.chosen();

				$(document).mouseup(function (e){
					if (!$popup.is(e.target) && $popup.has(e.target).length === 0) {
						removePopup();
					}
				});
			}

			function removePopup(){
				$(".font-picker-container").remove();
			}
		});
	};

	$(function() {
		$('.font-picker').fontPicker();
	});

}(jQuery));





/*
 Gallery Selector
 */

(function($){
	jQuery(document).ready(function($){
		if (wp.media) {
			wp.media.SREditGallery = {

				frame: function( gallery_insert_target ) {

					var gallery_insert_target_id = $(gallery_insert_target).attr('id');
					if ( $(gallery_insert_target).val() == '' ) {
						$(gallery_insert_target).val(' ');
					}

					var selection = this.select( gallery_insert_target );



					this._frame = wp.media({
						id:			'sr-g-' + gallery_insert_target_id,
						frame:     	'post',
						state:     	'gallery-edit',
						library: 	{type: 'image'},
						title:     	wp.media.view.l10n.editGalleryTitle,
						editing:   	true,
						multiple:  	true,
						selection: 	selection
					});

					var controller = this._frame.states.get('gallery-edit');

					// Turn off refreshContent callback so we do not throw a null error on
					// frame.router.get()
					controller.get('selection').on( 'remove reset', function() {
						var controller = wp.media.SREditGallery._frame.states.get('gallery-edit');
						this.off('remove reset', controller.refreshContent, controller);
					});

					// Don't display gallery settings
					controller.frame.on( 'content:create', function() {
						var controller = wp.media.SREditGallery._frame.states.get('gallery-edit');

						controller.frame.off( 'content:render:browse', controller.gallerySettings, controller );
					});

					this._frame.on( 'update',
							function() {
								var controller = wp.media.SREditGallery._frame.states.get('gallery-edit');
								var library = controller.get('library');
								// Need to get all the attachment ids for gallery
								var ids = library.pluck('id');

								$(gallery_insert_target).val(ids);

								controller.off('reset');
								wp.media.editor.remove('content');
							});

					return this._frame;
				},


				// Gets initial gallery-edit images. Function modified from wp.media.gallery.edit
				// From wp-includes/js/media-editor.js.source.html
				select: function( gallery_insert_target ) {

					var shortcode = wp.shortcode.next( 'gallery', '[gallery ids="' + $(gallery_insert_target).val()  + '"]' ),
							defaultPostId = wp.media.gallery.defaults.id,
							attachments, selection;

					// Bail if we didn't match the shortcode or all of the content.
					if ( ! shortcode )
						return;

					// Ignore the rest of the match object.
					shortcode = shortcode.shortcode;

					if ( _.isUndefined( shortcode.get('id') ) && ! _.isUndefined( defaultPostId ) )
						shortcode.set( 'id', defaultPostId );

					attachments = wp.media.gallery.attachments( shortcode );
					selection = new wp.media.model.Selection( attachments.models, {
						props:    attachments.props.toJSON(),
						multiple: true
					});

					selection.gallery = attachments.gallery;

					return selection;
				},

				init: function() {

					$('body').on({ click: function( event )
					{
						event.preventDefault();

						var button = $(this);
						var gallery_insert_target = $(button.data('target'));

						wp.media.SREditGallery.frame( gallery_insert_target ).open();
					}
					}, '.gallery-picker-select');
				}

			};
			$( wp.media.SREditGallery.init );
		}
	});
})(jQuery);

