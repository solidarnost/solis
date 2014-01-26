
<?php
	solis_new_post();
	solis_edited_post();	


?>

<?php
/**
 * The template for displaying Archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Twenty Fourteen
 * already has tag.php for Tag archives, category.php for Category archives,
 * and author.php for Author archives.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage solidarnost-demokracija
 * @since Solidarnost Demokracija 1.0
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

	<header class="page-header">
				<h1 class="page-title">
	<?php  $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); 
		echo "Predlogi iz področja ". $term->name; ?>
<?php
	$current_user = wp_get_current_user();
	$add_classes = "notification_mail_topic_off";
	echo "<span id='notification_mail_topic-".$term->term_id."' class='notification notification_mail_topic clickable $add_classes' onClick='toggle_option(".'"notification_mail_topic"'.",".$term->term_id.",$current_user->ID);'>e-opomnik</span>";
?>
				</h1>
			<p>
			<?php 
//* Notify WP when user visited this site last time.
   // Setting the last login of the user
					$current_user = wp_get_current_user();
    					update_usermeta ( $current_user->ID, 'last_check_'.$term->term_id, current_time('mysql') );
				if(current_user_can('publish_proposals')){
			?>
			<div id="ajaxform"><button id="newproposal" class="button center" onclick="newproposalclick(<?php echo $term->term_id; ?>,<?php echo get_current_user_id(); ?>)">+ Nov predlog za <?php echo $term->name; ?></button></div>
			<?php } ?>
			</p>
			</header><!-- .page-header -->

			<?php if ( have_posts() ) : ?>

			<?php

					// Start the Loop.
					while ( have_posts() ) : the_post();
						/*
						 * Include the post format-specific template for the content. If you want to
						 * use this in a child theme, then include a file called called content-___.php
						 * (where ___ is the post format) and that will be used instead.
						 */
						get_template_part( 'content', get_post_format() );

					endwhile;
					// Previous/next page navigation.
					twentyfourteen_paging_nav();

				else :
					// If no content, include the "No posts found" template.
				//	get_template_part( 'content', 'none' );

				endif;
			?>
		</div><!-- #content -->
	</section><!-- #primary -->

<?php
get_sidebar( 'content' );
get_sidebar();
get_footer();
