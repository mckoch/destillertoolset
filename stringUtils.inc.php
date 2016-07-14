<?php
/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/

/**
Assorted collection of string utilities
*/

/**
 * Create a random password.
 *
 * @param integer $maxLength, maximum length
 * @param boolean $onlyNumbers
 * @return string
 */
function simplePassword ($maxLength = 6, $onlyNumbers=false) {

    //srand ((double)microtime()*1000003) ;

    // no 0 ('Oh') or O (zero) allowed as this causes confusion...
    $acceptedChars = 'azertyuiopqsdfghjklmwxcvbnAZERTYUIPQSDFGHJKLMWXCVBN123456789';
    if ($onlyNumbers) {
        $acceptedChars = '012345678938' ;
    }
    $max = strlen($acceptedChars)-1;
    $newpassword = null;
    for($i=0; $i < $maxLength; $i++) {
        $newpassword .= $acceptedChars{rand(0, $max)};
    }
    return $newpassword ;
}

function trim_value(&$value, $key, $trimChars)
{
    if (is_string ($value)) {
        $value = trim($value, $trimChars);
    }
}

/**
Trims all strings in the array.  Trims the array in place.
*/
function array_trim (&$array, $trimChars=" \n\r\t") {
    array_walk($array, 'trim_value', $trimChars);
}

function tolower_value (&$value, $key) {
    $value = strtolower($value) ;
}

/**
 * Puts all strings in the array to lower case
 * @param array $array
 */
function array_tolower (&$array) {
    array_walk ($array, 'tolower_value') ;
}

/**
 * Remove empty lines from the array
 */
function array_removeEmptyLines ($array) {
    $result = array() ;
    foreach ($array as $element) {
        $element = trim ($element) ;
        if ($element != '') $result[] = $element ;
    }
    return $result ;
}

/**
From an array of key/value pairs, extract only one key.
@return array with extracted value corresponding to key
*/
function array_extract ($array, $keyName) {
    if (!is_array($array)) return array() ;
    $result = array () ;
    foreach ($array as $value) {
        if (isset ($value[$keyName]))
        $result[] = $value[$keyName] ;
    }
    return $result ;
}

/**
Transforms an associated array into key=value string. If the $value is NULL, then only the
$key is printed.  If the string is non-empty it **starts and ends** with the $sep.
@param array $array the input array
@param string $sep separator string, default = blank
*/

function array_keyvalue ($array, $sep=' ') {
    if (count($array) == 0) {
        return '' ;
    }
    $result = $sep ;
    foreach ($array as $key => $value) {
        if ($value === NULL) $result .= "$key$sep" ;
        else                 $result .= "$key=\"$value\"$sep" ;
    }
    return $result ;
}

/**
Convert a string with accents into a string without accents.
see http://be2.php.net/strtr
*/
function transcribe($string) {
    if (function_exists('mb_convert_encoding')) {
        $string = mb_convert_encoding ($string, "ISO-8859-1") ;
        $string = strtr($string,
        "\xA1\xAA\xBA\xBF\xC0\xC1\xC2\xC3\xC5\xC7\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1\xD2\xD3\xD4\xD5\xD8\xD9\xDA\xDB\xDD\xE0\xE1\xE2\xE3\xE5\xE7\xE8\xE9\xEA\xEB\xEC\xED\xEE\xEF\xF0\xF1\xF2\xF3\xF4\xF5\xF8\xF9\xFA\xFB\xFD\xFF",
        "!ao?AAAAACEEEEIIIIDNOOOOOUUUYaaaaaceeeeiiiidnooooouuuyy");
        $string = strtr($string, array("\xC4"=>"Ae", "\xC6"=>"AE", "\xD6"=>"Oe", "\xDC"=>"Ue", "\xDE"=>"TH", "\xDF"=>"ss", "\xE4"=>"ae", "\xE6"=>"ae", "\xF6"=>"oe", "\xFC"=>"ue", "\xFE"=>"th"));
        return($string);
    } else {
        return remove_accents($string) ;
    }
}

