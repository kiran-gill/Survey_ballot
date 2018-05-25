<?php
/**
 * Custom template tags and content rendering functions
 *
 * @package inbound
 */


if ( ! function_exists( 'inbound_custom_block' ) ) {
	function inbound_custom_block( $key ) {
		global $inbound_custom_blocks;
		$key = strtolower( $key );

		if ( inbound_option( 'advanced_global_parse_custom_blocks', true ) ) {
			if ( isset( $inbound_custom_blocks[ $key ] ) ) {
				do_action( 'inbound_custom_block_before_' . $key );
				echo do_shortcode( $inbound_custom_blocks[ $key ] );
				do_action( 'inbound_custom_block_after_' . $key );

				return true;
			} elseif ( function_exists( 'inbound_custom_block_' . $key ) ) {
				do_action( 'inbound_custom_block_before_' . $key );
				call_user_func( 'inbound_custom_block_' . $key );
				do_action( 'inbound_custom_block_after_' . $key );

				return true;
			} elseif ( locate_template( 'custom-' . $key . '.php' ) != '' ) {
				get_template_part( 'custom', $key );

				return true;
			}

			return false;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'inbound_slider_options' ) ) {
	function inbound_slider_options( $options ) {
		if ( ! is_array( $options ) ) {
			$options = array();
		}

		$defaults = array(
			'controls'      => false,
			'pips'          => false,
			'transition'    => 'fade',
			'pauseonhover'  => false,
			'pauseonaction' => false,
			'randomize'     => false,
			'speed'         => '7000'
		);
		$options  = array_merge( $defaults, $options );

		$classes = array();
		if ( $options['controls'] ) {
			$classes[] = "has-controls";
		}
		if ( $options['pips'] ) {
			$classes[] = "has-item-navigation";
		}
		if ( $options['pauseonaction'] ) {
			$classes[] = "pause-on-action";
		}
		if ( $options['pauseonhover'] ) {
			$classes[] = "pause-on-hover";
		}
		if ( $options['randomize'] ) {
			$classes[] = "randomize";
		}

		if ( isset ( $options['transition'] ) && $options['transition'] == 'slide' ) {
			$classes[] = "transition-slide";
		}
		if ( isset ( $options['transition'] ) && $options['transition'] == 'fade' ) {
			$classes[] = "transition-fade";
		}

		$data = '';
		if ( isset ( $options['speed'] ) && intval( $options['speed'] ) > 0 ) {
			$data .= ' data-speed="' . $options['speed'] . '"';
		}

		return array(
			'classes' => ' ' . implode( ' ', $classes ),
			'data'    => $data
		);
	}
}

if ( ! function_exists( 'inbound_wp_link_pages' ) ) {
	function inbound_wp_link_pages( $args = '' ) {
		$defaults = array(
			'before'           => '<p>' . __( 'Pages:', 'inbound' ),
			'after'            => '</p>',
			'link_before'      => '',
			'link_after'       => '',
			'next_or_number'   => 'number',
			'separator'        => ' ',
			'nextpagelink'     => __( 'Next page', 'inbound' ),
			'previouspagelink' => __( 'Previous page', 'inbound' ),
			'pagelink'         => '%',
			'echo'             => 1
		);

		$r = wp_parse_args( $args, $defaults );
		$r = apply_filters( 'wp_link_pages_args', $r );
		extract( $r, EXTR_SKIP );

		global $page, $numpages, $multipage, $more;

		$output = '';
		if ( $multipage ) {
			if ( 'number' == $next_or_number ) {
				$output .= $before;
				for ( $i = 1; $i <= $numpages; $i ++ ) {
					$link = $link_before . str_replace( '%', $i, $pagelink ) . $link_after;
					if ( $i != $page || ! $more && 1 == $page ) {
						$link = _wp_link_page( $i ) . $link . '</a>';
					} else {
						$output .= '<span class="current-page">';
					}

					$link = apply_filters( 'wp_link_pages_link', $link, $i );

					if ( $i != $page || ! $more && 1 == $page ) {
						$output .= $separator . $link;
					} else {
						$output .= $link;
					}

					if ( $i == $page ) {
						$output .= '</span>';
					}
				}
				$output .= $after;
			} elseif ( $more ) {
				$output .= $before;
				$i = $page - 1;
				if ( $i ) {
					$link = _wp_link_page( $i ) . $link_before . $previouspagelink . $link_after . '</a>';
					$link = apply_filters( 'wp_link_pages_link', $link, $i );
					$output .= $separator . $link;
				}
				$i = $page + 1;
				if ( $i <= $numpages ) {
					$link = _wp_link_page( $i ) . $link_before . $nextpagelink . $link_after . '</a>';
					$link = apply_filters( 'wp_link_pages_link', $link, $i );
					$output .= $separator . $link;
				}
				$output .= $after;
			}
		}

		$output = apply_filters( 'wp_link_pages', $output, $args );

		if ( $echo ) {
			echo $output;
		}

		return $output;
	}
}


if ( ! function_exists( 'inbound_has_share' ) ) {
	function inbound_has_share() {
		if ( ! ( is_single() || is_page() ) ) {
			false;
		}

		$icons   = inbound_option( 'blog_share_options', false );
		$enabled = inbound_option( 'blog_share_enabled', false );
		$custom  = inbound_option( 'blog_share_custom', false );

		if ( $enabled == false) {
			return false;
		}

		if ( ! empty ( $custom ) ) {
			return true;
		}

		if ( empty ( $icons ) ) {
			return false;
		}

		return true;

	}
}

if ( ! function_exists( 'inbound_share' ) ) {
	function inbound_share( $echo = true, $label = true, $style = false, $size = false ) {
		if ( ! ( is_single() || is_page() ) ) {
			return;
		}


		if ( ! $style ) {
			$style = inbound_option( 'blog_share_style', false );
		}

		if ( ! $size ) {
			$size = inbound_option( 'blog_share_size', false );
		}

		$icons   = inbound_option( 'blog_share_options', false );
		$enabled = inbound_option( 'blog_share_enabled', false );
		$custom  = inbound_option( 'blog_share_custom', false );


		$out = '';

		if ( $custom && trim( $custom ) != "" ) {
			$out .= do_shortcode( $custom );

			return;
		}

		if ( $icons && $enabled && is_array( $icons ) ) {
			$url   = urlencode( get_permalink() );
			$title = urlencode( get_the_title() );
			$out .= '<div class="share-icons-container">';
			if ( $label ) {
				$out .= '<span>' . esc_html__( 'Share on:', 'inbound' ) . '</span>';
			}
			$out .= '<ul class="share-icons style-' . $style . ' icon-size-' . $size . '">';
			foreach ( $icons as $icon ) {
				switch ( $icon ) {
					case "facebook":
						$out .= '<li class="facebook"><a class="facebook" target="_blank" href="' . esc_url( 'http://www.facebook.com/sharer.php?u=' . $url ) . '" title="' . esc_attr( get_the_title() ) . '" rel="nofollow"><i class="fa fa-' . $size . 'x fa-facebook"></i></a></li>';
						break;
					case "twitter";
						$out .= '<li class="twitter"><a class="twitter" target="_blank" href="' . esc_url( 'https://twitter.com/share?text=' . esc_attr( get_the_title() ) . '&amp;url=' . $url ) . '" title="' . esc_attr( get_the_title() ) . '" rel="nofollow"><i class="fa fa-' . $size . 'x fa-twitter"></i></a></li>';
						break;
					case "googleplus":
						$out .= '<li class="googleplus"><a class="googleplus" target="_blank" href="' . esc_url( 'https://plus.google.com/share?url=' . $url ) . '" title="' . esc_attr( get_the_title() ) . '" rel="nofollow"><i class="fa fa-' . $size . 'x fa-google-plus"></i></a></li>';
						break;
					case "tumblr":
						$out .= '<li class="tumblr"><a class="tumblr" target="_blank" href="' . esc_url( 'http://tumblr.com/share?s=&amp;v=3&amp;t=' . $title . '&amp;u=' . $url ) . '" title="' . esc_attr( get_the_title() ) . '" rel="nofollow"><i class="fa fa-' . $size . 'x fa-tumblr"></i></a></li>';
						break;
					case "pinterest":
						$out .= '<li class="pinterest"><a class="pinterest" target="_blank" href="' . esc_url( 'http://pinterest.com/pin/create/button/?' . $title . '&amp;url=' . $url ) . '" title="' . esc_attr( get_the_title() ) . '" rel="nofollow"><i class="fa fa-' . $size . 'x fa-pinterest"></i></a></li>';
						break;
					case "linkedin":
						$out .= '<li class="linkedin"><a class="linkedin" target="_blank" href="' . esc_url( 'https://www.linkedin.com/cws/share?url=' . $url ) . '" title="' . esc_attr( get_the_title() ) . '" rel="nofollow"><i class="fa fa-' . $size . 'x fa-linkedin"></i></a></li>';
						break;
					case "reddit":
						$out .= '<li class="reddit"><a class="reddit" target="_blank" href="' . esc_url( 'http://www.reddit.com/submit?url=' . $url . '&title=' . $title ) . '" title="' . esc_attr( get_the_title() ) . '" rel="nofollow"><i class="fa fa-' . $size . 'x fa-reddit"></i></a></li>';
						break;
					case "stumbleupon":
						$out .= '<li class="stumbleupon"><a class="stumbleupon" target="_blank" href="' . esc_url( 'http://www.stumbleupon.com/submit?url=' . $url . '&title=' . $title ) . '" title="' . esc_attr( get_the_title() ) . '" rel="nofollow"><i class="fa fa-' . $size . 'x fa-stumbleupon"></i></a></li>';
						break;
					case "vk":
						$out .= '<li class="vk"><a class="vk" target="_blank" href="' . esc_url( 'http://vkontakte.ru/share.php?url=' . $url . '&title=' . $title ) . '" title="' . esc_attr( get_the_title() ) . '" rel="nofollow"><i class="fa fa-' . $size . 'x fa-vk"></i></a></li>';
						break;
				}
			}
			$out .= '</ul></div>';
		}

		if ( $echo ) {
			echo $out;
		} else {
			return $out;
		}

	}
}


function inbound_pagination() {
	if ( is_archive() || is_category() || is_search() || is_home() ) {
		$type = inbound_option( 'archive_posts_navigation', 'nextprev' );
	} else {
		$type = inbound_option( 'blog_posts_navigation', 'nextprev' );
	}
	if ( is_single() ) {
		$type = inbound_option ( 'single_posts_navigation', '');
	}

	if ( $type == "nextprev" ) {
		inbound_content_nav( 'posts-navigation-bottom' );
	}
	if ( $type == "numeric" ) {
		inbound_numeric_posts_nav();
	}
}

if ( ! function_exists( 'inbound_content_nav' ) ) :
	/**
	 * Display navigation to next/previous pages when applicable
	 */
	function inbound_content_nav( $nav_id ) {
		global $wp_query, $post;

		// Don't print empty markup on single pages if there's nowhere to navigate.
		if ( is_single() ) {
			$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
			$next     = get_adjacent_post( false, '', false );

			if ( ! $next && ! $previous ) {
				return;
			}
		}

		// Don't print empty markup in archives if there's only one page.
		if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) ) {
			return;
		}

		$nav_class = ( is_single() ) ? 'navigation-post' : 'navigation-paging';

		?>
		<nav role="navigation" id="<?php echo esc_attr( $nav_id ); ?>" class="<?php echo $nav_class; ?>">
			<h1 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'inbound' ); ?></h1>

			<?php if ( is_single() ) : // navigation links for single posts
				$prev = get_adjacent_post ( false, '', true );
				$next = get_adjacent_post ( false, '', false );
				if ( $prev && $next ) $both = ''; else $both = 'class="pagination-one-item"';
				?>
				<!--Start of Pagination Single Post-->
				<div id="pagination-single"<?php echo $both ?>>
					<?php if ( ! empty ( $prev ) ) : ?>
						<?php previous_post_link( '%link', '<div class="pagination-single-link link-prev"><span>' . esc_html__( 'Previous Post', 'inbound' ) . '</span><h4>%title</h4></div>' ); ?>
					<?php endif; ?>
					<?php if ( ! empty ( $next ) ) : ?>
						<?php next_post_link( '%link', '<div class="pagination-single-link link-next"><span>' . esc_html__( 'Next Post', 'inbound' ) . '</span><h4>%title</h4></div>' ); ?>
					<?php endif; ?>
				</div>
				<!--End of Pagination Single Post-->

			<?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : // navigation links for home, archive, and search pages ?>

				<?php if ( get_next_posts_link() ) : ?>
					<div class="nav-previous"><?php next_posts_link( inbound_esc_html ( __( '<span class="meta-nav">&larr;</span> Older posts', 'inbound' ) ) ); ?></div>
				<?php endif; ?>

				<?php if ( get_previous_posts_link() ) : ?>
					<div class="nav-next"><?php previous_posts_link( inbound_esc_html ( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'inbound' ) ) ); ?></div>
				<?php endif; ?>

			<?php endif; ?>

		</nav><!-- #<?php echo esc_html( $nav_id ); ?> -->
		<?php
	}
