<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_0_4($object)
{
	Configuration::updateValue('SMART_BLOG_ARCHIVE_DHTML', 0);
	return true;
}
