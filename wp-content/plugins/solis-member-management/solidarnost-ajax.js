function set_error(field){
	jQuery("#"+field+"-span").addClass("form_error");
}
function clear_error(field){
	jQuery("#"+field+"-span").removeClass("form_error");
}


      function validate(element) {
/*      post_id = jQuery(this).attr("data-post_id")
     nonce = jQuery(this).attr("data-nonce") */
	value=jQuery("#"+element).val();
	req=jQuery("#"+element).attr("required");
	if(req!=undefined){
		if(value=="") set_error(element);
		else clear_error(element);
	}
if(element=="email" && value!=""){
  jQuery.ajax({
         type : "get",
         dataType : "json",
         url : solidarnostAjax.ajaxurl,
         data : {action: "solidarnost_verify_email", value:value},
	cache: false,
         success: function(response) {
               if(response.retval==1) set_error("email");
               if(response.retval==0){
		 clear_error("email");
		 jQuery("#username").val(value);
		}
            }
      });   
}

if(element=="postcode"){
		
      jQuery.ajax({
         type : "get",
         dataType : "json",
         url : solidarnostAjax.ajaxurl,
         data : {action: "solidarnost_get_postname", value:value},
	cache: false,
         success: function(response) {
               jQuery("#postname").val(response.post_name);
               jQuery("#municipality").val(response.municipality);
            }
      });   
} // end if element==postcode
 }// end function


function solidarnost_submit(){
	var values=jQuery("#democracy_adduser_form").serialize();
	//alert("was here1");
 jQuery.ajax({
         type : "get",
         dataType : "json",
         url : solidarnostAjax.ajaxurl,
         data : values,
	cache: false,
         success: function(response) {
		//alert(response);
               if(response.success) 
			alert("All is ok!");	

		else{
			alert("Info is incorrect");
		}

            }
      });   

}


function solis_load_user(field){
	var value=jQuery("#"+field).val();
 	jQuery.ajax({
         type : "get",
         dataType : "json",
         url : solidarnostAjax.ajaxurl,
         data : {action: "solis_get_user_id", field: field, data:value},
	cache: false,
         success: function(response) {
		if(response.success){
			solis_fill_in_form(response.uid);
		} else {
			alert(response.error_message);
		}
	}
	});
	
}

function solis_load_user_pn(offset){
	var value=jQuery("#username").val();
 	jQuery.ajax({
         type : "get",
         dataType : "json",
         url : solidarnostAjax.ajaxurl,
         data : {action: "solis_get_user_id", field: 'username', data:value},
	cache: false,
         success: function(response) {
		if(response.success){
			solis_fill_in_form(response.uid+offset);
		} else {
			alert(response.error_message);
		}
	}
	});
	
}


function solis_fill_in_form(user_id){
//	var values=jQuery("#democracy_adduser_form").serialize();
	var values={user_id:1,
			action: "solis_get_user_data"
	} 
 jQuery.ajax({
         type : "get",
         dataType : "json",
         url : solidarnostAjax.ajaxurl,
         data : {action: "solis_get_user_data", uid:user_id},
	cache: false,
         success: function(response) {
		//alert(response.gsm);
/*		for (var prop in response) {
  		if (response.hasOwnProperty(prop)) { 
		//	jQuery("#"+prop).val(response[prop]);
  			// or if (Object.prototype.hasOwnProperty.call(obj,prop)) for safety...
    			//alert("prop: " + prop + " value: " + obj[prop])
  		}
		}

*/
		jQuery('.cbox').prop('checked', false);
		jQuery.each (response, function (field_name) {
			if(field_name=='checkboxes'){
				jQuery.each(response['checkboxes'], function(comp){
					jQuery("#"+response['checkboxes'][comp]).prop('checked',1);
					//console.log(response['solcomp'][comp]);		
				});
			}else if(field_name=='signed' || field_name=='notifications' || field_name=='enableduser') {
					if(response[field_name]!=0) jQuery("#"+field_name).prop('checked',1);
    					console.log (field_name);
    					console.log (response[field_name]);

			}else {
			jQuery("#"+field_name).val(response[field_name]);
    			console.log (field_name);
    			console.log (response[field_name]);

			}

		});
		//jQuery("#name").val(response.municipality);
          /*     if(response.success) 
			alert("All is ok!");	

		else{
			alert("Info is incorrect");
		} */

            }
      });   

}