endif; // inbound_content_nav


if ( ! function_exists( 'inbound_numeric_posts_nav' ) ) {
	function inbound_numeric_posts_nav() {

		if ( is_singular() ) {
			return;
		}

		global $wp_query;

		/** Stop execution if there's only 1 page */
		if ( $wp_query->max_num_pages <= 1 ) {
			return;
		}

		$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
		$max   = intval( $wp_query->max_num_pages );

		/**    Add current page to the array */
		if ( $paged >= 1 ) {
			$links[] = $paged;
		}

		/**    Add the pages around the current page to the array */
		if ( $paged >= 3 ) {
			$links[] = $paged - 1;
			$links[] = $paged - 2;
		}

		if ( ( $paged + 2 ) <= $max ) {
			$links[] = $paged + 2;
			$links[] = $paged + 1;
		}

		echo '<div class="navigation-posts-num"><ul>' . "\n";

		/**    Previous Post Link */
		if ( get_previous_posts_link() ) {
			printf( '<li>%s</li>' . "\n", get_previous_posts_link() );
		}

		/**    Link to first page, plus ellipses if necessary */
		if ( ! in_array( 1, $links ) ) {
			$class = 1 == $paged ? ' class="active"' : '';

			printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

			if ( ! in_array( 2, $links ) ) {
				echo '<li class="ellipses"><span>&hellip;</span></li>';
			}
		}

		/**    Link to current page, plus 2 pages in either direction if necessary */
		sort( $links );
		foreach ( (array) $links as $link ) {
			$class = $paged == $link ? ' class="active"' : '';
			printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
		}

		/**    Link to last page, plus ellipses if necessary */
		if ( ! in_array( $max, $links ) ) {
			if ( ! in_array( $max - 1, $links ) ) {
				echo '<li class="ellipses"><span>&hellip;</span></li>' . "\n";
			}

			$class = $paged == $max ? ' class="active"' : '';
			printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
		}

		/**    Next Post Link */
		if ( get_next_posts_link() ) {
			printf( '<li>%s</li>' . "\n", get_next_posts_link() );
		}

		echo '</ul></div>' . "\n";

	}
}


