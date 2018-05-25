<?php
/* Based on http://wp.tutsplus.com/tutorials/creative-coding/custom-post-type-helper-class/
 * Further developed for ShapingRain.com by the ShapingRain.com development team
 * GPLv2
 */

if ( ! class_exists( 'SR_Custom_Post_Type') ) :
class SR_Custom_Post_Type
	{
		public $post_type_name;
		public $post_type_args;
		public $post_type_labels;
		
		/* Class constructor */
		public function __construct( $name, $args = array(), $labels = array() )
		{
			// Set some important variables
			$this->post_type_name		= strtolower( str_replace( ' ', '_', $name ) );
			$this->post_type_args 		= $args;
			$this->post_type_labels 	= $labels;

			// Add action to register the post type, if the post type doesnt exist
			if( ! post_type_exists( $this->post_type_name ) )
			{
				add_action( 'init', array( &$this, 'register_post_type' ) );
			}

		}
		
		/* Method which registers the post type */
		public function register_post_type()
		{		
			//Capitalize the words and make it plural
			$name 		= ucwords( str_replace( '_', ' ', $this->post_type_name ) );

            if (substr($name, strlen($name)-1, 1) == "y") {
                $plural 	= substr($name, 0, strlen($name)-1) . 'ies';
            }
            else {
                $plural 	= $name . 's';
            }

			// We set the default labels based on the post type name and plural. We overwrite them with the given labels.
			$labels = array_merge(

				// Default
				array(
					'name' 					=> $plural,
					'singular_name' 		=> $name,
					'add_new' 				=> sprintf ( __( 'Add New %s', 'inbound' ), ucwords( $name ) ),
					'add_new_item' 			=> sprintf ( __( 'Add New %s', 'inbound' ), $name ),
					'edit_item' 			=> sprintf ( __( 'Edit %s', 'inbound' ), $name ),
					'new_item' 				=> sprintf ( __( 'New %s', 'inbound' ), $name ),
					'all_items' 			=> sprintf ( __( 'All %s', 'inbound' ), $plural ),
					'view_item' 			=> sprintf ( __( 'View %s', 'inbound' ), $name ),
					'search_items' 			=> sprintf ( __( 'Search %s', 'inbound' ), $plural ),
					'not_found' 			=> sprintf ( __( 'No %s found', 'inbound'), strtolower( $plural ) ),
					'not_found_in_trash' 	=> sprintf ( __( 'No %s found in Trash', 'inbound'), strtolower( $plural ) ),
					'parent_item_colon' 	=> '',
					'menu_name' 			=> $plural
				),

				// Given labels
				$this->post_type_labels

			);

			// Same principle as the labels. We set some default and overwite them with the given arguments.
			$args = array_merge(

				// Default
				array(
					'label' 				=> $plural,
					'labels' 				=> $labels,
					'public' 				=> true,
					'show_ui' 				=> true,
					'supports' 				=> array( 'title', 'editor' ),
					'show_in_nav_menus' 	=> true,
					'_builtin' 				=> false,
				),

				// Given args
				$this->post_type_args

			);

			// Register the post type
			register_post_type( $this->post_type_name, $args );
		}
		
	}
endif; // End Check Class Exists
?>
