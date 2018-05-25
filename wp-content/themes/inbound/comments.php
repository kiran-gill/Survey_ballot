<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to inbound_comment() which is
 * located in the inc/template-tags.php file.
 *
 * @package inbound
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>

    <!--Start of Comments-->
    <section id="comments" class="blog-section">
        <?php if ( have_comments() ) : ?>
            <h3>
                <?php
                    echo '<span>' . number_format_i18n( get_comments_number() ) . '</span> ';
                    if (get_comments_number() > 1) esc_html_e('Comments', 'inbound'); else esc_html_e('Comment', 'inbound');
                ?>
            </h3>

            <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
                <nav id="comment-nav-above" class="navigation-comment" role="navigation">
                    <h1 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'inbound' ); ?></h1>
                    <div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'inbound' ) ); ?></div>
                    <div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'inbound' ) ); ?></div>
                </nav><!-- #comment-nav-before -->
            <?php endif; // check for comment navigation ?>

            <ul class="comment-list">
                <?php
                /* Loop through and list the comments. Tell wp_list_comments()
                 * to use inbound_comment() to format the comments.
                 * If you want to overload this in a child theme then you can
                 * define inbound_comment() and that will be used instead.
                 * See inbound_comment() in inc/template-tags.php for more.
                 */
                wp_list_comments( array( 'callback' => 'inbound_comment' ) );
                ?>
            </ul><!-- .comment-list -->

            <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
                <nav id="comment-nav-below" class="navigation-comment" role="navigation">
                    <h1 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'inbound' ); ?></h1>
                    <div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'inbound' ) ); ?></div>
                    <div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'inbound' ) ); ?></div>
                </nav><!-- #comment-nav-below -->
            <?php endif; // check for comment navigation ?>

        <?php endif; // have_comments() ?>

        <?php
        // If comments are closed and there are comments, let's leave a little note, shall we?
        if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
            ?>
            <p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'inbound' ); ?></p>
        <?php endif; ?>

        <?php inbound_comment_form(); ?>
    </section>
    <!--End of Comments-->



