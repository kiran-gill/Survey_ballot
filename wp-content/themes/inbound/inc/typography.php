<?php
/*
* Typography
*/

if (!class_exists('SR_Typography')) {

	class SR_Typography {
		public $fonts_in_use = null;
		public $font_import = null;

		public function get_fonts($font = null, $refresh = false) {

			$fonts = get_option('sr_inbound_available_fonts' , $default = false);

			if ($fonts === false || $refresh === true ){

				$fonts = array(
					'built-in' 	=> array(),
					'google'	=> array(),
					'custom'    => array()
				);

				$default_styles = array ();

				$fonts['built-in'] = array(
					'arial' => array(
						'name' => 'Arial',
						'css' => "font-family:Arial, Helvetica, sans-serif;",
						'type' => 'built-in',
						'variants' => $default_styles
					),
					'courier' => array(
						'name' => 'Arial',
						'css' => "font-family:\"Courier New\", Courier, monospace;",
						'type' => 'built-in',
						'variants' => $default_styles
					),
					'verdana' => array(
						'name' => "Verdana, Geneva",
						'css' => "font-family:Verdana, Arial, Helvetica, sans-serif;",
						'type' => 'built-in',
						'variants' => $default_styles
					),
					'trebuchet' => array(
						'name' => "Trebuchet",
						'css' => "font-family:\"Trebuchet MS\", Helvetica, sans-serif;",
						'type' => 'built-in',
						'variants' => $default_styles
					),
					'georgia' => array(
						'name' => "Georgia",
						'css' => "font-family:Georgia, \"Times New Roman\", Times, serif;",
						'type' => 'built-in',
						'variants' => $default_styles
					),
					'times' => array(
						'name' => "Times New Roman",
						'css' => "font-family:\"Times New Roman\", Times, serif;",
						'type' => 'built-in',
						'variants' => $default_styles
					),
					'tahoma' => array(
						'name' => "Tahoma, Geneva",
						'css' => "font-family:Tahoma, Geneva, sans-serif;",
						'type' => 'built-in',
						'variants' => $default_styles
					),
					'palatino' => array(
						'name' => "Palatino",
						'css' => "font-family:\"Palatino Linotype\", \"Book Antiqua\", Palatino, serif;",
						'type' => 'built-in',
						'variants' => $default_styles
					),
					'helvetica' => array(
						'name' => "Verdana, Geneva",
						'css' => "font-family:Helvetica, Arial, sans-serif;",
						'type' => 'built-in',
						'variants' => $default_styles
					),
				);

				/*
				 * Custom Fonts
				 */
				$custom_fonts = inbound_option('web_fonts_custom', false);
				if ($custom_fonts && is_array($custom_fonts)) {
					foreach ($custom_fonts as $custom_font) {
						$fonts['custom'][sanitize_title($custom_font['name'])] = array(
							'name' => $custom_font['name'],
							'face' => $custom_font['face_name'],
							'files' => array (
								'eot' => $custom_font['url_eot'],
								'woff' => $custom_font['url_woff'],
								'ttf' => $custom_font['url_ttf'],
								'svg' => $custom_font['url_svg']
							),
							'css' => "font-family:'" . $custom_font['face_name'] .  "', " . $custom_font['face_fallback'] . ";",
							'type' => 'custom',
							'variants' => $default_styles
						);
					}
				}


				/*
				 * Google Web Fonts
				 */


				$api_keys = array(
					'AIzaSyBgIcdKAZHFIwtREzf2KPlQSkRwbtLnyTE',
					'AIzaSyBukLRwzuBJiVNMpFUeDeMT1aPm-T1j59k',
					'AIzaSyC8jCiLOImmROHwLVefViluZjZ0tfkaFi0'
				);
				$k = rand(0,count($api_keys) -1 );
				$gs = wp_remote_get( 'https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha&key='.$api_keys[$k] ,array('sslverify' => false));
				if(! is_wp_error( $gs ) ) {
					$fontArray = json_decode($gs['body']);
					if (!isset($fontArray->items)) {
						$fontArray = json_decode(inbound_file_read_contents( get_template_directory() . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'googlefonts.json' ));
					}
					if (isset($fontArray->items)) {
						$fontArray = $fontArray->items;
						if (isset($fontArray) && count ($fontArray) > 0) {
							foreach ( $fontArray as $f ){
								$key = strtolower(str_replace(" ", "_", $f->family));

								$family = $f->family;
								if (substr_count($family, ' ' ) > 0) $family = "'" . $family . "'";

								$fonts['google'][$key] = array(
									'name' => $f->family,
									'import' => str_replace(" ","+",$f->family),
									'variants' => (array)$f->variants,
									'subsets' => (array)$f->subsets,
									'css' => 'font-family: '. $family . ', sans-serif, Arial, Helvetica;',
									'type' => 'google'
								);
							}
						}
					}
				}
				update_option( 'sr_inbound_available_fonts' ,$fonts);
			}

			if ($font === null){
				return $fonts;
			}else{
				if (isset($fonts['built-in'][$font])) {
					$fonts['built-in'][$font]['type'] = 'built-in';
					return $fonts['built-in'][$font];
				}
				elseif (isset($fonts['google'][$font])) {
					$fonts['google'][$font]['type'] = 'google';
					return $fonts['google'][$font];
				}
				elseif (isset($fonts['custom'][$font])) {
					$fonts['custom'][$font]['type'] = 'custom';
					return $fonts['custom'][$font];
				}
				else {
					return false;
				}
			}
		}

		/**
		 * Get list of supported font faces
		 * @return array
		 */
		public function get_font_style(){
			$default = array(
				'normal' => 'Normal',
				'italic' => 'Italic',
				'oblique ' => 'Oblique'
			);
			return $default;
		}

		/**
		 * Get list of supported font weights
		 * @return array
		 */
		public function get_font_weight(){
			$default = array(
				'regular' => 'regular',
				'bold' => 'bold',
				'italic' => 'italic',
				'bold italic' => 'bold italic',
			);
			return $default;
		}

		public function get_fonts_in_use($font_options) {
			$fonts_in_use = $this->fonts_in_use;
			foreach ($font_options as $font_option) {
				$font_option = inbound_option( $font_option );
				if (is_serialized($font_option)) {
					$font = unserialize($font_option);

					if (!isset($fonts_in_use['face']['weight'][$font['weight']]))
						$fonts_in_use[$font['face']]['weight'][$font['weight']] = 1;
				}
			}
			$this->fonts_in_use = $fonts_in_use;
			return true;
		}

		public function get_button_style_fonts( $styles = array() ) {
			$fonts_in_use = $this->fonts_in_use;
			foreach ($styles as $style) {
				$font = $style['font'];
				if ( ! isset( $fonts_in_use['face']['weight'][ $font['weight'] ] ) )
					$fonts_in_use[ $font['face'] ]['weight'][ $font['weight'] ] = 1;

			}
			$this->fonts_in_use = $fonts_in_use;
			return true;
		}

		public function get_font_family( $option ) {
			$option = inbound_option ( $option, false );
			if (isset($option) && is_serialized( $option )) {
				$options = unserialize( $option );

				$font = $this->get_fonts( $options['face'] );
				return $font['css'];
			}
			return false;
		}

		public function get_font_color( $option ) {
			$option = inbound_option ( $option, false );
			if (isset($option) && is_serialized( $option )) {
				$options = unserialize( $option );
				return $options['color'];
			}
			return false;
		}

		public function get_font_size( $option ) {
			$option = inbound_option ( $option, false );
			if (isset($option) && is_serialized( $option )) {
				$options = unserialize( $option );
				return $options['size'];
			}
			return false;
		}



		public function get_typography_css( $option ) {

			if ( is_array($option) ) {
				// we already have our option
			} else {
				$option = inbound_option ( $option, false );
			}

			if ( ( isset($option) && is_serialized( $option ) ) || is_array($option) ) {

				if (is_serialized( $option )) $options = unserialize( $option ); else $options = $option;

				$font = $this->get_fonts( $options['face'] );
				$options['family'] = $font['css'];

				$options['weight'] = str_replace('regular', 'normal', $options['weight']);

				$options['style'] = '';

				if (substr_count($options['weight'], 'italic')) {
					$options['style'] = 'font-style: italic;' . "\n" ;
					$options['weight'] = str_replace('italic', '', $options['weight']);
				} else {
					$options['style'] = 'font-style: normal;' . "\n" ;
				}

				$css = $options['family'] . "\n";
				$css .= 'font-size: ' . $options['size'] . ';' . "\n";
				$css .= 'font-weight: ' . $options['weight'] . ';' . "\n";
				$css .= $options['style'];


				if ($options['color']) {
					$css .= 'color: ' . $options['color'] . ";\n";
				}

				return $css;

			}
			return '';
		}

		public function get_custom_fonts_import() {
			$custom_fonts = array();
			foreach ( $this->fonts_in_use as $slug => $options ) {
				$font = $this->get_fonts( $slug );
				if ($font && $font['type'] == 'custom') {
					$custom_fonts[] = $font;
				}
			}
			if (count($custom_fonts) > 0) return $custom_fonts; else return false;
		}

		public function get_fonts_import() {
			$import = null;
			$fonts = null;
			foreach ($this->fonts_in_use as $slug => $options) {
				$font = $this->get_fonts($slug);

				$variants = null;

				if ( $font && $font['type'] != 'built-in' && $font['type'] != 'custom' ) {

					if (isset($options['weight'])) {
						$variants = implode ( ',', array_keys ( $options['weight'] ) );
					}

					$tmp = '';
					$tmp .= $font['import'];

					if ($variants) {
						$tmp .= ':' . $variants;
					}

					$fonts[] =  $tmp;

				}
			}

			if ($fonts && count($fonts) > 0) {
				$import = implode ( "%7c", $fonts );
				return $import;
			}
			else {
				return false;
			}
		}
	}
}
