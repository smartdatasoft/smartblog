{if $archive_type==1}
  <div class="block-categories hidden-sm-down">
    <h4 class="text-uppercase h6 hidden-sm-down">{l s='Blog Archive' mod='smartblogarchive'}</h4>
    <h4 class="text-uppercase h6 hidden-sm-down"></h4>
    <ul class="category-sub-menu">
      {foreach from=$archives item="archive"}
        {foreach from=$archive.month item="months"}
          {assign var="linkurl" value=null}
          {$linkurl.year = $archive.year}
          {$linkurl.month = $months.month}
          {assign var="monthname" value=null}
          {if $months.month == 1}{$monthname = 'January'}{elseif $months.month == 2}{$monthname = 'February'}{elseif $months.month == 3}
          {$monthname = 'March'} {elseif $months.month == 4} {$monthname = 'Aprill'}{elseif $months.month == 5}{$monthname = 'May'}
          {elseif $months.month == 6}{$monthname = 'June'}{elseif $months.month == 7}{$monthname = 'July'} {elseif $months.month == 8}
          {$monthname = 'August'} {elseif $months.month == 9}{$monthname = 'September'}{elseif $months.month == 10} {$monthname = 'October'}
          {elseif $months.month == 11}{$monthname = 'November'}{elseif $months.month == 12} {$monthname = 'December'}{/if}

          <li data-depth="0">
            <a class="text-uppercase h6" href="{smartblog::GetSmartBlogLink('smartblog_month',$linkurl)}">{$monthname}-{$archive.year}</a>
          </li>
        {/foreach}
      {/foreach}
    </ul>
  </div>
{else}
    {function name="archives" nodes=[] depth=0}
      {strip}
        {if $nodes|count}
          <ul class="category-sub-menu">
            {foreach from=$nodes item=node}
              {if $node.name != ''}
                <li data-depth="{$depth}">
                  {if $depth===0}
                    <a href="{$node.link}">{$node.name}</a>
                    {if $node.children}
                      
                        {if $isDhtml}
                          <div class="navbar-toggler collapse-icons" data-toggle="collapse" data-target="#exBlogArchiveCollapsingNavbar{$node.id}">
                            <i class="material-icons add">&#xE145;</i>
                            <i class="material-icons remove">&#xE15B;</i>
                          </div>
                        {/if}
                    
                      <div class="{if $isDhtml}collapse{/if}" id="exBlogArchiveCollapsingNavbar{$node.id}">
                        {archives nodes=$node.children depth=$depth+1}
                      </div>
                    {/if}
                  {else}
                    <a class="category-sub-link" href="{$node.link}">{$node.name}</a>
                    {if $node.children}
                      {if $isDhtml}
                        <span class="arrows" data-toggle="collapse" data-target="#exBlogArchiveCollapsingNavbar{$node.id}">
                          <i class="material-icons arrow-right">&#xE315;</i>
                          <i class="material-icons arrow-down">&#xE313;</i>
                        </span>
                    {/if}
                      <div class="{if $isDhtml}collapse{/if}" id="exBlogArchiveCollapsingNavbar{$node.id}">
                        {archives nodes=$node.children depth=$depth+1}
                      </div>
                    {/if}
                  {/if}
                </li>
              {/if}
            {/foreach}
          </ul>
        {/if}
      {/strip}
    {/function}

    <div class="block-categories hidden-sm-down">
      <h4 class="text-uppercase h6 hidden-sm-down">{l s='Blog Archive' mod='smartblogarchive'}</h4>
      <ul class="category-top-menu">
        <li><a class="text-uppercase h6" href="{$archives.link nofilter}">{$archives.name}</a></li>
        <li>{archives nodes=$archives.children}</li>
      </ul>
    </div>
{/if}