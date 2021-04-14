function validaForm(){
	var form = document.forms[0];
	var maxchars = 1500000;

	if (form.form_tree_newick.value.length > maxchars) {
		( function($) {  $('#tr_tree_newick').css('background-color', 'rgb(255, 175, 175)'); } ) ( jQuery );
		alert("Too much data in the text box Tree! Please remove " + (form.form_tree_newick.value.length - maxchars) + " characters");
		( function($) {  $('#tr_tree_newick').css('background-color', 'rgb(255, 175, 175)'); } ) ( jQuery );
    return "";
	}
	else if( form.form_put_mdcc.value.lenght > maxchars){
		alert("Too much data in the text box Tips! Please remove " + (form.form_put_mdcc.value.length - maxchars) + " characters");
		( function($) {  $('#tr_put_mdcc').css('background-color', 'rgb(255, 175, 175)'); } ) ( jQuery );
    return "";
	}
	else if (form.form_tree_newick.value.length == 0) {
		alert("No data included in box Tree - submission rejected.");
		( function($) {  $('#tr_tree_newick').css('background-color', 'rgb(255, 175, 175)'); } ) ( jQuery );
    return "";
	}
	else {
		var tree = form.form_tree_newick.value.replace(/^\s+|\s+$/g,'');
		var puts = form.form_put_mdcc.value.replace(/^\s+|\s+$/g,'');
		// var calcMat = "savefile.php";
		// var geraTree = "savefile.php";
		// var imageCalcMat = "images/downloadLow.png";
		// var imageGeraTree = "images/downloadLow.png";
		if( tree == "Unfortunately your browser does not support file upload." ){
			alert("Unfortunately your browser does not support file upload.");
			return "";
		}
		if( puts == "Unfortunately your browser does not support file upload."){
			alert("Unfortunately your browser does not support file upload.");
			return "";
		}
		var params = "" + tree + "#";
		if( form.form_put_mdcc.value == "" ) params += "#none";
		else{
			params += "#";
			var i = 0;
			var str = form.form_put_mdcc.value + "\n*";
			var cnt = 0;
			var ant = " ";
			var check = 0;
			str = str.replaceAll("\t", " ");
			while( str.charAt(i) != '*' ){
				if( str.charAt(i) == '\n' ){
					if( cnt != 2 && check == 1 ){
						alert(i);
						( function($) {  $('#tr_put_mdcc').css('background-color', 'rgb(255, 175, 175)'); } ) ( jQuery );
						alert("Error: Format of Species is incorrect!");
						return ""; }
					else if( check == 1 ) params += "@";
					ant = " ";
					cnt = 0;
					check = 0;
					i++;
					continue;
				}
				else if( str.charAt(i) == ' ' ){
					if( ant.charAt(0) != ' ' && check <= 0 ){ check = 1; params += "~";  }
				}
				else if( str.charAt(i) != '*' ){
					if( ant.charAt(0) == ' ' ) cnt ++;
					params += str.charAt(i);
				}
				ant = str.charAt(i);
				i++;
			}
		}
		// check &
		//alert(params);
		params = params.replace(/&/g ,"¨");
		//alert(params);
		//alert("ok");
		// check tree format newick
		var tree = form.form_tree_newick.value + "!";
		var i = 0;
		var ch;
		var lbrack = 0;
		var rbrack = 0;
		var space = 0;
		var let = 0;
		while( tree.charAt(i) != '!' ){
			ch = tree.charAt(i);
			if( ch.charAt(0) == '(' ) lbrack++;
			else if( ch.charAt(0) == ')' ) rbrack++;
			else if( ch.charAt(0) == ' ' ) space++;
			else let ++;
			i++
		}
		if(lbrack != rbrack || ( ( space > 0 || let > 0 ) && lbrack == 0 && rbrack == 0 )){ alert("Error: The tree is not in Newick format!"); return ""; }
		var checkboxes_based = document.getElementsByName('checkbox_based');
		params += "#";
		if( checkboxes_based[0].checked ){
			params += "0";
		}
		if( checkboxes_based[1].checked  ){
			params += "1";
		}
		if(!(checkboxes_based[1].checked || checkboxes_based[0].checked)){
			if( form.form_put_mdcc.value != ""){
				( function($) {  $('#tr_based').css('background-color', 'rgb(255, 175, 175)'); } ) ( jQuery );
				alert("Choose one of the methods of insertion!");
				return "";
			}
			else params += "none";
		}
		if((checkboxes_based[1].checked || checkboxes_based[0].checked)){
			if( form.form_put_mdcc.value == ""){
				( function($) {  $('#tr_put_mdcc').css('background-color', 'rgb(255, 175, 175)'); } ) ( jQuery );
				alert("The insertion method has been selected but have none PUT and MDDC informed!");
				return "";
			}
		}
		if( document.getElementById('number_of_trees').value  ){
			params += "#";
			params += document.getElementById('number_of_trees').value;
			if( !(form.form_put_mdcc.value.length > 0) ){
				params += "#0";
				geraTree = "javascript:checkFileTree();";
				imageGeraTree = "images/downloadLowPb.png";
			}
		}
		else{
			if( form.form_put_mdcc.value.length > 0 ){
				( function($) {  $('#tr_options').css('background-color', 'rgb(255, 175, 175)'); } ) ( jQuery );
				alert("Enter the number of trees to be generated!");
				return "";
			}
			else{
				params += "#";
				params += "0";
				geraTree = "javascript:checkFileTree();";
				imageGeraTree = "images/downloadLowPb.png";
			}
		}
		if( document.getElementById('compute_distance_matrices').checked ) params += "#1";
		else{
			params += "#";
			params += "0";
			calcMat = "javascript:checkFileMatrice();";
			imageCalcMat = "images/downloadLowPb.png";
		}
		if( form.form_tree_newick.value.length > 0 && form.form_put_mdcc.value.length <= 0
			&& !document.getElementById('compute_distance_matrices').checked ){
				( function($) {  $('#tr_options').css('background-color', 'rgb(255, 175, 175)'); } ) ( jQuery );
				alert("The option 'Calculate distance matrices' has not been checked!");
				return "";
		}

    var extExp = form.extension_tree_file.value;
    var extMat = form.extension_distance_file.value;
    params += "#" + extExp;
    params += "#" + extMat;
    //var variables = "calcMat="+calcMat+"&imageCalcMat="+imageCalcMat+"&geraTree="+geraTree+"&imageGeraTree="+imageGeraTree+"&extExp="+extExp+"&extMat="+extMat;
		//alert(String(variables));

		document.getElementById("params_hash").value = params;

    return params;

		xmlHttp.open("POST", "sunplin.php?"+variables, true);
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		//xmlHttp.setRequestHeader("Content-length", params.length);
		//xmlHttp.setRequestHeader("Connection", "close");
		xmlHttp.onreadystatechange = handlerSubmitSequence;
		/* Mostra a mensagem de loading */
		var textoLoading = "<br/><br/><div class=\"labels\" style=\"text-align: center; font-weight: bold;\">Processing... <br/><img src=\"images/loadingProgress.gif\" /></div>";
		document.getElementById("conteudoPrincipal").innerHTML = textoLoading;
		var loadingLogo = "<div class=\"labels\" style=\"text-align: center; font-weight: bold;\"><img src=\"images/loading.gif\" /></div>";
		document.getElementById("logo").innerHTML = loadingLogo;
    document.getElementById("siteDesc").style.visibility = "hidden";
 	  document.getElementById("desc").innerHTML= "";
 	  document.getElementById("desc").style.visibility="hidden";
    document.getElementById("a").href += extExp;
    document.getElementById("b").href += extMat;
		xmlHttp.send("str="+params);
		document.getElementById("a").style.visibility= "visible";
		document.getElementById("b").style.visibility= "visible";
		document.getElementById("back1").style.visibility= "visible";
  }
}
