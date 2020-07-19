<?php
/*

	TEST SCRIPT FOR csv2hash.class.php

*/
require_once(dirname(__DIR__).'/src/csv2hash.class.php');
$sCsvfile=dirname(__DIR__).'/example/file1.csv';

$aResult=array('ok'=>array(), 'error'=>array());

// ----------------------------------------------------------------------
// FUNCTIONS
// ----------------------------------------------------------------------

function checkResult($value, $expected, $sMsgOk, $sMsgError){
	global $aResult;
	if($value===$expected){
		$aResult['ok'][]=$sMsgOk;
	} else {
		$aResult['error'][]=$sMsgError.' - FOUND:'.$value.' - EXPECTED '.$expected;
	}
	return true;
}

// ----------------------------------------------------------------------
// MAIN
// ----------------------------------------------------------------------

$oC2H=new csv2hash();

$result=$oC2H->setCsvFile('nonexisting-file.txt');
checkResult($result,false,
	'setCsvFile - Non existing file was detected.',
	'setCsvFile - Non existing file was NOT detected.',
);

$result=$oC2H->setCsvFile($sCsvfile);
checkResult($result,true,
	'setCsvFile - Existing file was detected.',
	'setCsvFile - Existing file was NOT detected.',
);

$result=$oC2H->getData();
checkResult(count($result),3,
	count($result).' records were read',
	count($result).' records were read',
);
checkResult(isset($result[0]['flag.x']),true,
	'flat structure was found: key flag.x exists',
	'flat structure was not found: key flag.x does not exist',
);

$oC2H->setHeaderSplitter('.');
$result=$oC2H->getData();
checkResult(isset($result[0]['flag']['x']),true,
	'setHeaderSplitter - nested structure was found: structure [flag][x] exists',
	'setHeaderSplitter - nested structure was not found: structur [flag][x] does not exist',
);
	

$aFilter=array(
	'label'=>array('matches', '/^t/'),
);
$result=$oC2H->getData($aFilter);
checkResult(count($result),2,
	count($result).' record(s) were read with "matches" filter.',
	count($result).' record(s) were read with "matches" filter.',
);


$aFilter=array(
	'label'=>array('is', 'one'),
);
$result=$oC2H->getData($aFilter);
checkResult(count($result),1,
	count($result).' record(s) were read with "is" filter.',
	count($result).' record(s) were read with "is" filter.',
);

$aFilter=array(
	'hide'=>array('not', 1),
);
$result=$oC2H->getData($aFilter);
checkResult(count($result),2,
	count($result).' record(s) were read with "not" filter.',
	count($result).' record(s) were read with "not" filter.',
);


$aFilter=array(
	'label'=>array('matches', '/^th/'),
	'hide'=>array('is', "0"),
);
$result=$oC2H->getData($aFilter, 'and');
checkResult(count($result),1,
	count($result).' record(s) were read with 2 filters and AND.',
	count($result).' record(s) were read with 2 filters and AND.',
);


$aFilter=array(
	'label'=>array('matches', '/^th/'),
	'hide'=>array('is', "1"),
);
$result=$oC2H->getData($aFilter, 'or');
checkResult(count($result),2,
	count($result).' record(s) were read with 2 filters and OR.',
	count($result).' record(s) were read with 2 filters and OR.',
);


// ----------------------------------------------------------------------
// RESULT
// ----------------------------------------------------------------------

echo '------ TEST RESULTS:'.PHP_EOL; print_r($aResult);
echo '------ SUMMARY OF THE TEST:'.PHP_EOL;
echo  'OK    : '.count($aResult['ok']).PHP_EOL
	. 'ERRORS: '.count($aResult['error']).PHP_EOL
	;
// ----------------------------------------------------------------------
