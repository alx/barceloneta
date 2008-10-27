var someresults=function()  {
  document.getElementById('changenotice').innerHTML = "Thank you, your change has been saved";
  }

var noresults=function()  {
  // see nothing, know nothing, do nothing
  }

if(typeof(select_min_height) == undefined)
  {
  var select_min_height = 0;
  var select_max_height = 200;
  }

jQuery(document).ready(
  function() {  
  jQuery('div.select_product_file').Resizable({
    minWidth: 300,
    minHeight: select_min_height,
    maxWidth: 300,
    maxHeight: select_max_height,
    handlers: {
      s: '.select_product_handle'
      }
    });
		jQuery("div.admin_product_name a.shorttag_toggle").toggle(
			function () {
				jQuery("div.admin_product_shorttags", jQuery(this).parent("div.admin_product_name")).css('display', 'block');
			},
			function () {
				//jQuery("div#admin_product_name a.shorttag_toggle").toggleClass('toggled');
				jQuery("div.admin_product_shorttags", jQuery(this).parent("div.admin_product_name")).css('display', 'none');
			}
		);
  }
);

function activate_resizable() {
  jQuery('div.edit_select_product_file').Resizable({
    minWidth: 300,
    minHeight: select_min_height,
    maxWidth: 300,
    maxHeight: select_max_height,
    handlers: {
      s: '.edit_select_product_handle'
      }
	});
}
  
	jQuery(document).ready(function(){
		jQuery(function() {
		  // set us up some mighty fine tabs for the options page
			jQuery('#wpsc_options > ul').tabs();
			
			// this here code handles remembering what tab you were on
			jQuery('#wpsc_options > ul').bind('tabsselect', function(event, ui) {
			  form_action = jQuery('#cart_options').attr('action').split('#');  //split at the #
			  form_action = form_action[0]+"#"+ui.panel.id; // get the first item, add the hash then our current tab ID
			  jQuery('#cart_options').attr('action', form_action); // stick it all back in the action attribute
			});
		});
  });
  
  
function categorylist(url) {
  self.location = url;
}
  
function submit_change_country() {
  document.cart_options.submit();
  //document.cart_options.submit();
}
  
var getresults=function(results) {
  document.getElementById('formcontent').innerHTML = results;
  document.getElementById('additem').style.display = 'none';
  document.getElementById('productform').style.display = 'block';
	jQuery("#loadingindicator_span").css('visibility','hidden');
  jQuery('#formcontent .postbox h3').click( function() {
  	jQuery(jQuery(this).parent('div.postbox')).toggleClass('closed');
		if(jQuery(jQuery(this).parent('div.postbox')).hasClass('closed')) {
			jQuery('a.togbox',this).html('+');
		} else {
			jQuery('a.togbox',this).html('&ndash;');
		}
  });
  activate_resizable();
  TB_init();
  
	jQuery("div.admin_product_name a.shorttag_toggle").toggle(
		function () {
			jQuery("div.admin_product_shorttags", jQuery(this).parent("div.admin_product_name")).css('display', 'block');
		},
		function () {
			//jQuery("div#admin_product_name a.shorttag_toggle").toggleClass('toggled');
			jQuery("div.admin_product_shorttags", jQuery(this).parent("div.admin_product_name")).css('display', 'none');
		}
	);
}

function filleditform(prodid)	{
	ajax.post("index.php",getresults,"ajax=true&admin=true&prodid="+prodid);
	jQuery('#loadingimage').attr('src', jQuery("#loadingimage").attr('src'));
	jQuery('#loadingindicator_span').css('visibility','visible');
}
   
function fillvariationform(variation_id) {
  ajax.post("index.php",getresults,"ajax=true&admin=true&variation_id="+variation_id);
	jQuery('#loadingimage').attr('src', WPSC_URL+'/images/indicator.gif');
	jQuery('#loadingindicator_span').css('visibility','visible');
}
   
