		{if $node.name == ''}
				{if $node.children|@count > 0}
					{foreach from=$node.children item=child name=categoryTreeBranch}
						{if $smarty.foreach.categoryTreeBranch.last}
							{include file="./category-tree-branch.tpl" node=$child last='true' select='false'}
						{else}
							{include file="./category-tree-branch.tpl" node=$child last='false' select='false'}
						{/if}
					{/foreach}
				{/if}
		{else}
			<option value="{$node.link}" class="category_{$node.id}{if isset($last) && $last == 'true'} last{/if}" >
				<a href="{$node.link|escape:'html':'UTF-8'}" {if isset($currentCategoryId) && $node.id == $currentCategoryId}class="selected"{/if}
					title="{$node.desc|strip_tags|trim|truncate:255:'...'|escape:'html':'UTF-8'}">{$node.level_depth nofilter}{$node.level_depth nofilter}-{$node.name|escape:'html':'UTF-8'}</a>
				{if $node.children|@count > 0}
					{foreach from=$node.children item=child name=categoryTreeBranch}
						{if $smarty.foreach.categoryTreeBranch.last}
							{include file="$branche_tpl_path" node=$child last='true' select='false'}
						{else}
							{include file="$branche_tpl_path" node=$child last='false' select='false'}
						{/if}
					{/foreach}
				{/if}
			</option>
		{/if}