<?php
/**
 * WordPress Widgets Helper Class
 *
 * https://github.com/sksmatt/WordPress-Widgets-Helper-Class
 *
 * Copyright 2013 by @sksmatt | www.mattvarone.com
 *
 * modified by ShapingRain.com Labs for ShapingRain.com themes
 * Copyright 2013-2015 by ShapingRain.com | www.shapingrain.com
 *
 *
 * This class has been released under GPLv2
 *
 * GNU General Public License v2.0
 * http://www.gnu.org/licenses/gpl-2.0.html
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

if ( ! class_exists( 'SR_Widget' ) )
{

	class SR_Widget extends WP_Widget
	{

		/**
		 * Create Widget
		 *
		 * Creates a new widget and sets it's labels, description, fields and options
		 *
		 * @access   public
		 * @param    array
		 * @return   void
		 * @since    1.0
		 */

		function create_widget( $args ) {
			// settings some defaults
			$defaults = array(
					'label'        => '',
					'description'  => '',
					'fields'       => array(),
					'groups'       => array(),
					'options'      => array(),
			);

			// parse and merge args with defaults
			$args = wp_parse_args( $args, $defaults );

			// extract each arg to its own variable
			extract( $args, EXTR_SKIP );

			// set the widget vars
			$this->slug    = sanitize_title( $label );
			$this->fields  = $fields;
			$this->groups  = $groups;


			// check options
			$this->options = array( 'classname' => $this->slug, 'description' => $description );
			if ( ! empty( $options ) ) $this->options = array_merge( $this->options, $options );

			// call WP_Widget to create the widget
			parent::__construct( $this->slug, $label, $this->options );

		}


		/**
		 * Form
		 *
		 * Creates the settings form.
		 *
		 * @access   private
		 * @param    array
		 * @return   void
		 * @since    1.0
		 */

		function form( $instance ) {
			$this->instance = $instance;

			if (!empty($this->groups)) {
				$groups = $this->create_groups();
				echo '<div class="tabs-panel-container">';
				echo $groups;
				echo '<div class="tabs-panel">';
			}

			$form = $this->create_fields();

			echo $form;

			if (!empty($this->groups)) {
				echo '</div></div>';
			}

		}

		/**
		 * Update Fields
		 *
		 * @access   private
		 * @param    array
		 * @param    array
		 * @return   array
		 * @since    1.0
		 */

		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$this->before_update_fields();

			foreach ( $this->fields as $key ) {

				if ( $key['type'] != "paragraph" && $key['type'] != "headline" ) {

					$slug = $key['id'];

					if (isset($new_instance[$slug])) {
						$this_new_instance = $new_instance[$slug];
					}
					else {
						$this_new_instance = null;
					}

					if ( isset( $key['validate'] ) ) {
						if ( false === $this->validate( $key['validate'], $this_new_instance ) ) {
							return $instance;
						}
					}

					if ( isset( $key['filter'] ) ) {
						$instance[$slug] = $this->filter( $key['filter'], $this_new_instance );
					}
					else {
						$instance[$slug] = strip_tags( $new_instance[$slug] );
					}
				}
			}

			return $this->after_validate_fields( $instance );
		}


		/**
		 * Before Validate Fields
		 *
		 * Allows to hook code on the update.
		 *
		 * @access   public
		 * @param    string
		 * @return   string
		 * @since    1.6
		 */

		function before_update_fields() {
			return;
		}


		/**
		 * After Validate Fields
		 *
		 * Allows to modify the output after validating the fields.
		 *
		 * @access   public
		 * @param    string
		 * @return   string
		 * @since    1.6
		 */

		function after_validate_fields( $instance = "" ) {
			return $instance;
		}


		/**
		 * Validate
		 *
		 * @access   private
		 * @param    string
		 * @param    string
		 * @return   boolean
		 * @since    1.0
		 */

		function validate( $rules, $value ) {
			$rules = explode( '|', $rules );

			if ( empty( $rules ) || count( $rules ) < 1 )
				return true;

			foreach ( $rules as $rule ) {
				if ( false === $this->do_validation( $rule, $value ) )
					return false;
			}

			return true;
		}


		/**
		 * Filter
		 *
		 * @access   private
		 * @param    string
		 * @param    string
		 * @return   void
		 * @since    1.0
		 */

		function filter( $filters, $value ) {
			$filters = explode( '|', $filters );

			if ( empty( $filters ) || count( $filters ) < 1 )
				return $value;

			foreach ( $filters as $filter )
				$value = $this->do_filter( $filter, $value );

			return $value;
		}


		/**
		 * Do Validation Rule
		 *
		 * @access   private
		 * @param    string
		 * @param    string
		 * @return   boolean
		 * @since    1.0
		 */

		function do_validation( $rule, $value = "" )
		{

			if ( empty ($value ) ) return $value;

			switch ( $rule ) {

				case 'alpha':
					return ctype_alpha( $value );
					break;

				case 'alpha_numeric':
					return ctype_alnum( $value );
					break;

				case 'alpha_dash':
					return preg_match( '/^[a-z0-9-_]+$/', $value );
					break;

				case 'numeric':
					return ctype_digit( $value );
					break;

				case 'numeric_with_unit':
					if ( ! empty ( $value['number'] ) ) {
						return ctype_digit( $value['number'] );
					}
					break;

				case 'slider':
					return true;
					break;

				case 'integer':
					return ( bool ) preg_match( '/^[\-+]?[0-9]+$/', $value );
					break;

				case 'boolean':
					return is_bool( $value );
					break;

				case 'email':
					return is_email( $value );
					break;

				case 'decimal':
					return ( bool ) preg_match( '/^[\-+]?[0-9]+\.[0-9]+$/', $value );
					break;

				case 'natural':
					return ( bool ) preg_match( '/^[0-9]+$/', $value );

				case 'natural_not_zero':
					if ( ! preg_match( '/^[0-9]+$/', $value ) ) return false;
					if ( $value == 0 ) return false;
					return true;

				case 'noop':
					return true;

				default:
					if ( method_exists( $this, $rule ) )
						return $this->$rule( $value );
					else
						return false;
					break;

			}
		}


		/**
		 * Do Filter
		 *
		 * @access   private
		 * @param    string
		 * @param    string
		 * @return   boolean
		 * @since    1.0
		 */

		function do_filter( $filter, $value = "" )
		{
			switch ( $filter )
			{
				case 'strip_tags':
					return strip_tags( $value );
					break;

				case 'wp_strip_all_tags':
					return wp_strip_all_tags( $value );
					break;

				case 'esc_attr':
					return esc_attr( $value );
					break;

				case 'esc_url':
					return esc_url( $value );
					break;

				case 'esc_textarea':
					return esc_textarea( $value );
					break;

				case 'esc_html':
					return esc_html( $value );
					break;

				default:
					if ( method_exists( $this, $filter ) )
						return $this->$filter( $value );
					else
						return $value;
					break;
			}
		}

		/**
		 * Create Groups/Tabs
		 */
		function create_groups ( $out = "" ) {

			if ( ! empty( $this->groups ) ) {

				// get first group
				foreach ($this->groups as $first_group => $first_label) {
					break;
				}

				// generate mark-up
				$out .= '<ul class="category-tabs widget-groups" data-group-first="' . $first_group . '">';
				$x = 0;
				$tab_class = '';
				foreach ( $this->groups as $key => $title ) {
					if ($x == 0) $tab_class = ' class="tabs"'; else $tab_class = '';
					$out .= '<li' . $tab_class . '><a class="group-tab-link" href="javascript:void(0);" data-group-target="' . esc_attr( $key ) . '">' . esc_html( $title ) . '</a></li>';
					$x++;
				}
				$out .= '</ul>';
			}

			return $out;
		}


		/**
		 * Create Fields
		 *
		 * Creates each field defined.
		 *
		 * @access   private
		 * @param    string
		 * @return   string
		 * @since    1.0
		 */

		function create_fields( $out = "" ) {

			$out = $this->before_create_fields( $out );

			if ( ! empty( $this->fields ) ) {
				foreach ( $this->fields as $key )
					$out .= $this->create_field( $key );
			}

			$out = $this->after_create_fields( $out );

			return $out;
		}


		/**
		 * Before Create Fields
		 *
		 * Allows to modify code before creating the fields.
		 *
		 * @access   public
		 * @param    string
		 * @return   string
		 * @since    1.0
		 */

		function before_create_fields( $out = "" ) {
			return $out;
		}


		/**
		 * After Create Fields
		 *
		 * Allows to modify code after creating the fields.
		 *
		 * @access   public
		 * @param    string
		 * @return   string
		 * @since    1.0
		 */

		function after_create_fields( $out = "" ) {
			return $out;
		}


		/**
		 * Create Fields
		 *
		 * @access   private
		 * @param    string
		 * @param    string
		 * @return   string
		 * @since    1.0
		 */

		function create_field( $key, $out = "" ) {
			/* Set Defaults */
			$key['std'] = isset( $key['std'] ) ? $key['std'] : "";

			$slug = $key['id'];

			if ( $key['type'] != "paragraph" && $key['type'] != "headline" ) {
				if ( isset( $this->instance[$slug] ) ) {
					if ( !is_array($this->instance[$slug]) )
					{
						$key['value'] = empty( $this->instance[$slug] ) ? '' : strip_tags( $this->instance[$slug] );
					}
					else {
						$key['value'] = empty( $this->instance[$slug] ) ? '' : $this->instance[$slug];
					}
				}
				else {
					unset( $key['value'] );
				}
			}

			/* Set field id and name  */
			$key['_id'] = $this->get_field_id( $slug );
			$key['_name'] = $this->get_field_name( $slug );

			/* Set field type */
			if ( ! isset( $key['type'] ) ) $key['type'] = 'text';

			/* Prefix method */
			$field_method = 'create_field_' . str_replace( '-', '_', $key['type'] );

			/* Tab Groups */
			if (!empty($key['group'])) {
				$key['class-p'] = ' group group-' . $key['group'];
			}


			/* Groups based on selectors */
			// is this a group selector element?
			if ( isset($key['group-selector']) && $key['group-selector'] == true ) {
				if ( isset($key['class']) ) {
					$key['class'] .= ' group-selector';
				} else {
					$key['class'] = ' group-selector';
				}
			}
			// is this an element that belongs to a group?
			$group_class = '';
			$group_data = '';
			if ( isset($key['is-group']) && isset($key['group-value']) ) {
				$repeater_item_group_id = $this->get_field_id( $key['is-group'] );
				$group_class = ' is-group group-' . $repeater_item_group_id;
				$group_data  = ' data-group-selector="' . $repeater_item_group_id . '"';
				$group_data .=  " data-group-value='" . json_encode ( $key['group-value'] ) . "'";
			}


			/* Check for <p> Class */
			if ( isset( $key['class-p'] ) ) {
				$p = '<div class="at-field' . $key['class-p'] . $group_class . '"' . $group_data . '>';
			} else {
				$p = '<div class="at-field' . $group_class . '"' . $group_data . '>';
			}



			/* Run method */
			if ( method_exists( $this, $field_method ) )
				return $p . $this->$field_method( $key ) . '</div>';

		}


		function create_field_posts( $key, $out = "" )
		{
			$query = new WP_Query( array( 'post_type' => $key['post_type'], 'posts_per_page' => -1 ) );
			$posts = $query->get_posts();

			if (!isset($key['std'])) $key['std'] = 0;

			if ($posts) {

				$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

				if (!isset($key['value'])) $key['value'] = $key['std'];

				$out .= '<select class="at-posts-select"	 name="' . esc_attr( $key['_name'] ) . '">';
				$out .= '<option value="0"' . selected($key['value'], 0, false) . '>'. esc_html__( 'None', 'inbound' ) . '</option>';
				foreach ($posts as $p) {
					$out .= '<option value="' . $p->ID . '"' . selected($key['value'], $p->ID, false) . '>' . $p->post_title . ' [' . $p->ID . ']' . '</option>';
				}
				$out .= '</select>';

				if ( isset( $key['desc'] ) )
					$out .= '<br/><small class="description">'.esc_html( $key['desc'] ).'</small>';

			} else {
				$out .= '<input type="hidden" name="' . esc_attr( $key['_name'] ) . '" value="' . $key['std'] . '">';
			}

			return $out;
		}

		function create_field_categories( $key, $out = "" )
		{
			$args = array(
				'type'                     => $key['post_type'],
				'orderby'                  => 'name',
				'order'                    => 'ASC',
				'hide_empty'               => 1,
				'hierarchical'             => 1,
				'taxonomy'                 => $key['taxonomy'],
				'pad_counts'               => false
			);
			$categories = get_categories( $args );

			if (!isset($key['std'])) $key['std'] = 0;

			if ($categories) {

				$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

				if (!isset($key['value'])) $key['value'] = $key['std'];

				if (! $key['multiple']) {
					// select box
					$out .= '<select class="at-categories-select"	 name="' . esc_attr( $key['_name'] ) . '">';
					$out .= '<option value="0"' . selected($key['value'], 0, false) . '>'. esc_html__( 'All', 'inbound' ) . '</option>';
					foreach ($categories as $p) {
						$out .= '<option value="' . $p->slug . '"' . selected($key['value'], $p->slug, false) . '>' . $p->cat_name . ' ('. $p->category_count .')</option>';
					}
					$out .= '</select>';
				} else {
					// checkboxes
					$out .= '<ul class="at-checkbox-list">';

					$fields_count = 0;

					$fields = array();
					foreach ($categories as $p) {
						$fields[$p->slug] = $p->cat_name .  ' ('. $p->category_count .')';
					}

					foreach ( $fields as $field => $option )
					{

						$id=    $key['_id'] . '_' . str_replace("-", "", $field);
						$name = $key['_name'] . '[' . $field . ']';

						$out .= '<li><label for="' . esc_attr( $id ). '">';

						$out .= ' <input type="checkbox" ';

						if ( isset( $key['class'] ) )
							$out .= 'class="' . esc_attr( $key['class'] ) . '" ';

						$out .= 'id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . $field . '" ';

						if ( ( isset( $key['value'] ) && is_array ( $key['value'] ) && array_key_exists( $field, $key['value']) ) OR ( ! isset( $key['value'] ) && ( isset ($key['std']) && is_array($key['std']) && array_key_exists( $field, $key['std']) ) ) )
							$out .= ' checked="checked" ';

						$out .= ' /> ';

						$out .=  ' ' . esc_html( $option ) . '</label></li>';
						$fields_count++;
					}

					$out .= '</ul>';

				}

				if ( isset( $key['desc'] ) )
					$out .= '<br/><small class="description">'.esc_html( $key['desc'] ).'</small>';

			} else {
				$out .= '<input type="hidden" name="' . esc_attr( $key['_name'] ) . '" value="' . $key['std'] . '">';
			}

			return $out;
		}


		function create_field_checkbox_list( $key, $out = "" )
		{
			$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

			$sortable_class = '';
			$widget_class = '';


			// convert stored value into simple array
			if (!empty($key['value'])) {
				$value = $key['value'];
			} else {
				$value = array();
			}
			$values = array();
			if (is_array($value)) {
				foreach ($value as $k => $v) {
					if (!empty($v)) {
						$values[] = $k;
					}
				}
			}
			$key['value'] = $values;

			// handle sortable
			if ( isset ($key['sortable']) ) {
				$sortable_class = ' at-sortable';
				$widget_class = ' class="widget-sort"';
				$key['fields'] = array_merge(array_flip($key['value']), $key['fields']);
			}

			$list = '';

			$list .= '<ul class="at-checkbox-list' . $sortable_class . '">';

			$fields_count = 0;

			foreach ( $key['fields'] as $field => $option )
			{

				$id=    $key['_id'] . '_' . str_replace("-", "", $field);
				$name = $key['_name'] . '[' . $field . ']';

				$list .= '<li'. $widget_class .'><label for="' . esc_attr( $id ). '">';

				$list .= ' <input type="checkbox" ';

				if ( isset( $key['class'] ) )
					$list .= 'class="' . esc_attr( $key['class'] ) . '" ';

				$list .= ' id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . $field . '" ';

				if ( in_array( $field, $key['value']) )
					$list .= ' checked="checked" ';

				$list .= ' />';

				$list .=  ' ' . esc_html( $option ) . '</label>';

				$list .= '</li>';

				$fields_count++;
			}

			$list .= '</ul>';

			$out .= $list;

			if ( isset( $key['desc'] ) )
				$out .= '<br/><small class="description">'.esc_html( $key['desc'] ).'</small>';

			return $out;
		}


		function create_field_slider( $key, $out = "" )
		{
			$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

			if (!isset ($key['hidden'])) $key['hidden'] = array();

			$id = $key['_id'];
			$name = $key['name'];

			if ( ! isset ( $key['value'] ) && isset ( $key['std'] ) ) {
				$key['value'] = $key['std'];
			}

			$out .= '<small class="description">'. esc_html__('Select control and user interaction options.', 'inbound' ) . '</small><br/>';

			/*
			 * Show Controls
			 */
			if  (!in_array('controls', $key['hidden'] ) ) {
				$out .= '<label for="' . esc_attr( $id ). '_controls">';
				$out .= ' <input type="checkbox" id="' . esc_attr( $key['_id'] ) . '_controls" name="' . esc_attr( $key['_name'] ) . '[controls]" value="on"';
				if ( !empty($key['value']['controls']) ) $meta = true; else $meta = false;
				$out .= checked( $meta , true, false );
				$out .=	'/> ';
				$out .=  ' ' . esc_html__('Display navigation arrows', 'inbound') . '</label>';
				$out .= '<br/>';
			}

			/*
			 * Show Navigation Pips
			 */
			if  (!in_array('pips', $key['hidden'] ) ) {
				$out .= '<label for="' . esc_attr( $id ). '_pips">';
				$out .= ' <input type="checkbox" id="' . esc_attr( $key['_id'] ) . '_pips" name="' . esc_attr( $key['_name'] ) . '[pips]" value="1" ';
				if ( !empty($key['value']['pips']) ) $meta = true; else $meta = false;
				$out .= checked( $meta , true, false );
				$out .= ' /> ';
				$out .=  ' ' . esc_html__('Display item navigation pips', 'inbound') . '</label>';

				$out .= '<br/>';
			}

			/*
			 * Pause on Hover
			 */
			if  (!in_array('pauseonhover', $key['hidden'] ) ) {
				$out .= '<label for="' . esc_attr( $id ). '_pauseonhover">';
				$out .= ' <input type="checkbox" id="' . esc_attr( $key['_id'] ) . '_pauseonhover" name="' . esc_attr( $key['_name'] ) . '[pauseonhover]" value="1" ';
				if ( !empty($key['value']['pauseonhover']) ) $meta = true; else $meta = false;
				$out .= checked( $meta , true, false );
				$out .= ' /> ';
				$out .=  ' ' . esc_html__('Pause when the user hovers over the slider', 'inbound') . '</label>';

				$out .= '<br/>';
			}

			/*
			 * Pause on Action
 			*/
			if  (!in_array('pauseonaction', $key['hidden'] ) ) {
				$out .= '<label for="' . esc_attr( $id ). '_pauseonaction">';
				$out .= ' <input type="checkbox" id="' . esc_attr( $key['_id'] ) . '_pauseonaction" name="' . esc_attr( $key['_name'] ) . '[pauseonaction]" value="1" ';
				if ( !empty($key['value']['pauseonaction']) ) $meta = true; else $meta = false;
				$out .= checked( $meta , true, false );
				$out .= ' /> ';
				$out .=  ' ' . esc_html__('Pause when the user interacts with control elements', 'inbound') . '</label>';

				$out .= '<br/>';
			}

			/*
			 * Randomization
			 */
			if  (!in_array('randomize', $key['hidden'] ) ) {
				$out .= '<label for="' . esc_attr( $id ). '_randomize">';
				$out .= ' <input type="checkbox" id="' . esc_attr( $key['_id'] ) . '_randomize" name="' . esc_attr( $key['_name'] ) . '[randomize]" value="1" ';
				if ( !empty($key['value']['randomize']) ) $meta = true; else $meta = false;
				$out .= checked( $meta , true, false );
				$out .= ' /> ';
				$out .=  ' ' . esc_html__('Randomize slide order', 'inbound') . '</label>';

				$out .= '<br/>';
			}

			/*
			 * Transition
			 */
			if  ( !in_array('transition', $key['hidden']) && !in_array('speed', $key['hidden']) ) {
				$out .= '<br/><small class="description">' . esc_html__('Select which transition to use and set the speed of the slideshow cycling in milliseconds.', 'inbound' ) . '</small><br/>';
			}
			elseif ( in_array('transition', $key['hidden'])  ) {
				$out .= '<br/><small class="description">' . esc_html__('Set the speed of the slideshow cycling in milliseconds.', 'inbound' ) . '</small><br/>';
			} else {
				$out .= '<br/><small class="description">'. esc_html__( 'Select which transition to use.', 'inbound' ) . '</small><br/>';
			}

			if  (!in_array('transition', $key['hidden'] ) ) {
				$out .= '<select id="' . esc_attr( $key['_id'] ) . '_transition" name="' . esc_attr( $key['_name'] ) . '[transition]">';
				$selected = isset( $key['value']['transition'] ) ? $key['value']['transition'] : $key['std']['transition'];
				$options = array(
						array( 'value' => 'slide', 'name' => esc_html__('Slide', 'inbound') ),
						array( 'value' => 'fade', 'name'  => esc_html__('Fade', 'inbound') ),
				);

				foreach ( $options as $field => $option )
				{
					$out .= '<option value="' . esc_attr( $option['value'] ) . '" ';

					if ( esc_attr( $selected ) == $option['value'] )
						$out .= ' selected="selected" ';

					$out .= '> '.esc_html( $option['name'] ).'</option>';
				}

				$out .= ' </select> ';
			}

			if  (!in_array('speed', $key['hidden'] ) ) {

				$out .= '<input type="number" min="0" ';
				$value = isset( $key['value']['speed'] ) ? $key['value']['speed'] : $key['std']['speed'];
				$out .= 'id="' . esc_attr( $key['_id'] ) . '_speed" name="' . esc_attr( $key['_name'] ) . '[speed]" value="' . esc_attr ( $key['value']['speed'] ) . '" ';
				$out .= ' />';


				$out .= '<br/>';
			}


			return $out;

		}

		/**
		 * Field Text
		 *
		 * @access   private
		 * @param    array
		 * @param    string
		 * @return   string
		 * @since    1.5
		 */

		function create_field_text( $key, $out = "" )
		{
			$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

			$out .= '<input type="text" ';

			if ( isset( $key['class'] ) )
				$out .= 'class="' . esc_attr( $key['class'] ) . '" ';

			$value = isset( $key['value'] ) ? $key['value'] : $key['std'];

			$out .= 'id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . '" value="' . esc_attr ( $value ) . '" ';

			if ( isset( $key['size'] ) )
				$out .= 'size="' . esc_attr( $key['size'] ) . '" ';

			$out .= ' />';

			if ( isset( $key['desc'] ) )
				$out .= '<br/><small class="description">'.esc_html( $key['desc'] ).'</small>';

			return $out;
		}


		/**
		 * Field Paragraph
		 */

		function create_field_paragraph( $key, $out = "" )
		{
			if ( isset( $key['desc'] ) )
				$out .= '<p class="description">'.esc_html( $key['desc'] ).'</p>';

			return $out;
		}

		/*
		 * Field Headline
		 */

		function create_field_headline( $key, $out = "" )
		{
			if ( isset( $key['desc'] ) )
				$out .= '<h4 class="description">'.esc_html( $key['desc'] ).'</h4>';

			return $out;
		}


		/**
		 * Color Picker
		 *
		 * @access   private
		 * @param    array
		 * @param    string
		 * @return   string
		 */

		function create_field_color( $key, $out = "" )
		{
			$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

			$out .= '<input type="text" ';

			$key['class'] = 'at-color-iris';
			$key['size']  = '8';

			if ( isset( $key['class'] ) )
				$out .= 'class="' . esc_attr( $key['class'] ) . '" ';

			$value = isset( $key['value'] ) ? $key['value'] : $key['std'];

			$out .= 'id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . '" value="' . esc_attr ( $value ) . '" ';

			if ( isset( $key['size'] ) )
				$out .= 'size="' . esc_attr( $key['size'] ) . '" ';

			$out .= ' />';

			if ( isset( $key['desc'] ) )
				$out .= '<br/><small class="description">'.esc_html( $key['desc'] ).'</small>';

			return $out;
		}


		/**
		 * Image Gallery/Slider/Image Selector
		 *
		 * @access   private
		 * @param    array
		 * @param    string
		 * @return   string
		 */

		function create_field_gallery( $key, $out = "" )
		{
			$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

			$out .= '<input type="text" ';

			if ( isset( $key['class'] ) )
				$out .= 'class="' . esc_attr( $key['class'] ) . '" ';

			$value = isset( $key['value'] ) ? $key['value'] : $key['std'];

			$out .= 'id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . '" value="' . esc_attr ( $value ) . '" ';

			if ( isset( $key['size'] ) )
				$out .= 'size="' . esc_attr( $key['size'] ) . '" ';

			$out .= ' />';

			$out .= '<input type="button" class="button gallery-picker-select" data-target="#' . esc_attr( $key['_id'] ) . '" id="'. esc_attr( $key['_id'] ) . '_ids' .'" value="'.esc_html__('Select Items', 'inbound').'">';


			if ( isset( $key['desc'] ) )
				$out .= '<br/><small class="description">'.esc_html( $key['desc'] ).'</small>';


			return $out;
		}



		/**
		 * FontAwesome Icon Picker
		 *
		 * @access   private
		 * @param    array
		 * @param    string
		 * @return   string
		 */

		function create_field_fontawesome( $key, $out = "" )
		{
			$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

			$out .= '<input type="text" class="fontawesome-picker-text" ';

			$value = isset( $key['value'] ) ? $key['value'] : $key['std'];

			$out .= 'id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . '" value="' . esc_attr ( $value ) . '" ';

			if ( isset( $key['size'] ) )
				$out .= 'size="' . esc_attr( $key['size'] ) . '" ';

			$out .= ' />';

			$out .= '<input type="button" class="button fontawesome-picker" data-target="#' . esc_attr( $key['_id'] ) . '" id="'. esc_attr( $key['_id'] ) . '_picker' .'" value="'.esc_html__('Select Icon', 'inbound').'">';

			if ( isset( $key['desc'] ) )
				$out .= '<br/><small class="description">'.esc_html( $key['desc'] ).'</small>';

			return $out;
		}


		/**
		 * Field Textarea
		 *
		 * @access   private
		 * @param    array
		 * @param    string
		 * @return   string
		 * @since    1.5
		 */

		function create_field_textarea( $key, $out = "" )
		{
			$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

			$out .= '<textarea ';

			if ( isset( $key['class'] ) )
				$out .= 'class="' . esc_attr( $key['class'] ) . '" ';

			if ( isset( $key['rows'] ) )
				$out .= 'rows="' . esc_attr( $key['rows'] ) . '" ';

			if ( isset( $key['cols'] ) )
				$out .= 'cols="' . esc_attr( $key['cols'] ) . '" ';

			$value = isset( $key['value'] ) ? $key['value'] : $key['std'];

			$out .= 'id="'. esc_attr( $key['_id'] ) .'" name="' . esc_attr( $key['_name'] ) . '">'.esc_html( $value );

			$out .= '</textarea>';

			if ( isset( $key['desc'] ) )
				$out .= '<br/><small class="description">'.esc_html( $key['desc'] ).'</small>';

			return $out;
		}

		function create_field_textarea_code( $key, $out = "" )
		{
			$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

			$out .= '<textarea ';

			if ( isset( $key['class'] ) )
				$out .= 'class="' . esc_attr( $key['class'] ) . '" ';

			if ( isset( $key['rows'] ) )
				$out .= 'rows="' . esc_attr( $key['rows'] ) . '" ';

			if ( isset( $key['cols'] ) )
				$out .= 'cols="' . esc_attr( $key['cols'] ) . '" ';

			$value = isset( $key['value'] ) ? $key['value'] : $key['std'];

			$out .= 'id="'. esc_attr( $key['_id'] ) .'" name="' . esc_attr( $key['_name'] ) . '">' . esc_html ( $value );

			$out .= '</textarea>';

			if ( isset( $key['desc'] ) )
				$out .= '<br/><small class="description">'.esc_html( $key['desc'] ).'</small>';

			return $out;
		}


		/**
		 * Field Checkbox
		 *
		 * @access   private
		 * @param    array
		 * @param    string
		 * @return   string
		 * @since    1.5
		 */

		function create_field_checkbox( $key, $out = "" )
		{

			$id = $key['_id'];
			$name = $key['name'];


			$out .= '<label for="' . esc_attr( $id ). '">';

			$out .= ' <input type="checkbox" ';

			if ( isset( $key['class'] ) )
				$out .= 'class="' . esc_attr( $key['class'] ) . '" ';

			$out .= 'id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . '" value="1" ';

			if ( ( isset( $key['value'] ) && $key['value'] == 1 ) OR ( ! isset( $key['value'] ) && $key['std'] == 1 ) )
				$out .= ' checked="checked" ';

			$out .= ' /> ';

			$out .=  ' ' . esc_html( $name ) . '</label>';

			if ( isset( $key['desc'] ) )
				$out .= '<br/><small class="description">'.esc_html( $key['desc'] ).'</small>';

			return $out;
		}


		/**
		 * Field Select
		 *
		 * @access   private
		 * @param    array
		 * @param    string
		 * @return   string
		 * @since    1.5
		 */

		function create_field_select( $key, $out = "" )
		{
			$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

			$out .= '<select id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . '" ';

			if ( isset( $key['class'] ) )
				$out .= 'class="' . esc_attr( $key['class'] ) . '" ';

			$out .= '> ';

			$selected = isset( $key['value'] ) ? $key['value'] : $key['std'];

			foreach ( $key['fields'] as $field => $option )
			{

				$out .= '<option value="' . esc_attr ( $option['value'] ) . '" ';

				if ( esc_attr( $selected ) == $option['value'] )
					$out .= ' selected="selected" ';

				$out .= '> '.esc_html( $option['name'] ).'</option>';

			}

			$out .= ' </select> ';

			if ( isset( $key['desc'] ) )
				$out .= '<br/><small class="description">'.esc_html( $key['desc'] ).'</small>';

			return $out;
		}


		/**
		 * Field Select with Options Group
		 *
		 * @access   private
		 * @param    array
		 * @param    string
		 * @return   string
		 * @since    1.5
		 */

		function create_field_select_group( $key, $out = "" )
		{

			$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

			$out .= '<select id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . '" ';

			if ( isset( $key['class'] ) )
				$out .= 'class="' . esc_attr( $key['class'] ) . '" ';

			$out .= '> ';

			$selected = isset( $key['value'] ) ? $key['value'] : $key['std'];

			foreach ( $key['fields'] as $group => $fields )
			{

				$out .= '<optgroup label="' . $group . '">';

				foreach ( $fields as $field => $option )
				{
					$out .= '<option value="' . esc_attr( $option['value'] ) . '" ';

					if ( esc_attr( $selected ) == $option['value'] )
						$out .= ' selected="selected" ';

					$out .= '> ' . esc_html( $option['name'] ) . '</option>';
				}

				$out .= '</optgroup>';

			}

			$out .= '</select>';

			if ( isset( $key['desc'] ) )
				$out .= '<br/><small class="description">'.esc_html( $key['desc'] ).'</small>';

			return $out;
		}


		/**
		 * Field Number
		 *
		 * @access   private
		 * @param    array
		 * @param    string
		 * @return   string
		 * @since    1.5
		 */
		function create_field_number( $key, $out = "" )
		{
			$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

			$out .= '<input type="number" min="0" ';

			if ( isset ( $key['class'] ) )
				$out .= 'class="' . esc_attr( $key['class'] ) . '" ';

			if ( isset( $key['units'] ) ) {
				$value = isset( $key['value']['number'] ) ? $key['value']['number'] : $key['std'];
				$out .= 'id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . '[number]" value="' . esc_attr( $value ) . '" ';
			} else {
				$value = isset( $key['value'] ) ? $key['value'] : $key['std'];
				$out .= 'id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . ' value="' . esc_attr( $value ) . '" ';
			}

			if ( isset( $key['size'] ) )
				$out .= 'size="' . esc_attr( $key['size'] ) . '" ';

			$out .= ' />';

			if ( isset( $key['units'] ) ) {

				$units = $key['units'];

				if ( ! empty ($units) ) {

					if ( count ($units) == 1 ) {
						$out .= '<span class="label-unit">' . $units[0] . '</span>';
					} else {
						$out .= '<select name="' . esc_attr( $key['_name'] ) . '[unit]" class="select-unit">';
						$selected = '';
						foreach ( $units as $unit ) {
							if ( isset ($key['value'] ) ) {
								if ( isset ( $key['value']['unit'] ) && $key['value']['unit'] == $unit ) $selected = ' selected="selected"'; else $selected = '';
							}
							$out .= '<option value="' . esc_attr( $unit ) . '"' . $selected . '>' . esc_html ( $unit ) . '</option>';
						}
						$out .= '</select>';
					}

				}

			}

			if ( isset( $key['desc'] ) )
				$out .= '<br/><small class="description">'.esc_html( $key['desc'] ).'</small>';

			return $out;
		}


		/**
		 * Field Date
		 *
		 * @access   private
		 * @param    array
		 * @param    string
		 * @return   string
		 * @since    1.5
		 */

		function create_field_date( $key, $out = "" )
		{
			$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

			$out .= '<input type="text" ';

			if ( isset( $key['class'] ) )
				$out .= 'class="' . esc_attr( $key['class'] ) . '" ';

			$value = isset( $key['value'] ) ? $key['value'] : $key['std'];

			$out .= 'id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . '" value="' . esc_attr ( $value ) . '" ';

			if ( isset( $key['size'] ) )
				$out .= 'size="' . esc_attr( $key['size'] ) . '" ';

			$out .= ' />';

			if ( isset( $key['desc'] ) )
				$out .= '<br/><small class="description">'.esc_html( $key['desc'] ).'</small>';

			return $out;
		}



		/**
		 * Field Image
		 *
		 * @access   private
		 * @param    array
		 * @param    string
		 * @return   string
		 */

		function create_field_image( $key, $out = "" )
		{
			$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

			$value = isset( $key['value'] ) ? $key['value'] : $key['std'];

			if ( !empty ($value ) ) {
				$preview = wp_get_attachment_image_src ( intval ( $value ) );
				if ( $preview ) {
					$preview_url = $preview[0];
				} else
					$preview_url = false;
			}

			if ( isset ($preview_url ) && $preview_url ) {
				$out .= '<div class="inbound-widget-preview"><img class="inbound-widget-preview-image" src="' . esc_url( $preview_url ) . '"></div>';
			} else {
				$out .= '<div class="inbound-widget-preview"><img class="inbound-widget-preview-image empty" src="' . get_template_directory_uri() . '/lib/admin/images/transparent.png"></div>';
			}

			$out .= '<input type="text" ';

			if ( isset( $key['class'] ) )
				$out .= 'class="img-picker-input ' . esc_attr( $key['class'] ) . '" ';

			$out .= 'id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . '" value="' . esc_attr ( $value ) . '" ';

			if ( isset( $key['size'] ) )
				$out .= 'size="' . esc_attr( $key['size'] ) . '" ';

			$out .= ' />';

			$out .= '<input type="button" class="button select-img select-img-widget" data-target="' . esc_attr( $key['_id'] ) . '" value="' . esc_html__('Select Image', 'inbound') . '" />';
			$out .= '<input type="button" class="button select-img clear-img-widget" data-target="' . esc_attr( $key['_id'] ) . '" data-preview="' . get_template_directory_uri() . '/lib/admin/images/transparent.png" value="' . esc_html__('Clear', 'inbound') . '" />';

			if ( isset( $key['desc'] ) )
				$out .= '<br/><small class="description">'.esc_html( $key['desc'] ).'</small>';

			return $out;
		}


		/**
		 * Field Label
		 *
		 * @access   private
		 * @param    string
		 * @param    string
		 * @return   string
		 * @since    1.5
		 */

		function create_field_label( $name = "", $id = "" ) {
			return '<label for="' . esc_attr( $id ). '">' . esc_html( $name ) . ':</label>';
		}

	} // class
}


function sr_widget_scripts()
{
	$this_theme = wp_get_theme();

	wp_enqueue_style('thickbox');
	wp_enqueue_script('thickbox');

	wp_enqueue_media();
	wp_enqueue_script('media-upload');

	wp_register_style( 'sr-custom_wp_admin_css', get_template_directory_uri() . '/lib/admin.widgets.css', false, $this_theme->get( 'Version' ) );
	wp_enqueue_style( 'sr-custom_wp_admin_css' );

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );

	wp_register_style( 'fontawesome', get_template_directory_uri() . '/css/font-awesome.min.css', false, $this_theme->get( 'Version' ) );
	wp_enqueue_style( 'fontawesome' );

	wp_enqueue_script('jquery-ui-datepicker');


	wp_enqueue_script('sr_widget_scripts', get_template_directory_uri() . '/lib/admin.widgets.js', null, null, true);
}
add_action('admin_enqueue_scripts', 'sr_widget_scripts');