// Related Posts Function, derived from Bones framework, modified
if ( ! function_exists( 'inbound_related_posts' ) ) {
	function inbound_related_posts() {
		global $post;
		$tags = wp_get_post_tags( $post->ID );
		if ( $tags ) {
			$tag_arr = '';
			foreach ( $tags as $tag ) {
				$tag_arr .= $tag->slug . ',';
			}
			$args          = array(
				'tag'          => $tag_arr,
				'numberposts'  => 4,
				'post__not_in' => array( $post->ID )
			);
			$related_posts = get_posts( $args );
			if ( $related_posts ) {
				echo '<aside id="similar-posts" class="blog-section">';
				echo '<h3>' . esc_html__( 'You might also be interested in these posts...', 'inbound' ) . '</h3>';
				echo '<ul>';
				foreach ( $related_posts as $post ) : setup_postdata( $post );
					$rel_format = get_post_format();
					if ( false === $rel_format ) {
						$rel_format = 'standard';
					}
					?>
					<li class="col-4">
						<a class="entry-unrelated format-<?php echo $rel_format ?>" href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
							<?php if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
								the_post_thumbnail( 'post-thumbnail-medium' );
							}
							?>
							<h4><?php the_title(); ?></h4>
							<span class="meta-date"><?php the_time( 'F j, Y' ); ?></span>
						</a>
					</li>
				<?php endforeach;
				echo '</ul></aside>';
			}
		}
		wp_reset_query();
	}
}


if ( ! function_exists( 'inbound_comment_form' ) ) :
	function inbound_comment_form() {
		$commenter     = wp_get_current_commenter();
		$req           = get_option( 'require_name_email' );
		$aria_req      = ( $req ? " aria-required='true'" : '' );
		$user          = wp_get_current_user();
		$user_identity = $user->display_name;

		$args = array(
			'id_form'              => 'commentform',
			'id_submit'            => 'submit',
			'title_reply'          => esc_html__( 'Leave a Reply', 'inbound' ),
			'title_reply_to'       => esc_html__( 'Leave a Reply to %s', 'inbound' ),
			'cancel_reply_link'    => esc_html__( 'Cancel Reply', 'inbound' ),
			'label_submit'         => esc_html__( 'Post Comment', 'inbound' ),
			'comment_field'        => '<p class="comment-form-comment">' .
			                          '<textarea id="comment" name="comment" cols="45" placeholder="' . _x( 'Comment', 'noun', 'inbound' ) . '" rows="8" aria-required="true">' .
			                          '</textarea></p>',
			'must_log_in'          => '<p class="must-log-in">' .
			                          sprintf(
				                          inbound_esc_html( __( 'You must be <a href="%s">logged in</a> to post a comment.', 'inbound' ) ),
				                          esc_url ( wp_login_url( apply_filters( 'the_permalink', get_permalink() ) ) )
			                          ) . '</p>',
			'logged_in_as'         => '<p class="logged-in-as">' .
			                          sprintf(
				                          inbound_esc_html ( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'inbound' ) ),
				                          esc_url( admin_url( 'profile.php' ) ),
				                          $user_identity,
				                          esc_url( wp_logout_url( apply_filters( 'the_permalink', get_permalink() ) ) )
			                          ) . '</p>',
			'comment_notes_before' => '',
			'comment_notes_after'  => '',
			'comment_notes_before' => '',

			'submit_field' => '<p class="comment-notes">' .
			                  esc_html__( 'Your email address will not be published.', 'inbound' ) .
			                  '</p><p class="form-submit">%1$s %2$s</p>',
			'fields'               => apply_filters( 'comment_form_default_fields', array(

					'author' =>
						'<p class="comment-form-author">' .
						'<input id="author" name="author" type="text" placeholder="' . esc_html__( 'Name', 'inbound' ) . '" value="' . esc_attr( $commenter['comment_author'] ) .
						'" size="30"' . $aria_req . ' /></p>',
					'email'  =>
						'<p class="comment-form-email">' .
						'<input id="email" name="email" type="text" placeholder="' . esc_html__( 'Email', 'inbound' ) . '" value="' . esc_attr( $commenter['comment_author_email'] ) .
						'" size="30"' . $aria_req . ' /></p>',
					'url'    =>
						'<p class="comment-form-url">' .
						'<input id="url" name="url" type="text" placeholder="' . esc_html__( 'Website', 'inbound' ) . '" value="' . esc_attr( $commenter['comment_author_url'] ) .
						'" size="30" /></p>'
				)
			),
		);
		comment_form( $args );
	}
endif;


