<?php
require get_stylesheet_directory() . '/inc/template-tags.php';

// for editing inline!

function solis_edited_post(){

if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'])) {

	if(current_user_can('edit_proposals',$_REQUEST['postID']) && $_POST['action']=="solis-editpost"){


	global $post;	
 	// here we set the return page URL.
   	if (is_singular()) :
   		$current_url = get_permalink($post->ID);
   	else :
   		$pageURL = 'http';
   		if ($_SERVER["HTTPS"] == "on") $pageURL .= "s";
   		$pageURL .= "://";
   		if ($_SERVER["SERVER_PORT"] != "80") $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
   		else $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
   		$current_url = $pageURL;
   	endif;		
	$redirect = $current_url;

		
		if (isset ($_POST['proposaldescription'])) {
			$description = $_POST['proposaldescription'];
		} else {
			echo 'Prosimo, napišite kratek opis predloga';
			die();
		}
		if(!isset($_POST['postID'])){
			echo "Please no cheating!";
			die();
		}

		$post_id=$_POST['postID'];
		$my_post = array(
		      'ID'           => $post_id,
		      'post_content' => $description
  		);
		echo "was here";
		wp_update_post( $my_post );
		if(is_singular()) {
		wp_redirect( $redirect );
		} else {
		wp_redirect( $redirect."#post-".$post_id );

		}
		}
	
} // end IF

}


function solis_new_post(){

if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'])) {
	if(current_user_can('publish_proposals') && $_POST['action']=="solis-newpost"){
		// Do some minor form validation to make sure there is content
		if (isset ($_POST['proposaltitle'])) {
			$title =  $_POST['proposaltitle'];
		} else {
			echo 'Prosimo, izpolnite naslov predloga.';
			die();
		}
		if (isset ($_POST['proposaldescription'])) {
			$description = $_POST['proposaldescription'];
		} else {
			echo 'Prosimo, napišite kratek opis predloga';
			die();
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
}
}

?>
