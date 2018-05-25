<?php
/*
 * Installation and Initial Setup
 */

add_action( 'admin_head', 'inbound_setup_admin_head' );
add_action( 'admin_menu', 'inbound_setup_pages');
function inbound_setup_pages() {
	$welcome_page_title = esc_html__( 'Inbound for WordPress Setup', 'inbound' );
	$setup = add_theme_page( $welcome_page_title, $welcome_page_title, 'manage_options', 'inbound-setup', 'inbound_setup_page' );
	$setup = add_theme_page( $welcome_page_title, $welcome_page_title, 'manage_options', 'inbound-setup-upload', 'inbound_upload_page' );
	$setup = add_theme_page( $welcome_page_title, $welcome_page_title, 'manage_options', 'inbound-welcome', 'inbound_welcome_page' );
	$setup = add_theme_page( $welcome_page_title, $welcome_page_title, 'manage_options', 'inbound-finish', 'inbound_finish_page' );
	$setup = add_theme_page( $welcome_page_title, $welcome_page_title, 'manage_options', 'inbound-plugins', 'inbound_plugin_page' );
}

function inbound_setup_admin_head() {
	remove_submenu_page( 'themes.php', 'inbound-setup' );
	remove_submenu_page( 'themes.php', 'inbound-setup-upload' );
	remove_submenu_page( 'themes.php', 'inbound-welcome' );
	remove_submenu_page( 'themes.php', 'inbound-finish' );
	remove_submenu_page( 'themes.php', 'inbound-plugins' );
}

function inbound_plugins_active() {
	// SiteOrigin Page Builder
	if ( ! function_exists( 'siteorigin_panels_render')) {
		return false;
	}

	// Inbound Core Feature Pack
	if ( ! defined ('INBOUND_FEATURE_PACK') ) {
		return false;
	}

	return true;
}

function inbound_welcome_page() {
	if ( ! inbound_plugins_active() ) {
		inbound_plugin_page();
		return;
	}

	wp_enqueue_style( 'inbound-setup-css', SR_ADMIN_URL . '/css/setup.css' );
	?>
	<div class="wrap about-wrap" id="setup-container">
		<div class="changelog">
			<h1><?php esc_html_e( 'Welcome to Inbound for WordPress', 'inbound' ); ?></h1>
			<h2><?php esc_html_e( 'Before you can use your site with Inbound, we need to set up a few things.', 'inbound' ); ?></h2>
		</div>
		<div id="setup-header">
			<ul>
				<li class="setup-active"><?php esc_html_e('Welcome', 'inbound'); ?></li>
				<li><?php esc_html_e('Select Template', 'inbound'); ?></li>
				<li><?php esc_html_e('Finish', 'inbound'); ?></li>
			</ul>
			<?php if ( ! inbound_hide_support_links() ) : ?><a href="<?php echo esc_url( SR_SUPPORT_URL ); ?>" target="_blank"><?php esc_html_e('Need Help? Get assistance!', 'inbound'); ?></a><?php endif; ?>
		</div>
		<div id="setup-welcome">
			<div id="setup-theme">
				<h3><?php esc_html_e('Installation & Getting Started', 'inbound'); ?></h3>
				<p>
					<?php esc_html_e('Thank you for installing Inbound for WordPress. The theme has been successfully activated, but before you can use it, we need to set up some defaults to make the theme look just the way you want it. This will only take seconds.', 'inbound'); ?>
				</p>
				<a href="<?php echo esc_url( add_query_arg(array('page'=>'inbound-setup', 'from' => 'welcome'), admin_url('themes.php') ) );?>" class="button button-primary button-hero" id="start-setup"><?php esc_html_e( "Start setup", 'inbound' ); ?></a>
				<p><a href="<?php echo esc_url( add_query_arg(array('page'=>'inbound-finish', 'from' => 'welcome', 'skip' => 1), admin_url('themes.php') ) );?>" id="skip-setup"><?php esc_html_e( "Skip setup", 'inbound' ); ?></a></p>
			</div>
			<?php if ( ! inbound_support_message_html() ) : ?>
				<div id="theme-support">
					<h4><?php esc_html_e('Customer support is just a click away', 'inbound'); ?></h4>
					<p><?php esc_html_e('If at any time you need any assistance, please contact customer support via email or live chat. Please note that we do not provide any customer support through the themeforest comments section though.', 'inbound'); ?></p>
					<a href="<?php echo esc_url( SR_SUPPORT_URL ); ?>" target="_blank"><?php esc_html_e( 'Get Support from ShapingRain.com', 'inbound'); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}


function inbound_plugin_page() {
	wp_enqueue_style( 'inbound-setup-css', SR_ADMIN_URL . '/css/setup.css' );
	?>
	<div class="wrap about-wrap" id="setup-container">
		<div class="changelog">
			<h1><?php esc_html_e( 'Welcome to Inbound for WordPress', 'inbound' ); ?></h1>
			<h2><?php esc_html_e( 'Before you can use your site with Inbound, we need to set up a few things.', 'inbound' ); ?></h2>
		</div>
		<div id="setup-header">
			<ul>
				<li class="setup-active"><?php esc_html_e('Welcome', 'inbound'); ?></li>
				<li><?php esc_html_e('Select Template', 'inbound'); ?></li>
				<li><?php esc_html_e('Finish', 'inbound'); ?></li>
			</ul>
			<?php if ( ! inbound_hide_support_links() ) : ?><a href="<?php echo esc_url( SR_SUPPORT_URL ); ?>" target="_blank"><?php esc_html_e('Need Help? Get assistance!', 'inbound'); ?></a><?php endif; ?>
		</div>
		<div id="setup-welcome">
			<div id="setup-theme">
				<h3><?php esc_html_e('Installation & Getting Started: Prerequisites', 'inbound'); ?></h3>
				<p>
					<?php echo inbound_esc_html( __('Thank you for installing Inbound for WordPress. This theme <strong>requires the SiteOrigin Page Builder</strong> plug-in for many of its features and in order to use our starter templates and the widgets that ship with the theme.  The theme also <strong>requires core plug-ins</strong> which ship with the theme and need to be installed and activated as well before the theme can be used.', 'inbound') ); ?>
				</p>
				<a href="<?php echo esc_url( add_query_arg(array('page'=>'tgmpa-install-plugins'), admin_url('themes.php') ) );?>" class="button button-primary button-hero" id="start-setup"><?php esc_html_e( "Install required plug-ins", 'inbound' ); ?></a>
			</div>
			<?php if ( ! inbound_support_message_html() ) : ?>
				<div id="theme-support">
					<h4><?php esc_html_e('Customer support is just a click away', 'inbound'); ?></h4>
					<p><?php esc_html_e('If at any time you need any assistance, please contact customer support via email or live chat. Please note that we do not provide any customer support through the themeforest comments section though.', 'inbound'); ?></p>
					<a href="<?php echo esc_url( SR_SUPPORT_URL ); ?>" target="_blank"><?php esc_html_e( 'Get Support from ShapingRain.com', 'inbound'); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}


