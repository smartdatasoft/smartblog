<div class="row justify-content-center">
    <div class="col-lg-10 module-catalog-page">
       <div id="modules-list-container-all" class="row modules-list" style="display: flex;" data-name="all">
         {foreach from=$addons key=k item=value}
         <div class="module-item module-item-grid col-md-12 col-lg-6 col-xl-3 " data-id="" data-name="{$k}">
             <div class="module-item-wrapper-grid">
                <div class="module-item-heading-grid">
                   <div class="module-logo-thumb-grid">
                      <img src="{$image_url}{$k}/logo.png" >
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
                   <div class="badges-container">
                      Made by <a><img src="" alt="Made by ClassyDevs"></a>
                   </div>
                   <hr>
                   <div class="float-right module-price">
                      <span class="pt-2">Free</span>
                   </div>
                   <div class="btn-group module-actions">
                     {if $value.installed == '-1'}
                        <a>Download Now</a>
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
                </div>
             </div>
          </div>
         {/foreach}
       </div>
    </div>
 </div>