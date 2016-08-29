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

<h3 class="title"><span>{$blg_title|escape:'htmlall':'UTF-8'}</span></h3>
<div id="smartblogcat" class="block {$blg_class|escape:'htmlall':'UTF-8'}">
{foreach from=$postcategory item=post}
	<article class=" single_blog_post cat_post p_bottom_20 m_bottom_30 clearfix 
	col-sm-{$per_column|escape:'htmlall':'UTF-8'}" id="smartblogpost-{$post.id_post|escape:'htmlall':'UTF-8'}">
 	 
    {$catlink.id_category = $post.id_category}
    {$catlink.slug = $post.cat_link_rewrite}
		<figure class="post_thumbnail m_bottom_20">
			<a itemprop="url" title="{$post.meta_title|escape:'htmlall':'UTF-8'}" href="{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}" class="imageFeaturedLink">
              		<img itemprop="image" alt="{$post.meta_title|escape:'htmlall':'UTF-8'}" src="{$smartbloglink->getImageLink($post.link_rewrite, $post.id_post, $image_type)}" class="img-responsive">
          	</a>
          	<div class="blog_mask">
          		<div class="mask_content">
          			<a itemprop="url" title="{$post.meta_title|escape:'htmlall':'UTF-8'}" href="{$smartbloglink->getImageLink($post.link_rewrite, $post.id_post, 'home-default')}" class="post_lightbox"><i class="icon-resize-full"></i></a>
          			<a itemprop="url" title="{$post.meta_title|escape:'htmlall':'UTF-8'}" href="{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}" class="imageFeaturedLink"><i class="icon-link"></i></a>
          		</div>
          	</div>
		</figure>
		<h3 class="post_title m_bottom_0"><a title="{$post.meta_title|escape:'htmlall':'UTF-8'}" href='{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}'>{$post.meta_title|escape:'htmlall':'UTF-8'}</a></h3>
		<div class="post_meta m_bottom_30">
			<span class="post_meta_date"><label>{l s='Posted on' mod='smartblog'}</label> <a itemprop="url" title="{$post.meta_title|escape:'htmlall':'UTF-8'}" href="{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}">{$post.created|date_format:"%B %e, %Y"} <label>{l s='at' mod='smartblog'}</label> {$post.created|date_format:"%r"}</a></span>
		  	<span itemprop="author"><label>{l s='by ' mod='smartblog'}</label> {$post.firstname|escape:'htmlall':'UTF-8'}  {$post.lastname|escape:'htmlall':'UTF-8'}</span>
		  	<span itemprop="articleSection"><label>{l s='/' mod='smartblog'}</label> <a href="{$smartbloglink->getSmartBlogCategoryLink($assoCat.id_category,$assoCat.link_rewrite)|escape:'htmlall':'UTF-8'}">{$post.cat_name|escape:'htmlall':'UTF-8'}</a></span>
		  	<span><label>{l s='/' mod='smartblog'}</label>{l s=' views' mod='smartblog'} ({$post.viewed|escape:'htmlall':'UTF-8'})</span>
		</div>       
		<div class="blog_post_details m_bottom_20">
		{$post.short_description|escape:'htmlall':'UTF-8'}
		</div>
		<div class="blog_post_read_more f_right">
        	 
     			<a class="button" href="{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$post.meta_title|escape:'htmlall':'UTF-8'}">{l s='Read more' mod='smartblog'}</a>
		</div>
	</article> 
{/foreach}
</div>