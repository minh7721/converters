<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/21/18
 * Time: 16:13
 */

namespace Colombo\Converters\MimeType;


class BuiltInReader implements ReaderInterface {
	
	public function fromFile( $path ) {
		if(!file_exists( $path )){
			throw new \Exception("File not found at " . $path);
		}
		return mime_content_type( $path );
	}
	
	public function fromResource( $resource ) {
		throw new \Exception("Not supported");
	}
}