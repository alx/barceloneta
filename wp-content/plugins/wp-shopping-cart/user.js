var testsuccess = 0;
var lnid = new Array();
function categorylist(url)
  {
  self.location = url;
  }

var noresults=function(results)
  {
  return true;
  }

var changetaxntotal=function(results) {
	prices = results.split(':');
	total = parseFloat(prices[0])+parseFloat(prices[1])+parseFloat(prices[2]);
	var total = roundNumber(total,2)
	var tax = roundNumber(prices[0],2)
	tax = tax.toFixed(2);
	total = total.toFixed(2);
	document.getElementById('checkout_tax').innerHTML="<span class='pricedisplay'>$"+tax+"</span>";
	document.getElementById('checkout_total').innerHTML="<span class='pricedisplay'>$"+total+"</span>";
	//alert(t);
	return true;
  }

function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}
var getresults=function(results)
  {
  eval(results);
  if(window.drag_and_drop_cart_updater)
    {
     drag_and_drop_cart_updater();
    }
  if(document.getElementById('loadingimage') != null)
    {
    document.getElementById('loadingindicator').style.visibility = 'hidden';
    }
    else if(document.getElementById('alt_loadingimage') != null)
    {
    document.getElementById('alt_loadingindicator').style.visibility = 'hidden';
    }
  if((document.getElementById('sliding_cart') != null) && (document.getElementById('sliding_cart').style.display == 'none'))
    {
    jQuery("#fancy_collapser").attr("src", (WPSC_URL+"/images/minus.png"));
    jQuery("#sliding_cart").show("fast",function(){
    ajax.post("index.php",noresults,"ajax=true&set_slider=true&state=1"); });
    }
  if(document.getElementById('fancy_notification') != null)
    {
    jQuery('#loading_animation').css("display", 'none');
    //jQuery('#fancy_notificationimage').css("display", 'none');
    }
  }
  
function set_billing_country(html_form_id, form_id)
  {
  var billing_region = '';
  country = jQuery(("div#"+html_form_id+" select[@class=current_country]")).val();
  region = jQuery(("div#"+html_form_id+" select[@class=current_region]")).val();
  if(/[\d]{1,6}/.test(region)) // number over 6 digits for a region ID? yeah right, not in the lifetime of this code
    {
    billing_region = "&billing_region="+region;
    }
  ajax.post("index.php",getresults,("ajax=true&form_id="+form_id+"&billing_country="+country+billing_region));
  ajax.post("index.php",changetaxntotal,("ajax=true&changetax=true&form_id="+form_id+"&billing_country="+country+billing_region));
  }

function submitform(frm, show_notification)
  {
  if(show_notification != false)
    {
    show_notification = true;
    }
  //alert(ajax.serialize(frm));
  ajax.post("index.php?ajax=true&user=true",getresults,ajax.serialize(frm));
  if(document.getElementById('loadingimage') != null)
    {
    document.getElementById('loadingimage').src = WPSC_URL+'/images/indicator.gif';
    document.getElementById('loadingindicator').style.visibility = 'visible';
    } 
    else if(document.getElementById('alt_loadingimage') != null)
    {
    document.getElementById('alt_loadingimage').src = WPSC_URL+'/images/indicator.gif';
    document.getElementById('alt_loadingindicator').style.visibility = 'visible';
    }     
  if((show_notification == true) && (document.getElementById('fancy_notification') != null))
    {
    var options = {
      margin: 1 ,
      border: 1 ,
      padding: 1 ,
      scroll: 1 
      };

    form_button_id = frm.id + "_submit_button";
    //alert(form_button_id);

    var container_offset = {};
    new_container_offset = jQuery('#products_page_container').offset(options, container_offset);
    
		if(container_offset['left'] == null) {
      container_offset['left'] = new_container_offset.left;
      container_offset['top'] = new_container_offset.top;
    }
    

    var button_offset = {};
    new_button_offset = jQuery('#'+form_button_id).offset(options, button_offset)

    
    if(button_offset['left'] == null) {
      button_offset['left'] = new_button_offset.left;
      button_offset['top'] = new_button_offset.top;
    }
    
    
    jQuery('#fancy_notification').css("left", (button_offset['left'] - container_offset['left'] + 10) + 'px');
    jQuery('#fancy_notification').css("top", ((button_offset['top']  - container_offset['top']) -60) + 'px');
    
    
    
    jQuery('#fancy_notification').css("display", 'block');
    jQuery('#loading_animation').css("display", 'block');
    jQuery('#fancy_notification_content').css("display", 'none');  
    }
  return false;
  }

