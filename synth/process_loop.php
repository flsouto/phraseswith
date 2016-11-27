<?php

$queue = file('queue.txt');

while(!empty($queue)){

	$target = trim(array_shift($queue));

	if(empty($target)){
		continue;
	}

	file_put_contents('queue.txt', implode(PHP_EOL,$queue));

	passthru("php process.php '$target'");

}