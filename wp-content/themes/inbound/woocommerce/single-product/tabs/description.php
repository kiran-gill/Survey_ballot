<?php
/**
 * Description tab
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

$heading = esc_html( apply_filters( 'woocommerce_product_description_heading', esc_html__( 'Product Description', 'inbound' ) ) );

?>

<?php if ( $heading ): ?>
  <h3><?php echo inbound_esc_html ( $heading ); ?></h3>
<?php endif; ?>

<?php the_content(); ?>
