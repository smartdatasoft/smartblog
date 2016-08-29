</div></div>
<style type="text/css">
    .form-group.smartblog_meta_fields{
        display:none;
    }
</style>
{$languages = Language::getLanguages()}
{$id_lang_default = Configuration::get('PS_LANG_DEFAULT', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id)}
{foreach $meta_fields as $field_group_id => $fields}    
    {foreach $fields as $field}
        <div class="form-group smartblog_meta_fields smartblog_{$field_group_id}_meta_fields">
            <label class="control-label col-lg-3"> {$field.title} </label>
            <div class="col-lg-9">
                {if isset($field.lang) && $field.lang && $languages|count > 1}
                    <div class="form-group">
                        {foreach $languages as $language}                            
                            <div class="translatable-field lang-{$language.id_lang}"{if $language.id_lang != $id_lang_default} style="display: none;"{/if}>
                                <div class="col-lg-9">
                                    {if $field.type == 'text'}
                                        <input type="text" value="{BlogPostMeta::get($id_smart_blog_post, $field_group_id|cat:'-'|cat:$field.name,false,$language.id_lang)}" class="" name="{$field_group_id}-{$field.name}_{$language.id_lang}" id="{$field_group_id}-{$field.name}_{$language.id_lang}">
                                    {elseif $field.type == 'textarea'}
                                        <textarea type="text" class=" textarea-autosize" name="{$field_group_id}-{$field.name}_{$language.id_lang}" id="{$field_group_id}-{$field.name}_{$language.id_lang}">{BlogPostMeta::get($id_smart_blog_post, $field_group_id|cat:'-'|cat:$field.name,false,$language.id_lang)}</textarea>            
                                    {/if}
                                    {if isset($field.desc)}
                                        <p class="help-block">
                                            {$field.desc}
                                        </p>
                                    {/if}
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
                                        {$language.iso_code}
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        {foreach from=$languages item=language}
                                            <li>
                                                <a href="javascript:hideOtherLanguage({$language.id_lang});">{$language.name}</a>
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                {else}    
                    {if $field.type == 'text'}
                        <input type="text" value="{BlogPostMeta::get($id_smart_blog_post, $field_group_id|cat:'-'|cat:$field.name)}" class="" name="{$field_group_id}-{$field.name}" id="{$field_group_id}-{$field.name}">
                    {elseif $field.type == 'textarea'}
                        <textarea type="text" class=" textarea-autosize" name="{$field_group_id}-{$field.name}" id="{$field_group_id}-{$field.name}">{BlogPostMeta::get($id_smart_blog_post, $field_group_id|cat:'-'|cat:$field.name)}</textarea>            
                    {/if}
                    {if isset($field.desc)}
                        <p class="help-block">
                            {$field.desc}
                        </p>
                    {/if}
                {/if}
            </div>
        </div>
    {/foreach}

{/foreach}
{literal}

    <script type="text/javascript">
        $(document).ready(function() {
            $('input[name="post_type"]').on('click', function() {
                var type = $(this).val();
                $('.form-group.smartblog_meta_fields').hide(300);
                if ($('.form-group.smartblog_' + type + '_meta_fields').length > 0) {
                    $('.form-group.smartblog_' + type + '_meta_fields').show(300);
                }
            });
            $(window).on('load',function(){
                $('input[name="post_type"]:checked').trigger('click');
                $(window).trigger('resize');
            });
        });

    </script>
{/literal}
<div class="form-group">    
    <div class="col-lg-3 "></div>
    <div class="col-lg-9 ">