function inbound_finish_page() {
	wp_enqueue_style( 'inbound-setup-css', SR_ADMIN_URL . '/css/setup.css' );

	if (!empty($_REQUEST['skip']) && $_REQUEST['skip'] == 1) {
		$profile = json_decode ( inbound_file_read_contents( get_template_directory() . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'profile.json' ), true);
		$new_profile = array(
				'post_type'         => 'profile',
				'post_title'        => $profile['post_data']['post_title'],
				'post_name'         => $profile['post_data']['post_name'],
				'post_content'      => $profile['post_data']['post_content'],
				'comment_status'    => 'closed',
				'ping_status'       => 'closed',
				'post_status'       => 'private',
		);
		$new_profile_id = wp_insert_post( $new_profile );

		// Update meta fields that contain the actual options
		foreach ($profile['post_meta'] as $field => $content) {
			if (substr($field, 0, 1) != "_") { // insert field only if not hidden
				$content = $content[0];

				// if serialized, unserialize
				if (is_serialized( $content )) {
					$content = unserialize( $content );
				}

				update_post_meta( $new_profile_id, $field, $content );
			}
		}

		inbound_save_options(
				array (
						'default_profile' => $new_profile_id,
						'default_profile_blog' => 0,
						'default_profile_woocommerce' => 0
				)
		);

		inbound_save_options(
				array (
						'skipped_setup' => 1
				),
				'inbound_options_global'
		);
	}

	?>
	<div class="wrap about-wrap" id="setup-container">
		<div class="changelog">
			<h1><?php esc_html_e( 'Welcome to Inbound for WordPress', 'inbound' ); ?></h1>
			<h2><?php esc_html_e( 'Congratulations! You are now ready to use the theme.', 'inbound' ); ?></h2>
		</div>
		<div id="setup-header">
			<ul>
				<li><?php esc_html_e('Welcome', 'inbound'); ?></li>
				<li><?php esc_html_e('Select Template', 'inbound'); ?></li>
				<li class="setup-active"><?php esc_html_e('Finish', 'inbound'); ?></li>
			</ul>
			<?php if ( ! inbound_hide_support_links() ) : ?><a href="<?php echo esc_url( SR_SUPPORT_URL ); ?>" target="_blank"><?php esc_html_e('Need Help? Get assistance!', 'inbound'); ?></a><?php endif; ?>
		</div>
		<div id="setup-finish">
			<div id="setup-done">
				<h3><?php esc_html_e('Everything is ready to go', 'inbound'); ?></h3>
				<p>
					<?php esc_html_e('The theme is now ready to be used. You can start editing contents and customizing the template. The user guide that ships with the theme is available to guide you through the user interface.', 'inbound'); ?>
				</p>
				<a href="<?php echo esc_url( home_url( '/' ) ) ?>" target="_blank"><?php esc_html_e( "Explore your site", 'inbound' ); ?></a>
			</div>
			<?php if ( ! inbound_support_message_html() ) : ?>
				<div id="theme-support">
					<h4><?php esc_html_e('Customer support is just a click away', 'inbound'); ?></h4>
					<p><?php esc_html_e('If at any time you need any assistance, please contact customer support via email or live chat. Please note that we do not provide any customer support through the themeforest comments section though.', 'inbound'); ?></p>
					<a href="<?php echo esc_url( SR_SUPPORT_URL ); ?>" target="_blank"><?php esc_html_e( 'Get Support from ShapingRain.com', 'inbound'); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php
}


