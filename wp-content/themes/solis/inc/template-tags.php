<?php
/**
 * Custom template tags for Solidarnost Demokracija
 *
 * @package WordPress
 * @subpackage Solidarnost_demokracija
 * @since Twenty Fourteen 1.0
 */
if ( ! function_exists( 'demokracija_posted_on' ) ) :
/**
 * Print HTML with meta information for the current post-date/time and author.
 *
 * @since Solidarnost Demokracija 1.0
 *
 * @return void
 */
function demokracija_posted_on() {
/*	if ( is_sticky() && is_home() && ! is_paged() ) {
		echo '<span class="featured-post">' . __( 'Sticky', 'twentyfourteen' ) . '</span>';
	}
*/
	// Set up and print post meta information.
	printf( '<span class="entry-date"><a href="%1$s" rel="bookmark"><time class="entry-date" datetime="%2$s">%3$s</time></a></span> <span class="byline"><span class="author vcard"><a class="url fn n" href="%4$s" rel="author">%5$s</a></span></span>',
		esc_url( get_permalink() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		get_the_author()
	);
//	echo get_the_author();
}
endif;




if ( ! function_exists( 'demokracija_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 *
 * @since Twenty Fourteen 1.0
 *
 * @return void
 */
function demokracija_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}

	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'twentyfourteen' ); ?></h1>
		<div class="nav-links">
			<?php
			if ( is_attachment() ) :
				previous_post_link( '%link',  __( '<span class="meta-nav">Published In</span>%title', 'twentyfourteen' ) );
			else :
				previous_post_link( '%link', '<span class="meta-nav">Prejšnji predlog</span>%title' );
				next_post_link( '%link', '<span class="meta-nav">Naslednji predlog</span>%title');
			endif;
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;


