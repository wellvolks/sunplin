<?php
//#!/usr/bin/php
	@session_start();
	
	header("Content-type: text/plain");
	header("Content-Disposition: attachment; filename=websat-" . date("m-d-Y") . ".csv");

	// cabecalho do arquivo
	$SEQ_ID 		= 6;
	$SSR 			= 0;
	$FW_PRIMER 		= 1;
	$TMF 			= 2;
	$FW_INDEX 		= 7;
	$RV_PRIMER 		= 3;
	$TMR 			= 4;
	$RV_INDEX 		= 9;
	$PRODUCT_SIZE 		= 5;
	$sessionExportSSR	= $_SESSION["sessionExportSSR"];

	printf("SEQ-ID,SSR,PRODUCT-SIZE,FW-PRIMER,FW-LEN,TM,RV-PRIMER,RV-LEN,TM,SSR-INDEX,FW-INDEX,RV-INDEX\r\n");
	foreach ($_SESSION["export"] as $seq => $value) {
		$dados 		= $_SESSION["export"][$seq];
		$curseq 	= str_replace(",", "_", $dados[$SEQ_ID]);
		$FW_LEN 	= $dados[8]-$dados[7];			
		$RV_LEN 	= $dados[10]-$dados[9];
		$ssr_txt 	= substr($dados[$SSR], 0, strpos($dados[$SSR], ")"));
		$ssr_txt	= str_replace("(", "", $ssr_txt);
		$SSR_INDEX 	= $curseq . $ssr_txt;

		printf("%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\r\n", $curseq, $dados[$SSR], $dados[$PRODUCT_SIZE], $dados[$FW_PRIMER], $FW_LEN, $dados[$TMF], $dados[$RV_PRIMER], $RV_LEN, $dados[$TMR], $sessionExportSSR[$SSR_INDEX], $dados[$FW_INDEX], $dados[$RV_INDEX]);
	}
?>
