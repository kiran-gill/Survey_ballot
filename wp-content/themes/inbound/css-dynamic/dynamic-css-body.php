<?php
$body_font_color = $tpl_global['body_font_color'];
$f = $tpl_global['fonts'];

$inbound_body = array(
	'boxed' => inbound_is_layout('boxed'),
	'background_mode' => inbound_option('body_background_mode'),
	'color' => inbound_option('color_body_background'),
	'color_wrapper' => inbound_option('color_body_background_boxed_wrapper'),
	'image' => inbound_option('body_background_image')
);
$inbound_body_css = '';

$inbound_background_color = $inbound_body['color'] . ';';

if ( $inbound_body['background_mode'] == 'image-fixed' || $inbound_body['background_mode'] == 'image-tile' || $inbound_body['background_mode'] == 'image-parallax' ) {
	$image = $inbound_body['image'];
	if ($image && is_serialized($image)) {
		$image = unserialize($image);
		if ($inbound_body['background_mode'] == 'image-fixed') {
			$inbound_body_css = 'background:' . $inbound_body['color'] . ' url(' . $image['url'] . ') no-repeat;' . "\n";
			$inbound_body_css .= 'background-attachment:fixed; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover; background-position:center;';
		}
		elseif ($inbound_body['background_mode'] == 'image-parallax') {
			$inbound_body_css = 'background:' . $inbound_body['color'] . ' url(' . $image['url'] . ') no-repeat;' . "\n";
			$inbound_body_css .= '-webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover; background-position:center;';
		}
		else
		{
			$inbound_body_css = 'background:' . $inbound_body['color'] . ' url(' . $image['url'] . ') repeat;';
		}
	} else { // no image set or does not exist
		$inbound_body_css = 'background:' . $inbound_body['color'] . ';';
	}
} elseif ( $inbound_body['background_mode'] == 'solid' ) {
	$inbound_body_css = 'background:' . $inbound_body['color'] . ';';
}
?>

html, body, body.boxed {
<?php echo $inbound_body_css; ?>
}

<?php if ( $inbound_body['boxed'] ) : ?>
	#wrapper {
	background: <?php echo $inbound_body['color_wrapper']; ?>;
	}
<?php endif; ?>

a, .product .price, .order-total .amount, .tagcloud a, .blog_post.right:before, .blog_post.left:before, .inbound-event a:hover {
color: <?php echo inbound_option('color_body_link'); ?>;
}

.post.type-post blockquote, .textwidget blockquote {
border-color: <?php echo inbound_option('color_body_link'); ?>;
}

.button-read-more:after, .inbound-icon-block a:after, .blog-layout-medium .blog_post.teaser .meta-date:after {
background: <?php echo inbound_option('color_body_link'); ?>;
}

.widget_tag_cloud a {
font-size:<?php echo $f->get_font_color('font_body'); ?> !important;
}

a:hover, .header_meta a:hover,  .meta_data a:hover,
.product-options a.add_to_cart_button:hover, .product-options a.view-product-details:hover, .products a h3:hover, #sidebar a:hover {
color: <?php echo inbound_option('color_body_link_hover'); ?>;
}

.blog_post.teaser h3 a:hover, .blog_post.teaser h2 a:hover, .format-quote a:hover blockquote q, .post_format-post-format-link a h2:hover {
color: <?php echo inbound_option('color_body_link_hover'); ?>!important;
}

.inbound-recent-comments .comment-author, .widget_date, .woocommerce-tabs a, .payment_methods p, .woocommerce.widget_layered_nav small
{
color: <?php echo $body_font_color; ?>!important;
}

#header-cart .widget_shopping_cart, #header-cart .quantity, #header-cart .total, #header-cart .variation, #header-cart .widget_shopping_cart_content, #header-cart .widget_shopping_cart_content a, #header-cart h2, #header-cart .woocommerce-result-count,  #header-cart .widget_shopping_cart_content p.total, #header-region .search-form, .inbound-language-switcher ul {
background: <?php echo inbound_option('header_background'); ?>;
color: <?php echo inbound_option('header_text');?>!important;
}

#header-cart li {
border-bottom:1px solid <?php echo inbound_hex2rgba(inbound_option('header_text'), .15) ?>;
}

#header-cart .total {
border-top:3px solid <?php echo inbound_hex2rgba(inbound_option('header_text'), .15) ?>;
}

#header-cart-trigger:before, #header-search-trigger:before, .inbound-language-switcher ul:before {
color: <?php echo inbound_option('header_background'); ?>;
}


.post-thumbnail:before,.format-image a:before, .gallery a:before, .blog-layout-minimal .post h2:before, .format-link a.format-link-content:before, .format-quote a blockquote q:before,
.inbound-recent-posts li.format-link a:before, .inbound-recent-posts li.format-video a:before, .inbound-recent-posts li.format-quote a:before, .inbound-recent-posts li.format-gallery a:before,
.inbound-recent-posts li.format-image a:before, .navigation-posts-num li.active a, .page-numbers li span.current, .page-link span, #post_tags a:hover, .tagcloud a:hover, #header-cart-total,
#sub_footer .tagcloud a:hover, .blog-layout-masonry .format-link a, .blog-layout-masonry .format-link a h3,
.blog-layout-masonry .format-quote a blockquote q, .blog-layout-masonry .format-quote a blockquote .quote_author,
.blog-layout-masonry .format-quote a blockquote,  .blog-layout-grid .format-link a, .blog-layout-grid .format-link a h3,
.blog-layout-grid .format-quote a blockquote q, .blog-layout-grid .format-quote a blockquote .quote_author,
.blog-layout-grid .format-quote a blockquote, .details-on-hover .team-header a, .details-on-hover .team-header .social-icons a,
.details-on-hover .team-header h3, .details-on-hover .team-header h3 a, .details-on-hover p.team-position, .details-on-hover .team-header
{
background: <?php echo inbound_option('color_body_link'); ?> !important;
<?php if (inbound_is_layout('boxed')) : ?>
color: <?php echo inbound_option('color_body_background_boxed_wrapper'); ?> !important;
<?php else : ?>
color: <?php echo inbound_option('color_body_background'); ?> !important;
<?php endif; ?>
}

