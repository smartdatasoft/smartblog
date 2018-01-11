<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_0_2($object)
{
	 $res = Configuration::updateValue('SMARTBBLOG_ADD_THIS_API_KEY', '');
	 return (bool)$res;
}
