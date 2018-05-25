<?php
/**
 * The blog index template file.
 *
 * This is the template used for the blog index (posts) page.
 * It is divided into multiple sub templates for each blog layout.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package inbound
 */

get_header();
$blog_layout = inbound_option('blog_layout', 'list');
?>

<?php get_template_part( 'templates/banners/banner', 'blog' ); ?>
<?php get_template_part( 'templates/blog/standalone/blog', $blog_layout ); ?>


<?php get_footer(); ?>
