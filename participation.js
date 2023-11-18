var editingallowed=false;
function teilnahme(uid,abs,divid,update_stats) {
	if(!update_stats){
		var update_stats=false;
	}
	var a = false;
	if(arguments.length == 4 || arguments.length == 3){
		var grund = document.getElementById("grund-"+divid).value;
	}else{
		a = true;
		var grund = "Absage";
	}
	var xmlhttp = new XMLHttpRequest();
       xmlhttp.onreadystatechange = function() {
        	if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			if(!a){
			    if(abs){
				document.getElementById("teilnahme-"+divid).innerHTML="Sie nehmen teil.";
				document.getElementById("absage-"+divid).innerHTML='<button class="teilnahme_button tribe-common-c-btn" type="button" onclick="absagen('+uid+','+divid+','+update_stats+');">Absagen</button>';
			    }else{
				document.getElementById("absage-"+divid).innerHTML="Sie haben abgesagt.";
				document.getElementById("teilnahme-"+divid).innerHTML='<button class="teilnahme_button tribe-common-c-btn" type="button" onclick="teilnahme('+uid+',true,'+divid+','+update_stats+');">Teilnehmen</button>';
			    }
			    document.getElementById("popup-"+divid).className='overlayHidden';
			}else{
			    document.getElementById("popup").className='overlayHidden';
			}
			if(update_stats){
				get_num_participations(divid);
				get_participating_names(divid);
			}
            	}
      	}
	if(abs||grund!=""){
      		xmlhttp.open("GET", "/ajax/register_participation.php?pid="+divid+"&abs="+abs+"&grund="+grund, true);
       		xmlhttp.send();
	}else{
		document.getElementById("hinweis-"+divid).innerHTML="Bitte Grund angeben!";
	}
}

function add_teilnahme(uid,abs,divid,update_stats) {
	if(!update_stats){
		var update_stats=false;
	}
	var a = false;
	if(arguments.length == 4 || arguments.length == 3){
		var grund = document.getElementById("grund-"+divid).value;
	}else{
		a = true;
		var grund = "Absage";
	}
	var xmlhttp = new XMLHttpRequest();
       xmlhttp.onreadystatechange = function() {
        	if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			if(!a){
			    if(abs){
				document.getElementById("teilnahme-"+divid).innerHTML="Sie nehmen teil.";
				document.getElementById("absage-"+divid).innerHTML='<button class="teilnahme_button" type="button" onclick="absagen('+uid+','+divid+','+update_stats+');">Absagen</button>';
			    }else{
				document.getElementById("absage-"+divid).innerHTML="Sie haben abgesagt.";
				document.getElementById("teilnahme-"+divid).innerHTML='<button class="teilnahme_button" type="button" onclick="teilnahme('+uid+',true,'+divid+','+update_stats+');">Teilnehmen</button>';
			    }
			    document.getElementById("popup-"+divid).className='overlayHidden';
			}else{
			    document.getElementById("popup").className='overlayHidden';
			}
			if(update_stats){
				get_num_participations(divid);
				get_participating_names(divid);
			}
            	}
      	}
	if(abs||grund!=""){
      		xmlhttp.open("GET", "/ajax/add_participation.php?uid="+uid+"&pid="+divid+"&abs="+abs+"&grund="+grund, true);
       		xmlhttp.send();
	}else{
		document.getElementById("hinweis-"+divid).innerHTML="Bitte Grund angeben!";
	}
}

function get_participation(uid,divid) {
	var xmlhttp = new XMLHttpRequest();
       xmlhttp.onreadystatechange = function() {
        	if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			if(xmlhttp.responseText == 1){
              		document.getElementById("teilnahme-"+divid).innerHTML="Sie nehmen teil.";
			}
			if(xmlhttp.responseText == 0){
              		document.getElementById("absage-"+divid).innerHTML="Sie haben abgesagt.";
			}

            	}
      	}
      	xmlhttp.open("GET", "/ajax/get_participation.php?pid="+divid, true);
       xmlhttp.send();
}

