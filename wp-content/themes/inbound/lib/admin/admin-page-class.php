<?php
/**
 * ShapingRain.com Admin Page Class
 *
 * (C) Copyright 2014 ShapingRain.com (email: support@shapingrain.com)
 * based on 'Admin Page Class' (C) Copyright 2012 - 2013 Ohad Raz (email: admin@bainternet.info),
 * derived from My-Meta-Box (https://github.com/bainternet/My-Meta-Box script)
 *
 *
 * @license GNU General Public License v3.0 - license.txt
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package Admin Page Class
 */

if ( ! class_exists( 'SR_Admin_Page' ) ) :


	class SR_Admin_Page {

		protected $_saved;
		protected $args;
		protected $option_group;
		public $_fields;
		protected $table = false;
		protected $tab_div = false;
		public $Top_Slug;
		public $_Slug;
		public $_help_tabs;
		public $_div_or_row;
		public $saved_flag = false;
		public $google_fonts = true;
		public $field_types = array();
		public $errors = array();
		public $errors_flag = false;
		public $data_type = 'options';

		/**
		 * Builds a new Page
		 *
		 * @param $args (string|mixed array) -
		 *
		 * Possible keys within $args:
		 *  > menu (array|string) - (string) -> this the name of the parent Top-Level-Menu or a TopPage object to create
		 *                      this page as a sub menu to.
		 *              (array)  -> top - Slug for the New Top level Menu page to create.
		 *  > page_title (string) - The name of this page (good for Top level and sub menu pages)
		 *  > capability (string) (optional) - The capability needed to view the page (good for Top level and sub menu pages)
		 *  > menu_title (string) - The name of the Top-Level-Menu (Top level Only)
		 *  > menu_slug (string) - A unique string identifying your new menu (Top level Only)
		 *  > icon_url (string) (optional) - URL to the icon, decorating the Top-Level-Menu (Top level Only)
		 *  > position (string) (optional) - The position of the Menu in the ACP (Top level Only)
		 *  > option_group (string) (required) - the name of the option to create in the database
		 *
		 *
		 */
		public function __construct( $args ) {
			if ( is_array( $args ) ) {
				if ( isset( $args['option_group'] ) ) {
					$this->option_group = $args['option_group'];
				}
				$this->args = $args;
			} else {
				$array['page_title'] = $args;
				$this->args          = $array;
			}

			//add hooks for export download
			add_action( 'template_redirect', array( $this, 'admin_redirect_download_files' ) );
			add_filter( 'init', array( $this, 'add_query_var_vars' ) );

			// If we are not in admin area exit.
			if ( ! is_admin() ) {
				return;
			}

			//set defaults
			$this->_div_or_row = true;
			$this->saved       = false;
			//store args
			$this->args = $args;
			//google_fonts
			$this->google_fonts = isset( $args['google_fonts'] ) ? true : false;

			//sub $menu
			add_action( 'admin_menu', array( $this, 'AddMenuSubPage' ) );


			// Assign page values to local variables and add it's missed values.
			$this->_Page_Config  = $args;
			$this->_fields       = $this->_Page_Config['fields'];
			$this->_Local_images = ( isset( $args['local_images'] ) ) ? true : false;
			$this->_div_or_row   = ( isset( $args['div_or_row'] ) ) ? $args['div_or_row'] : false;
			$this->add_missed_values();
			if ( isset( $args['use_with_theme'] ) ) {
				if ( $args['use_with_theme'] === true ) {
					$this->SelfPath = get_stylesheet_directory_uri() . '/admin';
				} elseif ( $args['use_with_theme'] === false ) {
					$this->SelfPath = plugins_url( 'admin', plugin_basename( dirname( __FILE__ ) ) );
				} else {
					$this->SelfPath = $args['use_with_theme'];
				}
			} else {
				$this->SelfPath = plugins_url( 'admin', plugin_basename( dirname( __FILE__ ) ) );
			}

			// Load common js, css files
			// Must enqueue for all pages as we need js for the media upload, too.


			//add_action('admin_head', array($this, 'loadScripts'));
			add_filter( 'attribute_escape', array( $this, 'edit_insert_to_post_text' ), 10, 2 );

			// Delete file via Ajax
			add_action( 'wp_ajax_apc_delete_mupload', array( $this, 'wp_ajax_delete_image' ) );
			//import export
			add_action( 'wp_ajax_apc_import_' . $this->option_group, array( $this, 'import' ) );
			add_action( 'wp_ajax_apc_export_' . $this->option_group, array( $this, 'export' ) );
			add_action( 'wp_ajax_apc_defaults_' . $this->option_group, array( $this, 'defaults' ) );
			//fonts db refresh
			add_action( 'wp_ajax_apc_fonts_' . $this->option_group, array( $this, 'fonts_refresh' ) );
			// clean form code
			add_action( 'wp_ajax_clean_form_' . $this->option_group, array( $this, 'clean_form' ) );
		}


		/**
		 * Does all the complicated stuff to build the page
		 *
		 * @since  0.1
		 * @access public
		 */
		public function AddMenuSubPage() {
			$default    = array(
				'capability' => 'edit_themes',
				'menu_title' => $this->args['page_title']
			);
			$this->args = array_merge( $default, $this->args );
			$id   = $this->args['id'];
			$page = add_theme_page( $this->args['page_title'], $this->args['menu_title'], $this->args['capability'], $id, array(
				$this,
				'DisplayPage'
			) );
			if ( $page ) {
				$this->_Slug = $page;
				add_action( 'load-' . $page, array( $this, 'LoadPageHook' ) );
			}
		}

		/**
		 * loads scripts and styles for the page
		 *
		 * @author ohad raz
		 * @since  0.1
		 * @access public
		 */
		public function LoadPageHook() {
			$page = $this->_Slug;
			//help tabs
			add_action( 'admin_head-' . $page, array( $this, 'admin_add_help_tab' ) );
			//scripts and styles
			add_action( 'admin_print_styles', array( $this, 'load_scripts_styles' ) );
			//panel script
			add_action( 'admin_footer-' . $page, array( $this, 'panel_script' ) );
			//add mising scripts
			//add_action('admin_enqueue_scripts',array($this,'Finish'));

			if ( isset( $_POST['action'] ) && $_POST['action'] == 'save' ) {
				$this->save();
				$this->saved_flag = true;
			}
		}

		/**
		 * Creates an unique slug out of the page_title and the current menu_slug
		 *
		 * @since  0.1
		 * @access private
		 */
		private function createSlug() {
			$slug = $this->args['page_title'];
			$slug = strtolower( $slug );
			$slug = str_replace( ' ', '_', $slug );

			return $this->Top_Slug . '_' . $slug;
		}

		public function HelpTab( $args ) {
			$this->_help_tabs[] = $args;
		}

		public function admin_add_help_tab() {
			$screen = get_current_screen();
			/*
     * Check if current screen is My Admin Page
     * Don't add help tab if it's not
     */

			if ( $screen->id != $this->_Slug ) {
				return;
			}
			// Add help_tabs for current screen

			foreach ( (array) $this->_help_tabs as $tab ) {

				$screen->add_help_tab( $tab );
			}
		}

		public function panel_script() {
			?>
			<script>

				/* cookie stuff */
				function setCookie(name, value, days) {
					if (days) {
						var date = new Date();
						date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
						var expires = "; expires=" + date.toGMTString();
					}
					else var expires = "";
					document.cookie = name + "=" + value + expires + "; path=/";
				}

				function getCookie(name) {
					var nameEQ = name + "=";

					var ca = document.cookie.split(";");
					for (var i = 0; i < ca.length; i++) {
						var c = ca[i];
						while (c.charAt(0) == ' ') c = c.substring(1, c.length);
						if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
					}
					return null;
				}

				function eraseCookie(name) {
					setCookie(name, "", -1);
				}

				var last_tab = getCookie("apc_<?php echo $this->option_group; ?>_last");
				if (last_tab) {
					var last_tab = last_tab;
				} else {
					var last_tab = null;
				}

				jQuery(document).ready(function () {

					function show_tab(li) {
						if (!jQuery(li).hasClass("nav-tab-active")) {
							//hide all
							jQuery(".settingstab").hide();
							jQuery(".nav-tab").removeClass("nav-tab-active");
							tab = jQuery(li).attr("href");
							jQuery(li).addClass("nav-tab-active");
							jQuery(tab).show();
							setCookie("apc_<?php echo $this->option_group; ?>_last", tab);
						}
					}

					//hide all
					jQuery(".settingstab").hide();

					//set first_tab as active if no cookie found
					if (last_tab == null) {
						jQuery(".nav-tab-wrapper a:first").addClass("nav-tab-active");
						var tab = jQuery(".nav-tab-wrapper a:first").attr("href");
						jQuery(tab).show();
					} else {
						show_tab(jQuery('[href="' + last_tab + '"]'));
					}

					//bind click on menu action to show the right tab.
					jQuery(".nav-tab-wrapper a").bind("click", function (event) {
						event.preventDefault();
						show_tab(jQuery(this));

					});
					<?php
            if ($this->has_Field('upload')){
              ?>
					function load_images_muploader() {
						jQuery(".mupload_img_holder").each(function (i, v) {
							if (jQuery(this).next().next().val() != "") {
								jQuery(this).append('<img src="' + jQuery(this).next().next().val() + '" />');
								jQuery(this).next().next().next().val("Delete");
								jQuery(this).next().next().next().removeClass("apc_upload_image_button").addClass("apc_delete_image_button");
							}
						});
					}

					//upload button
					var formfield1;
					var formfield2;
					jQuery("#image_button").click(function (e) {
						if (jQuery(this).hasClass("apc_upload_image_button")) {
							formfield1 = jQuery(this).prev();
							formfield2 = jQuery(this).prev().prev();
							tb_show("", "media-upload.php?type=image&amp;apc=insert_file&amp;TB_iframe=true");
							return false;
						} else {
							var field_id = jQuery(this).attr("rel");
							var at_id = jQuery(this).prev().prev();
							var at_src = jQuery(this).prev();
							var t_button = jQuery(this);
							data = {
								action: "apc_delete_mupload",
								_wpnonce: $("#nonce-delete-mupload_" + field_id).val(),
								field_id: field_id,
								attachment_id: jQuery(at_id).val()
							};

							$.post(ajaxurl, data, function (response) {
								if ("success" == response.status) {
									jQuery(t_button).val("Upload Image");
									jQuery(t_button).removeClass("apc_delete_image_button").addClass("apc_upload_image_button");
									//clear html values
									jQuery(at_id).val("");
									jQuery(at_src).val("");
									jQuery(at_id).prev().html("");
									load_images_muploader();
								} else {
									alert(response.message);
								}
							}, "json");
							return false;
						}
					});


					//store old send to editor function
					window.restore_send_to_editor = window.send_to_editor;
					//overwrite send to editor function
					window.send_to_editor = function (html) {
						imgurl = jQuery("img", html).attr("src");
						img_calsses = jQuery("img", html).attr("class").split(" ");
						att_id = "";
						jQuery.each(img_calsses, function (i, val) {
							if (val.indexOf("wp-image") != -1) {
								att_id = val.replace("wp-image-", "");
							}
						});

						jQuery(formfield2).val(att_id);
						jQuery(formfield1).val(imgurl);
						load_images_muploader();
						tb_remove();
						//restore old send to editor function
						window.send_to_editor = window.restore_send_to_editor;
					};
					<?php
        }
        ?>
				});
			</script>
			<?php
		}

		public function edit_insert_to_post_text( $safe_text, $text ) {
			if ( is_admin() && 'Insert into Post' == $safe_text ) {
				if ( isset( $_REQUEST['apc'] ) && 'insert_file' == $_REQUEST['apc'] ) {
					return str_replace( esc_html__( 'Insert into Post', 'inbound' ), esc_html__( 'Use this File', 'inbound' ), $safe_text );
				} else {
					return str_replace( esc_html__( 'Insert into Post', 'inbound' ), esc_html__( 'Use this Image', 'inbound' ), $safe_text );
				}
			}

			return $safe_text;
		}

		public function panel_style() {
			//echo '<style></style>';
		}

		public function DisplayPage() {
			do_action( 'admin_page_class_before_page' );
			if (defined('ADMIN_CLASS_SKIP_PAGE')) return;

			echo '<div class="wrap">';

			echo '<h2>' . esc_html__( 'Inbound Options', 'inbound' );

			if ( defined ('INBOUND_FEATURE_PACK') ) {
				echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=profile' ) ) . '" id="sr-profiles" class="add-new-h2">' . __( 'Profiles', 'inbound' ) . '</a>';
				echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=banner' ) ) . '" id="sr-banners" class="add-new-h2">' . __( 'Banners', 'inbound' ) . '</a>';
			}

			if ( function_exists( 'siteorigin_panels_render' ) ) {
				echo '<a href="' . esc_url ( admin_url( 'themes.php?page=inbound-setup&from=options' ) ) . '" id="sr-banners" class="add-new-h2">' . __( 'Templates', 'inbound' ) . '</a>';
			}

			if ( ! inbound_hide_support_links() ) {
				echo '<a href="' . esc_url ( SR_SUPPORT_URL ) . '" class="add-new-h2" id="sr-support" target="_blank">' . __( 'Customer Support', 'inbound' ) . '</a>';
			}

			if ( inbound_option_global( 'finished_setup' ) ) {
				// do not display this link if setup has been completed already
			} else {
				echo '<a href="' . esc_url( admin_url( 'themes.php?page=inbound-welcome' ) ) . '" class="add-new-h2" id="sr-support">' . __( 'Finish Theme Setup', 'inbound' ) . '</a>';
			}

			echo '</h2>';

			echo '<form method="post" name="' . apply_filters( 'apc_form_name', 'admin_page_class', $this ) . '" class="' . apply_filters( 'apc_form_class', 'admin_page_class', $this ) . '" id="' . apply_filters( 'apc_form_id', 'admin_page_class', $this ) . '" enctype="multipart/form-data">';

			echo '<div class="header_wrap">
              <input type="submit" value="' . esc_attr( __( 'Save Changes', 'inbound' ) ) . '" name="Submit" class="' . apply_filters( 'admin_page_class_submit_class', 'button button-primary menu-save admin-options-save' ) . ' btn">
      		  </div>';

			wp_nonce_field( basename( __FILE__ ), 'SR_Admin_Page_Class_nonce' );


			$saved        = get_option( $this->option_group );
			$this->_saved = $saved;
			$skip         = array(
				'title',
				'paragraph',
				'subtitle',
				'textlabel',
				'TABS',
				'CloseDiv',
				'TABS_Listing',
				'OpenTab',
				'custom',
				'import_export'
			);
			$nocontainer  = array( 'nonce', 'hidden' );

			foreach ( $this->_fields as $field ) {
				if ( ! in_array( $field['type'], $skip ) ) {
					if ( ! $this->table ) {
						if ( $this->_div_or_row ) {
							echo '<table class="form-table">';
							$this->table = true;
						} else {
							echo '<div class="form-table">';
							$this->table = true;
						}
					}
				} else {
					if ( $this->table ) {
						if ( $this->_div_or_row ) {
							echo '</table>';
						} else {
							echo '</div>';
						}
						$this->table = false;
					}
				}
				$data = '';
				if ( isset( $saved[ $field['id'] ] ) ) {
					$data = $saved[ $field['id'] ];
				}
				if ( isset( $field['std'] ) && $data === '' ) {
					$data = $field['std'];
				}

				if ( method_exists( $this, 'show_field_' . $field['type'] ) ) {

					// is this a group selector element?
					if ( isset( $field['group-selector'] ) && $field['group-selector'] == true ) {
						if ( isset( $field['class'] ) ) {
							$field['class'] .= ' group-selector';
						} else {
							$field['class'] = 'group-selector';
						}
					}

					// is this an element that belongs to a group?
					$group_class = '';
					$group_data  = '';
					if ( isset( $field['is-group'] ) && isset( $field['group-value'] ) ) {
						$group_class = ' is-group group-' . $field['is-group'];
						$group_data  = ' data-group="' . $field['is-group'] . '"';
						$group_data .= " data-group-value='" . json_encode( $field['group-value'] ) . "'";
					}

					if ( $this->_div_or_row ) {
						echo '<td>';
					} else {
						echo apply_filters( 'admin_page_class_field_container_open', '<div class="field field-type-' . $field['type'] . $group_class . '"' . $group_data . '>', $field );
					}
					call_user_func( array( $this, 'show_field_' . $field['type'] ), $field, $data );
					if ( $this->_div_or_row ) {
						echo '</td>';
					} else {
						echo apply_filters( 'admin_page_class_field_container_close', '</div>', $field );
					}

				} else {
					switch ( $field['type'] ) {
						case 'TABS':
							echo '<div id="tabs">';
							break;
						case 'CloseDiv':
							$this->tab_div = false;
							echo '</div>';
							break;
						case 'TABS_Listing':
							echo '<h2 class="nav-tab-wrapper">';
							foreach ( $field['links'] as $id => $name ) {
								$extra_classes = strtolower( str_replace( ' ', '-', $name ) ) . ' ' . strtolower( str_replace( ' ', '-', $id ) );
								echo '<a class="nav-tab ' . $extra_classes . '" href="#' . $id . '">' . $name . '</a>';
							}
							echo '</h2>';

							if ( $this->saved_flag ) {
								if ( $this->errors_flag ) {
									echo '<div id="message" class="error below-h2">';

								} else {
									echo '<div id="message" class="updated below-h2">';
								}
								$this->errors = apply_filters( 'admin_page_class_errors', $this->errors, $this );
								if ( is_array( $this->errors ) && count( $this->errors ) > 0 ) {
									$this->errors_flag = true;
									$this->displayErrors();
								} else {
									echo '<p>' . __( 'Settings saved.', 'inbound' ) . '</p>';
								}
								echo '</div>';
							}


							echo '<div class="sections">';
							break;
						case 'OpenTab':
							$this->tab_div = true;
							echo '<div class="settingstab" id="' . $field['id'] . '">';
							do_action( 'admin_page_class_after_tab_open' );
							break;
						case 'title':
							echo '<h2>' . esc_html($field['label']) . '</h2>';
							break;
						case 'subtitle':
							echo '<h3>' . esc_html($field['label']) . '</h3>';
							break;
						case 'paragraph':
							echo '<p>' . inbound_esc_html ( $field['text'] ) . '</p>';
							break;
						case 'repeater':
							do_action( 'admin_page_class_before_repeater' );
							$this->output_repeater_fields( $field, $data );
							do_action( 'admin_page_class_after_repeater' );
							break;
						case 'import_export':
							$this->show_import_export();
							do_action( 'admin_page_class_import_export_tab' );
							break;
					}
				}
				if ( ! in_array( $field['type'], $skip ) ) {
					echo '</tr>';
				}
			}
			if ( $this->table ) {
				echo '</table>';
			}
			if ( $this->tab_div ) {
				echo '</div>';
			}
			echo '</div><div class="footer_wrap">
        <div style="apc_submit_footer">
          <input type="submit" name="Submit" class="' . apply_filters( 'admin_page_class_submit_class', 'button button-primary menu-save admin-options-save' ) . ' btn" value="' . esc_attr( __( 'Save Changes', 'inbound' ) ) . '" />
        </div>
      </div>';
			echo '<input type="hidden" name="action" value="save" />';
			echo '</form></div></div>';


			do_action( 'admin_page_class_after_page' );
		}

		public function OpenTabs_container( $text = null ) {
			$args['type'] = 'TABS';
			$text         = ( null == $text ) ? '' : $text;
			$args['text'] = $text;
			$args['id']   = 'TABS';
			$args['std']  = '';
			$this->SetField( $args );
		}

		public function CloseDiv_Container() {
			$args['type'] = 'CloseDiv';
			$args['id']   = 'CloseDiv';
			$args['std']  = '';
			$this->SetField( $args );
		}

		public function TabsListing( $args ) {
			$args['type'] = 'TABS_Listing';
			$args['id']   = 'TABS_Listing';
			$args['std']  = '';
			$this->SetField( $args );
		}

		public function OpenTab( $name ) {
			$args['type'] = 'OpenTab';
			$args['id']   = $name;
			$args['std']  = '';
			$this->SetField( $args );
		}

		public function CloseTab() {
			$args['type'] = 'CloseDiv';
			$args['id']   = 'CloseDiv';
			$args['std']  = '';
			$this->SetField( $args );
		}

		private function SetField( $args ) {
			$default = array(
				'std' => '',
				'id'  => ''
			);
			$args    = array_merge( $default, $args );
			$this->buildOptions( $args );
			$this->_fields[] = $args;
		}

		private function buildOptions( $args ) {
			$default = array(
				'std' => '',
				'id'  => ''
			);
			$args    = array_merge( $default, $args );
			$saved   = get_option( $this->option_group );
			if ( isset( $saved[ $args['id'] ] ) ) {
				if ( $saved[ $args['id'] ] === false ) {
					$saved[ $args['id'] ] = $args['std'];
					update_option( $this->args['option_group'], $saved );
				}
			}
		}

		public function Title( $label, $repeater = false ) {
			$args['type']  = 'title';
			$args['std']   = '';
			$args['label'] = $label;
			$args['id']    = 'title' . $label;
			$this->SetField( $args );
		}

		public function Subtitle( $label, $repeater = false ) {
			$args['type']  = 'subtitle';
			$args['label'] = $label;
			$args['id']    = 'title' . $label;
			$args['std']   = '';
			$this->SetField( $args );
		}

		public function Paragraph( $text, $repeater = false ) {
			$args['type'] = 'paragraph';
			$args['text'] = $text;
			$args['id']   = 'paragraph';
			$args['std']  = '';
			$this->SetField( $args );
		}

		public function load_scripts_styles() {

			// Get Plugin Path
			$plugin_path = $this->SelfPath;

			//this replaces the ugly check fields methods calls
			foreach ( array( 'upload', 'color', 'date', 'time', 'code', 'select', 'editor' ) as $type ) {
				call_user_func( array( $this, 'check_field_' . $type ) );
			}

			wp_enqueue_script( 'common' );
			if ( $this->has_Field( 'TABS' ) ) {
				wp_print_scripts( 'jquery-ui-tabs' );
			}

			// Enqueue admin page Style
			wp_enqueue_style( 'Admin_Page_Class', $plugin_path . '/css/admin_page_class.css' );

			// Enqueue admin page Scripts
			wp_enqueue_script( 'inbound-frosty', $plugin_path . '/js/frosty.min.js', array( 'jquery' ), null, true );
			wp_enqueue_script( 'inbound-image-picker', $plugin_path . '/js/image-picker.min.js', array( 'jquery' ), null, true );

			wp_enqueue_script( 'Admin_Page_Class', $plugin_path . '/js/admin_page_class.js', array(
				'jquery',
				'inbound-frosty'
			), null, true );

			wp_enqueue_script( 'utils' );
			wp_enqueue_script( 'jquery-ui-sortable' );

			add_action( 'admin_head', array( $this, 'add_typography_data' ) );
			wp_enqueue_script( 'at-chosen', $plugin_path . '/js/chosen/chosen.jquery.min.js', array( 'jquery' ), null, true );
			wp_enqueue_style( 'at-chosen', $plugin_path . '/js/chosen/chosen.min.css' );


		}


		public function add_typography_data() {
			$f     = new SR_Typography();
			$fonts = $f->get_fonts();

			$fonts_export = array();
			$fonts_merged = array_merge( $fonts['built-in'], $fonts['google'], $fonts['custom'] );
			foreach ( $fonts_merged as $slug => $font ) {
				if ( isset( $font['subsets'] ) ) {
					$subsets = $font['subsets'];
				} else {
					$subsets = array();
				}
				if ( isset( $font['variants'] ) ) {
					$variants = $font['variants'];
				} else {
					$variants = array();
				}
				$fonts_export[] = array(
					'slug'     => $slug,
					'name'     => $font['name'],
					'subsets'  => $subsets,
					'variants' => $variants,
					'type'     => $font['type']
				);
			}

			echo '<script type="text/javascript">' . "\n" . 'var fonts_data=';
			echo json_encode( $fonts_export ) . ";\n" . 'var font_picker_select = "' . esc_attr( __( 'Update', 'inbound' ) ) . '";' . "</script>\n";
		}


		public function check_field_code() {

			if ( $this->has_field( 'code' ) && $this->is_edit_page() ) {
				$plugin_path = $this->SelfPath;
			}
		}

		public function check_field_editor() {
			if ( $this->has_Field( 'editor' ) ) {
				global $wp_version;
				if ( version_compare( $wp_version, '3.2.1' ) < 1 ) {
					wp_print_scripts( 'tiny_mce' );
					wp_print_scripts( 'editor' );
					wp_print_scripts( 'editor-functions' );
				}
			}
		}

		public function check_field_select() {
			if ( $this->has_field_any( array( 'select', 'typography' ) ) && $this->is_edit_page() ) {
				$plugin_path = $this->SelfPath;
			}
		}

		public function check_field_upload() {

			// Check if the field is an image or file. If not, return.
			if ( ! $this->has_field_any( array( 'image', 'file' ) ) ) {
				return;
			}

			// Add data encoding type for file uploading.
			add_action( 'post_edit_form_tag', array( $this, 'add_enctype' ) );

			if ( wp_style_is( 'wp-color-picker', 'registered' ) ) { //since WordPress 3.5
				wp_enqueue_media();
				wp_enqueue_script( 'media-upload' );
			} else {
				// Make upload feature work event when custom post type doesn't support 'editor'
				wp_enqueue_script( 'media-upload' );
				add_thickbox();
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-sortable' );
			}


			// Add filters for media upload.
			add_filter( 'media_upload_gallery', array( $this, 'insert_images' ) );
			add_filter( 'media_upload_library', array( $this, 'insert_images' ) );
			add_filter( 'media_upload_image', array( $this, 'insert_images' ) );

			// Delete all attachments when delete custom post type.
			add_action( 'wp_ajax_at_delete_file', array( $this, 'delete_file' ) );
			add_action( 'wp_ajax_at_reorder_images', array( $this, 'reorder_images' ) );
			// Delete file via Ajax
			add_action( 'wp_ajax_at_delete_mupload', array( $this, 'wp_ajax_delete_image' ) );
		}

		public function add_enctype() {
			echo ' enctype="multipart/form-data"';
		}

		public function insert_images() {

			// If post variables are empty, return.
			if ( ! isset( $_POST['at-insert'] ) || empty( $_POST['attachments'] ) ) {
				return;
			}

			// Security Check
			check_admin_referer( 'media-form' );

			// Create Security Nonce
			$nonce = wp_create_nonce( 'at_ajax_delete' );

			// Get Post Id and Field Id
			$id = $_POST['field_id'];

			// Modify the insertion string
			$html = '';
			foreach ( $_POST['attachments'] as $attachment_id => $attachment ) {

				// Strip Slashes
				$attachment = stripslashes_deep( $attachment );

				// If not selected or url is empty, continue in loop.
				if ( empty( $attachment['selected'] ) || empty( $attachment['url'] ) ) {
					continue;
				}

				$rel = esc_attr( "{$nonce}|{$id}|{$id}|{$attachment_id}" );

				$li = "<li id='item_{$attachment_id}'>";
				$li .= "<img src='" . esc_url( $attachment['url'] ) . "' alt='image_{$attachment_id}' />";
				$li .= "<a title='" . esc_attr__( 'Delete this image', 'inbound' ) . "' class='at-delete-file' href='#' rel='{$rel}'><img src='" . esc_url( $this->SelfPath . "/images/delete-16.png" ) . "' alt='" . esc_html__( 'Delete', 'inbound' ) . "' /></a>";
				$li .= "<input type='hidden' name='{$id}[]' value='{$attachment_id}' />";
				$li .= "</li>";
				$html .= $li;

			} // End For Each

			return media_send_to_editor( $html );

		}

		public function delete_attachments( $post_id ) {

			// Get Attachments
			$attachments = get_posts( array(
				'numberposts' => - 1,
				'post_type'   => 'attachment',
				'post_parent' => $post_id
			) );

			// Loop through attachments, if not empty, delete it.
			if ( ! empty( $attachments ) ) {
				foreach ( $attachments as $att ) {
					wp_delete_attachment( $att->ID );
				}
			}

		}

		public function wp_ajax_delete_image() {
			$field_id         = isset( $_GET['field_id'] ) ? $_GET['field_id'] : 0;
			$attachment_id    = isset( $_GET['attachment_id'] ) ? intval( $_GET['attachment_id'] ) : 0;
			$ok               = false;
			$remove_meta_only = apply_filters( "apc_delete_image", true );
			if ( strpos( $field_id, '[' ) === false ) {
				check_admin_referer( "at-delete-mupload_" . urldecode( $field_id ) );
				$temp = get_option( $this->args['option_group'] );
				unset( $temp[ $field_id ] );
				update_option( $this->args['option_group'], $temp );
				if ( ! $remove_meta_only ) {
					$ok = wp_delete_attachment( $attachment_id );
				} else {
					$ok = true;
				}
			} else {
				$f = explode( '[', urldecode( $field_id ) );
				foreach ( $f as $k => $v ) {
					$f[ $k ] = str_replace( ']', '', $v );
				}
				$temp = get_option( $this->args['option_group'] );

				/**
				 * repeater  block
				 * $f[0] = repeater id
				 * $f[1] = repeater item number
				 * $f[2] = in repeater item image field id
				 *
				 * conditional  block
				 * $f[0] = conditional  id
				 * $f[1] = in conditional block image field id
				 */
				$saved = $temp[ $f[0] ];
				if ( isset( $f[2] ) && isset( $saved[ $f[1] ][ $f[2] ] ) ) { //delete from repeater  block
					unset( $saved[ $f[1] ][ $f[2] ] );
					$temp[ $f[0] ] = $saved;
					update_option( $this->args['option_group'], $temp );
					if ( ! $remove_meta_only ) {
						$ok = wp_delete_attachment( $attachment_id );
					} else {
						$ok = true;
					}
				} elseif ( isset( $saved[ $f[1] ]['src'] ) ) { //delete from conditional block
					unset( $saved[ $f[1] ] );
					$temp[ $f[0] ] = $saved;
					update_option( $this->args['option_group'], $temp );
					if ( ! $remove_meta_only ) {
						$ok = wp_delete_attachment( $attachment_id );
					} else {
						$ok = true;
					}
				}
			}

			//	    header('Content-type: application/json');

			echo json_encode( array( 'status' => 'success' ) );
			die;
		}

		public function reorder_images() {

			if ( ! isset( $_POST['data'] ) ) {
				die();
			}

			list( $order, $post_id, $key, $nonce ) = explode( '|', $_POST['data'] );

			if ( ! wp_verify_nonce( $nonce, 'at_ajax_reorder' ) ) {
				die( '1' );
			}

			parse_str( $order, $items );
			$items = $items['item'];
			$order = 1;
			foreach ( $items as $item ) {
				wp_update_post( array( 'ID' => $item, 'post_parent' => $post_id, 'menu_order' => $order ) );
				$order ++;
			}

			die( '0' );

		}

		public function check_field_color() {

			if ( $this->has_field_any( array( 'color', 'typography' ) ) && $this->is_edit_page() ) {
				if ( wp_style_is( 'wp-color-picker', 'registered' ) ) {
					wp_enqueue_style( 'wp-color-picker' );
					wp_enqueue_script( 'wp-color-picker' );
				} else {
					// Enqueu built-in script and style for color picker.
					wp_enqueue_style( 'farbtastic' );
					wp_enqueue_script( 'farbtastic' );
				}
			}

		}

		public function check_field_date() {

			if ( $this->has_field( 'date' ) && $this->is_edit_page() ) {
				$plugin_path = $this->SelfPath;
				// Enqueu JQuery UI, use proper version.
				wp_enqueue_style( 'jquery-ui-css', $plugin_path . '/css/jquery-ui.css' );
				wp_enqueue_script( 'jquery-ui' );
				wp_enqueue_script( 'jquery-ui-datepicker' );
			}

		}

		public function check_field_time() {

			if ( $this->has_field( 'time' ) && $this->is_edit_page() ) {
				$plugin_path = $this->SelfPath;

				wp_enqueue_style( 'jquery-ui-css', $plugin_path . '/css/jquery-ui.css' );
				wp_enqueue_script( 'jquery-ui' );
				wp_enqueue_script( 'at-timepicker', $plugin_path . '/js/time-and-date/jquery-ui-timepicker-addon.js', array(
					'jquery-ui-slider',
					'jquery-ui-datepicker'
				), null, true );

			}

		}

		public function add() {

			// Loop through array
			foreach ( $this->_meta_box['pages'] as $page ) {
				add_meta_box( $this->_meta_box['id'], $this->_meta_box['title'], array(
					$this,
					'show'
				), $page, $this->_meta_box['context'], $this->_meta_box['priority'] );
			}

		}

		public function show() {

			global $post;
			wp_nonce_field( basename( __FILE__ ), 'SR_Admin_Page_Class_nonce' );
			echo '<table class="form-table">';
			foreach ( $this->_fields as $field ) {
				$meta = get_post_meta( $post->ID, $field['id'], ! $field['multiple'] );
				$meta = ( $meta !== '' ) ? $meta : $field['std'];
				if ( 'image' != $field['type'] && $field['type'] != 'repeater' ) {
					$meta = is_array( $meta ) ? array_map( 'esc_attr', $meta ) : esc_attr( $meta );
				}
				echo '<tr>';

				// Call Separated methods for displaying each type of field.
				call_user_func( array( $this, 'show_field_' . $field['type'] ), $field, $meta );
				echo '</tr>';
			}
			echo '</table>';
		}

		public function show_field_repeater( $field, $meta ) {

			// Get Plugin Path
			$plugin_path = $this->SelfPath;
			$this->show_field_begin( $field, $meta );
			$class = '';
			if ( $field['sortable'] ) {
				$class = " repeater-sortable";
			}
			$jsid = ltrim( strtolower( str_replace( ' ', '', $field['id'] ) ), '0123456789' );
			echo "<div class='at-repeat" . $class . "' id='{$jsid}'>";

			$c                 = 0;
			$temp_div_row      = $this->_div_or_row;
			$this->_div_or_row = true;
			$meta              = isset( $this->_saved[ $field['id'] ] ) ? $this->_saved[ $field['id'] ] : '';

			if ( count( $meta ) > 0 && is_array( $meta ) ) {
				foreach ( $meta as $me ) {
					//for label toggles
					$mmm = isset( $me[ $field['fields'][0]['id'] ] ) ? $me[ $field['fields'][0]['id'] ] : "";
					$mmm = ( in_array( $field['fields'][0]['type'], array( 'image', 'file' ) ) ? '' : $mmm );

					echo '<div class="at-repeater-block">';
					echo '<span class="at-repeater-block-title">' . $mmm . '</span>';
					echo '<a class="button button-warning at-icon at-re-remove" id="remove-' . $field['id'] . '" title="' . esc_attr__( 'Delete', 'inbound' ) . '">' . esc_html__( 'Delete', 'inbound' ) . '</a>';
					echo '<div class="repeater-table" style="display: none;">';

					foreach ( $field['fields'] as $f ) {
						//reset var $id for repeater
						$id = '';
						$id = $field['id'] . '[' . $c . '][' . $f['id'] . ']';
						$m  = isset( $me[ $f['id'] ] ) ? $me[ $f['id'] ] : '';

						if ( $m == '' ) {
							$m = isset( $f['std'] ) ? $f['std'] : '';
						}

						if ( 'image' != $f['type'] && $f['type'] != 'repeater' ) {
							$m = is_array( $m ) ? array_map( 'esc_attr', $m ) : esc_attr( $m );
						}
						if ( in_array( $f['type'], array( 'text', 'textarea' ) ) ) {
							$m = stripslashes( $m );
						}

						//set new id for field in array format
						$f['id']           = $id;
						$f['id_sanitized'] = trim( str_replace( array( '[', ']' ), "_", $id ), '_' );

						// is this a group selector element?
						if ( isset( $f['group-selector'] ) && $f['group-selector'] == true ) {
							if ( isset( $f['class'] ) ) {
								$f['class'] .= ' group-selector';
							} else {
								$f['class'] = 'group-selector';
							}
						}

						// is this an element that belongs to a group?
						$group_class = '';
						$group_data  = '';
						if ( isset( $f['is-group'] ) && isset( $f['group-value'] ) ) {
							$repeater_item_group_id = $field['id'] . "_" . $c . "__" . $f['is-group'];
							$group_class            = ' is-group group-' . $repeater_item_group_id;
							$group_data             = ' data-group="' . $repeater_item_group_id . '"';
							$group_data .= " data-group-value='" . json_encode( $f['group-value'] ) . "'";
						}

						echo '<div class="field field-type-' . $f['type'] . $group_class . '"' . $group_data . '>';
						call_user_func( array( $this, 'show_field_' . $f['type'] ), $f, $m );
						echo '</div>';
					}

					echo '</div>';
					echo '<a class="button at-icon at-re-toggle" title="' . esc_attr__( 'Edit', 'inbound' ) . '">' . esc_html__( 'Edit', 'inbound' ) . '</a>';
					echo '</div>';
					$c = $c + 1;

				}
			}

			echo '<a class="button at-icon at-re-add" id="add-' . $jsid . '" title="' . esc_attr__( 'Add', 'inbound' ) . '">' . esc_html__( 'Add', 'inbound' ) . '</a></div>';

			//create all fields once more for js function and catch with object buffer
			ob_start();

			echo '<div class="at-repeater-block at-repeater-new-item">';
			echo '<span class="at-repeater-block-title">' . esc_html__( 'New Item', 'inbound' ) . '</span>';
			echo '<a class="button button-warning at-icon at-re-remove" id="remove-' . $jsid . '" title="' . esc_attr__( 'Delete', 'inbound' ) . '">' . esc_html__( 'Delete', 'inbound' ) . '</a>';

			echo '<div class="repeater-table">';
			foreach ( $field['fields'] as $f ) {
				//reset var $id for repeater
				$id = '';

				$id                = $field['id'] . '[CurrentCounter][' . $f['id'] . ']';
				$f['id']           = $id;
				$f['id_sanitized'] = trim( str_replace( array( '[', ']' ), "_", $id ), '_' );

				$m = isset( $f['std'] ) ? $f['std'] : '';

				// is this a group selector element?
				if ( isset( $f['group-selector'] ) && $f['group-selector'] == true ) {
					if ( isset( $f['class'] ) ) {
						$f['class'] .= ' group-selector';
					} else {
						$f['class'] = 'group-selector';
					}
				}

				// is this an element that belongs to a group?
				$group_class = '';
				$group_data  = '';
				if ( isset( $f['is-group'] ) && isset( $f['group-value'] ) ) {
					$repeater_item_group_id = $field['id'] . "_CurrentCounter__" . $f['is-group'];
					$group_class            = ' is-group group-' . $repeater_item_group_id;
					$group_data             = ' data-group="' . $repeater_item_group_id . '"';
					$group_data .= ' data-group-value="' . htmlentities( json_encode( $f['group-value'] ) ) . '"';
				}

				echo '<div class="field field-type-' . $f['type'] . $group_class . '"' . $group_data . '>';
				call_user_func( array( $this, 'show_field_' . $f['type'] ), $f, $m );
				echo '</div>';
			}
			echo '</div>';

			echo '<a class="button at-icon at-re-toggle" id="edit-' . $jsid . '" title="' . esc_attr__( 'Edit', 'inbound' ) . '">' . esc_html__( 'Edit', 'inbound' ) . '</a>';
			echo '</div>';

			$counter = 'countadd_' . $jsid;
			$js_code = ob_get_clean();
			$js_code = str_replace( "'", "\"", $js_code );
			$js_code = str_replace( "CurrentCounter", "' + " . $counter . " + '", $js_code );
			echo '<script>
        jQuery(document).ready(function() {
          var ' . $counter . ' = ' . $c . ';
          jQuery("#add-' . $jsid . '").live(\'click\', function() {
            ' . $counter . ' = ' . $counter . ' + 1;
            jQuery(this).before(\'' . $js_code . '\');
            update_repeater_fields();
          });
              jQuery("#remove-' . $jsid . '").live(\'click\', function() {
                  jQuery(this).parent().remove();
              });
          });
        </script>';

			$this->_div_or_row = $temp_div_row;
			$this->show_field_end( $field, $meta );
		}

		public function show_field_begin( $field, $meta ) {
			if ( $this->_div_or_row ) {
				echo "<td class='at-field'>";
			}

			//check for errors
			if ( $this->saved_flag && $this->errors_flag && isset( $field['validate'] ) && isset( $field['id'] ) && $this->has_error( $field['id'] ) ) {
				echo '<div class="alert alert-error field-validation-error"><button data-dismiss="alert" class="close" type="button">x</button>';
				$ers = $this->getFieldErrors( $field['id'] );
				foreach ( (array) $ers['m'] as $m ) {
					echo "{$m}</br />";
				}
				echo '</div>';
			}

			if ( $field['name'] != '' || $field['name'] != false ) {
				echo "<div class='at-label'><label for='{$field['id']}'>{$field['name']}";
				if ( isset( $field['desc'] ) && $field['desc'] != '' ) {
					echo ' <span class="label-desc has-tip tip-right" title="' . esc_attr( $field['desc'] ) . '">?</span>';
				}
				echo "</label></div>";
			}
		}

		public function show_field_end( $field, $meta = null, $group = false ) {
			if ( $this->_div_or_row ) {
				echo "</td>";
			}
		}

		public function show_field_sortable( $field, $meta ) {

			if ( ! is_array( $field['options'] ) ) {
				$field['options'] = call_user_func( $field['options'] );
			}

			$this->show_field_begin( $field, $meta );
			$re = '<div class="at-sortable-con"><ul class="at-sortable">';
			$i  = 0;
			if ( ! is_array( $meta ) || empty( $meta ) ) {
				foreach ( $field['options'] as $value => $label ) {
					$re .= '<li class="widget-sort at-sort-item_' . $i . '">' . $label . '<input type="hidden" value="' . $label . '" name="' . $field['id'] . '[' . $value . ']">';
				}
			} else {
				// add new items to selection that did not previously exist
				foreach ( $field['options'] as $m => $v ) {
					if ( ! array_key_exists( $m, $meta ) ) {
						$meta[ $m ] = $v;
					}
				}

				// remove items that are no longer available for selection
				foreach ( $meta as $m => $v ) {
					if ( ! array_key_exists( $m, $field['options'] ) ) {
						unset( $meta[ $m ] );
					}
				}

				foreach ( $meta as $value => $label ) {
					$re .= '<li class="widget-sort at-sort-item_' . $i . '">' . $label . '<input type="hidden" value="' . $label . '" name="' . $field['id'] . '[' . $value . ']">';
				}
			}
			$re .= '</ul></div>';
			echo $re;
			$this->show_field_end( $field, $meta );
		}

		public function show_field_text( $field, $meta ) {
			$this->show_field_begin( $field, $meta );
			if ( isset( $field['field_type'] ) ) {
				$type = $field['field_type'];
			} else {
				$type = "text";
			}
			if ( $type == "number" ) {
				$min = " min='0'";
			} else {
				$min = '';
			}
			echo "<input type='{$type}' " . $min . " class='at-text" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}' id='{$field['id']}' value='" . esc_attr( stripslashes( $meta ) ) . "' size='30' />";
			if ( isset( $field['text_after'] ) ) {
				echo " " . $field['text_after'];
			}
			$this->show_field_end( $field, $meta );
		}

		public function show_field_code( $field, $meta ) {
			$this->show_field_begin( $field, $meta );
			echo "<textarea class='code_text" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}' id='{$field['id']}' data-lang='{$field['syntax']}' data-theme='{$field['theme']}'>" . stripslashes( $meta ) . "</textarea>";
			$this->show_field_end( $field, $meta );
		}

		public function show_field_hidden( $field, $meta ) {
			echo "<input type='hidden' class='at-hidden" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}' id='{$field['id']}' value='{$meta}'/>";
		}

		public function show_field_paragraph( $field ) {
			echo '<p>' . $field['value'] . '</p>';
		}

		public function show_field_section( $field ) {
			echo '<h3>' . $field['value'] . '</h3>';
		}

		public function show_field_textlabel( $field, $meta ) {
			$this->show_field_begin( $field, $meta );
			echo "<input type='text' readonly class='at-text readonly" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}' id='{$field['id']}' value='" . esc_attr( stripslashes( $field['value'] ) ) . "' />";
			if ( isset( $field['text_after'] ) ) {
				echo " " . $field['text_after'];
			}
			$this->show_field_end( $field, $meta );
		}

		public function show_field_textarea( $field, $meta ) {
			$this->show_field_begin( $field, $meta );

			if ( isset( $field['code'] ) ) {
				echo "<textarea class='at-textarea textarea-code large-text" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}' id='{$field['id']}' cols='60' rows='10'>" . addslashes( $meta ) . "</textarea>";
			} else {
				echo "<textarea class='at-textarea large-text" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}' id='{$field['id']}' cols='60' rows='10'>" . esc_textarea( stripslashes( $meta ) ) . "</textarea>";
			}

			if ( isset( $field['form'] ) ) {
				echo '<a class="button-secondary clean_form" href="javascript:void(0);">' . esc_html__( 'Clean Form Code', 'inbound' ) . '</a>';
			}


			$this->show_field_end( $field, $meta );
		}

		public function show_field_select( $field, $meta ) {

			if ( ! is_array( $meta ) ) {
				$meta = (array) $meta;
			}

			$this->show_field_begin( $field, $meta );

			if ( ! isset( $field['id_sanitized'] ) ) {
				$field['id_sanitized'] = trim( str_replace( array( '[', ']' ), "_", $field['id'] ), '_' );
			}

			if ( isset( $field['image-picker'] ) && $field['image-picker'] == true ) {
				echo "<select id='{$field['id_sanitized']}' class='at-select image-picker show-labels show-html" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}" . ( ( isset( $field['multiple'] ) && $field['multiple'] ) ? "[]' id='{$field['id']}' multiple='multiple'" : "'" ) . ">";
				foreach ( $field['options'] as $key => $value ) {
					echo "<option value='{$key}'" . selected( in_array( $key, $meta ), true, false ) . " data-img-src='{$value['image']}'  data-img-label='{$value['label']}'>{$value['label']}</option>";
				}
				echo "</select>";

			} else {
				echo "<select id='{$field['id_sanitized']}' class='at-select" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}" . ( ( isset( $field['multiple'] ) && $field['multiple'] ) ? "[]' multiple='multiple'" : "'" ) . ">";
				foreach ( $field['options'] as $key => $value ) {
					echo "<option value='{$key}'" . selected( in_array( $key, $meta ), true, false ) . ">{$value}</option>";
				}
				echo "</select>";

			}

			$this->show_field_end( $field, $meta );
		}

		public function show_field_radio( $field, $meta ) {

			if ( ! is_array( $meta ) ) {
				$meta = (array) $meta;
			}

			$this->show_field_begin( $field, $meta );
			foreach ( $field['options'] as $key => $value ) {
				echo "<input id='{$field['id']}_{$key}' type='radio' " . ( isset( $field['style'] ) ? "style='{$field['style']}' " : '' ) . " class='at-radio" . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' ) . "' name='{$field['id']}' value='{$key}'" . checked( in_array( $key, $meta ), true, false ) . " /> <label for='{$field['id']}_{$key}'><span class='at-radio-label'>{$value}</span></label>";
			}
			$this->show_field_end( $field, $meta );
		}

		public function show_field_checkbox( $field, $meta ) {

			$this->show_field_begin( $field, $meta );
			$meta = ( $meta == 'on' ) ? true : $meta;

			if ( ! isset( $field['id_sanitized'] ) ) {
				$field['id_sanitized'] = trim( str_replace( array( '[', ']' ), "_", $field['id'] ), '_' );
			}

			echo "<input type='checkbox' class='at-checkbox" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}' id='{$field['id_sanitized']}'" . checked( $meta, true, false ) . " />";

			if ( isset( $field['caption'] ) ) {
				echo "<label class='at-checkbox-label' for='{$field['id']}'><span>{$field['caption']}</span></label>";
			}

			$this->show_field_end( $field, $meta );
		}

		public function show_field_cond( $field, $meta ) {

			$this->show_field_begin( $field, $meta );
			$checked = false;
			if ( is_array( $meta ) && isset( $meta['enabled'] ) && $meta['enabled'] == 'on' ) {
				$checked = true;
			}
			echo "<input type='checkbox' class='no-toggle conditional_control' name='{$field['id']}[enabled]' id='{$field['id']}'" . checked( $checked, true, false ) . " />";
			//start showing the fields
			$display = ( $checked ) ? '' : ' style="display: none;"';

			echo '<div class="conditional_container"' . $display . '>';
			foreach ( (array) $field['fields'] as $f ) {
				//reset var $id for cond
				$id = '';
				$id = $field['id'] . '[' . $f['id'] . ']';
				$m  = '';
				$m  = ( isset( $meta[ $f['id'] ] ) ) ? $meta[ $f['id'] ] : '';
				$m  = ( $m !== '' ) ? $m : ( isset( $f['std'] ) ? $f['std'] : '' );
				if ( 'image' != $f['type'] && $f['type'] != 'repeater' ) {
					$m = is_array( $m ) ? array_map( 'esc_attr', $m ) : esc_attr( $m );
				}
				//set new id for field in array format
				$f['id'] = $id;
				call_user_func( array( $this, 'show_field_' . $f['type'] ), $f, $m );
			}
			echo '</div>';
			$this->show_field_end( $field, $meta );
		}

		public function show_field_wysiwyg( $field, $meta ) {
			$this->show_field_begin( $field, $meta );
			// Add TinyMCE script for WP version < 3.3
			global $wp_version;

			if ( version_compare( $wp_version, '3.2.1' ) < 1 ) {
				echo "<textarea class='at-wysiwyg theEditor large-text" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}' id='{$field['id']}' cols='60' rows='10'>{$meta}</textarea>";
			} else {
				// Use new wp_editor() since WP 3.3
				wp_editor( stripslashes( stripslashes( html_entity_decode( $meta ) ) ), $field['id'], array( 'editor_class' => 'at-wysiwyg' . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) ) );
			}
			$this->show_field_end( $field, $meta );
		}

		public function show_field_file( $field, $meta ) {

			global $post;

			if ( ! is_array( $meta ) ) {
				$meta = (array) $meta;
			}

			$this->show_field_begin( $field, $meta );
			echo "{$field['desc']}<br />";

			if ( ! empty( $meta ) ) {
				$nonce = wp_create_nonce( 'at_ajax_delete' );
				echo '<div style="margin-bottom: 10px"><strong>' . esc_html__( 'Uploaded files', 'inbound' ) . '</strong></div>';
				echo '<ol class="at-upload">';
				foreach ( $meta as $att ) {
					// if (wp_attachment_is_image($att)) continue; // what's image uploader for?
					echo "<li>" . wp_get_attachment_link( $att, '', false, false, ' ' ) . " (<a class='at-delete-file' href='#' rel='{$nonce}|{$post->ID}|{$field['id']}|{$att}'>" . esc_html__( 'Delete', 'inbound' ) . "</a>)</li>";
				}
				echo '</ol>';
			}

			// show form upload
			echo "<div class='at-file-upload-label'>";
			echo "<strong>" . esc_html__( 'Upload new files', 'inbound' ) . "</strong>";
			echo "</div>";
			echo "<div class='new-files'>";
			echo "<div class='file-input'>";
			echo "<input type='file' name='{$field['id']}[]' />";
			echo "</div><!-- End .file-input -->";
			echo "<a class='at-add-file button' href='#'>" . esc_html__( 'Add more files', 'inbound' ) . "</a>";
			echo "</div><!-- End .new-files -->";
			echo "</td>";
		}


		public function show_field_media_manager( $field, $meta ) {
			$this->show_field_begin( $field, $meta );
			$html   = wp_nonce_field( "at-delete-mupload_{$field['id']}", "nonce-delete-mupload_" . $field['id'], false, false );
			$height = ( isset( $field['preview_height'] ) ) ? $field['preview_height'] : '150px';
			$width  = ( isset( $field['preview_width'] ) ) ? $field['preview_width'] : '150px';
			$multi  = ( isset( $field['multiple'] ) && $field['multiple'] == true ) ? 'true' : 'false';
			if ( is_array( $meta ) ) {
				if ( isset( $meta[0] ) && is_array( $meta[0] ) ) {
					$meta = $meta[0];
				}
			}
			if ( is_array( $meta ) && isset( $meta['src'] ) && $meta['src'] != '' ) {
				$html .= "<span class='mupload_img_holder' data-wi='" . $width . "' data-he='" . $height . "'><img src='" . esc_url( $meta['src'] ) . "' /></span>";
				$html .= "<input type='hidden' name='" . $field['id'] . "[id]' id='" . $field['id'] . "[id]' value='" . $meta['id'] . "' />";
				$html .= "<input type='hidden' name='" . $field['id'] . "[src]' id='" . $field['id'] . "[src]' value='" . $meta['src'] . "' />";
				$html .= "<input class='at-delete_image_button button' type='button' rel='" . $field['id'] . "' value='" . esc_html__( 'Delete Image', 'inbound' ) . "' />";
			} else {
				$html .= "<span class='mupload_img_holder'  data-wi='" . $width . "' data-he='" . $height . "' data-multi='" . $multi . "'></span>";
				$html .= "<input type='hidden' name='" . $field['id'] . "[id]' id='" . $field['id'] . "[id]' value='' />";
				$html .= "<input type='hidden' name='" . $field['id'] . "[src]' id='" . $field['id'] . "[src]' value='' />";
				$html .= "<input class='at-mm-upload_image_button button' type='button' rel='" . $field['id'] . "' value='" . esc_html__( 'Upload Image', 'inbound' ) . "' />";
			}
			echo $html;
			$this->show_field_end( $field, $meta );
		}

		public function show_field_image( $field, $meta ) {
			$this->show_field_begin( $field, $meta );
			$html        = wp_nonce_field( "at-delete-mupload_{$field['id']}", "nonce-delete-mupload_" . $field['id'], false, false );
			$height      = ( isset( $field['preview_height'] ) ) ? $field['preview_height'] : '150px';
			$width       = ( isset( $field['preview_width'] ) ) ? $field['preview_width'] : '150px';
			$upload_type = ( ! function_exists( 'wp_enqueue_media' ) ) ? 'tk' : 'mm';
			if ( is_array( $meta ) ) {
				if ( isset( $meta[0] ) && is_array( $meta[0] ) ) {
					$meta = $meta[0];
				}
			}
			if ( is_array( $meta ) && isset( $meta['src'] ) && $meta['src'] != '' ) {
				$html .= "<span class='mupload_img_holder' data-wi='" . $width . "' data-he='" . $height . "'><img src='" . esc_url( $meta['src'] ) . "' /></span>";
				$html .= "<input type='hidden' name='" . $field['id'] . "[id]' id='" . $field['id'] . "[id]' value='" . $meta['id'] . "' />";
				$html .= "<input type='hidden' name='" . $field['id'] . "[src]' id='" . $field['id'] . "[src]' value='" . $meta['src'] . "' />";
				$html .= "<input class='at-delete_image_button button' type='button' data-u='" . $upload_type . "' rel='" . $field['id'] . "' value='" . esc_html__( 'Delete Image', 'inbound' ) . "' />";
			} else {
				$html .= "<span class='mupload_img_holder'  data-wi='" . $width . "' data-he='" . $height . "'></span>";
				$html .= "<input type='hidden' name='" . $field['id'] . "[id]' id='" . $field['id'] . "[id]' value='' />";
				$html .= "<input type='hidden' name='" . $field['id'] . "[src]' id='" . $field['id'] . "[src]' value='' />";
				$html .= "<input class='at-upload_image_button button' type='button' data-u='" . $upload_type . "' rel='" . $field['id'] . "' value='" . esc_html__( 'Upload Image', 'inbound' ) . "' />";
			}
			echo $html;
			$this->show_field_end( $field, $meta );
		}


		public function show_field_button( $field, $meta ) {
			//$this->show_field_begin( $field, $meta );
			$html = wp_nonce_field( "{$field['id']}_nonce", "{$field['id']}_nonce", false, false );
			$link = "javascript:void(0);";
			if (isset($field['href'])) $link = esc_url( $field['href'] );
			$html .= '<a class="button-primary" id="' . $field['id'] . '" href="' . esc_attr( $link ) . '">' . $field['caption'] . '</a>';
			$html .= '<div id="' . $field['id'] . '_status" style="display: none;"><span>' . esc_html__( 'Refreshing fonts database...', 'inbound' ) . '</span></div>';
			$html .= '<div id="' . $field['id'] . '_results" style="display: none;" class="apc-results-message alert"></div>';
			echo $html;
			//$this->show_field_end( $field, $meta );
		}

		public function show_field_typography( $field, $meta ) {
			$this->show_field_begin( $field, $meta );

			if ( ! is_array( $meta ) ) {
				$meta = array(
					'size'   => '',
					'face'   => '',
					'weight' => '',
					'color'  => '#',
				);
			}

			$id_sanitized = trim( str_replace( array( '[', ']' ), "_", $field['id'] ), '_' );

			$f     = new SR_Typography();
			$faces = $f->get_fonts();

			$selected_font = $meta['face'];

			// Font Size
			$html = '<input type="hidden" class="at-typography at-typography-size" name="' . esc_attr( $field['id'] . '[size]' ) . '" id="' . esc_attr( $id_sanitized . '_size' ) . '" value="' . esc_attr( $meta['size'] ) . '" readonly>';

			// Font Face
			$html .= '<input type="hidden" class="at-typography at-typography-face" name="' . esc_attr( $field['id'] . '[face]' ) . '" id="' . esc_attr( $id_sanitized . '_face' ) . '" value="' . esc_attr( $meta['face'] ) . '" readonly>';

			// Font Weight
			$html .= '<input type="hidden" class="at-typography at-typography-weight" name="' . esc_attr( $field['id'] . '[weight]' ) . '" id="' . esc_attr( $id_sanitized . '_weight' ) . '" value="' . $meta['weight'] . '" readonly>';

			// Color
			if ( $field['std']['color'] ) {
				$html .= '<input class="at-typography at-color-iris" name="' . esc_attr( $field['id'] . '[color]' ) . '" id="' . $id_sanitized . '_color" value="' . $meta['color'] . '" size="8" />';
			} else {
				$html .= '<input type="hidden" name="' . esc_attr( $field['id'] . '[color]' ) . '" id="' . $id_sanitized . '_color" value="0" />';
			}

			// Selector link
			if ( isset( $faces['built-in'][ $selected_font ] ) ) {
				$face_display = $faces['built-in'][ $selected_font ]['name'];
			} elseif ( isset( $faces['google'][ $selected_font ] ) ) {
				$face_display = $faces['google'][ $selected_font ]['name'];
			} else {
				$face_display = $selected_font;
			}

			$html .= '<input id="' . $id_sanitized . '_preview" type="text" class="at-font-select-preview" value="' . $meta['size'] . " " . esc_attr( $face_display ) . "/" . $meta['weight'] . '" readonly><a href="javascript:void(0);" class="button at-font-select" data-target="' . esc_attr( $id_sanitized ) . '">' . esc_html__( 'Select', 'inbound' ) . '</a>';


			echo $html;
			$this->show_field_end( $field, $meta );
		}

		public function show_field_color( $field, $meta ) {

			if ( empty( $meta ) ) {
				$meta = '#';
			}

			$this->show_field_begin( $field, $meta );
			if ( wp_style_is( 'wp-color-picker', 'registered' ) ) { //iris color picker since 3.5
				echo "<input class='at-color-iris" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' type='text' name='{$field['id']}' id='{$field['id']}' value='{$meta}' size='8' />";
			} else {
				echo "<input class='at-color" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' type='text' name='{$field['id']}' id='{$field['id']}' value='{$meta}' size='8' />";
				echo "<input type='button' class='at-color-select button' rel='{$field['id']}' value='" . esc_html__( 'Select a color', 'inbound' ) . "'/>";
				echo "<div style='display:none' class='at-color-picker' rel='{$field['id']}'></div>";
			}
			$this->show_field_end( $field, $meta );

		}

		public function show_field_checkbox_list( $field, $meta ) {

			if ( ! is_array( $meta ) ) {
				$meta = (array) $meta;
			}

			$this->show_field_begin( $field, $meta );

			$html = array();

			if ( isset( $field['sortable'] ) && $field['sortable'] ) {
				$is_sortable    = true;
				$sortable_class = ' at-sortable';
				if ( is_array( $meta ) ) {
					$field['options'] = array_merge( array_flip( $meta ), $field['options'] );
				}
			} else {
				$is_sortable    = false;
				$sortable_class = '';
			}

			if ( isset ($field['special']) ) {
				if ( $field['special'] == "post-types") {
					$field['options'] = array();

					$args = array(
						'show_ui' => 'true'
					);

					$post_types = get_post_types( $args, 'objects' );
					foreach ( $post_types  as $post_type ) {
						$field['options'][$post_type->name] = $post_type->labels->name;
					}
				}
			}

			echo '<ul class="at-checkbox-list' . $sortable_class . '">';
			foreach ( $field['options'] as $key => $value ) {
				if ( $is_sortable ) {
					$html[] = "<li class='widget-sort'><label class='at-checkbox_list-label'><input type='checkbox' class='at-checkbox_list" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}[]' value='{$key}'" . checked( in_array( $key, $meta ), true, false ) . " />{$value}</label></li>";
				} else {
					$html[] = "<li><label class='at-checkbox_list-label'><input type='checkbox' class='at-checkbox_list" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}[]' value='{$key}'" . checked( in_array( $key, $meta ), true, false ) . " />{$value}</label></li>";
				}
			}
			echo implode( "\n", $html );
			echo '</ul>';

			$this->show_field_end( $field, $meta );

		}

		public function show_field_date( $field, $meta ) {
			$this->show_field_begin( $field, $meta );
			echo "<input type='text' class='at-date" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}' id='{$field['id']}' rel='{$field['format']}' value='{$meta}' size='30' />";
			$this->show_field_end( $field, $meta );
		}

		public function show_field_time( $field, $meta ) {
			$this->show_field_begin( $field, $meta );
			echo "<input type='text' class='at-time" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}' id='{$field['id']}' rel='{$field['format']}' value='{$meta}' size='30' />";
			$this->show_field_end( $field, $meta );
		}

		public function show_field_posts( $field, $meta ) {
			global $post;

			if ( ! is_array( $meta ) ) {
				$meta = (array) $meta;
			}
			$this->show_field_begin( $field, $meta );
			$options = $field['options'];
			//$posts = get_posts($options['args']);

			$query = new WP_Query( array( 'post_type' => $field['options']['post_type'], 'posts_per_page' => - 1 ) );
			$posts = $query->get_posts();

			// checkbox_list
			if ( 'checkbox_list' == $options['type'] ) {
				foreach ( $posts as $p ) {
					if ( isset( $field['class'] ) && $field['class'] == 'no-toggle' ) {
						echo "<label class='at-posts-checkbox-label'><input type='checkbox' class='at-posts-checkbox" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}[]' value='$p->ID'" . checked( in_array( $p->ID, $meta ), true, false ) . " /> {$p->post_title}</label>";
					} else {
						echo "{$p->post_title}<input type='checkbox' class='at-posts-checkbox" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}[]' value='$p->ID'" . checked( in_array( $p->ID, $meta ), true, false ) . " />";
					}
				}
			} // select
			else {
				echo "<select class='at-posts-select" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}" . ( $field['multiple'] ? "[]' multiple='multiple'  style='height:auto'" : "'" ) . ">";
				echo '<option value="0">' . esc_html__( 'None', 'inbound' ) . '</option>';
				foreach ( $posts as $p ) {
					echo "<option value='$p->ID'" . selected( in_array( $p->ID, $meta ), true, false ) . ">$p->post_title [ID $p->ID]</option>";
				}
				echo "</select>";
			}

			$this->show_field_end( $field, $meta );
		}

		public function show_field_taxonomy( $field, $meta ) {
			global $post;

			if ( ! is_array( $meta ) ) {
				$meta = (array) $meta;
			}
			$this->show_field_begin( $field, $meta );
			$options = $field['options'];
			$terms   = get_terms( $options['taxonomy'], $options['args'] );

			// checkbox_list
			if ( 'checkbox_list' == $options['type'] ) {
				foreach ( $terms as $term ) {
					if ( isset( $field['class'] ) && $field['class'] == 'no-toggle' ) {
						echo "<label class='at-tax-checkbox-label'><input type='checkbox' class='at-tax-checkbox" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}[]' value='$term->slug'" . checked( in_array( $term->slug, $meta ), true, false ) . " /> {$term->name}</label>";
					} else {
						echo "{$term->name} <input type='checkbox' class='at-tax-checkbox" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}[]' value='$term->slug'" . checked( in_array( $term->slug, $meta ), true, false ) . " />";
					}
				}
			} // select
			else {
				echo "<select class='at-tax-select" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}" . ( $field['multiple'] ? "[]' multiple='multiple' style='height:auto'" : "'" ) . ">";
				foreach ( $terms as $term ) {
					echo "<option value='$term->slug'" . selected( in_array( $term->slug, $meta ), true, false ) . ">$term->name</option>";
				}
				echo "</select>";
			}

			$this->show_field_end( $field, $meta );
		}

		public function show_field_WProle( $field, $meta ) {
			if ( ! is_array( $meta ) ) {
				$meta = (array) $meta;
			}
			$this->show_field_begin( $field, $meta );
			$options = $field['options'];
			global $wp_roles;
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
			$names = $wp_roles->get_names();
			if ( $names ) {
				// checkbox_list
				if ( 'checkbox_list' == $options['type'] ) {
					foreach ( $names as $n ) {
						if ( isset( $field['class'] ) && $field['class'] == 'no-toggle' ) {
							echo "<label class='at-posts-checkbox-label'><input type='checkbox'  class='at-role-checkbox" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}[]' value='$n'" . checked( in_array( $n, $meta ), true, false ) . " /> " . $n . "</label>";
						} else {
							echo "{$n} <input type='checkbox'  class='at-role-checkbox" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}[]' value='$n'" . checked( in_array( $n, $meta ), true, false ) . " />";
						}
					}
				} // select
				else {
					echo "<select  class='at-role-select" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' name='{$field['id']}" . ( $field['multiple'] ? "[]' multiple='multiple' style='height:auto'" : "'" ) . ">";
					foreach ( $names as $n ) {
						echo "<option value='$n'" . selected( in_array( $n, $meta ), true, false ) . ">$n</option>";
					}
					echo "</select>";
				}
			}
			$this->show_field_end( $field, $meta );
		}

		/*
     * Processing Functions
     */


		public function save( $repeater = false ) {
			$saved        = get_option( $this->option_group );
			$this->_saved = $saved;

			$post_data = isset( $_POST ) ? $_POST : null;

			If ( $post_data == null ) {
				return;
			}

			$skip = array(
				'title',
				'paragraph',
				'subtitle',
				'TABS',
				'CloseDiv',
				'TABS_Listing',
				'OpenTab',
				'import_export'
			);

			//check nonce
			if ( ! check_admin_referer( basename( __FILE__ ), 'SR_Admin_Page_Class_nonce' ) ) {
				return;
			}

			foreach ( $this->_fields as $field ) {
				if ( ! in_array( $field['type'], $skip ) ) {

					$name = $field['id'];
					$type = $field['type'];
					$old  = isset( $saved[ $name ] ) ? $saved[ $name ] : null;
					$new  = ( isset( $_POST[ $name ] ) ) ? $_POST[ $name ] : ( ( isset( $field['multiple'] ) && $field['multiple'] ) ? array() : '' );


					//Validate and sanitize meta value
					//issue #27
					$validationClass = apply_filters( 'apc_validation_class_name', 'SR_Admin_Page_Class_Validate', $this );
					if ( class_exists( $validationClass ) && isset( $field['validate_func'] ) && method_exists( $validationClass, $field['validate_func'] ) ) {
						$new = call_user_func( array( $validationClass, $field['validate_func'] ), $new, $this );
					}

					//native validation
					if ( isset( $field['validate'] ) ) {
						if ( ! $this->validate_field( $field, $new ) ) {
							$new = $old;
						}
					}
					// Call defined method to save meta value, if there's no methods, call common one.
					$save_func = 'save_field_' . $type;
					if ( method_exists( $this, $save_func ) ) {
						call_user_func( array( $this, 'save_field_' . $type ), $field, $old, $new );
					} else {
						$this->save_field( $field, $old, $new );
					}

				}//END Skip
			} // End foreach

			update_option( $this->args['option_group'], $this->_saved );
		}

		public function save_field( $field, $old, $new ) {
			$name = $field['id'];
			unset( $this->_saved[ $name ] );
			if ( $new === '' || $new === array() ) {
				return;
			}
			if ( isset( $field['multiple'] ) && $field['multiple'] ) {
				foreach ( $new as $add_new ) {
					$temp[] = $add_new;
				}
				$this->_saved[ $name ] = $temp;
			} else {
				$this->_saved[ $name ] = $new;
			}
		}

		public function save_field_image( $field, $old, $new ) {
			$name = $field['id'];
			unset( $this->_saved[ $name ] );
			if ( $new === '' || $new === array() || $new['id'] == '' || $new['src'] == '' ) {
				return;
			}

			$this->_saved[ $name ] = $new;
		}

		public function save_field_wysiwyg( $field, $old, $new ) {
			$this->save_field( $field, $old, htmlentities( $new ) );
		}

		public function save_field_textarea( $field, $old, $new ) {
			$this->save_field( $field, $old, stripslashes( $new ) );
		}

		public function save_field_text( $field, $old, $new ) {
			$this->save_field( $field, $old, stripslashes( $new ) );
		}

		public function save_field_checkbox( $field, $old, $new ) {
			if ( $new === '' ) {
				$this->save_field( $field, $old, false );
			} else {
				$this->save_field( $field, $old, true );
			}
		}

		public function save_field_repeater( $field, $old, $new ) {
			if ( is_array( $new ) && count( $new ) > 0 ) {
				foreach ( $new as $n ) {
					foreach ( $field['fields'] as $f ) {
						$type = $f['type'];
						switch ( $type ) {
							case 'wysiwyg':
								$n[ $f['id'] ] = wpautop( $n[ $f['id'] ] );
								break;
							case 'file':
								$n[ $f['id'] ] = $this->save_field_file_repeater( $f, '', $n[ $f['id'] ] );
								break;
							default:
								break;
						}
					}
					if ( ! $this->is_array_empty( $n ) ) {
						$temp[] = $n;
					}
				}
				if ( isset( $temp ) && count( $temp ) > 0 && ! $this->is_array_empty( $temp ) ) {
					$this->_saved[ $field['id'] ] = $temp;
				} else {
					if ( isset( $this->_saved[ $field['id'] ] ) ) {
						unset( $this->_saved[ $field['id'] ] );
					}
				}
			} else {
				//  remove old meta if exists
				if ( isset( $this->_saved[ $field['id'] ] ) ) {
					unset( $this->_saved[ $field['id'] ] );
				}
			}
		}


		public function add_missed_values() {

			// Default values for admin
			//$this->_meta_box = array_merge( array( 'context' => 'normal', 'priority' => 'high', 'pages' => array( 'post' ) ), $this->_meta_box );

			// Default values for fields
			foreach ( $this->_fields as &$field ) {

				$multiple = in_array( $field['type'], array( 'checkbox_list', 'file', 'image' ) );
				$std      = $multiple ? array() : '';
				$format   = 'date' == $field['type'] ? 'yy-mm-dd' : ( 'time' == $field['type'] ? 'hh:mm' : '' );

				$field = array_merge( array(
					'multiple'      => $multiple,
					'std'           => $std,
					'desc'          => '',
					'format'        => $format,
					'validate_func' => ''
				), $field );

			} // End foreach

		}

		public function has_field( $type ) {
			//faster search in single array.
			if ( count( $this->field_types ) > 0 ) {
				return in_array( $type, $this->field_types );
			}

			//run once over all fields and store the types in a local array
			$temp = array();
			foreach ( $this->_fields as $field ) {
				$temp[] = $field['type'];
				if ( 'repeater' == $field['type'] || 'cond' == $field['type'] ) {
					foreach ( (array) $field["fields"] as $repeater_field ) {
						$temp[] = $repeater_field["type"];
					}
				}
			}

			//remove duplicates
			$this->field_types = array_unique( $temp );

			//call this function one more time now that we have an array of field types
			return $this->has_field( $type );
		}

		public function has_field_any( $types ) {
			foreach ( (array) $types as $t ) {
				if ( $this->has_field( $t ) ) {
					return true;
				}
			}

			return false;
		}

		public function is_edit_page() {
			//global $pagenow;
			return true;
			//return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
		}

		/**
		 * Fixes the odd indexing of multiple file uploads.
		 *
		 * Goes from the format:
		 * $_FILES['field']['key']['index']
		 * to
		 * The More standard and appropriate:
		 * $_FILES['field']['index']['key']
		 *
		 * @param string $files
		 *
		 * @since  0.1
		 * @access public
		 */
		public function fix_file_array( &$files ) {

			$output = array();

			foreach ( $files as $key => $list ) {
				foreach ( $list as $index => $value ) {
					$output[ $index ][ $key ] = $value;
				}
			}

			return $files = $output;

		}

		public function get_jqueryui_ver() {

			global $wp_version;

			if ( version_compare( $wp_version, '3.1', '>=' ) ) {
				return '1.8.10';
			}

			return '1.7.3';

		}

		/*
     * "Add field..." functions
     */

		public function addField( $id, $args ) {
			$new_field       = array( 'id' => $id, 'std' => '', 'desc' => '', 'style' => '' );
			$new_field       = array_merge( $new_field, $args );
			$this->_fields[] = $new_field;
		}

		public function addTypography( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'  => 'typography',
				'id'    => $id,
				'std'   => array(
					'size'   => '12px',
					'face'   => 'arial',
					'weight' => 'normal',
					'color'  => '#000000',
				),
				'desc'  => '',
				'style' => '',
				'name'  => 'Typography field'
			);
			$new_field = array_merge( $new_field, $args );

			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}

		}

		public function addText( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'  => 'text',
				'id'    => $id,
				'std'   => '',
				'desc'  => '',
				'style' => '',
				'name'  => 'Text Field'
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}


		public function addHidden( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'  => 'hidden',
				'id'    => $id,
				'std'   => '',
				'desc'  => '',
				'style' => '',
				'name'  => ''
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addCode( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'   => 'code',
				'id'     => $id,
				'std'    => '',
				'desc'   => '',
				'style'  => '',
				'name'   => 'Code Editor Field',
				'syntax' => 'php',
				'theme'  => 'default'
			);
			$new_field = array_merge( $new_field, (array) $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addParagraph( $p, $repeater = false ) {
			$new_field = array( 'type' => 'paragraph', 'id' => '', 'value' => $p );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addTextlabel( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'  => 'textlabel',
				'id'    => $id,
				'std'   => '',
				'desc'  => '',
				'style' => '',
				'name'  => 'Text Label'
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addSection( $p, $repeater = false ) {
			$new_field = array( 'type' => 'section', 'id' => '', 'value' => $p );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addCheckbox( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'  => 'checkbox',
				'id'    => $id,
				'std'   => '',
				'desc'  => '',
				'style' => '',
				'name'  => 'Checkbox Field'
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		/**
		 *  Add Checkbox conditional Field to Page
		 *
		 * @param $args mixed|array
		 *              'name' => // field name/label string optional
		 *              'desc' => // field description, string optional
		 *              'std' => // default value, string optional
		 *              'validate_func' => // validate function, string optional
		 *              'fields' => list of fields to show conditionally.
		 */
		public function addCondition( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'   => 'cond',
				'id'     => $id,
				'std'    => '',
				'desc'   => '',
				'style'  => '',
				'name'   => 'Conditional Field',
				'fields' => array()
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addCheckboxList( $id, $options = array(), $args, $repeater = false ) {
			$new_field = array(
				'type'     => 'checkbox_list',
				'id'       => $id,
				'std'      => '',
				'desc'     => '',
				'style'    => '',
				'name'     => 'Checkbox List Field',
				'options'  => $options,
				'class'    => '',
				'sortable' => false,
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addPostTypes( $id, $options = array(), $args, $repeater = false ) {
			$new_field = array(
				'type'     => 'checkbox_list',
				'id'       => $id,
				'std'      => '',
				'desc'     => '',
				'style'    => '',
				'name'     => 'Checkbox List Field',
				'options'  => $options,
				'class'    => '',
				'sortable' => false,
				'special'  => 'post-types'
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addTextarea( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'  => 'textarea',
				'id'    => $id,
				'std'   => '',
				'desc'  => '',
				'style' => '',
				'name'  => 'Textarea Field'
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addSelect( $id, $options, $args, $repeater = false ) {
			$new_field = array(
				'type'     => 'select',
				'id'       => $id,
				'std'      => array(),
				'desc'     => '',
				'style'    => '',
				'name'     => 'Select Field',
				'multiple' => false,
				'options'  => $options
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addSortable( $id, $options, $args, $repeater = false ) {
			$new_field = array(
				'type'     => 'sortable',
				'id'       => $id,
				'std'      => array(),
				'desc'     => '',
				'style'    => '',
				'name'     => 'Select Field',
				'multiple' => false,
				'options'  => $options
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addRadio( $id, $options, $args, $repeater = false ) {
			$new_field = array(
				'type'     => 'radio',
				'id'       => $id,
				'std'      => array(),
				'desc'     => '',
				'style'    => '',
				'name'     => 'Radio Field',
				'options'  => $options,
				'multiple' => false
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addDate( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'   => 'date',
				'id'     => $id,
				'std'    => '',
				'desc'   => '',
				'format' => 'd MM, yy',
				'name'   => 'Date Field'
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addTime( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'   => 'time',
				'id'     => $id,
				'std'    => '',
				'desc'   => '',
				'format' => 'hh:mm',
				'name'   => 'Time Field'
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addColor( $id, $args, $repeater = false ) {
			$new_field = array(
				'type' => 'color',
				'id'   => $id,
				'std'  => '',
				'desc' => '',
				'name' => 'ColorPicker Field'
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addImage( $id, $args, $repeater = false ) {
			$new_field = array( 'type' => 'image', 'id' => $id, 'desc' => '', 'name' => 'Image Field' );
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addButton( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'    => 'button',
				'id'      => $id,
				'desc'    => '',
				'name'    => 'Button',
				'caption' => 'Button Caption'
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addWysiwyg( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'  => 'wysiwyg',
				'id'    => $id,
				'std'   => '',
				'desc'  => '',
				'style' => 'width: 300px; height: 400px',
				'name'  => 'WYSIWYG Editor Field'
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addTaxonomy( $id, $options, $args, $repeater = false ) {
			$temp      = array( 'taxonomy' => 'category', 'type' => 'select', 'args' => array( 'hide_empty' => 0 ) );
			$options   = array_merge( $temp, $options );
			$new_field = array(
				'type'     => 'taxonomy',
				'id'       => $id,
				'desc'     => '',
				'name'     => 'Taxonomy Field',
				'options'  => $options,
				'multiple' => false
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addRoles( $id, $options, $args, $repeater = false ) {
			$options   = array_merge( array( 'type' => 'select' ), $options );
			$new_field = array(
				'type'     => 'WProle',
				'id'       => $id,
				'desc'     => '',
				'name'     => 'WP Roles Field',
				'options'  => $options,
				'multiple' => false
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addPosts( $id, $options, $args, $repeater = false ) {
			$temp      = array( 'type' => 'select', 'args' => array( 'posts_per_page' => - 1, 'post_type' => 'post' ) );
			$options   = array_merge( $temp, $options );
			$new_field = array(
				'type'     => 'posts',
				'id'       => $id,
				'desc'     => '',
				'name'     => 'Posts Field',
				'options'  => $options,
				'multiple' => false
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addRepeaterBlock( $id, $args ) {
			$new_field       = array(
				'type'     => 'repeater',
				'id'       => $id,
				'name'     => 'Reapeater Field',
				'fields'   => array(),
				'inline'   => false,
				'sortable' => false
			);
			$new_field       = array_merge( $new_field, $args );
			$this->_fields[] = $new_field;
		}


		/*
     * Other Utility Functions
     */

		public function Finish() {
			/*$this->add_missed_values();
        $this->check_field_upload();
        $this->check_field_color();
        $this->check_field_date();
        $this->check_field_time();
        $this->check_field_code();*/
		}

		public function is_array_empty( $array ) {
			if ( ! is_array( $array ) ) {
				return true;
			}

			foreach ( $array as $a ) {
				if ( is_array( $a ) ) {
					foreach ( $a as $sub_a ) {
						if ( ! empty( $sub_a ) && $sub_a != '' ) {
							return false;
						}
					}
				} else {
					if ( ! empty( $a ) && $a != '' ) {
						return false;
					}
				}
			}

			return true;
		}

		public function get_fonts_family( $font = null ) {
			$fonts = get_option( 'WP_EX_FONTS_LIST', $default = false );
			if ( $fonts === false ) {
				$fonts = array(
					'arial'     => array(
						'name' => 'Arial',
						'css'  => "font-family: Arial, sans-serif;",
					),
					'verdana'   => array(
						'name' => "Verdana, Geneva",
						'css'  => "font-family: Verdana, Geneva;",
					),
					'trebuchet' => array(
						'name' => "Trebuchet",
						'css'  => "font-family: Trebuchet;",
					),
					'georgia'   => array(
						'name' => "Georgia",
						'css'  => "font-family: Georgia;",
					),
					'times'     => array(
						'name' => "Times New Roman",
						'css'  => "font-family: Times New Roman;",
					),
					'tahoma'    => array(
						'name' => "Tahoma, Geneva",
						'css'  => "font-family: Tahoma, Geneva;",
					),
					'palatino'  => array(
						'name' => "Palatino",
						'css'  => "font-family: Palatino;",
					),
					'helvetica' => array(
						'name' => "Verdana, Geneva",
						'css'  => "font-family: Helvetica*;",
					),
				);
				if ( $this->google_fonts ) {
					$api_keys = array(
						'AIzaSyDXgT0NYjLhDmUzdcxC5RITeEDimRmpq3s',
						'AIzaSyD6j7CsUTblh29PAXN3NqxBjnN-5nuuFGU',
						'AIzaSyB8Ua6XIfe-gqbkE8P3XL4spd0x8Ft7eWo',
						'AIzaSyDJYYVPLT9JaoMPF8G5cFm1YjTZMjknizE',
						'AIzaSyDXt6e2t_gCfhlSfY8ShpR9WpqjMsjEimU'
					);
					$k        = rand( 0, count( $api_keys ) - 1 );
					$gs       = wp_remote_get( 'https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity&key=' . $api_keys[ $k ], array( 'sslverify' => false ) );
					if ( ! is_wp_error( $gs ) ) {
						$fontsSeraliazed = $gs['body'];
						$fontArray       = json_decode( $gs['body'] );
						$fontArray       = $fontArray->items;
						foreach ( $fontArray as $f ) {
							$key           = strtolower( str_replace( " ", "_", $f->family ) );
							$fonts[ $key ] = array(
								'name'   => $f->family,
								'import' => str_replace( " ", "+", $f->family ),
								'css'    => 'font-family: ' . $f->family . ';',
								//@import url(http://fonts.googleapis.com/css?family=
							);
						}
					}
				}
				update_option( 'WP_EX_FONTS_LIST', $fonts );
			}
			$fonts = apply_filters( 'WP_EX_available_fonts_family', $fonts );
			if ( $font === null ) {
				return $fonts;
			} else {
				foreach ( $fonts as $f => $value ) {
					if ( $f == $font ) {
						return $value;
					}
				}
			}
		}

		public function get_font_style() {
			$default = array(
				'normal'   => 'Normal',
				'italic'   => 'Italic',
				'oblique ' => 'Oblique'
			);

			return apply_filters( 'BF_available_fonts_style', $default );
		}

		public function get_font_weight() {
			$default = array(
				'normal'  => 'Normal',
				'bold'    => 'Bold',
				'bolder'  => 'Bolder',
				'lighter' => 'Lighter',
				'100'     => '100',
				'200'     => '200',
				'300'     => '300',
				'400'     => '400',
				'500'     => '500',
				'600'     => '600',
				'700'     => '700',
				'800'     => '800',
				'900'     => '900',
				'inherit' => 'Inherit'
			);

			return apply_filters( 'BF_available_fonts_weights', $default );
		}

		/*
	 * Misc Utility Functions
	 */
		public function addNonce( $id, $args, $repeater = false ) {
			$new_field       = array( 'type' => 'nonce', 'id' => $id, 'value' => '' );
			$this->_fields[] = $new_field;
		}

		public function show_field_nonce( $field, $meta ) {
			echo '<input type="hidden" id="' . $field['id'] . '_nonce" name="' . $field['id'] . '_nonce" value="' . wp_create_nonce( $field['id'] . '_nonce' ) . '" />';
		}

		public function addUid( $id, $args, $repeater = false ) {
			$new_field = array( 'type' => 'uid', 'id' => $id, 'value' => '' );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function show_field_uid( $field, $meta ) {
			echo '<input type="hidden" id="' . $field['id'] . '" name="' . $field['id'] . '" value="' . esc_attr( stripslashes( $meta ) ) . '" class="at-field-uuid" />';
		}

		/**
		 *  Export Import Functions
		 */

		/**
		 *  Add import export to Page
		 *
		 * @author Ohad Raz
		 * @since  0.8
		 * @access public
		 *
		 * @return void
		 */

		public function addImportExport() {
			$new_field       = array( 'type' => 'import_export', 'id' => '', 'value' => '' );
			$this->_fields[] = $new_field;
		}

		public function show_import_export() {
			$this->show_field_begin( array( 'name' => '' ), null );
			$ret = '
    <div class="apc_ie_panel field">
      <div style="padding 10px;" class="apc_export"><h3>' . esc_html__( 'Export', 'inbound' ) . '</h3>
        <p>' . inbound_esc_html( __( 'Click the <code>Get Export Code</code> button below to retrieve the latest settings from the database. The export text can be copied and pasted to the import box of another installation, or to restore a previous state.', 'inbound' ) ) . '</p>
        <div class="export_code">
          <label for="export_code">' . esc_html__( 'Export Code', 'inbound' ) . '</label>
          <textarea id="export_code"></textarea>
          <a class="button-primary" id="apc_export_b" src="javascript:void(0);">' . esc_html__( 'Get Export Code', 'inbound' ) . '</a> ' . $this->create_export_download_link() . '
          <div class="export_status" style="display: none;"><span>' . esc_html__( 'Loading...', 'inbound' ) . '</span></div>
          <div class="export_results alert" style="display: none;"></div>
        </div>
      </div>
      <div style="padding 10px;" class="apc_import"><h3>' . esc_html__( 'Import', 'inbound' ) . '</h3>
        <p>' . inbound_esc_html( __( 'To import saved settings, paste the export output in to the <code>Import Code</code> box below and click <code>Import</code>.', 'inbound' ) ) . '</p>
        <div class="import_code">
          <label for="import_code">' . esc_html__( 'Import Code', 'inbound' ) . '</label>
          <textarea id="import_code"></textarea>
                  <input class="button-primary" type="button"  value="' . esc_html__( 'Import', 'inbound' ) . '" id="apc_import_b" />
                  <input class="button-secondary" type="button"  value="' . esc_html__( 'Factory Defaults', 'inbound' ) . '" id="apc_import_defaults" />
          <div class="import_status" style="display: none;"><span>' . esc_html__( 'Loading...', 'inbound' ) . '</span></div>
          <div class="import_results alert" style="display: none;"></div>
        </div>
      </div>
      <input type="hidden" id="option_group_name" value="' . $this->option_group . '" />
      <input type="hidden" id="apc_import_nonce" name="apc_Import" value="' . wp_create_nonce( "apc_import" ) . '" />
      <input type="hidden" id="apc_export_nonce" name="apc_export" value="' . wp_create_nonce( "apc_export" ) . '" />
      <input type="hidden" id="apc_defaults_nonce" name="apc_export" value="' . wp_create_nonce( "apc_defaults" ) . '" />
    ';
			echo apply_filters( 'apc_import_export_panel', $ret );
			$this->show_field_end( array( 'name' => '', 'desc' => '' ), null );
		}

		/**
		 * Ajax export
		 *
		 * @author Ohad   Raz
		 * @since  0.8
		 * @access public
		 *
		 * @return json object
		 */
		public function export() {
			check_ajax_referer( 'apc_export', 'seq' );
			if ( ! isset( $_GET['group'] ) ) {
				$re['err']   = esc_html__( 'error in ajax request! (1)', 'inbound' );
				$re['nonce'] = wp_create_nonce( "apc_export" );
				echo json_encode( $re );
				die();
			}

			$options = get_option( $this->option_group, false );
			if ( $options !== false ) {
				$re['code'] = json_encode( $options );
			} else {
				$re['err'] = esc_html__( 'error in ajax request! (2)', 'inbound' );
			}

			//update_nonce
			$re['nonce'] = wp_create_nonce( "apc_export" );
			echo json_encode( $re );
			die();

		}

		public function defaults() {
			check_ajax_referer( 'apc_defaults', 'seq' );

			if ( ! isset( $_GET['group'] ) ) {
				$re['err']   = esc_html__( 'error in ajax request! (1)', 'inbound' );
				$re['nonce'] = wp_create_nonce( "apc_defaults" );
				echo json_encode( $re );
				die();
			}

			$re['code'] = inbound_file_read_contents( get_template_directory() . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'options.json' );

			//update_nonce
			$re['nonce'] = wp_create_nonce( "apc_defaults" );
			echo json_encode( $re );
			die();

		}


		/**
		 * Ajax import
		 *
		 * @author Ohad   Raz
		 * @since  0.8
		 * @access public
		 *
		 * @return json object
		 */
		public function import() {
			check_ajax_referer( 'apc_import', 'seq' );
			if ( ! isset( $_POST['imp'] ) ) {
				$re['err']   = esc_html__( 'error in ajax request! (3)', 'inbound' );
				$re['nonce'] = wp_create_nonce( "apc_import" );
				echo json_encode( $re );
				die();
			}
			$import_code = trim( stripslashes( $_POST['imp'] ) );
			$import_code = json_decode( $import_code, true );
			if ( is_array( $import_code ) ) {
				update_option( $this->option_group, $import_code );
				$re['success'] = esc_html__( 'Settings successfully imported. Make sure you ', 'inbound' ) . '<input class="button-primary" type="button"  value="' . esc_html__( 'Refresh this page', 'inbound' ) . '" id="apc_refresh_page_b" />';
			} else {
				$re['err'] = esc_html__( 'Could not import settings! (4)', 'inbound' );
			}
			//update_nonce
			$re['nonce'] = wp_create_nonce( "apc_import" );
			echo json_encode( $re );
			die();
		}


		//then define the function that will take care of the actual download
		public function download_file( $content = null, $file_name = null ) {
			if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'theme_export_options' ) ) {
				wp_die( 'Security check' );
			}

			//here you get the options to export and set it as content, ex:
			$options   = get_option( $_REQUEST['option_group'] );
			$content   = json_encode( $options );
			$file_name = apply_filters( 'apc_theme_export_filename', 'options.txt' );
			header( 'HTTP/1.1 200 OK' );

			if ( ! current_user_can( 'edit_themes' ) ) {
				wp_die( '<p>' . esc_html__( 'You do not have sufficient permissions to edit templates for this site.', 'inbound' ) . '</p>' );
			}

			if ( $content === null || $file_name === null ) {
				wp_die( '<p>' . esc_html__( 'Error Downloading file.', 'inbound' ) . '</p>' );
			}
			$fsize = strlen( $content );
			header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
			header( 'Content-Description: File Transfer' );
			header( "Content-Disposition: attachment; filename=" . $file_name );
			header( "Content-Length: " . $fsize );
			header( "Expires: 0" );
			header( "Pragma: public" );
			echo $content;
			exit;
		}

		public function create_export_download_link( $echo = false ) {
			$site_url   = home_url('/');
			$args       = array(
				'theme_export_options' => 'safe_download',
				'nonce'                => wp_create_nonce( 'theme_export_options' ),
				'option_group'         => $this->option_group
			);
			$export_url = add_query_arg( $args, $site_url );
			if ( $echo === true ) {
				echo '<a href="' . esc_url($export_url) . '" target="_blank">' . esc_html__( 'Download', 'inbound' ) . '</a>';
			} elseif ( $echo == 'url' ) {
				return $export_url;
			}

			return '<a class="button-primary" href="' . esc_url($export_url) . '" target="_blank">' . esc_html__( 'Download', 'inbound' ) . '</a>';
		}

		//first  add a new query var
		public function add_query_var_vars() {
			global $wp;
			$wp->add_query_var( 'theme_export_options' );
		}

		//then add a template redirect which looks for that query var and if found calls the download function
		public function admin_redirect_download_files() {
			global $wp;
			global $wp_query;
			//download theme export
			if ( array_key_exists( 'theme_export_options', $wp->query_vars ) && $wp->query_vars['theme_export_options'] == 'safe_download' && $this->option_group == $_REQUEST['option_group'] ) {
				$this->download_file();
				die();
			}
		}


		/*
	 * Form Code Cleanup
	 */
		public function clean_form() {
			check_ajax_referer( 'clean_form_nonce', 'seq' );
			if ( ! isset( $_POST['group'] ) || empty( $_REQUEST['code'] ) ) {
				$re['err']   = esc_html__( 'error in ajax request! (1)', 'inbound' );
				$re['nonce'] = wp_create_nonce( "clean_form_nonce" );
				echo json_encode( $re );
				die();
			}

			$code = '';

			$response = wp_remote_post( SR_OPTIN_API_URL, array(
					'method'      => 'POST',
					'timeout'     => 15,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking'    => true,
					'headers'     => array(),
					'body'        => array( 'code' => stripslashes( $_REQUEST['code'] ) ),
					'cookies'     => array()
				)
			);

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				$re['err']     = "Something went wrong: $error_message";
				$re['nonce']   = wp_create_nonce( "clean_form_nonce" );
				echo json_encode( $re );
				die();
			} else {
				//update_nonce
				$code         = json_decode( $response['body'] );
				$code         = $code->code;
				$re           = array();
				$re['nonce']  = wp_create_nonce( "clean_form_nonce" );
				$re['code']   = $code;
				$re['status'] = (int) 200;
				echo json_encode( $re );
				die();
			}
		}


		/*
	 * Fonts Database Refresh
	 */
		public function fonts_refresh() {
			check_ajax_referer( 'web_fonts_refresh_nonce', 'seq' );
			if ( ! isset( $_POST['group'] ) ) {
				$re['err']   = esc_html__( 'error in ajax request! (1)', 'inbound' );
				$re['nonce'] = wp_create_nonce( "web_fonts_refresh_nonce" );
				echo json_encode( $re );
				die();
			}

			// refresh fonts
			if ( class_exists( 'SR_Typography' ) ) {
				$typo = new SR_Typography();
				$typo->get_fonts( null, true );
			}

			//update_nonce
			$re            = array();
			$re['nonce']   = wp_create_nonce( "web_fonts_refresh_nonce" );
			$re['success'] = esc_html__( 'The fonts database has been refreshed.', 'inbound' );
			$re['status']  = (int) 200;
			echo json_encode( $re );
			die();
		}

		/**
		 * Validation functions
		 */

		public function validate_field( $field, $meta ) {
			if ( ! isset( $field['validate'] ) || ! is_array( $field['validate'] ) ) {
				return true;
			}

			$ret = true;
			foreach ( $field['validate'] as $type => $args ) {
				if ( method_exists( $this, 'is_' . $type ) ) {
					if ( call_user_func( array( $this, 'is_' . $type ), $meta, $args['param'] ) === false ) {
						$this->errors_flag                    = true;
						$this->errors[ $field['id'] ]['name'] = $field['name'];
						$this->errors[ $field['id'] ]['m'][]  = ( isset( $args['message'] ) ? $args['message'] : esc_html__( 'Not Valid ', 'inbound' ) . $type );
						$ret                                  = false;
					}
				}
			}

			return $ret;
		}

		public function displayErrors() {
			if ( $this->errors_flag ) {
				echo '<h4>' . esc_html__( 'Errors in saving changes', 'inbound' ) . '</h4>';
				foreach ( $this->errors as $id => $arr ) {
					echo "<strong>{$arr['name']}</strong>: ";
					foreach ( $arr['m'] as $m ) {
						echo "<br />&nbsp;&nbsp;&nbsp;&nbsp;{$m}";
					}
					echo '<br />';
				}
			}
		}

		public function getFieldErrors( $field_id ) {
			if ( $this->errors_flag ) {
				if ( isset( $this->errors[ $field_id ] ) ) {
					return $this->errors[ $field_id ];
				}
			}

			return esc_html__( 'Unknown Error', 'inbound' );
		}

		public function has_error( $field_id ) {
			//exit if not saved or no validation errors
			if ( ! $this->saved_flag || ! $this->errors_flag ) {
				return false;
			}
			//check if this field has validation errors
			if ( isset( $this->errors[ $field_id ] ) ) {
				return true;
			}

			return false;
		}

		public function is_email( $val ) {
			return (bool) ( preg_match( "/^([a-z0-9+_-]+)(.[a-z0-9+_-]+)*@([a-z0-9-]+.)+[a-z]{2,6}$/ix", $val ) );
		}

		public function is_numeric( $val ) {
			return is_numeric( $val );
		}

		public function is_minvalue( $number, $max ) {
			return (bool) ( (int) $number > (int) $max );
		}

		public function is_maxvalue( $number, $max ) {
			return ( (int) $number < (int) $max );
		}

		public function is_minlength( $val, $min ) {
			return ( strlen( $val ) >= (int) $min );
		}

		public function is_maxlength( $val, $max ) {
			return ( strlen( $val ) <= (int) $max );
		}

		public function is_length( $val, $length ) {
			return ( strlen( $val ) == (int) $length );
		}

		public function is_url( $val ) {
			return (bool) preg_match( '|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $val );
		}

		public function is_alphanumeric( $val ) {
			return (bool) preg_match( "/^([a-zA-Z0-9])+$/i", $val );
		}


	} // End Class

endif; // End Check Class Exists