function showaddform() {
   document.getElementById('productform').style.display = 'none';
   document.getElementById('additem').style.display = 'block';
   return false;
}
   
 function showadd_categorisation_form(){
  if(jQuery('div#add_categorisation').css('display') != 'block') {
    jQuery('div#add_categorisation').css('display', 'block');      
    jQuery('div#edit_categorisation').css('display', 'none');
  } else {
    jQuery('div#add_categorisation').css('display', 'none');
  }
	return false;
}


function showedit_categorisation_form(){
  if(jQuery('div#edit_categorisation').css('display') != 'block') {
    jQuery('div#edit_categorisation').css('display', 'block');  
    jQuery('div#add_categorisation').css('display', 'none');
  } else {
		jQuery('div#edit_categorisation').css('display', 'none');
	}
	return false;
}  
	
function fillcategoryform(catid) {
	ajax.post("index.php",getresults,"ajax=true&admin=true&catid="+catid);
}

function fillbrandform(catid) {
	ajax.post("index.php",getresults,"ajax=true&admin=true&brandid="+catid);
}

var gercurrency=function(results)
  {
  document.getElementById('cslchar1').innerHTML = results;
  document.getElementById('cslchar2').innerHTML = results;
  document.getElementById('cslchar3').innerHTML = results;
  document.getElementById('cslchar4').innerHTML = results;
  }

function getcurrency(id) {
	ajax.post("index.php",gercurrency,"ajax=true&currencyid="+id);
}
  
function country_list(id) {
  var country_list=function(results) {
    document.getElementById('options_region').innerHTML = results;
	}
  ajax.post("index.php",country_list,"ajax=true&get_country_tax=true&country_id="+id);
}     
  
function hideelement(id) {
  state = document.getElementById(id).style.display;
  //alert(document.getElementById(id).style.display);
  if(state != 'block') {
    document.getElementById(id).style.display = 'block';
	} else {
		document.getElementById(id).style.display = 'none';
	}
}
  
function update_preview_url(prodid) {
  image_height = document.getElementById("image_height").value;
  image_width = document.getElementById("image_width").value;
  if(((image_height > 0) && (image_height <= 1024)) && ((image_width > 0) && (image_width <= 1024))) {
    new_url = "index.php?productid="+prodid+"&height="+image_height+"&width="+image_width+"";
    document.getElementById("preview_link").setAttribute('href',new_url);
	} else {
		new_url = "index.php?productid="+prodid+"";
		document.getElementById("preview_link").setAttribute('href',new_url);
	}
  return false;
}




function checkimageresize() {
	document.getElementById('image_resize2').checked = true;
}
   
      
   
function add_variation_value(value_type)
  {
  container_id = value_type+"_variation_values";
  //alert(container_id);
  last_element_id = document.getElementById(container_id).lastChild.id;
  last_element_id = last_element_id.split("_");
  last_element_id = last_element_id.reverse();
  new_element_id = "variation_value_"+(parseInt(last_element_id[0])+1);
  
  
  old_elements = document.getElementById(container_id).innerHTML;
  
  //new_element_contents = "<span id='"+new_element_id+"'>";
  new_element_contents = "";
  if(value_type == "edit")
    {
    new_element_contents += "<input type='text' class='text' name='new_variation_values[]' value='' />";
    }
    else
      {
      new_element_contents += "<input type='text' class='text' name='variation_values[]' value='' />";
      }
  new_element_contents += " <a class='image_link' href='#' onclick='remove_variation_value_field(\""+new_element_id+"\")'><img src='"+WPSC_URL+"/images/trash.gif' alt='"+TXT_WPSC_DELETE+"' title='"+TXT_WPSC_DELETE+"' /></a><br />";
  //new_element_contents += "</span>";
  
  new_element = document.createElement('span');
  new_element.id = new_element_id;
   
  document.getElementById(container_id).appendChild(new_element);
  document.getElementById(new_element_id).innerHTML = new_element_contents;
  return false;
  }
  
  
 // if(($_POST['ajax'] == "true") && ($_POST['remove_variation_value'] == "true") && is_numeric($_POST['variation_value_id']))
