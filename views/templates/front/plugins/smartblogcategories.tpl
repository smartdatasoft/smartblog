{if isset($categories) AND !empty($categories)}
<div id="category_blog_block_left"  class="block blogModule boxPlain">
  <h5 class='title_block'>{l s='Blog Categories' mod='smartblog'}</h5>
   <div class="block_content list-block">
         <ul>
	{foreach from=$categories item="category"}
                <li>
                <a href="{$smartbloglink->getSmartBlogCategoryLink($category.id_smart_blog_category,$category.link_rewrite)|escape:'htmlall':'UTF-8'}">[{$category.count}] {$category.name}</a>
                </li>
	{/foreach}
        </ul>
   </div>
</div>
{/if}