<?php

function codificacao($string) {
	return mb_detect_encoding($string.'x', 'UTF-8, ISO-8859-1');
}

function preprocess($var, $xss = false) {
    if (!is_array($var) && !is_object($var)) {
        if (codificacao($var) == 'UTF-8') {
            $var = toUTF($var);
        }
    		
    	if ($xss == true) {
            $var = htmlspecialchars(strip_tags($var));
        }
    		
        if (!get_magic_quotes_gpc()) {
            $var = addslashes($var);
        }
            
    } else if (is_array($var)) {
        foreach ($var as $key => $value) {
            $var[$key] = preprocess($value, $xss);
        }
    } else if (is_object($var)) {
        foreach ((array) $var as $key => $value) {
            $var->{$key} = preprocess($value, $xss);
        }
    }
    
    return $var;
}

function toUTF($var) {
    if (!is_array($var) && !is_object($var)) {
        if (codificacao($var)!='UTF-8') {
            $var = utf8_encode($var);
        }
    } else if (!is_object($var)){
        foreach($var as $key=>$value) {
            $var[$key] = toUTF($value);
        }
    } else {
        foreach((array)$var as $key=>$value) {
            $var->{$key} = toUTF($value);
        }
    }
    return $var;
}
function toISO($var) {
    if (!is_array($var) && !is_object($var)) {
        if (codificacao($var)==='UTF-8') {
            $var = utf8_decode($var);
        }
    } else if (!is_object($var)){
        foreach($var as $key=>$value) {
            $var[$key] = toISO($value);
        }
    } else {
        foreach((array)$var as $key=>$value) {
            $var->{$key} = toISO($value);
        }
    }
    return $var;
}

function text_break($text,$limit) {
	$texto = explode(' ', strip_tags($text));
        $retorno = '';
        foreach ($texto as $word) {
            if (strlen($retorno.$word)<=$limit) {
                $retorno.=$word.' ';
            } else {
                break;
            }
        }
        if (strlen($retorno)!=strlen(strip_tags($text))) $retorno.='...';
        return $retorno;
}

function text_break2($text,$limit=1) {

	$doc = phpQuery::newDocument($text);
    phpQuery::selectDocument($doc);

    $result = "";
    $count=0;
    foreach(pq('p') as $p) {    	
    	$parts = explode("\n",trim(pq($p)->text(),"\n"));
    	foreach($parts as $part) {
    		$count++;
    		if (trim($part,"\n")=='') continue;
    		if ($count<=$limit) {
    			if ($result != "") $result.="\n";
    			$result.=$part;
    		} else {
    			break;
    		}
    	}
    	if ($count>$limit) break;
    }
    return nl2br($result);
}

function removeracentos($str,$enc='UTF-8') {
	$acentos = array(
		'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
		'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
		'C' => '/&Ccedil;/',
		'c' => '/&ccedil;/',
		'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
		'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
		'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
		'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
		'N' => '/&Ntilde;/',
		'n' => '/&ntilde;/',
		'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
		'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&Ouml;/',
		'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
		'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
		'Y' => '/&Yacute;/',
		'y' => '/&yacute;/',
		'' => '/&ordf;|&ordm;/'
	);
	return preg_replace($acentos,array_keys($acentos),htmlentities($str,ENT_NOQUOTES,$enc));
}

function linkfy($str) {
	$foreign_characters = array(
		'/ä|æ|ǽ/' => 'ae',
		'/ö|œ/' => 'oe',
		'/ü/' => 'ue',
		'/Ä/' => 'Ae',
		'/Ü/' => 'Ue',
		'/Ö/' => 'Oe',
		'/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ/' => 'A',
		'/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/' => 'a',
		'/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
		'/ç|ć|ĉ|ċ|č/' => 'c',
		'/Ð|Ď|Đ/' => 'Dj',
		'/ð|ď|đ/' => 'dj',
		'/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/' => 'E',
		'/è|é|ê|ë|ē|ĕ|ė|ę|ě/' => 'e',
		'/Ĝ|Ğ|Ġ|Ģ/' => 'G',
		'/ĝ|ğ|ġ|ģ/' => 'g',
		'/Ĥ|Ħ/' => 'H',
		'/ĥ|ħ/' => 'h',
		'/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ/' => 'I',
		'/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı/' => 'i',
		'/Ĵ/' => 'J',
		'/ĵ/' => 'j',
		'/Ķ/' => 'K',
		'/ķ/' => 'k',
		'/Ĺ|Ļ|Ľ|Ŀ|Ł/' => 'L',
		'/ĺ|ļ|ľ|ŀ|ł/' => 'l',
		'/Ñ|Ń|Ņ|Ň/' => 'N',
		'/ñ|ń|ņ|ň|ŉ/' => 'n',
		'/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/' => 'O',
		'/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/' => 'o',
		'/Ŕ|Ŗ|Ř/' => 'R',
		'/ŕ|ŗ|ř/' => 'r',
		'/Ś|Ŝ|Ş|Š/' => 'S',
		'/ś|ŝ|ş|š|ſ/' => 's',
		'/Ţ|Ť|Ŧ/' => 'T',
		'/ţ|ť|ŧ/' => 't',
		'/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/' => 'U',
		'/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/' => 'u',
		'/Ý|Ÿ|Ŷ/' => 'Y',
		'/ý|ÿ|ŷ/' => 'y',
		'/Ŵ/' => 'W',
		'/ŵ/' => 'w',
		'/Ź|Ż|Ž/' => 'Z',
		'/ź|ż|ž/' => 'z',
		'/Æ|Ǽ/' => 'AE',
		'/ß/'=> 'ss',
		'/Ĳ/' => 'IJ',
		'/ĳ/' => 'ij',
		'/Œ/' => 'OE',
		'/ƒ/' => 'f'
	);
	$link = strtolower(preg_replace(array_keys($foreign_characters), array_values($foreign_characters), $str));
	$link = preg_replace("/[^a-z0-9]/",' ',$link);
	$link = preg_replace("/\s\s+/",' ',$link);
	$link = str_replace(" ",'-',$link);
	return $link;
}

