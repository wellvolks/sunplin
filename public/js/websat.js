var xmlHttp = false;

function newXmlHttpRequest() {
	if (!xmlHttp) {
		try {
			xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
		} 
		catch (e) {
			try {
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			} 
			catch (e2) {
				xmlHttp = false;
			}
		}
	
		if (!xmlHttp && typeof XMLHttpRequest != 'undefined') {
			xmlHttp = new XMLHttpRequest();
		}
	}
}

function sampleSequence() {
	var sample = "CCTTCTGCTTGCGAAGCAGATGCAAGGGAAGCGCACCCTTCGCAAGGAAAGGGGAAAGTGCAGCTTGGTACAGCAGAAAAGAAAGGGAAGCAACAAAAAAAAAATAAGGTCTGTATTAACACAACTCAAATTATATTAAATAATAGCCTAACTTTCTAAGTTAGCCTCTAATTAGTTACTGATGCATAGCTATTTATAGCTATCGAAAAGAAAAGAAAAGAAAAGAAAAGAAAAGAAAAGAGCTAGATATTTTTGATGATGCTTCAAACCTTCATAAATAAAGTTATAATTATATGGTCAGGAAATCAGCTCTCTTTTTATCTTATTTTAATATCTAAGTTCCTGTTTTATAAGTTAGCTTCTCCTTTGCTACCTTTACTATTTTGCTGCAAATTAGCGAAGCAGCCCTTTGTCCGACTCGGTTGTCCGGACTAGTATATGTTCAGACACACACACACACACACACACACTATATCAGAGTGTTCCTCCCTTGCTTTCTTCTTAGCAGCTGCTAAGAAGAAGGGAAGATAAGTTTGCTAAGCTTCTA";
	
	document.forms[0].sequence.value = sample;
}

function validaForm() {
	var form = document.forms[0];
	var maxchars = 150000;
	
	if (form.sequence.value.length > maxchars) {
		alert("Too much data in the text box! Please remove " + (form.sequence.value.length - maxchars) + " characters");
	}
	else if (form.sequence.value.length == 0) {
		alert("No data included - submission rejected.");
	}
	else {
		var sequencia 	= form.sequence.value;
		var params 		= "sequence=" + sequencia + "&seq=true&rnd=" + Math.random();

		if (form.mono.checked) 
			if (form.mmono.value != "")
				params += "&mono=true&mmono=" + form.mmono.value;
			else {
				alert("Please, fill in \"MONO Repeat Minimum\"");
				return false;
			}
			
		if (form.di.checked) 
			if (form.mdi.value != "")
				params += "&di=true&mdi=" + form.mdi.value;
			else {
				alert("Please, fill in \"DI Repeat Minimum\"");
				return false;
			}
		
		if (form.tri.checked) 
			if (form.mtri.value != "")
				params += "&tri=true&mtri=" + form.mtri.value;
			else {
				alert("Please, fill in \"TRI Repeat Minimum\"");
				return false;
			}
		
		if (form.tetra.checked) 
			if (form.mtetra.value != "")
				params += "&tetra=true&mtetra=" + form.mtetra.value;
			else {
				alert("Please, fill in \"TETRA Repeat Minimum\"");
				return false;
			}
		
		if (form.penta.checked) 
			if (form.mpenta.value != "")
				params += "&penta=true&mpenta=" + form.mpenta.value;
			else {
				alert("Please, fill in \"PENTA Repeat Minimum\"");
				return false;
			}
			
		if (form.hexa.checked) {
			if (form.mhexa.value != "")
				params += "&hexa=true&mhexa=" + form.mhexa.value;
			else {
				alert("Please, fill in \"HEXA Repeat Minimum\"");
				return false;
			}
		}
/*
		xmlHttp.open("POST", "cgi-local/sat.php", true);
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlHttp.setRequestHeader("Content-length", params.length);
		xmlHttp.setRequestHeader("Connection", "close");
		xmlHttp.onreadystatechange = handlerSubmitSequence;
*/
		xmlHttp.open("POST", "cgi-local/sat.php", true);
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlHttp.setRequestHeader("Content-length", params.length);
		xmlHttp.setRequestHeader("Connection", "close");
		xmlHttp.onreadystatechange = handlerSubmitSequence;

		/* Mostra a mensagem de loading */
		/* var textoLoading = "<br/><br/><div class=\"labels\" style=\"text-align: center; font-weight: bold;\">Finding repeats... <br/><img src=\"/websat/imagens/loading.gif\" /></div>";*/
		var textoLoading = "<br/><br/><div class=\"labels\" style=\"text-align: center; font-weight: bold;\">Finding repeats... <br/><img src=\"imagens/loading.gif\" /></div>";
		
		document.getElementById("conteudoPrincipal").innerHTML = textoLoading;

		xmlHttp.send(params)
	}
}

