<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_0_1($object)
{
		$SmartBlogPopularPosts = new SmartBlogPopularPosts();
		$SmartBlogPopularPosts->registerHook('actionsbdeletepost');
	 	$SmartBlogPopularPosts->registerHook('actionsbnewpost');
	 	$SmartBlogPopularPosts->registerHook('actionsbupdatepost');
	 	$SmartBlogPopularPosts->registerHook('actionsbtogglepost');
	 	$SmartBlogPopularPosts->registerHook('actionsbsingle');
		return true;
}
