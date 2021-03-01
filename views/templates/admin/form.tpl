<div class="row justify-content-center">
    <div class="col-lg-10 module-catalog-page">
    {if $smartblog_validity == "invalid" || $smartblog_validity == ""}
    <div class="addons-controller-promotion">
      <p>You need to <a href="{$link->getAdminLink('AdminModules')}&configure=smartblog">activate license</a> to use these addons.</p>
      <p class="smartblog-promo-text">Don't have a purchase code??? <a href="#">GET ONE FOR FREE!!!</a></p>
    </div>
    {/if}
    <div class="ajax-loader-wrapper"><div class="ajax-loader"><img src="{$image_url}loader.gif"></div></div>
       <div id="modules-list-container-all" class="row modules-list" style="display: flex;" data-name="all">
         {foreach from=$addons key=k item=value}
         <div class="module-item module-item-grid col-md-12 col-lg-6 col-xl-3 " data-id="" data-name="{$k}">
             <div class="module-item-wrapper-grid">
                <div class="module-item-heading-grid">
                   <div class="module-logo-thumb-grid">
                      <img src="{$image_url}addons/{$k}/logo.png" >
                   </div>
                   <h3 class="text-ellipsis module-name-grid smartblog-addons-name" data-toggle="pstooltip" data-placement="top" title="1-Click Upgrade">
                      {$value.title}
                   </h3>
                   <div class="text-ellipsis small-text module-version-author-grid">
                      {$value.version}
                   </div>
                </div>
                <div class="module-quick-description-grid small no-padding mb-0">
                   <div class="module-quick-description-text">
                      {$value.description}
                   </div>
                </div>
                <div class="module-container module-quick-action-grid clearfix">
                   <div class="smartblog-addons-author badges-container">
                     by <a href="https://classydevs.com/?utm_source=smartblog_addons&utm_medium=smartblog_addons&utm_campaign=smartblog_addons&utm_term=smartblog_addons" target="_blank"><img src="{$image_url}classy-devs.svg" alt="Made by SmartDataSoft"></a>
                   </div>
                   <hr>
                   {assign var="priceclass" value="free-class"}
                   {if $value.price > '0'}
                     {assign "priceclass" value="pro-class"}
                   {/if}
                   <div class="float-right module-price addons-price {$priceclass}">
                     {if $value.price == '0'}
                        <span class="pt-2">Free</span>
                     {else}
                        <span class="pt-2">Price: ${$value.price}</span>
                     {/if}
                   </div>
                   {if $smartblog_validity == "invalid" || $smartblog_validity == ""}
                        <div class="btn-group module-actions">
                           <a href="{$link->getAdminLink('AdminModules')}&configure=smartblog" class="smartblog-activation-sec">Activate SmartBlog to Get The Add-on</a> 
                        </div>
                   {else}
                     <div class="btn-group module-actions smartblog-addon-action">
                        {if $value.installed == '-1'}
                           <a href="#" class="smartblog-activation-sec">Get Now</a>
                        {else}
                           {assign var="text" value="Install"}
                           {if $value.installed == '1'}
                              {assign "text" value="Uninstall"}
                           {/if}
                           <button class="btn btn-primary-reverse btn-outline-primary smartblog_addons_install" data-addon_name="{$k}" data-installed="{$value.installed}">
                           {$text}
                           </button>
                        {/if}
                     </div>
                   {/if}
                </div>
             </div>
          </div>
         {/foreach}
       </div>
    </div>
 </div>