.team-member.details-on-hover .team-header a, .team-member.details-on-hover h3, .team-member.details-on-hover .social-icons a, .details-on-hover p.team-position  {
	background:none!important;
}

#header-cart-total:after {
color: <?php echo inbound_option('color_body_link'); ?>;
}

#post_tags a:hover, #post_tags a:hover, .tagcloud a:hover {
border: 1px solid <?php echo inbound_option('color_body_link'); ?>;
}

#header-top {
background: <?php echo inbound_option('header_multipurpose_background'); ?>;
color: <?php echo inbound_option('header_multipurpose_text'); ?> !important;
}

#header-top a, #header-top p {
color: <?php echo inbound_option('header_multipurpose_text'); ?> !important;
}

#header-region {
color: <?php echo inbound_option('header_text'); ?>;
}

.has-transparent-menu .animated-header-shrink .shrink #header-region-inner, .has-solid-menu #header-region-inner, .no-banner #header-region-inner
{
background: <?php echo inbound_hex2rgba(inbound_option('header_background'), 1); ?>;
}

#main_navigation .sub-menu  {
background: <?php echo inbound_option('header_background'); ?>;
border-top: 3px solid <?php echo inbound_hex2rgba(inbound_option('menu_link_hover'), 1); ?>;
}

#main_navigation a, #tool-navigation-lower, #tool-navigation-lower a,
.has-transparent-menu #banner #header-region.shrink-sticky ul li a,
.has-transparent-menu #banner #header-region.shrink-sticky #tool-navigation-lower,
.has-transparent-menu #banner #header-region ul li ul.sub-menu li a,
.has-transparent-menu #banner #header-region.shrink-sticky #tool-navigation-lower a,
.has-transparent-menu #banner #header-region #tool-navigation-lower .inbound-language-switcher ul li a {
color: <?php echo inbound_option('header_text'); ?>;
}

#main_navigation a:hover,
.has-transparent-menu #banner #header-region ul li ul.sub-menu li a:hover,
.has-transparent-menu #banner #header-region ul li a:hover,
.sub-menu .current-menu-item a,
.has-transparent-menu #banner #header-region ul li ul.sub-menu li.current-menu-item a,
.has-transparent-menu #banner.animated-header-shrink #header-region ul li a:hover,
.has-solid-menu #banner .sub-menu li.current-menu-item a,
.inbound-language-switcher li.active
{
color: <?php echo inbound_option('menu_link_hover'); ?>;
}

#main_navigation a:hover, #main_navigation a.highlighted, #main_navigation .current-menu-item a,
.has-transparent-menu #banner.animated-header-shrink #header-region ul li a:hover,
.has-transparent-menu #banner.animated-header-shrink #main_navigation .current-menu-item a {
border-bottom: 3px solid <?php echo inbound_hex2rgba(inbound_option('menu_link_hover'), 1); ?>;
}

.has-transparent-menu #banner #header-region ul li a:hover,
.has-transparent-menu #banner #main_navigation .current-menu-item a {
border-bottom: 3px solid <?php echo inbound_hex2rgba(inbound_option('menu_link_hover'), 0); ?>;
}

#sub_footer  {
background: <?php echo inbound_option('subfooter_background'); ?>;
color: <?php echo inbound_option('subfooter_text'); ?>;
}

#sub_footer a, #sub_footer .widget h3, #sub_footer li p span.widget_date {
color: <?php echo inbound_option('subfooter_text'); ?>!important;
}

#page_footer, #page_footer a {
background: <?php echo inbound_option('footer_background'); ?>;
color: <?php echo inbound_option('footer_text'); ?>;
}

@media (max-width: 980px) {
#main_navigation a, #tool-navigation-lower, #tool-navigation-lower a, .has-transparent-menu #banner.animated-header-shrink #header-region ul li a, .has-transparent-menu #banner.animated-header-shrink #header-region #tool-navigation-lower, .has-transparent-menu #banner #header-region ul li ul.sub-menu li a, .has-transparent-menu #banner.animated-header-shrink #header-region #tool-navigation-lower a  {
color: <?php echo inbound_option('header_text');?>;
}
.sm li {
border-bottom:1px solid <?php echo inbound_hex2rgba(inbound_option('header_text'), .3); ?> !important;
}
.has-transparent-menu  #header-region-inner, .has-solid-menu #header-region-inner, .no-banner #header-region-inner
{
background: <?php echo inbound_hex2rgba(inbound_option('header_background'), 1); ?>;
}
#main_navigation a, #tool-navigation-lower, #tool-navigation-lower a,
.has-transparent-menu #banner #header-region-inner ul li a,
.has-transparent-menu #banner #header-region-inner #tool-navigation-lower,
.has-transparent-menu #banner #header-region-inner ul li ul.sub-menu li a,
.has-transparent-menu #banner #header-region-inner #tool-navigation-lower a {
color: <?php echo inbound_option('header_text'); ?> !important;
}
#banner #header-region-inner #logo h1 a {
color: <?php echo $f->get_font_color('font_logo'); ?> !important;
}
.has-transparent-menu #banner #header-region-inner #logo h2 {
color: <?php echo $f->get_font_color('font_logo_tagline'); ?> !important;
}
}