function emptycart() {
  ajax.post("index.php",getresults,"ajax=true&user=true&emptycart=true&current_page="+window.location+"");
  if(document.getElementById('loadingimage') != null) {
    document.getElementById('loadingimage').src = WPSC_URL+'/images/indicator.gif';
    document.getElementById('loadingindicator').style.visibility = 'visible';
	} else if(document.getElementById('alt_loadingimage') != null) {
    document.getElementById('alt_loadingimage').src = WPSC_URL+'/images/indicator.gif';
    document.getElementById('alt_loadingindicator').style.visibility = 'visible';
	}    
}

function show_additional_description(id,image_id)
  {
  currentstate = document.getElementById(id).style.display;
  //document.getElementById(id).style.display = 'inline';
  if(currentstate != 'inline')
    {
    document.getElementById(id).style.display = 'inline';
    document.getElementById(image_id).src = WPSC_URL+'/images/icon_window_collapse.gif';
    }
    else
      {
      document.getElementById(id).style.display = 'none';
      document.getElementById(image_id).src = WPSC_URL+'/images/icon_window_expand.gif';
      }
  return false;
  }

function prodgroupswitch(state)
  {
  if(state == 'brands')
    {
    jQuery('.categorydisplay').css("display", 'none');
    jQuery('.branddisplay').css("display", 'block');
    }
    else if(state == 'categories')
      {
      jQuery('.categorydisplay').css("display", 'block');
      jQuery('.branddisplay').css("display", 'none');
      }
  return false;
  }
  
var previous_rating;
function ie_rating_rollover(id,state)
  {
  target_element = document.getElementById(id);
  switch(state)
    {
    case 1:
    previous_rating = target_element.style.background;
    target_element.style.background = "url("+WPSC_URL+"/images/blue-star.gif)";
    break;
    
    default:
    if(target_element.style.background != "url("+WPSC_URL+"/images/gold-star.gif)")
      {
      target_element.style.background = previous_rating;
      }
    break;
    }
  }  
  
var apply_rating=function(results)
  {
  outarr = results.split(",");
  //alert(results);
  for(i=1;i<=outarr[1];i++)
    {
    id = "star"+outarr[0]+"and"+i+"_link";
    document.getElementById(id).style.background = "url("+WPSC_URL+"/images/gold-star.gif)";
    }
    
  for(i=5;i>outarr[1];i--)
    {
    id = "star"+outarr[0]+"and"+i+"_link";
    document.getElementById(id).style.background = "#c4c4b8";
    }
  lnid[outarr[0]] = 1; 
    
  rating_id = 'rating_'+outarr[0]+'_text';
  //alert(rating_id);
  if(document.getElementById(rating_id).innerHTML != "Your Rating:")
    {
    document.getElementById(rating_id).innerHTML = "Your Rating:";
    }
    
  saved_id = 'saved_'+outarr[0]+'_text';
  document.getElementById(saved_id).style.display = "inline";
  update_vote_count(outarr[0]);
  }
  
function hide_save_indicator(id)
  {
  document.getElementById(id).style.display = "none";
  }
  
function rate_item(prodid,rating)
  {
  ajax.post("index.php",apply_rating,"ajax=true&rate_item=true&product_id="+prodid+"&rating="+rating);
  }
  
function update_vote_count(prodid)
  {
  var update_vote_count=function(results)
    {
    outarr = results.split(",");
    vote_count = outarr[0];
    prodid = outarr[1];
    vote_count_id = 'vote_total_'+prodid;
    document.getElementById(vote_count_id).innerHTML = vote_count;
    }
  ajax.post("index.php",update_vote_count,"ajax=true&get_rating_count=true&product_id="+prodid);
  }

function submit_change_country()
  {
  document.forms.change_country.submit();
  }
  
function update_preview_url(prodid)
  {
  image_height = document.getElementById("image_height").value;
  image_width = document.getElementById("image_width").value;
  if(((image_height > 0) && (image_height <= 1024)) && ((image_width > 0) && (image_width <= 1024)))
    {
    new_url = "index.php?productid="+prodid+"&height="+image_height+"&width="+image_width+"";
    document.getElementById("preview_link").setAttribute('href',new_url);
    }
    else
      {
      new_url = "index.php?productid="+prodid+"";
      document.getElementById("preview_link").setAttribute('href',new_url);
      }
  return false;
  }
  
