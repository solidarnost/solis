<?php

//This file is a part of Solis system. If user activated plugin Events manager, then this will override some Events manager settings and do some formatting


function solis_events_list_shortcode($attrs){
	$current_user = wp_get_current_user();
	$events=EM_Events::get($attrs);
//	print_r($events);
	$line="<div class='event-list'>";
	foreach($events as $event){

	if($event->event_status!=1) continue;
	$post_id=$event->post_id;
	$event_name=$event->event_name;
	$event_start_time=$event->event_start_time;
	$event_start_date=$event->event_start_date;
	$event_name=$event->event_name;
	$url=$event->guid;
	$location=$event->get_location();
	$location_name=$location->name;
	$location_url=$location->guid;
		$line=$line."<div class='event'><div class='event-when'>
		<div class='event-date'>$event_start_date</div>
		<div class='event-time'>$event_start_time</div></div>
		<div class='event-info'>
		<div class='event-title'><a href='$url'>$event_name</a></div>
		<div class='event-venue'><a href='$location_url'>$location_name</a></div></div>
		<div class='event-confirm'>".solis_attendance_getbuttons($post_id, $current_user->ID)."</div>
		</div>";

	}
	return "$line</div>";

	
}

add_shortcode('solis_events_list', 'solis_events_list_shortcode');

function solis_attendance_getbuttons($post_id, $user_id){
	$attendance=solis_is_attending_event($post_id, $user_id);
	if($attendance==1){
		$yesdivclass='event-attending-yes_on'; 
		$nodivclass='';
	}
	if($attendance==0) {
		$nodivclass='event-attending-no_on'; 
		$yesdivclass='';
	}
	if(solis_is_attendance_toggle_allowed($post_id)){
		$yesdivclass=$yesdivclass." clickable";
		$nodivclass=$nodivclass." clickable";
		$yesonclick="toggle_option(".'"event-attending"'.",$post_id,$user_id, false,1)";
		$noonclick="toggle_option(".'"event-attending"'.",$post_id,$user_id, false,0)";	
	}
	else{
		$yesonclick="";
		$noonclick="";
	}
	return "
	<div id='event-attending-$post_id-yes' class='notification event-attending-yes $yesdivclass' onClick='$yesonclick'>pridem</div>
	<div id='event-attending-$post_id-no' class='notification event-attending-no $nodivclass' onClick='$noonclick'>ne pridem</div>";

}

function solis_is_attendance_toggle_allowed($post_id){

	$event=new EM_Event($post_id, 'post_id');
	$timeEvent=strtotime($event->event_start_date." ".$event->event_start_time);
	$now=strtotime(current_time("mysql"));
	if($now<$timeEvent) return true;
	else return false;
}


function event_attending_toggle($post_id, $uid, $setval){
	if(!solis_is_attendance_toggle_allowed($post_id)) return $setval;
	$subscribed=solis_is_attending_event($post_id,$uid);
	if(($subscribed==1 && $setval==1) || ($subscribed==0 && $setval==0)) return $subscribed;

	if($setval==1){
		solis_attend_event($post_id, $uid);
	} elseif($setval==0){
		solis_dont_attend_event($post_id, $uid);
	}
	return $setval;
}




/* find booking information subscriptions */
function solis_is_attending_event($post_id, $user_id){

	$retval=get_post_meta($post_id,'_solis_user_not_attending_event');
	if(in_array($user_id, $retval)){
		return 0;
	}

	$retval=get_post_meta($post_id,'_solis_user_attending_event');

	if(in_array($user_id, $retval)){
		return 1;
	}
	//we dont know :(
	return -1;
}

function solis_attend_event($post_id, $user_id){
	delete_post_meta($post_id, '_solis_user_not_attending_event', $user_id);
	add_post_meta($post_id, '_solis_user_attending_event', $user_id);
}


function solis_dont_attend_event($post_id, $user_id){
	delete_post_meta($post_id, '_solis_user_attending_event', $user_id);
	add_post_meta($post_id, '_solis_user_not_attending_event', $user_id);
}


// TODO: Attendance list!

function solis_event_attendees($post_id, $type=true){
		if($type) $retval=get_post_meta($post_id,'_solis_user_attending_event');
		else $retval=get_post_meta($post_id,'_solis_user_not_attending_event');
		echo "<ul>";
		foreach($retval as $user_id){
		 $udata=get_userdata( $user_id );
		echo "<li>".$udata->display_name."</li>";
		}
		echo "</ul>";

}
