{if $comment.id_smart_blog_comment != ''}
    <ul class="commentList">
        <div id="comment-{$comment.id_smart_blog_comment}">
            <li class="even">
                <img class="avatar" alt="Avatar" src="{$modules_dir}/smartblog/images/avatar/avatar-author-default.jpg">
                <div class="name">{$childcommnets.name}</div>
                <div class="created">
                    <span itemprop="commentTime">{Tools::displayDate($childcommnets.created, null, $smartshowtime)}</span>
                </div>
                <p>{$childcommnets.content}</p>
                {if Configuration::get('smartenablecomment') == 1}
                    {if $comment_status == 1}
                        <div class="reply">
                            <a onclick="return addComment.moveForm('comment-{$comment.id_smart_blog_comment}', '{$comment.id_smart_blog_comment}', 'respond', '{$smarty.get.id_post}')"  class="comment-reply-link">{l s='Reply' mod='smartblog'}</a>
                        </div>
                    {/if}
                {/if}
                {if isset($childcommnets.child_comments)}
                    {foreach from=$childcommnets.child_comments item=comment}  
                        {if isset($childcommnets.child_comments)}
                            {include file="./comment_loop.tpl" childcommnets=$comment}

                            {$i=$i+1}

                        {/if}
                    {/foreach}
                {/if}
            </li>
        </div>
    </ul>
{/if}

