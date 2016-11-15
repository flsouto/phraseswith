<?php

require_once(__DIR__.'/vendor/autoload.php');
use Symfony\Component\DomCrawler\Crawler;

function parseEbooksURLs($letter){

	$url = "https://www.gutenberg.org/browse/titles/".strtolower($letter);

	shell_exec("wget '$url' -O tmp");

	$contents = file_get_contents("tmp");

	$crawler = new Crawler($contents);

	$links = $crawler->filter('h2:contains("English")');

	foreach($links as $link){

		$link = new Crawler($link);

		$urls[] = "https://www.gutenberg.org".$link->filter("a")->attr('href');

	}

	shell_exec("rm tmp");

	return $urls;
}

function parseEbookInfo($ebook_url){

	shell_exec("wget '$ebook_url' -O tmp");

	$crawler = new Crawler(file_get_contents("tmp"));
	$url = $crawler->filter("a:contains(Plain Text)")->attr('href');
	$title = $crawler->filter("h1")->text();

	shell_exec("rm tmp");

	return [
		'txt_url' => 'https:'.$url,
		'title' => $title
	];

}


function parseEbookText($txt_url){

	shell_exec("wget '$txt_url' -O tmp");

	$contents = file_get_contents("tmp");

	shell_exec("rm tmp");

	$start_pos = stripos($contents, "start of this project");

	if($start_pos===false){
		return false;
	}

	$end_pos = stripos($contents, "end of the project gutenberg");
	if($end_pos===false){
		$end_pos = stripos($contents, "end of this project gutenberg");
		if($end_pos===false){
			return false;
		}
	}
	
	$line = '';
	$lines = [];
	// Try to match a decent body of text to be considered the start of the REAL content
	$found = false;
	for($i=$start_pos;$i<=$end_pos;$i++){
		$char = substr($contents,$i,1);
		if($char=="\n"){
			$line = trim($line);
			if(strlen($line)>=50){
				$lines[] = $line;
				$line = '';
				if(count($lines)==4){
					$start_pos = $i - strlen(implode("\n", $lines));
					$found = true;
					break;
				}
			} else{
				$lines = [];
			}
		} else {
			$line .= $char;
		}
	}

	if(!$found){
		return false;
	}

	$end_pos-=200;

	$contents = substr($contents, $start_pos, $end_pos-$start_pos);

	$contents = substr($contents,0,strrpos($contents,'.'));

	$contents = preg_replace("/\[illustration.*?\]/i","",$contents);

	return $contents;

}

$letter = "c";

if(!is_dir($dir="crawler/$letter/")){
	mkdir($dir);
}

$urls = parseEbooksURLs($letter);

foreach($urls as $url){

	sleep((60 * rand(3,5)) + rand(11, 59) );

	try{
		$info = parseEbookInfo($url);
		$destination = $dir.str_replace(":",",",$info['title']).'.txt';
		if(file_exists($destination)){
			continue;
		}

		if(empty($info['txt_url'])||empty($info['title'])){
			continue;
		}

		$contents = parseEbookText($info['txt_url']);
		if(empty($contents)){
			continue;
		}

		touch($destination);
		file_put_contents($destination,$contents);		

	} catch(Exception $e) {

	}

}











