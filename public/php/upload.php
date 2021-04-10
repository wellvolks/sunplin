<?php
	session_start();

	//$DIRETORIO_SEQ_UPLOAD = "/home/wellington/tmp/";
	$DIRETORIO_SEQ_UPLOAD = "/home/wellington/tmpdirs/upldir/";

        mkdir($DIRETORIO_SEQ_UPLOAD);
	
	if($_SERVER['REQUEST_METHOD']=='POST') {
		$uploaded = move_uploaded_file($_FILES['fileSeqUpload']['tmp_name'], $DIRETORIO_SEQ_UPLOAD . $_FILES['fileSeqUpload']['name']);

		chmod($DIRETORIO_SEQ_UPLOAD . $_FILES['fileSeqUpload']['name'], 0644);
		
		if ($uploaded) {
			$nomeArquivo = $_FILES["fileSeqUpload"]["name"];
			echo '{"total":' . $_FILES["fileSeqUpload"]["size"] . ', "nomeArquivo": "' . $nomeArquivo . '"}';
		}
		else {
			echo '{"total":0}';
		}
	}
?>
