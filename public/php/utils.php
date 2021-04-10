
<?php
//!/usr/bin/php

//=-------------------------------------------------------------------------------------------------------=
	
//=-- Arquivo de configuracao do websat	
//=- V 1.0 - 02/10/08 00:51
//=-------------------------------------------------------------------------------------------------------=

	
// Diretorio de onde estao os arquivos php (cgi-local),no caso da locaweb), (com a ultima barra)
	
// Acesso via web, entÃ£o, nÃ£o precisa do diretÃ³rio completo, apenas o que Ã© visto atraves da porta 80
	

$DIRETORIO_CGI = "/cgi-local/websat/";
	

//$DIRETORIO_BASE = "/usr/local/apache2/htdocs/webtroll/";
//$DIRETORIO_BASE = "/home/wellington/public_html/websat/";
$DIRETORIO_BASE = "/home/wellington/tmpdirs/";

	
// Diretorio do TROLL (sem a ultima barra)
//$DIRETORIO_TROLL = "/usr/local/troll";
$DIRETORIO_TROLL = "/home/wellington/troll";


// Diretorio onde fica os motifs (sem a ultima barra)
//$DIRETORIO_MOTIFS = "/usr/local/apache2/htdocs/webtroll/troll";
$DIRETORIO_MOTIFS = "/home/wellington/troll";

	
// Diretorio temporario de upload de sequencias
//$DIRETORIO_SEQ_UPLOAD = "/var/www/html/websat/tmp/seqsUpds/";			
//$DIRETORIO_SEQ_UPLOAD = "/home/wellington/tmp/";
$DIRETORIO_SEQ_UPLOAD = "/home/wellington/tmpdirs/upldir/";
	

// Tamanho maximo do arquivo de upload de sequencia
	
