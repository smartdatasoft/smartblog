<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_0_1($object)
{
		$smartblogarchive = new smartblogarchive();
		$smartblogarchive->registerHook('actionsbdeletepost');
		$smartblogarchive->registerHook('actionsbnewpost');
		$smartblogarchive->registerHook('actionsbupdatepost');
		$smartblogarchive->registerHook('actionsbtogglepost');
		return true;
}
