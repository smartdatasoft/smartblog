{*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $isDropdown}
	{if $select == 'true'}
	<select onchange="document.location.href=this.options[this.selectedIndex].value;">
		<option value="">Select Category</option>
	{/if}
		{if $node.name == ''}
				{if $node.children|@count > 0}
					{foreach from=$node.children item=child name=categoryTreeBranch}
						{if $smarty.foreach.categoryTreeBranch.last}
							{include file="$branche_tpl_path" node=$child last='true' select='false'}
						{else}
							{include file="$branche_tpl_path" node=$child last='false' select='false'}
						{/if}
					{/foreach}
				{/if}
		{else}
			<option value="{$node.link}" class="category_{$node.id}{if isset($last) && $last == 'true'} last{/if}" {if $node.id_cat == $node.id}selected="selected"{/if}>
				<a href="{$node.link|escape:'html':'UTF-8'}" {if isset($currentCategoryId) && $node.id == $currentCategoryId}class="selected"{/if}
					title="{$node.desc|strip_tags|trim|truncate:255:'...'|escape:'html':'UTF-8'}">{$node.level_depth}{$node.name|escape:'html':'UTF-8'}</a>
				{if $node.children|@count > 0}
					{foreach from=$node.children item=child name=categoryTreeBranch}
						{if $smarty.foreach.categoryTreeBranch.last}
							{include file="$branche_tpl_path" node=$child last='true' select='false'}
						{else}
							{include file="$branche_tpl_path" node=$child last='false' select='false'}
						{/if}
					{/foreach}
				{/if}
			</option>
		{/if}
	{if $select == 'true'}
	</select>
	{/if}
{else}
	{if $node.name == ''}
			{if $node.children|@count > 0}
				{foreach from=$node.children item=child name=categoryTreeBranch}
					{if $smarty.foreach.categoryTreeBranch.last}
						{include file="$branche_tpl_path" node=$child last='true' select='false'}
					{else}
						{include file="$branche_tpl_path" node=$child last='false' select='false'}
					{/if}
				{/foreach}
			{/if}
	{else}
		<li class="category_{$node.id}{if isset($last) && $last == 'true'} last{/if}">
			<a href="{$node.link|escape:'html':'UTF-8'}" {if isset($currentCategoryId) && $node.id == $currentCategoryId}class="selected"{/if}
				title="{$node.desc|strip_tags|trim|truncate:255:'...'|escape:'html':'UTF-8'}">{$node.name|escape:'html':'UTF-8'}</a>
			{if $node.children|@count > 0}
				<ul>
				{foreach from=$node.children item=child name=categoryTreeBranch}
					{if $smarty.foreach.categoryTreeBranch.last}
						{include file="$branche_tpl_path" node=$child last='true'}
					{else}
						{include file="$branche_tpl_path" node=$child last='false'}
					{/if}
				{/foreach}
				</ul>
			{/if}
		</li>
	{/if}
{/if}