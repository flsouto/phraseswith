<?php

require_once('inc.utils.php');

$queue = file('queue.txt');

$processed = 0;
$start_ts = time();

while(!empty($queue)){

	//$target = trim(array_shift($queue));
	$target = rand(0,1) ? findRandomTarget() : findParsedTarget();

	if(empty($target)){
		continue;
	}

	//file_put_contents('queue.txt', implode(PHP_EOL,$queue));

	passthru("php process.php '$target'");

	$processed++;

	echo PHP_EOL;
	echo "Total Processed:".$processed.PHP_EOL;
	echo "Time Taken:".(time()-$start_ts).PHP_EOL;
	echo PHP_EOL;

}