function inbound_setup_page() {
	if ( ! inbound_plugins_active() ) {
		inbound_plugin_page();
		return;
	}

	wp_enqueue_style( 'Admin_Page_Class', SR_ADMIN_URL . '/css/admin_page_class.css' );
	wp_enqueue_style( 'inbound-setup-css', SR_ADMIN_URL . '/css/setup.css' );
	wp_enqueue_script( 'inbound-image-picker', SR_ADMIN_URL . '/js/image-picker.min.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'inbound-overlay', SR_ADMIN_URL . '/js/loading-overlay.min.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'inbound-tether', SR_ADMIN_URL . '/js/tether.min.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'inbound-drop', SR_ADMIN_URL . '/js/drop.min.js', array( 'jquery', 'inbound-tether' ), null, true );
	wp_enqueue_script( 'inbound-frosty', SR_ADMIN_URL . '/js/frosty.min.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'inbound-setup', SR_ADMIN_URL . '/js/setup.js', array( 'jquery', 'inbound-image-picker', 'inbound-overlay', 'inbound-tether', 'inbound-drop' ), null, true );

	if ( inbound_option_global( 'finished_setup' ) ) {
		$checked_make_front = '';
		$checked_make_default = '';
	} else {
		$checked_make_front = ' checked="checked"';
		$checked_make_default = ' checked="checked"';
	}

	$import = new inbound_import();

	if (isset($_GET['local'])) {
		$packages = $import->get_available_templates( true );
	} else {
		$packages = $import->get_available_templates();
	}
	?>
	<?php wp_nonce_field( 'inbound-setup-nonce' ); ?>
	<div class="wrap about-wrap" id="setup-container">
		<div class="changelog">
			<h1><?php esc_html_e( 'Welcome to Inbound for WordPress', 'inbound' ); ?></h1>
			<h2><?php esc_html_e( 'Before you can use your site with Inbound, we need to set up a few things.', 'inbound' ); ?></h2>
		</div>
		<div id="setup-error" class="setup-block" style="display: none;">
			<h3><?php esc_html_e('An unexpected error has occured', 'inbound'); ?></h3>
			<p id="setup-error-message"></p>
			<?php if ( ! inbound_hide_support_links() ) : ?>
				<p>
					<a href="<?php echo esc_url( SR_SUPPORT_URL ); ?>" class="button button-primary button-hero" target="_blank"><?php esc_html_e( "Ask customer support for help", 'inbound' ); ?></a>
				</p>
			<?php endif; ?>
		</div>
		<div id="setup-header">
			<ul>
				<li><?php esc_html_e('Welcome', 'inbound'); ?></li>
				<li class="setup-active"><?php esc_html_e('Select Template', 'inbound'); ?></li>
				<li><?php esc_html_e('Finish', 'inbound'); ?></li>
			</ul>
			<?php if ( ! inbound_hide_support_links() ) : ?><a href="<?php echo esc_url( SR_SUPPORT_URL ); ?>" target="_blank"><?php esc_html_e('Need Help? Get assistance!', 'inbound'); ?></a><?php endif; ?>
		</div>
		<div id="setup-template">
			<div class="section">
				<h3><?php esc_html_e('Select your starting template', 'inbound'); ?> <span class="label-desc has-tip tip-right" title="<?php  esc_attr_e('This is where you select how you would like the theme to look like. This is meant as a starting point, a design from which you can develop your own. You can always come back here and try other designs, too.', 'inbound'); ?>">?</span></h3>
				<div class="select-theme-demo-template">
					<?php if ( !empty ($packages) && $packages != -2 ) : ?>
						<select name="theme-demo-template" class="at-select image-picker show-labels show-html" id="theme-demo-template">
							<?php foreach ($packages as $package) : ?>
								<option data-img-src="<?php echo $package['preview_thumbnail']; ?>" data-img-label="<?php echo esc_attr($package['title']); ?>" value="<?php echo $package['folder']; ?>"><?php echo $package['title']; ?></option>
							<?php endforeach; ?>
						</select>
					<?php else : ?>
						<?php if ( $packages == -2 ) : ?>
							<p><?php echo sprintf (  inbound_esc_html ( __( 'The theme\'s local repository directory (<em>%s</em>) does not exist or cannot be accessed by the theme. This directory is normally created automatically during the theme\'s setup, but may have been accidentally deleted, or file permissions may have been changed. Consider following the theme\'s troubleshooting guide or contact customer support via support@shapingrain.com for assistance.', 'inbound' ) ), $import->upload_dir ); ?></p>
						<?php else : ?>
							<p><?php esc_html_e('There are no packages in the repository to install.', 'inbound'); ?></p>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( !empty ($packages) && $packages != -2 ) : ?>
				<div class="section">
					<h3><?php esc_html_e('Additional Options', 'inbound'); ?></h3>
					<p>
						<label for="make_front"><input name="make_front" type="checkbox" id="make_front" value="0"<?php echo $checked_make_front; ?>> <?php esc_html_e( 'Set up this template as your static front page.', 'inbound' ); ?> <span class="label-desc has-tip tip-right" title="<?php  esc_attr_e('When this option is checked, the theme will import the selected template and make the newly imported page your front page. You can change your front page later in your WordPress settings.', 'inbound'); ?>">?</span></label>
					</p>
					<p>
						<label for="make_default"><input name="make_default" type="checkbox" id="make_default" value="0"<?php echo $checked_make_default; ?>> <?php esc_html_e( 'Set the profile associated with this template as your site default.', 'inbound' ); ?> <span class="label-desc has-tip tip-right" title="<?php  esc_attr_e('When this option is checked, the theme will make the profile associated with the selected template your site default profile. That way all pages will use the same basic design, e.g. colors, header settings etc.', 'inbound'); ?>">?</span></label>
					</p>
					<?php if ( inbound_option('support_options_dev_mode') ) : ?>
						<p>
							<label for="skip_images"><input name="skip_images" type="checkbox" id="skip_images" value="0"> <?php esc_html_e( 'Skip import of images associated with this template.', 'inbound' ); ?> <span class="label-desc has-tip tip-right" title="<?php  esc_attr_e('If this option is checked, the template import tool will not import any images attached to the template.', 'inbound'); ?>">?</span></label>
						</p>
					<?php endif; ?>
				</div>
				<div class="section">
					<p>
						<a href="javascript:void(0);" class="button button-primary button-hero" id="start-template-setup"><?php esc_html_e( "Proceed with import", 'inbound' ); ?></a>
					</p>
				</div>
			<?php endif; ?>
		</div>
		<?php if (isset($_GET['local'])) : ?>
			<div id="setup-secondary-options" class="section">
				<a href="<?php echo esc_url( add_query_arg(array('page'=>'inbound-setup-upload'), admin_url('themes.php') ) );?>"><?php esc_html_e( 'Upload template package', 'inbound' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg(array('page'=>'inbound-setup'), admin_url('themes.php') ) );?>"><?php esc_html_e( 'Use remote repository', 'inbound' ); ?></a>
			</div>
		<?php else : ?>
			<div id="setup-secondary-options" class="section">
				<a href="<?php echo esc_url( add_query_arg(array('page'=>'inbound-setup', 'local'=>'1'), admin_url('themes.php') ) );?>"><?php esc_html_e( 'Use local repository', 'inbound' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg(array('page'=>'inbound-setup', 'refresh'=>'1'), admin_url('themes.php') ) );?>"><?php esc_html_e( 'Refresh repository', 'inbound' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg(array('page'=>'inbound-setup-upload'), admin_url('themes.php') ) );?>"><?php esc_html_e( 'Upload template package', 'inbound' ); ?></a>
			</div>
		<?php endif; ?>

		<ul class="setup-mini-links">
			<li><a href="#" id="setup-console-toggle"><?php esc_html_e('Show Log Console', 'inbound'); ?></a></li>
		</ul>

		<div id="setup-console" style="display: none;">
			<?php printf ( esc_html__('Started: %s', 'inbound'), date('M j G:i:s T Y') ); ?>
		</div>
	</div>
	<?php
}


function inbound_upload_page() {
	if ( !empty ( $_FILES['package']['tmp_name'] ) ) {
		inbound_upload_setup();
	} else {
		inbound_upload_form();
	}
}

function inbound_upload_setup() {
	$message = false;

	/* Prevent further execution if user is not logged in */
	if ( !is_user_logged_in() ) {
		$this->throw_ajax_error( esc_html__( 'You need to be logged in to upload a theme package.', 'inbound' ) );
	}

	/* Prevent further execution if current user does not have permission to edit theme options */
	if ( !current_user_can('edit_theme_options' ) ) {
		$this->throw_ajax_error( esc_html__( 'The current WordPress user does not have permissions required to edit theme options.', 'inbound' ) );
	}

	if ( ! function_exists( 'wp_handle_upload' ) ) {
		inbound_require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}

	if ( wp_verify_nonce($_POST['_wpnonce'], 'inbound-setup-upload-nonce') ) {
		$file = $_FILES['package']['tmp_name'];
		$name = $_FILES['package']['name'];
		$path_parts = pathinfo( strtolower($name) );
		if ($path_parts['extension'] == "zip") {
			$import = new inbound_import();

			WP_Filesystem();
			$unzipfile = unzip_file( $file, $import->upload_dir);
			unlink($file);

			$template_dir = trailingslashit ( $import->upload_dir ) . $path_parts['filename'];

			if ( $unzipfile && file_exists( $template_dir ) ) {
				$message = esc_html__('The package has been successfully uploaded and extracted into your repository.', 'inbound');
				inbound_upload_form( false, $message, true );
				return;
			} else {
				$message =  esc_html__('There was an error unzipping the file. To resolve this issue, please check your upload directory permissions and refer to all the solutions offered in the troubleshooting guide that ships with the theme. Contact customer support via support@shapingrain.com if you need any help.', 'inbound');
				inbound_upload_form ( true, $message, false );
				return;
			}
		}
		else {
			$message      = esc_html__( "This file is not a valid zip archive.", 'inbound' );
		}
	} else {
		$message      = esc_html__( "There was a problem authenticating you while submitting the form.", 'inbound' );
	}
	inbound_upload_form ( true, $message );
}

function inbound_upload_form( $done = false, $message = false, $success = false ) {
	wp_enqueue_style( 'inbound-setup-css', SR_ADMIN_URL . '/css/setup.css' );
	?>
	<div class="wrap about-wrap" id="setup-container">
		<div class="changelog">
			<h1><?php esc_html_e( 'Welcome to Inbound for WordPress', 'inbound' ); ?></h1>
			<h2><?php esc_html_e( 'Before you can use your site with Inbound, we need to set up a few things.', 'inbound' ); ?></h2>
		</div>
		<div id="setup-upload">
			<h3><?php esc_html_e('Upload Template Package', 'inbound'); ?></h3>
			<?php
			if ($message) {
				if ( $success ) {
					echo '<div class="inbound-setup-error registration-updated message-updated"><p>' . $message . '</p></div>';
				} else {
					echo '<div class="inbound-setup-error registration-updated message-error"><p>' . $message . '</p></div>';
				}
			}
			?>
			<?php if ( $success == false ) : ?>
				<p>
					<?php esc_html_e('Please select a template package in .zip format. Only Inbound for WordPress packages are supported.', 'inbound'); ?>
				</p>
				<form method="post" enctype="multipart/form-data" id="inbound-setup-upload-form">
					<?php wp_nonce_field( 'inbound-setup-upload-nonce' ); ?>
					<p class="package-upload">
						<input type="file" name="package">
					</p>
					<p class="package-submit">
						<input type="submit" value="<?php esc_html_e( "Upload package", 'inbound' ); ?>" name="submit" class="button button-primary button-hero">
					</p>
				</form>
			<?php else : ?>
				<p>
					<a href="<?php echo esc_url( admin_url('themes.php?page=inbound-setup&local=1') ); ?>" class="button button-primary button-hero"><?php esc_html_e('View local repository', 'inbound') ;?></a>
				</p>
			<?php endif; ?>
		</div>
		<div class="return-to-dashboard">
			<?php if ( ! inbound_hide_support_links() ) : ?><a href="<?php echo esc_url( SR_SUPPORT_URL ); ?>" target="_blank"><?php esc_html_e('Need Help? Get assistance!', 'inbound'); ?></a><?php endif; ?>
		</div>
	</div>
	<?php
}

function inbound_welcome() {
	if ( ! inbound_option_global('setup_init_done') ) {
		// theme has never before been activated, so we need to run initial setup routine
		inbound_setup_initial_setup();
		wp_redirect(admin_url("themes.php?page=inbound-welcome"));
	} else {
		// we've already done the initial setup, so we are taking the user right to the finish page
		wp_redirect(admin_url("themes.php?page=inbound-finish"));
	}
}
add_action('after_switch_theme', 'inbound_welcome');

/*
 * Initial Setup, Options Import
 */

function inbound_setup_initial_setup() {
	// import set of theme options
	inbound_import_raw_options();

	// import default widgets if sidebar areas are empty
	if ( !is_active_sidebar('inbound-footer-widgets') ) {
		$import = new inbound_import();
		$import->import_widget_data();
	}

	// re-init some options
	inbound_save_options(
			array (
					'default_profile' => 0,
					'default_profile_blog' => 0,
					'default_profile_woocommerce' => 0
			)
	);

	// mark setup as completed
	inbound_save_options( array ( 'setup_init_done' => true	), 'inbound_options_global' );
}

function inbound_import_raw_options() {
	$options = json_decode( inbound_file_read_contents( get_template_directory() . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'options.json' ), true );
	update_option( 'inbound_options', $options );
}


/*
 * Setup Functions
 */

add_action( 'wp_ajax_inbound_setup_images', 'inbound_setup_init' );
add_action( 'wp_ajax_inbound_setup_profiles', 'inbound_setup_init' );
add_action( 'wp_ajax_inbound_setup_modals', 'inbound_setup_init' );
add_action( 'wp_ajax_inbound_setup_banners', 'inbound_setup_init' );
add_action( 'wp_ajax_inbound_setup_pages', 'inbound_setup_init' );
add_action( 'wp_ajax_inbound_setup_finalize', 'inbound_setup_init' );


add_action( 'wp_ajax_inbound_setup_init', 'inbound_setup_init' );
function inbound_setup_init() {

	/* Attempt to prevent timeouts */
	if ( ! ini_get( 'safe_mode' ) ) {
		@set_time_limit( 0 );
	}


	$log_message = '';
	if ( is_user_logged_in() && is_admin() ) {

		if (! wp_verify_nonce($_POST['_ajax_nonce'], 'inbound-setup-nonce')) { /* Check authorization */
			/* output error message and exit from setup procedure */
			$newNonce = wp_create_nonce('inbound-setup-nonce');
			$response['type'] = "error";
			$response['message'] = esc_html__('A security breach was detected.', 'inbound');
			$response['logmessage'] = '';
			$response['run_again'] = false;
			$response['newNonce'] = $newNonce;
			$response = json_encode($response);
			echo $response;
			wp_die();
		} else {
			/* execute setup sequence and generate output */
			$import = new inbound_import();

			/* change filesystem access mode */
			add_filter('filesystem_method', create_function('$a', 'return "direct";' ));

			/* Prevent further execution if current user does not have permission to edit theme options */
			if ( !current_user_can('edit_theme_options' ) ) {
				$import->throw_ajax_error( esc_html__( 'The current WordPress user does not have permissions required to edit theme options.', 'inbound' ) );
			}

			$folder = $_POST['folder'];

			if ( !preg_match('/^[a-zA-Z0-9-_]+$/', $folder) ) {
				$import->throw_ajax_error( esc_html__( 'The selected folder does not look like a valid option and execution of this command has been denied for security reasons. Please contact support if you think this happened in error.', 'inbound' ) );
			}

			$action = $_POST['action'];

			$type = false;

			// some checks prior to import process to start
			if ($action == 'inbound_setup_init') {
				$id = $import->get_transient_id( $folder );
				delete_transient( $id );

				if ( ! wp_mkdir_p( $import->upload_dir ) ) {
					$import->throw_ajax_error( esc_html__( 'Your WordPress upload directory does not exist or cannot be written to. Please check your WordPress directory permissions and try again.', 'inbound' ) );
				}

				if ( ! inbound_option_global('setup_init_done') ) {
					// theme has never before been activated, so we need to run initial setup routine
					inbound_setup_initial_setup();
				}
			}


			// download package file, if necessary
			if ($action == 'inbound_setup_init') {
				// prepare for installation

				// download package or use local copy
				if ( file_exists( $import->upload_dir . DIRECTORY_SEPARATOR . $folder )) {
					// this folder already exists
				} else {
					$download_url = $import->repository_manifest_url . $folder . '.zip';
					// this folder does not exist, so we need to download and unzip the package
					$package_file = download_url( $download_url, 60 );

					if ( empty ( $package_file->errors ) ) { // if download successful
						WP_Filesystem();
						$unzip_package = unzip_file( $package_file, $import->upload_dir);
						unlink( $package_file );
					} // if download successful
					else {
						$errors = $package_file->get_error_messages();
						$import->throw_ajax_error( sprintf (  inbound_esc_html ( __( 'Download of package file (%1$s) from repository failed: %2$s.<br />If this issue persists, consider following the user guide\'s instructions for installing a template package manually or contact customer support for assistance.', 'inbound' ) ), $download_url, implode(" ", $errors ) ) );
					}
				}

				// check manifest, compare versions
				$manifest = $import->get_manifest_by_type( $folder, 'page' );
				if ( $manifest ) {
					$min_version = '1.0.0';
					if ( !empty ( $manifest['min_version'] ) )
						$min_version = $manifest['min_version'];

					if ( $import->version_compare( $import->current_theme_version(), $min_version) >= 0) {
						// everything's fine, the current theme version supports this package
					}
					else {
						$import->throw_ajax_error( esc_html__( 'The installed theme does not support this package. Please update the theme to its latest version.', 'inbound' ) . ' (' . $min_version . ' >= ' . $import->current_theme_version() . ')' );
					}

				} else {
					$import->throw_ajax_error( esc_html__( 'Unable to read package manifest. Extraction of the package may have failed due to a lack of file permissions, or the package file was corrupted. Delete the package folder and try again.', 'inbound' ) . ' (' . $folder . ')' );
				}

			}

			// import images first
			if ($action == 'inbound_setup_images')
				$type = 'image';

			// import profiles
			if ($action == 'inbound_setup_profiles')
				$type = 'profile';

			// import modals
			if ($action == 'inbound_setup_modals')
				$type = 'modal';

			// import banners
			if ($action == 'inbound_setup_banners')
				$type = 'banner';

			// import pages
			if ($action == 'inbound_setup_pages')
				$type = 'page';

			// finalize import
			$redirect = '';
			if ($action == 'inbound_setup_finalize') {
				$new_options = array();

				/*
				 * Import button styles associated with template
				 */
				if ( file_exists( $import->upload_dir . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . 'button_styles.json' ) ) {
					$new_button_styles = json_decode( inbound_file_read_contents($import->upload_dir . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . 'button_styles.json'), true );
					$old_button_styles = inbound_option( 'global_button_styles' );

					// only proceed if there is actually a button style to import
					$has_new_button_styles = false;
					if ( is_array ( $new_button_styles ) && count ( $new_button_styles) > 0 ) {
						if ( is_array ( $old_button_styles ) && count ( $old_button_styles ) > 0 ) {
							// button styles exist, so let's check if the ones to add already exist, and if no add them
							foreach ( $new_button_styles as $new_style_uuid => $new_button_style ) {
								if ( ! $import->array_subkey_value_match ( $old_button_styles, 'uid', $new_style_uuid ) ) {
									$old_button_styles[] = $new_button_style;
									$has_new_button_styles = true;
								}
							}
						}
						else {
							// no button styles defined, so just add the new styles
							$old_button_styles = array();
							foreach ( $new_button_styles as $new_button_style ) {
								$old_button_styles[] = $new_button_style;
								$has_new_button_styles = true;
							}
						}

						if ( $has_new_button_styles ) {
							$new_options['global_button_styles'] = $old_button_styles;
						}
					}
				}


				/*
				 * Import forms associated with template
				 */
				if ( file_exists( $import->upload_dir . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . 'forms.json' ) ) {
					$new_forms = json_decode( inbound_file_read_contents($import->upload_dir . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . 'forms.json'), true );
					$old_forms = inbound_option( 'forms' );

					// only proceed if there is actually a button style to import
					$has_new_forms = false;
					if ( is_array ( $new_forms ) && count ( $new_forms ) > 0 ) {
						if ( is_array ( $old_forms ) && count ( $old_forms ) > 0 ) {
							// forms exist, so let's check if the ones to add already exist, and if no add them
							foreach ( $new_forms as $new_form_uuid => $new_form ) {
								if ( ! $import->array_subkey_value_match ( $old_forms, 'form_uid', $new_form_uuid ) ) {
									$old_forms[] = $new_form;
									$has_new_forms = true;
								}
							}
						}
						else {
							// no forms defined, so just add the new styles
							$old_forms = array();
							foreach ( $new_forms as $new_form ) {
								$old_forms[] = $new_form;
								$has_new_forms = true;
							}
						}

						if ( $has_new_forms ) {
							$new_options['forms'] = $old_forms;
						}
					}
				}


				// save button styles and forms
				if ( count ( $new_options ) > 0 ) {
					inbound_save_options( $new_options );
				}

				// mark setup status as finished
				inbound_save_options(
						array (
								'finished_setup' => 1
						),
						'inbound_options_global'
				);
				$redirect = admin_url('themes.php?page=inbound-finish&from=setup');
			}

			// if something (a package component) should actually be imported now
			$run_again = false;
			if ($type) {
				$log_message = date('M j G:i:s T Y') . ": " . esc_html__('Finished step:', 'inbound') . " " . ucfirst($type) . "\n";
				$result = $import->import_package ( $folder, $type );
				$log_message .= $result['message'];
				$run_again = $result['run_again'];
				$log_message = nl2br($log_message);
			}

			// prepare text response
			$newNonce = wp_create_nonce('inbound-setup-nonce');
			$response['type'] = "success";
			$response['message'] = esc_html__('Initialization successful.', 'inbound');
			$response['logmessage'] = $log_message;
			$response['run_again'] = $run_again;
			$response['newNonce'] = $newNonce;
			$response['redirect'] = $redirect;
			$response = json_encode($response);
			echo $response;
			wp_die();
		}
	}
}

/*
 * Import Class
 */

class inbound_import {
	public $upload_dir = '';
	public $upload_url = '';

	private $image_manifest = array();

	public $image_mapping = array();
	public $modal_mapping = array();

	public $repository_manifest_url = 'http://d1kz3q8ez01zmf.cloudfront.net/inbound/'; // faster, cloud-based repository

	private $widgets_with_images = array (
		'Inbound_Bio_Block_Widget' => 'avatar',
		'Inbound_Comparison_Widget' => array ('image_1', 'image_2'),
		'Inbound_Image_Widget' => 'image',
		'Inbound_Gallery_Widget' => 'ids', // multiple
		'Inbound_Feature_Media_Widget' => 'image',
		'Inbound_Icon_Block_Widget' => 'custom_icon',
		'Inbound_Portfolio_Item_Widget' => 'image',
		'Inbound_Slider_Widget' => 'items', // multiple
		'Inbound_Testimonial_Widget' => 'avatar',
	);

	private $widgets_with_modals = array (
		'Inbound_Link_Widget' => 'modal',
		'Inbound_Button_Widget' => 'modal',
		'Inbound_Image_Widget' => 'modal',
		'Inbound_CTA_Box_Widget' => 'modal',
		'Inbound_Pricing_Block_Widget' => 'modal',
		'Inbound_Split_Button_Widget' => array( 'modal_left', 'modal_right' )
	);

	private $profile_option_with_images = array (
		'sr_inbound_body_background_image',
		'sr_inbound_background_image',
		'sr_inbound_header_logo_image',
		'sr_inbound_header_logo_image_secondary'
	);

	function __construct() {
		$inbound_upload_dir_tmp = wp_upload_dir();
		$inbound_upload_dir = $inbound_upload_dir_tmp['basedir'] . DIRECTORY_SEPARATOR . 'inbound_exports';
		$inbound_upload_url = $inbound_upload_dir_tmp['baseurl'] . '/' . 'inbound_exports';

		$this->upload_dir = $inbound_upload_dir;
		$this->upload_url = $inbound_upload_url;
	}

	public function get_random_string( $length = 10 ) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public function array_subkey_value_match( $var, $field, $val ) {
			foreach($var as $key => $row)
			{
				if ( isset ( $row[$field] ) && $row[$field] == $val )
					return true;
			}
			return false;
	}

	public function throw_ajax_error( $message ) {
		$newNonce = wp_create_nonce('inbound-setup-nonce');
		$response['type'] = "error";
		$response['message'] = $message;
		$response['logmessage'] = '';
		$response['run_again'] = false;
		$response['newNonce'] = $newNonce;
		$response = json_encode($response);
		echo $response;
		wp_die();
	}

	public function get_available_templates( $local = false ) {
		if ( isset($_GET['refresh'] ) ) {
			delete_transient( 'inbound_setup_template_repo' );
		}

		if ($local) {
			$page_id = 0;
			$page_file = false;
			$packages = false;

			$weeds = array('.', '..');

			if ( ! file_exists ( $this->upload_dir ) ) { // directory does not exist
				if ( wp_mkdir_p ( $this->upload_dir ) ) {
					// all good, directory could be created
				} else {
					return -2;
				}
			}

			$directories = array_diff( scandir ( $this->upload_dir ), $weeds );

			if ( ! empty ( $directories ) ) {
				foreach( $directories as $dir )
				{
					if ( is_dir ( $this->upload_dir . DIRECTORY_SEPARATOR . $dir ) )
					{
						if ( file_exists($this->upload_dir . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'manifest.json') ) { // if directory contains a package manifest
							$manifest = json_decode( inbound_file_read_contents($this->upload_dir . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'manifest.json'), true );

							if ( ! empty ($manifest ) ) {
								foreach ( $manifest as $id => $meta ) {
									if ( $meta['type'] == "page" ) {
										$page_id   = $id;
										$page_file = $meta['file'];
										break;
									}
								}
							}

							if ($page_file) {
								$page = json_decode( inbound_file_read_contents( $this->upload_dir . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $page_file ), true ) ;
								$packages[] = array (
										'folder' => $dir,
										'title' => $page['post_data']['post_title'],
										'id' => $page_id,
										'file' => $page_file,
										'local' => true,
										'preview_thumbnail' => $this->upload_url . "/" . $dir . "/preview.jpg"
								);
							}
						}
					}
				}
			}
		} else {
			$manifest = array();
			if ( ! $packages = get_transient( 'inbound_setup_template_repo' ) ) { // check if local copy available
				$rnd = $this->get_random_string(5);
				$manifest_file = download_url( $this->repository_manifest_url . 'index.json?rnd=' . $rnd , 30 );
				if ( empty ( $manifest_file->errors ) ) { // if download successful
					$packages = json_decode( inbound_file_read_contents( $manifest_file ), true );
					set_transient( 'inbound_setup_template_repo', $packages, 14400 );
					unlink( $manifest_file );
				} // if download successfully
				else { // download was not successful
					$packages = $this->get_available_templates( true ); // try and use local directory structure
				}
			} // local copy not available
			else { // local copy is available
				// we already have a local transient copy - do nothing
			}

			// if packages are not based on directory structure, check if local directory copy exists
			if ( !empty ( $packages ) && $packages != -2 ) { // if we actually have packages to analyze
				$x=0;
				foreach ($packages as $package) {
					$dir = $package['folder'];
					if(is_dir($this->upload_dir . DIRECTORY_SEPARATOR . $dir)) { // directory exists locally
						$packages[$x]['local'] = true;
						$packages[$x]['preview_thumbnail'] = $this->upload_url . "/" . $dir . "/preview.jpg";

					} else { // directory does not exist, this must be downloaded first
						$packages[$x]['local'] = false;
						$packages[$x]['preview_thumbnail'] = $this->repository_manifest_url . $dir . "-preview.jpg";
					}
					$x++;
				}
			}
		} // not only local
		return $packages;
	}

	public function current_theme_version () {
		$my_theme = wp_get_theme();
		return $my_theme->get( 'Version' );
	}

	function version_compare($ver1, $ver2, $operator = null)
	{
		$p = '#(\.0+)+($|-)#';
		$ver1 = preg_replace($p, '', $ver1);
		$ver2 = preg_replace($p, '', $ver2);
		return isset($operator) ?
				version_compare($ver1, $ver2, $operator) :
				version_compare($ver1, $ver2);
	}

	public function get_manifest ( $folder ) {
		$manifest = json_decode( inbound_file_read_contents($this->upload_dir . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . 'manifest.json'), true );
		return $manifest;
	}

	public function get_manifest_by_type ( $folder, $type ) {
		$manifest = json_decode( inbound_file_read_contents($this->upload_dir . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . 'manifest.json'), true );
		if ( $manifest ) {
			foreach ( $manifest as $id => $options ) {
				if ( !empty ($options['type'] ) ) {
					if ( $type == $options{'type'} ) {
						return $options;
					}
				}
			}
			echo 'there!';
		}
		return false;
	}

	public function get_transient_id ( $folder ) {
		return "inbound_setup_" . str_replace ("-", "", filter_var( $folder, FILTER_SANITIZE_NUMBER_INT ) );
	}

	private function get_temp_defaults () {
		$defaults = array(
				'new_banner_id' => 0,
				'new_profile_id' => 0,
				'new_page_id' => 0,
		);
		return $defaults;
	}

	private function get_temp( $folder ) {
		$defaults = $this->get_temp_defaults();
		$id = $this->get_transient_id( $folder );
		$db_settings = get_transient( $id );
		if ($db_settings) {
			return array_merge($defaults, $db_settings);
		} else {
			return $defaults;
		}
	}

	private function set_temp ( $folder, $new_settings ) {
		$id = $this->get_transient_id( $folder );

		$db_settings = get_transient( $id );
		$defaults = $this->get_temp_defaults();
		if ($db_settings) {
			$settings = array_merge($defaults, $db_settings);
		} else {
			$settings = $defaults;
		}

		$settings = array_merge($settings, $new_settings);

		set_transient( $id, $settings, 360 );
		return $settings;
	}

	public function import_package( $folder, $type ) {
		$inbound_upload_dir     = $this->upload_dir . "/" . $folder;

		$settings = $this->get_temp( $folder ); // retrieve settings for this import

		/* New object IDs */
		$new_profile_id = $settings['new_profile_id'];
		$new_banner_id  = $settings['new_banner_id'];

		$modals = array();

		$log_message = '';
		$run_again = false;

		$manifest = $this->get_manifest( $folder );

		/*
		 * Parse Manifest
		 */
		foreach ($manifest as $id => $meta) {
			if ($meta['type'] == "page") {
				$page_id = $id;
				$page_file = $meta['file'];
			}
			elseif ($meta['type'] == "profile") {
				$profile_id = $id;
				$profile_file = $meta['file'];
			}
			elseif ($meta['type'] == "banner") {
				$banner_id = $id;
				$banner_file = $meta['file'];
			}
			elseif ($meta['type'] == "modal") {
				$modals[] = array(
						'id' => $id,
						'file' => $meta['file']
				);
			}
		}

		/* Parse Modal Window Mapping File */
		if (file_exists( $inbound_upload_dir . DIRECTORY_SEPARATOR . 'modal_mapping.json' )) {
			$this->modal_mapping = json_decode ( inbound_file_read_contents( $inbound_upload_dir . DIRECTORY_SEPARATOR . 'modal_mapping.json' ), true );
		}

		/*
		 * Parse Image Manifest
		 */
		$image_manifest = json_decode ( inbound_file_read_contents( $inbound_upload_dir . DIRECTORY_SEPARATOR . 'image_manifest.json' ), true );

		$image_mapping_tmp = get_transient( "inboundimg_" . md5 ( $inbound_upload_dir ) );

		if ( $image_mapping_tmp ) {
			$this->image_mapping = json_decode ( $image_mapping_tmp , true );
		} else {
			$this->image_mapping = array();
		}

		/*
		 * Import all required images before importing pages, profiles and other resources
		 */
		if ( $type == "image" && count ($image_manifest) > 0 ) {

			$max_index = count ( $image_manifest ) - 1;
			$last = $_REQUEST['last'];

			$image = array_slice ( $image_manifest, $last, 1 );
			$image = $image[0];

			if ( ! array_key_exists ($image['original_id'], $this->image_mapping) ) { // process only if image has not already been imported previously
				$filename = trailingslashit ( $inbound_upload_dir ) . $image['file'];
				$parent_post_id = 0;
				$filetype = wp_check_filetype( $filename, null );

				$attachment = array(
					'guid'           => $image['file'],
					'post_mime_type' => $filetype['type'],
					'post_title'     => $image['wp_meta']['title'],
					'post_content'   => '',
					'post_status'    => 'inherit'
				);

				// attach image to WP uploads database
				$attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

				// generate meta data
				inbound_require_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
				wp_update_attachment_metadata( $attach_id, $attach_data );

				$this->image_mapping[$image['original_id']] = array(
					'url' => wp_get_attachment_url( $attach_id ),
					'original_id' => $image['original_id'],
					'new_id' => $attach_id
				);
				$log_message .= inbound_esc_html( sprintf ( __('Imported image with new ID %s.', 'inbound'), $attach_id ) )  . "\n";
			} else {
				$log_message .= inbound_esc_html( sprintf ( __('Skipped image with original ID %s (already exists).', 'inbound'), $image['original_id'] ) )  . "\n";
			}

			$next = $last + 1;
			if ( $next <= $max_index ) {
				$run_again = $next;
			}

			set_transient( "inboundimg_" . md5 ( $inbound_upload_dir ), json_encode( $this->image_mapping ), 0 );
		}


		/*
		 * Import associated profile and re-assign images
		 */
		if ($type == "profile") {
			if (isset($profile_file)) {
				$profile = json_decode ( inbound_file_read_contents( $inbound_upload_dir . DIRECTORY_SEPARATOR . $profile_file ), true );

				$new_profile = array(
						'post_type'         => 'profile',
						'post_title'        => $profile['post_data']['post_title'],
						'post_name'         => $profile['post_data']['post_name'],
						'post_content'      => $profile['post_data']['post_content'],
						'comment_status'    => 'closed',
						'ping_status'       => 'closed',
						'post_status'       => 'private',
				);

				// Insert the profile into the database
				$new_profile_id = wp_insert_post( $new_profile );
				$this->set_temp( $folder, array('new_profile_id' => $new_profile_id) );

				// Update meta fields that contain the actual options
				foreach ($profile['post_meta'] as $field => $content) {
					if (substr($field, 0, 1) != "_") { // insert field only if not hidden
						$content = $content[0];

						// if serialized, unserialize
						if (is_serialized( $content )) {
							$content = unserialize( $content );
						}

						// if this option contains an image
						if ( in_array( $field, $this->profile_option_with_images ) ) {
							$old_image_id   = $content['id'];
							$content['id']  = $this->image_mapping[$old_image_id]['new_id'];
							$content['url'] = $this->image_mapping[$old_image_id]['url'];
						}

						update_post_meta( $new_profile_id, $field, $content );
					}
				}

				if ( ! empty ( $_REQUEST['make_default'] ) && $_REQUEST['make_default'] == "true" )  {
					inbound_save_options(
							array (
									'default_profile' => $new_profile_id,
							)
					);
				}

			}
		}

		/*
		 * Import modal windows
		 */
		if ($type == "modal" && !empty ($modals)) {
			foreach ($modals as $modal_data) {
				$modal_file = $modal_data['file'];
				$modal_old_id = $modal_data['id'];
				if ( isset ( $modal_file ) ) {
					$modal = json_decode( inbound_file_read_contents( $inbound_upload_dir . DIRECTORY_SEPARATOR . $modal_file ), true );

					$new_modal = array(
							'post_type'         => 'modal',
							'post_title'        => $modal['post_data']['post_title'],
							'post_name'         => $modal['post_data']['post_name'],
							'post_content'      => $modal['post_data']['post_content'],
							'comment_status'    => 'closed',
							'ping_status'       => 'closed',
							'post_status'       => 'publish',
					);

					// Insert the modal post type into the database
					$new_modal_id = wp_insert_post( $new_modal );
					$this->modal_mapping[$modal_old_id] = array(
							'original_id' => $modal_old_id,
							'new_id' => $new_modal_id
					);

					// Update meta fields that contain the actual options
					foreach ($modal['post_meta'] as $field => $content) {
						if (substr($field, 0, 1) != "_") { // insert field only if not hidden
							$content = $content[0];

							// if serialized, unserialize
							if (is_serialized( $content )) {
								$content = unserialize( $content );
							}

							if ( $field == "panels_data") { // modify panels data to replace references to images
								$content = $this->map_panels_images( $content, $this->image_mapping );
							}
							update_post_meta( $new_modal_id, $field, $content );
						}
					}
				}
			}
			inbound_file_write_contents( $inbound_upload_dir . DIRECTORY_SEPARATOR . 'modal_mapping.json', json_encode( $this->modal_mapping ) );
		}



		/*
		 * Import banner
		 */
		if ($type == "banner") {
			if ( isset ( $banner_file ) ) {
				$banner = json_decode( inbound_file_read_contents( $inbound_upload_dir . DIRECTORY_SEPARATOR . $banner_file ), true );

				$new_banner = array(
						'post_type'         => 'banner',
						'post_title'        => $banner['post_data']['post_title'],
						'post_name'         => $banner['post_data']['post_name'],
						'post_content'      => $banner['post_data']['post_content'],
						'comment_status'    => 'closed',
						'ping_status'       => 'closed',
						'post_status'       => 'publish',
				);

				// Insert the banner into the database
				$new_banner_id = wp_insert_post( $new_banner );
				$this->set_temp( $folder, array('new_banner_id' => $new_banner_id) );

				// Update meta fields that contain the actual options
				foreach ($banner['post_meta'] as $field => $content) {
					if (substr($field, 0, 1) != "_") { // insert field only if not hidden
						$content = $content[0];

						// if serialized, unserialize
						if (is_serialized( $content )) {
							$content = unserialize( $content );
						}

						if ( $field == "panels_data") { // modify panels data to replace references to images and modals
							$content = $this->map_panels_images( $content, $this->image_mapping );
							$content = $this->map_panels_modals( $content, $this->modal_mapping );
						}

						if ( in_array( $field, $this->profile_option_with_images ) ) {
							$old_image_id   = $content['id'];
							$content['id']  = $this->image_mapping[$old_image_id]['new_id'];
							$content['url'] = $this->image_mapping[$old_image_id]['url'];
						}

						update_post_meta( $new_banner_id, $field, $content );
					}
				}
			}
		}


		/*
		 * Import page
		 */
		if ( $type == "page" ) {
			if ( isset ( $page_file ) ) {
				$page = json_decode ( inbound_file_read_contents( $inbound_upload_dir . DIRECTORY_SEPARATOR . $page_file ), true );

				$new_page = array(
						'post_type'         => 'page',
						'post_title'        => $page['post_data']['post_title'],
						'post_name'         => $page['post_data']['post_name'],
						'post_content'      => $page['post_data']['post_content'],
						'comment_status'    => 'closed',
						'ping_status'       => 'closed',
						'post_status'       => 'publish',
				);

				// Insert the page into the database
				$new_page_id = wp_insert_post( $new_page );
				$this->set_temp( $folder, array('new_page_id' => $new_page_id) );

				// Update meta fields that contain the actual options
				foreach ($page['post_meta'] as $field => $content) {
					if (substr($field, 0, 1) != "_") { // insert field only if not hidden
						$content = $content[0];

						// if serialized, unserialize
						if (is_serialized( $content )) {
							$content = unserialize( $content );
						}

						if ( $field == "panels_data") { // modify panels data to replace references to images and modals
							$content = $this->map_panels_images( $content, $this->image_mapping );
							$content = $this->map_panels_modals( $content, $this->modal_mapping );
						}

						if ( $field == "sr_inbound_profile") { // assign newly imported profile
							$content = $new_profile_id;
						}

						if ( $field == "sr_inbound_custom_banner") { // assign newly imported banner
							$content = $new_banner_id;
						}

						update_post_meta( $new_page_id, $field, $content );
					}
				}

				if ( ! empty ( $_REQUEST['make_front'] ) && $_REQUEST['make_front'] == "true" )  {
					update_option( 'page_on_front', $new_page_id );
					update_option( 'show_on_front', 'page' );
				}

			}
		}

		$result = array (
			'message' => $log_message,
			'run_again' => $run_again
		);

		return $result;
	}

	function map_panels_images( $content, $image_mapping )
	{

		// if there are no widgets in this one, return with original content as there are no images to process
		if ( empty($content['widgets']) )
			return $content;

		// process widgets
		foreach ($content['widgets'] as $idx => $options) {
			if ( !empty ( $options['panels_info']['class'] ) ) {
				$class = $options['panels_info']['class'];
				if ( array_key_exists( $class, $this->widgets_with_images ) ) {
					$field_to_map = $this->widgets_with_images[$class];
					$old_image_id = $options[$field_to_map];
					if ( substr_count( $old_image_id, "," ) > 0 ) {  // we have multiple IDs
						$new_image_ids = array();
						$old_image_ids = explode ( ",", $old_image_id );
						foreach ($old_image_ids as $old_image_id) {
							if ( array_key_exists($old_image_id, $image_mapping) ) {
								$new_image_ids[] = $image_mapping[$old_image_id]['new_id'];
							}
						}
						$new_image_id = implode (",", $new_image_ids);
						$content['widgets'][$idx][$field_to_map] = $new_image_id;
					} else { // we have a single ID
						if ( array_key_exists($old_image_id, $image_mapping) ) {
							$new_image_id = $image_mapping[$old_image_id]['new_id'];
							$content['widgets'][$idx][$field_to_map] = $new_image_id;
						}
					}
				}

				if ( isset ( $options['panels_info']['style']['background_image_attachment'] ) ) { // if widget style is set
					$old_image_id = $options['panels_info']['style']['background_image_attachment'];
					if ($old_image_id > 0) {
						if ( array_key_exists($old_image_id, $image_mapping) ) {
							$new_image_id = $image_mapping[ $old_image_id ]['new_id'];
							$content['widgets'][ $idx ]['panels_info']['style']['background_image_attachment'] = $new_image_id;
						}
					}
				}
			}
		}

		// process row styles
		foreach ($content['grids'] as $idx => $options) {
			if ( isset ( $options['style']['background_image_attachment'] ) ) { // if widget style is set
				$old_image_id = $options['style']['background_image_attachment'];
				if ($old_image_id > 0) {
					$new_image_id = $image_mapping[$old_image_id]['new_id'];
					$content['grids'][$idx]['style']['background_image_attachment'] = $new_image_id;
				}
			}
		}
		return $content;
	}

	function map_panels_modals( $content, $modal_mapping )
	{
		if ( empty($content['widgets']) )
			return $content;

		// process widgets
		foreach ($content['widgets'] as $idx => $options) {
			if ( !empty ( $options['panels_info']['class'] ) ) {
				$class = $options['panels_info']['class'];
				if ( array_key_exists( $class, $this->widgets_with_modals ) ) {
					$fields_to_map = $this->widgets_with_modals[$class];

					if ( !is_array( $fields_to_map ) )
						$fields_to_map = array ( $fields_to_map );

					if ( !empty ($fields_to_map ) ) {
						foreach ($fields_to_map as $field_to_map) {
							if ( !empty ( $options[$field_to_map] ) ) {
								$old_modal_id = $options[$field_to_map];
								if ( array_key_exists($old_modal_id, $modal_mapping) ) {
									$new_modal_id = $modal_mapping[$old_modal_id]['new_id'];
									$content['widgets'][$idx][$field_to_map] = $new_modal_id;
								}
							}
						}
					}
				}
			}
		}
		return $content;
	}


	/*
	 * Widget Import Features
	 * Based on "Widget Data" plug-in
	 * Original authors: Voce Communications - Kevin Langley, Sean McCafferty, Mark Parolisi
	 * http://vocecommunications.com
	 * Licensed unter GPLv3
	 */

	/**
	 * Import widgets
	 */
	public static function parse_import_data( $import_array ) {
		$sidebars_data = $import_array[0];
		$widget_data = $import_array[1];
		$current_sidebars = get_option( 'sidebars_widgets' );
		$new_widgets = array( );

		foreach ( $sidebars_data as $import_sidebar => $import_widgets ) :

			foreach ( $import_widgets as $import_widget ) :
				//if the sidebar exists
				if ( isset( $current_sidebars[$import_sidebar] ) ) :
					$title = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
					$index = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
					$current_widget_data = get_option( 'widget_' . $title );
					$new_widget_name = self::get_new_widget_name( $title, $index );
					$new_index = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );

					if ( !empty( $new_widgets[ $title ] ) && is_array( $new_widgets[$title] ) ) {
						while ( array_key_exists( $new_index, $new_widgets[$title] ) ) {
							$new_index++;
						}
					}
					$current_sidebars[$import_sidebar][] = $title . '-' . $new_index;
					if ( array_key_exists( $title, $new_widgets ) ) {
						$new_widgets[$title][$new_index] = $widget_data[$title][$index];
						$multiwidget = $new_widgets[$title]['_multiwidget'];
						unset( $new_widgets[$title]['_multiwidget'] );
						$new_widgets[$title]['_multiwidget'] = $multiwidget;
					} else {
						$current_widget_data[$new_index] = $widget_data[$title][$index];
						$current_multiwidget = $current_widget_data['_multiwidget'];
						$new_multiwidget = isset($widget_data[$title]['_multiwidget']) ? $widget_data[$title]['_multiwidget'] : false;
						$multiwidget = ($current_multiwidget != $new_multiwidget) ? $current_multiwidget : 1;
						unset( $current_widget_data['_multiwidget'] );
						$current_widget_data['_multiwidget'] = $multiwidget;
						$new_widgets[$title] = $current_widget_data;
					}

				endif;
			endforeach;
		endforeach;

		if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
			update_option( 'sidebars_widgets', $current_sidebars );

			foreach ( $new_widgets as $title => $content ) {
				$content = apply_filters( 'widget_data_import', $content, $title );
				update_option( 'widget_' . $title, $content );
			}

			return true;
		}

		return false;
	}

	/**
	 * Parse JSON import file and load
	 */
	public static function import_widget_data( $import_file = false, $widgets_file = false ) {

		if (!$import_file) {
			$import_file = get_template_directory() . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'widgets_data.json';
		}

		if (!$widgets_file) {
			$widgets_file = get_template_directory() . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'widgets.json';
		}

		$json_data = json_decode( inbound_file_read_contents( $import_file ), true );
		$widgets   = json_decode( inbound_file_read_contents( $widgets_file ), true );

		$sidebar_data = $json_data[0];
		$widget_data = $json_data[1];

		$is_sidebars = false;
		$is_widgets  = false;

		if ( is_array( $sidebar_data ) && count ( $sidebar_data ) > 0 ) {
			foreach ( $sidebar_data as $title => $sidebar ) {
				$count = count( $sidebar );
				for ( $i = 0; $i < $count; $i++ ) {
					$widget = array( );
					$widget['type'] = trim( substr( $sidebar[$i], 0, strrpos( $sidebar[$i], '-' ) ) );
					$widget['type-index'] = trim( substr( $sidebar[$i], strrpos( $sidebar[$i], '-' ) + 1 ) );
					if ( !isset( $widgets[$widget['type']][$widget['type-index']] ) ) {
						unset( $sidebar_data[$title][$i] );
					}
				}
				$sidebar_data[$title] = array_values( $sidebar_data[$title] );
			}
		}

		if ( is_array( $widgets ) && count ( $widgets ) > 0 ) {
			foreach ( $widgets as $widget_title => $widget_value ) {
				foreach ( $widget_value as $widget_key => $widget_value ) {
					$widgets[$widget_title][$widget_key] = $widget_data[$widget_title][$widget_key];
				}
			}
		}

		if ( $is_sidebars && $is_widgets ) {
			$sidebar_data = array( array_filter( $sidebar_data ), $widgets );
			self::parse_import_data( $sidebar_data );
		}

	}

	public static function get_new_widget_name( $widget_name, $widget_index ) {
		$current_sidebars = get_option( 'sidebars_widgets' );
		$all_widget_array = array( );
		foreach ( $current_sidebars as $sidebar => $widgets ) {
			if ( !empty( $widgets ) && is_array( $widgets ) && $sidebar != 'wp_inactive_widgets' ) {
				foreach ( $widgets as $widget ) {
					$all_widget_array[] = $widget;
				}
			}
		}
		while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
			$widget_index++;
		}
		$new_widget_name = $widget_name . '-' . $widget_index;
		return $new_widget_name;
	}

} // end of import class


