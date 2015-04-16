<?php

namespace LinkerUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class LinkerUserBundle extends Bundle
{
	public function getParent()
	{
		return 'FOSUserBundle';
	}

}