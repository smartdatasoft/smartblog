<?php
if (!defined('_PS_VERSION_')) {
	exit;
}

function upgrade_module_3_0_3($object)
{
	$object->registerhook('displayDashboardTop');

	$sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'smart_blog_post_related  CHANGE related_post_id related_poroduct_id varchar(100)';
	Db::getInstance()->execute($sql);

	Db::getInstance()->execute(' INSERT INTO `' . _DB_PREFIX_ . 'smart_blog_post_related` (`id_smart_blog_post`, `related_poroduct_id`)
				
		SELECT id_smart_blog_post,associations FROM ' . _DB_PREFIX_ . 'smart_blog_post as post
		
		WHERE NOT EXISTS (SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post_related WHERE id_smart_blog_post=post.id_smart_blog_post) ');
	return true;
}
