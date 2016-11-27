<?php

require_once('inc.utils.php');

$stdin = fopen('php://stdin','r+');

echo PHP_EOL;

$asked = [];

while(true){
	
	$target = rand(0,1) ? findRandomTarget() : findParsedTarget();
	if(isset($asked[$target])){
		continue;
	}

	echo "Accept target '".$target."'? [y/n]";
	echo PHP_EOL;

	$ans = trim(fgets($stdin));

	if($ans=='y'){
		file_put_contents('queue.txt',$target.PHP_EOL,FILE_APPEND);
	} else {
		file_put_contents('skip.txt',$target.PHP_EOL,FILE_APPEND);		
	}

	$asked[$target] = 1;

}