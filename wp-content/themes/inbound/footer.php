<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package inbound
 */
?>
</div>
<?php inbound_sub_footer(); ?>
</main>
<!--End of Main Content-->

<?php inbound_footer(); ?>

<?php if (inbound_is_layout('boxed')) : ?></div><!--End of Wrapper--><?php endif; ?>

<a href="#" class="scrollup"><span><?php esc_html_e('Scroll up', 'inbound'); ?></span></a>
<?php do_action ( 'inbound_before_wp_footer' ); ?>
</div>
<?php wp_footer(); ?>


</body>

</html>