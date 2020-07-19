<?php
/*

	csv2hash.class.php
	EXAMPLE 2

*/
require_once(dirname(__DIR__).'/src/csv2hash.class.php');
$sCsvfile='file1.csv';

// ----------------------------------------------------------------------
// MAIN
// ----------------------------------------------------------------------

$oC2H=new csv2hash();

$oC2H->setKeyColumn(2);
$oC2H->setCsvFile($sCsvfile);

$aFilter=array(
	'label'=>array('matches', '/^o/'),
);
echo '----- filter data:'.PHP_EOL
	.print_r($aFilter,1).PHP_EOL
	.'----- data:'.PHP_EOL
	.print_r($oC2H->getData($aFilter), 1);

// ----------------------------------------------------------------------