if ( ! function_exists( 'inbound_author_social' ) ) {
	function inbound_author_social() {
		$author_id = get_the_author_meta( 'ID' );
		$user_data = get_userdata( $author_id );

		$social = array();


		$social['website'] = array(
			'value'  => $user_data->user_url,
			'icon'   => 'fa fa-home fa-1x',
			'label'  => esc_html__( 'Website', 'inbound' ),
			'prefix' => ''
		);

		if ( defined ( 'INBOUND_USER' ) ) {

			$social['twitter'] = array(
				'value'  => get_user_meta( $author_id, 'twitter', true ),
				'icon'   => 'fa fa-twitter fa-1x',
				'label'  => esc_html__( 'Twitter', 'inbound' ),
				'prefix' => 'https://www.twitter.com/'
			);

			$social['facebook'] = array(
				'value'  => get_user_meta( $author_id, 'facebook', true ),
				'icon'   => 'fa fa-facebook fa-1x',
				'label'  => esc_html__( 'Facebook', 'inbound' ),
				'prefix' => 'https://www.facebook.com/'
			);

			$social['googleplus'] = array(
				'value'  => get_user_meta( $author_id, 'gplus', true ),
				'icon'   => 'fa fa-google-plus fa-1x',
				'label'  => esc_html__( 'Google+', 'inbound' ),
				'prefix' => 'https://plus.google.com/'
			);

			$social['skype'] = array(
				'value'  => get_user_meta( $author_id, 'skype', true ),
				'icon'   => 'fa fa-skype fa-1x',
				'label'  => esc_html__( 'Skype', 'inbound' ),
				'prefix' => 'skype://'
			);

			$social['linkedin'] = array(
				'value'  => get_user_meta( $author_id, 'linkedin', true ),
				'icon'   => 'fa fa-linkedin fa-1x',
				'label'  => esc_html__( 'LinkedIn', 'inbound' ),
				'prefix' => ''
			);

			$social['instagram'] = array(
				'value'  => get_user_meta( $author_id, 'instagram', true ),
				'icon'   => 'fa fa-instagram fa-1x',
				'label'  => esc_html__( 'Instagram', 'inbound' ),
				'prefix' => 'https://instagram.com/'
			);

			$social['pinterest'] = array(
				'value'  => get_user_meta( $author_id, 'pinterest', true ),
				'icon'   => 'fa fa-pinterest fa-1x',
				'label'  => esc_html__( 'Pinterest', 'inbound' ),
				'prefix' => 'https://www.pinterest.com/'
			);

			$social['flickr'] = array(
				'value'  => get_user_meta( $author_id, 'flickr', true ),
				'icon'   => 'fa fa-flickr fa-1x',
				'label'  => esc_html__( 'Flickr', 'inbound' ),
				'prefix' => 'https://www.flickr.com/photos/'
			);

			$social['tumblr'] = array(
				'value'  => get_user_meta( $author_id, 'tumblr', true ),
				'icon'   => 'fa fa-tumblr fa-1x',
				'label'  => esc_html__( 'Tumblr', 'inbound' ),
				'prefix' => ''
			);

			$social['foursquare'] = array(
				'value'  => get_user_meta( $author_id, 'foursquare', true ),
				'icon'   => 'fa fa-foursquare fa-1x',
				'label'  => esc_html__( 'Foursquare', 'inbound' ),
				'prefix' => 'https://foursquare.com/'
			);

			$social['youtube'] = array(
				'value'  => get_user_meta( $author_id, 'youtube', true ),
				'icon'   => 'fa fa-youtube fa-1x',
				'label'  => esc_html__( 'YouTube', 'inbound' ),
				'prefix' => ''
			);

			$social['vimeo'] = array(
				'value'  => get_user_meta( $author_id, 'vimeo', true ),
				'icon'   => 'fa fa-vimeo-square fa-1x',
				'label'  => esc_html__( 'Vimeo', 'inbound' ),
				'prefix' => 'https://vimeo.com/'
			);

		}

		$output = '';
		foreach ( $social as $service => $meta ) {
			$value = $meta['value'];
			if ( $value ) {
				$output .= '<li><a href="' . esc_url( $meta['prefix'] . $meta['value'] ) . '" target="_blank" class="' . $service . '"><i class="' . $meta['icon'] . '"></i><span>' . $meta['label'] . '</span></a></li>' . "\n";
			}
		}

		if ( $output ) {
			echo '<ul class="social-icons style-white icon-size-1">' . "\n";
			echo $output . "\n";
			echo '</ul>' . "\n";
		}


	}
}


if ( ! function_exists( 'inbound_comment' ) ) :
	/**
	 * Template for comments and pingbacks.
	 *
	 * Used as a callback by wp_list_comments() for displaying the comments.
	 */
	function inbound_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
				?>
				<li class="post pingback">
				<p><?php esc_html_e( 'Pingback:', 'inbound' ); ?><?php comment_author_link(); ?><?php edit_comment_link( esc_html__( 'Edit', 'inbound' ), '<span class="edit-link">', '<span>' ); ?></p>
				<?php
				break;
			default :
				?>
				<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
					<article id="comment-<?php comment_ID(); ?>" class="comment">
						<?php echo get_avatar( $comment, 65 ); ?>
						<p class="comment-meta">
							<span class="fn n comment_name"><?php echo get_comment_author_link(); ?></span>

							<time datetime="<?php comment_time( 'c' ); ?>">
								<?php printf( _x( '%1$s at %2$s', '1: date, 2: time', 'inbound' ), get_comment_date(), get_comment_time() ); ?>
							</time>

							<?php
							comment_reply_link( array_merge( $args, array(
								'depth'     => $depth,
								'max_depth' => $args['max_depth'],
							) ) );
							?>
						</p>
						<p><?php comment_text(); ?></p>


					</article>
				</li>

				<?php
				break;
		endswitch;
	}
endif; // ends check for inbound_comment()


if ( ! function_exists( 'inbound_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function inbound_posted_on() {
		printf( inbound_esc_html( __( '<time class="entry-date" datetime="%3$s">%4$s</time>', 'inbound' ) ),
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'inbound' ), get_the_author() ) ),
			get_the_author()
		);
	}
endif;


if ( ! function_exists( 'inbound_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function inbound_posted_by() {
		printf( inbound_esc_html( __( '<span class="author vcard meta_author"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span>', 'inbound' ) ),
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( esc_html__( 'View all posts by %s', 'inbound' ), get_the_author() ) ),
			get_the_author()
		);
	}
endif;


if ( ! function_exists( 'inbound_tags' ) ) {
	function inbound_tags() {
		$posttags = get_the_tags();
		if ( $posttags ) {
			foreach ( $posttags as $tag ) {
				echo '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '" rel="tag">' . $tag->name . '</a>';
			}
		}
	}
}

/*
 * Categories
 */
if ( ! function_exists( 'inbound_categories' ) ) {
	function inbound_categories() {
		if ( inbound_categorized_blog() ) {
			$categories = get_the_category();
			$separator  = ', ';
			$output     = '';
			if ( $categories ) {
				echo '<span class="meta_categories">';
				foreach ( $categories as $category ) {
					$output .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" title="' . esc_attr( sprintf( esc_html__( "View all posts in %s", 'inbound' ), $category->name ) ) . '">' . $category->cat_name . '</a>' . $separator;
				}
				echo trim( $output, $separator );
				echo '</span>';
			}
		}
	}
}


/**
 * Returns true if a blog has more than 1 category
 */
function inbound_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'all_the_cool_cats', $all_the_cool_cats );
	}

	if ( '1' != $all_the_cool_cats ) {
		// This blog has more than 1 category so inbound_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so inbound_categorized_blog should return false
		return false;
	}
}

/**
 * Flush out the transients used in inbound_categorized_blog
 */
function inbound_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'all_the_cool_cats' );
}

add_action( 'edit_category', 'inbound_category_transient_flusher' );
add_action( 'save_post', 'inbound_category_transient_flusher' );


/**
 * Breadcrumbs
 */