function formatRawSize($bytes) {
 
    //CHECK TO MAKE SURE A NUMBER WAS SENT
    if(!empty($bytes)) {

        //SET TEXT TITLES TO SHOW AT EACH LEVEL
        $s = array('bytes', 'kb', 'MB', 'GB', 'TB', 'PB');
        $e = floor(log($bytes)/log(1024));

        //CREATE COMPLETED OUTPUT
        $output = sprintf('%.2f '.$s[$e], ($bytes/pow(1024, floor($e))));

        //SEND OUTPUT TO BROWSER
        return $output;

    }
}

function resizeIframe($html,$width,$height=0) {
	$doc = phpQuery::newDocument($html);
    phpQuery::selectDocument($doc);
    $iframe = pq('iframe');
    $w = $iframe->attr('width')*1.0;
    $h = $iframe->attr('height')*1.0;
	if ($height==0) {
		$height = $h*$width/$w;
	}
    $iframe->attr('width',$width);
    $iframe->attr('height',$height);
    return $iframe.'';
}

function hasher($plain='',$salt='') {
	if ($salt=='') {
		$salt = md5(uniqid());
	} else {
		$salt = substr($salt,0,32);
	}
	return $salt.md5($salt.$plain);
}

function mascara_string($mascara,$string) {
   $string = str_replace(" ","",$string);
   for($i=0;$i<strlen($string);$i++) {
      $mascara[strpos($mascara,"#")] = $string[$i];
   }
   return $mascara;
}


function isPhone($phone) {
	return preg_match('/^\([0-9]{2}\) [0-9]{4}\-[0-9]{4,5}$/',$phone,$m);
}

function isCPF($cpf) {
	return preg_match('/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}$/',$cpf,$m);
}

function isCNPJ($cnpj) {
	return preg_match('/^[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}$/',$cnpj,$m);
}

function isCEP($cep) {
	return preg_match('/^[0-9]{5}\-[0-9]{3}$/',$cep,$m);
}

function isDate($data) {
	return preg_match('/^[0-3][0-9]\/[0-1][0-9]\/[0-9]{4}$/',$data,$m);
}

function xssFilter($var) {
	if (is_object($var)) {
		foreach((array)$var as $key=>$value) {
			$var->{$key} = xssFilter($value);
		}
	} else if (is_array($var)) {
		foreach($var as $key=>$value) {
			$var[$key] = xssFilter($value);
		}
	} else {
		$var = trim(strip_tags($var));
	}
	return $var;
}


if (!function_exists('ctype_alpha')) {
	function ctype_alpha($str) {
		return preg_match('/^[a-zA-Z]*$/',$str,$m);
	}
}
if (!function_exists('ctype_space')) {
	function ctype_space($str) {
		return preg_match('/^[\s\t\n\r]*$/',$str,$m);
	}
}

if (!function_exists('ctype_digit')) {
	function ctype_digit($str) {
		return preg_match('/^[0-9]*$/',$str,$m);
	}
}

function Ymd2dmY($date, $glue = '/') {
    return substr($date, 8, 2) . $glue . substr($date, 5, 2) . $glue . substr($date, 0, 4) . substr($date, 10);
}

function dmY2Ymd($date, $glue = '-') {
    return substr($date, 6, 4) . $glue . substr($date, 3, 2) . $glue . substr($date, 0, 2) . substr($date, 10);
}