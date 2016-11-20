<?php

$contents = file_get_contents($argv[1]);
$contents = preg_replace("/\[Illustration:.*?\]/i","",$contents);

$source = rtrim(basename($file),'.txt');

preg_match_all("/\s([a-z]{3,})\s/",$contents,$matches,PREG_OFFSET_CAPTURE);
unset($matches[0]);

$grouped = [];

foreach($matches[1] as $i => $word){
	$grouped[$word[0]][] = $word[1];
};

foreach($grouped as $word => $positions){

	$word_file = __DIR__."/words/$word.txt";
	
	$count = count($positions);
	
	if($count <= 50) {
		$goal = $count;
	} else if($count <= 100){
		$goal = 50;
	} else {
		$goal = 1;
	}

	$phrases = 0;
	shuffle($positions);
	foreach($positions as $pos){
		$phrase = extractPhrase($contents, $pos);
		if(strlen($phrase)<30 || strlen($phrase)>200){
			continue;
		}
		if(!preg_match("/^[A-Z]/",$phrase)){
			continue;
		}
		if(strstr($phrase,'"')){
			continue;
		}
		if(substr($phrase,-1)!='.'){
			continue;
		}
		if(!file_exists($word_file)){
			touch($word_file);
		}
		file_put_contents($word_file, $phrase.'|'.$source.PHP_EOL, FILE_APPEND);
		$phrases++;
		if($phrases>=$goal){
			break;
		}
	}

}

function extractPhrase($content,$pos){

	$length = strlen($content);

	$append = '';
	for($i=$pos;$i<$length;$i++){
		$char = substr($content,$i,1);
		$char_prev = substr($content,$i-1,1);
		if($char==" "&&$char_prev==" "){
			continue;
		}
		if(ctype_cntrl($char)){
			if(ctype_cntrl($char_prev)){
				break;
			}
			$char = " ";
		}
		$append .= $char;
		if($char=="." && checkShouldBreak($content,$i)){
			break;
		}
	}

	$prepend = '';
	for($i=$pos-1;$i>=0;$i--){
		$char = substr($content,$i,1);
		$char_prev = substr($content,$i+1,1);

		if($char==" "&&$char_prev==" "){
			continue;
		}
		if($char=='.' && checkShouldBreak($content,$i)){
			break;
		}
		if(ctype_cntrl($char)){
			if(ctype_cntrl($char_prev)){
				break;
			}
			$char = " ";
		}
		$prepend = $char.$prepend;
	}

	$phrase = ltrim($prepend).$append;
    return $phrase;
}

function checkShouldBreak($content, $period_pos){
	$word = "";
	for($i=$period_pos-1;$i>=0;$i--){
		$char = substr($content,$i,1);
		if(!preg_match("/[a-z]/i",$char)){
			break;
		}
		$word = $char.$word;
	}

	if(preg_match("/^[A-Z]/",$word)){
		return false;
	}

	if(in_array(strtolower($word),['mr','sr','mrs','srs'])){
		return false;		
	}

	return true;
}