
<?php  
solis_edited_post();
?>


<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<?php
				// Start the Loop.
				while ( have_posts() ) : the_post();

					/*
					 * Include the post format-specific template for the content. If you want to
					 * use this in a child theme, then include a file called called content-___.php
					 * (where ___ is the post format) and that will be used instead.
					 */
					get_template_part( 'content', 'event' );


					// If comments are open or we have at least one comment, load up the comment template.
					
					// Previous/next post navigation.
				//	demokracija_post_nav();
				
						if(function_exists('solis_attendance_getbuttons')){
			?>
							<div id="attendance" style="width:40%;margin: 0px auto;">	
							<center><h2>Potrdite prisotnost na dogodku</h2></center>
							<p>* Na spodnjem seznamu boste vidni šele ko boste ponovno naložili stran</p>	
							<br>
			<?php
				$current_user = wp_get_current_user();
				echo "<div>".solis_attendance_getbuttons(get_the_ID(), $current_user->ID)."</div>";
				echo "</div>";
				echo "<div id='atendees' style='width:40%;margin: 0px auto;'>";
				echo "<h3>Prisotnost so potrdili:</h3><p>";
				solis_event_attendees(get_the_ID(),true);

				echo "</p><h3>Opravičili so se:</h3><p>";
				solis_event_attendees(get_the_ID(),false);
				echo "</p></div>";
		}

				endwhile;
			?>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php
get_sidebar( 'content' );
get_sidebar();
get_footer();
