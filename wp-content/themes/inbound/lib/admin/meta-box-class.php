<?php
/**
 * Custom Meta Box Class
 *
 * This class is derived from Meta Box script by Rilwis <rilwis@gmail.com> version 3.2. which later was forked
 * by Cory Crowley <cory.ivan@gmail.com>; later extended upon by Ohad Raz and adapted in 2013
 * for ShapingRain.
 *
 * @license GNU General Public LIcense v3.0 - license.txt
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

if ( ! class_exists( 'SR_Meta_Box' ) ) :
	class SR_Meta_Box {

		protected $_meta_box;
		protected $_prefix;
		protected $_fields;
		protected $_Local_images;
		protected $SelfPath;
		public $field_types = array();
		public $inGroup = false;

		public function __construct( $meta_box ) {
			if ( ! is_admin() ) {
				return;
			}

			// Assign meta box values to local variables and add it's missed values.
			$this->_meta_box     = $meta_box;
			$this->_prefix       = ( isset( $meta_box['prefix'] ) ) ? $meta_box['prefix'] : '';
			$this->_fields       = $this->_meta_box['fields'];
			$this->_Local_images = ( isset( $meta_box['local_images'] ) ) ? true : false;
			$this->add_missed_values();
			if ( isset( $meta_box['use_with_theme'] ) ) {
				if ( $meta_box['use_with_theme'] === true ) {
					$this->SelfPath = get_stylesheet_directory_uri() . '/meta-box-class';
				} elseif ( $meta_box['use_with_theme'] === false ) {
					$this->SelfPath = plugins_url( 'meta-box-class', plugin_basename( dirname( __FILE__ ) ) );
				} else {
					$this->SelfPath = $meta_box['use_with_theme'];
				}
			} else {
				$this->SelfPath = plugins_url( 'meta-box-class', plugin_basename( dirname( __FILE__ ) ) );
			}

			// Add metaboxes
			add_action( 'add_meta_boxes', array( $this, 'add' ) );
			//add_action( 'wp_insert_post', array( $this, 'save' ) );
			add_action( 'save_post', array( $this, 'save' ) );
			// Load common js, css files
			// Must enqueue for all pages as we need js for the media upload, too.
			add_action( 'admin_print_styles', array( $this, 'load_scripts_styles' ) );
			//limit File type at upload
			add_filter( 'wp_handle_upload_prefilter', array( $this, 'Validate_upload_file_type' ) );
		}

		/**
		 * Load all Javascript and CSS
		 *
		 * @since  1.0
		 * @access public
		 */
		public function load_scripts_styles() {

			// Get Plugin Path
			$plugin_path = $this->SelfPath;


			//only load styles and js when needed
			/*
			 * since 1.8
			 */
			global $typenow;
			if ( in_array( $typenow, $this->_meta_box['pages'] ) && $this->is_edit_page() ) {
				// Enqueue Meta Box Style
				wp_enqueue_style( 'at-meta-box', $plugin_path . '/css/meta-box.css' );

				// Tooltips
				wp_enqueue_script( 'frosty', $plugin_path . '/js/frosty.min.js', array( 'jquery' ), null, true );

				// Enqueue Meta Box Scripts
				wp_enqueue_script( 'at-meta-box', $plugin_path . '/js/meta-box.js', array( 'jquery' ), null, true );

				// Make upload feature work event when custom post type doesn't support 'editor'
				if ( $this->has_field( 'image' ) || $this->has_field( 'file' ) || $this->has_field( 'gallery' ) ) {
					wp_enqueue_script( 'media-upload' );
					add_thickbox();
					wp_enqueue_script( 'jquery-ui-core' );
					wp_enqueue_script( 'jquery-ui-sortable' );
				}

				// Check for special fields and add needed actions for them.
				if ( $this->has_field( 'typography' ) && $this->is_edit_page() ) {
					add_action( 'admin_head', array( $this, 'add_typography_data' ) );
					wp_enqueue_script( 'at-chosen', $plugin_path . '/js/chosen/chosen.jquery.min.js', array( 'jquery' ), null, true );
					wp_enqueue_style( 'at-chosen', $plugin_path . '/js/chosen/chosen.min.css' );
				}

				//this replaces the ugly check fields methods calls
				foreach ( array( 'upload', 'color', 'date', 'time', 'code', 'select', 'image', 'gallery' ) as $type ) {
					call_user_func( array( $this, 'check_field_' . $type ) );
				}
			}

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
			echo json_encode( $fonts_export ) . ";\n" . 'var font_picker_select = "' . esc_attr( esc_html__( 'Update', 'inbound' ) ) . '";' . "</script>\n";
		}

		/**
		 * Check the Field select, Add needed Actions
		 *
		 * @since  2.9.8
		 * @access public
		 */
		public function check_field_select() {

			// Check if the field is an image or file. If not, return.
			if ( ! $this->has_field( 'select' ) ) {
				return;
			}
			$plugin_path = $this->SelfPath;
		}

		/**
		 * Check the Field Upload, Add needed Actions
		 *
		 * @since  1.0
		 * @access public
		 */
		public function check_field_upload() {

			// Check if the field is an image or file. If not, return.
			if ( ! $this->has_field( 'image' ) && ! $this->has_field( 'file' ) ) {
				return;
			}

			// Add data encoding type for file uploading.
			add_action( 'post_edit_form_tag', array( $this, 'add_enctype' ) );

		}

		/**
		 * Add data encoding type for file uploading
		 *
		 * @since  1.0
		 * @access public
		 */
		public function add_enctype() {
			printf( ' enctype="multipart/form-data" encoding="multipart/form-data" ' );
		}

		/**
		 * Check Field Color
		 *
		 * @since  1.0
		 * @access public
		 */
		public function check_field_color() {

			if ( $this->has_field( 'color' ) && $this->is_edit_page() ) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
			}
		}


		/**
		 * Check Field Date
		 *
		 * @since  1.0
		 * @access public
		 */
		public function check_field_date() {

			if ( $this->has_field( 'date' ) && $this->is_edit_page() ) {
				// Enqueue JQuery UI, use proper version.
				$plugin_path = $this->SelfPath;
				wp_enqueue_style( 'at-jquery-ui-css', $plugin_path . '/js/jquery-ui/jquery-ui.css' );
				wp_enqueue_script( 'jquery-ui' );
				wp_enqueue_script( 'jquery-ui-datepicker' );
			}
		}

		/**
		 * Check Field Time
		 *
		 * @since  1.0
		 * @access public
		 */
		public function check_field_time() {

			if ( $this->has_field( 'time' ) && $this->is_edit_page() ) {
				$plugin_path = $this->SelfPath;
				// Enqueu JQuery UI, use proper version.
				wp_enqueue_style( 'at-jquery-ui-css', $plugin_path . '/js/jquery-ui/jquery-ui.css' );
				wp_enqueue_script( 'jquery-ui' );
				wp_enqueue_script( 'at-timepicker', $plugin_path . '/js/jquery-ui/jquery-ui-timepicker-addon.js', array(
					'jquery-ui-slider',
					'jquery-ui-datepicker'
				), false, true );
			}
		}

		/**
		 * Check Field code editor
		 *
		 * @since  2.1
		 * @access public
		 */
		public function check_field_code() {
			if ( $this->has_field( 'code' ) && $this->is_edit_page() ) {
				$plugin_path = $this->SelfPath;
			}
		}

		public function check_field_image() {
			wp_enqueue_media();
			wp_enqueue_script( 'media-upload' );
		}

		public function check_field_gallery() {
			wp_enqueue_media();
			wp_enqueue_script( 'media-upload' );
		}

		/**
		 * Add Meta Box for multiple post types.
		 *
		 * @since  1.0
		 * @access public
		 */
		public function add( $postType ) {
			if ( in_array( $postType, $this->_meta_box['pages'] ) ) {
				add_meta_box( $this->_meta_box['id'], $this->_meta_box['title'], array(
					$this,
					'show'
				), $postType, $this->_meta_box['context'], $this->_meta_box['priority'] );
			}
		}

		/**
		 * Callback function to show fields in meta box.
		 *
		 * @since  1.0
		 * @access public
		 */
		public function show() {
			$this->inGroup = false;
			global $post;

			wp_nonce_field( basename( __FILE__ ), 'sr_meta_box_nonce' );
			echo '<table class="form-table">';
			$field_count = 0;
			$field_max   = count( $this->_fields );
			foreach ( $this->_fields as $field ) {
				$field_count ++;
				if ( $field_count == $field_max ) {
					$field['last'] = true;
				}
				$field['multiple'] = isset( $field['multiple'] ) ? $field['multiple'] : false;
				$field['post_id']  = $post->ID;
				$meta              = get_post_meta( $post->ID, $field['id'], ! $field['multiple'] );
				$meta              = ( $meta !== '' ) ? $meta : @$field['std'];

				if ( ! in_array( $field['type'], array( 'image', 'repeater', 'file' ) ) ) {
					$meta = is_array( $meta ) ? array_map( 'esc_attr', $meta ) : esc_attr( $meta );
				}

				$group_class = '';
				$group_data  = '';
				if ( isset( $field['is-group'] ) && isset( $field['group-value'] ) ) {
					$group_class = ' class="is-group group-' . $field['is-group'] . '"';
					$group_data  = ' data-group="' . $field['is-group'] . '"';
					$group_data .= " data-group-value='" . json_encode( $field['group-value'] ) . "'";
				}

				if ( $this->inGroup !== true ) {
					echo '<tr' . $group_class . $group_data . '>';
				}

				if ( isset( $field['group'] ) && $field['group'] == 'start' ) {
					$this->inGroup = true;
					echo '<td><table class="form-table"><tr' . $group_class . $group_data . '>';
				}

				if ( isset( $field['group-selector'] ) && $field['group-selector'] == true ) {
					if ( isset( $field['class'] ) ) {
						$field['class'] .= ' group-selector';
					} else {
						$field['class'] = 'group-selector';
					}
				}

				// Call Separated methods for displaying each type of field.
				call_user_func( array( $this, 'show_field_' . $field['type'] ), $field, $meta );

				if ( $this->inGroup === true ) {
					if ( isset( $field['group'] ) && $field['group'] == 'end' ) {
						echo '</tr></table></td></tr>';
						$this->inGroup = false;
					}
				} else {
					echo '</tr>';
				}
			}
			echo '</table>';
		}

		public function show_field_repeater( $field, $meta ) {
			global $post;

			// Get Plugin Path
			$plugin_path = $this->SelfPath;
			$this->show_field_begin( $field, $meta );
			$class = '';
			if ( $field['sortable'] ) {
				$class = " repeater-sortable";
			}
			$jsid = ltrim( strtolower( str_replace( ' ', '', $field['id'] ) ), '0123456789' );
			echo "<div class='at-repeat" . $class . "' id='{$jsid}'>";

			$c    = 0;
			$meta = get_post_meta( $post->ID, $field['id'], true );

			if ( isset( $field['title'] ) && ! empty( $field['title'] ) ) {
				$title_field = $field['title'];
			} else {
				$title_field = '';
			}

			$block_id = $field['id'];


			if ( count( $meta ) > 0 && is_array( $meta ) ) {
				foreach ( $meta as $me ) {
					//for labeling toggles
					$mmm = isset( $me[ $field['fields'][0]['id'] ] ) ? $me[ $field['fields'][0]['id'] ] : "";
					$mmm = ( in_array( $field['fields'][0]['type'], array( 'image', 'file' ) ) ? '' : $mmm );

					// if specific title field is set, use that one to display block title
					if ( ! empty( $title_field ) && isset( $me[ $title_field ] ) ) {
						$mmm = $me[ $title_field ];
					}

					echo '<div class="at-repeater-block" data-field-prefix="' . $block_id . '[' . $c . ']">';
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
						$f['is_repeater'] = true;
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

			echo '<div class="at-repeater-block at-repeater-new-item" data-field-prefix="' . $block_id . '[CurrentCounter]">';
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

			$this->show_field_end( $field, $meta );
		}

		public function show_field_begin( $field, $meta ) {
			if ( isset( $field['is_repeater'] ) ) {
				$repeater = true;
			} else {
				$repeater = false;
			}

			if ( $repeater ) {
				if ( $field['name'] != '' || $field['name'] != false ) {
					echo "<div class='at-label'><label for='{$field['id']}'>" . esc_html($field['name']);
					if ( isset( $field['desc'] ) && $field['desc'] != '' ) {
						echo ' <span class="label-desc has-tip tip-right" title="' . esc_attr( $field['desc'] ) . '">?</span>';
					}
					echo "</label></div>";
				}
			} else {
				if ( isset( $field['label_location'] ) && $field['label_location'] == 'top' ) {
					echo "<td colspan='2' class='at-field" . ( isset( $field['last'] ) ? ' at-field-last' : '' ) . "'" . ( ( $this->inGroup === true ) ? " valign='top'" : "" ) . ">";
					if ( $field['name'] != '' || $field['name'] != false ) {
						echo "<div class='at-label'>";
						echo "<label for='{$field['id']}'>" . esc_html($field['name']);
						if ( isset( $field['desc'] ) && $field['desc'] != "" ) {
							echo '<span class="label-desc has-tip tip-right" title="' . esc_attr( $field['desc'] ) . '">?</span>';
						}
						echo "</label>";
						echo "</div>";
					}
				} elseif ( isset( $field['label_location'] ) && $field['label_location'] == 'none' ) {
					echo "<td colspan='2' class='at-field" . ( isset( $field['last'] ) ? ' at-field-last' : '' ) . "'" . ( ( $this->inGroup === true ) ? " valign='top'" : "" ) . ">";
				} else {
					echo "<td colspan='1' class='at-field" . ( isset( $field['last'] ) ? ' at-field-last' : '' ) . "'" . ( ( $this->inGroup === true ) ? " valign='top'" : "" ) . " width=\"25%\">";
					if ( $field['name'] != '' || $field['name'] != false ) {
						echo "<div class='at-label'>";
						echo "<label for='{$field['id']}'>" . esc_html( $field['name'] );
						if ( isset( $field['desc'] ) && $field['desc'] != "" ) {
							echo '<span class="label-desc has-tip tip-right" title="' . esc_attr( $field['desc'] ) . '">?</span>';
						}
						echo "</label>";
						echo "</div>";
					}
					echo "</td>";
					echo "<td class='at-field" . ( isset( $field['last'] ) ? ' at-field-last' : '' ) . "'" . ( ( $this->inGroup === true ) ? " valign='top'" : "" ) . ">";

					//print description
					if ( isset( $field['desc'] ) && $field['desc'] != '' ) {
						if ( isset( $field['desc_position'] ) && $field['desc_position'] == 'above' ) {
							echo "<div class='desc-field'>" . esc_html ( $field['desc'] ) . "</div>";
						}
					}

				}

			}
		}

		/**
		 * End Field.
		 *
		 * @param string $field
		 * @param string $meta
		 *
		 * @since  1.0
		 * @access public
		 */
		public function show_field_end( $field, $meta = null, $group = false ) {

			if ( isset( $field['is_repeater'] ) ) {
				$repeater = true;
			} else {
				$repeater = false;
			}

			if ( $repeater ) {

			} else {
				//print description
				if ( isset( $field['desc'] ) && $field['desc'] != '' ) {
					if ( isset( $field['desc_position'] ) && $field['desc_position'] == 'above' ) {
						// do nothing, description is displayed above
					} else {
						// echo "<div class='desc-field'>{$field['desc']}</div>";
					}
				}
				echo "</td>";
			}
		}


		public function show_field_icon( $field, $meta ) {
			$this->show_field_begin( $field, $meta );
			$id_sanitized = trim( str_replace( array( '[', ']' ), "_", $field['id'] ), '_' );
			echo "<input type='text' class='at-text" . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' ) . "' name='{$field['id']}' id='{$id_sanitized}' value='{$meta}' size='" . ( isset( $field['size'] ) ? $field['size'] : '30' ) . "' " . ( isset( $field['style'] ) ? "style='{$field['style']}'" : '' ) . "/>";
			echo '<input type="button" class="button fontawesome-picker" data-target="#' . esc_attr( $id_sanitized ) . '" id="' . 'picker_' . esc_attr( $id_sanitized ) . '" value="' . esc_html__( 'Select Icon', 'inbound' ) . '">';
			$this->show_field_end( $field, $meta );
		}

		/**
		 * Show Section Headline
		 *
		 */
		public function show_field_section( $field, $meta ) {
			$this->show_field_begin( $field, $meta );
			echo '<h4>' . $field['name'] . '</h4>';
			$this->show_field_end( $field, $meta );
		}


		/**
		 * Show Field Text.
		 *
		 * @param string $field
		 * @param string $meta
		 *
		 * @since  1.0
		 * @access public
		 */
		public function show_field_text( $field, $meta ) {
			$this->show_field_begin( $field, $meta );
			echo "<input type='text' class='at-text" . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' ) . "' name='{$field['id']}' id='{$field['id']}' value='{$meta}' size='" . ( isset( $field['size'] ) ? $field['size'] : '30' ) . "' " . ( isset( $field['style'] ) ? "style='{$field['style']}'" : '' ) . "/>";
			$this->show_field_end( $field, $meta );
		}

		/**
		 * Show Field Typography.
		 *
		 * @param string $field
		 * @param string $meta
		 *
		 * @since  1.0
		 * @access public
		 */
		public function show_field_typography( $field, $meta ) {
			$this->show_field_begin( $field, $meta );
			$html = '';

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


		/**
		 * Show Field number.
		 *
		 * @param string $field
		 * @param string $meta
		 *
		 * @since  1.0
		 * @access public
		 */
		public function show_field_number( $field, $meta ) {
			$this->show_field_begin( $field, $meta );
			$step = ( isset( $field['step'] ) || $field['step'] != '1' ) ? "step='" . $field['step'] . "' " : '';
			$min  = isset( $field['min'] ) ? "min='" . $field['min'] . "' " : '';
			if ( $min == '' ) {
				$min = '0';
			}
			$max = isset( $field['max'] ) ? "max='" . $field['max'] . "' " : '';
			echo "<input type='number' class='at-number" . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' ) . "' name='{$field['id']}' id='{$field['id']}' value='{$meta}' size='30' " . $step . $min . $max . ( isset( $field['style'] ) ? "style='{$field['style']}'" : '' ) . "/>";
			$this->show_field_end( $field, $meta );
		}

		/**
		 * Show Field code editor.
		 *
		 * @param string $field
		 *
		 * @author Ohad Raz
		 *
		 * @param string $meta
		 *
		 * @since  2.1
		 * @access public
		 */
		public function show_field_code( $field, $meta ) {
			$this->show_field_begin( $field, $meta );
			echo "<textarea class='code_text" . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' ) . "' name='{$field['id']}' id='{$field['id']}' data-lang='{$field['syntax']}' " . ( isset( $field['style'] ) ? "style='{$field['style']}'" : '' ) . " data-theme='{$field['theme']}'>{$meta}</textarea>";
			$this->show_field_end( $field, $meta );
		}


		/**
		 * Show Field hidden.
		 *
		 * @param string $field
		 * @param string|mixed $meta
		 *
		 * @since  0.1.3
		 * @access public
		 */
		public function show_field_hidden( $field, $meta ) {
			//$this->show_field_begin( $field, $meta );
			echo "<input type='hidden' " . ( isset( $field['style'] ) ? "style='{$field['style']}' " : '' ) . "class='at-text" . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' ) . "' name='{$field['id']}' id='{$field['id']}' value='{$meta}'/>";
			//$this->show_field_end( $field, $meta );
		}

		/**
		 * Show Field Paragraph.
		 *
		 * @param string $field
		 *
		 * @since  0.1.3
		 * @access public
		 */
		public function show_field_paragraph( $field, $meta ) {
			if ( ! empty ( $meta['no-border'] ) ) $noborder= ' no-bottom-border'; else $noborder = '';
			echo '<tr><td colspan="2" class="at-field' . $noborder . '"><p class="inline_paragraph"><em>' . $field['value'] . '</em></p></td></tr>';
		}

		/**
		 * Show Field Textarea.
		 *
		 * @param string $field
		 * @param string $meta
		 *
		 * @since  1.0
		 * @access public
		 */
		public function show_field_textarea( $field, $meta ) {
			$rows = 10;
			if ( isset( $field['rows'] ) && $field['rows'] > 0 ) {
				$rows = $field['rows'];
			}
			$this->show_field_begin( $field, $meta );
			echo "<textarea class='at-textarea large-text" . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' ) . "' name='{$field['id']}' id='{$field['id']}' " . ( isset( $field['style'] ) ? "style='{$field['style']}' " : '' ) . " cols='60' rows='" . $rows . "'>{$meta}</textarea>";
			$this->show_field_end( $field, $meta );
		}

		/**
		 * Show Field Select.
		 *
		 * @param string $field
		 * @param string $meta
		 *
		 * @since  1.0
		 * @access public
		 */
		public function show_field_select( $field, $meta ) {

			if ( ! is_array( $meta ) ) {
				$meta = (array) $meta;
			}

			$this->show_field_begin( $field, $meta );
			echo "<select " . ( isset( $field['style'] ) ? "style='{$field['style']}' " : '' ) . " class='at-select" . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' ) . "' name='{$field['id']}" . ( $field['multiple'] ? "[]' id='{$field['id']}' multiple='multiple'" : "'" ) . ">";
			foreach ( $field['options'] as $key => $value ) {
				echo "<option value='{$key}'" . selected( in_array( $key, $meta ), true, false ) . ">{$value}</option>";
			}
			echo "</select>";
			$this->show_field_end( $field, $meta );

		}

		/**
		 * Show Radio Field.
		 *
		 * @param string $field
		 * @param string $meta
		 *
		 * @since  1.0
		 * @access public
		 */
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

		/**
		 * Show Checkbox Field.
		 *
		 * @param string $field
		 * @param string $meta
		 *
		 * @since  1.0
		 * @access public
		 */
		public function show_field_checkbox( $field, $meta ) {
			$this->show_field_begin( $field, $meta );
			echo "<input type='checkbox' " . ( isset( $field['style'] ) ? "style='{$field['style']}' " : '' ) . " class='rw-checkbox" . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' ) . "' name='{$field['id']}' id='{$field['id']}'" . checked( ! empty( $meta ), true, false ) . " />";
			if ( isset( $field['caption'] ) ) {
				echo "<label for='{$field['id']}'><span class='at-checkbox-label'>{$field['caption']}</span></label>";
			}
			$this->show_field_end( $field, $meta );
		}


		/**
		 * Show Wysiwig Field.
		 *
		 * @param string $field
		 * @param string $meta
		 *
		 * @since  1.0
		 * @access public
		 */
		public function show_field_wysiwyg( $field, $meta, $in_repeater = false ) {
			$this->show_field_begin( $field, $meta );

			if ( $in_repeater ) {
				echo "<textarea class='at-wysiwyg theEditor large-text" . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' ) . "' name='{$field['id']}' id='{$field['id']}' cols='60' rows='10'>{$meta}</textarea>";
			} else {
				// Use new wp_editor() since WP 3.3
				$settings                 = ( isset( $field['settings'] ) && is_array( $field['settings'] ) ? $field['settings'] : array() );
				$settings['editor_class'] = 'at-wysiwyg' . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' );
				$id                       = str_replace( "_", "", $this->stripNumeric( strtolower( $field['id'] ) ) );
				wp_editor( html_entity_decode( $meta ), $id, $settings );
			}
			$this->show_field_end( $field, $meta );
		}

		/**
		 * Show File Field.
		 *
		 * @param string $field
		 * @param string $meta
		 *
		 * @since  1.0
		 * @access public
		 */
		public function show_field_file( $field, $meta ) {
			wp_enqueue_media();
			$this->show_field_begin( $field, $meta );

			$std      = isset( $field['std'] ) ? $field['std'] : array( 'id' => '', 'url' => '' );
			$multiple = isset( $field['multiple'] ) ? $field['multiple'] : false;
			$multiple = ( $multiple ) ? "multiFile " : "";
			$name     = esc_attr( $field['id'] );
			$value    = isset( $meta['id'] ) ? $meta : $std;
			$has_file = ( empty( $value['url'] ) ) ? false : true;
			$type     = isset( $field['mime_type'] ) ? $field['mime_type'] : '';
			$ext      = isset( $field['ext'] ) ? $field['ext'] : '';
			$type     = ( is_array( $type ) ? implode( "|", $type ) : $type );
			$ext      = ( is_array( $ext ) ? implode( "|", $ext ) : $ext );
			$id       = $field['id'];
			$li       = ( $has_file ) ? "<li><a href='" . esc_url($value['url']) . "' target='_blank'>{$value['url']}</a></li>" : "";

			echo "<span class='simplePanelfilePreview'><ul>{$li}</ul></span>";
			echo "<input type='hidden' name='{$name}[id]' value='{$value['id']}'/>";
			echo "<input type='hidden' name='{$name}[url]' value='{$value['url']}'/>";
			if ( $has_file ) {
				echo "<input type='button' class='{$multiple} button simplePanelfileUploadclear' id='{$id}' value='Remove File' data-mime_type='{$type}' data-ext='{$ext}'/>";
			} else {
				echo "<input type='button' class='{$multiple} button simplePanelfileUpload' id='{$id}' value='Upload File' data-mime_type='{$type}' data-ext='{$ext}'/>";
			}

			$this->show_field_end( $field, $meta );
		}

		/**
		 * Show Image Field.
		 *
		 * @param array $field
		 * @param array $meta
		 *
		 * @since  1.0
		 * @access public
		 */
		public function show_field_image( $field, $meta ) {
			wp_enqueue_media();
			$this->show_field_begin( $field, $meta );

			$std   = isset( $field['std'] ) ? $field['std'] : array( 'id' => '', 'url' => '' );
			$name  = esc_attr( $field['id'] );
			$value = isset( $meta['id'] ) ? $meta : $std;

			$value['url'] = isset( $meta['src'] ) ? $meta['src'] : $value['url']; //backwards capability
			$has_image    = empty( $value['url'] ) ? false : true;
			$id           = $field['id'];
			$multiple     = isset( $field['multiple'] ) ? $field['multiple'] : false;
			$multiple     = ( $multiple ) ? "multiFile " : "";

			if ( $has_image ) {
				$value['url'] = esc_url( $value['url'] );
				echo "<span class='simplePanelImagePreview has-image'><img src='{$value['url']}' class='preview-image'></span>";
			} else {
				echo "<span class='simplePanelImagePreview no-image'></span>";
			}
			echo "<input type='hidden' name='{$name}[id]' value='{$value['id']}'/>";
			echo "<input type='hidden' name='{$name}[url]' value='{$value['url']}'/>";
			if ( $has_image ) {
				echo "<input class='{$multiple} button simplePanelimageUploadclear' id='{$id}' value='" . esc_html__( 'Remove Image', 'inbound' ) . "' type='button'/>";
			} else {
				echo "<input class='{$multiple} button simplePanelimageUpload' id='{$id}' value='" . esc_html__( 'Upload Image', 'inbound' ) . "' type='button'/>";
			}
			$this->show_field_end( $field, $meta );
		}


		public function show_field_gallery( $field, $meta ) {
			wp_enqueue_media();
			$this->show_field_begin( $field, $meta );

			$std   = "";
			$name  = esc_attr( $field['id'] );
			$value = isset( $meta ) ? $meta : $std;

			$id = $field['id'];

			$out = '';
			$out .= '<input type="text" ';

			$out .= 'id="' . $name . '" name="' . $name . '" value="' . esc_attr( $value ) . '" ';

			if ( isset( $key['size'] ) ) {
				$out .= 'size="' . esc_attr( $key['size'] ) . '" ';
			}

			$out .= ' />';

			$out .= '<input type="button" class="button gallery-picker-select" data-target="#' . $name . '" id="' . $name . '_ids' . '" value="' . esc_html__( 'Select Items', 'inbound' ) . '">';

			echo $out;

			$this->show_field_end( $field, $meta );
		}


		/**
		 * Show Color Field.
		 *
		 * @param string $field
		 * @param string $meta
		 *
		 * @since  1.0
		 * @access public
		 */
		public function show_field_color( $field, $meta ) {

			if ( empty( $meta ) ) {
				$meta = '#';
			}

			$this->show_field_begin( $field, $meta );
			echo "<input class='at-color-iris" . ( isset( $field['class'] ) ? " {$field['class']}" : "" ) . "' type='text' name='{$field['id']}' id='{$field['id']}' value='{$meta}' size='8' />";
			$this->show_field_end( $field, $meta );

		}

		/**
		 * Show Checkbox List Field
		 *
		 * @param string $field
		 * @param string $meta
		 *
		 * @since  1.0
		 * @access public
		 */
		public function show_field_checkbox_list( $field, $meta ) {
			if ( ! is_array( $meta ) ) {
				$meta = (array) $meta;
			}

			$this->show_field_begin( $field, $meta );

			$html = array();

			$value_count = 0;
			foreach ( $field['options'] as $key => $value ) {
				$value_count ++;
				$html[] = "<input type='checkbox' " . ( isset( $field['style'] ) ? "style='{$field['style']}' " : '' ) . "  class='at-checkbox_list" . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' ) . "' name='{$field['id']}[]' id='{$field['id']}_{$value_count}' value='{$key}'" . checked( in_array( $key, $meta ), true, false ) . " /> <label for='{$field['id']}_{$value_count}'><span class='at-checkbox-label'>{$value}</span></label>";
			}

			echo implode( '<br />', $html );

			$this->show_field_end( $field, $meta );
		}

		/**
		 * Show Date Field.
		 *
		 * @param string $field
		 * @param string $meta
		 *
		 * @since  1.0
		 * @access public
		 */
		public function show_field_date( $field, $meta ) {
			$this->show_field_begin( $field, $meta );
			echo "<input type='text'  " . ( isset( $field['style'] ) ? "style='{$field['style']}' " : '' ) . " class='at-date" . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' ) . "' name='{$field['id']}' id='{$field['id']}' rel='{$field['format']}' value='{$meta}' size='30' />";
			$this->show_field_end( $field, $meta );
		}

		/**
		 * Show time field.
		 *
		 * @param string $field
		 * @param string $meta
		 *
		 * @since  1.0
		 * @access public
		 */
		public function show_field_time( $field, $meta ) {
			$this->show_field_begin( $field, $meta );
			$ampm = ( $field['ampm'] ) ? 'true' : 'false';
			echo "<input type='text'  " . ( isset( $field['style'] ) ? "style='{$field['style']}' " : '' ) . " class='at-time" . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' ) . "' name='{$field['id']}' id='{$field['id']}' data-ampm='{$ampm}' rel='{$field['format']}' value='{$meta}' size='30' />";
			$this->show_field_end( $field, $meta );
		}

		/**
		 * Show Posts field.
		 * used creating a posts/pages/custom types checkboxlist or a select dropdown
		 *
		 * @param string $field
		 * @param string $meta
		 *
		 * @since  1.0
		 * @access public
		 */
		public function show_field_posts( $field, $meta ) {
			global $post;

			if ( ! is_array( $meta ) ) {
				$meta = (array) $meta;
			}
			$this->show_field_begin( $field, $meta );
			$options = $field['options'];

			$query = new WP_Query( $field['options']['args'] );
			$query->set( 'posts_per_page', - 1 );
			$query->set( 'suppress_filters', 1 );
			$posts = $query->get_posts();

			// checkbox_list
			if ( 'checkbox_list' == $options['type'] ) {
				$value_count = 0;
				foreach ( $posts as $p ) {
					$value_count ++;
					echo "<input type='checkbox' id='{$field['id']}_{$value_count}' " . ( isset( $field['style'] ) ? "style='{$field['style']}' " : '' ) . " class='at-posts-checkbox" . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' ) . "' name='{$field['id']}[]' value='$p->ID'" . checked( in_array( $p->ID, $meta ), true, false ) . " /> <label for='{$field['id']}_{$value_count}'><span class='at-checkbox-label'>{$p->post_title}</span></label><br/>";
				}
			} // select
			else {
				echo "<select " . ( isset( $field['style'] ) ? "style='{$field['style']}' " : '' ) . " class='at-posts-select" . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' ) . "' name='{$field['id']}" . ( $field['multiple'] ? "[]' multiple='multiple' style='height:auto'" : "'" ) . ">";
				echo '<option value="0">' . esc_html__( 'None', 'inbound' ) . '</option>';
				foreach ( $posts as $p ) {
					echo "<option value='$p->ID'" . selected( in_array( $p->ID, $meta ), true, false ) . ">$p->post_title [$p->ID]</option>";
				}
				echo "</select>";
			}

			$this->show_field_end( $field, $meta );
		}

		/**
		 * Show Taxonomy field.
		 * used creating a category/tags/custom taxonomy checkboxlist or a select dropdown
		 *
		 * @param string $field
		 * @param string $meta
		 *
		 * @since  1.0
		 * @access public
		 *
		 * @uses   get_terms()
		 */
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
				$field_count = 0;
				foreach ( $terms as $term ) {
					$field_count ++;
					echo "<input type='checkbox' " . ( isset( $field['style'] ) ? "style='{$field['style']}' " : '' ) . " class='at-tax-checkbox" . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' ) . "' name='{$field['id']}[]' id='{$field['id']}_{$field_count}' value='$term->slug'" . checked( in_array( $term->slug, $meta ), true, false ) . " /> <label for='{$field['id']}_{$field_count}'><span class='at-checkbox-label'>$term->name</span></label><br/>";
				}
			} // select
			else {
				echo "<select " . ( isset( $field['style'] ) ? "style='{$field['style']}' " : '' ) . " class='at-tax-select" . ( isset( $field['class'] ) ? ' ' . $field['class'] : '' ) . "' name='{$field['id']}" . ( $field['multiple'] ? "[]' multiple='multiple' style='height:auto'" : "'" ) . ">";
				if ( isset( $field['none'] ) && $field['none'] != "" ) {
					echo "<option value=''" . selected( in_array( '', $meta ), true, false ) . ">" . $field['none'] . "</option>";
				}
				foreach ( $terms as $term ) {
					echo "<option value='$term->slug'" . selected( in_array( $term->slug, $meta ), true, false ) . ">$term->name</option>";
				}
				echo "</select>";
			}

			$this->show_field_end( $field, $meta );
		}


		/*
		 * Show Taxonomy Custom Text Attribute List
		 */
		public function show_field_taxonomy_custom( $field, $meta ) {
			global $post;

			if ( ! is_array( $meta ) ) {
				$meta = (array) $meta;
			}
			$this->show_field_begin( $field, $meta );
			$this->show_field_end( $field, $meta );

			$options = $field['options'];
			$terms   = get_terms( $options['taxonomy'], $options['args'] );

			if ( 'text' == $options['type'] ) {
				$field_count         = 0;
				$field_count_enabled = 0;

				foreach ( $terms as $term ) {

					$is_feature_custom = true;
					if ( isset( $field['meta_field_enabled'] ) ) {
						$is_feature_custom = get_tax_meta( $term->term_id, $field['meta_field_enabled'] );
					}

					if ( $is_feature_custom ) {
						$field_count_enabled ++;
					}
				}


				foreach ( $terms as $term ) {

					$is_feature_custom = true;
					if ( isset( $field['meta_field_enabled'] ) ) {
						$is_feature_custom = get_tax_meta( $term->term_id, $field['meta_field_enabled'] );
					}

					if ( $is_feature_custom ) {
						$field_count ++;

						$nfield['desc_position'] = 'left';
						$nfield['desc']          = '';

						if ( $field_count == $field_count_enabled ) {
							$nfield['last'] = true;
						} else {
							$nfield['last'] = true;
						}

						$nfield['type'] = 'text';
						$nfield['name'] = $term->name;

						$nfield['id'] = $field['id'] . "_" . $term->slug;

						$meta = get_post_meta( $field['post_id'], $nfield['id'], true );

						echo '</tr>';
						echo '<tr>';
						$this->show_field_text( $nfield, $meta );
						echo '</tr>';

					}

				}
			}

		}


		/**
		 * Show conditinal Checkbox Field.
		 *
		 * @param string $field
		 * @param string $meta
		 *
		 * @since  2.9.9
		 * @access public
		 */
		public function show_field_cond( $field, $meta ) {

			$this->show_field_begin( $field, $meta );
			$checked = false;
			if ( is_array( $meta ) && isset( $meta['enabled'] ) && $meta['enabled'] == 'on' ) {
				$checked = true;
			}
			echo "<input type='checkbox' class='conditinal_control' name='{$field['id']}[enabled]' id='{$field['id']}'" . checked( $checked, true, false ) . " />";
			//start showing the fields
			$display = ( $checked ) ? '' : ' style="display: none;"';

			echo '<div class="conditinal_container"' . $display . '><table>';
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
				echo '<tr>';
				call_user_func( array( $this, 'show_field_' . $f['type'] ), $f, $m );
				echo '</tr>';
			}
			echo '</table></div>';
			$this->show_field_end( $field, $meta );
		}

		/**
		 * Save Data from Metabox
		 *
		 * @param string $post_id
		 *
		 * @since  1.0
		 * @access public
		 */
		public function save( $post_id ) {

			global $post_type;

			$post_type_object = get_post_type_object( $post_type );

			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )                      // Check Autosave
			     || ( ! isset( $_POST['post_ID'] ) || $post_id != $_POST['post_ID'] )        // Check Revision
			     || ( ! in_array( $post_type, $this->_meta_box['pages'] ) )                  // Check if current post type is supported.
			     || ( ! check_admin_referer( basename( __FILE__ ), 'sr_meta_box_nonce' ) )    // Check nonce - Security
			     || ( ! current_user_can( $post_type_object->cap->edit_post, $post_id ) )
			)  // Check permission
			{
				return $post_id;
			}

			// special pre-processing to find valid fields in custom types
			foreach ( $this->_fields as $field ) {
				if ( $field['type'] == "taxonomy_custom" ) {
					$options = $field['options'];
					$terms   = get_terms( $options['taxonomy'], $options['args'] );

					foreach ( $terms as $term ) {
						$is_feature_custom = true;
						if ( isset( $field['meta_field_enabled'] ) ) {
							$is_feature_custom = get_tax_meta( $term->term_id, $field['meta_field_enabled'] );
						}
						if ( $is_feature_custom ) {
							$nfield['type']          = 'text';
							$nfield['id']            = $field['id'] . "_" . $term->slug;
							$nfield['format']        = null;
							$nfield['validate_func'] = null;
							$this->_fields[]         = $nfield;
						}
					}
				}
			}


			foreach ( $this->_fields as $field ) {

				$name = $field['id'];
				$type = $field['type'];
				$old  = get_post_meta( $post_id, $name, ! $field['multiple'] );
				$new  = ( isset( $_POST[ $name ] ) ) ? $_POST[ $name ] : ( ( $field['multiple'] ) ? array() : '' );


				// Validate meta value
				if ( class_exists( 'at_Meta_Box_Validate' ) && method_exists( 'at_Meta_Box_Validate', $field['validate_func'] ) ) {
					$new = call_user_func( array( 'at_Meta_Box_Validate', $field['validate_func'] ), $new );
				}

				//skip on Paragraph field
				if ( $type != "paragraph" ) {

					// Call defined method to save meta value, if there's no methods, call common one.
					$save_func = 'save_field_' . $type;
					if ( method_exists( $this, $save_func ) ) {
						call_user_func( array( $this, 'save_field_' . $type ), $post_id, $field, $old, $new );
					} else {
						$this->save_field( $post_id, $field, $old, $new );
					}
				}

			} // End foreach
		}

		public function save_field( $post_id, $field, $old, $new ) {
			$name = $field['id'];
			delete_post_meta( $post_id, $name );
			if ( $new === '' || $new === array() ) {
				return;
			}
			if ( $field['multiple'] ) {
				foreach ( $new as $add_new ) {
					add_post_meta( $post_id, $name, $add_new, false );
				}
			} else {
				update_post_meta( $post_id, $name, $new );
			}
		}

		/**
		 * function for saving image field.
		 *
		 * @param string $post_id
		 * @param string $field
		 * @param string $old
		 * @param string|mixed $new
		 *
		 * @since  1.7
		 * @access public
		 */
		public function save_field_image( $post_id, $field, $old, $new ) {
			$name = $field['id'];
			delete_post_meta( $post_id, $name );
			if ( $new === '' || $new === array() || $new['id'] == '' || $new['url'] == '' ) {
				return;
			}

			update_post_meta( $post_id, $name, $new );
		}

		public function save_field_wysiwyg( $post_id, $field, $old, $new ) {
			$id  = str_replace( "_", "", $this->stripNumeric( strtolower( $field['id'] ) ) );
			$new = ( isset( $_POST[ $id ] ) ) ? $_POST[ $id ] : ( ( $field['multiple'] ) ? array() : '' );
			$this->save_field( $post_id, $field, $old, $new );
		}

		public function save_field_repeater( $post_id, $field, $old, $new ) {
			if ( is_array( $new ) && count( $new ) > 0 ) {
				foreach ( $new as $n ) {
					foreach ( $field['fields'] as $f ) {
						$type = $f['type'];
						switch ( $type ) {
							case 'wysiwyg':
								$n[ $f['id'] ] = wpautop( $n[ $f['id'] ] );
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
					update_post_meta( $post_id, $field['id'], $temp );
				} else {
					//  remove old meta if exists
					delete_post_meta( $post_id, $field['id'] );
				}
			} else {
				//  remove old meta if exists
				delete_post_meta( $post_id, $field['id'] );
			}
		}

		public function save_field_file( $post_id, $field, $old, $new ) {

			$name = $field['id'];
			delete_post_meta( $post_id, $name );
			if ( $new === '' || $new === array() || $new['id'] == '' || $new['url'] == '' ) {
				return;
			}

			update_post_meta( $post_id, $name, $new );
		}

		public function save_field_file_repeater( $post_id, $field, $old, $new ) { }

		public function add_missed_values() {

			// Default values for meta box
			$this->_meta_box = array_merge( array(
				'context'  => 'normal',
				'priority' => 'high',
				'pages'    => array( 'post' )
			), (array) $this->_meta_box );

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
			//faster search in single dimention array.
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

		public function is_edit_page() {
			global $pagenow;

			return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
		}

		/**
		 * Fixes the odd indexing of multiple file uploads.
		 *
		 * Goes from the format:
		 * $_FILES['field']['key']['index']
		 * to
		 * The More standard and appropriate:
		 * $_FILES['field']['index']['key']
		 */
		public function fix_file_array( &$files ) {
			$output = array();
			foreach ( $files as $key => $list ) {
				foreach ( $list as $index => $value ) {
					$output[ $index ][ $key ] = $value;
				}
			}

			return $output;
		}

		public function get_jqueryui_ver() {
			global $wp_version;
			if ( version_compare( $wp_version, '3.1', '>=' ) ) {
				return '1.8.10';
			}

			return '1.7.3';
		}

		public function addField( $id, $args ) {
			$new_field       = array( 'id' => $id, 'std' => '', 'desc' => '', 'style' => '' );
			$new_field       = array_merge( $new_field, $args );
			$this->_fields[] = $new_field;
		}

		public function addSection( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'           => 'section',
				'id'             => $id,
				'std'            => '',
				'desc'           => '',
				'style'          => '',
				'name'           => 'Section Headline',
				'label_location' => 'none'
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addIcon( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'  => 'icon',
				'id'    => $id,
				'std'   => '',
				'desc'  => '',
				'style' => '',
				'name'  => 'Icon'
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

		public function addTypography( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'  => 'typography',
				'id'    => $id,
				'std'   => array(
					'color'  => '#ffffff',
					'size'   => '13px',
					'style'  => 'normal',
					'weight' => 'normal'
				),
				'desc'  => '',
				'style' => '',
				'name'  => 'Typography Field'
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addNumber( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'  => 'number',
				'id'    => $id,
				'std'   => '0',
				'desc'  => '',
				'style' => '',
				'name'  => 'Number Field',
				'step'  => '1',
				'min'   => '0'
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
				'theme'  => 'defualt'
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
				'name'  => 'Text Field'
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addParagraph( $id, $args, $repeater = false ) {

			$new_field = array(
					'type' => 'paragraph',
					'id' => $id,
					'value' => '',
					'std'   => '',
					'desc'  => '',
					'style' => '',
					'no-border' => false,
					'name'  => ''
			);
			$new_field = array_merge( $new_field, $args );
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

		public function addCheckboxList( $id, $options, $args, $repeater = false ) {
			$new_field = array(
				'type'     => 'checkbox_list',
				'id'       => $id,
				'std'      => '',
				'desc'     => '',
				'style'    => '',
				'sortable' => false,
				'name'     => 'Checkbox List Field',
				'options'  => $options,
				'multiple' => true,
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
				'type'           => 'select',
				'id'             => $id,
				'std'            => array(),
				'desc'           => '',
				'style'          => '',
				'group-selector' => false,
				'name'           => 'Select Field',
				'multiple'       => false,
				'options'        => $options
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
				'type'    => 'radio',
				'id'      => $id,
				'std'     => array(),
				'desc'    => '',
				'style'   => '',
				'name'    => 'Radio Field',
				'options' => $options
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
				'name'   => 'Time Field',
				'ampm'   => false
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
			$new_field = array(
				'type'     => 'image',
				'id'       => $id,
				'desc'     => '',
				'name'     => 'Image Field',
				'std'      => array( 'id' => '', 'url' => '' ),
				'multiple' => false
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addGallery( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'     => 'gallery',
				'id'       => $id,
				'desc'     => '',
				'name'     => 'Gallery Field',
				'std'      => '',
				'multiple' => false
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addFile( $id, $args, $repeater = false ) {
			$new_field = array(
				'type'     => 'file',
				'id'       => $id,
				'desc'     => '',
				'name'     => 'File Field',
				'multiple' => false,
				'std'      => array( 'id' => '', 'url' => '' )
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
			$temp      = array(
				'args' => array( 'hide_empty' => 0 ),
				'tax'  => 'category',
				'type' => 'select'
			);
			$options   = array_merge( $temp, $options );
			$new_field = array(
				'type'    => 'taxonomy',
				'id'      => $id,
				'desc'    => '',
				'name'    => 'Taxonomy Field',
				'options' => $options
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}

		public function addTaxonomyCustom( $id, $options, $args, $repeater = false ) {
			$temp      = array(
				'args' => array( 'hide_empty' => 0 ),
				'tax'  => 'category',
				'type' => 'text'
			);
			$options   = array_merge( $temp, $options );
			$new_field = array(
				'type'    => 'taxonomy_custom',
				'id'      => $id,
				'desc'    => '',
				'name'    => 'Taxonomy Custom Attribute Field',
				'options' => $options
			);
			$new_field = array_merge( $new_field, $args );
			if ( false === $repeater ) {
				$this->_fields[] = $new_field;
			} else {
				return $new_field;
			}
		}


		public function addPosts( $id, $options, $args, $repeater = false ) {
			$post_type = isset( $options['post_type'] ) ? $options['post_type'] : ( isset( $args['post_type'] ) ? $args['post_type'] : 'post' );
			$type      = isset( $options['type'] ) ? $options['type'] : 'select';
			$q         = array(
				'posts_per_page' => - 1,
				'post_type'      => $post_type,
				'orderby'        => 'title',
				'order'          => 'ASC'
			);
			if ( isset( $options['args'] ) ) {
				$q = array_merge( $q, (array) $options['args'] );
			}
			$options   = array( 'post_type' => $post_type, 'type' => $type, 'args' => $q );
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
				'sortable' => false,
				'title'    => false
			);
			$new_field       = array_merge( $new_field, $args );
			$this->_fields[] = $new_field;
		}

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

		public function Finish() {
			$this->add_missed_values();
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

		function Validate_upload_file_type( $file ) {
			if ( isset( $_POST['uploadeType'] ) && ! empty( $_POST['uploadeType'] ) && isset( $_POST['uploadeType'] ) && $_POST['uploadeType'] == 'my_meta_box' ) {
				$allowed = explode( "|", $_POST['uploadeType'] );
				$ext     = substr( strrchr( $file['name'], '.' ), 1 );

				if ( ! in_array( $ext, (array) $allowed ) ) {
					$file['error'] = esc_html__( "Sorry, you cannot upload this file type for this field.", 'inbound' );

					return $file;
				}

				foreach ( get_allowed_mime_types() as $key => $value ) {
					if ( strpos( $key, $ext ) || $key == $ext ) {
						return $file;
					}
				}
				$file['error'] = esc_html__( "Sorry, you cannot upload this file type for this field.", 'inbound' );
			}

			return $file;
		}

		public function idfy( $str ) {
			return str_replace( " ", "_", $str );

		}

		public function stripNumeric( $str ) {
			return trim( str_replace( range( 0, 9 ), '', $str ) );
		}


	} // End Class
endif; // End Check Class Exists