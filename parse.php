<?php
$books = [];
$dir = __DIR__.'/adelaide/extracted/';
foreach(scandir($dir) as $file){
	if(substr($file,-4)=='.txt'){
		$books[] = $dir.$file;
	}
}

$result = [];
$total = 0;

while(!empty($books)){

	$file = array_shift($books);
	
	shell_exec('php '.__DIR__.'/parse_book.php "'.$file.'"');	

	echo "Remaining: ".count($books).PHP_EOL;

	break;

}






