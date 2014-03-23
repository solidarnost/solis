<?php
    /* 
    Plugin Name: User management for Solis system
    Plugin URI: http://solidarnost.si 
    Description: Plugin allows easy management of users
    Author: Solidarnost.si team 
    Version: 1.0 
    Author URI: http://solidarnost.si
    */  

include_once( 'form.php');
include_once( 'posts.php');
include_once( 'user-taxonomies/user-taxonomies.php' );


function register_taxonomies(){
register_taxonomy('solfunc', array('user'), array(
        'public'                =>true,
	'hierarchical'	=>true,
  	'show_ui' => true,
        'labels'                =>array(
                'name'                                          =>'Vloga v stranki',
                'singular_name'                         =>'Vloga v stranki',
                'menu_name'                                     =>'Vloge v stranki',
                'search_items'                          =>'Išči vloge',
                'popular_items'                         =>'Najpogostejše vloge',
                'all_items'                                     =>'Vse vloge',
                'edit_item'                                     =>'Uredi vloge',
                'update_item'                           =>'Posodobi vloge',
                'add_new_item'                          =>'Dodaj novo vlogo',
                'new_item_name'                         =>'Ime nove vloge',
                'separate_items_with_commas'=>'Loči vloge z vejicami',
                'add_or_remove_items'           =>'Dodaj ali odstrani vloge',
                'choose_from_most_used'         =>'Izberi med najpostejšimi vlogami',
        ),
        'rewrite'               =>array(
                'with_front'                            =>true,
                'slug'                                          =>'author/solfunc',
        ),
        'capabilities'  => array(
                'manage_terms'                          =>'edit_users',
                'edit_terms'                            =>'edit_users',
                'delete_terms'                          =>'edit_users',
                'assign_terms'                          =>'read',
        ),
));
// Workgroups
register_taxonomy('solwg', array('user'), array(
        'public'                =>true,
	'hierarchical'	=>true,
  	'show_ui' => true,
        'labels'                =>array(
                'name'                                          =>'Delovne skupine',
                'singular_name'                         =>'Delovna skupina',
                'menu_name'                                     =>'Delovne skupine',
                'search_items'                          =>'Išči delovne skupine',
                'popular_items'                         =>'Najpogostejše delovne skupine',
                'all_items'                                     =>'Vse delovne skupine',
                'edit_item'                                     =>'Uredi delovne skupine',
                'update_item'                           =>'Posodobi delovne skupine',
                'add_new_item'                          =>'Dodaj novo delovno skupino',
                'new_item_name'                         =>'Ime nove delovne skupine',
                'separate_items_with_commas'=>'Loči delovne skupine z vejicami',
                'add_or_remove_items'           =>'Dodaj ali odstrani delovne skupine',
                'choose_from_most_used'         =>'Izberi med najpostejšimi delovnimi skupinami',
        ),
        'rewrite'               =>array(
                'with_front'                            =>true,
                'slug'                                          =>'author/solwg',
        ),
        'capabilities'  => array(
                'manage_terms'                          =>'edit_users',
                'edit_terms'                            =>'edit_users',
                'delete_terms'                          =>'edit_users',
                'assign_terms'                          =>'read',
        ),
));
// Competences
register_taxonomy('solcomp', 'user', array(
        'public'                =>true,
	'hierarchical'	=>true,
  	'show_ui' => true,
        'labels'                =>array(
                'name'                                          =>'Kompetence',
                'singular_name'                         =>'Kompetenca',
                'menu_name'                                     =>'Kompetence',
                'search_items'                          =>'Išči kompetence',
                'popular_items'                         =>'Najpogostejše kompetence',
                'all_items'                                     =>'Vse kompetence',
                'edit_item'                                     =>'Uredi kompetence',
                'update_item'                           =>'Posodobi kompetence',
                'add_new_item'                          =>'Dodaj nove kompetence',
                'new_item_name'                         =>'Ime nove kompetence',
                'separate_items_with_commas'=>'Loči kompetence z vejicami',
                'add_or_remove_items'           =>'Dodaj ali odstrani kompetence',
                'choose_from_most_used'         =>'Izberi med najpostejšimi kompetencami',
        ),
        'rewrite'               =>array(
                'with_front'                            =>true,
                'slug'                                          =>'author/solcomp',
        ),
        'capabilities'  => array(
                'manage_terms'                          =>'edit_users',
                'edit_terms'                            =>'edit_users',
                'delete_terms'                          =>'edit_users',
                'assign_terms'                          =>'read',
        ),
	'update_count_callback' => 'my_update_education_count' // Use a custom function to update the count.
));

// Education
register_taxonomy('soledu', 'user', array(
        'public'                =>true,
	'hierarchical'	=>true,
	'show_tagcloud' => true,
  	'show_ui' => true,
        'labels'                =>array(
                'name'                                          =>'Izobrazba',
                'singular_name'                         =>'Izobrazba',
                'menu_name'                                     =>'Izobrazba',
                'search_items'                          =>'Išči po izobrazbi',
                'popular_items'                         =>'Najpogostejša izobrazba',
                'all_items'                                     =>'Vse izobrazbe',
                'edit_item'                                     =>'Uredi izobrazbe',
                'update_item'                           =>'Posodobi izobrazbe',
                'add_new_item'                          =>'Dodaj nove izobrazbe',
                'new_item_name'                         =>'Ime nove izobrazbe',
                'separate_items_with_commas'=>'Loči izobrazbe z vejicami',
                'add_or_remove_items'           =>'Dodaj ali odstrani izobrazbe',
                'choose_from_most_used'         =>'Izberi med najpostejšimi izobrazbami',
        ),
        'rewrite'               =>array(
                'with_front'                            =>true,
                'slug'                                          =>'author/soledu',
        ),
        'capabilities'  => array(
                'manage_terms'                          =>'edit_users',
                'edit_terms'                            =>'edit_users',
                'delete_terms'                          =>'edit_users',
                'assign_terms'                          =>'read',
        ),
	'update_count_callback' => 'solis_update_education_count' // Use a custom function to update the count.
)

);



}

