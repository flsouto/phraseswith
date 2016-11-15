<?php

$total_words = 0;
$total_phrases = 0;
$total_words_ok = 0;
$words_ok = [];

$dir = __DIR__."/words/";
foreach(scandir($dir) as $word){
	if(substr($word,-4)!='.txt'){
		continue;
	}
	$total_words++;
	$phrases = file($dir.$word);
	$cnt_phrases = 0;
	foreach($phrases as $p){
		$p = trim($p);
		if(!$p){
			continue;
		}
		$cnt_phrases++;
	}
	$total_phrases+=$cnt_phrases;
	if($cnt_phrases>=20){
		$total_words_ok++;
		$words_ok[] = substr($word,0,-4);
	}
}

echo "Total words: ".$total_words.PHP_EOL;
echo "Total phrases: ".$total_phrases.PHP_EOL;
echo "Total words ok: ".$total_words_ok.PHP_EOL;

print_r($words_ok);