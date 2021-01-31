{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="pagenotfound" class="row">
												<div class="center_column col-xs-12 col-sm-12" id="center_column">
	<div class="pagenotfound">
	
	<h1>{l s='Sorry, but nothing matched your search terms.' mod='smartblog'}</h1>

	<p>
		{l s='Please try again with some different keywords.' mod='smartblog'}
	</p>

	
	<form class="std" method="post" action="{smartblog::GetSmartBlogLink('smartblog_search')|escape:'htmlall':'UTF-8'}">
		<fieldset>
			<div>
				
		 
				<input type="text" class="form-control grey" value="{$smartsearch|escape:'htmlall':'UTF-8'}" name="smartsearch" id="search_query">
                <button class="btn btn-default button button-small" value="{l s='Ok' mod='smartblog'}" name="smartblogsubmit" type="submit"><span>{l s='Ok' mod='smartblog'}</span></button>
			</div>
		</fieldset>
	</form>

	<div class="buttons"><a title="Home" href="{smartblog::GetSmartBlogLink('smartblog')|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-medium"><span><i class="icon-chevron-left left"></i>{l s='Home page' mod='smartblog'}</span></a></div>
</div>
					</div>
										</div>