function solis_usermanager_init(){
if(current_user_can( 'create_users' )){
 wp_register_script( "gridforms", WP_PLUGIN_URL.'/solis-member-management/gridforms/gridforms.js', array('jquery') );
//   wp_localize_script( 'democracy_voting_script', 'demovoteAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
 wp_enqueue_script( 'gridforms' );
 wp_register_style( 'GridFormStylesheet', WP_PLUGIN_URL.'/solis-member-management/gridforms/gridforms.css' );
 wp_enqueue_style( 'GridFormStylesheet' );

  wp_register_script("solidarnost_ajaxResponse", WP_PLUGIN_URL.'/solis-member-management/solidarnost-ajax.js', array('jquery'));
	wp_localize_script('solidarnost_ajaxResponse', 'solidarnostAjax', array('ajaxurl'=>admin_url('admin-ajax.php')));
	wp_enqueue_script('solidarnost_ajaxResponse');
}
register_taxonomies();



}

// Hook into the 'init' action
add_action( 'init', 'solis_usermanager_init' );


add_action("wp_ajax_solis_get_user_data", "solis_get_user_data");

/** Function prints out json of user metadata for user with given ID */
function solis_get_user_data(){
	$user_id=$_REQUEST['uid'];
	if ( current_user_can( 'create_users' ) ){
		$retval= get_user_meta($user_id);
	
	//get all competences
		$cboxes=wp_get_object_terms($user_id, array('solcomp','solwg','solfunc'),array('fields'=>'slugs'));
		$retval['checkboxes']=$cboxes;
		$ddown=wp_get_object_terms($user_id, array('soledu'),array('fields'=>'ids'));
		$retval['education']=$ddown[0];
		$user_info=get_userdata($user_id);
		$retval['role']=$user_info->roles;
		$retval['username']=$user_info->user_login;
		$retval['email']=$user_info->user_email;
	echo (json_encode($retval));
	//error_log(json_encode($retval));
	//echo json_encode(array("A"=>1));

	}
	die();
}

add_action("wp_ajax_solis_get_user_id", "solis_get_user_id");

/** Function returns user ID for given fields. */
function solis_get_user_id(){
		if ( !current_user_can( 'create_users' ) ){
		echo json_encode(array("success"=>false, "error_message"=>"You don't have permission to search for user id!"));
		die();
		}
		if($_REQUEST['field']=='username'){
			$user = get_user_by( 'login', $_REQUEST['data'] );
			if($user===false) echo json_encode(array("success"=>false, "error_message"=>"User does not exist!"));	
			else echo json_encode(array("success"=>true, "uid"=>$user->ID));
		}
		else echo json_encode(array("success"=>false, "error_message"=>"Unknown field to search for user id!"));

		die();
}

