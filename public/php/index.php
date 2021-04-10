<?php
	session_start();

	$path1 = "../../tmpdirs/upldir/";
	removeArquivos($path1);
	$path2 = "../../tmpdirs/prodir/";
	removeArquivos($path2);

	function removeArquivos($path) {
		//$path = "tmp/";
		$diretorio = dir($path);

		//echo "Diretorio ".$path.":<br><br>";

		while ($arquivo = $diretorio->read())
		{
			$dataArq = filemtime($path . $arquivo);

			if ( date("Y", $dataArq) < date("Y") ||
			     date("m", $dataArq) < date("m") ||
			     //date("d", $dataArq) <= (date("d")-2)) {
			     date("d", $dataArq) <= (date("d")-1)) {

				unlink($path . $arquivo);

				//echo $path . $arquivo . " deletado!<br/>";
			}
			//else {
			//	echo $arquivo . "   " . date("d", $dataArq) . "/" . date("m", $dataArq) . "/" . date("Y", $dataArq) . "<br/>";
			//}
		}
		$diretorio->close();
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="KeyWords" content="bioinformatics ssr tandem repeat microsatellite microsatelite websat bioinformatica bioinformática biologia computacional computational biology" />
	<title>WebSat</title>
	
	<!-- PAGE JAVA SCRIPT -->	
	<script type="text/javascript" src="js/websat.js"></script>	
	<script type="text/javascript" src="yui/yahoo.js"></script>	
	<script type="text/javascript" src="yui/event.js"></script>	
	<script type="text/javascript" src="yui/dom.js"></script>	
	<script type="text/javascript" src="yui/animation.js"></script>	
	<script type="text/javascript" src="yui/dragdrop.js"></script>	
	<script type="text/javascript" src="yui/connection.js"></script>	
	<script type="text/javascript" src="yui/container.js"></script>		
    
    <!-- PAGE CSS -->	
    <link rel="stylesheet" type="text/css" href="yui/container.css" />	
    <link rel="stylesheet" href="css/websat.css" type="text/css"></style>
</head>
<body onload="newXmlHttpRequest(); loadHome(); MM_preloadImages('imagens/loading.gif');">

<div style="text-align: center">
<script type="text/javascript"><!--
google_ad_client = "pub-6446130225099366";
/* WebSat, 728x90, created 7/13/10 */
google_ad_slot = "6929458622";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>	
<br/>
	<table width="622" border="0" align="center" cellpadding="0" cellspacing="0" style="border: 1px double #040204;">
	    <tr>
            <td align="center" bgcolor="#9CCEFC"><a href="http://finder.sourceforge.net/" target="_blank"><img src="imagens/banner_websat.JPG" alt="WebSat"  width="633" height="60" border="0" title="WebSat" /></a></td>
      </tr>
	    <tr>
	        <td align="center">
			<div id="conteudoPrincipal" style="padding: 10px; text-align: left;">			</div>			</td>
        </tr>
	    <tr>
		    <td height="50" align="center" class="rodape">      
	      <p><span xmlns:dc="http://purl.org/dc/elements/1.1/" property="dc:title">
          <strong>Please cite:</strong> Martins WS, Lucas DCS, Neves KFS, Bertioli DJ, <br />
WebSat - A Web
Software for MicroSatellite Marker Development, <br />
Bioinformation 2009,
3(6):282-283. <a href="http://www.ncbi.nlm.nih.gov/pubmed/19255650" target="_blank">[PubMed]</a><br />
<br/>
          WebSat is under the terms of the </span><a href="http://creativecommons.org/licenses/by-nc/3.0/" target="_blank" rel="license">Creative Commons Attribution Noncommercial 2.5 License</a>.<br />
          You can <a href="http://sourceforge.net/projects/satfinder/" target="_blank">click here</a> and download the source code.<br />
          This service uses the <a href="http://primer3.sourceforge.net/" target="_blank">Primer3</a> 
	      (Whitehead Institute) software.</p></td>
      </tr>
    </table>
<br/><br/>	

<?php 
	if ($_SESSION["msgFront"] != "") { 
?>
	<script type="text/javascript">
		alert('<?= $_SESSION["msgFront"]; ?>');
	</script>
<?php 
		$_SESSION["msgFront"] = "";
	} 
?>

<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
	try {
		var pageTracker = _gat._getTracker("UA-684450-4");
		pageTracker._trackPageview();
	} 
	catch(err) {}
</script>
</body>
</html>
