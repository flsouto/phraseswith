<?php

require_once(__DIR__.'/../vendor/autoload.php');

use Symfony\Component\DomCrawler\Crawler;

$books = scandir(__DIR__.'/unzipped/');
$books = array_diff($books,[".",".."]);

while(!empty($books)){

	$book = array_shift($books);

	$files = scandir('unzipped/'.$book);

	$text = '';

	foreach($files as $file){
		if(substr($file,-5)=='.html'){
			$contents = file_get_contents("unzipped/$book/$file");
			$crawler = new Crawler($contents);
			$ps = $crawler->filter(".chapter p");
			if(count($ps)){
				foreach($ps as $p){
					$text .= (new Crawler($p))->text();
					$text .= PHP_EOL;
				}
			}
		}
	}

	if(!empty($text)){
		file_put_contents("extracted/$book.txt",$text);
	}

	echo 'Remaining: '.count($books).PHP_EOL;

}
