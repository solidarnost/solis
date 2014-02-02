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

	$timestamp = strtotime($event_start_date." ".$event_start_time);
	$dayname = date('D', $timestamp); //to be translated
	$day= date('j', $timestamp);
	$month_number=date('n', $timestamp); //to be translated
	$month_array=array("januar", "februar", "marec", "april", "maj", "junij", "julij", "avgust", "september", "oktober","november", "december");
	$month=$month_array[$month_number-1];
	$time=date('G:i', $timestamp);
	$event_name=$event->event_name;
	$url=$event->guid;
	$location=$event->get_location();
	$location_name=$location->name;
	if($location_name!="")  $location="<div class='event-venue'><a href='$location_url'>$location_name</a></div>";
		else $location="";
	$location_url=$location->guid;
		$line=$line."<div class='event'><div class='event-when'>
		<div class='event-day'>$day</div><div class='event-month'>$month</div></div>
		<div class='event-info'>
		<div class='event-title'><a href='$url'>$event_name</a></div>
		$location
		<div class='event-time'>$time</div>
		<div class='event-gcal'><a href='".solis_event_google_link($post_id)."'>Dodaj v Googlov koledar</a></div></div>
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

function solis_event_google_link($post_id){
		$event=new EM_Event($post_id, 'post_id');

					//get dates
					if($event->event_all_day && $event->event_start_date == $event->event_end_date){
						$dateStart	= date('Ymd',$event->start - (60*60*get_option('gmt_offset')));
						$dateEnd	= date('Ymd',$event->start + 60*60*24 - (60*60*get_option('gmt_offset')));
					}else{
						$dateStart	= date('Ymd\THis\Z',$event->start - (60*60*get_option('gmt_offset')));
						$dateEnd = date('Ymd\THis\Z',$event->end - (60*60*get_option('gmt_offset')));
					}
					//build url
					$gcal_url = 'http://www.google.com/calendar/event?action=TEMPLATE&text=event_name&dates=start_date/end_date&details=post_content&location=location_name&trp=false&sprop=event_url&sprop=name:blog_name';
					$gcal_url = str_replace('event_name', urlencode($event->event_name), $gcal_url);
					$gcal_url = str_replace('start_date', urlencode($dateStart), $gcal_url);
					$gcal_url = str_replace('end_date', urlencode($dateEnd), $gcal_url);
					$gcal_url = str_replace('location_name', urlencode($event->output('#_LOCATION')), $gcal_url);
					$gcal_url = str_replace('blog_name', urlencode(get_bloginfo()), $gcal_url);
					$gcal_url = str_replace('event_url', urlencode($event->get_permalink()), $gcal_url);
					//calculate URL length so we know how much we can work with to make a description.
					if( !empty($event->post_excerpt) ){
						$gcal_url_description = $event->post_excerpt;
					}else{
						$matches = explode('<!--more', $event->post_content);
						$gcal_url_description = wp_kses_data($matches[0]);
					}
					$gcal_url_length = strlen($gcal_url) - 9;
					if( strlen($gcal_url_description) + $gcal_url_length > 1350 ){
						$gcal_url_description = substr($gcal_url_description, 0, 1380 - $gcal_url_length - 3 ).'...';
					}
					$gcal_url = str_replace('post_content', urlencode($gcal_url_description), $gcal_url);
					//get the final url
					$replace = $gcal_url;
				//	if( $result == '#_EVENTGCALLINK' ){
				//		$img_url = 'www.google.com/calendar/images/ext/gc_button2.gif';
				//		$img_url = is_ssl() ? 'https://'.$img_url:'http://'.$img_url;
				//		$replace = '<a href="'.esc_url($replace).'" target="_blank"><img src="'.esc_url($img_url).'" alt="0" border="0"></a>';
				//	}
			return $replace;

}
