
<?php
//#!/usr/bin/php
	// Inicializa a sessao do usuario
	session_start();

	// Arquivo utilitario
	include("utils.php");

	// IP do usuario que esta nos visitando, e o nome da maquina
	$ipMaqVisitante	  = session_id();

	// Arquivo que sera gravado as repeticoes
	$repsFileName = $DIRETORIO_BASE . "prodir/repeats_$ipMaqVisitante.tmp";

	// Arquivo que contem as sequencias entradas pelo usuario
	$seqsFileName = $DIRETORIO_BASE . "prodir/seqs_$ipMaqVisitante.tmp";
	
	// PRIMER3 input file
	$p3in_file = $DIRETORIO_BASE . "prodir/p3in_$ipMaqVisitante.tmp";

	// PRIMER3 output file
	$p3out_file = $DIRETORIO_BASE . "prodir/p3out_$ipMaqVisitante.tmp";
	
	// Comando de chamada ao primer3_core
  	$comando 	= "$DIRETORIO_TROLL/primer3_core <";
	$comando	.= $comando . " $p3in_file > " . $p3out_file;

	// Dados do primer desta pagina
	$dadosPrimer	= array();


	// Parametros recebidos via form
	$pRepeat 		= $_POST["repeat"];
	$pIdSequence 	= $_POST["sequence"];

	// Le todas as linhas do arquivo que contem as sequencias entradas
	leArquivo($seqsFileName, $seqsLinhas);

	// Le todas as linhas do arquivo que contem as repeticoes
	leArquivo($repsFileName, $repsLinhas);
	
	// Encontra a posicao inicial da sequencia informada	
	foreach ($repsLinhas as $repLinha) {
		if ( preg_match("/#,(\d+),(\d+)/", $repLinha, $matches) ) {
     			if ($matches[2] == $pIdSequence) {
       				$begSequence = $matches[1];
       				break;
     			}
  		}
	}

	// Obtem o nome e a sequencia que foi pedida para ser analisada
	$count 		= 0;
	$curSequence 	= "";
	$curSeqName 	= "";
	foreach ($seqsLinhas as $seqLinha) {
		if ($count == $begSequence) {
	  		$curSeqName = $seqLinha;
	  	}
	  
	  	if (($count > $begSequence) && ($count <= $pIdSequence)) {
	    		$curSequence .= $seqLinha;
	  	}
	  
	  	$count++;
	}

	$rep1 	= 0;
	$ind 	= 0;
	$indAux = 0;
	$mot[0] = "";
	$beg[0] = -1;
	$end[0] = -1;

	foreach ($repsLinhas as $repLinha) {
		// echo $repLinha . "<br/>";
		if ( preg_match("/([ACGTNX]+),(\d+),(\d+)/", $repLinha, $matches) ) {
	        	if ($matches[3] == $pRepeat) {
	           		$mot[$rep1] = $matches[1];
	           		$beg[$rep1] = $matches[2];
	           		$end[$rep1] = $matches[3];

				$sessionOverlaped = $_SESSION["sessionOverlaped"];
				$overlapPos = ($curSeqName . $mot[$rep1] . ($beg[$rep1] - 1));
				if ( isset($sessionOverlaped[$overlapPos]) ) {
					//echo "entrou";
					$beg[$rep1] = $sessionOverlaped[$overlapPos];
					//echo $rep1 . " -> " . $beg[$rep1];
				}

	           		$rep1++;
		   		$ind++;
		   		
		   		$j = $rep1-1;
	        	}
	     	}
	}

	/*
	$beg[0] = 50;
	echo "Beg: ";
	print_r($beg);

	echo "<br/>End: ";
	print_r($end);
	*/

	$contm 	= 0;
	$rep 	= 0;

	$fpPrimer = fopen($p3in_file, "w");
	
	$curSeqName = trim($curSeqName);
	$rangePartes = explode("-", $_POST["PRIMER_PRODUCT_SIZE_RANGE"]);
	$primerSequence = prepareSequenceToPrimer($curSequence, $beg[$j], $end[$j], $newBeg, $newLen, $deslSeq, $rangePartes[1]) . "\n";

	$textoPrimer = "PRIMER_SEQUENCE_ID=$curSeqName\n";
	$textoPrimer .= "SEQUENCE=" . $primerSequence;
	$textoPrimer .= ("TARGET=" . $newBeg . "," . $newLen . "\n");
	$textoPrimer .= "PRIMER_PRODUCT_SIZE_RANGE=" . $_POST['PRIMER_PRODUCT_SIZE_RANGE'] . "\n";
	$textoPrimer .= "PRIMER_MAX_END_STABILITY=" . $_POST['PRIMER_MAX_END_STABILITY'] . "\n";
	$textoPrimer .= "PRIMER_MIN_SIZE=" . $_POST['PRIMER_MIN_SIZE'] . "\n";
	$textoPrimer .= "PRIMER_OPT_SIZE=" . $_POST['PRIMER_OPT_SIZE'] . "\n";
	$textoPrimer .= "PRIMER_MAX_SIZE=" . $_POST['PRIMER_MAX_SIZE'] . "\n";
	$textoPrimer .= "PRIMER_MIN_TM=" . $_POST['PRIMER_MIN_TM'] . "\n";
	$textoPrimer .= "PRIMER_OPT_TM=" . $_POST['PRIMER_OPT_TM'] . "\n";
	$textoPrimer .= "PRIMER_MAX_TM=" . $_POST['PRIMER_MAX_TM'] . "\n";
	$textoPrimer .= "PRIMER_MAX_DIFF_TM=" . $_POST['PRIMER_MAX_DIFF_TM'] . "\n";
	$textoPrimer .= "PRIMER_MIN_GC=" . $_POST['PRIMER_MIN_GC'] . "\n";
	$textoPrimer .= "PRIMER_MAX_GC=" . $_POST['PRIMER_MAX_GC'] . "\n";
	$textoPrimer .= "PRIMER_SELF_ANY=" . $_POST['PRIMER_SELF_ANY'] . "\n";
	$textoPrimer .= "PRIMER_SELF_END=" . $_POST['PRIMER_SELF_END'] . "\n";
	$textoPrimer .= "PRIMER_NUM_NS_ACCEPTED=" . $_POST['PRIMER_NUM_NS_ACCEPTED'] . "\n";
	$textoPrimer .= "PRIMER_MAX_POLY_X=" . $_POST['PRIMER_MAX_POLY_X'] . "\n";
	$textoPrimer .= "PRIMER_NUM_RETURN=1\n";
	$textoPrimer .= "=\n";

	fwrite($fpPrimer, $textoPrimer);
	
	fclose($fpPrimer);
	
	// Executa o primer3_core, o resultado Ã© no arquivo
	exec( $comando );

	// Le a saida do primer3_core
	leArquivo($p3out_file, $p3outLinhas);

	// Inicialmente sem erros
	$error_flag = false;

	// Iteramos na saida do primer
	if ($pOut = implode("\n", $p3outLinhas)) {
		$sub = 	($end[$contm]-$beg[$contm]+1)/strlen($mot[$contm]);

		$dadosPrimer[0] = $mot[$contm];
		$dadosPrimer[1] = $sub;

		$contm++;
		if ( preg_match("/PRIMER_LEFT_SEQUENCE=(.*)/", $pOut, $matches) ) {
			$dadosPrimer[2] = $matches[1];
     	
			preg_match("/PRIMER_LEFT=(\d+),(\d+)/", $pOut, $matches2);
     			$bpleft = $matches2[1] + $deslSeq;
     			$epleft = $matches2[1] + $deslSeq + $matches2[2] - 1;

			preg_match("/PRIMER_RIGHT=(\d+),(\d+)/", $pOut, $matches3);
			$bpright = $matches3[1] + $deslSeq - $matches3[2] + 1;
     			$epright = $matches3[1] + $deslSeq;
     			
     			// Obtem o tm da sequencia da esquerda
     			preg_match("/PRIMER_LEFT_TM=(.*)/", $pOut, $matches4);
     			$dadosPrimer[3] = $matches4[1];
     		}

		if ( preg_match("/PRIMER_RIGHT_SEQUENCE=(.*)/", $pOut, $matches) ) {
			$dadosPrimer[4] = $matches[1];
     			print "</center></table>";
     	
		    	preg_match("/PRIMER_LEFT=(\d+),(\d+)/", $pOut, $matches2);
     			$bpleft = $matches2[1] + $deslSeq;
     			$epleft = $matches2[1] + $deslSeq + $matches2[2] - 1;

		    	preg_match("/PRIMER_RIGHT=(\d+),(\d+)/", $pOut, $matches3);
     			$bpright = $matches3[1] + $deslSeq - $matches3[2] + 1;
     			$epright = $matches3[1] + $deslSeq;

     			// Obtem o tm da sequencia da direita
     			preg_match("/PRIMER_RIGHT_TM=(.*)/", $pOut, $matches4);
     			$dadosPrimer[5] = $matches4[1];
     		}

		// Obtem product size utilizado
		preg_match("/PRIMER_PRODUCT_SIZE=(.*)/", $pOut, $matches);
		$dadosPrimer[6] = $matches[1];
	}

	// Verificamos se ocorreu algum erro
	if ( $dadosPrimer[2] == ""  &&  $dadosPrimer[4] == "" ) {
		$error_flag = true;
	}

	
   	$flag1 	= 0;
   	$flag2 	= 0;
   	$cont 	= 0;
   	$len 	= strlen($curSequence);

	$dadosPrimer[7] = $curSeqName;

	/* Obter dados do satellite, gravados na sessao */
	$sessionSeqs 	 = $_SESSION["sessionSeqs"];
	$sessionSeqId 	 = $_POST["sessionSeqId"];
	$sessionSeqDados = $sessionSeqs[$sessionSeqId];
	$sessionOverlapedPrimer = $_SESSION["sessionOverlapedPrimer"];
	
	$begSat		 = $sessionSeqDados[2];
	$repSat		 = 0;
	$rep1Sat	 = $sessionSeqDados[6];
	$indexSat	 = $sessionSeqDados[8];
	$erepSat	 = $sessionSeqDados[5];
	$satSat		 = $sessionSeqDados[3];
	$endSat		 = $sessionSeqDados[4];
	$flag		 = "";		/* Para SSR gerais */
	$flag2		 = "";		/* Para Primers */
	$flag3		 = "";		/* Para o SSR clicado */
	$iPrimerSelect   = -1;		/* indice do SSR clicado */

	# I dont like this... but this make the stuffs simple
	$auxPf = sprintf("%4d\t", 1);
	$dadosPrimer[8] .= $auxPf;

	for ($i=0; $i<$len; $i++) {
		$a = substr($curSequence, $i, 1);
		$sel = false;	// Seta que a posicao atual nao inicia o SSR clicado

		if ($cont == 70) {
			$auxPf = "";
			#$auxPf = "</span></span></a>";
			if ($flag2 != "") $auxPf .= "</span>";
			if ($flag3 != "") $auxPf .= "</span>";
			if ($flag != "") $auxPf .= "</a>";

			$auxPf .= sprintf("<BR/>%4d\t", ($i+1));

			if ($flag2 != "") $auxPf .= $flag2;
			if ($flag != "") $auxPf .= $flag;

			$cont = 0;

			$dadosPrimer[8] .= $auxPf;
		}
		else if (($cont % 10) == 0 && $cont) {
			// A cada 10 bases imprimirmos um espaço
			$dadosPrimer[8] .= " ";
		}

		/* DADOS DE PRIMER */
		if ($i == $beg[$rep] && $rep < $rep1) {
			$flag3 = "<span class=\"primerSelected\">";
			$dadosPrimer[8] .= $flag3;
			$sel = true;	// informa que aqui inicia o SSR clicado
			$iPrimerSelect = $i;
		}
		
		if ($i == ($end[$rep]+1) && $rep < $rep1) {
			addEnd($sessionSeqId, $i);
			$dadosPrimer[8] .= "</span>";
			$flag3 = "";
		}

		if ($i == $bpleft && !$error_flag) {
			$flag2 = "<span class=\"primer\">";
			$dadosPrimer[8] .= $flag2;
		}
		
		if ($i == $epleft + 1) {
			$flag2 = "";
			$dadosPrimer[8] .= "</span>";
		}
		
		if ($i == $bpright && !$error_flag) {
			$flag2 = "<span class=\"primer\">";
			$dadosPrimer[8] .= $flag2; 
		}
		
		if ($i == $epright + 1) {
			$flag2 = "";
			$dadosPrimer[8] .= "</span>";
		}

		// Verificamos se existe um primer (overlaped) que esta iniciando em $i
		if (isset($sessionOverlapedPrimer[$curSeqName . $i])) {
			$classe = "overlaped";
		}
		else {
			$classe = "satellite";
		}

		/* DADOS DE MICROSATELLITE */
	  	// Inicio de um novo microsatelite
	  	if ($i == $begSat[$repSat] && $repSat < $rep1Sat) { 
			$size = $endSat[$repSat] - $begSat[$repSat];
			$size = $size / strlen($satSat[$repSat]);
	
			if ($sel) {
				if ($flag2 != "") $flag .= "</span>";
				$flag .= "<a style='cursor: pointer;' class=\"primerSelected\" onclick=\"callPrimer(" . $sessionSeqId . ", " . ($indexSat-1) . ", " . $erepSat[$repSat] . ");\" title=\"(" . $satSat[$repSat] . ")". $size ."\" >";
				$flag .= $flag2;
				$iPrimerSelect = $i;
			}
			else {
				$flag = "<a style='cursor: pointer;' onclick=\"callPrimer(" . $sessionSeqId . ", " . ($indexSat-1) . ", " . $erepSat[$repSat] . ");\" title=\"(" . $satSat[$repSat] . ")". $size ."\" class=\"". $classe  . "\">";
	  		}

			$dadosPrimer[8] .= $flag;
		}
	  	
	  	// Fim de um microsatelite, verifica se nao inicio do proximo
	  	if ($i == $endSat[$repSat] && $repSat < $rep1Sat) {
			$flag = "";
	    		$dadosPrimer[8] .= "</a>";
	    		
	    		if ($endSat[$repSat] > $begSat[$repSat+1] && $repSat < $rep1Sat-1) { 
	    			$flag = "<a title=\"" . $satSat[$repSat] . "\" class=\"" . $classe . "\">";
	    			$dadosPrimer[8] .= $flag;
			}
	    
	    		$repSat++;
	  	}

		$dadosPrimer[8] .= "$a";
		$cont++;
   	}

	//=--------------------------------------------
	//=- Persiste os dados selecionados na sessao
	if (!$error_flag) {
		//session_register("export");
		//$_SESSION["export"] =  $GLOBALS["export"];
		$exp = $_SESSION["export"];
	
		$novo = array("(" . $dadosPrimer[0] . ")" . $dadosPrimer[1], $dadosPrimer[2], $dadosPrimer[3], $dadosPrimer[4], $dadosPrimer[5], $dadosPrimer[6], $dadosPrimer[7], $bpleft+1, $epleft+2, $bpright+1, $epright+2);
	
		$exp[$dadosPrimer[7] . $dadosPrimer[0] . $dadosPrimer[1] . $iPrimerSelect] = $novo;
	
		$_SESSION["export"] = $exp;
	}
