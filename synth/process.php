<?php

$config = json_decode(file_get_contents(__DIR__.'/config.json'),true);

$corpus_dir = $config['corpus_dir'];

$corpus_files = array_diff(scandir($corpus_dir),['.','..']);

function findRandomTarget(){

	global $corpus_dir, $corpus_files;

	$corpus_file = $corpus_files[array_rand($corpus_files)];

	$corpus_content = file_get_contents($corpus_dir.'/'.$corpus_file);

	$target = '';

	while(true){
		$pos = rand(0, strlen($corpus_content)-50);

		$excerpt = substr($corpus_content, $pos, 50);

		$words = explode(' ', $excerpt);

		array_shift($words);
		array_pop($words);
		
		if(count($words) < 2){
			continue;
		}

		$target = $words[0].' '.$words[1];

		if(preg_match("/[^a-z ]/",$target)){
			continue;
		}

		if(file_exists('extracted/'.$target.'.txt')){
			continue;
		}

		break;

	}

	return $target;

}

$target = findRandomTarget();
$target_file = 'extracted/'.$target.'.txt';
$all_matches = [];

function showStatus(){

	global $all_matches, $corpus_files, $target;
	
	echo "Processing target '$target'".PHP_EOL;
	echo "Total matches so far: ".count($all_matches).PHP_EOL;
	echo "Corpus remaining: ".count($corpus_files).PHP_EOL;
	echo PHP_EOL;

}

$last_update = time();

echo PHP_EOL;
showStatus();

while(!empty($corpus_files)){
	$file = array_shift($corpus_files);
	$contents = file_get_contents($corpus_dir."/".$file);

	preg_match_all("/$target\s([a-z]+\s[a-z]+)/",$contents,$m);
	$matches = array_unique($m[1]);
	foreach($matches as $m){
		if(!isset($all_matches['+'.$m])){
			file_put_contents($target_file, '+'.$m.PHP_EOL, FILE_APPEND);
			$all_matches['+'.$m] = 1;
		}
	}

	preg_match_all("/([a-z]+\s[a-z]+)\s$target/",$contents,$m);
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