function uploadSequence() {
	
	var form = document.forms[1];
	
	form.action = 'websat/upload.php';
	
	if (form.fileSeqUpload.value.length == 0) {
		alert("No file selected!");
		return false;
	}
	
	YAHOO.util.Connect.setForm(form.name, true);
  	YAHOO.util.Connect.asyncRequest('POST', form.action, callback);

  	progress_win = new YAHOO.widget.Panel("progress_win", { width:"420px", fixedcenter:true, underlay:"shadow", close:false, draggable:true, modal:true, effect:{effect:YAHOO.widget.ContainerEffect.FADE, duration:0.3} } );
  	progress_win.setHeader("WebSat: Uploading "+form.fileSeqUpload.value+" ...");
  	progress_win.setBody('<div id="ptxt" class="boxUpload" style="text-align: center;"> <img src="imagens/loading.gif" border="0" /> <br/> Uploading file... </div>');
  	progress_win.render(document.body);

  	return false;
}

function callLoadArquivo(nomeArquivo) {
	
	var form	= document.forms[0];
	var sequencia 	= "";	/* sera identificado via sessao */
	var params 		= "sequence=" + sequencia + "&seq=true&rnd=" + Math.random() + "&nomeArquivo=" + nomeArquivo;

	if (form.mono.checked) 
		if (form.mmono.value != "")
			params += "&mono=true&mmono=" + form.mmono.value;
		else {
			alert("Please, fill in \"MONO Repeat Minimum\"");
			return false;
		}
		
	if (form.di.checked) 
		if (form.mdi.value != "")
			params += "&di=true&mdi=" + form.mdi.value;
		else {
			alert("Please, fill in \"DI Repeat Minimum\"");
			return false;
		}
	
	if (form.tri.checked) 
		if (form.mtri.value != "")
			params += "&tri=true&mtri=" + form.mtri.value;
		else {
			alert("Please, fill in \"TRI Repeat Minimum\"");
			return false;
		}
	
	if (form.tetra.checked) 
		if (form.mtetra.value != "")
			params += "&tetra=true&mtetra=" + form.mtetra.value;
		else {
			alert("Please, fill in \"TETRA Repeat Minimum\"");
			return false;
		}
	
	if (form.penta.checked) 
		if (form.mpenta.value != "")
			params += "&penta=true&mpenta=" + form.mpenta.value;
		else {
			alert("Please, fill in \"PENTA Repeat Minimum\"");
			return false;
		}

	xmlHttp.open("POST", "cgi-local/sat.php", true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", params.length);
	xmlHttp.setRequestHeader("Connection", "close");
	xmlHttp.onreadystatechange = handlerSubmitSequence;

	/* Mostra a mensagem de loading */
	var textoLoading = "<br/><br/><div class=\"labels\" style=\"text-align: center; font-weight: bold;\">Finding repeats... <br/><img src=\"imagens/loading.gif\" /></div>";

	document.getElementById("conteudoPrincipal").innerHTML = textoLoading;

	xmlHttp.send(params)
}

var fN = function callBack(o) {

  	var resp = eval('(' + o.responseText + ')');
  
  	if (resp['total'] > 0) {
  	  	document.getElementById('ptxt').innerHTML = resp['total'] + " bytes uploaded!";
  	  	setTimeout("progress_win.hide(); callLoadArquivo('" + resp["nomeArquivo"] + "');", 1000);
  	}
  	else {
  		document.getElementById('ptxt').innerHTML = "<div class='boxUploadError'>Unable to upload file. \nPlease contact the admistrator.</div>";
  	  	setTimeout("progress_win.hide();", 2000);
  	}
}

var callback = { upload:fN }

function handlerSubmitSequence() {
	if (xmlHttp.readyState == 4) {
		var conteudo = xmlHttp.responseText;
		
		conteudo = conteudo.replace("/usr/bin/php", "");
		conteudo = conteudo.replace(/[!#]*/, "");
		conteudo = conteudo.replace(/!/, "");

		document.getElementById("conteudoPrincipal").innerHTML = conteudo;
	}
}

function loadHome() {
	var handler = function() {
		if (xmlHttp.readyState == 4) {
			document.getElementById("conteudoPrincipal").innerHTML = xmlHttp.responseText;
			document.forms[0].sequence.focus();
		}
	};
	
	xmlHttp.open("GET", "websat/home.php", true);
	xmlHttp.onreadystatechange = handler;
	xmlHttp.send(null);
}

function callPrimer(sessionSeqId, sequenceId, repeatParam) {
	
//	var loadingText = "<div id=\"loadPrimer\" style=\"text-align: center;\">Designing primers... <br/><img src=\"/websat/imagens/loading.gif\" /><br/></div>";
//	var primAntigo = document.getElementById("seq_" + sessionSeqId).innerHTML;
//	
//	document.getElementById("seq_" + sessionSeqId).innerHTML = loadingText + primAntigo;
	
  	progress_win = new YAHOO.widget.Panel("progress_win", { width:"420px", fixedcenter:true, underlay:"shadow", close:false, draggable:true, modal:true, effect:{effect:YAHOO.widget.ContainerEffect.FADE, duration:0.3} } );
  	progress_win.setHeader("WebSat");
  	progress_win.setBody('<div id="ptxt" class="boxUpload" style="text-align: center;"> <img src="imagens/loading.gif" border="0" /> <br/> Designing primers... </div>');
  	progress_win.render(document.body);
	progress_win.show();
	
	with (document.forms[0]) {
		var handler = function() {
			handlerSubmitSatellite(sessionSeqId);
		};
	
		var params = "sequence=" + sequenceId + "&repeat=" + repeatParam + "&sessionSeqId=" + sessionSeqId;
			params += "&PRIMER_MIN_SIZE=" + PRIMER_MIN_SIZE.value;
			params += "&PRIMER_OPT_SIZE=" + PRIMER_OPT_SIZE.value;
			params += "&PRIMER_MAX_SIZE=" + PRIMER_MAX_SIZE.value;
			params += "&PRIMER_MIN_TM=" + PRIMER_MIN_TM.value;
			params += "&PRIMER_OPT_TM=" + PRIMER_OPT_TM.value;
			params += "&PRIMER_MAX_TM=" + PRIMER_MAX_TM.value;
			params += "&PRIMER_MIN_GC=" + PRIMER_MIN_GC.value;
			params += "&PRIMER_MAX_GC=" + PRIMER_MAX_GC.value;
			params += "&PRIMER_PRODUCT_SIZE_RANGE=" + PRIMER_PRODUCT_SIZE_RANGE.value;
			params += "&PRIMER_MAX_DIFF_TM=" + PRIMER_MAX_DIFF_TM.value;
			params += "&PRIMER_MAX_END_STABILITY=" + PRIMER_MAX_END_STABILITY.value;
			params += "&PRIMER_SELF_ANY=" + PRIMER_SELF_ANY.value;
			params += "&PRIMER_NUM_NS_ACCEPTED=" + PRIMER_NUM_NS_ACCEPTED.value;
			params += "&PRIMER_SELF_END=" + PRIMER_SELF_END.value;
			params += "&PRIMER_MAX_POLY_X=" + PRIMER_MAX_POLY_X.value;

		xmlHttp.open("POST", "cgi-local/primer.php", true);
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlHttp.setRequestHeader("Content-length", params.length);
		xmlHttp.setRequestHeader("Connection", "close");
		xmlHttp.onreadystatechange = handler;

		xmlHttp.send(params);
	}
}

function handlerSubmitSatellite(sessionSeqId) {
	if (xmlHttp.readyState == 4) {
		var conteudo = xmlHttp.responseText;
		
		conteudo = conteudo.replace("/usr/bin/php", "");
		conteudo = conteudo.replace(/[!#]*/, "");

		document.getElementById("seq_" + sessionSeqId).innerHTML = conteudo;
		
		progress_win.hide();
	}
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function exportData() {
	window.open("cgi-local/export.php", "WEBSAT_EXPORT_POPUP", "");
}

function exportSSR() {
	window.open("cgi-local/exportSSR.php", "WEBSAT_EXPORT_SSR_POPUP", "");
}
