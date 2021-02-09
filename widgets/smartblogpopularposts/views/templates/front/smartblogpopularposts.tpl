{if isset($posts) AND !empty($posts)}
<div class="block block-blog smart-block blogModule boxPlain">
   <h4 class="smart_blog_sidebar_title hidden-sm-down"><a href="{smartblog::GetSmartBlogLink('smartblog')}">{l s='Popular Articles' mod='smartblogpopularposts'}</a></h4>
   <div class="block_content sdsbox-content smart-blog-post-content">
      <ul class="popularArticles">
            {foreach from=$posts item="post"}

            <li>
		<a class="image"
		    title="{$post.meta_title}" href="{$smartbloglink->getSmartBlogPostLink($post.id_smart_blog_post,$post.link_rewrite)}">
			<img alt="{$post.meta_title}"
		src="{if $smartbloglink->getImageLink($post.link_rewrite, $post.id_smart_blog_post, 'home-small') != 'false'}{$smartbloglink->getImageLink($post.link_rewrite, $post.id_smart_blog_post, 'home-small')}{/if}" style="overflow: hidden;">
				</a>
         <div class="smart-blog-post-title-date">
         <a class="title paddleftreleted"  title="{$post.meta_title}" href="{$smartbloglink->getSmartBlogPostLink($post.id_smart_blog_post,$post.link_rewrite)}">{$post.meta_title}</a>
	      <span class="info">{$post.created|date_format:"%b %d, %Y"}</span>
         </div>
	    </li> 
	{/foreach}
      </ul>
   </div>
   <div class="box-footer"><span></span></div>
</div>
{/if}