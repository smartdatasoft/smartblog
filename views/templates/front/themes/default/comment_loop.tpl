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

{if $comment.id_smart_blog_comment != ''}
    <ul class="commentList smart-blog-comment-list-ul">
        <div id="comment-{$comment.id_smart_blog_comment|intval}">
            <li class="even">
                <div class="smart-blog-comments-avatar-box">
                  <img class="avatar" alt="Avatar"
                      src="{$modules_dir|escape:'htmlall':'UTF-8'}/smartblog/images/avatar/avatar-author-default.jpg">
                </div>

                <div class="name">{$childcommnets.name|escape:'htmlall':'UTF-8'}</div>
                
                <p class="smart-blog-comment-text">{$childcommnets.content nofilter}</p>
                {if Configuration::get('smartenablecomment') == 1}
                    {if $comment_status == 1}
                        
                      <div class="smart-blog-comment-time-reply-area">
                          <div class="created">
                                <span class="smart-blog-reply-icon"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="calendar-alt" class="svg-inline--fa fa-calendar-alt fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M0 464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V192H0v272zm320-196c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zm0 128c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zM192 268c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zm0 128c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zM64 268c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12H76c-6.6 0-12-5.4-12-12v-40zm0 128c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12H76c-6.6 0-12-5.4-12-12v-40zM400 64h-48V16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v48H160V16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v48H48C21.5 64 0 85.5 0 112v48h448v-48c0-26.5-21.5-48-48-48z"></path></svg></span>
                              <span class="smart-blog-comment-margin" itemprop="commentTime">{$childcommnets.created|date_format|escape:'htmlall':'UTF-8'}</span>
                          </div>
                          <div class="reply">
                                <span class="smart-blog-reply-icon"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="reply" class="svg-inline--fa fa-reply fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M8.309 189.836L184.313 37.851C199.719 24.546 224 35.347 224 56.015v80.053c160.629 1.839 288 34.032 288 186.258 0 61.441-39.581 122.309-83.333 154.132-13.653 9.931-33.111-2.533-28.077-18.631 45.344-145.012-21.507-183.51-176.59-185.742V360c0 20.7-24.3 31.453-39.687 18.164l-176.004-152c-11.071-9.562-11.086-26.753 0-36.328z"></path></svg></span>
                              <a onclick="return addComment.moveForm('comment-{$comment.id_smart_blog_comment|escape:'htmlall':'UTF-8'}', '{$comment.id_smart_blog_comment|escape:'htmlall':'UTF-8'}', 'respond', '{$comment.id_post|intval}')"
                                  class="comment-reply-link smart-blog-comment-margin">{l s='Reply' mod='smartblog'}</a>
                          </div>
                      </div>  

                    {/if}
                {/if}
                {if isset($childcommnets.child_comments)}
                    {foreach from=$childcommnets.child_comments item=comment}
                        {if isset($childcommnets.child_comments)}
                            {include file="module:smartblog/views/templates/front/comment_loop.tpl" childcommnets=$comment}

                            {$i=$i+1}

                        {/if}
                    {/foreach}
                {/if}
            </li>
        </div>
    </ul>
{/if}