function init() {
	tinyMCEPopup.resizeToInnerSize();
}

function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function insertWPSCLink() {

	var tagtext;
	var select_category=document.getElementById('wpsc_category_panel');
	var category = document.getElementById('wpsc_category');
	var slider=document.getElementById('product_slider_panel');

// 	var album = document.getElementById('album_panel');
// 	var singlepic = document.getElementById('singlepic_panel');
	
	// who is active ?
	if (select_category.className.indexOf('current') != -1) {
		var categoryid = category.value;
		var fulldisplay = document.getElementById('wpsc_fulldisplay').checked;
		if (categoryid > 0 ) {
			if (fulldisplay)
				tagtext = "[wpsc_category=" + categoryid + ",full]";
			else
				tagtext = "[wpsc_category=" + categoryid + "]";
		} else {
			tinyMCEPopup.close();
		}
	}
	
	if (slider.className.indexOf('current') != -1) {
		category = document.getElementById('wpsc_slider_category');
		visi = document.getElementById('wpsc_slider_visibles');
		var categoryid = category.value;
		var visibles = visi.value;

		if (categoryid > 0 ) {
			if (visibles != '') {
				tagtext = "[wpsc_slider_category=" + categoryid + "," + visibles + "]";
			} else {
				tagtext = "[wpsc_slider_category=" + categoryid + "]";
			}
		} else {
			tinyMCEPopup.close();
		}
	}

// 	if (singlepic.className.indexOf('current') != -1) {
// // 		var singlepicid = document.getElementById('singlepictag').value;
// // 		var imgWidth = document.getElementById('imgWidth').value;
// // 		var imgHeight = document.getElementById('imgHeight').value;
// // 		var imgeffect = document.getElementById('imgeffect').value;
// // 		var imgfloat = document.getElementById('imgfloat').value;
// 
// 		if (singlepicid != 0 ) {
// 			if (imgeffect == "none")
// 				tagtext = "[singlepic=" + singlepicid + "," + imgWidth + "," + imgHeight + ",," + imgfloat + "]";
// 			else
// 				tagtext = "[singlepic=" + singlepicid + "," + imgWidth + "," + imgHeight + "," + imgeffect + "," + imgfloat + "]";
// 		} else {
// 			tinyMCEPopup.close();
// 		}
// 	}
	
	if(window.tinyMCE) {
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		//Peforms a clean up of the current editor HTML. 
		//tinyMCEPopup.editor.execCommand('mceCleanup');
		//Repaints the editor. Sometimes the browser has graphic glitches. 
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}
