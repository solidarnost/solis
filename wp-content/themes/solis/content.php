<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php twentyfourteen_post_thumbnail(); ?>
	<header class="entry-header">
		<?php if ( in_array( 'proposal_topic', get_object_taxonomies( get_post_type() ) ) ) : ?>
		<div class="entry-meta">
			<span class="cat-links"><?php 
				//echo get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'twentyfourteen' ) ); 
		echo get_proposal_topics_list(); ?>
		<?php 
		if(function_exists('solis_lastread_comments_count')) { 
			$current_user = wp_get_current_user();
			$last_check = get_post_meta ( get_the_ID(), 'last_check_by_'.$current_user->ID, true );
			$cnt=solis_lastread_comments_count(array('proposal_id'=>get_the_ID(), 'since'=>$last_check));
			if($cnt>0){
				echo "<span class='notification notification_new_comment notification_alert'>$cnt</span>";
			}
			else{
		//		echo "<span class='notification_new_comment notification'>0</span>";

			}
		}
		?>
		<?php
		if(solis_is_subscribed_post_email(get_the_ID(), $current_user->ID)==true)
			$add_classes="notification_set notification_mail_on";
		else
			$add_classes="notification_mail_off";
		echo "<span id='notification_mail-".get_the_ID()."' class='notification notification_mail clickable $add_classes' onClick='toggle_option(".'"notification_mail"'.",".get_the_ID().",$current_user->ID);'>e-opomnik</span>";
		?>

		</div>
		<?php
			endif;
			if ( is_single() ) :
				the_title( '<h1 class="entry-title">', '</h1>' );
			else :
				the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' );
			endif;
		?>
		<div class="entry-meta">
			<?php
				if ( 'proposal' == get_post_type() || 'post' == get_post_type() )
					demokracija_posted_on();

				if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) :
			?>
			<span class="comments-link"><?php 
				
				comments_popup_link( "Začnite debato", '1 argument', '% argumentov' ); ?></span>
			<?php
				endif;

//				edit_post_link( __( 'Edit', 'twentyfourteen' ), '<span class="edit-link">', '</span>' );
				edit_proposal_link( __( 'Edit', 'twentyfourteen' ), '<span class="edit-link">', '</span>' );
//				echo "<span class='edit-link'>edit_proposal_link( __<a href=''>".__('Edit','twentyfourteen')."</a></span>";
			?>
<?php if(function_exists('solis_the_ratings')) { solis_the_ratings(); } ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->
	<?php if ( is_search() ) : ?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry-content">
		<?php
			the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ) );
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentyfourteen' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			) );
		?>
	</div><!-- .entry-content -->
	<?php endif; ?>

	<?php the_tags( '<footer class="entry-meta"><span class="tag-links">', '', '</span></footer>' ); ?>
</article><!-- #post-## -->
