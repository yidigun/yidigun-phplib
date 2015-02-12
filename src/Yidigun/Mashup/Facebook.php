<?php
/**
 * Copyright 2015 Yidigun.com
 */
namespace Yidigun\Mashup;

use Yidigun\URL;

class Facebook {

	public $appId;
	public $appSecret;
	public $clientToken;
	public $ogDefault = array();

	public function __construct($config = array()) {

		$this->appId		= $config['appId'];
		$this->appSecret	= $config['appSecret'];
		$this->clientToken	= $config['clientToken'];

		foreach ($config as $key => $value) {
			if (preg_match('/^og:([a-z0-9_]+)$/i', $key, $part)) {
				$this->ogDefault[$part[1]] = $value;
			}
		}
	}

	public function meta($og = array()) {

		$title = ($og['title'])?
					($this->ogDefault['title_prefix'] . $og['title'] . $this->ogDefault['title_suffix']):
					$this->ogDefault['title'];

		$desc = ($og['desc'])?
					($this->ogDefault['desc_prefix'] . $og['desc'] . $this->ogDefault['desc_suffix']):
					$this->ogDefault['desc'];

		$image = URL::abs(($og['image'])? $og['image']: $this->ogDefault['image']);

		$url = ($og['url'])? URL::abs($og['url']): URL::current();
?>
<meta property="fb:app_id" content="<?= $this->appId ?>" />
<meta property="og:title" content="<?= $title ?>" />
<meta property="og:description" content="<?= $desc ?>" /> 
<meta property="og:url" content="<?= $url ?>" /> 
<meta property="og:image" content="<?= $image ?>" /> 
<?php
	}
	
}