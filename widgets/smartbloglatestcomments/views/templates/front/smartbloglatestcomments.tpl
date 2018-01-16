{if isset($latesComments) AND !empty($latesComments)}
<div class="block block-blog blogModule boxPlain">
   <h4 class="text-uppercase h6 hidden-sm-down">{l s='Latest Comments' mod='smartbloglatestcomments'}</h4>
   <div class="block_content sdsbox-content">
      <ul class="recentComments">
	  {foreach from=$latesComments item="comment"}
 
         <li>
            <a title="" href="{$smartbloglink->getSmartBlogPostLink($comment.id_post,$comment.link_rewrite)}">
	       <img class="image" alt="Avatar" src="{$modules_dir}/smartblog/images/avatar/avatar-author-default.jpg"></a>
            {$comment.name} <i>{l s='on'}</i>
		   <a class="title"   href="{$smartbloglink->getSmartBlogPostLink($comment.id_post,$comment.link_rewrite)}">{SmartBlogPost::subStr($comment.content,50) nofilter}</a>
         </li>
          {/foreach}
      </ul>
   </div>
   <div class="box-footer"><span></span></div>
</div>
{/if}