function remove_variation_value(id,variation_value)
  {
  var delete_variation_value=function(results)
    {
    }
  element_count = document.getElementById("add_variation_values").childNodes.length;
  if(element_count > 1)
    {
    ajax.post("index.php",delete_variation_value,"ajax=true&remove_variation_value=true&variation_value_id="+variation_value);
    target_element = document.getElementById(id);
    document.getElementById("add_variation_values").removeChild(target_element);
    }
  }
 
function remove_variation_value_field(id)
  {
  element_count = document.getElementById("add_variation_values").childNodes.length;
  if(element_count > 1)
    {
    target_element = document.getElementById(id);
    document.getElementById("add_variation_values").removeChild(target_element);
    }
  }
  
function variation_value_list(id) {
	var display_list=function(results) {
		eval(results);
		if(variation_value_html != '') {
			new_element_id = "product_variations_"+variation_value_id;
			if(document.getElementById(new_element_id) === null) {
				new_element = document.createElement('span');
				new_element.id = new_element_id;
				document.getElementById("edit_product_variations").appendChild(new_element);
				document.getElementById(new_element_id).innerHTML = variation_value_html;
			}
		}
		prodid=document.getElementById("prodid").value;
		var id_string = '';
		jQuery("#"+new_element_id+" input[@type='checkbox']").each(function() {id_string+="&"+this.name+"="+this.value; });
		jQuery("#"+new_element_id+" input[@type='hidden']").each(function() {id_string+="&"+this.name+"="+this.value; });
		//alert(id_string);
		//ajax.post("index.php",display_list_ajaxx,"ajax=true&list_variation_values_ajaxx=true&pid="+prodid+id_string);
	}
	ajax.post("index.php",display_list,"ajax=true&list_variation_values=true&variation_id="+id+"&prefix=edit_product_variations");
 }
 

  
function edit_variation_value_list(id) {
  // haah, the javascript end does essentially nothing of interest, just sends a request, and dumps the output in a div tag
	var display_variation_forms=function(results) {
		if(results !== "false") { // do nothing if just the word false is returned
	  //alert(jQuery("div#edit_variations_container").html(results));
		
			//alert(jQuery("div#edit_variations_container"));
			jQuery("div#edit_variations_container").html(results);	
		}
	}	
	product_id= jQuery("#prodid").val();
	ajax.post("index.php",display_variation_forms,"ajax=true&edit_variation_value_list=true&variation_id="+id+"&product_id="+product_id);
 }
  
  
  
var display_list_ajaxx=function(results) {
	jQuery("div#edit_variations_container").html(results);
	//alert(results);
}
  
function add_variation_value_list(id)
  {
	var display_list=function(results) {
		eval(results);
		if(variation_value_html != '') {
			new_element_id = "add_product_variations_"+variation_value_id;
			if(document.getElementById(new_element_id) === null) {
				new_element = document.createElement('span');
				new_element.id = new_element_id;
				document.getElementById("add_product_variations").appendChild(new_element);
				document.getElementById(new_element_id).innerHTML = variation_value_html;
			}
		jQuery("#add_product_variation_details").html(variation_subvalue_html);
		}
		jQuery("#edit_product_variations input[@type='checkbox']").each(function() {
// 		  alert(this.id);
		  });
		//ajax.post("index.php",display_list_ajaxx,"ajax=true&list_variation_values_ajaxx=true");
	}
	current_variations = jQuery("input.variation_ids").serialize();
	ajax.post("index.php",display_list,"ajax=true&list_variation_values=true&new_variation_id="+id+"&prefix=add_product_variations&"+current_variations+"");
}
  
function remove_variation_value_list(prefix,id){
	var redisplay_list=function(results) {
		jQuery("#add_product_variation_details").html(results);
	}
  if(prefix == "edit_product_variations") {
    target_element_id = "product_variations_"+id;
	} else {
		target_element_id = prefix+"_"+id;
	}
  target_element = document.getElementById(target_element_id);
  document.getElementById(prefix).removeChild(target_element);
  if(prefix == "add_product_variations") {
		current_variations = jQuery("input.variation_ids").serialize();
		ajax.post("index.php",redisplay_list,"ajax=true&redisplay_variation_values=true&"+current_variations+"");
  }  
  return false;
}
  
