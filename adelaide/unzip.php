<?php

foreach(scandir('downloads') as $file){
	if(substr($file,-4)=='.zip'){
		$name = trim(substr($file,0,-4));
		shell_exec('unzip "downloads/'.$file.'" -d "unzipped/'.$name.'" ');
	}
}