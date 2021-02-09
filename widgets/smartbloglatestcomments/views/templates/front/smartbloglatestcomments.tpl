{if isset($latesComments) AND !empty($latesComments)}
<div class="block block-blog smart-block smart-blog-latest-comments blogModule boxPlain">
   <h4 class="smart_blog_sidebar_title smart-title-shape hidden-sm-down">{l s='Latest Comments' mod='smartbloglatestcomments'}</h4>
   <div class="block_content sdsbox-content smart-blog-comments-content">
      <ul class="recentComments">
      {foreach from=$latesComments item="comment"}
   
         <li>
            <a class="smart-blog-author-avata-image" title="" href="{$smartbloglink->getSmartBlogPostLink($comment.id_post,$comment.link_rewrite)}">
            <img class="image" alt="Avatar" src="{$modules_dir}/smartblog/images/avatar/avatar-author-default.jpg"></a>
            <span class="smart-blog-lcomments-text">
               <span class="smart-blog-authorl-name">{$comment.name}</span> <span class="smart-blog-aauthor-comment-on">{l s='commented on'}</span>
         <a class="title"   href="{$smartbloglink->getSmartBlogPostLink($comment.id_post,$comment.link_rewrite)}">{SmartBlogPost::subStr($comment.content,50) nofilter}</a>
            </span>
         </li>
            {/foreach}
      </ul>
   </div>
   <div class="box-footer"><span></span></div>
</div>
{/if}