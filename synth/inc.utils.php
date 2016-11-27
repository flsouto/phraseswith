<?php

function getConfig(){
	static $config= null;
	if(is_null($config)){
		$config = json_decode(file_get_contents(__DIR__.'/config.json'),true);
	}
	return $config;
}

function getCorpusFiles(){
	
	static $files = null;

	if(is_null($files)){

		$config = getConfig();

		$files = [];

		foreach(scandir($config['corpus_dir']) as $file){
			if($file=='.'||$file=='..'){
				continue;
			}
			$files[] = $config['corpus_dir'].'/'.$file;
		}


	}

	return $files;

}

function findRandomTarget(){

	$corpus_files = getCorpusFiles();

	$corpus_file = $corpus_files[array_rand($corpus_files)];

	$corpus_content = file_get_contents($corpus_file);

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

		if(checkTargetExists($target)){
			continue;
		}

		if(checkSkipTarget($target)){
			continue;
		}

		break;

	}

	return $target;

}

function findParsedTarget(){
	
	foreach(scandir('extracted') as $file){
		if($file=='.'||$file=='..'){
			continue;
		}
		$targets = file("extracted/$file");
		shuffle($targets);
		foreach($targets as $target){
			$target = trim($target);
			$target = ltrim($target,'+-');
			if(empty($target)){
				continue;
			}
			if(checkTargetExists($target)){
				continue;
			}
			if(checkSkipTarget($target)){
				continue;
			}
			return $target;
		}
	}

}

function checkTargetExists($target){
	return file_exists('extracted/'.$target.'.txt');
}

function checkSkipTarget($target){

	static $skip_targets = null;

	if(is_null($skip_targets)){
		$skip_targets = file_get_contents('skip.txt');
	}

	return strstr($skip_targets,$target);
}