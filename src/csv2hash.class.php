<?php
/**
 * ======================================================================
 * 
 * CLASS   C S V   2   H A S H
 * 
 * ----------------------------------------------------------------------
 * If you have csv file with column names in the first row ... 
 * this class reads the csv and returns each row using the column names
 * as key.
 * ----------------------------------------------------------------------
 * THERE IS NO WARRANTY FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE <br>
 * LAW. EXCEPT WHEN OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR <br>
 * OTHER PARTIES PROVIDE THE PROGRAM ?AS IS? WITHOUT WARRANTY OF ANY KIND, <br>
 * EITHER EXPRESSED OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED <br>
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE. THE <br>
 * ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU. <br>
 * SHOULD THE PROGRAM PROVE DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY <br>
 * SERVICING, REPAIR OR CORRECTION.<br>
 * <br>
 * ----------------------------------------------------------------------
 * 2020-07-19  www.axel-hahn.de  v1.0
 * ======================================================================
 */
class csv2hash {

    /**
     * delimiter in the CSV
     * @var string|char
     */
    var $_sDelimiter = ',';

    /**
     * delimiter for header items in the CSV to split subkeys
     * @var string|char
     */
	var $_sHeaderSplitter = false;

    /**
     * number of column in the CSV to use as uniq key
	 * - false creates a array of items
	 * - a number uses values in the given column as key
     * @var boolean|integer
     */
    var $_iKeyColumn = false;
    
    /**
     * filename of the csv to read
     * @var string
     */
    var $_csvFile = false;
    
    /**
     * read data from csv file
     * @var type 
     */
    var $_aData = array();

    // ----------------------------------------------------------------------
    // CONSTRUCTOR
    // ----------------------------------------------------------------------
    public function __construct($file = false, $sDelim = false) {
        if ($file) {
            $this->setCsvFile($file, $sDelim);
        }
        return true;
    }
    
    // ----------------------------------------------------------------------
    // PRIVATE FUNCTIONS
    // ----------------------------------------------------------------------

    /**
     * read CSV file and return as array/ hash
     * @return Array
     */
    protected function _readCsvFile() {
        $aReturn = array();
        $aHeader = array();
		$sDivider = '.';
        if (file_exists($this->_csvFile)) {

            // --- read file
            $handle = fopen($this->_csvFile, 'r');

            // put data to an array
            while (($data = fgetcsv($handle, 1000, $this->_sDelimiter)) !== FALSE) {
                $num = count($data);
                if (!count($aHeader)) {
					// add header
                    for ($c = 0; $c < $num; $c++) {
                        $aHeader[$c] = $data[$c];
                    }
                } else {
					// add data
					$aItem=array();
                    for ($c = 0; $c < $num; $c++) {
						$sItemKey=$aHeader[$c];
						if($this->_sHeaderSplitter){
							$aTmp=preg_split('/\\'.$this->_sHeaderSplitter.'/', $sItemKey);
							if(count($aTmp)>1){
								$target=&$aItem;
								foreach($aTmp as $sSubKey){
									if(!isset($target[$sSubKey])){
										$target[$sSubKey]=array();
									}
									$target=&$target[$sSubKey];
								}
							} else {
								$target=&$aItem[$sItemKey];
							}
						} else {
							$target=&$aItem[$sItemKey];
						}

						$target = $data[$c];
                    }
					
					// add item
					if ($this->_iKeyColumn===false){
						$aReturn[]=$aItem;
					} else {
						$sItemKey=$data[$this->_iKeyColumn];
						$aReturn[$sItemKey]=$aItem;
					}
                }
            }
            fclose($handle);
        }
        return $aReturn;
    }

    // ----------------------------------------------------------------------
    // PUBLIC FUNCTIONS - SETTER
    // ----------------------------------------------------------------------