$TAM_MAX_FILE_UPLOAD = 1024 * 150;	// 150 KB

	function printSequence($len, $seq, $beg, $sat, $end, $erep, $rep1, $curseq, $index, $sessionSeqId, $overlaped) {

		$index	= $index - 1;
		$rep 	= 0;
		$flag 	= "";
		$cont	= 0;
	
		echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border: 1px solid #000;">';
		echo '<tr>';
		echo '<td valign="top" class="sequencia" id="seq_' . $sessionSeqId . '"><div style="padding: 10px;"><strong>' . $curseq . '</strong><br/><pre>';

		# I dont like this... but this make the stuffs simple
		printf("%4d\t", 1);

		# inspect each caracter for a begin of a repeat
		for ($i=0; $i<$len; $i++) {
			$a = substr($seq, $i, 1);

			// Significa que vamos ter 70 caracteres por linha
		  	if ($cont == 70) {
				if ($flag != "")
					printf("</a><BR/>%4d\t%s", ($i+1), $flag);
				else
					printf("<BR/>%4d\t", ($i+1));
		    		$cont = 0;
		  	}
			else if (($cont % 10) == 0 && $cont) {
				// A cada 10 bases imprimirmos um espaço
				echo " ";
			}

			// Verificamos se existe um SSR overlaped iniciando em $i
			if ( isset($overlaped[$curseq . $i]) ) {
				$classe = "overlaped";
			}
			else {
				$classe = "satellite";
			}
		  	
		  	// Inicio de um novo microsatelite
		  	if ($i == $beg[$rep] && $rep < $rep1) { 
				$size = $end[$rep] - $beg[$rep];
				$size = $size / strlen($sat[$rep]);

				// se for um satelite sobreposto, ele deve ficar laranja

				$flag = "<a style='cursor: pointer;' onclick=\"callPrimer(" . $sessionSeqId . ", " . $index . ", " . $erep[$rep] . ");\" title=\"(" . $sat[$rep] . ")" . $size . "\" class=\"" . $classe . "\">";
		  		echo $flag;
			}
		  	
		  	// Fim de um microsatelite, verifica se Ã© inicio do proximo
		  	if ($i == $end[$rep] && $rep < $rep1) {
				$flag = "";
		    		echo "</a>";
		    		
		    		if ($end[$rep] > $beg[$rep+1] && $rep < $rep1-1) { 
		    			$flag = "<a title=\"" . $sat[$rep] . "\" class=\"" . $classe . "\">";
					echo $flag;
		    		}
		    
		    		$rep++;
		  	}
		  	
		  	echo "$a";
		  	$cont++;
		}
		
		echo '</pre></div></td>';
		echo '</tr>';
		echo '</table><br style="line-height: 30px;"/>';
	}

	function leArquivo($nomeArquivo, &$linhas) {
	
		$fp = fopen($nomeArquivo, "r");
		if ($fp != false) {
			while ( !feof($fp) ) {
				$l = str_replace("\n", "", fgets($fp));
				
				if ($l != "")
					$linhas[ count($linhas) ] = $l;
			}
			fclose($fp);
		}
	}
	
	function getSequenceParameter() {
	
		global $DIRETORIO_SEQ_UPLOAD, $TAM_MAX_FILE_UPLOAD, $nomeArquivo;
		
		if ($_POST['sequence'] != "") {
			$sequence = $_POST['sequence'];
		}
		else if ($nomeArquivo != "") {
			leArquivo($DIRETORIO_SEQ_UPLOAD . $nomeArquivo, $linhas);
			
			$sequence = implode("\n", $linhas);
		}
		else {
			// Arquivo muito grande
			if ($_FILES['fileSeqUpload']['size'] > $TAM_MAX_FILE_UPLOAD) {
				$_SESSION["msgFront"] = "Sequence file is to big.";
				header("location: index.php");
				exit(1);
			}
			
			// O tipo do arquivo deve ser txt
			if ( stristr($_FILES['fileSeqUpload']['type'], "text") == FALSE) {
				$_SESSION["msgFront"] = "The sequence file must be text.";
				header("location: index.php");
				exit(1);
			}
		
			// Faz a movimentacao do arquivo para a pasta final (tmp)
			if (move_uploaded_file($_FILES['fileSeqUpload']['tmp_name'], $DIRETORIO_SEQ_UPLOAD . $_FILES['fileSeqUpload']['name'])) {
				leArquivo($DIRETORIO_SEQ_UPLOAD . $_FILES['fileSeqUpload']['name'], $linhas);
				
				$sequence = implode("\n", $linhas);
			} 
			else {
				$_SESSION["msgFront"] = "Unable to upload the file.";
				header("location: index.php");
				exit(1);
			}
		}

		// Retira espaços e números da string
		//$sequence = preg_replace("/[0-9]*/", "", $sequence);
		//$sequence = preg_replace("/[^ACTGNactgn]*/", "", $sequence);

		// Valida os parametros informados pelo usuario
		if (strlen($sequence) == 0) {
			$_SESSION["msgFront"] = "No data included - submission rejected.";
		
			header("location: index.php");
		
			exit(1);
		}
		else if (strlen($sequence) > 153600) {
			$_SESSION["msgFront"] = "Too much data!";	
			header("location: index.php");
			exit(1);
		}

		return $sequence;
	}
	
	function correctInputSequence($pSequence) {
		
		$arrInput = explode("\n", $pSequence);
		$seqOutput = "";
		
		for ($i=0; $i<count($arrInput); $i++) {
			$arrInput[$i] = preg_replace("[\n\f\r]", "", $arrInput[$i]);
			
			if ( strpos($arrInput[$i], ">") === false )		// precisa ser === e nao ==
				$seqOutput .= "\n" . $arrInput[$i] . "\n";
			else
				$seqOutput .= $arrInput[$i];
		}
		
		return $seqOutput;
	}

	function prepareSequenceToPrimer($sequence, $begIndex, $endIndex, &$newBegin, &$newLen, &$desOri, $qtdDeslocar) {

		$auxBegin = $begIndex - $qtdDeslocar;
		$auxLen = $endIndex + $qtdDeslocar;
		$newLen = $endIndex - $begIndex + 7;

		if ($auxBegin < 0) {
			$auxBegin = 0;
			$newBegin = $begIndex;
		}
		else {
			$newBegin = $qtdDeslocar;		
		}

		if ($auxLen > strlen($sequence)) {
			$auxLen = strlen($sequence);
		}

		// Deslocamento feito na sequencia original
		$desOri = $auxBegin;

		return str_replace("\n", "", substr($sequence, $auxBegin, $auxLen-$auxBegin));	
	}


	function isBegin($seq, $i) {
		return $_SESSION[$seq . "beg" . $i] == true; 
	}

	function isEnd($seq, $i) {
		return $_SESSION[$seq . "end" . $i] == true;
	}

	function addBegin($seq, $i) {
		$ssrs = $_SESSION["ssrs"];
		$ssrs[count($ssrs)] = array($seq, $i);
		$_SESSION["ssrs"] = $ssrs;

		$_SESSION[$seq . "beg" . $i] = true;
	}

	function addEnd($seq, $i) {
		$ssrs = $_SESSION["ssrs"];
		$ssrs[count($ssrs)] = array($seq, $i);
		$_SESSION["ssrs"] = $ssrs;

		$_SESSION[$seq . "end" . $i] = true;
	}
?>
