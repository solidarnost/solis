
<?php  

if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'])) {
	if(current_user_can('publish_proposals')){
		// Do some minor form validation to make sure there is content
		if (isset ($_POST['proposaltitle'])) {
			$title =  $_POST['proposaltitle'];
		} else {
			echo 'Prosimo, izpolnite naslov predloga.';
		}
		if (isset ($_POST['proposaldescription'])) {
			$description = $_POST['proposaldescription'];
		} else {
			echo 'Prosimo, napišite kratek opis predloga';
		}
		$tags = $_POST['proposalpost_tags'];

		// Add the content of the form to $post as an array
		$post = array(
			'post_title'	=>  wp_strip_all_tags($title),
			'post_content'	=>  wp_filter_nohtml_kses( $description),
			'tax_input'	=> array("proposal_topic"=> wp_strip_all_tags($_POST['proposalcat'])),  // Usable for custom taxonomies too
			'post_status'	=> 'publish',			// Choose: publish, preview, future, etc.
			'post_type'	=>  wp_strip_all_tags($_POST['proposalpost_type'])  // Use a custom post type if you want to
		);
		$post_id=wp_insert_post($post);  // Pass  the value of $post to WordPress the insert function
								// http://codex.wordpress.org/Function_Reference/wp_insert_post
		wp_redirect( get_permalink($post_id) );
	}
} // end IF


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
	<?php  $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); //echo "Predlogi iz področja ". $term->name; ?>
				</h1>
			<?php 
//* Notify WP when user visited this site last time.
   // Setting the last login of the user
					$current_user = wp_get_current_user();
    					update_usermeta ( $current_user->ID, 'last_check_'.$term->term_id, current_time('mysql') );
				if(current_user_can('publish_proposals')){
			?>
			<div id="ajaxform"><button id="newproposal" class="button center" onclick="newproposalclick(<?php echo $term->term_id; ?>,<?php echo get_current_user_id(); ?>)">+ Nov predlog za <?php echo $term->name; ?></button></div>
			<?php } ?>
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