/*
 * Show start setup notice until installation has been completed or skipped
 */
function inbound_setup_admin_error_notice() {
	if ( ! Inbound_Dismissable_Admin_Notices::is_admin_notice_active( 'inbound-setup-notice' ) ) {
		return;
	}

	if ( inbound_option_global( 'skipped_setup' ) || inbound_option_global( 'finished_setup' ) ) {
		// We have skipped or previously completed the setup
	} else {
		if (!empty($_REQUEST['page'])) {
			$page = $_REQUEST['page'];
		} else {
			$page = false;
		}

		$exemptions = array('tgmpa-install-plugins', 'inbound_admin_page');

		if ( substr_count( $page, 'inbound-' ) == 0 && ! in_array ( $page, $exemptions ) ) {
			$welcome = esc_html__('Welcome to Inbound for WordPress!', 'inbound');
			$message = esc_html__('The theme has been installed and activated but it has not been set up. In order to use the theme, please proceed with the setup procedure.', 'inbound');
			$button = esc_html__('Proceed with theme setup', 'inbound');
			echo '<div class="update-nag notice notice-success is-dismissible" data-dismissible="inbound-setup-notice"><h2>' . $welcome . '</h2><p>' . $message . '</p><p><a href="' .  esc_url( admin_url( 'themes.php?page=inbound-welcome' ) ) . '" class="button button-primary button-hero" id="start-setup">' . $button . '</a></p></div>';
		}
	}
}
add_action( 'admin_init', array( 'Inbound_Dismissable_Admin_Notices', 'init' ) );
add_action( 'admin_notices', 'inbound_setup_admin_error_notice' );



