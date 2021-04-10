<?php
//#!/usr/bin/php
	@session_start();
	
	header("Content-type: text/plain");
	header("Content-Disposition: attachment; filename=websat-ssr-" . date("m-d-Y") . ".csv");
	
	print_r($sessionExportSSR);
?>
