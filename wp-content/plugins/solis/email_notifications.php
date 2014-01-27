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


/** Defines ajax call function for turning on or off custom options */
function solis_ajax_toggle_options() {
	if($_REQUEST['uid']!=get_current_user_id()){
		$err_desc=__("You are not authorised to change this setting!", 'solis');
		echo json_encode(array("success"=>false, "error"=>$err_desc));
		die();
	}
	$option_name=$_REQUEST['optionName'];
	if("notification_mail"==$option_name){
		if(solis_is_subscribed_post_email($_REQUEST['postID'], $_REQUEST['uid'])){
			solis_unsubscribe_post_email($_REQUEST['postID'],$_REQUEST['uid']);
			$state=0;
			
		} else {
			solis_subscribe_post_email($_REQUEST['postID']	,$_REQUEST['uid']);
			$state=1;
		}
	} elseif("notification_mail_topic"==$option_name){
			//postID is actually topic ID!
		if(solis_is_subscribed_topic_email($_REQUEST['postID'], $_REQUEST['uid'])){
			solis_unsubscribe_topic_email($_REQUEST['postID'],$_REQUEST['uid']);
			$state=0;
			
		} else {
			solis_subscribe_topic_email($_REQUEST['postID']	,$_REQUEST['uid']);
			$state=1;
		}
	} else {
		echo json_encode(array("success"=>false, "error"=>__("Option not recognised!", "solis")));
		die();
		
	}
	echo json_encode(array("success"=>true, "state"=>$state));
	die();
}

function solis_nopriv_ajax_toggle_options() {
	$err_desc=__("You are not allowed to toggle given setting!", 'solis');
	echo json_encode(array("success"=>false, "error"=>$err_desc));
	die();
}

add_action("wp_ajax_solis_toggle_option", "solis_ajax_toggle_options");
add_action("wp_ajax_nopriv_solis_toggle_option", "solis_nopriv_ajax_toggle_options");


/* proposal subscriptions */
function solis_is_subscribed_post_email($post_id, $user_id){
	$retval=get_post_meta($post_id,'_solis_subscribed_by_user');
	return	in_array($user_id, $retval);
}

function solis_unsubscribe_post_email($post_id, $user_id){
	delete_post_meta($post_id, '_solis_subscribed_by_user', $user_id);
}


function solis_subscribe_post_email($post_id, $user_id){
	add_post_meta($post_id, '_solis_subscribed_by_user', $user_id);
}


/* topic subscriptions */
function solis_is_subscribed_topic_email($topic_id, $user_id){
	$retval=get_user_meta($user_id, '_solis_subscribe_topic');
	return	in_array($topic_id, $retval);
}

function solis_unsubscribe_topic_email($topic_id, $user_id){
	delete_user_meta($user_id, '_solis_subscribe_topic', $topic_id);
}


function solis_subscribe_topic_email($topic_id, $user_id){
	add_user_meta($user_id, '_solis_subscribe_topic', $topic_id);
}



/** Hooks for sending notification emails whenever new argument is added to the proposal */
add_action('comment_post','solis_email_on_new_comment_post',99,2);

function solis_email_on_new_comment_post($comment_ID, $approval_status){
	$comment_values=get_comment($comment_ID);
	$recipients=get_post_meta($comment_values->comment_post_ID, '_solis_subscribed_by_user');
	$post=get_post($comment_values->comment_post_ID);
	$subject=sprintf(__("[Solis] User %s commented on post you are watching!",'solis'),$comment_values->comment_author);
	$message=sprintf(__("You received this message, because you are subscribed to news about the proposal %s!

User %s commented on proposal with comment:
%s

You may check the proposal by clicking on this link %s. If you wish to unfollow this proposal, please click the link and unsubscribe to proposal.


Your Solis team.", 'solis'),$post->post_title, $comment_values->comment_author, $comment_values->comment_content, esc_url(get_post_permalink($comment_values->comment_post_ID)));
	if($recipients){
		foreach($recipients as $recipient){
			$udata=get_userdata($recipient);
			//TODO emails should not be send immediately to all the recepients but scheduled by cron!
			solis_send_notification($udata->user_email, $subject, $message);
		}
	}
}


/** Function that sendd notifications through email to subscribers */
function solis_send_notification($email, $subject, $message){
		$headers[]= "From: Solis <no-reply@solidarnost.si>";
		wp_mail ( $email, $subject , $message, $headers );

}

/** Hooks for sending notification emails whenever new proposal is added to topic */
add_action( 'publish_post', 'solis_email_on_new_proposal_topic');

function solis_email_on_new_proposal_topic($post_id){
//if( ( $_POST['post_status'] == 'publish' ) && ( $_POST['original_post_status'] != 'publish' ) ) {
	$post = get_post($post_id);
        $author = get_userdata($post->post_author);

	$term_list = wp_get_post_terms($post_id, 'proposal_topic', array("fields" => "all"));
	foreach($term_list as $term){
		$termID=$term->term_id;
		$users=$get_users("meta=proposal_topic&meta_value=$termID");
		
		$subject=sprintf(__("[Solis] User %s posted proposal on topic you are watching!",'solis'),$author);
		$message=sprintf(__("You received this message, because you are subscribed to news about the topic %s!

User %s posted proposal in chosen topic with comment:
%s

You may check the proposal by clicking on this link %s. If you wish to unfollow the topic, please login to Solis system and unsubscribe.


Your Solis team.", 'solis'),$post->post_title, $author, $post->post_content, esc_url(get_post_permalink($post_id)));
	
		foreach($users as $user){
		}
	}
//} //END IF
}


?>
