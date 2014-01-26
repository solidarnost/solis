<?php
    /* 
    Plugin Name: Solis -- democratic decision making platform
    Plugin URI: http://solidarnost.si 
    Description: It helps to make choices online using direct democracy. Released under GPLv3. See http://www.gnu.org/licenses/.
    Author: Samo Penič of Solidarnost.si team 
    Version: 1.0 
    Author URI: http://solidarnost.si
    Text Domain: solis
    */  


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



add_action('init', 'solis_textdomain');
function solis_textdomain() {
	load_plugin_textdomain('solis', false, dirname( plugin_basename( __FILE__ ) )."/languages/");
}


if ( ! function_exists('proposal') ) {
	/** 
	  * Registration of taxonomy that sets ``topics'' and ``tags'' of fields of debate for new post type. 
	  * It defines two distinct taxonomies proposal_topic and proposal_tags.
	  **/

function proposal() {
	register_taxonomy(
        'proposal_topic',
        'proposal',
        array(
            'labels' => array(
                'name' => __('Topic','solis'),
                'add_new_item' => __('Add new topic', 'solis'),
                'new_item_name' => __("New topic", 'solis')
            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true,
	    'capabilities' => array(
            	'manage_terms' => 'manage_proposal_topic',
            	'edit_terms' => 'edit_proposal_topic',
            	'delete_terms' => 'delete_proposal_topic',
            	'assign_terms' => 'assign_proposal_topic'
        	)
        )
    );
	register_taxonomy(
        'proposal_tags',
        'proposal',
        array(
            'labels' => array(
                'name' => __('Tags', 'solis'),
                'add_new_item' => __('Add new tag', 'solis'),
                'new_item_name' => __('New tag', 'solis')
            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => false,
	    'capabilities' => array(
            	'manage_terms' => 'manage_proposal_tags',
            	'edit_terms' => 'edit_proposal_tags',
            	'delete_terms' => 'delete_proposal_tags',
            	'assign_terms' => 'assign_proposal_tags'
        	)
        )
    );

	/** Defines custom post type for debate, called proposal. Here we define some properties for the post. **/
	$labels = array(
		'name'                => _x( 'Proposals', 'Post Type General Name', 'solis' ),
		'singular_name'       => _x( 'Proposal', 'Post Type Singular Name', 'solis' ),
		'menu_name'           => __( 'Proposals', 'solis' ),
		'parent_item_colon'   => __( 'Parent proposal', 'solis' ),
		'all_items'           => __( 'All proposals', 'solis' ),
		'view_item'           => __( 'View proposal', 'solis' ),
		'add_new_item'        => __( 'Add new proposal', 'solis' ),
		'add_new'             => __( 'Add new', 'solis' ),
		'edit_item'           => __( 'Edit proposal', 'solis' ),
		'update_item'         => __( 'Update proposal', 'solis' ),
		'search_items'        => __( 'Search proposals', 'solis' ),
		'not_found'           => __( 'Proposal not found', 'solis' ),
		'not_found_in_trash'  => __( 'Proposal not found in trash', 'solis' ),
	);
	$rewrite = array(
		'slug'                => 'proposal',
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => true,
	);
	$args = array(
		'label'               => __( 'proposal', 'solis' ),
		'description'         => __( 'Proposal for discussion', 'solis' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'author', 'revisions', 'comments' ),
		'taxonomies'          => array( 'proposal_topic' , 'proposal_tags'),
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
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'query_var'           => 'proposal',
		'rewrite'             => $rewrite,
		'capability_type'	=> 'proposal', /* adds default capability for posts with proposal or proposals at the end */
		'map_meta_cap'		=> true,
	);
	register_post_type( 'proposal', $args );
	flush_rewrite_rules(false);
} /* end function */

// Hook function into the 'init' action
add_action( 'init', 'proposal' );
} /* end if */


	/** Function adds two set of hidden information to distinguish whether 
	user is posting pro argument or contra argument and it inserts it into comment forms.

		It can operate in main loop or outside main loop, but we have to provide variable 
		`type' in $_POST . If type equals 1, the corresponding comment is pro, else it is contra.
	 **/
function democracy_custom_fields() {
	global $post;
	if($post->post_type=="proposal"){
		echo '<input type="hidden" id="proetcontra" name="proetcontra" value="contra" \>';
	}
	if(isset($_REQUEST['type'])){
		//we are called after main loop. No problem. Make fields
		$type=$_REQUEST['type'];
		if($type==1)
			echo '<input type="hidden" id="proetcontra" name="proetcontra" value="pro" \>';
		else
			echo '<input type="hidden" id="proetcontra" name="proetcontra" value="contra" \>';
			
	}
}
add_action( 'comment_form_logged_in_after', 'democracy_custom_fields' );
add_action( 'comment_form_after_fields', 'democracy_custom_fields' );

// Save the comment meta data along with comment
	/** Saves comments metadata for sent comments if post have variable `proetcontra' in $_POST.
	 * It connects to comment_post action of Wordpress default posting of comments */
function democracy_save_comment_meta_data( $comment_id ) {
	/// TODO: possible bug, since sometimes proetcontra is not set. Betterto put into brackets
	if ( ( isset( $_POST['proetcontra'] ) ) && ( $_POST['proetcontra'] != '') )
		$proetcontra = wp_filter_nohtml_kses($_POST['proetcontra']);
	add_comment_meta( $comment_id, 'proetcontra', $proetcontra );
}
add_action( 'comment_post', 'democracy_save_comment_meta_data' );


/* I am complicating, I know, but maybe it will be useful someday! */
/** Expanding the comments form is done in AJAX call. I know it is complicated variant and could be 
  *done much easier, but I am counting it could be easily improved in the future.
  **/
function solis_expand() {
	comment_form('',$_REQUEST['post_id']);
	die();
}

/// TODO: to be improved one day!
function solis_expand_login() {
   echo __("Only logged in users can post arguments to posts!");
   die();
}
add_action("wp_ajax_solis_expand", "solis_expand");
add_action("wp_ajax_nopriv_solis_expand", "solis_expand_login");

/** Need to register ajax scripts for expanding the comments form */
function solis_js_enqueuer() {
   wp_register_script( "solis_js", plugin_dir_url(__FILE__).'solis.js', array('jquery') );
   wp_localize_script( 'solis_js', 'solisAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
   wp_enqueue_script( 'jquery' );
   wp_enqueue_script( 'solis_js' );
}
add_action( 'init', 'solis_js_enqueuer' );



/** Defines ajax call function for expanding the custom frontend form for adding the new ``proposal'' */
function solis_newproposal_form() {
	if($_REQUEST['uid']!=get_current_user_id()){
		_e("You are not allowed to add new proposal on given topic!", 'solis');
		die();
	}
	newpost_draw_form($_REQUEST['field']);
	die();
}

/// TODO: to be improved one day!
function solis_nopriv_newproposal_form() {
	_e("You are not allowed to add new proposal on given topic!", 'solis');
	die();
}
add_action("wp_ajax_solis_newproposal_form", "solis_newproposal_form");
add_action("wp_ajax_nopriv_solis_newproposal_form", "solis_nopriv_newproposal_form");

/** Defines ajax call function for editing ``proposal'' inline. It exchanges post with window for editing contents */
/* TODO: Define, when user can change post. Maybe a good idea will be to reset all votes after the post will be added */
function solis_editproposal_form(){
	if(!current_user_can('edit_proposals',$_REQUEST['postID'])){
		_e("You are not allowed to edit this proposal!", 'solis');
		die();
	}
	$edit_array=editpost_draw_form($_REQUEST['postID']);
	echo json_encode($edit_array);
	die();
}
add_action("wp_ajax_solis_editproposal_form", "solis_editproposal_form");
add_action("wp_ajax_nopriv_solis_editproposal_form", "solis_editproposal_form");

/** a code that adds Edit button to the post */
function edit_proposal_link( $text, $span, $endspan){
	if(current_user_can('edit_post')){
		echo $span."<a href='#a' onClick='editproposal_click(".get_the_ID().");'>".$text."</a>".$endspan;
	}
}

/** Final touch. Addd a custom post type ``proposal'' an icon */
function solis_add_menu_icons_styles(){
?>
<style>
#adminmenu .menu-icon-proposal div.wp-menu-image:before {
  content: '\f122';
}
</style>
<?php
}
add_action( 'admin_head', 'solis_add_menu_icons_styles' );


function proposal_author_archive( &$query )
{
    if ( $query->is_author )
        $query->set( 'post_type', 'proposal' );
    remove_action( 'pre_get_posts', 'proposal_author_archive' ); // run once!
}
add_action( 'pre_get_posts', 'proposal_author_archive' );



add_action( 'add_meta_boxes', 'solis_remove_post_meta_boxes' );
function solis_remove_post_meta_boxes() {
	if (!current_user_can('administrator') || !current_user_can('edit_others_proposals')) {

	/* Publish meta box. */
	remove_meta_box( 'submitdiv', 'proposal', 'normal' );

	/* Comments meta box. */
	remove_meta_box( 'commentsdiv', 'proposal', 'normal' );

	/* Revisions meta box. */
	remove_meta_box( 'revisionsdiv', 'proposal', 'normal' );

	/* Author meta box. */
	remove_meta_box( 'authordiv', 'proposal', 'normal' );

	/* Slug meta box. */
	remove_meta_box( 'slugdiv', 'proposal', 'normal' );

	/* Post tags meta box. */
	remove_meta_box( 'tagsdiv-post_tag', 'proposal', 'side' ); 
	remove_meta_box( 'tagsdiv-proposal_tags', 'proposal', 'side' ); 

	/* Category meta box. */
	remove_meta_box( 'categorydiv', 'proposal', 'side' );

	/* Excerpt meta box. */
	remove_meta_box( 'postexcerpt', 'proposal', 'normal' );

	/* Post format meta box. */
	remove_meta_box( 'formatdiv', 'proposal', 'normal' );

	/* Trackbacks meta box. */
	remove_meta_box( 'trackbacksdiv', 'proposal', 'normal' );

	/* Custom fields meta box. */
	remove_meta_box( 'postcustom', 'proposal', 'normal' );
	/* Featured image meta box. */
	remove_meta_box( 'postimagediv', 'proposal', 'side' );

	/* Page attributes meta box. */
//	remove_meta_box( 'pageparentdiv', 'page', 'side' );
	}

	/* Only Administrator can block comments */
	if (!current_user_can('administrator')){
	/* Comment status meta box. */
	remove_meta_box( 'commentstatusdiv', 'proposal', 'normal' );
	}

}


/** This options make links in post and commentc automatically clickable */
add_filter( 'comment_text', 'make_clickable',      9 );
add_filter( 'the_content', 'make_clickable',      12 );


/** This function add metadata to proposal, that user visited it. Must be called inside a loop. */
add_action('single_post_title', 'solis_log_user_post');
function solis_log_user_post(){
// TODO: warning, this can result in massive data buildup. Check whether it is acceptable to do that.
/* it is better to store info to post than to user. If post is eventually deleted, space is freed.  */
	update_post_meta(get_the_ID(), 'last_check_by_'.wp_get_current_user()->ID, current_time('mysql') );
}


/** Normal users (voters, also authors and below) should not see the admin bar when logged in. This looks much nicer **/
function solis_remove_admin_bar() {
	if (!current_user_can('administrator') || !current_user_can('edit_others_posts') || !current_user_can('edit_others_proposals')) {
  		show_admin_bar(false);
	}
}
add_action('after_setup_theme', 'solis_remove_admin_bar');





/** Function is called when new proposal button is pressed on frontend. It draws form for posting ``proposal'' to the
  * blog. It is meant to be called via AJAX call, but can be called independetly from the theme. */
function newpost_draw_form($category){
	if(current_user_can('publish_proposals')){
?>

<!-- New Post Form -->
<div id="postbox">
<form id="new_post" name="new_post" method="post" action="">
<h2>Dodaj nov predlog:</h2>
<p><label for="title"><?php _e("Title of new proposal", 'solis'); ?></label><br />
<input type="text" id="title" value="" tabindex="100" size="50" name="proposaltitle" required /></p>

<p><label for="description"><?php _e("Detailed description of proposal", 'solis'); ?></label><br />
<textarea id="description" tabindex="101" name="proposaldescription" rows="10" columns="50" required></textarea></p>

<p align="right"><input type="submit" value="<?php _e('Publish') ?>" tabindex="102" id="submit" name="submit" /></p>

<input type="hidden" name="proposalpost_type" id="post_type" value="proposal" />
<input type="hidden" name="proposalcat" id="cat" value="<?php echo $category;?>" />
<input type="hidden" name="action" value="solis-newpost" />
<?php wp_nonce_field( 'new-post' ); ?>
</form>
</div>
<!--// New Post Form -->
<?php
	}
}

/** Function draws form when user requests edit of a ``proposal''. It should be called by AJAX call from the frontend */
function editpost_draw_form($postID){

$begin_form='<form id="edit_post" name="edit_post" method="post" action="">';
$title_html='<p><input type="text" id="title" tabindex="100" size="50" name="proposaltitle" required value="'.get_post_field('post_title', $postID).'"/></p>';

$post_html='<textarea id="description" tabindex="101" name="proposaldescription" rows="10" columns="50" required>'. get_post_field('post_content', $postID).'</textarea></p>

<p align="right"><input type="submit" value="'.__('Submit').'" tabindex="102" id="submit" name="submit" /></p>

<input type="hidden" name="proposalpost_type" id="post_type" value="proposal" />
<input type="hidden" name="postID" id="postID" value="'. $postID .'" />
<input type="hidden" name="action" value="solis-editpost" />'.wp_nonce_field( -1, 'solis-edit-post',true, false );
$end_form='</form>';
return array("title"=>$title_html, "post"=>$post_html, "begin_form"=>$begin_form, "end_form"=>$end_form);
}


/** ************************************* VOTING PART OF SOLIS ************************
 **/


/** Voting requires acces to custom table in the database to record votes. This is done through defining new element in the array $wpdb. **/
global $wpdb;

$wpdb->demovote=$wpdb->prefix."demovote";

/** Defines functions to get ip_address of user. This is required for recording the vote. */
if(!function_exists('get_ipaddress')) {
	function get_ipaddress() {
		if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$ip_address = $_SERVER["REMOTE_ADDR"];
		} else {
			$ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		if(strpos($ip_address, ',') !== false) {
			$ip_address = explode(',', $ip_address);
			$ip_address = $ip_address[0];
		}
		return esc_attr($ip_address);
	}
}




/** When plugin is activated, new table is created if necessary. Add capability to manage votes to admin. **/
function demovote_create_database_tables() {
	global $wpdb;
	$charset_collate = '';
	$sql = "CREATE TABLE $wpdb->demovote (".
			"vote_id INT(11) NOT NULL auto_increment,".
			"post_id INT(11) NOT NULL ,".
			"user_id int(10) NOT NULL default '0',".
			"positive INT(11) NOT NULL ,".
			"negative INT(11) NOT NULL ,".
			"timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,".
			"ip VARCHAR(40) NOT NULL ,".
			"host VARCHAR(200) NOT NULL,".
			"PRIMARY KEY (vote_id));";
if($wpdb->get_var("show tables like '$wpdb->demovote'") != $wpdb->demovote) 
	{
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	$role = get_role('administrator');
	if(!$role->has_cap('manage_ratings')) {
		$role->add_cap('manage_ratings');
	}
}
register_activation_hook( __FILE__, 'demovote_create_database_tables' );
register_activation_hook( __FILE__, 'solis_add_roles' );

/** We create two extra columns for proposals (vote for proposal or vote against it) */
function demovote_add_post_column_for($defaults) {
	$defaults['demovote_for']= __("Supports", 'solis');
	return $defaults;
}
add_action('manage_posts_custom_column', 'demovote_add_post_column_content_for');

function demovote_add_post_column_content_for($column_name) {
	global $post;
	if($column_name == 'demovote_for'){

	echo get_positive_votes($post->ID);
	}
}
add_filter('manage_posts_columns', 'demovote_add_post_column_for');

function demovote_add_post_column_against($defaults) {
	$defaults['demovote_against']= __("Don't support", 'solis');
	return $defaults;
}
add_action('manage_posts_custom_column', 'demovote_add_post_column_content_against');

function demovote_add_post_column_content_against($column_name) {
	global $post;
	if($column_name == 'demovote_against'){
		echo get_negative_votes($post->ID);
	}
}
add_filter('manage_posts_columns', 'demovote_add_post_column_against');

/** Function that is called in theme that shows voting form. It must be used inside the loop */
function solis_the_ratings(){
	global $post;
	global $user_ID;
	$retval="";
	if('proposal'==get_post_type($post)){
		$retval="<span id='vote-$post->ID' class='democracy-vote'>".demovote_post_voting_line($post->ID,$user_ID,false)."</span>";
	}
	echo $retval;
}

/** Function called from AJAX when vote is done. It highlights desired vote and thanks user for voting. */
/// TODO: style should go into theme css
function demovote_post_voting_line($post_id, $user_id, $voted=false, $voted_msg=''){
	$retval="<span class='vote_counter_positive'>".get_positive_votes($post_id)."</span>/<span class='vote_counter_negative'>".get_negative_votes($post_id)."</span>|";
	
	$user_count=user_voted($post_id, $user_id);
   	$nonce = wp_create_nonce("democracy_vote_nonce");
	if($user_count==0){

    	$retval=$retval.'<span class="user_vote_positive not_voted" onClick="demo_vote_click('.$post_id.",'".$nonce."'".',1)" style="cursor: pointer; border: 0px; color=#ff0000;">'.__('I support', 'solis').'</span>';
    	$retval=$retval.'<span class="user_vote_negative not_voted" onClick="demo_vote_click('.$post_id.",'".$nonce."'".',-1)" style="cursor: pointer; border: 0px; color=#00ff00;">'.__("I don't support", 'solis').'</span>';

	} else {

	//user has already voted
		if(user_voted_how($post_id,$user_id)>0){
			$retval=$retval.'<span class="user_vote_positive_voted">'.__('I support', 'solis').'</span>';
	    		$retval=$retval.'<span class="user_vote_negative">'.__("I don't support", 'solis').'</span>';
		} else {
			$retval=$retval.'<span class="user_vote_positive">'.__('I support', 'solis').'</span>';
	    		$retval=$retval.'<span class="user_vote_negative_voted">'.__("I don't support", 'solis').'</span>';
		}

	// verify if the user is not revoking the vote too fast. Allow only one change of mind in 5 minutes //
		if(user_post_vote_frequency($post_id,$user_id,300)<=1){
			$retval=$retval.'<span class="user_vote_wantchange not_voted" onClick="demo_vote_click('.$post_id.",'".$nonce."'".',0)" style="cursor: pointer; border: 0px;">'.__("Change vote",'solis').'</span> ';
		}
	}
	//user has just voted
	if($voted){
	$retval=$retval.$voted_msg;
	}
	return $retval;
}

/** Function that returns how many times user voted (changed votes) within period of time in seconds */
function user_post_vote_frequency($post_id, $user_id, $interval){
	global $wpdb;
	return $wpdb->get_var($wpdb->prepare("select COUNT(*) FROM $wpdb->demovote WHERE post_id=%d and user_id=%d and timestamp>now()-INTERVAL %d SECOND",$post_id, $user_id, $interval ));
}


/** Function that returns how many users voted for proposal */
function get_positive_votes($post_id){
	global $wpdb;
	$retval = $wpdb->get_var($wpdb->prepare("SELECT SUM(positive) FROM $wpdb->demovote WHERE post_id=%d",$post_id ));
	return $retval?$retval:0;
}

/** Function that returns how many users voted against proposal */
function get_negative_votes($post_id){
	global $wpdb;
	$retval= $wpdb->get_var($wpdb->prepare("SELECT SUM(negative) FROM $wpdb->demovote WHERE post_id=%d",$post_id ));
	return $retval?$retval:0;
}

/** Function checks whether user already voted */
function user_voted($post_id, $user_id){
/*global $wpdb;
return $wpdb->get_var($wpdb->prepare( "SELECT SUM(*) FROM $wpdb->demovote WHERE user_id=%d and post_id=%d" ,$user_id,$post_id));
*/
return user_voted_how($post_id, $user_id);
}

/** Function checks how user voted*/
function user_voted_how($post_id, $user_id){
	global $wpdb;
	$positive= $wpdb->get_var($wpdb->prepare( "SELECT SUM(positive) FROM $wpdb->demovote WHERE user_id=%d and post_id=%d" ,$user_id,$post_id));
	$negative= $wpdb->get_var($wpdb->prepare( "SELECT SUM(negative) FROM $wpdb->demovote WHERE user_id=%d and post_id=%d" ,$user_id,$post_id));
	return $positive-$negative;
}


add_action("wp_ajax_democracy_perform_vote", "democracy_perform_vote");
add_action("wp_ajax_nopriv_democracy_perform_vote", "democracy_perform_vote_login");
/** AJAX handler function that records the users vote. Requires ``post_id'' and ``value'' variable in $_POST. ``value'' is how user voted
  * and it is set as negative for actions of votes against the proposal or positive for voting for proposal. */
function democracy_perform_vote() {
	if ( !wp_verify_nonce( $_REQUEST['nonce'], "democracy_vote_nonce")) {
		// Someone wants to trick us!
		exit(__("Vote is invalid!", 'solis'));
   	}   

	global $wpdb;
	global $user_ID;
	$value=intval($_REQUEST['value']);
	$post_id=absint($_REQUEST['post_id']);
/*	$user_count = $wpdb->get_var($wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->demovote WHERE user_id=%d and post_id=%d" ,$user_ID,$post_id));
	if($user_count!=0){
		// user already voted!
*/
	$positive=0;
	$negative=0;
/// TODO: Verify if user is voting too fast. First filter is already in form, but can be overriden by malicious code. Fix it here!
	if(user_post_vote_frequency($post_id,$user_ID,300)>2){
				echo demovote_post_voting_line($post_id,$user_ID,true, __('You are voting too fast!','solis'));	
				die();
		}

	if(user_voted($post_id,$user_ID)!=0){
		if($value==0){
			$revoke=user_voted_how($post_id,$user_ID);
			if($revoke>1|| $revoke<-1) {
				_e("System error. User vote count too large. Database could be damaged. Contact administrator!",'solis');
				die();
			}
			if($revoke>0) $positive--;
			if($revoke<0) $negative--;
		} else {
			//If it had voted but not requested the revocation, display error. Shouldn't happen.
			exit(__("User already voted!",'solis'));
		}
	}
	if($value<0) {
		$negative++;
	}
	if($value>0) {
		$positive++;
	}
	/* Here we insert voting result into database */
	$wpdb->query($wpdb->prepare("INSERT into $wpdb->demovote (post_id, user_id, positive, negative, ip, host) values (%d,%d,%d,%d,%s,%s)",$post_id,$user_ID,$positive,$negative, get_ipaddress(),@gethostbyaddr( get_ipaddress() )));

   	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		if($value==0)
			echo demovote_post_voting_line($post_id,$user_ID,true, __('You can vote again!','solis'));
		else
			echo demovote_post_voting_line($post_id,$user_ID,true, __('Thank you for your vote!','solis'));
	}
   	die();
}

// TODO: to be improved one day!
function democracy_perform_vote_login() {
	_e("You need to login to vote!", 'solis');
	die();
}

/** ***************************** USER PASSWORD CHANGE FRONTEND ************************************ **/
/** ***************************** USER PASSWORD CHANGE FRONTEND ************************************ **/
/** ***************************** USER PASSWORD CHANGE FRONTEND ************************************ **/

// Inspired by: http://pippinsplugins.com/change-password-form-short-code/

function solis_change_password_form() {
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
 
	ob_start(); //php codeto buffer the page until it is done!
 
		// show any error messages after form submission
		solis_show_error_messages(); 
		?>
 
		<?php if(isset($_GET['password-reset']) && $_GET['password-reset'] == 'true') { ?>
			<div class="solis_message success">
				<span><?php _e('Password changed successfully', 'solis'); ?></span>
			</div>
		<?php } ?>
		<form id="solis_password_form" method="POST" action="<?php echo $current_url; ?>">
			<fieldset>
				<p>
					<label for="solis_user_old_pass"><?php _e('Old Password', 'solis'); ?></label>
					<input name="solis_user_old_pass" id="old_solis_user_pass" class="required" type="password"/>
				</p>
	<p>
					<label for="solis_user_pass"><?php _e('New Password', 'solis'); ?></label>
					<input name="solis_user_pass" id="solis_user_pass" class="required" type="password"/>
				</p>
				<p>
					<label for="solis_user_pass_confirm"><?php _e('Password Confirm', 'solis'); ?></label>
					<input name="solis_user_pass_confirm" id="solis_user_pass_confirm" class="required" type="password"/>
				</p>
				<p>
					<input type="hidden" name="solis_action" value="reset-password"/>
					<input type="hidden" name="solis_redirect" value="<?php echo $redirect; ?>"/>
					<input type="hidden" name="solis_password_nonce" value="<?php echo wp_create_nonce('solis-password-nonce'); ?>"/>
					<input id="solis_password_submit" type="submit" value="<?php _e('Change Password', 'solis'); ?>"/>
				</p>
			</fieldset>
		</form>
	<?php
	return ob_get_clean();	
}

// password reset form
function solis_reset_password_form() {
	if(is_user_logged_in()) {
		return solis_change_password_form();
	}
}
add_shortcode('password_form', 'solis_reset_password_form');


//receive the form!

function solis_reset_password() {
	// reset a users password
	if(isset($_POST['solis_action']) && $_POST['solis_action'] == 'reset-password') {
 
		global $user_ID;
 
		if(!is_user_logged_in())
			return;
 
		if(wp_verify_nonce($_POST['solis_password_nonce'], 'solis-password-nonce')) {

//			$user = get_user_by( 'login', $username )
			$user = get_userdata($user_ID);
			if($_POST['solis_user_old_pass']=='' || !wp_check_password( $_POST['solis_user_old_pass'] , $user->data->user_pass, $user->ID)){
				solis_errors()->add('old_password_incorrect', __('Old password incorrect', 'solis'));

			}
			if($_POST['solis_user_old_pass']==$_POST['solis_user_pass']){
				solis_errors()->add('old_new_password_equals', __('New password is the same as the old one.', 'solis'));

			}	
			if($_POST['solis_user_pass'] == '' || $_POST['solis_user_pass_confirm'] == '') {
				// password(s) field empty
				solis_errors()->add('password_empty', __('Please enter a password, and confirm it', 'solis'));
			}
			if($_POST['solis_user_pass'] != $_POST['solis_user_pass_confirm']) {
				// passwords do not match
				solis_errors()->add('password_mismatch', __('Passwords do not match', 'solis'));
			}
 
			// retrieve all error messages, if any
			$errors = solis_errors()->get_error_messages();
 
			if(empty($errors)) {
				// change the password here
				$user_data = array(
					'ID' => $user_ID,
					'user_pass' => $_POST['solis_user_pass']
				);
				wp_update_user($user_data);
				// send password change email here (if WP doesn't)
				wp_redirect(add_query_arg('password-reset', 'true', $_POST['solis_redirect']));
				exit;
			}
		}
	}	
}
add_action('init', 'solis_reset_password');

if(!function_exists('solis_show_error_messages')) {
	// displays error messages from form submissions
	function solis_show_error_messages() {
		if($codes = solis_errors()->get_error_codes()) {
			echo '<div class="solis_message error">';
			    // Loop error codes and display errors
			   foreach($codes as $code){
			        $message = solis_errors()->get_error_message($code);
			        echo '<span class="solis_error"><strong>' . __('Error', 'solis') . '</strong>: ' . $message . '</span><br/>';
			    }
			echo '</div>';
		}	
	}
}

if(!function_exists('solis_errors')) { 
	// used for tracking error messages
	function solis_errors(){
	    static $wp_error; // Will hold global variable safely
	    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
	}
}


/* disable user profile for users */

function solis_stop_access_profile() {
	if(!current_user_can('administrator')){
    if(IS_PROFILE_PAGE === true) {
        wp_die( 'Please contact your administrator to have your profile information changed.' );
    }
    remove_menu_page( 'profile.php' );
    remove_submenu_page( 'users.php', 'profile.php' );
	}
}
add_action( 'admin_init', 'solis_stop_access_profile' );



/** ****************************** LAST POSTS COUNTER *****************************
  */

/** A very very dirty hack. Should do it in some other way! But it is fast!*/
function solis_lastlogin_count( $atts ) {
	extract( shortcode_atts( array(
		'topic' => 'none',
		'topic_id'=>-1,
		'since'=> date ( 'Y-m-d H:i:s' ) 
	), $atts ) );
	if($topic_id>0){
		global $wpdb;
		// Get the last login time of the user
		
//select post_type, d.slug from wp_posts as a left join wp_term_relationships as b  on a.ID=b.object_id left join wp_term_taxonomy as c on b.term_taxonomy_id=c.term_taxonomy_id left join wp_terms as d on  c.term_id=d.term_id where slug='tema1';	
//$cnt=$wpdb->get_var($wpdb->prepare("select count(slug) from $wpdb->posts as a left join $wpdb->term_relationships as b  on a.ID=b.object_id left join $wpdb->term_taxonomy as c on b.term_taxonomy_id=c.term_taxonomy_id left join $wpdb->terms as d on  c.term_id=d.term_id where slug='$topic'"));
		$cnt=$wpdb->get_var($wpdb->prepare("select count(term_id) from $wpdb->posts as a left join $wpdb->term_relationships as b  on a.ID=b.object_id left join $wpdb->term_taxonomy as c on b.term_taxonomy_id=c.term_taxonomy_id where term_id=$topic_id and post_status='publish' and post_date>'%s'",$since));


//select COUNT(*) FROM $wpdb->demovote WHERE post_id=%d and user_id=%d and timestamp>now()-INTERVAL %d SECOND",$post_id, $user_id, $interval ));
		if($cnt==0){
			return "";
		}
		else{
			return "<div class='proposal_count'>".$cnt."</div>";
		}
	}
	else return "";


}
add_shortcode( 'solis_recent', 'solis_lastlogin_count' );


/** Counts last comments on topics */
function solis_lastread_comments_count( $atts ){
	extract (shortcode_atts( array(
		'proposal_id'=>-1,
		'since' => date ('Y-m-d H:i:s')), $atts) );
	if($proposal_id>0){
		global $wpdb;
		$cnt=$wpdb->get_var($wpdb->prepare("select count(ID) from $wpdb->posts as a right join $wpdb->comments as b on (a.ID=b.comment_post_ID) where ID=%d and b.comment_date>'%s' and b.comment_date>'2014-01-23 20:40:00' and comment_approved=1", $proposal_id, $since));
		if($cnt==0) return "";
		else return "<div class='comment_count'>".$cnt."</div>";
	} else return ""; 
}
add_shortcode( 'solis_recent_comments', 'solis_lastread_comments_count' );

/** ***************************** Secondary menu modifications  *************************************   */
function proposal_topic_menu_count( $menu_items ) {
    foreach ( $menu_items as $menu_item ) {
	if( 'proposal_topic'== $menu_item->object && 'taxonomy'==$menu_item->type){
		/* check when the user visited archive for the last time */
		$current_user = wp_get_current_user();
		$last_check = get_user_meta ( $current_user->ID, 'last_check_'.$menu_item->object_id, true );
	//	$last_login = get_user_meta ( $current_user->ID, 'last_login', true );
	//	echo $menu_item->object_id;
	//	echo $last_login;
                $menu_item->title = $menu_item->title. solis_lastlogin_count(array('topic_id'=>$menu_item->object_id, 'since'=>$last_check));
        }
    }

    return $menu_items;
}
add_filter( 'wp_nav_menu_objects', 'proposal_topic_menu_count' );


/* Remember when the user last logged in */
add_action ( 'wp_login', 'solis_user_last_login' );

function solis_user_last_login ( $login ) {
    $user = get_userdatabylogin ( $login );
    // Setting the last login of the user
    update_usermeta ( $user->ID, 'last_login', date ( 'Y-m-d H:i:s' ) );
}



/** ***************************** Primary menu modifications  *************************************   */
function solis_top_menu_items_add( $items,$args ) {
	if($args->theme_location == 'primary'){

$items .= "<li class='menu_item'><a href='".wp_logout_url()."'>".__('Sign out', 'solis')."</a></li>";
	}
    return $items;
}
add_filter( 'wp_nav_menu_items', 'solis_top_menu_items_add' , 10,2);


/** ******************************* TEMPORARY ******************************************************
    ******************************* Add basic CSV of users ***************************************** **/


add_action('admin_menu', 'solis_add_user_menu',0);

function solis_add_user_menu() {
	add_users_page(__('Basic CSV import', 'solis'), __('Basic CSV import', 'solis'), 'create_users', 'solis-add-user', 'solis_add_user_menu_page');
}

function solis_add_user_menu_page(){
if ( !current_user_can( 'create_users' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	?>
	<div class="wrap">
	<h2 id="add-new-user"><?php _e('Automatized addition of new users via CSV (use with caution)','solis'); ?></h2>
<?php
 if ( isset( $_REQUEST['m'] ) && $_REQUEST['m'] == '1' )
  {
	check_admin_referer( 'solis-csv-nonce' );
 	$vsebina=file($_FILES['CSVfile']['tmp_name']);
	foreach($vsebina as $linija){
		$ex=explode(",",trim(mb_convert_encoding($linija,"UTF-8","auto"),',;.\n"'));
		$name=$ex[0];
		$last_name=$ex[1];
		$email=$ex[2];
		$success=solis_import_user($email, $name, $last_name, $email);
	}
?>
   <div id='message' class='updated fade'><p><strong>Uspešno ste dodali novega uporabnika.</strong></p></div>
<?php 
	die();
  } ?>
	<br>
<?php
solis_CSV_basic_draw_form();
echo "</div>";
}


function solis_CSV_basic_draw_form(){
?>
<form name="solis_csv_form" id="solis_csv_form" enctype="multipart/form-data" method="post">
	<?php wp_nonce_field( 'solis-csv-nonce' ); ?>
<input type="hidden" name="action" value="solis_csv_submit" />
<input type="hidden" name="m" value="1" />
<p><?php _e('CSV file:','solis'); ?> <input type="file" name="CSVfile" id="CSVfile" /></p>
<button name="sform" id="sform" class="button button-primary"><?php _e('Import CSV'); ?></button>
</form>

<?php 
}


function solis_import_user($username, $name, $last_name, $email){
	$user_id = username_exists( $username );
	if ( !$user_id and email_exists($email) == false ) {
		$random_password = wp_generate_password( $length=8, $include_standard_special_chars=false );
		$userdata = array(
			'user_pass' => $random_password,
			'user_login' => $username,
			'first_name' => $name,
			'last_name' => $last_name,
			'user_email' => $email
		);
		$user_id = wp_insert_user( $userdata ) ;
		if(is_wp_error($user_id)) {
	//		echo "cannot add".  $user_name;
			return false;
		} else {
//		add_user_data($user_id, "nologin", 1);
			solis_notify_user( $userdata );
			return true;
		}
		
	} else {
//		echo "fail";
		return false;
	}
}

function solis_notify_user($userdata){

	$headers[]= "From: Solis <no-reply@solidarnost.si>";
$message='
Pozdravljeni!

Na spletni strani https://solis.solidarnost.si je zaživel Solis, portal za participativno demokracijo stranke Solidarnost. Trenutno je sistem v testnem obratovanju in je odprt le za člane Sveta stranke, kasneje pa ga bomo razširili na celotno članstvo.

Kot članici oz. članu Sveta Solidarnosti vam pošiljamo vaše
uporabniško ime: '.$userdata['user_email'].'in
geslo: '.$userdata['user_pass'].'

Kratka navodila za uporabo portala Solis:
	- vsebina portala nastaja v obliki predlogov,
	- predlogi so urejeni po področjih (stolpec na levi),
	- o predlogih se lahko razpravlja v obliki argumentov ZA in PROTI,
	- za predloge se lahko glasuje (ZA/PROTI); ko je doseženo določeno število glasov ZA (trenutno je meja nastavljena na 50% vseh članov foruma), bo predlog obravnavan na pristojnih organih stranke,
	- če želite dodati predlog, najprej izberite področje in znotraj njega lahko dodate nov predlog in njegov opis.

Prosimo, da tudi vse predloge za spremembe portala in napake objavite v obliki predloga znotraj Solisa, pod področjem "Sistem Solis". Hvala!

Odbor za participativno demokracijo stranke Solidarnost

';
$subject="Solis, portal za participativno demokracijo stranke Solidarnost";

//	$message="To je test, ".$userdata['ime']. " ". $userdata['user_pass'].". ";

	wp_mail ( $userdata['user_email'], $subject , $message, $headers );
}

/* end of solis.php */
?>