function tick_active(target_id,input_value)
  {
  if(input_value != '')
    {
    document.getElementById(target_id).checked = true;
    }
  }
  
function add_form_field()
  {
  time = new Date();
  new_element_number = time.getTime();
  new_element_id = "form_id_"+new_element_number;
  
  new_element_contents = "";
  new_element_contents += " <table><tr>\n\r";
  new_element_contents += "<td class='namecol'><input type='text' name='new_form_name["+new_element_number+"]' value='' /></td>\n\r";
  new_element_contents += "<td class='typecol'><select name='new_form_type["+new_element_number+"]'>"+HTML_FORM_FIELD_TYPES+"</select></td>\n\r"; 
  new_element_contents += "<td class='mandatorycol' style='text-align: center;'><input type='checkbox' name='new_form_mandatory["+new_element_number+"]' value='1' /></td>\n\r";
  new_element_contents += "<td class='logdisplaycol' style='text-align: center;'><input type='checkbox' name='new_form_display_log["+new_element_number+"]' value='1' /></td>\n\r";
  new_element_contents += "<td class='ordercol'><input type='text' size='3' name='new_form_order["+new_element_number+"]' value='' /></td>\n\r";
  new_element_contents += "<td  style='text-align: center; width: 12px;'><a class='image_link' href='#' onclick='return remove_new_form_field(\""+new_element_id+"\");'><img src='"+WPSC_URL+"/images/trash.gif' alt='"+TXT_WPSC_DELETE+"' title='"+TXT_WPSC_DELETE+"' /></a></td>\n\r";
  new_element_contents += "<td></td>\n\r";
  new_element_contents += "</tr></table>";
  
  new_element = document.createElement('div');
  new_element.id = new_element_id;
   
  document.getElementById("form_field_form_container").appendChild(new_element);
  document.getElementById(new_element_id).innerHTML = new_element_contents;
  return false;
  }
  
function remove_new_form_field(id)
  {
  element_count = document.getElementById("form_field_form_container").childNodes.length;
  if(element_count > 1)
    {
    target_element = document.getElementById(id);
    document.getElementById("form_field_form_container").removeChild(target_element);
    }
  return false;
  }
  
function remove_form_field(id,form_id)
  {
  var delete_variation_value=function(results)
    {
    }
  element_count = document.getElementById("form_field_form_container").childNodes.length;
  if(element_count > 1)
    {
    ajax.post("index.php",delete_variation_value,"ajax=true&remove_form_field=true&form_id="+form_id);
    target_element = document.getElementById(id);
    document.getElementById("form_field_form_container").removeChild(target_element);
    }
  return false;
  }  
  
function show_status_box(id,image_id)
  {
  state = document.getElementById(id).style.display; 
  if(state != 'block')
    {
    document.getElementById(id).style.display = 'block';
    document.getElementById(image_id).src = WPSC_URL+'/images/icon_window_collapse.gif';
    }
    else
      {
      document.getElementById(id).style.display = 'none';
      document.getElementById(image_id).src = WPSC_URL+'/images/icon_window_expand.gif';
      }
  return false;
  }
  
function submit_status_form(id) {
  document.getElementById(id).submit();
}
  
// pe.{
var prevElement = null;
var prevOption = null;

function hideOptionElement(id, option) {
	if (prevOption == option) {
		return;
	}
	if (prevElement != null) {
		prevElement.style.display = "none";
	}
  
	if (id == null) {
		prevElement = null;
	} else {
		prevElement = document.getElementById(id);
		jQuery('#'+id).css( 'display','block');
	}
	prevOption = option;
}


// }.pe  

function toggle_display_options(state)
  {
  switch(state)
    {
    case 'list':
    document.getElementById('grid_view_options').style.display = 'none';
    document.getElementById('list_view_options').style.display = 'block';    
    break;
    
    case 'grid':
    document.getElementById('list_view_options').style.display = 'none';
    document.getElementById('grid_view_options').style.display = 'block';
    break;
    
    default:
    document.getElementById('list_view_options').style.display = 'none';
    document.getElementById('grid_view_options').style.display = 'none';
    break;
    }
  }
  