if ( ! function_exists( 'inbound_breadcrumb' ) ) {
	function inbound_breadcrumb() {
		global $post;

		$items = array();

		$items[] = array(
			'link'  => home_url('/'),
			'title' => get_bloginfo( 'name' )
		);

		if ( is_home() ) {
			return;
		}

		if ( ! is_home() ) {

			if ( is_category() || is_single() ) {
				$cats = get_the_category( $post->ID );

				foreach ( $cats as $cat ) {
					$items[] = array(
						'link'  => get_category_link( $cat->cat_ID ),
						'title' => $cat->cat_name
					);
				}

				if ( is_single() ) {
					$items[] = array(
						'title' => get_the_title()
					);
				}
			} elseif ( is_page() ) {

				if ( $post->post_parent ) {
					$anc      = get_post_ancestors( $post->ID );
					if ( $anc && is_array( $anc )) {
						$anc = array_reverse( $anc );
						foreach ( $anc as $ancestor ) {
							$anc_link = get_permalink( $ancestor );
							$items[] = array(
								'title' => get_the_title( $ancestor ),
								'link'  => $anc_link
							);
						}
						$items[] = array(
							'title' => get_the_title()
						);
					}
				} else {
					$items[] = array(
						'title' => get_the_title()
					);
				}

			} elseif ( is_tag() ) {
				$items[] = array(
					'title' => sprintf( esc_html__( "Tag: %s", "inbound" ), inbound_get_the_title() )
				);
			} elseif ( is_day() ) {
				$items[] = array(
					'title' => sprintf( esc_html__( "Archive: %s", "inbound" ), get_the_time( 'F jS, Y' ) )
				);
			} elseif ( is_month() ) {
				$items[] = array(
					'title' => sprintf( esc_html__( "Archive: %s", "inbound" ), get_the_time( 'F, Y' ) )
				);
			} elseif ( is_year() ) {
				$items[] = array(
					'title' => sprintf( esc_html__( "Archive: %s", "inbound" ), get_the_time( 'Y' ) )
				);
			} elseif ( is_author() ) {
				$author  = get_userdata( get_query_var( 'author' ) );
				$items[] = array(
					'title' => sprintf( esc_html__( "Author's Archive for %s", "inbound" ), $author->display_name() )
				);
			} elseif ( isset( $_GET['paged'] ) && ! empty( $_GET['paged'] ) ) {
				$items[] = array(
					'title' => sprintf( esc_html__( "Blog Archive: %s", "inbound" ), inbound_get_the_title() )
				);
			} elseif ( is_search() ) {
				$items[] = array(
					'title' => sprintf( esc_html__( "Search Results: %s", "inbound" ), get_query_var( 's' ) )
				);
			} else {
				$items[] = array(
					'title' => inbound_get_the_title()
				);
			}

			echo '<ul xmlns:v="http://rdf.data-vocabulary.org/#" class="breadcrumb">' . "\n";
			foreach ( $items as $item ) {
				echo '<li>';
				echo '<span  typeof="v:Breadcrumb">';
				if ( isset( $item['link'] ) ) {
					echo '<a rel="v:url" property="v:title" href="' . esc_url( $item['link'] ) . '">';
				}
				echo $item['title'];
				if ( isset( $item['link'] ) ) {
					echo '</a>';
				}
				echo '</span>';
				echo '</li>';
			}
			echo '</ul>';
		}
	}
}



/*
 * Custom title function, derived from WordPress core
 */
if ( ! function_exists( 'inbound_get_the_title' ) ) {
	function inbound_get_the_title( $sep = '', $seplocation = '' ) {
		global $wpdb, $wp_locale;

		$m        = get_query_var( 'm' );
		$year     = get_query_var( 'year' );
		$monthnum = get_query_var( 'monthnum' );
		$day      = get_query_var( 'day' );
		$search   = get_query_var( 's' );
		$title    = '';

		$t_sep = '%WP_TITILE_SEP%'; // Temporary separator, for accurate flipping, if necessary

		// If there is a post
		if ( is_single() || ( is_home() && ! is_front_page() ) || ( is_page() && ! is_front_page() ) ) {
			$title = single_post_title( '', false );
		}

		// If there's a category or tag
		if ( is_category() || is_tag() ) {
			$title = single_term_title( '', false );
		}

		// If there's a taxonomy
		if ( is_tax() ) {
			$term  = get_queried_object();
			$tax   = get_taxonomy( $term->taxonomy );
			$title = single_term_title( $tax->labels->name . $t_sep, false );
		}

		// If there's an author
		if ( is_author() ) {
			$author = get_queried_object();
			$title  = $author->display_name;
		}

		// If there's a post type archive
		if ( is_post_type_archive() ) {
			$title = post_type_archive_title( '', false );
		}

		// If there's a month
		if ( is_archive() && ! empty( $m ) ) {
			$my_year  = substr( $m, 0, 4 );
			$my_month = $wp_locale->get_month( substr( $m, 4, 2 ) );
			$my_day   = intval( substr( $m, 6, 2 ) );
			$title    = $my_year . ( $my_month ? $t_sep . $my_month : '' ) . ( $my_day ? $t_sep . $my_day : '' );
		}

		// If there's a year
		if ( is_archive() && ! empty( $year ) ) {
			$title = $year;
			if ( ! empty( $monthnum ) ) {
				$title .= $t_sep . $wp_locale->get_month( $monthnum );
			}
			if ( ! empty( $day ) ) {
				$title .= $t_sep . zeroise( $day, 2 );
			}
		}

		// If it's a search
		if ( is_search() ) {
			/* translators: 1: separator, 2: search phrase */
			$title = sprintf( esc_html__( 'Search Results %1$s %2$s', 'inbound' ), $t_sep, strip_tags( $search ) );
		}

		// If it's a 404 page
		if ( is_404() ) {
			$title = esc_html__( 'Page not found', 'inbound' );
		}

		$prefix = '';
		if ( ! empty( $title ) ) {
			$prefix = " $sep ";
		}

		// Determines position of the separator and direction of the breadcrumb
		if ( 'right' == $seplocation ) { // sep on right, so reverse the order
			$title_array = explode( $t_sep, $title );
			$title_array = array_reverse( $title_array );
			$title       = implode( " $sep ", $title_array ) . $prefix;
		} else {
			$title_array = explode( $t_sep, $title );
			$title       = $prefix . implode( " $sep ", $title_array );
		}


		// Send it out
		return trim( $title );
	}
}


/*
 * Output function for theme options
 */
if ( ! function_exists( 'inbound_render_option' ) ) {
	function inbound_render_option(
		$key = '', $before = '', $after = '', $else = null, $filter = array(
		'do_shortcode',
		'stripslashes'
	)
	) {
		global $inbound_options;
		if ( isset( $inbound_options[ $key ] ) ) {
			// set content to output, or fall back to 'else' case
			if ( ! empty( $inbound_options[ $key ] ) ) {
				$content = $inbound_options[ $key ];
			} else {
				$content = $else;
			}

			$content = apply_filters( 'inbound_render_option', $content );
			$content = apply_filters( 'inbound_render_option_' . $key, $content );

			// if there is still something to output, do so
			if ( ! empty ( $content ) ) {
				if ( isset( $filter['stripslashes'] ) ) {
					$content = stripslashes( $content );
				}
				if ( isset( $filter['shortcodes'] ) ) {
					$content = do_shortcode( $content );
				}
				echo $before;
				echo $content;
				echo $after;
			}
		}
	}
}

/*
 * Output a list of slides retrieved from a gallery shortcode
 */
if ( ! function_exists( 'inbound_render_slides' ) ) {
	function inbound_render_slides( $content ) {
		$pattern = get_shortcode_regex();
		if ( preg_match_all( '/' . $pattern . '/s', $content, $matches )
		     && array_key_exists( 2, $matches )
		     && in_array( 'gallery', $matches[2] )
		):
			$keys = array_keys( $matches[2], 'gallery' );
			foreach ( $keys as $key ):
				$atts = shortcode_parse_atts( $matches[3][ $key ] );
				if ( array_key_exists( 'ids', $atts ) ):
					$images = explode( ',', $atts['ids'] );
					if ( is_array( $images ) && count( $images ) > 0 ) {
						foreach ( $images as $image ) {
							echo "<li>" . wp_get_attachment_image( $image, 'property-slides' ) . "</li>\n";
						}
					}
				endif;
			endforeach;
		endif;
	}
}

/*
 * Special formatting for currency values
 */
