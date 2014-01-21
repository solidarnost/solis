<?php
/**
 * The template for displaying Comments
 *
 * The area of the page that contains comments and the comment form.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area-proposal">
	<?php if ( have_comments() ) : ?>

	<h2 class="comments-title">
		<?php
		/*	printf( _n( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'twentyfourteen' ),
				number_format_i18n( get_comments_number() ), get_the_title() );
		*/
			echo ("Razprava na temo");
		?>
	</h2>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Comment navigation', 'twentyfourteen' ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'twentyfourteen' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'twentyfourteen' ) ); ?></div>
	</nav><!-- #comment-nav-above -->
	<?php endif; // Check for comment navigation. ?>

	<ol class="comment-list-pro">
		<?php
			$args=array("meta_key"=>"proetcontra", "meta_value"=>"pro", "post_id"=>$post->ID);
			$props=array('style'=> 'ol', 'short_ping' => true,'avatar_size'=> 34);
			wp_list_comments($props, get_comments($args)
			 );
		?>
	</ol><!-- .comment-list -->

	<ol class="comment-list-contra">
		<?php
			$args=array("meta_key"=>"proetcontra", "meta_value"=>"contra", "post_id"=>$post->ID);
			$props=array('style'=> 'ol', 'short_ping' => true,'avatar_size'=> 34);
			wp_list_comments($props, get_comments($args)
			 );
		?>
	</ol><!-- .comment-list -->
	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Comment navigation', 'twentyfourteen' ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'twentyfourteen' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'twentyfourteen' ) ); ?></div>
	</nav><!-- #comment-nav-below -->
	<?php endif; // Check for comment navigation. ?>

	<?php if ( ! comments_open() ) : ?>
	<p class="no-comments"><?php _e( 'Comments are closed.', 'twentyfourteen' ); ?></p>
	<?php endif; ?>

	<?php endif; // have_comments() ?>	

<span id="comment-form-ajax">
<h3></h3><br><br>
<center><span class="gumb_za" onclick='democracy_argue(<?php echo $post->ID  ?>,1);'>Imam argument za</span> <span class="gumb_proti" onclick='democracy_argue(<?php echo $post->ID  ?>,0);'>Imam argument proti</span></center></span>
	<?php 
//comment_form(); 
	?>

</div><!-- #comments -->
