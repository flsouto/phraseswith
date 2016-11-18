<?php

$total_words = 0;
$total_words_ok = 0;
$words_ok = [];
$phrases_hashes = [];

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
		$hash = md5($p);
        $phrases_hashes[$hash] = 1;
        $cnt_phrases++;
	}
	if($cnt_phrases>=10){
		$total_words_ok++;
		$words_ok[] = substr($word,0,-4);
	}
}

echo "Total words: ".$total_words.PHP_EOL;
echo "Total phrases: ".count($phrases_hashes).PHP_EOL;
echo "Total words ok: ".$total_words_ok.PHP_EOL;
