{if isset($posts) AND !empty($posts)}
<div class="block blogModule boxPlain">
   <h2 class='sdstitle_block'><a href="{smartblog::GetSmartBlogLink('smartblog')}">{l s='Popular Articles' mod='smartblogpopularposts'}</a></h2>
   <div class="block_content sdsbox-content">
      <ul class="popularArticles">
            {foreach from=$posts item="post"}

            <li>
		<a class="image"
		    title="{$post.meta_title}" href="{$smartbloglink->getSmartBlogPostLink($post.id_smart_blog_post,$post.link_rewrite)}">
			<img style="max-width: 100%;" alt="{$post.meta_title}"
		src="{$smartbloglink->getImageLink($post.link_rewrite, $post.id_smart_blog_post, 'home-small')}">
				</a>

         <a class="title paddleftreleted"  title="{$post.meta_title}" href="{$smartbloglink->getSmartBlogPostLink($post.id_smart_blog_post,$post.link_rewrite)}">{$post.meta_title}</a>
	      <span class="info">{$post.created|date_format:"%b %d, %Y"}</span>
	    </li> 
	{/foreach}
      </ul>
   </div>
   <div class="box-footer"><span></span></div>
</div>
{/if}