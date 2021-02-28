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
{extends file='page.tpl'}
{block name='breadcrumb'}
  {if isset($breadcrumb)}
    <nav class="breadcrumb smart-blog-breadcrumb">
      <ol>
          <li>
            <a href="{$breadcrumb.links[0].url}">
              <span itemprop="name">{$breadcrumb.links[0].title}</span>
            </a>
          </li>
          <li>
            <a href="{smartblog::GetSmartBlogLink('smartblog')}">
            <span itemprop="name">{l s='All Post' mod='smartblog'}</span>
            </a>
          </li>
          {if $title_category != ''}
          {assign var="link_category" value=null}
          {$link_category.id_category = $id_category}
          {$link_category.slug = $cat_link_rewrite}
          <li>
            <a href="{smartblog::GetSmartBlogLink('smartblog_category',$link_category)}">
            <span itemprop="name">{$title_category}</span>
            </a>
          </li>
        {/if}
      </ol>
    </nav>
  {/if}
{/block}
{block name='page_content'}
    {if $postcategory == ''}
        {include file="module:smartblog/views/templates/front/search-not-found.tpl" postcategory=$postcategory}
    {else}
        <div id="smartblogcat" class="block">
            {foreach from=$postcategory item=post}
                {include file="module:smartblog/views/templates/front/category_loop.tpl" postcategory=$postcategory}
            {/foreach}
        </div>
    {/if}
    {if isset($smartcustomcss)}
        <style>
            {$smartcustomcss|escape:'htmlall':'UTF-8'}
        </style>
    {/if}
{/block}