function remove_accents($string) {
    $from = array ('á','ä','à','â','å','ã','æ', 'é','è','ë','ê','í','ì','ï','î','ú','ù','ü','û','ç','ð','ñ','ó','ò','ô','ö','ø','Š','Œ', 'Ž','š','œ' ,'¢','Á','A','Ä','Â','Å','Æ' ,'Ç','É','È','Ë','Ê','Í','Ì','Ï','Î','Ñ','Ó','Ò','Ö','Ô','Ø','Ù','Ú','Ü','Û','Þ','ß') ;
    $to   = array ('a','a','a','a','a','a','ae','e','e','e','e','i','i','i','i','u','u','u','u','c','o','n','o','o','o','o','o','S','OE','Z','s','oe','c','A','A','A','A','A','AE','C','E','E','E','E','I','I','I','I','N','O','O','O','O','O','U','U','U','U','TH', 'ss') ;

    return str_replace($from, $to, $string);
}

/**
 * Remove part of the string between start- and endindex (inclusive)
 * eg. str_remove ('abcdef', 2,3) -> 'abef'
 *
 * @param string $str
 * @param integer $startIndex
 * @param integer $endIndex
 */
function str_remove ($str, $startIndex, $endIndex) {

    $first = substr($str, 0, $startIndex) ;
    if ($endIndex == NULL) return $first ;

    $second = substr($str, $endIndex+1) ;
    return $first.$second ;
}

/**
 * Remove part of the string that's in brackets (including)
 * @param string $str
 * @return string
 */
function str_removeBrackets ($str) {
    $i1 = strpos ($str, '(') ;
    $i2 = strpos ($str, ')') ;
    if (($i1 === false)) {
        $i1 = strpos ($str, '[') ;
        $i2 = strpos ($str, ']') ;
    }
    if (($i1 === false) || ($i2 === false)) {
        return $str ;
    }
    $str = str_remove($str, $i1, $i2) ;
    return trim ($str) ;
}

/**
Recursive array search.
Search for key, return the key=>value and optionally also key2=>value2
@param array $array
@param mixed $key    key to search in the array
@param mixed $key2   second key, optional
@return array:  array (value, value,..) or array (value1=>value2, ..)
*/

function array_keysearch ($array, $key1, $key2=NULL) {

    if (($array == NULL) || !is_array ($array)) {
        return array() ;
    }

    $result = array () ;
    if (isset ($array [$key1])) {
        if ($key2 != NULL) {
            $result[$array [$key1]] = isset ($array[$key2]) ?  $array[$key2] : NULL ;
        }
        else {
            $result[] = $array[$key1] ;
        }
    }
    foreach ($array as $element) {
        if (is_array ($element)) {
            if ($key2 == NULL) {
                $result = array_merge ($result, array_keysearch ($element, $key1, $key2)) ;
            } else {
                $result = $result + array_keysearch ($element, $key1, $key2) ;
            }
        }
    }
    return $result ;
}

/**
 * Remove an element with a $key from the $array
 *
 * @param array $array, array of arrays
 * @param string $key
 * @return array
 */
function array_keyremove ($array, $key) {

    foreach ($array as &$oneArray) {
        if (isset ($oneArray[$key])) unset ($oneArray[$key]) ;
    }

    return $array ;
}

/**
 Return and array with the number of occurances of a value in the input array.
 @param array $input
 @return array (key => weight), highest weights first
*/
function array_weight ($input) {
    $result = array () ;
    foreach ($input as $value) {
        if (isset ($result[$value])) {
            $result[$value]++ ;
        }
        else {
            $result[$value] = 0 ;
        }
    }
    arsort ($result) ;
    return $result ;
}

/**
 Recursive check if $value occurs in $arrray
 @param mixed $value is the value to search for
 @param string $key is the key for the looked for value
 @param array $array
 @return true if $value found in $array
 */

function deep_in_array($searchValue, $searchKey, $array) {
    foreach($array as $key => $item) {
        if(!is_array($item)) {
            if (($key == $searchKey) && ($item == $searchValue)) return true;
            else continue;
        }

        if (isset ($item[$searchKey]) && ($item[$searchKey] == $searchValue)) {
            return true;
        } else if (deep_in_array($searchValue, $searchKey, $item)) {
            return true;
        }
    }
    return false;
}