add_action('admin_menu', 'solis_usermanager_add_user_menu',0);

function solis_usermanager_add_user_menu() {
	add_users_page('Dodajanje uporabnika', 'Kartoteka', 'create_users', 'solis-add-usermanager', 'solis_add_usermanager_menu_page');
}

function solis_add_usermanager_menu_page(){
if ( !current_user_can( 'create_users' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	?>
	<div class="wrap">
	<h2 id="add-new-user">Kartoteka uporabnikov. Pregled, urejanje in dodajanje uporabnikov.</h2>
<?php
 if ( isset( $_GET['m'] ) && $_GET['m'] == '1' )
  {
?>
   <div id='message' class='updated fade'><p><strong>Uspešno ste dodali novega uporabnika.</strong></p></div>
<?php } ?>
	<br>
<?php
solis_add_user_draw_form();
if(isset($_GET['action']) && $_GET['action']=='edit_user'){
	echo "<script>jQuery('document').ready(function() {
			//alert('ready');
			solis_fill_in_form(".$_GET['user'].");
			
	});</script>";
}
echo "</div>";
}


/* ajax settings for form validation */
add_action("wp_ajax_solidarnost_get_postname", "solidarnost_get_postname");
add_action("wp_ajax_nopriv_solidarnost_get_postname", "solidarnost_nopriv_get_postname");

function solidarnost_get_postname(){
	$postname=trim(solis_get_post_name($_REQUEST["value"]));
	echo json_encode(array('post_name'=>$postname,'municipality'=>solis_get_municipality(mb_strtoupper($postname))));
	die();
}
function solidarnost_nopriv_get_postname(){
	echo "not authorized";
	die();
}
add_action("wp_ajax_solidarnost_verify_email", "solidarnost_verify_email");
add_action("wp_ajax_nopriv_solidarnost_verify_email", "solidarnost_nopriv_verify_email");

function solidarnost_verify_email(){
	//TODO: Sanitize!
	$retval=0;
	if(!filter_var($_REQUEST['value'], FILTER_VALIDATE_EMAIL)){
		echo json_encode(array("retval"=>1));
		die();
	}
	$retval=email_exists($_REQUEST['value'])?1:0;
	echo json_encode(array("retval"=>$retval));
	die();
}
function solidarnost_nopriv_verify_email(){
	echo "not authorized";
	die();
}

add_action("wp_ajax_solidarnost_submit", "solidarnost_receive_form");
add_action("wp_ajax_nopriv_solidarnost_submit", "solidarnost_nopriv_receive_form");
function solidarnost_receive_form(){
	if ( !current_user_can( 'create_users' ) )
	{
		echo json_encode(array('success'=>false, 'error_message'=>'You are not allowed to be on this page.'));
		die();
   	}
   	// Check that nonce field
   	check_admin_referer( 'solidarnost-add-user-nonce' );
	$retval=[];
	$retval["success"]=true;
	$data=$_REQUEST;
	//verification if username is set. This is required for insertion o user!//
	if(!isset($data['username']) || $data['username']==""){
		$retval["username"]=array('type'=>error, 'message'=>'Obvezno vnesite uporabniško ime');
		$retval["success"]=false;
		$retval["error_message"]="Please enter username for new user!";
	}
	else{
		//TODO: Sanitize all datafields!

/*		foreach($_REQUEST as $key=>$field){
			error_log($key."=>".$field);
		} */
		//$retfunction=solis_create_user($_REQUEST['username'],$_REQUEST);
		error_log(json_encode($_REQUEST));
		$user_basicdata=array(
			'first_name'=>$_REQUEST['first_name'],
			'last_name'=>$_request['last_name'],
			'email'=>$_REQUEST['email'],
			'username'=>$_REQUEST['username'] //username is the same as email by default
		);
		$user_metadata=array(
			'member_id'=>$_REQUEST['member_id'],
			'birthdate'=>$_REQUEST['birthdate'],
			'gender'=>$_REQUEST['gender'],
			'address'=>$_REQUEST['address'],
			'postcode'=>$_REQUEST['postcode'],
			'postname'=>$_REQUEST['postname'],
			'municipality'=>$_REQUEST['municipality'],
			'voting_unit'=>$_REQUEST['voting_unit'],
			'gsm'=>$_REQUEST['gsm'],
			'phone'=>$_REQUEST['phone'],
			'occupation'=>$_REQUEST['occupation'],
			'employer'=>$_REQUEST['employer'],
			'notifications'=>$_REQUEST['notifications'],
			'signed'=>$_REQUEST['signed'],
			'enableduser'=>isset($_REQUEST['enableduser'])
		);
		$user_tags=array();
		$terms=get_terms('solcomp',array("hide_empty"=>false));	
		$taxtags="";
		foreach($terms as $term){
			if(isset($_REQUEST[$term->slug])){
				$taxtags=$taxtags.",".$term->slug;
			}
		}
		$user_tags['solcomp']=trim($taxtags,',');

		$terms=get_terms('solfunc',array("hide_empty"=>false));	
		$taxtags="";
		foreach($terms as $term){
			if(isset($_REQUEST[$term->slug])){
				$taxtags=$taxtags.",".$term->slug;
			}
		}
		$user_tags['solfunc']=trim($taxtags.',');

		$terms=get_terms('solwg',array("hide_empty"=>false));	
		$taxtags="";
		foreach($terms as $term){
			if(isset($_REQUEST[$term->slug])){
				$taxtags=$taxtags.",".$term->slug;
			}
		}
		$user_tags['solwg']=trim($taxtags,',');


		$user_tags['soledu']=get_term($_REQUEST['education'],'soledu')->slug;
		
		error_log(json_encode($user_tags));

		$retfunction=solis_import_user($user_basicdata,$user_metadata,$user_tags);

		/* override some user data */
			$user = get_user_by( 'login', $_REQUEST['username'] );
			$user_basicdata=array(
			'ID'=>$user->ID,
			'first_name'=>$_REQUEST['first_name'],
			'last_name'=>$_REQUEST['last_name'],
			'email'=>$_REQUEST['email'],
			'role'=>$_REQUEST['role']
		//	'username'=>$_REQUEST['username'] //username is the same as email by default
		);
		wp_update_user($user_basicdata);

		if(!$retfunction) {
			$retval["success"]=false;
			$retval["error_message"]="There was something wrong with insertion of user into database. Please check data and try again!";
		}
		
	}
	echo json_encode($retval);
	die();
}

