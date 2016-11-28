<?php

require_once(__DIR__.'/inc.utils.php');

if(empty($argv[1])){
	die("Usage: command <TARGET>".PHP_EOL);
}

$target = $argv[1];
$target_file = 'extracted/'.$target.'.txt';
$all_matches = [];
$corpus_files = getCorpusFiles();

function showStatus(){

	global $all_matches, $corpus_files, $target;
	
	echo "Processing target '$target'".PHP_EOL;
	echo "Total matches so far: ".count($all_matches).PHP_EOL;
	echo "Corpus remaining: ".count($corpus_files).PHP_EOL;
	echo PHP_EOL;

}

echo PHP_EOL;
showStatus();

$last_update = time();

while(!empty($corpus_files)){
	$file = array_shift($corpus_files);
	$contents = file_get_contents($file);

	preg_match_all("/$target\s([a-z]+\s[a-z]+)[ ,.]/",$contents,$m);
	$matches = array_unique($m[1]);
	foreach($matches as $m){
		if(!isset($all_matches['+'.$m])){
			file_put_contents($target_file, '+'.$m.PHP_EOL, FILE_APPEND);
			$all_matches['+'.$m] = 1;
		}
	}

	preg_match_all("/[ ,.]([a-z]+\s[a-z]+)\s$target/",$contents,$m);
	$matches = array_unique($m[1]);
	foreach($matches as $m){
		if(!isset($all_matches['-'.$m])){
			file_put_contents($target_file, '-'.$m.PHP_EOL, FILE_APPEND);
			$all_matches['-'.$m] = 1;
		}
	}

	if(time()-$last_update >= 4){
		$last_update = time();
		showStatus();	
	}

}

showStatus();

echo 'finished'.PHP_EOL;

if(empty($all_matches)){
	file_put_contents('skip.txt',$target.PHP_EOL,FILE_APPEND);
}
