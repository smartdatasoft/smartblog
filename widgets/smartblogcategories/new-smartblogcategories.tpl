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
	<div class="block-categories hidden-sm-down">
		<h4 class="text-uppercase h6 hidden-sm-down"><a href="{smartblog::GetSmartBlogLink('smartblog')}">{l s='Category' mod='smartblogpopularposts'}</a></h4>
		<select onchange="document.location.href=this.options[this.selectedIndex].value;">
			<option value="">Select Category</option>
			{include file="./category-tree-branch.tpl" node=$blockCategTree last='true' select='false'}
		</select>
	</div>
{else}
	{function name="blockCategTree" nodes=[] depth=0}
	  {strip}
	    {if $nodes|count}
	      <ul class="category-sub-menu">
	        {foreach from=$nodes item=node}
	        	{if $node.name != ''}
		          <li data-depth="{$depth}">
		            {if $depth===0}
		              <a href="{$node.link}">{$node.name}</a>
		              {if $node.children}
		              	
			              	{if $isDhtml}
				                <div class="navbar-toggler collapse-icons" data-toggle="collapse" data-target="#exBlogCollapsingNavbar{$node.id}">
				                  <i class="material-icons add">&#xE145;</i>
				                  <i class="material-icons remove">&#xE15B;</i>
				                </div>
			                {/if}
			            
		                <div class="{if $isDhtml}collapse{/if}" id="exBlogCollapsingNavbar{$node.id}">
		                  {blockCategTree nodes=$node.children depth=$depth+1}
		                </div>
		              {/if}
		            {else}
		              <a class="category-sub-link" href="{$node.link}">{$node.name}</a>
		              {if $node.children}
		              	{if $isDhtml}
			                <span class="arrows" data-toggle="collapse" data-target="#exBlogCollapsingNavbar{$node.id}">
			                  <i class="material-icons arrow-right">&#xE315;</i>
			                  <i class="material-icons arrow-down">&#xE313;</i>
			                </span>
			            {/if}
		                <div class="{if $isDhtml}collapse{/if}" id="exBlogCollapsingNavbar{$node.id}">
		                  {blockCategTree nodes=$node.children depth=$depth+1}
		                </div>
		              {/if}
		            {/if}
		          </li>
		        {/if}
	        {/foreach}
	      </ul>
	    {/if}
	  {/strip}
	{/function}

	<div class="block-categories hidden-sm-down">
		<h4 class="text-uppercase h6 hidden-sm-down"><a href="{smartblog::GetSmartBlogLink('smartblog')}">{l s='Category' mod='smartblogpopularposts'}</a></h4>
	  <ul class="category-top-menu">
	    <li><a class="text-uppercase h6" href="{$blockCategTree.link nofilter}">{$blockCategTree.name}</a></li>
	    <li>{blockCategTree nodes=$blockCategTree.children}</li>
	  </ul>
	</div>
{/if}