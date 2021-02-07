{if isset($posts) AND !empty($posts)}
<div id="recent_article_smart_blog_block_left"  class="block block-blog smart-block blogModule boxPlain">
   <h4 class="smart_blog_sidebar_title hidden-sm-down"><a href="{smartblog::GetSmartBlogLink('smartblog')}">{l s='Recent Articles' mod='smartblogrecentposts'}</a></h4>
   <div class="smart-blog-post-content block_content sdsbox-content">
      <ul class="recentArticles">
        {foreach from=$posts item="post"}
             <li>
                 <a class="image" title="{$post.meta_title}" href="{$smartbloglink->getSmartBlogPostLink($post.id_smart_blog_post,$post.link_rewrite)}">
                     <img alt="{$post.meta_title}" src="{if $smartbloglink->getImageLink($post.link_rewrite, $post.id_smart_blog_post, 'home-small') != 'false'}{$smartbloglink->getImageLink($post.link_rewrite, $post.id_smart_blog_post, 'home-small')}{/if}" style="overflow: hidden;">
                 </a>
                 <div class="smart-blog-post-title-date">
                    <a class="title"  title="{$post.meta_title}" href="{$smartbloglink->getSmartBlogPostLink($post.id_smart_blog_post,$post.link_rewrite)}">{$post.meta_title}</a>
                    <span class="info">{$post.created}</span>
                 <div>
             </li>
         {/foreach}
        </ul>
   </div>
</div>
{/if}