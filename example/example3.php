<?php
/*

	csv2hash.class.php
	EXAMPLE 3

*/
require_once(dirname(__DIR__).'/src/csv2hash.class.php');
$sCsvfile='file1.csv';

// ----------------------------------------------------------------------
// MAIN
// ----------------------------------------------------------------------

$oC2H=new csv2hash();

$oC2H->setHeaderSplitter('.');
$oC2H->setCsvFile($sCsvfile);

echo '----- all data headers with . are splitted into subkeys: see "flag"'.PHP_EOL
	.print_r($oC2H->getData(),1)
	.PHP_EOL
	;

// ----------------------------------------------------------------------
