{*
* Multi Vendor XML Data
* by Dvs.gr
* Do not edit below
*}

{extends file="helpers/list/list_content.tpl"}
{block name="td_content"}
        {if isset($tr.$key)}
             {if isset($params.type) && $params.type == 'editablemixkeyval' && isset($tr.id_category)}
                <div>
                    {if isset($params.buttons)}
                        <div class="simplebuttons">
                        {foreach $params.buttons AS $buttonname => $buttonrow}

                                <span title="{$buttonname|escape:'html':'UTF-8'}"  name="{$tr.id_category}" class="btn btn-default" onclick="{$buttonrow['onclick']|escape:'html':'UTF-8'};">
                                    <i class="{$buttonrow['icon']|escape:'html':'UTF-8'}"></i>&nbsp;{$buttonname|escape:'html':'UTF-8'}
                                </span>
                        {/foreach}
                        </div>
                    {/if}
                </div>
            {elseif isset($params.type) && $params.type == 'simpletext'}
                <div>
                    {foreach $params.text AS $text}
                        <div class="simpletext"><strong>{$text|escape:'html':'UTF-8'}</strong></div>
                    {/foreach}
                </div>
            {elseif isset($params.type) && $params.type == 'editable' && isset($tr.id_category)}
                <div>
                    <div class="fpdatarow"><input type="text" id="{$params.match|escape:'html':'UTF-8'}_{$tr.id_category|escape:'html':'UTF-8'}"
                        value="{$tr[$params.match]|escape:'html':'UTF-8'}" class="{$key|escape:'html':'UTF-8'}"/></div>
                </div>
            {else}
                {$tr.$key|escape:'html':'UTF-8'}
            {/if}
        {else}
            {if isset($params.type) && $params.type == 'editable' && isset($tr.id_category)}
                <div>
                    <div class="fpdatarow"><input type="text" id="{$params.match|escape:'html':'UTF-8'}_{$tr.id_category|escape:'html':'UTF-8'}"
                        value="{$tr[$params.match]|escape:'html':'UTF-8'}" class="{$key|escape:'html':'UTF-8'}"/></div>
                </div>
            {else}
                {block name="default_field"}--{/block}
            {/if}
        {/if}
{/block}


