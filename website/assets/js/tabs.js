function openTabStatic(but,div='tab1'){
	var tab = document.getElementById("tab");		
	var tabcontents = tab.getElementsByClassName("tabContent");
	for (var i = 0; i < tabcontents.length; i++){
		if(tabcontents[i].id==div)
			tabcontents[i].style.display = "block";
		else
			tabcontents[i].style.display = "none";
	}
	
	tab.getElementsByClassName("active")[0].className="tabButton";
	
	var tabButtons=tab.getElementsByClassName("tabButton");
	for (i = 0; i < tabButtons.length; i++){
		if(tabButtons[i]==but)
			tabButtons[i].className="active";
		else
			tabButtons[i].className="tabButton";
	}
}