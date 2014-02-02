   function democracy_argue(post_id,type) {
      jQuery.ajax({
         type : "get",
         dataType : "html",
         url : solisAjax.ajaxurl,
         data : {action: "solis_expand", type: type, post_id:post_id},
	cache: false,
         success: function(response) {
               jQuery("#comment-form-ajax").html(response);
         }
      });   

   }


 function newproposalclick(field,uid){
 jQuery.ajax({
         type : "get",
         dataType : "html",
         url : solisAjax.ajaxurl,
         data : {action: "solis_newproposal_form", field: field, uid:uid},
	cache: false,
         success: function(response) {
               jQuery("#ajaxform").html(response);
		jQuery("#title").focus();
         }
      });   

}


   function demo_vote_click(post_id,nonce,value) {
      jQuery.ajax({
         type : "get",
         dataType : "html",
         url : solisAjax.ajaxurl,
         data : {action: "democracy_perform_vote", post_id : post_id, nonce: nonce, value:value},
	cache: false,
         success: function(response) {
               jQuery("#vote-"+post_id).html(response)
         }
      });   

   }

  function editproposal_click(post_id) {
jQuery.ajax({
         type : "get",
         dataType : "json",
         url : solisAjax.ajaxurl,
         data : {action: "solis_editproposal_form", postID: post_id},
	cache: false,
         success: function(response) {
               jQuery("#post-"+post_id).html(response.begin_form+jQuery("#post-"+post_id).html()+response.end_form);	
               jQuery("#post-"+post_id).find(".entry-title").html(response.title);
               jQuery("#post-"+post_id).find(".entry-content").html(response.post);
		jQuery("#post-"+post_id).find(".entry-content").focus();
         }
      });   



  }


function toggle_option(option_name, post_id, user_id, single_toggle, set_value){
	if(typeof(single_toggle)==='undefined') single_toggle=true;
	if(typeof(single_toggle)==='undefined') set_value=0;
jQuery.ajax({
         type : "get",
         dataType : "json",
         url : solisAjax.ajaxurl,
         data : {action: "solis_toggle_option", optionName:option_name, postID: post_id, uid:user_id, setVal:set_value},
	cache: false,
         success: function(response) {
		if(response.success==true){
			if(single_toggle==true){
				if(response.state==1){
					jQuery("#"+option_name+"-"+post_id).addClass("notification_set").addClass(option_name+"_on").removeClass(option_name+"_off");
				} else {
					jQuery("#"+option_name+"-"+post_id).removeClass("notification_set").addClass(option_name+"_off").removeClass(option_name+"_on");
				}
			} else {
				if(response.state==1){
					jQuery("#"+option_name+"-"+post_id+"-yes").addClass(option_name+"-yes_on").removeClass(option_name+"-yes_off");
					jQuery("#"+option_name+"-"+post_id+"-no").addClass(option_name+"-no_off").removeClass(option_name+"-no_on");
				} else if(response.state==0) {
					jQuery("#"+option_name+"-"+post_id+"-yes").addClass(option_name+"-yes_off").removeClass(option_name+"-yes_on");				
					jQuery("#"+option_name+"-"+post_id+"-no").addClass(option_name+"-no_on").removeClass(option_name+"-no_off");
				} else {
					jQuery("#"+option_name+"-"+post_id+"-yes").addClass(option_name+"-yes_off").removeClass(option_name+"-yes_on");				
					jQuery("#"+option_name+"-"+post_id+"-no").addClass(option_name+"-no_off").removeClass(option_name+"-no_on");
				}


			}
		} else {
			alert("Error: "+response.error);
		}
         }
      });   
}
