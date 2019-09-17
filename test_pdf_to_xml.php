<?php


ini_set( 'display_errors', 1);

require __DIR__ . "/vendor/autoload.php";

$input = __DIR__ . "/files/test.pdf";

$converter = new \Colombo\Converters\Helpers\Converter();

$converter->setInput($input);

$tmp = __DIR__ . "/tmp";

// force custom converter
$converter->setForceConverter( new \Colombo\Converters\Drivers\PdfToXml() );
$converter->setOutputFormat( 'html');

$result = $converter->run();

echo $result->getContent();
