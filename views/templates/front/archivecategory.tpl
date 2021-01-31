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
        <nav class="breadcrumb">
          <ol>
              <li>
                <a href="{$breadcrumb.links[0].url}">
                  <span>{$breadcrumb.links[0].title}</span>
                </a>
              </li>
              <li>
                <a href="{smartblog::GetSmartBlogLink('smartblog')}">
                <span>{l s='All Post' mod='smartblog'}</span>
                </a>
              </li>
                {if $year}
                  <li>
                      {assign var="linkurl_year" value=null}
                      {$linkurl_year.year = $year}  
                    <a href="{smartblog::GetSmartBlogLink('smartblog_year',$linkurl_year)}">
                    <span>{l s=$year mod='smartblog'}</span>
                    </a>
                  </li>
                {/if}
                {if $month}
                  <li>
                    {assign var="linkurl" value=null}
                    {$linkurl.year = $year}
                    {$linkurl.month = $month}
                    <a href="{smartblog::GetSmartBlogLink('smartblog_month',$linkurl)}">
                    <span>{l s=$month_name mod='smartblog'}</span>
                    </a>
                  </li>
                {/if}
                {if $day}
                  <li>
                    {assign var="linkurl" value=null}
                    {$linkurl.year = $year}
                    {$linkurl.month = $month}
                    {$linkurl.day = $day}
                    <a href="{smartblog::GetSmartBlogLink('smartblog_day',$linkurl)}">
                    <span>{l s=$day mod='smartblog'}</span>
                    </a>
                  </li>
                {/if}
              {if $title_category != ''}
             {assign var="link_detail" value=null}
            {$link_detail.id_post = $id_post} 
            {$link_detail.slug = $link_rewrite_}
              <li>
                <a href="{smartblog::GetSmartBlogLink('smartblog_post',$link_detail)}">
                <span>{$meta_title}</span>
                </a>
              </li>
            {/if}
          </ol>
        </nav>
    {/if}
{/block}
{block name='page_content'}
    {capture name=path}<a href="{smartblog::GetSmartBlogLink('smartblog')|escape:'htmlall':'UTF-8'}">{l s='All Blog News' mod='smartblog'}</a>
         {if $title_category != ''}
        <span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>{$title_category|escape:'htmlall':'UTF-8'}{/if}{/capture}
        {if $postcategory == ''}
                 <p class="error">{l s='No Post in Archive' mod='smartblog'}</p>
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