<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package inbound
 */

get_header();
$blog_layout = inbound_option('blog_archive_layout', 'list');
?>
<?php get_template_part( 'templates/banners/banner', 'blog' ); ?>
<?php get_template_part( 'templates/blog/standalone/blog', $blog_layout ); ?>

<?php get_footer(); ?>
