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
