<?php

$words = scandir(__DIR__.'/words/');
$words = array_diff($words,['.','..']);
shuffle($words);
$word = current($words);

$phrases = file(__DIR__.'/words/'.$word);
shuffle($phrases);
echo str_replace('.txt','',$word).PHP_EOL;
echo current($phrases);