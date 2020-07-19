# PHP CLASS CSV2HASH

## Description

If you have csv file with column names in the first row ... 

This class reads the csv and returns each row using the column names
as key. If the column name contains a dot "." it is used to handle
subkeys.

  * Author: Axel Hahn
  * License: GNU GPL 3.0 (free software and open source)
  * Source: https://github.com/axelhahn/class-csv2hash

## Example

My example csv data:

```
id,hide,label,flag.x,flag.y
1,1,one,0,1
2,0,two,1,0
3,0,three,1,0
```

... can be be returned as

```
Array
(
    [0] => Array
        (
            [id] => 1
            [hide] => 1
            [label] => one
            [flag] => Array
                (
                    [x] => 0
                    [y] => 1
                )

        )

    [1] => Array
        (
            [id] => 2
            [hide] => 0
            [label] => two
            [flag] => Array
                (
                    [x] => 1
                    [y] => 0
                )

        )

    [2] => Array
        (
            [id] => 3
            [hide] => 0
            [label] => three
            [flag] => Array
                (
                    [x] => 1
                    [y] => 0
                )

        )

)
```


## Installation

Copy the file ./src/csv2hash.class.php to a good place.

## Usage

### Read CSV and get all data

``` php
require_once(dirname(__DIR__).'/src/csv2hash.class.php');
$sCsvfile='/full/path/of/file.csv';

$oC2H=new csv2hash();
$oC2H->setCsvFile($sCsvfile);

print_r($oC2H->getData(),1)
```

### Set another delimiter

Default delimiter for csv data is a comma.

``` php
$oC2H->setDelimiter(';');
$oC2H->setCsvFile($sCsvfile);

echo '<pre>'.print_r($oC2H->getData(), 1).'</pre>';
```
or
``` php
$oC2H->setCsvFile($sCsvfile, ';');
echo '<pre>'.print_r($oC2H->getData(), 1).'</pre>';
```

### Generate subkeys in data

If a header has values divided by a given string you can create subkeys in the data.

Example:

In the demo csv are colums flag.x and flag.y. Set the dot as splitting string.

``` php
$oC2H->setHeaderSplitter('.');
$oC2H->setCsvFile($sCsvfile);

echo '<pre>'.print_r($oC2H->getData(), 1).'</pre>';
```

BTW: This is the source for the array in the beginning of the readme.

### Get filtered data

At first: This is a very basic implementation. But maybe it helps you. If not you can loop over all result entries and filter yourselfes. Or add it in the class to send a merge request.

The method getData supports 2 parameters

  * Array of arrays with filter definitions
    * each filter item has the structure
	  * name of column in CSV to filter
	  * array([Compare], [Value]) Compare is one of
	    * "is" - value in CSV is exactly given value (bool|int|string)
		* "not" - value in CVS does not match (bool|int|string)
		* "matches" - vanlue in CSV matches a given regex
  * Method if you have more than one filters: how to combine them; one of "and" | "or"

Here is a single filter with a regex to match the column "label"

``` php
// filter by column "label" to return lines that match 
// regex /^o/ (start with letter "o" --> matches the line with label "one")
$aFilter=array(
	'label'=>array('matches', '/^o/'),
);
echo '<pre>'.print_r($oC2H->getData($aFilter), 1).'</pre>';
```

2nd Example: 

2 filters using OR function - getData($aFilter, '**or**'). It matches the first row with hide=1 and the 3rd line by label "three".

``` php
$aFilter=array(
	'hide'=>array('is', "1"),
	'label'=>array('matches', '/^th/'),
);
echo '<pre>'.print_r($oC2H->getData($aFilter, 'or'), 1).'</pre>';
```
