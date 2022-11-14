function get_stats(year) {
	var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
        	if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("stats").innerHTML = xmlhttp.responseText;
            }
        }
      	xmlhttp.open("GET", "/ajax/stats.php?year="+year, true);
        xmlhttp.send();
}