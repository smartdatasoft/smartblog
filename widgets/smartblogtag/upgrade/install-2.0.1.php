<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_0_1($object)
{
		$SmartBlogTag = new SmartBlogTag();
		$SmartBlogTag->registerHook('actionsbdeletepost');
		$SmartBlogTag->registerHook('actionsbnewpost');
		$SmartBlogTag->registerHook('actionsbupdatepost');
		$SmartBlogTag->registerHook('actionsbtogglepost');
		return true;
}