/** Strip leading 'the' and 'a' from a string.
@param string $t
@return string
*/
function stripTHE ($t) {
    if (strncasecmp ($t, "THE ", 4) == 0) {
        $t = substr ($t, 4) ;
    }
    elseif (strncasecmp ($t, "A ", 2) == 0) {
        $t = substr ($t, 2) ;
    }
    return $t ;
}

/**
 * Count the number of occurences of $needle in the $haystack.
 * @param string $haystack
 * @param string $needle
 * return integer
 */
function strcount ($haystack, $needle) {
    $count = 0 ;
    $index = 0 ;
    for (;;) {
        $index = stripos ($haystack, $needle, $index) ;
        if ($index === false) {
            break ;
        }
        $count++ ;
        $index++ ;
    }
    return $count ;
}

/**
         * Return a standard SQL date
         * @param integer $day
         * @param integer $month
         * @param integer $year with 4 numbers
         * @param integer $increment 1=tomorrow, 2=day after tomorrow,...
         * @return string
         */
function standardDate ($day, $month, $year, $increment=0) {
    $t = mktime (20, 0, 0, $month, $day, $year) ;
    $t += $increment * (60*60*24) ;
    return date ('Y-m-d', $t) ;
}

/**
 * Wrapper for the build-in array_rand().
 * - $req can be any integer, not just between 1 and count($arr)
 * - $arr can be empty
 * - always return an array, never an integer
 *
 * @param array $arr
 * @param integer $num
 * @return array of keys
 */
function array_random ($arr, $num=1) {

    if ((count ($arr) == 0) || ($num == 0)) {
        return array() ;
    }
    if ($num == 1) {
        return array (array_rand ($arr, 1)) ;
    }
    if ($num > count ($arr)) $num = count ($arr) ;
    $r = array_rand ($arr, $num) ;
    return (is_array($r)) ? $r : array ($r) ;
}

/**
 * Sorts an array of associative arrays.
 * The $key has to be present in each element of the array.
 * @param array $array
 * @param string $key
 */
$_key = NULL ;
function arrayListSort (&$array, $key) {
    global $_key ;
    $_key = $key ;
    usort ($array, '_sortByKey') ;
}

function _sortByKey ($elem1, $elem2) {
    global $_key ;
    $v1 = $elem1[$_key] ;
    $v2 = $elem2[$_key] ;
    if ($v1 == $v1) $r = 0 ;
    $r = ($v1 < $v2) ? -1 : 1 ;
    //echo("key=$_key  v1=$v1  v2=$v2  r=$r<br>");
    return $r ;
}

/**
 * Counts the number of elements in a array, recursively
 * @param array $array
 * @return integer
 */
function array_count (&$array) {
    if (!is_array($array)) {
        return 0 ;
    }
    $count = 0 ;
    foreach ($array as $element) {
        if (is_array($element)) {
            $count += array_count($element) ;
        }
    }
    $count += count ($array) ;
    return $count ;
}

// We can't use the above function as it causes a server error!
function count_globals () {
    $count = 0 ;
    foreach ($GLOBALS as $element) {
        if (is_array($element)) {
            $count += count($element) ;
        }
    }
    $count += count ($GLOBALS) ;
    return $count ;
}

/**
 * Remove text up to (and including) $cutHeader from $source
 * Remove text from (and including) $cutFooter from $source
 * Return the remainder of $source
 *
 * @param string $source
 * @param string $cutHeader, optional
 * @param string $cutFooter, optional
 * @return string or null if header and/or footer not found
 */
function textCutting ($source, $cutHeader = NULL, $cutFooter = NULL) {
    if ($cutHeader != NULL) {
        $pos = stripos ($source, $cutHeader) ;
        if ($pos === false) return NULL ;
        $source = substr($source, $pos + strlen($cutHeader)) ;
    }

    if ($cutFooter != NULL) {
        $pos = strripos($source, $cutFooter) ;
        if ($pos === false) return NULL ;
        $source = substr($source, 0, $pos) ;
    }

    return $source ;
}

/**
 * Advance the array, return the next non-empty value
 *
 * @param array $array
 * @return mixed
 */