if ( ! function_exists( 'inbound_render_currency' ) ) {
	function inbound_render_currency( $value, $echo = true ) {
		if ( $echo == false ) {
			return inbound_currency_format( $value, 0 );
		} else {
			echo inbound_currency_format( $value, 0 );
		}
	}
}

if ( ! function_exists( 'inbound_paging_nav' ) ) :
	/**
	 * Display navigation to next/previous set of posts when applicable.
	 *
	 * @return void
	 */
	function inbound_paging_nav() {
		// Don't print empty markup if there's only one page.
		if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
			return;
		}
		?>
		<nav class="navigation paging-navigation" role="navigation">
			<h1 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'inbound' ); ?></h1>

			<div class="nav-links">

				<?php if ( get_next_posts_link() ) : ?>
					<div class="nav-previous"><?php next_posts_link( inbound_esc_html( __( '<span class="meta-nav">&larr;</span> Older posts', 'inbound' ) ) ); ?></div>
				<?php endif; ?>

				<?php if ( get_previous_posts_link() ) : ?>
					<div class="nav-next"><?php previous_posts_link( inbound_esc_html ( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'inbound' ) ) ); ?></div>
				<?php endif; ?>

			</div>
			<!-- .nav-links -->
		</nav><!-- .navigation -->
		<?php
	}
endif;

if ( ! function_exists( 'inbound_post_nav' ) ) :
	/**
	 * Display navigation to next/previous post when applicable.
	 *
	 * @return void
	 */
	function inbound_post_nav() {
		// Don't print empty markup if there's nowhere to navigate.
		$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
		$next     = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous ) {
			return;
		}
		?>
		<nav class="navigation post-navigation" role="navigation">
			<h1 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'inbound' ); ?></h1>

			<div class="nav-links">

				<?php previous_post_link( '%link', _x( '<span class="meta-nav">&larr;</span> %title', 'Previous post link', 'inbound' ) ); ?>
				<?php next_post_link( '%link', _x( '%title <span class="meta-nav">&rarr;</span>', 'Next post link', 'inbound' ) ); ?>

			</div>
			<!-- .nav-links -->
		</nav><!-- .navigation -->
		<?php
	}
endif;


/*
 * Custom Header Titles and URLs
 */
if ( ! function_exists( 'inbound_home_url' ) ) {
	function inbound_home_url() {
		$custom_link = inbound_option( 'header_link', false );
		if ( $custom_link ) {
			echo esc_url ( $custom_link );
		} else {
			echo esc_url( home_url( '/' ) );
		}
	}
}

if ( ! function_exists( 'inbound_get_site_title' ) ) {
	function inbound_get_site_title() {
		$custom_title = inbound_option( 'header_title', false );
		if ( $custom_title ) {
			return $custom_title;
		} else {
			return get_bloginfo( 'name' );
		}
	}
}

if ( ! function_exists( 'inbound_get_site_tagline' ) ) {
	function inbound_get_site_tagline() {
		$custom_tagline = inbound_option( 'header_tagline', false );
		if ( $custom_tagline ) {
			return $custom_tagline;
		} else {
			return get_bloginfo( 'description' );
		}
	}
}

if ( ! function_exists( 'inbound_get_blog_title' ) ) {
	function inbound_get_blog_title() {
		$blog_title    = inbound_option( 'blog_headline', false );
		$archive_title = inbound_option( 'blog_archive_headline', false );

		if ( is_archive() ) {
			$title = esc_html__( 'Archives', 'inbound' );

			if ( is_day() ) {
				$title = sprintf( inbound_esc_html( __( '<span>%s</span>', 'inbound' ) ), get_the_date() );
			} elseif ( is_month() ) {
				$title = sprintf( inbound_esc_html( __( '<span>%s</span>', 'inbound' ) ), get_the_date( _x( 'F Y', 'monthly archives date format', 'inbound' ) ) );
			} elseif ( is_year() ) {
				$title = sprintf( inbound_esc_html( __( '<span>%s</span>', 'inbound' ) ), get_the_date( _x( 'Y', 'yearly archives date format', 'inbound' ) ) );
			} elseif ( is_category() ) {
				$title = sprintf( inbound_esc_html( __( '<span>%s</span>', 'inbound' ) ), '<span>' . single_cat_title( '', false ) . '</span>' );
			}

			if ( empty( $custom_title ) ) {
				$prefix = esc_html__( 'Blog', 'inbound' );
			} else {
				$prefix = $archive_title;
			}

			return $prefix . ": " . $title;

		} else {
			if ( $blog_title ) {
				return $blog_title;
			} else {
				return esc_html__( 'Blog', 'inbound' );
			}
		}
	}
}

if ( ! function_exists( 'inbound_the_logo' ) ) {
	function inbound_the_logo() {
		$logo  = inbound_option( 'header_logo_image', false );
		if ( empty( $logo ) ) {
			// do nothing
		} else {
			if ( is_serialized( $logo ) ) {
				$logo     = unserialize( $logo );
				$logo_id  = $logo['id'];
				$logo_url = wp_get_attachment_url ( $logo_id );
				?>
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( inbound_get_site_title() ) ?>" title="<?php echo esc_attr( inbound_get_site_title() ) ?>" class="logo-primary" />
				<?php
			}
		}

		$logo = inbound_option( 'header_logo_image_secondary', false );
		if ( empty( $logo ) ) {
			// do nothing
		} else {
			if ( is_serialized( $logo ) ) {
				$logo     = unserialize( $logo );
				$logo_id  = $logo['id'];
				$logo_url = wp_get_attachment_url ( $logo_id );
				?>
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( inbound_get_site_title() ) ?>" title="<?php echo esc_attr( inbound_get_site_title() ) ?>" class="logo-secondary" />
				<?php
			}
		}

	}
}


/*
 * FontAwesome Star Rating
 */
if ( ! function_exists( 'inbound_get_stars' ) ) {
	function inbound_get_fa_stars( $rating ) {
		$stars = round( $rating * 2, 0, PHP_ROUND_HALF_UP );

		$out = '';

		$i          = 1;
		$star_count = 0;
		while ( $i <= $stars - 1 ) {
			$out .= '<i class="fa fa-star"></i>';
			$i += 2;
			$star_count ++;
		}

		if ( $stars & 1 ) {
			$out .= '<i class="fa fa-star-half-empty"></i>';
			$star_count ++;
		}

		if ( $star_count < 5 ) {
			$out .= '<i class="fa fa-star-o"></i>';
		}

		return $out;

	}
}


/*
 * Social Icons in Header
 */

