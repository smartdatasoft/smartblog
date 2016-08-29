{if isset($posts) AND !empty($posts)}
<div id="articleRelated">
     <p class="title_block">{l s='Related Posts: ' mod='smartblog'}</p>
     <div class="sdsbox-content"> 
            <ul class="fullwidthreleted">
                {foreach from=$posts item="post"}
                {if isset($post.id_smart_blog_post)}
                    <li>
                       <a class="title paddleftreleted"  title="{$post.meta_title}" href="{$smartbloglink->getSmartBlogPostLink($post.id_smart_blog_post,$post.link_rewrite)}">{$post.meta_title}</a>
                       <span class="info">{$post.created|date_format:"%b %d, %Y"}</span>
                    </li> 
                {/if}
                {/foreach}
            </ul>
     </div>
</div>
{/if}