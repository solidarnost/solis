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
