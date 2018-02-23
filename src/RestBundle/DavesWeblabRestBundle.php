<?php

namespace DavesWeblab\RestBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class DavesWeblabRestBundle extends AbstractPimcoreBundle
{
	public function getCssPaths() {
		return [
			"/assets/css/daves-weblab-theme.css"
		];
	}
}
