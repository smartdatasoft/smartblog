<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_0_2($object)
{

	Configuration::deleteByName('SMART_BLOG_CATEGORIES_DROPDOWN') ; 
		return true;
}
