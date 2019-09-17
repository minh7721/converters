<?php


ini_set( 'display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . "/vendor/autoload.php";

$input = __DIR__ . "/files/cv.pdf";

$converter = new \Colombo\Converters\Helpers\Converter($input);

// force custom converter
$pdftohtml = new \Colombo\Converters\Drivers\PdfToHtml();

$converter->setMappingConverter( 'pdf', 'html', $pdftohtml);
$converter->setMappingConverter( 'pdf', 'xml', \Colombo\Converters\Drivers\PdfToXml::class);
$converter->setMappingConverter( 'pdf', 'txt', new \Colombo\Converters\Drivers\PdfToText());

$converter->setOutputFormat( 'html');

$result = $converter->run();

echo $result->getContent();
