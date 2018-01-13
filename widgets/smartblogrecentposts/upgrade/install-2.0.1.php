<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_0_1($object)
{
		$SmartBlogRecentPosts = new SmartBlogRecentPosts();
		$SmartBlogRecentPosts->registerHook('actionsbdeletepost');
		$SmartBlogRecentPosts->registerHook('actionsbnewpost');
		$SmartBlogRecentPosts->registerHook('actionsbupdatepost');
		$SmartBlogRecentPosts->registerHook('actionsbtogglepost');
		return true;
}