/* We add extra check for those who want to enter the page, since it is important not to allow all the members to come in. Some of them are not active and are thus disabled */
add_filter('check_password', 'solidarnost_check_pass',9,4);
function solidarnost_check_pass($check, $password, $hash, $user_id=''){
	if($check) { // if wp check routine proved user is ok, then we check it ourselves
		return true;
	}
	return false;
}




function get_max_member_id(){
	$users=get_users();
	$max=0;
	error_log(json_encode($users));
	foreach($users as $user){
		$curr=get_user_meta($user->ID, 'member_id',false);
		if($curr['member_id']>$max){
			$max=$curr;
		}
	}
	error_log("$max");
	return $max;

}


/* NE DELAAA!
function solis_edit_user_redirect($location)
{
	error_log("was here!");
//    return admin_url('users.php?page=solis-add-usermanager');

}
add_filter('edit_user_profile_update', 'solis_edit_user_redirect');
*/



/* Various fixes of bugs */

/**
 * Function for updating the 'education' taxonomy count.  What this does is update the count of a specific term 
 * by the number of users that have been given the term.  We're not doing any checks for users specifically here. 
 * We're just updating the count with no specifics for simplicity.
 *
 * See the _update_post_term_count() function in WordPress for more info.
 *
 * @param array $terms List of Term taxonomy IDs
 * @param object $taxonomy Current taxonomy object of terms
 */
function solis_update_education_count( $terms, $taxonomy ) {
	global $wpdb;
	error_log("Was here");
	foreach ( (array) $terms as $term ) {

		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $term ) );

		do_action( 'edit_term_taxonomy', $term, $taxonomy );
		$wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), array( 'term_taxonomy_id' => $term ) );
		do_action( 'edited_term_taxonomy', $term, $taxonomy );
	}
}



/** Function adds user edit link to the user manager. */
function solis_user_action_links($actions, $user_object) {
	//unset($actions['edit']);
	unset($actions['bookings']);
	if( current_user_can( 'edit_users' ) ){
	$actions['edit'] = "<a class='solis_edit_badges' href='" . admin_url( "users.php?page=solis-add-usermanager&action=edit_user&amp;user=$user_object->ID") . "'>" . __( 'View/Edit user data', 'solis' ) . "</a>";
	}
	return $actions;
}
add_filter('user_row_actions', 'solis_user_action_links', 10, 2);
?>
