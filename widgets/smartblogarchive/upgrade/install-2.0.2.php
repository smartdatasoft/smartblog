<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_0_2($object)
{
	Configuration::updateValue('smartblogarchive_type', 2);
	return true;
}