function change_variation(product_id, variation_ids, special) {
  value_ids = '';
  special_prefix = "";
  if(special == true) {
    form_id = "specials_"+product_id;
	} else {
    form_id = "product_"+product_id;
	}
  for(var i in variation_ids) {
    if(!isNaN(parseInt(i))) {
      variation_name = "variation["+variation_ids[i]+"]";
      value_ids += "&variation[]="+document.getElementById(form_id).elements[variation_name].value;
		}
	}
  if(special == true) {
    var return_price=function(results) { 
      eval(results);
      if(product_id != null) {
        target_id = "special_product_price_"+product_id;
				buynow_id = "BB_BuyButtonForm"+product_id;
				document.getElementById(target_id).firstChild.innerHTML = price;
				if (price.substring(27,price.indexOf("&"))!='')
					document.getElementById(buynow_id).item_price_1.value = price.substring(27,price.indexOf("&"));
				}
      }
	} else {
    var return_price=function(results) {
      //alert(results);
      eval(results);
      if(product_id != null) {
        target_id = "product_price_"+product_id;
				buynow_id = "BB_BuyButtonForm"+product_id;
							document.getElementById(target_id).firstChild.innerHTML = price;
				if (price.substring(27,price.indexOf("&"))!='') {
					document.getElementById(form_id).item_price_1.value = price.substring(27,price.indexOf("&"));
				}
			}
		}
	}
  ajax.post("index.php",return_price,"ajax=true&get_updated_price=true&product_id="+product_id+value_ids);
}
  
function shopping_cart_collapser() {
  switch(jQuery("#sliding_cart").css("display")) {
    case 'none':
    jQuery("#sliding_cart").slideToggle("fast",function(){
      ajax.post("index.php",noresults,"ajax=true&set_slider=true&state=1");
      jQuery("#fancy_collapser").attr("src", (WPSC_URL+"/images/minus.png"));
		});
    break;
    
    default:
    jQuery("#sliding_cart").slideToggle("fast",function(){
      ajax.post("index.php",noresults,"ajax=true&set_slider=true&state=0");
      jQuery("#fancy_collapser").attr("src", (WPSC_URL+"/images/plus.png"));
		});
    break;
	}
  return false;
}
  
function show_details_box(id,image_id) {
  state = document.getElementById(id).style.display; 
  if(state != 'block') {
    document.getElementById(id).style.display = 'block';
    document.getElementById(image_id).src = WPSC_URL+"/images/icon_window_collapse.gif";
	} else {
		document.getElementById(id).style.display = 'none';
		document.getElementById(image_id).src = WPSC_URL+"/images/icon_window_expand.gif";
	}
  return false;
}
  
var register_results=function(results) {
  jQuery("div#TB_ajaxContent").html(results);
  jQuery('div#checkout_login_box').css("border", '1px solid #339933');
  jQuery('div#checkout_login_box').css("background-color", '#e8fcea');
}
  
function submit_register_form(frm)
  {
  jQuery('img#register_loading_img').css("display", 'inline');
  ajax.post("index.php?ajax=true&action=register",register_results,ajax.serialize(frm));

  return false;
  }

var fadeInSuggestion = function(suggestionBox, suggestionIframe) {
	$(suggestionBox).fadeTo(300,1);
};

var fadeOutSuggestion = function(suggestionBox, suggestionIframe) {
	$(suggestionBox).fadeTo(300,0);
};

function change_pics(command){
	location1 = window.location.href;

	if (command == 1){
		document.getElementById('out_view_type').innerHTML = "<input type='hidden' id='view_type' name='view_type' value='default'>";
		document.getElementById('out_default_pic').innerHTML ="<img id='default_pic' src='"+WPSC_URL+"/images/default-on.gif'>";
		document.getElementById('out_grid_pic').innerHTML ="<img id='grid_pic' style='cursor:pointer;' onclick='change_pics(0)' src='"+WPSC_URL+"/images/grid-off.gif'>";
		if (location1.search(/view_type/)!=-1) {
			$new_location = location1.replace("grid","default");
		} else {
		  if (location1.search(/\?/)!=-1) {
				$new_location = location1+"&view_type=default";
			} else {
				$new_location = location1+"?view_type=default";
			}
		}
		window.location = $new_location;
	} else {
		document.getElementById('out_view_type').innerHTML = "<input type='hidden' id='view_type' name='view_type' value='grid'>";
		document.getElementById('out_default_pic').innerHTML ="<img id='default_pic'  style='cursor:pointer;' onclick='change_pics(1)' src='"+WPSC_URL+"/images/default-off.gif'>";
		document.getElementById('out_grid_pic').innerHTML ="<img id='grid_pic' src='"+WPSC_URL+"/images/grid-on.gif'>";
		if (location1.search(/view_type/)!=-1) {
			$new_location = location1.replace("default","grid");
		} else {
		  if (location1.search(/\?/)!=-1) {
				$new_location = location1+"&view_type=grid";
			} else {
				$new_location = location1+"?view_type=grid";
			}
		}
		
		window.location = $new_location;
	}
}

