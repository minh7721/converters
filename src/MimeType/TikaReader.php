<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2019-08-07
 * Time: 15:35
 */

namespace Colombo\Converters\MimeType;


use Vaites\ApacheTika\Client;
use Vaites\ApacheTika\Clients\WebClient;

class TikaReader implements ReaderInterface {
	
	public static $tika_server = 'http://127.0.0.1:9998';
	
	protected $client;
	
	/**
	 * TikaReader constructor.
	 *
	 * @throws \Exception
	 */
	public function __construct() {
		$this->client = Client::make(self::$tika_server);
	}
	
	
	/**
	 * Read mime type from a path
	 *
	 * @param $path
	 *
	 * @return mixed
	 */
	public function fromFile( $path ) {
		$mimetype = $this->client->getMime($path);
		return $mimetype;
	}
	
	/**
	 * Read mime type from a resource
	 *
	 * @param $resource
	 *
	 * @return mixed
	 */
	public function fromResource( $resource ) {
		// TODO: Implement fromResource() method.
	}
}