?>

<html>
	<head>
		<title>Primers</title>
	    <link href="css/websat.css" rel="stylesheet" type="text/css" />
	</head>
	
	<body>
		<table width="100%" align="center" cellpadding="0" cellspacing="0" bordercolor="#000000" bgcolor="#FFFFFF" class="tabelaPrimer">
			<tr>
			    <td colspan="2" bordercolor="#FFFFFF" class="sequencia">&nbsp;</td>
		    	</tr>
			<tr>
				<td colspan="2" bordercolor="#FFFFFF">			
						<?php if (!$error_flag) {	// if have no erros ?>
							<table width="98%" align="center" cellpadding="0" cellspacing="0" border="0" class="tabelaPrimer">
			                    <tr>
			                        <td width="20%" height="25" align="left" bgcolor="#00FFFF" class="tituloColunaPrimer">&nbsp;Forward Primer</td>
			                        <td width="35%" height="25" align="left" bgcolor="#8CFFFF" class="labels">&nbsp;<?php echo $dadosPrimer[2]; ?></td>
			                        <td width="10%" height="25" align="left" bgcolor="#00FFFF" class="tituloColunaPrimer">&nbsp;Tm (&deg;C)</td>
			                        <td width="10%" height="25" align="center" bgcolor="#8CFFFF" class="labels"><?php echo $dadosPrimer[3]; ?></td>
			                        <td width="35%" height="25" align="center" bgcolor="#00FFFF" class="tituloColunaPrimer" style="border-left: 1px solid #000;">&nbsp;Product Size (bsp)</td>
			                    </tr>
			                    <tr>
			                        <td height="25" align="left" bgcolor="#00FFFF" class="tituloColunaPrimer">&nbsp;Reverse Primer</td>
			                        <td height="25" align="left" bgcolor="#8CFFFF" class="labels">&nbsp;<?php echo $dadosPrimer[4]; ?></td>
			                        <td height="25" align="left" bgcolor="#00FFFF" class="tituloColunaPrimer">&nbsp;Tm (&deg;C)</td>
			                        <td height="25" align="center" bgcolor="#8CFFFF" class="labels"><?php echo $dadosPrimer[5]; ?></td>
			                        <td height="25" align="center" bgcolor="#8CFFFF" class="labels" style="border-left: 1px solid #000;">&nbsp;<?php echo $dadosPrimer[6]; ?></td>
			                    </tr>
	                		</table>
						<?php } else { ?>
						  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="primerError">
							<tr>
								
          <td>&nbsp;Primer Error: Please, change the top parameters and try 
            again.</td>
							</tr>
						  </table>
						 <?php } ?>
	  			</td>
			</tr>
			<tr>
			    <td colspan="2" bordercolor="#FFFFFF" class="sequencia">&nbsp;</td>
		    </tr>
			<tr>
				<td colspan="2" bordercolor="#FFFFFF" style="padding: 10px;">
					<span class="sequencia"><strong><?php echo $dadosPrimer[7]; ?></strong></span>
<pre>
<?php echo $dadosPrimer[8]; ?>
</pre>
				</td>
			</tr>
	</table>

	</body>
</html>