function log_buynow(form){
	id = form.product_id.value;
	price = form.item_price_1.value;
	ajax.post("index.php",noresults,"ajax=true&buynow=true&product_id="+id+"price="+price);
}

function gotoexternallink(link){
	window.location = link;
}

function manage_extras(product_id, extras_id, special) {
	value_ids = '';
	special_prefix = "";
	extra_idss='';
	document.getElementById('extras_indicator'+product_id+extras_id).style.display='block';
	if(special == true) {
		form_id = "specials_"+product_id;
	} else {
		form_id = "product_"+product_id;
	}
	
	jQuery(document).ready(function(){
		extra_ids=jQuery("input.extras_"+product_id+":checked");
	});
	
	jQuery.each(extra_ids, function(key, value) {
		extra_idss += "&extra[]="+extra_ids[key].value;
	});
	pm='stay';

	if(special == true) {
		var return_price=function(results) {
			//alert(results);
			eval(results);
			if(product_id != null) {
				target_id = "special_product_price_"+product_id;
				buynow_id = "BB_BuyButtonForm"+product_id;
				document.getElementById(target_id).firstChild.innerHTML = price;
				if (price.substring(27,price.indexOf("&"))!='')
					document.getElementById(buynow_id).item_price_1.value = price.substring(27,price.indexOf("&"));
			}
			document.getElementById('extras_indicator'+product_id+extras_id).style.display='none';
		}
	} else {
		var return_price=function(results) {
			eval(results);
			if(product_id != null) {
				target_id = "product_price_"+product_id;
				buynow_id = "BB_BuyButtonForm"+product_id;
				document.getElementById(target_id).firstChild.innerHTML = price;
				if (price.substring(27,price.indexOf("&"))!='')
					document.getElementById(form_id).item_price_1.value = price.substring(27,price.indexOf("&"));
			}
			document.getElementById('extras_indicator'+product_id+extras_id).style.display='none';
		}
	}
ajax.post("index.php",return_price,"ajax=true&get_updated_price=true&pm="+pm+"&product_id="+product_id+extra_idss);
}

function store_list(){
	address = document.getElementById('user_address').value;
	city = document.getElementById('user_city').value;
	if ((address != '')&&(city != '')) {
		document.getElementById('gloc_loading').style.display='block';
		ajax.post("index.php",return_store_list,"ajax=true&store_list=true&addr="+address+"&city="+city);
	}
}

var return_store_list=function(results) {
	document.getElementById('gloc_storelist').innerHTML=results;
	document.getElementById('gloc_loading').style.display='none';
	return true;
}

function autocomplete(event) {
	if(!event){
		event=window.event;
	}
	if(event.keyCode){
		keyPressed=event.keyCode;
	}else if(event.which){
		keyPressed=event.which;
	}
// 	if (keyPressed==8){
// 		if (document.getElementById('blind_down').style.display!='none') {
// 			jQuery('#blind_down').slideUp(100);
// 		}
// 		return false;
// 	}
	//document.getElementById('wpsc_search_autocomplete').style.display='block';
	str = document.getElementById('wpsc_search_autocomplete').value;
	if (str != '') {
		ajax.post("index.php",autocomplete_results,"wpsc_live_search=true&keyword="+str);
	} else {
		jQuery('#blind_down').slideUp(100);
	}
}

var autocomplete_results=function(results) {
	document.getElementById('blind_down').innerHTML=results;
	if (document.getElementById('blind_down').style.display!='block') {
		jQuery('#blind_down').slideDown(200);
	}
	return true;
}

function statusTextKeyPress(event){
	if(!event){
		event=window.event;
	}
	if(event.keyCode){
		keyPressed=event.keyCode;
	}else if(event.which){
		keyPressed=event.which;
	}
	if(keyPressed==9){
		return false;
	}
	if(keyPressed==13){
		newstatus = document.getElementById('status_change_text').value;
		ajax.post("index.php",submit_user_status,"ajax=true&submitstatus=true&status="+newstatus);
		return false;
	}
	if(keyPressed==27){
		document.getElementById('edit_status_select').style.display='none';
		return false;
	}
	return true;
}
function switchmethod(key){
	total=document.getElementById("shopping_cart_total_price").value;
	ajax.post("index.php",usps_method_switch,"ajax=true&uspsswitch=true&key="+key+"&total="+total);
}

var usps_method_switch=function (results){
	document.getElementById("checkout_total").innerHTML=results;
}