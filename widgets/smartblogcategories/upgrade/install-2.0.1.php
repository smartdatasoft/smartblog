<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_0_1($object)
{
		$SmartBlogCategories = new SmartBlogCategories();
		$SmartBlogCategories->registerHook('actionsbdeletecat');
		$SmartBlogCategories->registerHook('actionsbnewcat');
		$SmartBlogCategories->registerHook('actionsbupdatecat'); 
		$SmartBlogCategories->registerHook('actionsbtogglecat'); 
		return true;
}
