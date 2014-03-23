<?php
/*
        Copyright (C) 2014, Samo Penič, Solidarnost.si

        This program is free software: you can redistribute it and/or modify
        it under the terms of the GNU General Public License as published by
        the Free Software Foundation, either version 3 of the License, or
        (at your option) any later version.

        This program is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        GNU General Public License for more details.

        You should have received a copy of the GNU General Public License
        along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/



// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
include_once("user_roles.php");
include_once("email_notifications.php");



function solis_messaging() {
	/** Defines custom post type for sending mass emails, called message. Here we define some properties for the post. **/
	$labels = array(
		'name'                => _x( 'Messages', 'Message Type General Name', 'solis' ),
		'singular_name'       => _x( 'Message', 'Message Type Singular Name', 'solis' ),
		'menu_name'           => __( 'Messaging', 'solis' ),
		'parent_item_colon'   => __( 'Parent message', 'solis' ),
		'all_items'           => __( 'All messages', 'solis' ),
		'view_item'           => __( 'View message', 'solis' ),
		'add_new_item'        => __( 'Add new message', 'solis' ),
		'add_new'             => __( 'Add new', 'solis' ),
		'edit_item'           => __( 'Edit message', 'solis' ),
		'update_item'         => __( 'Update message', 'solis' ),
		'search_items'        => __( 'Search messages', 'solis' ),
		'not_found'           => __( 'Message not found', 'solis' ),
		'not_found_in_trash'  => __( 'Message not found in trash', 'solis' ),
	);
	$rewrite = array(
		'slug'                => 'message',
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => true,
	);
	$args = array(
		'label'               => __( 'message', 'solis' ),
		'description'         => __( 'Mass messages to users', 'solis' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'author', 'revisions', 'comments' ),
		'taxonomies'          => array( 'solwg', 'solfunc'),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => '',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'query_var'           => 'message',
		'rewrite'             => $rewrite,
//		'capability_type'	=> 'proposal', /* adds default capability for posts with proposal or proposals at the end */
//		'map_meta_cap'		=> true,
	);
	register_post_type( 'message', $args );
	flush_rewrite_rules(false);
} /* end function */

// Hook function into the 'init' action
add_action( 'init', 'solis_messaging' );
 /* end if */

/** Addd a custom post type ``message'' an icon */
function solis_add_messaging_icons_styles(){
?>
<style>
#adminmenu .menu-icon-message div.wp-menu-image:before {
  content: '\f466';
}
</style>
<?php
}
add_action( 'admin_head', 'solis_add_messaging_icons_styles' );




/* send email on publishing post! */


function mail_new_message($post_id) {
/*	error_log(json_encode($_POST));
        $post = get_post($post_id);
	error_log(json_encode($post)); */
    if( ( $_POST['post_status'] == 'publish' ) && ( $_POST['original_post_status'] != 'publish' ) ) {
        $post = get_post($post_id);
        $author = get_userdata($post->post_author);
        $author_email = $author->user_email;
        $email_subject = $post->post_title;

/*        ob_start(); ?>

        <html>
            <head>
                <title>New post at <?php bloginfo( 'name' ) ?></title>
            </head>
            <body>
                <p>
                    Hi <?php echo $author->user_firstname ?>,
                </p>
                <p>
                    Your post <a href="<?php echo get_permalink($post->ID) ?>"><?php the_title_attribute() ?></a> has been published.
                </p>
            </body>
        </html>

        <?php
*/
        $message = $post->post_content;

//        ob_end_clean();


/* get taxonomies */
 	$taxonomies = get_object_taxonomies('message');
    	foreach ($taxonomies as $taxonomy) {        
        	// get the terms related to post
        	$terms = get_the_terms( $post_id, $taxonomy );
        	if ( !empty( $terms ) ) {
            		foreach ( $terms as $term )
				error_log("Send all users in ".$term->slug);
//                		$out .= '<a href="' .get_term_link($term->slug, $taxonomy) .'">'.$term->name.'</a> ';
				//here call wp_mail for each user individually.
        	}
    	}

//        wp_mail( $author_email, $email_subject, $message );
	error_log($author_email." :: ". $email_subject ." :: ". $message);
    }
}

add_action( 'publish_message', 'mail_new_message' );

?>
