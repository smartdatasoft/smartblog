<?php
if (!defined('_PS_VERSION_')) {
	exit;
}

function upgrade_module_3_0_2($object)
{

	$results = Db::getInstance()->executeS('SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = "' . _DB_PREFIX_ . 'smart_blog_tag" AND COLUMN_NAME = "slug"');

	if (empty($results)) {
		Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'smart_blog_tag` ADD COLUMN `slug` VARCHAR(45)  NOT NULL AFTER `name`');
	}
	$results = Db::getInstance()->executeS('SELECT  *  FROM ' . _DB_PREFIX_ . 'smart_blog_tag ');
	foreach ($results as $result) {
		if ($result['slug'] == '') {
			$slug          = str_replace(' ', '-', $result['name']);
			$result_update = Db::getInstance()->execute('UPDATE   ' . _DB_PREFIX_ . 'smart_blog_tag SET `slug` = "' . $slug . '" WHERE   `name` =  "' . $result['name'] . '"');
		}
	}
	return true;
}