    /**
     * set a csv file to read
     * 
     * @param string  $file     filename of a csv file to read 
     * @param string  $sDelim   optional: delimter
     * @return boolean
     */
    public function setCsvFile($file, $sDelim=false) {
        if (!file_exists($file)) {
            // die(__METHOD__ . " file $file does not exist.");
            return false;
        }
        $this->_csvFile = $file;
        if($sDelim){
            $this->setDelimiter($sDelim);
        }
        return true;
    }

    /**
     * set a new delimiter in for the csv file
     * 
     * @param string  $sDelim   new delimter; must be a single char
     * @return boolean
     */
    public function setDelimiter($sDelim) {
        if($sDelim && strlen($sDelim)==1){
            $this->_sDelimiter=$sDelim;
            return true;
        }
        return false;
    }
    /**
     * set a splitter in for the csv file
     * 
     * @param string  $sDelim   new delimter; must be a single char
     * @return boolean
     */
    public function setHeaderSplitter($sDelim) {
        if($sDelim && strlen($sDelim)==1){
            $this->_sHeaderSplitter=$sDelim;
            return true;
        }
        return false;
    }
    /**
     * set a new column to use as primary key
     * 
     * @param integer  $iCol   new column as integer or false
     * @return boolean
     */
    public function setKeyColumn($iCol=false) {
		$this->_iKeyColumn=($iCol===false) ? false : (int)$iCol;
        return true;
    }
    // ----------------------------------------------------------------------
    // PUBLIC FUNCTIONS - GETTER
    // ----------------------------------------------------------------------

    /**
     * get table data of csv file as an array;
     * it can be filtered by a given array
     *
     * @param  array  $aFilter  filter rules
     *                         - key = column to check
     *                         - array
     *                            - type - one of (is|matches|not)
     *                            - compare data
     * @param  string $sFilterMode  mode to filter; one of (and|or); default: or
     * @return array
     */
    public function getData($aFilter = array(), $sFilterMode = 'or') {
        // echo 'DEBUG: aFilter=<pre>'.print_r($aFilter, 1).'</pre>';
        $aReturn = array();
        $this->_aData = $this->_readCsvFile();
        foreach ($this->_aData as $sKey => $aItem) {
            // echo 'DEBUG: item <pre>'.print_r($aItem, 1).'</pre>';
            $bIsFirstFilter = true;

			$bAdd = count($aFilter) ? 
				($sFilterMode==='or' ? false : true)
				: true;

            // --- check filter items
            foreach ($aFilter as $sFilterKey => $aRule) {
                if (!isset($aItem[$sFilterKey])) {
                    continue;
                }
                $sCheck = $aRule[0];
                $sComparedata = $aRule[1];
                $bAddKey = false;
                switch ($sCheck) {
                    case 'is':
                        $bAddKey = $aItem[$sFilterKey] == $sComparedata;
                        break;
                    case 'matches':
                        $bAddKey = preg_match($sComparedata, $aItem[$sFilterKey]);
                        break;
                    case 'not':
                        $bAddKey = $aItem[$sFilterKey] != $sComparedata;
                        break;
                    default:
                        ;
                        ;
                }
                // echo "DEBUG: $sFilterMode: $sFilterKey [$sCheck] $sComparedata --> ".($bAddKey ? "true":"false").PHP_EOL;
                if ($bIsFirstFilter) {
                    $bAdd = $bAddKey;
                    $bIsFirstFilter = false;
                } else {

                    switch ($sFilterMode) {
                        case 'or':
                            $bAdd = $bAdd || $bAddKey;
                            break;
                        case 'and':
                            $bAdd = $bAdd && $bAddKey;
                            break;
                        default:
                            die(__METHOD__ . ' ERROR: unknown filter mode [' . $sFilterMode . ']. It must be one of and|or');
                    }
                }
            }
			// echo 'DEBUG bAdd '.($bAdd ? "true":"false").PHP_EOL;

            // --- fill return data
            if ($bAdd) {
                $aReturn[$sKey] = $aItem;
            }
        }
        return $aReturn;
    }

}
