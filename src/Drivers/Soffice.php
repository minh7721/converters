<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2/13/19
 * Time: 17:09
 */

namespace Colombo\Converters\Drivers;


use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Exceptions\ConvertException;
use Colombo\Converters\Helpers\TemporaryDirectory;
use Colombo\Converters\Process\CanRunCommand;

class Soffice extends CanRunCommand implements ConverterInterface {
	
	protected $bin = 'soffice';
	protected $process_options = [
		'--headless' => true,
		'--norestore' => true,
		'--nolockcheck' => true,
		'--convert-to' => '',
		'--outdir' => '',
	];
	
	protected $writer_aliases = [
		'html' => 'html:HTML:EmbedImages',
	];
	
	protected $user_installation = '';
	
	/** @var  TemporaryDirectory */
	protected $tmpFolder;
	
	public function __construct( $bin = '', $tmp = '') {
		parent::__construct( $bin );
		if($tmp instanceof TemporaryDirectory){
			$this->tmpFolder = $tmp;
		}else{
			$this->setTmp($tmp);
		}
	}
	
	public function setTmp( $location ) {
		if($this->tmpFolder){
			$this->tmpFolder->empty();
		}
		$this->tmpFolder = new TemporaryDirectory( $location );
		$this->tmpFolder->create();
		return $this->tmpFolder->path();
	}
	
	public function options( $key = null, $value = null ) {
		if ( $key == 'tmp' ) {
			$this->setTmp( $value );
		} else {
			return parent::options( $key, $value );
		}
	}
	
	protected function buildCommand($path = ''){
		$command = $this->bin;
		$command .= " " . $this->buildOptions();
		return $command . " " . $this->user_installation . " " . $path;
	}
	
	/**
	 * @param $path
	 * @param $outputFormat
	 * @param $inputFormat
	 *
	 * @return ConvertedResult
	 */
	public function convert( $path, $outputFormat, $inputFormat = '' ): ConvertedResult {
		$outdir = $this->tmpFolder->path();
		$this->user_installation = "-env:UserInstallation=\"file://" . $outdir . DIRECTORY_SEPARATOR . "tmp\"";
		// set convert-to
		$this->options('--convert-to', array_get($this->writer_aliases, $outputFormat, $outputFormat) );
		$this->options('--outdir', $outdir);
		$out_name = preg_replace( "/\.[^\.]*$/", "", basename( $path ) );
		$out_file = $outdir . DIRECTORY_SEPARATOR . $out_name . "." . $outputFormat;
		
//		die($out_file);
		
		$result = new ConvertedResult();
		
		$command = $this->buildCommand($path);
		try{
			$this->run( $command );
			
			if(!file_exists( $out_file )){
				$result->addErrors( "Can not convert", 500);
			}else{
				$result->setContent( file_get_contents( $out_file ));
			}
		}catch (\RuntimeException $ex){
			$result->addErrors( $ex->getMessage(), $ex->getCode());
		}
		return $result;
	}
	
	public function startPage( int $page ) {
		// Not supported
	}
	
	public function endPage( int $page ) {
		// Not supported
	}
}