function advance (&$array) {
    do {
        $current = current ($array) ;
        if ($current === false) return $current ;
        next ($array) ;
    } while ($current == '') ;
    return $current ;
}

/**
 * Folds an array.  It takes n elements at a time and returns a 2-dimensional array.
 * eg. array_fold (array (1,2,3,4,5,6),3) => array (array(1,2,3), array (4,5,6))
 * Keys are not preserved by default
 * @param mixed $array
 * @param integer $n, number of items per row folded
 * @param boolean $preserveKeys
 * @return array
 */
function array_fold ($array, $n, $preserveKeys=false) {
    $result = array () ;

    $m = count ($array) ;
    for ($i = 0 ; $i < $m ; $i += $n) {
        $result[] = array_slice ($array, $i, $n) ;
    }
    return $result ;
}

/**
 * @param string $s
 * @return boolean, true if $s consists of all ascii uppercase characters
 */
function is_all_uppercase ($s) {

    $m = array () ;
    $r = preg_match ('/[A-Z ]+/', $s, $m) ;
    if ($r == false) return false ;

    return strlen ($m[0]) === strlen ($s) ;
}

/**
 * Compares two strings word by word. Note that numerals are also discarded by str_word_count
 * @param string $s1
 * @param string $s2
 * @param array $exceptions optional list of words not be considered. Should already be lowercase for efficiency.
 * @return integer number of different words
 */
function wordDifference ($s1, $s2, $exceptions = NULL) {

    $s1 = strtolower($s1) ;
    $s2 = strtolower($s2) ;
    if ($exceptions != NULL) {
        $s1 = str_replace($exceptions, '', $s1) ;
        $s2 = str_replace($exceptions, '', $s2) ;
    }
    // one or both are empty, then any comparison is almost meaningless..
    if ((trim($s1) == '') || (trim($s2) == '')) {
        return 99 ;
    }
    $s1 = str_word_count($s1, 1, '0123456789:') ; // returns array with words inside $s1
    $s2 = str_word_count($s2, 1, '0123456789:') ;
    //print_array($s1);
    //print_array($s2);
    $diff = count(array_diff($s1, $s2)) + count(array_diff($s2, $s1)) ;
    return  ($diff) ;
}

/**
 * Fixes words in a text based on a dictionary
 * @param string $text
 * @param array $dictionary array of words in lowercase
 * @param integer $maxDiff optional maximum difference in Levensthein algo
 * @return string
 */
function fixText ($text, $dictionary, $maxDiff=1) {

    $words = explode (' ', $text) ;
    foreach ($words as &$oneWord) {
        $s = strtolower($oneWord) ;
        foreach ($dictionary as $oneEntry) {
            $m = levenshtein($s, $oneEntry) ;
            if ($m == 0) break ;        // word matches
            if ($m <= $maxDiff) {
                $oneWord = ucfirst ($oneEntry) ;
                break ;
            }
        }
    }
    return implode (' ', $words) ;
}

/**
 * Split a csv-formatted line into an array.
 * @see http://www.php.net/manual/en/function.split.php
 *
 * @param string $string
 * @param string $separator
 * @return array
 */
function getCSVValues($string, $separator=",")
{
    $elements = explode($separator, $string);
    for ($i = 0; $i < count($elements); $i++) {
        $nquotes = substr_count($elements[$i], '"');
        if ($nquotes %2 == 1) {
            for ($j = $i+1; $j < count($elements); $j++) {
                if (substr_count($elements[$j], '"') > 0) {
                    // Put the quoted string's pieces back together again
                    array_splice($elements, $i, $j-$i+1,
                    implode($separator, array_slice($elements, $i, $j-$i+1)));
                    break;
                }
            }
        }
        if ($nquotes > 0) {
            // Remove first and last quotes, then merge pairs of quotes
            $qstr =& $elements[$i];
            $qstr = substr_replace($qstr, '', strpos($qstr, '"'), 1);
            $qstr = substr_replace($qstr, '', strrpos($qstr, '"'), 1);
            $qstr = str_replace('""', '"', $qstr);
        }
    }
    return $elements;
}

?>