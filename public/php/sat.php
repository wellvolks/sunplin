
<?php
//#!/usr/bin/php
	// Inicializa a sessao do usuario
	@session_start();

	// Arquivo utilitario
	include("utils.php");

	// Seta o tamanho minimo do SSR e a quantidade minima de repeticoes
	$ssrMotifLen = array(true, true, false, false, false, false, false);
	$ssrMinimumRepeat = array(0, 0, 0, 0, 0, 0, 0);
	
	$ssrMotifLen[1] = isset($_POST["mono"]);
	$ssrMotifLen[2] = isset($_POST["di"]);
	$ssrMotifLen[3] = isset($_POST["tri"]);
	$ssrMotifLen[4] = isset($_POST["tetra"]);
	$ssrMotifLen[5] = isset($_POST["penta"]);
	$ssrMotifLen[6] = isset($_POST["hexa"]);

	$ssrMinimumRepeat[1] = $_POST["mmono"];
	$ssrMinimumRepeat[2] = $_POST["mdi"];
	$ssrMinimumRepeat[3] = $_POST["mtri"];
	$ssrMinimumRepeat[4] = $_POST["mtetra"];
	$ssrMinimumRepeat[5] = $_POST["mpenta"];
	$ssrMinimumRepeat[6] = $_POST["mhexa"];

	// Parametros recebidos via post da pagina principal
	$pMotifLen 	= 6;
	$pRepeatLen 	= 10;
	$nomeArquivo 	= $_POST["nomeArquivo"];
	
	// Retorna a sequencia informada, lida do arquivo de upload ou 
	//  do texto informado no textarea
	//$pSequence 	= getSequenceParameter($pSequence);
	$tSequence 	= getSequenceParameter();