if ( ! function_exists( 'inbound_has_social_icons' ) ) {
	function inbound_has_social_icons( $position = 'header' ) {
		$count        = 0;
		$social_icons = inbound_option( 'social_media_profiles' );
		if ( is_serialized( $social_icons ) ) {
			$services = unserialize( $social_icons );
			if ( is_array( $services ) ) {
				$count = 0;
				foreach ( $services as $service ) {
					if ( inbound_array_option( 'show_in_' . $position, $service, false ) ) {
						$count ++;
					}
				}
			}
		}
		if ( $count > 0 ) {
			return true;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'inbound_the_social_icons' ) ) {
	function inbound_the_social_icons( $position = 'header' ) {
		$social_icons = inbound_option( 'social_media_profiles' );
		if ( is_serialized( $social_icons ) ) {
			$services = unserialize( $social_icons );
			if ( is_array( $services ) ) {
				$count = 1;
				foreach ( $services as $service ) {
					if ( inbound_array_option( 'show_in_' . $position, $service, false ) ) {
						$link_title = inbound_array_option( 'title', $service, '' );
						$link       = inbound_array_option( 'link', $service, '#' );
						$icon       = inbound_array_option( 'icon', $service, 'fa-star' );
						echo '<li><a class="social-icon-header social-icon-' . $count . '" target="_blank" title="' . esc_attr( $link_title ) . '" href="' . esc_url( $link ) . '"><i class="fa ' . $icon . ' fa-1x"></i><span>' . esc_attr( $link_title ) . '</span></a></li>' . "\n";
						$count ++;
					}
				}
			}
		}
	}
}

/*
 * HEADER TEMPLATE TAGS
 */

/*
 * Custom Widget Script Calls
 */
if ( ! function_exists( 'inbound_print_custom_scripts' ) ) {
	function inbound_print_custom_scripts() {
		$widget_scripts = inbound_option( 'scripts' );

		if ( $widget_scripts && is_array( $widget_scripts ) && count( $widget_scripts ) > 0 ) {
			echo '<script type="text/javascript">' . "\n";
			foreach ( $widget_scripts as $type => $scripts ) {
				if ( is_array( $scripts ) && count( $scripts ) > 0 ) {
					if ( INBOUND_DEBUG ) {
						echo '/* Type: ' . ucwords( $type ) . ' */' . "\n";
					}
					foreach ( $scripts as $script ) {
						echo $script . "\n";
					}
				}
			}
			echo '</script>' . "\n";
		}

	}
}
add_action( 'wp_footer', 'inbound_print_custom_scripts' );


/*
 * Fav Icon
 */
if ( ! function_exists( 'inbound_favicon' ) ) {
	function inbound_favicon() {
		if ( inbound_option( 'favicon' ) ) {
			$favicon = inbound_option( 'favicon' );
			if ( is_serialized( $favicon ) ) {
				$favicon = unserialize( $favicon );
			}
			if ( is_array( $favicon ) && isset( $favicon['id'] ) ) {
				$favicon = $favicon['id'];
			} else {
				$favicon = false;
			}
			if ( $favicon ) {
				echo '<link rel="shortcut icon" href="' . esc_url( wp_get_attachment_url( $favicon ) ) . '" />';
			}
		}
	}
}

/*
 * Banner Logic
 */
if ( ! function_exists( 'inbound_banner' ) ) {
	function inbound_banner() {
		?>
		<!--Start of Banner-->
		<?php if ( ! inbound_custom_block( 'banner' ) ) : ?>
			<section id="banner" <?php inbound_banner_wrapper_classes(); ?><?php inbound_banner_data_attrs(); ?>>
				<?php if ( inbound_option( 'header_multipurpose' ) ) : ?>
					<?php if ( ! inbound_custom_block( 'multipurpose_bar' ) ) : ?>
						<!--Start of Multipurpose Header Bar-->
						<aside id="header-top">
							<div class="row<?php if ( inbound_option( 'header_multipurpose_full_width', false ) ) {
								echo ' row-full-width';
							} ?>">
								<div class="header-top-info">
									<?php if ( inbound_option( 'header_multipurpose_mode', 'content' ) == 'menu' ) : ?>
										<?php
										$this_menu = inbound_option( 'header_multipurpose_menu', '' );
										wp_nav_menu( array(
											'container'      => 'nav',
											'container_id'   => 'multipurpose-navigation',
											'menu_class'     => 'secondary',
											'theme_location' => 'inbound-multipurpose',
											'fallback_cb'    => false,
											'depth'          => 1,
											'menu'           => $this_menu
										) );
										?>
									<?php else : ?>
										<p><?php echo inbound_option( 'header_multipurpose_custom' ); ?></p>
									<?php endif; ?>
								</div>
								<?php if ( defined ('INBOUND_PROFILES') && inbound_has_social_icons( 'toolbar' ) ) : ?>
									<div class="header-top-social">
										<ul class="social-icons">
											<?php inbound_the_social_icons( 'toolbar' ); ?>
										</ul>
									</div>
								<?php endif; ?>
							</div>
						</aside>
					<?php endif; /* is custom multipurpose bar */ ?>
				<?php endif; ?>

				<!--Header Region-->
				<?php if ( ! inbound_page_option( 'sr_inbound_hide_header' ) ) : ?>
					<?php if ( ! inbound_custom_block( 'header' ) ) : ?>
						<?php if ( ! inbound_option( 'search_bar_hide' ) ) : ?>
							<!--Start of Search Form-->
							<div id="header-search-form">
								<?php inbound_search_header(); ?>
							</div>
							<!--End of Search Form-->
							<?php do_action('inbound_header_after_search_form'); ?>
						<?php endif; ?>
						<header id="header-region" class="menu-collapsed <?php echo 'menu-' . inbound_option( 'header_menu_alignment', 'left' ) ; ?>">
							<div id="header-region-inner">
								<div class="row<?php if ( inbound_option( 'header_full_width', false ) ) {
									echo ' row-full-width';
								} ?>">
									<!--Start of Header Logo-->
									<?php if ( ! inbound_custom_block( 'logo' ) ) : ?>
										<?php
										$logo_1 = inbound_option( 'header_logo_image', false );
										$logo_2 = inbound_option( 'header_logo_image_secondary', false );
										if ($logo_1 || $logo_2 ) $has_logo = true; else $has_logo = false;
										?>
										<?php if ( ! inbound_option( 'header_title_hide', false ) || $has_logo ) : ?>
											<div id="logo">
												<?php if ( ! inbound_option( 'header_title_hide', false ) ) : ?>
													<h1>
														<a href="<?php inbound_home_url(); ?>" rel="home"><?php inbound_the_logo(); ?><?php echo inbound_get_site_title(); ?></a>
													</h1>
												<?php else : ?>
													<?php if ( $has_logo ) : ?>
														<h1>
															<a href="<?php inbound_home_url(); ?>" rel="home"><?php inbound_the_logo(); ?></a>
														</h1>
													<?php endif ?>
												<?php endif; ?>
												<?php if ( ! inbound_option( 'header_tagline_hide', false ) ) : ?>
													<h2><?php echo inbound_get_site_tagline(); ?></h2><?php endif; ?>
											</div>
										<?php endif; ?>
									<?php endif; ?>
									<!--End of Header Logo-->

									<!--MainNavigation-->
									<div class="menu-button"><span><?php esc_html_e( 'Menu', 'inbound' ); ?></span></div>
									<?php if ( ! inbound_option( 'header_menu_hide', false ) ) : ?>
										<?php if ( ! inbound_custom_block( 'main_navigation' ) ) : ?>
											<?php
											$this_menu = inbound_option( 'header_menu', '' );
											wp_nav_menu( array(
												'container'      => 'nav',
												'container_id'   => 'main_navigation',
												'theme_location' => 'inbound-primary',
												'menu_class'     => 'site_main_menu sm',
												'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
												'fallback_cb'    => false,
												'menu'           => $this_menu
											) );
											?>
										<?php endif; ?>
									<?php endif; ?>
									<!--EndMainNavigation-->
									<?php
									$is_show_cart   = false;
									$is_show_search = false;
									$is_show_language_switcher = false;
									?>
									<!--Start of Lower Navigation Bar-->
									<?php if ( ! inbound_custom_block( 'lower_navigation' ) ) : ?>
										<aside id="tool-navigation-lower">
											<?php do_action('inbound_header_lower_navigation_start'); ?>
											<?php if ( function_exists( 'is_woocommerce' ) && inbound_option( 'enable_woocommerce_header_cart', true ) ) : $is_show_cart = true; ?>
												<?php global $woocommerce; ?>
												<!--Start of WooCommerce Cart-->
												<div id="header-cart" class="collapsed">
													<a href="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'inbound' ); ?>" <?php if ( $woocommerce->cart->cart_contents_count > 0 ) : ?>class="header-cart-notempty"<?php endif; ?> id="header-cart-trigger">
														<?php if ( $woocommerce->cart->cart_contents_count > 0 ) : ?>
															<span id="header-cart-total"><?php echo $woocommerce->cart->cart_contents_count; ?></span><?php endif; ?>
														<i class="fa fa-shopping-cart fa-1x"></i>
													</a>
													<?php the_widget( 'WC_Widget_Cart' ); ?>
												</div>
												<div id="header-cart-notification" class="collapsed">
													<span class="cart-notification"></span>
												</div>
												<!--End of WooCommerce Cart-->
												<?php do_action('inbound_header_after_cart'); ?>
											<?php endif; ?>
											<?php if ( ! inbound_option( 'language_switcher_hide' ) && class_exists('SitePress') ) : $is_show_language_switcher = true; ?>
												<?php inbound_custom_language_switcher(); ?>
												<?php do_action('inbound_header_after_language_switcher'); ?>
											<?php endif; ?>
											<?php if ( ! inbound_option( 'search_bar_hide' ) ) : $is_show_search = true; ?>
												<!--Start of Search Box-->
												<div id="header-search">
													<a href="#search-header" class="search-trigger"><i class="fa fa-search"></i></a>
												</div>
												<!--End of Search Box-->
												<?php do_action('inbound_header_after_search'); ?>
											<?php endif; ?>
											<?php if ( defined ('INBOUND_PROFILES') && inbound_has_social_icons( 'header' ) ) : ?>
												<ul class="social-icons<?php if ( $is_show_cart || $is_show_search || $is_show_language_switcher ) {
													echo " nav-separator";
												} ?>">
													<?php inbound_the_social_icons( 'header' ); ?>
												</ul>
											<?php endif; ?>
											<?php do_action('inbound_header_after_social_icons'); ?>
											<?php do_action('inbound_header_lower_navigation_end'); ?>
										</aside>
									<?php endif; ?>
									<!--End of Lower Navigation Bar-->
								</div>
							</div>
						</header>
					<?php endif; /* is custom header */ ?>
				<?php endif; /* if header is hidden */ ?>
				<!--End Header Region-->
				<?php if ( ! inbound_custom_block( 'banner_content' ) ) : ?>
					<?php inbound_the_banner(); ?>
				<?php endif; ?>
			</section>
		<?php endif; /* is custom banner */ ?>
		<!--End of Banner-->
		<?php
	}
}

