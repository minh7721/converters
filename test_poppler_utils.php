<?php


ini_set( 'display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . "/vendor/autoload.php";

$input = __DIR__ . "/files/cv.pdf";

$converter = new \Colombo\Converters\Helpers\Converter($input);

// force custom converter
$converter->setMappingConverter( 'pdf', 'html', new \Colombo\Converters\Drivers\PdfToHtml());
$converter->setMappingConverter( 'pdf', 'xml', new \Colombo\Converters\Drivers\PdfToXml());
$converter->setMappingConverter( 'pdf', 'txt', new \Colombo\Converters\Drivers\PdfToText());

$converter->setOutputFormat( 'txt');

$result = $converter->run();

echo $result->getContent();
