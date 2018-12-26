<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/21/18
 * Time: 16:06
 */

namespace Colombo\Converters\MimeType;


interface ReaderInterface {
	
	/**
	 * Read mime type from a path
	 * @param $path
	 *
	 * @return mixed
	 */
	public function fromFile($path);
	
	/**
	 * Read mime type from a resource
	 * @param $resource
	 *
	 * @return mixed
	 */
	public function fromResource($resource);
	
}