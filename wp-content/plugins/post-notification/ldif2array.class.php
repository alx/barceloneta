<?php
/**
* Class to read LDIF data
* @author tobozo <tobozo at phpsecure dot info>
* @contributor Vladimir Struchkov <great_boba at yahoo dot com>
* @copyleft (l) 2006-2007  tobozo
* @package ldif2Array
* @version 1.2
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @date 2006-12-13
* 
* 2008-09-16: Removed dead code; Moritz 'Morty' Strübe <morty@gmx.net>
* 
*/

class ldif2array {

    /**
    * stores file name
    * @type string
    */
    var $file;

    /**
    * store data
    * @type string
    */
    var $rawdata;

    /**
    * store entries
    * @type array
    */
    var $entries = array();


    //== constructor ====================================================================
    function ldif2array(/*string*/$file='', /*bool*/$process=false) {
      $this->file = $file;
      if($process) {
        $this->makeArray();
      }
    }


    /**
    * returns the array of LDIF entries
    * @return array
    */
    function getArray() {
      return $this->entries;
    }


    /**
    * Sanity check before building the array, returns false if error
    * @return bool
    */
    function makeArray() {
       if($this->file=='') {
         if($this->rawdata=='') {
           echo "No filename specified, aborting";
           return false;
         }
       } else {
         if(!file_exists($this->file)) {
           echo "File $this->file does not exist, aborting";
           return false;
         } else {
           $this->rawdata  = file_get_contents($this->file);
         }
       }

       if($this->rawdata=='') {
         echo "No data in file, aborting";
         return false;
       }

       $this->parse2array();
       return true;
    }


    /**
    * Build the array in two passes
    * @return void
    */
    function parse2array()  {
        /**
        * Thanks to Vladimir Struchkov <great_boba yahoo com> for providing the
        * code to extract base64 encoded values
        */

        $arr1=explode("\n",str_replace("\r",'',$this->rawdata));
        $i=$j=0;
        $arr2=array();

        /* First pass, rawdata is splitted into raw blocks */
        foreach($arr1 as $v)  {
            if (trim($v)=='') {
                ++$i;
                $j=0;
            } else {
                $arr2[$i][$j++]=$v;
            }
        }

        /* Second pass, raw blocks are updated with their name/value pairs */
        foreach($arr2 as $k1=>$v1) {
            $i=0;
            $decode=false;
            foreach($v1 as $v2) {
                if (ereg('::',$v2)) { // base64 encoded, chunk start
                    $decode=true;
                    $arr=explode(':',str_replace('::',':',$v2));
                    $i=$arr[0];
                    $this->entries[$k1][$i]=base64_decode($arr[1]);
                } elseif (ereg(':',$v2)) {
                    $decode=false;
                    $arr=explode(':',$v2);
                    $count=count($arr);
                    if ($count!=2)
                        for($i=$count-1;$i>1;--$i)
                            $arr[$i-1].=':'.$arr[$i];
                    $i=$arr[0];
                    $this->entries[$k1][$i]=$arr[1];
                } else {
                    if ($decode) { // base64 encoded, next chunk
                        $this->entries[$k1][$i].=base64_decode($v2);
                    } else {
                        $this->entries[$k1][$i]=$v2;
                    }
                }
            }
        }
    }



}; // end class

?>