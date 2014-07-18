<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_0_1($object)
{
		upgrade_addquickaccess();
		Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'smart_blog_post` ADD is_featured int(11) DEFAULT NULL');
		$smartblog = new smartblog();
		$smartblog->registerHook('displayBackOfficeHeader');
		$smartblog->SmartHookInsert();
		Configuration::updateGlobalValue('smartshowviewed', '1');
		Configuration::updateGlobalValue('smartcaptchaoption', '1');
		Configuration::updateGlobalValue('smartdisablecatimg','1'); 
		return true;
}
function upgrade_addquickaccess(){
		$link = new Link();
		$qa = new QuickAccess();
		$qa->link = $link->getAdminLink('AdminModules').'&configure=smartblog';
		$languages = Language::getLanguages(false);
			foreach ($languages as $language){
		$qa->name[$language['id_lang']] = 'Smart Blog Setting';
			}
		$qa->new_window = '0';
		
		if($qa->save()){
		Configuration::updateValue('smartblog_quick_access',$qa->id);
		}
		return true;
	}