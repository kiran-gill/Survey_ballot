<?php
/**
 * The blog's banner
 * @package inbound
 */
?>
<?php if ( ! inbound_custom_block('sub_banner_blog') ) : ?>
	<header id="page-header">
		<div class="row<?php if (inbound_option('header_sub_banner_full_width', false)) echo ' row-full-width'; ?>">
			<h1 class="page-title"><?php echo inbound_get_blog_title(); ?></h1>
			<?php if (!is_home()) : ?>
				<div id="breadcrumb">
					<span><?php esc_html_e('You are here:', 'inbound'); ?></span>
					<?php inbound_breadcrumb(); ?>
				</div>
			<?php else : ?>
				<div id="breadcrumb">
					<span><?php esc_html_e('You are here:', 'inbound'); ?></span>
					<ul xmlns:v="http://rdf.data-vocabulary.org/#" class="breadcrumb">
						<li>
							<span typeof="v:Breadcrumb"><?php esc_html_e('Blog', 'inbound'); ?></span>
						</li>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</header>
<?php endif; ?>