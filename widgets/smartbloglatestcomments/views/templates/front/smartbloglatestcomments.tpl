{if isset($latesComments) AND !empty($latesComments)}
<div class="block blogModule boxPlain">
   <h2 class='sdstitle_block'>{l s='Latest Comments' mod='smartbloglatestcomments'}</h2>
   <div class="block_content sdsbox-content">
      <ul class="recentComments">
	  {foreach from=$latesComments item="comment"}
 
         <li>
            <a title="" href="{$smartbloglink->getSmartBlogPostLink($comment.id_post,$comment.link_rewrite)}">
	       <img class="image" alt="Avatar" src="{$modules_dir}/smartblog/images/avatar/avatar-author-default.jpg"></a>
            {$comment.name} <i>{l s='on'}</i>
		   <a class="title"   href="{$smartbloglink->getSmartBlogPostLink($comment.id_post,$comment.link_rewrite)}">{$comment.content}</a>
         </li>
          {/foreach}
      </ul>
   </div>
   <div class="box-footer"><span></span></div>
</div>
{/if}