function get_num_participations(divid){
	var xmlhttp = new XMLHttpRequest();
       xmlhttp.onreadystatechange = function() {
        	if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var args=xmlhttp.responseText.split(",");
			document.getElementById("zusagen").innerHTML=args[0];
			document.getElementById("absagen").innerHTML=args[1];
            	}
      	}
      	xmlhttp.open("GET", "/ajax/num_participations.php?pid="+divid, true);
       xmlhttp.send();
}

function get_participating_names(divid){
	var xmlhttp = new XMLHttpRequest();
       xmlhttp.onreadystatechange = function() {
        	if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("teilnahme_detail").innerHTML=xmlhttp.responseText;
			if(editingallowed){
				document.getElementById("allowediting").disabled = true;
			}
			/*var args=xmlhttp.responseText.split(";");
			document.getElementById("zusagen_detail").innerHTML=args[0];
			var absagen=args[1].split(",,");
			var text="";
			var tmp=absagen[0].split("((");
			text='<a onmouseover="nhpup.popup(\''+tmp[1]+'\');" href="#">'+tmp[0]+'</a>';
			for (index = 1; index < absagen.length; ++index) {
    				var tmp=absagen[index].split("((");
				//text=text+',<a onmouseover="nhpup.popup(\''+tmp[1]+'\');" href="#">'+tmp[0]+'</a>';
			}
			document.getElementById("teilnahme_detail").innerHTML=text;*/
            	}
      	}
      	xmlhttp.open("GET", "/ajax/get_participating_names.php?pid="+divid, true);
       xmlhttp.send();
}
function absagen(uid,divid,update_stats){
	var open = document.getElementsByClassName("overlay");
	for(var i = 0;i < open.length;i++){
		open[i].className="overlayHidden";
	}
	document.getElementById("grund-"+divid).value="";
	document.getElementById("popup-"+divid).className="overlay";
	var xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
        	if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			if(xmlhttp.responseText!="ok"){
				var args=xmlhttp.responseText.split("/");
				if(args[1]*2>=args[0]){
					document.getElementById("hinweis-"+divid).innerHTML="Achtung: Aus deinem Register haben sich schon "+args[1]+" von "+args[0]+" Leuten abgemeldet!";

				}
			}
            	}
      	}
      	xmlhttp.open("GET", "/ajax/get_participating_group_members.php?pid="+divid, true);
       xmlhttp.send();

}
function showPopup(name,vid){
   if(editingallowed){
	var element = document.getElementById(name);
	var element2 = document.getElementById("popup");
	var position = getPositionTo(element,document.getElementById("tribe-events-content"));
	element2.style.position = "absolute";
	element2.style.left = position.x+"px";
	element2.style.top = position.y+"px";
	element2.className="overlay";
	var id = document.getElementById(name+"-id").innerHTML;
	element2.innerHTML='<div id="teilnahme-name">'+name+'</div><button id="zusage-button" onclick="add_teilnahme('+id+',true,'+vid+',true,1);">Zusagen</button><button id="absage-button" onclick="add_teilnahme('+id+',false,'+vid+',true,1);">Absagen</button><button id="schliessen" onclick="document.getElementById(\'popup\').className=\'overlayHidden\';">X</button>';
   }
}
function getPositionTo(element,to) {
    var xPosition = 0;
    var yPosition = 0;
      
    while(element!=to){
        xPosition += (element.offsetLeft - element.scrollLeft + element.clientLeft);
        yPosition += (element.offsetTop - element.scrollTop + element.clientTop);
        element = element.offsetParent;
    }
    return { x: xPosition, y: yPosition };
}
function allowEditing(){
	editingallowed = true;
	document.getElementById('allowediting').disabled = true;
}

function get_participations(uid,divids) {
	var xmlhttp = new XMLHttpRequest();
       xmlhttp.onreadystatechange = function() {
        	if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var teilnahmen = xmlhttp.responseText.split(",");
			var index;
			var pids = divids.split(",");
			for(index = 0; index < pids.length; ++index){
				if(teilnahmen[index] == 1){
              				document.getElementById("teilnahme-"+pids[index]).innerHTML="Sie nehmen teil.";
				}
				if(teilnahmen[index] == 0){
              				document.getElementById("absage-"+pids[index]).innerHTML="Sie haben abgesagt.";
				}
			}
            	}
      	}
      	xmlhttp.open("GET", "/ajax/get_participations.php?pid="+divids, true);
       xmlhttp.send();
}