function inbound_custom_language_switcher() {
	if (class_exists('SitePress')) {
		$languages = apply_filters( 'wpml_active_languages', NULL, array() );
		if( !empty( $languages ) && is_array( $languages ) && count ( $languages ) > 1 ) {
			echo '<div class="inbound-language-switcher"><ul>';
			foreach( $languages as $language ){

				$native_name = $language['native_name'];

				if ( ! empty ( $native_name ) ) {

					if( $language['active'] ) {
						echo '<li class="active">';
					} else {
						echo '<li>';
					}

					if( !$language['active'] ) echo '<a href="' . $language['url'] . '">';

					echo $native_name;

					if( !$language['active'] ) echo '</a>';

					echo '</li>';
				}

			}

			echo '</ul></div>';

		}
	}
}


/*
 * Search Form
 */

if ( ! function_exists( 'inbound_search_header' ) ) {
	function inbound_search_header() {
		?>
		<!--Start of Search Header-->
		<div id="search-header">
			<button class="close"><i class="fa fa-times"></i></button>
			<form method="get" class="search-form overlay overlay-scale" action="">
				<input type="search" id="search-overlay-input" class="search-field" placeholder="<?php esc_html_e('Search ...', 'inbound'); ?>" value="" name="s" autofocus>
				<input type="submit" class="search-submit" value="">
				<span class="search-icon"></span>
				<input type="hidden" name="lang" value="en">
			</form>
		</div>
		<!--End of Search Header-->
		<?php
	}
}



/*
 * FOOTER TEMPLATE TAGS
 */

if ( ! function_exists( 'inbound_sub_footer' ) ) {
	function inbound_sub_footer() {
		if ( ! inbound_custom_block( 'sub_footer' ) ) {
			if ( ( is_active_sidebar( 'inbound-footer-widgets' ) || is_active_sidebar( 'inbound-woocommerce-footer' ) ) && ! inbound_page_option( 'sr_inbound_hide_footer_widgets', false ) ) {
				?>
				<!--SubFooter-->
				<aside id="sub_footer" class="clearfix">
					<div class="row<?php if ( inbound_option( 'subfooter_full_width', false ) ) {
						echo ' row-full-width';
					} ?>">
						<?php
						if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
							if ( is_active_sidebar( 'inbound-woocommerce-footer' ) ) {
								inbound_get_sidebar( 'inbound-woocommerce-footer' );
							} else {
								inbound_get_sidebar( 'inbound-footer-widgets' );
							}
						} else {
							inbound_get_sidebar( 'inbound-footer-widgets' );
						}
						?>
					</div>
				</aside>
				<!--EndSubfooter-->
				<?php
			}
		}
	}
}

if ( ! function_exists( 'inbound_footer' ) ) {
	function inbound_footer() {
		?>
		<!--Start of Footer-->
		<?php if ( ! inbound_custom_block( 'footer' ) ) : ?>
			<?php
			$menu_content      = '';
			$copyright_content = inbound_option( 'footer_copyright' );
			if ( ! defined ('INBOUND_FEATURE_PACK')) {
				$copyright_content = inbound_footer_copyright();
			}

			if ( ! inbound_option( 'footer_menu_hide', '' ) ) {
				$this_menu    = inbound_option( 'footer_menu', '' );
				$menu_content = wp_nav_menu( array(
					'echo'           => false,
					'fallback_cb'    => '__return_false',
					'container'      => 'nav',
					'container_id'   => 'footer_navigation',
					'menu_class'     => 'secondary',
					'theme_location' => 'inbound-footer',
					'depth'          => 1,
					'menu'           => $this_menu
				) );
			}
			?>
			<?php if ( $menu_content || $copyright_content ) : ?>
				<footer id="page_footer" class="clearfix">
					<div class="row<?php if ( inbound_option( 'footer_full_width', false ) ) {
						echo ' row-full-width';
					} ?>">
						<?php if ( ! empty( $menu_content ) ) {
							echo $menu_content;
						} ?>
						<?php if ( $copyright_content ) : ?>
							<p>
								<?php echo inbound_esc_html( $copyright_content ); ?>
							</p>
						<?php endif; ?>
					</div>
				</footer>
			<?php endif; ?>
		<?php endif; ?>
		<!--End of Footer-->
		<?php
	}
}