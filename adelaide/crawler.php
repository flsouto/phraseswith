<?php

require_once(__DIR__.'/../vendor/autoload.php');

use Symfony\Component\DomCrawler\Crawler;

$base_url = "https://ebooks.adelaide.edu.au/";

function parseEbooksList($letter){

	global $base_url;

	$letter = strtoupper($letter);

	$url = $base_url."meta/titles/$letter.html";

	shell_exec("wget '$url' -O $letter.html");

	$contents = file_get_contents("$letter.html");

	shell_exec("rm $letter.html");

	$crawler = new Crawler($contents);
	$links = $crawler->filter(".works li a");

	$result = [];

	foreach($links as $link){
	
		$link = new Crawler($link);
		$item = [];
		
		$name = str_replace(' / ',' by ',$link->text());
		$name = str_replace(':',',',$name);
		$name = str_replace('[]','',$name);

		if(!strstr($name,' by ')){
			continue;
		}
		if(strstr($name,';')||strstr($name,':')||strstr($name,',')){
			continue;
		}

		if(strlen($name) > 100){
			continue;
		}

		$item['url'] = $base_url.ltrim($link->attr('href'),'/');
		$item['name'] = $name;

		$result[] = $item;
	}

	return $result;

}

$letter = "N";
$queue_file = "queues/$letter.json";
$queue = [];

function saveQueue(){
	global $queue_file, $queue;
	file_put_contents($queue_file, json_encode($queue, JSON_PRETTY_PRINT));
}

if(file_exists($queue_file)){
	$queue = json_decode(file_get_contents($queue_file),true);
} else {
	$queue = parseEbooksList($letter);	
	saveQueue();
}

while(!empty($queue)){

	$item = array_shift($queue);

	saveQueue();

	$dl_url = str_replace($base_url, "",$item['url']);
	$dl_url = $base_url."cgi-bin/zip/".$dl_url;

	shell_exec('wget "'.$dl_url.'" -O "downloads/'.$item['name'].'.zip"');

	echo count($queue)." items remaining...".PHP_EOL;
	sleep(rand(30,60));

}