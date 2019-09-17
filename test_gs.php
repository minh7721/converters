<?php


ini_set( 'display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . "/vendor/autoload.php";

$input = __DIR__ . "/files/test.pdf";

$tmp = __DIR__ . "/tmp";

$tmp = __DIR__ . "/tmp";
$tmpFolder = new \Colombo\Converters\Helpers\TemporaryDirectory($tmp);
//$tmpFolder->autoDestroyed(false);
//dd($tmpFolder->clean(1));


$converter = new \Colombo\Converters\Helpers\Converter(null );

$converter->setInput($input);

// force custom converter
//$converter->setForceConverter( new \Colombo\Converters\Drivers\GS('', $tmpFolder) );
$converter->setMappingConverter( 'pdf', 'pdf', new \Colombo\Converters\Drivers\GS('', $tmpFolder));

$converter->setOutputFormat( 'pdf');

$result = $converter->run();

$result->saveTo( __DIR__ . "/output_gs.pdf" );

//echo $result->getContent();