//        $pSequence = $tSequence;
	$ipMaqVisitante	  = session_id();
	$seqsFileName = $DIRETORIO_BASE . "prodir/seqs_" . $ipMaqVisitante . ".tmp";


	// Junta uma sequencia entrada em cada linha
	$pSequence = correctInputSequence($tSequence);

	// Arquivo que contem todos os motifs do tamanho que 
	// o usuario solicitou
	$motifFileName 	= "motifs" . $pMotifLen . ".dat";

	// IP do usuario que esta nos visitando, e o nome da maquina
	$ipMaqVisitante	  = session_id();

	// Arquivo que contem as sequencias entradas pelo usuario
	$seqsFileName = $DIRETORIO_BASE . "prodir/seqs_" . $ipMaqVisitante . ".tmp";

	// Arquivo que sera gravado as repeticoes
	$repsFileName = $DIRETORIO_BASE . "prodir/repeats_" . $ipMaqVisitante . ".tmp";

	// Arquivo de sequencia	
	$seqFileName  = $DIRETORIO_BASE . "prodir/seq_" . $ipMaqVisitante . ".tmp";

	// Arquivo de resultados do troll	
	$resFileName  = $DIRETORIO_BASE . "prodir/result_" . $ipMaqVisitante . ".tmp";


	// Se a sequencia nao estiver no formato fasta, adiciona uma
	// linha de cabecalho
	if ( strpos($pSequence, ">") === false ) {	// Note os 3(=)
		$pSequence = ">Untitled sequence\n" . $pSequence;
	}
	
	// Capitaliza todas as letras da sequencia
	$pSequence = strtoupper($pSequence);

	// Gera um arquivo com a sequencia
	if ( $fpSeqs = fopen($seqsFileName, "w") ) {
		fwrite($fpSeqs, $pSequence, strlen($pSequence));
		
		fclose($fpSeqs);
	}
	
	// Le todas as linhas do arquivo de senquencia e coloca em line
	leArquivo($seqsFileName, $seqsLinhas);

	// Abre o arquivo de repeticoes, para gravacao
	$fpReps = fopen($repsFileName, "w");

	// Vamos ler todas as linhas do arquivo de senquencia entrado	
	$new 	 = 1;
	$index 	 = 0;
	$repeats = "";

	// Variavel de sessao onde iremos armazenar o resultado do processamento 
	// das sequencias
	$_SESSION["sessionOverlaped"] = array();
	$_SESSION["sessionOverlapedPrimer"] = array();
	$_SESSION["sessionSeqs"] = array();
	$_SESSION["sessionExportSSR"] = array();
	$_SESSION["export"] = array();

	$ssrs = $_SESSION["ssrs"];
	for ($cont = 0; $cont < count($ssrs); $cont++) {
		$_SESSION[$ssrs[$cont][0] . "beg" . $ssrs[$cont][1]] = false;
		$_SESSION[$ssrs[$cont][0] . "end" . $ssrs[$cont][1]] = false;
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>WEBSAT</title>
	<!-- PAGE JAVA SCRIPT -->
	<script type="text/javascript" src="js/websat.js"></script>
		
	<!-- PAGE CSS -->
	<link rel="stylesheet" href="css/websat.css" type="text/css"></style>	
</head>
<body onload="newXmlHttpRequest();">

<form name="repeats" action="<?= $DIRETORIO_CGI;?>primer.php" target="WEBSAT_PRIMER" method="POST" enctype="MULTIPART/form-data">
	<input type="hidden" name="sequence" value="" />
	<input type="hidden" name="repeat" value="" />

	
  <table width="620" border="0" align="center" cellpadding="0" cellspacing="0">
   		 <tr>
			<td height="15" align="center"></td>
		</tr>
		<tr>
		    <td align="center">
			<table width="100%" border="0" cellspacing="1" cellpadding="0">
                <tr align="left">
                    <td class="labels">Primer Size Min:</td>
                    <td><input name="PRIMER_MIN_SIZE" value="18" type="text" class="select" size="10" maxlength="10" /></td>
                    <td class="labels">Opt:</td>
                    <td><input name="PRIMER_OPT_SIZE" value="22" type="text" class="select" size="10" maxlength="10" /></td>
                    <td class="labels">Max:</td>
                    <td><input name="PRIMER_MAX_SIZE" value="27" type="text" class="select" size="10" maxlength="10" /></td>
                </tr>
                <tr align="left">
                    <td class="labels">Primer Tm Min:</td>
                    <td><input name="PRIMER_MIN_TM" value="57.0" type="text" class="select" size="10" maxlength="10" /></td>
                    <td class="labels">Opt:</td>
                    <td><input name="PRIMER_OPT_TM" value="60.0" type="text" class="select" size="10" maxlength="10" /></td>
                    <td class="labels">Max: </td>
                    <td><input name="PRIMER_MAX_TM" value="68.0" type="text" class="select" size="10" maxlength="10" /></td>
                </tr>
                <tr align="left">
                    <td class="labels">Primer GC% Min:</td>
                    <td><input name="PRIMER_MIN_GC" value="40.0" type="text" class="select" size="10" maxlength="10" /></td>
                    <td class="labels">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="labels">Max:</td>
                    <td><input name="PRIMER_MAX_GC" value="80.0" type="text" class="select" size="10" maxlength="10" /></td>
                </tr>
                <tr align="left">
                    <td width="23%" class="labels">Product Size:</td>
                    <td width="16%"><input name="PRIMER_PRODUCT_SIZE_RANGE" value="100-400" type="text" class="select" size="10" maxlength="10" />                    </td>
                    <td width="7%" class="labels">&nbsp;</td>
                    <td width="19%">&nbsp;</td>
                    <td width="16%" class="labels">&nbsp;</td>
                    <td width="19%">&nbsp;</td>
                </tr>
                <tr align="left">
                    <td class="labels">Max Tm Difference:</td>
                    <td><input name="PRIMER_MAX_DIFF_TM" value="1.00" type="text" class="select" size="10" maxlength="10" /></td>
                    <td class="labels">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="labels">Max 3' Stability: </td>
                    <td><input name="PRIMER_MAX_END_STABILITY" value="250" type="text" class="select" size="10" maxlength="10" /></td>
                </tr>
                <tr align="left">
                    <td class="labels">Max Self Compl:</td>
                    <td><input name="PRIMER_SELF_ANY" value="4.00" type="text" class="select" size="10" maxlength="10" /></td>
                    <td class="labels">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="labels">Max #N's: </td>
                    <td><input name="PRIMER_NUM_NS_ACCEPTED" value="0" type="text" class="select" size="10" maxlength="10" /></td>
                </tr>
                <tr align="left">
                    <td class="labels">Max 3' Self Compl: </td>
                    <td><input name="PRIMER_SELF_END" value="2.00" type="text" class="select" size="10" maxlength="10" /></td>
                    <td class="labels">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="labels">Max Poly-X:</td>
                    <td><input name="PRIMER_MAX_POLY_X" value="4" type="text" class="select" size="10" maxlength="10" /></td>
                </tr>
            </table></td>
		</tr>
		<tr class="labels">
		    <td height="50" align="left">
				<table width="100%" cellpadding="0" cellspacing="0">
					<tr height="30">
						<td width="38%" align="left">
							<input type="button" name="Button" value="Back" onclick="loadHome();" />						</td>
						<td width="46%" align="right">&nbsp;</td>
					  <td width="16%" align="right"><input type="button" name="Export" value="Export Data" onclick="exportData();" /></td>
					</tr>
					<tr height="30">
						<td align="left">Click on SSRs to design primers</td>
						<td colspan="2" align="right">Save all primers designed in this session to a CSV file</td>
				    </tr>
				</table>
		  </td>
	    </tr>
		<tr>
		    <td align="center">

<table border="0" align="center" width="400">
	<tr class="labels">
		<td width="10" class="satellite" style="text-decoration: none; cursor: normal;">&nbsp;</td>
		<td width="50">SSR</td>
		<td width="10" class="primer" style="text-decoration: none;">&nbsp;</td>
		<td width="50">Primer</td>
		<td width="10" class="primerSelected" style="text-decoration: none;">&nbsp;</td>
		<td width="130">Selected Primer</td>
		<td width="10" class="overlaped" style="text-decoration: none;">&nbsp;</td>
		<td width="130">Overlaped SSR</td>
	</tr>
</table>

<?php
/*
      echo "<p>The value of the variable is : " . $seqsFileName . "</p>";
      echo "<p>The value of the variable is : " . $tSequence . "</p>";
      echo "<p>The value of the variable is : " . $nomeArquivo . "</p>";
      echo "<p>The value of the variable is : " . $pSequence . "</p>";
*/
    ?>

<?php
	foreach ($seqsLinhas as $seqLinha) {		// ORIGINAL: foreach $item (@line)
		if ( preg_match("/>/", $seqLinha) ) {	// start of a new sequence
			if ($new == 1) {		// a new file should be created
	      			$fpSeq = fopen($seqFileName, "w");
	      			
	      			# write start of sequence
	      			fwrite($fpReps, "#,$index,");

	      			$curseq = $seqLinha;
	      			$new 	= 0;
	      			$index++;
	      			
	      			continue;
			}
			else {
				fclose($fpSeq);
	      			$index--;
	      			
	      			fwrite($fpReps, "$index\n"); # write end of sequence

	      			$index++;

				// Le todas as linhas do arquivo de senquencia e coloca em seqsLinhas
				unset($seqs);
				leArquivo($seqFileName, $seqs);
				$seq = implode("", $seqs);

				// Calcula o tamanho da sequencia entrada (sem quebras de linha)
				$len 	= strlen($seq);
				$cont 	= 0;
				
				# call TROLL
				# troll -M$motiflen -m$repeatlen /usr/local/apache2/htdocs/webtroll/troll/motifs.dat $seq_file > $result_file`;
				$comando = "$DIRETORIO_TROLL/troll -M$pMotifLen -m$pRepeatLen $DIRETORIO_MOTIFS/$motifFileName $seqFileName > $resFileName";

				exec( $comando );	// Executa o troll, o resultado é no arquivo
				
				// Vamos ler o resultado do troll
				unset($linhasRes);
				leArquivo($resFileName, $linhasRes);

				array_shift( $linhasRes );	// Retira primeira linha, cabecalho
				array_pop( $linhasRes );	// Retira ultima linha, rodape
				
				// Le o conteudo do arquivo de motifs
				unset($linhasMot);
				$fpMot = fopen($DIRETORIO_MOTIFS . "/" . $motifFileName, "r");
				while ( !feof($fpMot) ) {
					$l = str_replace("\n", "", fgets($fpMot));
					
					if ($l != "") {
						$linhasMot[$l] = count($linhasMot);
						//$linhasMot[ count($linhasMot) ] = $l;
					}
				}
				fclose($fpMot);
				
				unset($sat);
				unset($beg);
				unset($end);
				unset($erep);
				$rep1 	= 0;			# store begin and end of a repeat
				$sat[0] = '';			# armazena qual e a repeticao
				$beg[0] = -1;			# inicio de uma repeticao
				$end[0] = -1;			# fim de uma repeticao

				foreach ($linhasRes as $linhaRes) {
					if ( preg_match("/(\d+)\t(\w+)\t(\d+)/", $linhaRes, $matches) ) {
						$begVal = $matches[1] - 1;
						$endVal = $matches[1] - 1 + $matches[3];
						$satVal = $matches[2];
						$size = ($endVal - $begVal) / strlen($satVal);

						// Se o tamanho do SSR nao for de um tamanho selecionado
						if ($ssrMotifLen[ strlen($satVal) ] != true) continue;
						if ($ssrMinimumRepeat[ strlen($satVal) ] > $size) continue; // Se a quantidade de repeticoes do SSR for menor que o minimo entao nao o mostra

						$sat[$rep1] = $matches[2];
						$beg[$rep1] = $begVal; //$matches[1] - 1;
						$end[$rep1] = $endVal; //$matches[1] - 1 + $matches[3];
						
						$brep	     = $matches[1] - 1;
						$erep[$rep1] = $brep + $matches[3] - 1;
						
						$repeats .= "$sat[$rep1],$brep,$erep[$rep1]\n";
						$sessionExportSSR[$curseq . $sat[$rep1]] = $brep;
						
						$rep1++;
				  	}
				}

				// Precisamos ordenar, porque a impressao eh feita caracter a caracter
				// da esquerda para a direita
				array_multisort($beg, $sat, $end, $erep);

				// se duas sequencias se sobrepoem, entao ajustamos o inicio da segunda
				// para apos o termino da primeira.
				// gravamos na sessao o novo inicio do ssr, para que ele possa ser 
				// recuperado quando estiver desenhando os primers
				if (count($beg) >= 2) {
					for ($i=1; $i<count($beg); $i++) {
						if ($beg[$i] < $end[$i-1]) {
							$sessionOverlaped[$curseq . $sat[$i] . ($beg[$i]-1)] = $end[$i-1] + 1;
							$beg[$i] = $end[$i-1] + 1;
							$sessionOverlapedPrimer[$curseq . $beg[$i]] = $beg[$i];
						}
					}			
				}

				// Solicita imprimir a sequencia com os microsatellites
				// Grava na sessao os parametros passados
				$sessionSeqId = count($sessionSeqs);
				$sessionSeqs[$sessionSeqId] = array($len, $seq, $beg, $sat, $end, $erep, $rep1, $curseq, $index);
				printSequence($len, $seq, $beg, $sat, $end, $erep, $rep1, $curseq, $index, $sessionSeqId, $sessionOverlapedPrimer);
				
				fwrite($fpReps, "$repeats"); # write repeats
			      	$repeats = "";
			      	fwrite($fpReps, "#,$index,"); # write start of sequence
			      	
			      	$fpSeq = fopen($seqFileName, "w");
		      	
	      			$curseq = $seqLinha;
	      			$index++;
	      			
	      			continue;
			}
		}
		
  		fwrite($fpSeq, $seqLinha);
  		$index++;
	}
	
	# process last sequence
	fclose($fpSeq);
	$index--;
	fwrite($fpReps, "$index\n"); 	# write end of sequence

	// Le todas as linhas do arquivo de senquencia e coloca em seqsLinhas
	unset($seqs);
	leArquivo($seqFileName, $seqs);
	$seq = "";
	$seq = implode("", $seqs);

	// Calcula o tamanho da sequencia entrada (sem quebras de linha)
	$len 	= strlen($seq);
	$cont 	= 0;
	
	# call TROLL
	# troll -M$motiflen -m$repeatlen /usr/local/apache2/htdocs/webtroll/troll/motifs.dat $seq_file > $result_file`;
	$comando = "$DIRETORIO_TROLL/troll -M$pMotifLen -m$pRepeatLen $DIRETORIO_MOTIFS/$motifFileName $seqFileName > $resFileName";

	//echo $comando . "<br\>";
	exec( $comando );	// Executa o troll, o resultado é no arquivo
	
	// Vamos ler o resultado do troll
	unset($linhasRes);
	leArquivo($resFileName, $linhasRes);

	array_shift( $linhasRes );	// Retira primeira linha, cabecalho
	array_pop( $linhasRes );	// Retira ultima linha, rodape

	// Le o conteudo do arquivo de motifs
	$fpMot = fopen($DIRETORIO_MOTIFS . "/" . $motifFileName, "r");
	unset($linhasMot);
	while ( !feof($fpMot) ) {
		$l = str_replace("\n", "", fgets($fpMot));
		
		if ($l != "") {
			//$linhasMot[ count($linhasMot) ] = $l;
			$linhasMot[ $l ] = count($linhasMot);
		}
	}
	fclose($fpMot);

	$rep1 	= 0;			# store begin and end of a repeat
	unset($sat);
	unset($beg);
	unset($end);
	unset($erep);
	$sat[0] = '';			# armazena qual e a repeticao
	$beg[0] = -1;			# inicio de uma repeticao
	$end[0] = -1;			# fim de uma repeticao
	foreach ($linhasRes as $linhaRes) {
		//echo $linhaRes . "<br/>";

		if ( preg_match("/(\d+)\t(\w+)\t(\d+)/", $linhaRes, $matches) ) {
			$begVal = $matches[1] - 1;
			$endVal = $matches[1] - 1 + $matches[3];
			$satVal = $matches[2];
			$size = ($endVal - $begVal) / strlen($satVal);
			
			// Se o tamanho do SSR nao for de um tamanho selecionado
			if ($ssrMotifLen[ strlen($satVal) ] != true) continue;
			
			// Se a quantidade de repeticoes do SSR for menor que o minimo entao nao o mostra
			if ($ssrMinimumRepeat[ strlen($satVal) ] > $size) continue; 
			
			$sat[$rep1] = $matches[2];
			$beg[$rep1] = $begVal; //$matches[1] - 1;
			$end[$rep1] = $endVal; //$matches[1] - 1 + $matches[3];
			
			//echo $sat[$rep1] . " -- " . $beg[$rep1] . " -- " . $end[$rep1] . "<br/>";

			$brep	     = $matches[1] - 1;
			$erep[$rep1] = $brep + $matches[3] - 1;
			
			$repeats .= "$sat[$rep1],$brep,$erep[$rep1]\n";
			$sessionExportSSR[$curseq . $sat[$rep1]] = $brep;

			$rep1++;
	  	}
	}

	// Precisamos ordenar, porque a impressao eh feita caracter a caracter
	// da esquerda para a direita
	array_multisort($beg, $sat, $end, $erep);

	// se duas sequencias se sobrepoem, entao ajustamos o inicio da segunda
	// para apos o termino da primeira.
	// gravamos na sessao o novo inicio do ssr, para que ele possa ser 
	// recuperado quando estiver desenhando os primers
	if (count($beg) >= 2) {
		for ($i=1; $i<count($beg); $i++) {
			if ($beg[$i] < $end[$i-1]) {
				$sessionOverlaped[$curseq . $sat[$i] . ($beg[$i]-1)] = $end[$i-1] + 1;
				$beg[$i] = $end[$i-1] + 1;
				$sessionOverlapedPrimer[$curseq . $beg[$i]] = $beg[$i];
			}
		}
	}

	// Solicita imprimir a sequencia com os microsatellites
	// Grava na sessao os parametros passados
	$sessionSeqId = count($sessionSeqs);
	$sessionSeqs[$sessionSeqId] = array($len, $seq, $beg, $sat, $end, $erep, $rep1, $curseq, $index+1);
	printSequence($len, $seq, $beg, $sat, $end, $erep, $rep1, $curseq, $index+1, $sessionSeqId, $sessionOverlapedPrimer);
	
	fwrite($fpReps, "$repeats"); # write repeats
	@fclose($fpReps);

	$_SESSION["sessionOverlaped"] = $sessionOverlaped;
	$_SESSION["sessionOverlapedPrimer"] = $sessionOverlapedPrimer;
	$_SESSION["sessionSeqs"] = $sessionSeqs;
	$_SESSION["sessionExportSSR"] = $sessionExportSSR;
?>		    
		    
           </td>
	</tr>
	<tr>
	    <td align="center">&nbsp;</td>
	</tr>
	</table>
</form>

</body>
</html>
