<?php
/*
 * Typography
 */

$f = $tpl_global['fonts'];
?>

body, .header_meta a, .meta_data a, .testimonials footer, .testimonial footer, .quote_author, textarea, input, a .meta-date, .product-options a.add_to_cart_button, .product-options a.view-product-details,
.comment_name a, .comment-meta .comment_name a, select, button, p.buttons a, #banner p.total, #sidebar a, .share-icons-container .share-icons a,
.inbound-event p.event-description, .inbound-event p.event-date
 {
<?php echo $f->get_typography_css( 'font_body' ); ?>
}

#logo h1, #logo a,
.has-transparent-menu #banner.animated-header-shrink #header-region #logo h1,
.has-transparent-menu #banner.animated-header-shrink #header-region #logo h1 a {
<?php echo $f->get_typography_css( 'font_logo' ); ?>
}

#logo h2, .has-transparent-menu #banner.animated-header-shrink #header-region #logo h2 {
<?php echo $f->get_typography_css( 'font_logo_tagline' ); ?>
}

#sidebar h3, #sub_footer h3, h3.widget-title {
<?php echo $f->get_typography_css( 'font_widget_title' ); ?>
}

blockquote, q {
<?php echo $f->get_typography_css( 'font_quote' ); ?>
}

h1 {
<?php echo $f->get_typography_css( 'font_h1' ); ?>
}

h2, .blog_post.teaser h2 a {
<?php echo $f->get_typography_css( 'font_h2' ); ?>
}

h3, .blog_post.teaser h3 a, #post_author h3 a  {
<?php echo $f->get_typography_css( 'font_h3' ); ?>
}

h4, .products .product h3 {
<?php echo $f->get_typography_css( 'font_h4' ); ?>
}

h5 {
<?php echo $f->get_typography_css( 'font_h5' ); ?>
}

h6 {
<?php echo $f->get_typography_css( 'font_h6' ); ?>
}