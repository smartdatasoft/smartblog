<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_0_1($object)
{
		$SmartblogLatestComments = new SmartblogLatestComments();
		$SmartblogLatestComments->registerHook('actionsbpostcomment');
		return true;
}
