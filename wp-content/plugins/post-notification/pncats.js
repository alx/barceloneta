var post_notification_running = false;
var post_notification_box = new Array();

function post_notification_cats_init(){
	var boxes =  document.getElementsByTagName("input");
	var tocheck = "";
	var i;
	if(post_notification_running) return;
	post_notification_running = true;
	
	
	for(i = 0; i < boxes.length; i++){
		if(boxes[i].id.substr(0, 4)== "cat."){
			if(boxes[i].disabled == true){
				boxes[i].checked = post_notification_box[i];
				boxes[i].disabled = false;	
			}
		}
	}
	
	
	for(i = 0; i < boxes.length; i++){
		if(boxes[i].id.substr(0, 4)== "cat."){
			if(tocheck != ""){
				if(boxes[i].id.substr(0, tocheck.length) == tocheck){
					post_notification_box[i]  = boxes[i].checked;
					boxes[i].checked = true;
					boxes[i].disabled = true;
				} else {
					tocheck = "";
				}
			}
			
			if(tocheck == ""){ //There is no string 
				if(boxes[i].checked == true){
					tocheck = boxes[i].id; //From now on this is the new String
				}
			}
		}
	}
	post_notification_running = false;
}


function post_notification_cats_change(){

}