function log_submitform(id)
  {
	value1 = document.getElementById(id);
	if (ajax.serialize(value1).search(/value=3/)!=-1) {
	document.getElementById("track_id").style.display="block";
	} else {
	document.getElementById("track_id").style.display="none";
	}
	var get_log_results=function(results)
    {
    eval(results);
    }
  frm = document.getElementById(id);
  ajax.post("index.php?ajax=true&log_state=true",get_log_results,ajax.serialize(frm));
  return false;
  }

function save_tracking_id(id)
  {
  value1 = document.getElementById('tracking_id_'+id).value;
  value1 ="id="+id +"&value="+value1;
  ajax.post("index.php?ajax=true&save_tracking_id=true",noresults,value1);
  return false;
  }
  

/* the following is written by Allen */
jQuery(document).ready(
	function()
	{
		jQuery('#description').Resizable(
			{
				minWidth: 50,
				minHeight: 50,
				maxWidth: 400,
				maxHeight: 400,
				handlers: {
					s: '#resizeS'
				},
				onResize: function(size)
				{
					jQuery('textarea', this).css('height', size.height - 6 + 'px');
				}
			}
		);
	}
);

jQuery(document).ready(
	function()
	{
		jQuery('#description1').Resizable(
			{
				minWidth: 50,
				minHeight: 50,
				maxWidth: 400,
				maxHeight: 400,
				handlers: {
					s: '#resizeS1'
				},
				onResize: function(size)
				{
					jQuery('textarea', this).css('height', size.height - 6 + 'px');
				}
			}
		);
	}
);

var select_min_height = 75;
var select_max_height = 50;

//ToolTip JavaScript
jQuery('img').Tooltip(
	{
		className: 'inputsTooltip',
		position: 'mouse',
		delay: 200
	}
);

jQuery(window).load( function () {
    
	jQuery('.additem .postbox h3').click( function() {
		jQuery(jQuery(this).parent('div.postbox')).toggleClass('closed');
		if(jQuery(jQuery(this).parent('div.postbox')).hasClass('closed')) {
			jQuery('a.togbox',this).html('+');
		} else {
			jQuery('a.togbox',this).html('&ndash;');
		}
	});
	
	jQuery('a.closeEl').bind('click', toggleContent);
	jQuery('div.groupWrapper').Sortable( {
			accept: 'groupItem',
			helperclass: 'sortHelper',
			activeclass : 	'sortableactive',
			hoverclass : 	'sortablehover',
			handle: 'div.itemHeader',
			tolerance: 'pointer',
			onChange : function(ser) {
			serialize();
			},
			onStart : function() {
				jQuery.iAutoscroller.start(this, document.getElementsByTagName('body'));
			},
			onStop : function() {
				jQuery.iAutoscroller.stop();
			}
		}
	);

	jQuery('a#close_news_box').click( function () {
		jQuery('div.wpsc_news').css( 'display', 'none' );
		ajax.post("index.php", noresults, "ajax=true&admin=true&hide_ecom_dashboard=true");
		return false;
	});
});
var toggleContent = function(e)
{
	var targetContent = $('div.itemContent', this.parentNode.parentNode);
	if (targetContent.css('display') == 'none') {
		targetContent.slideDown(300);
		$(this).html('[-]');
	} else {
		targetContent.slideUp(300);
		$(this).html('[+]');
	}
	return false;
};


function serialize(s) {
	var serialize_results=function()  {
		jQuery("div#changenotice").css("display","block").html('Product Order Saved');
		jQuery('#loadingindicator_span').css('visibility','hidden');
		}
	serial = jQuery.SortSerialize(s);
	category_id = jQuery("input#item_list_category_id").val();
	ajax.post("index.php", serialize_results, "ajax=true&changeorder=true&category_id="+category_id+"&"+serial.hash);
	jQuery("#loadingimage").attr('src', WPSC_URL+'/images/indicator.gif');
	jQuery('#loadingindicator_span').css('visibility','visible');
};


function hideelement1(id, item_value)
  {
  //alert(value);  
		if(item_value == 5) {
			jQuery(document.getElementById(id)).css('display', 'block');
		} else {
			jQuery(document.getElementById(id)).css('display', 'none');
		}
  }

  
