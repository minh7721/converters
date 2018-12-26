<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/21/18
 * Time: 16:39
 */

namespace Colombo\Converters;


use Colombo\Converters\Exceptions\Result\CanNotWriteResultException;
use Colombo\Converters\Helpers\TemporaryDirectory;
use Illuminate\Filesystem\Filesystem;

class ConvertedResult {
	
	/** @var  TemporaryDirectory */
	protected $temDir;
	protected $files = [];
	protected $isMultiFile = false;
	protected $content;
	protected $filesystem;
	protected $isSuccess = false;
	protected $messages = [
//		'code' => 'message',
	];
	protected $errors = [
//		'code' => 'message',
	];
	
	/**
	 * ConvertedResult constructor.
	 *
	 * @param $filesystem
	 */
	public function __construct( $filesystem = null ) {
		$this->filesystem = new Filesystem();
	}
	
	public function getContent(){
		if($this->isMultiFile){
			return $this->files;
		}else{
			return $this->content;
		}
	}
	
	public function setContent($content){
		if(!$this->isSuccess){
			$this->isSuccess = true;
		}
		$this->content = $content;
	}
	
	public function addContent($content, $path){
	
	}
	
	public function saveTo($path, $force = false, $mode = 0755){
		if($this->isMultiFile){
			// check dir
			if($this->filesystem->exists( $path )){
				if($this->filesystem->isDirectory( $path )){
					throw new CanNotWriteResultException("Path should be a directory, got " . $path);
				}
				if(!$this->filesystem->isWritable( $path)){
					throw new CanNotWriteResultException("Can not write to " . $path);
				}
			}else{
				$isMade = $this->filesystem->makeDirectory( $path, $mode, true, $force);
				if(!$isMade){
					throw new CanNotWriteResultException("Can not create " . $path);
				}
			}
			$path = rtrim( $path, "/");
			foreach ($this->files as $file){
				$this->filesystem->copy( $file, $path . "/" . basename( $file));
			}
			return count($this->files);
		}else{
			if(file_exists( $path ) && !$force){
				throw new CanNotWriteResultException("file existed at " . $path);
			}
			$this->filesystem->put( $path, $this->content );
		}
	}
	
	/**
	 * @return bool
	 */
	public function isMultiFile(): bool {
		return $this->isMultiFile;
	}
	
	/**
	 * @param bool $isMultiFile
	 */
	public function setIsMultiFile( bool $isMultiFile ) {
		$this->isMultiFile = $isMultiFile;
	}
	
	/**
	 * @return Filesystem
	 */
	public function getFilesystem(): Filesystem {
		return $this->filesystem;
	}
	
	/**
	 * @param Filesystem $filesystem
	 */
	public function setFilesystem( Filesystem $filesystem ) {
		$this->filesystem = $filesystem;
	}
	
	/**
	 * @return bool
	 */
	public function isSuccess(): bool {
		return $this->isSuccess;
	}
	
	/**
	 * @param bool $isSuccess
	 */
	public function setIsSuccess( bool $isSuccess ) {
		$this->isSuccess = $isSuccess;
	}
	
	/**
	 * @return array
	 */
	public function getMessages(): array {
		return $this->messages;
	}
	
	/**
	 * @param $message
	 * @param null $code
	 *
	 * @return bool
	 * @internal param array $messages
	 */
	public function addMessages( $message, $code = null ) {
		if(is_array( $message)){
			foreach ($message as $m){
				$this->addMessages( $m);
			}
			return true;
		}
		if($code){
			$this->messages[$code] = $message;
		}else{
			$this->messages["__" . count($this->errors)] = $message;
		}
	}
	
	/**
	 * @return array
	 */
	public function getErrors(): array {
		return $this->errors;
	}
	
	/**
	 * @param $message
	 * @param null $code
	 *
	 * @return bool
	 * @internal param array $errors
	 */
	public function addErrors( $message, $code = null) {
		if(is_array( $message)){
			foreach ($message as $m){
				$this->addErrors( $m);
			}
			return true;
		}
		if($code){
			$this->errors[$code] = $message;
		}else{
			$this->errors["__" . count($this->errors)] = $message;
		}
	}
	
}