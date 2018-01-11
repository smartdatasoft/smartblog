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
    {capture name=path}
      <a href="{smartblog::GetSmartBlogLink('smartblog')|escape:'htmlall':'UTF-8'}">{l s='All Blog News' mod='smartblog'}</a>
      {if $title_category != ''}<span class="navigation-pipe"></span>{$title_category|escape:'htmlall':'UTF-8'}{/if}
    {/capture}
    {if $postcategory == ''}
        {if $title_category != ''}
              <div class="alert alert-danger"><p>There is 1 error</p><ol><li>{l s='No Post in Category' mod='smartblog'}</li></ol></div>
        {else}
          <div class="alert alert-danger"><p>There is 1 error</p><ol><li>{l s='No Post in Blog' mod='smartblog'}</li></ol></div>
        {/if}
    {else}
      {if $smartdisablecatimg == '1'}
        {assign var="activeimgincat" value='0'}
          {$activeimgincat = $smartshownoimg} 
          {if $title_category != ''}        
            {foreach from=$categoryinfo item=category}
              <div id="sdsblogCategory">  
                {if $cat_image == "no" } 
                {else} 
                  {if ($cat_image != "no" && $activeimgincat == 0) || $activeimgincat == 1}
                    <img alt="{$category.meta_title|escape:'htmlall':'UTF-8'}" src="{$cat_image}" class="imageFeatured">
                  {/if}
                {/if}
                {$category.description}
              </div>
            {/foreach}  
          {/if}
        {/if}
        <div id="smartblogcat" class="block">
          {foreach from=$postcategory item=post}
            {include file="module:smartblog/views/templates/front/category_loop.tpl" postcategory=$postcategory}
          {/foreach}
        </div>
        {if !empty($pagenums)}
          <div class="row bottom-pagination-content">
            <div class="post-page col-md-12">
              <div id="pagination_bottom" class="col-md-6">
                <ul class="pagination">
                  {for $k=0 to $pagenums} 
                    {if ($k+1) == $c}
                      <li><span class="page-link page-active"><span>{$k+1|escape:'htmlall':'UTF-8'}</span></span></li>
                    {else}
                      {if $title_category != ''}
                        <li><a class="page-link" href="{$smartbloglink->getSmartBlogCategoryPagination($id_category,$cat_link_rewrite,$k+1)|escape:'htmlall':'UTF-8'}"><span>{$k+1|escape:'htmlall':'UTF-8'}</span></a></li> 
                      {else}
                          <li><a class="page-link" href="{$smartbloglink->getSmartBlogListPagination($k+1)|escape:'htmlall':'UTF-8'}"><span>{$k+1|escape:'htmlall':'UTF-8'}</span></a></li>
                      {/if}
                    {/if}
                  {/for}
                </ul>
              </div>
            </div>
            <div class="col-md-6">
              <div class="results">
                {l s='Showing' mod='smartblog'} {if $limit_start!=0}{$limit_start|escape:'htmlall':'UTF-8'}{else}1{/if} {l s='to' mod='smartblog'} {if $limit_start+$limit >= $total}{$total|escape:'htmlall':'UTF-8'}{else}{$limit_start+$limit|escape:'htmlall':'UTF-8'}{/if} {l s='of' mod='smartblog'} {$total|escape:'htmlall':'UTF-8'} ({$c|escape:'htmlall':'UTF-8'} {l s='Pages' mod='smartblog'})
              </div>
            </div>
          </div>
        {/if}
      {/if}
      {if isset($smartcustomcss)}
        <style>
          {$smartcustomcss|escape:'htmlall':'UTF-8'}
        </style>
      {/if}
{/block}