function suspendsubs(user_id)
{
	var comm =jQuery("#suspend_subs"+user_id).attr("checked");
	//alert(comm);
	if (comm == true){
		ajax.post("index.php",noresults,"ajax=true&log_state=true&suspend=true&value=1&id="+user_id);
	} else {		
		ajax.post("index.php",noresults,"ajax=true&log_state=true&suspend=true&value=2&id="+user_id);
	}
	return false;
}

function delete_extra_preview(preview_name, prodid) {
	var preview_name_results=function(results) {
		filleditform(prodid);
	}
	ajax.post("index.php",preview_name_results,"ajax=true&admin=true&prodid="+prodid+"&preview_name="+preview_name);
}

function shipwire_sync() {
	ajax.post("index.php",noresults,"ajax=true&shipwire_sync=ture");
}

function shipwire_tracking() {
	ajax.post("index.php",noresults,"ajax=true&shipwire_tracking=ture");
}

function display_settings_button() {
	jQuery("#settings_button").slideToggle(200);
	//document.getElementById("settings_button").style.display='block';
}

function submittogoogle(id){
	value1=document.getElementById("google_command_list_"+id).value;
	value2=document.getElementById("partial_amount_"+id).value;
	reason=document.getElementById("cancel_reason_"+id).value;
	comment=document.getElementById("cancel_comment_"+id).value;
	message=document.getElementById("message_to_buyer_message_"+id).value;
	document.getElementById("google_command_indicator").style.display='inline';
	ajax.post("index.php",submittogoogleresults,"ajax=true&submittogoogle=true&message="+message+"&value="+value1+"&amount="+value2+"&comment="+comment+"&reason="+reason+"&id="+id);
	return true;
}

var submittogoogleresults=function (results) {
	window.location.reload(true);
}

function display_partial_box(id){
	value1=document.getElementById("google_command_list_"+id).value;
	if ((value1=='Refund') || (value1=='Charge')){
		document.getElementById("google_partial_radio_"+id).style.display='inline';
		if (value1=='Refund'){
			document.getElementById("google_cancel_"+id).style.display='block';
			document.getElementById("cancel_reason_"+id).style.display='inline';
			document.getElementById("cancel_div_comment_"+id).style.display='none';
		}
	}else if ((value1=='Cancel')||(value1=='Refund')) {
		document.getElementById("google_cancel_"+id).style.display='block';
		document.getElementById("cancel_reason_"+id).style.display='inline';
	}else if (value1=='Send Message') {
		document.getElementById("message_to_buyer_"+id).style.display='block';
	} else {
		document.getElementById("cancel_div_comment_"+id).style.display='none';
		document.getElementById("google_cancel_"+id).style.display='none';
		document.getElementById("cancel_reason_"+id).style.display='none';
		document.getElementById("message_to_buyer_"+id).style.display='none';
		document.getElementById("google_partial_radio_"+id).style.display='none';
		document.getElementById("partial_amount_"+id).style.display='none';
	}
}

function add_more_meta(e) {
  current_meta_forms = jQuery(e).parent("div.product_custom_meta");  // grab the form container
  new_meta_forms = current_meta_forms.clone(true); // clone the form container
  jQuery("label input", new_meta_forms).val(''); // reset all contained forms to empty
  current_meta_forms.after(new_meta_forms);  // append it after the container of the clicked element
  return false;
}

function remove_meta(e, meta_id) {
  current_meta_form = jQuery(e).parent("div.product_custom_meta");  // grab the form container
  //meta_name = jQuery("input#custom_meta_name_"+meta_id, current_meta_form).val();
  //meta_value = jQuery("input#custom_meta_value_"+meta_id, current_meta_form).val();
	returned_value = jQuery.ajax({
		type: "POST",
		url: "admin.php?ajax=true",
		data: "admin=true&remove_meta=true&meta_id="+meta_id+"",
		success: function(results) {
			if(results > 0) {
			  jQuery("div#custom_meta_"+meta_id).remove();
			}
		}
	}); 
  return false;
}