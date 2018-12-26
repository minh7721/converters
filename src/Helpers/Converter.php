<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/21/18
 * Time: 15:32
 */

namespace Colombo\Converters\Helpers;


use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Drivers\ConverterInterface;
use Colombo\Converters\Drivers\JodConverter;
use Colombo\Converters\Drivers\PdfToHtml;
use Colombo\Converters\Drivers\PdfToText;
use Colombo\Converters\Exceptions\ConvertException;

class Converter {
	
	const MSG_TIME = 1000;
	const MSG_OUTPUT = 1001;
	
	protected $input;
	protected $inputFormat;
	protected $outputFormat;
	protected $startPage;
	protected $endPage;
	protected $converterMapping = [
		'doc' => [
			'docx' => '',
			'html' => JodConverter::class,
			'pdf' => JodConverter::class,
			'txt' => JodConverter::class,
		],
		'docx' => [
			'html' => JodConverter::class,
			'txt' => JodConverter::class,
			'xml' => JodConverter::class,
			'pdf' => JodConverter::class,
		],
		'pdf' => [
			'html' => PdfToHtml::class,
			'xml'  => PdfToHtml::class,
			'text' => PdfToText::class,
			'txt'  => PdfToText::class,
		]
	];
	
	/** @var  ConverterInterface */
	protected $converter;
	/** @var  ConverterInterface always use this converter if it was assigned */
	protected $force_converter;
	protected $mimeHelper;
	
	protected $result;
	
	/**
	 * Init class with path to file
	 *
	 * @param string $path
	 *
	 * @throws \Exception
	 */
	public function __construct( $path = '' ) {
		if ( !empty($path) && ! file_exists( $path ) ) {
			throw new \Exception( "File not found at " . $path );
		}
		$this->input = $path;
		
		$this->mimeHelper = new Mime();
		
		if(!empty($path)){
			$this->inputFormat = $this->mimeHelper->getExtension( $path );
		}
	}
	
	/**
	 * @return string
	 */
	public function getInput(): string {
		return $this->input;
	}
	
	/**
	 * @param string $input
	 */
	public function setInput( string $input ) {
		$this->input = $input;
		$this->inputFormat = $this->mimeHelper->getExtension( $input );
	}
	
	/**
	 * @return string
	 */
	public function getInputFormat(): string {
		return $this->inputFormat;
	}
	
	/**
	 * @param string $inputFormat
	 */
	public function setInputFormat( string $inputFormat ) {
		$this->inputFormat = $inputFormat;
	}
	
	/**
	 * @return string
	 */
	public function getOutputFormat() {
		return $this->outputFormat;
	}
	
	/**
	 * @param string $outputFormat
	 */
	public function setOutputFormat( $outputFormat ) {
		$this->outputFormat = $outputFormat;
	}
	
	/**
	 * @return integer
	 */
	public function getStartPage(): int {
		return $this->startPage;
	}
	
	/**
	 * @param integer $startPage
	 */
	public function setStartPage( $startPage ) {
		$this->startPage = $startPage;
	}
	
	/**
	 * @return integer
	 */
	public function getEndPage(): int
	{
		return $this->endPage;
	}
	
	/**
	 * @param integer $endPage
	 */
	public function setEndPage( int $endPage )
	{
		$this->endPage = $endPage;
	}
	
	/**
	 * @return ConverterInterface
	 */
	public function getConverter() {
		return $this->converter;
	}
	
	/**
	 * @return ConverterInterface
	 */
	public function getForceConverter() {
		return $this->force_converter;
	}
	
	/**
	 * Set force converter to use it for any format
	 * @param ConverterInterface $converter
	 */
	public function setForceConverter( ConverterInterface $converter ) {
		$this->force_converter = $converter;
	}
	
	/**
	 * Change converter options
	 */
	public function options() {
	
	}
	
	/**
	 * $converter_options can be a string(class name) or array
	 * [
	 *  'class' => ConverterClassName,
	 *  'options' => [], // options for converter
	 * ]
	 *
	 * @param array $options
	 *
	 * @throws ConvertException
	 */
	protected function makeConverter($options = []){
		$converter_options = array_get($this->converterMapping, $this->getInputFormat() . "." . $this->getOutputFormat());
		if(!is_array( $converter_options )){
			$converter_options = [
				'class' => $converter_options,
				'options' => $options,
			];
		}else{
			$converter_options['options'] = isset($converter_options['options']) ?
				array_merge( $converter_options['options'], $options) : $options;
		}
		try{
			if($this->force_converter){
				$this->force_converter->options($options);
			}else{
				$this->converter = new $converter_options['class'];
				$this->converter->options($converter_options['options']);
			}
		}catch (\Exception $ex){
			throw new ConvertException("Can not make converter " . $converter_options['class']);
		}
		
	}
	
	/**
	 * Call convert process
	 *
	 * @param array $options
	 *
	 * @return ConvertedResult
	 */
	public function run($options = []): ConvertedResult{
		$this->makeConverter($options);
		
		$converter = $this->force_converter ?: $this->converter;
		
		if($this->startPage){
			$converter->startPage( $this->startPage);
		}
		if($this->endPage){
			$converter->endPage( $this->endPage);
		}
		$start = microtime(true);
		$convertedResult = $converter->convert( $this->getInput(),
			$this->getOutputFormat(),
			$this->getInputFormat()
		);
		$runtime = microtime(true) - $start;
		$convertedResult->addMessages( $runtime , self::MSG_TIME);
		return $convertedResult;
	}
	
}