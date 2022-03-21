{*
* Multi Vendor XML Data
* by Dvs.gr
* Do not edit below
*}

{if $selectCategories == 'feed_links'}

<span class="btn-group-action">
    <a data-selenium-id="view_feed_link" class="btn btn-default _blank" href="{$feed_links|escape:'htmlall':'UTF-8'}" target="_blank">

    <span class="ladda-label">
        {l s='Links' mod='mvxmldata'}
    </span><span class="ladda-spinner"></span></a>

</span>

{elseif $selectCategories == 'download_Feed'}

<span class="btn-group-action">
    <a data-selenium-id="view_download_link" class="btn btn-default _blank" href="{$mvxmldownload_link|escape:'htmlall':'UTF-8'}" target="_blank"><span class="ladda-label">
        {l s='Download' mod='mvxmldata'}
    </span><span class="ladda-spinner"></span></a>

</span>

{elseif $selectCategories == 'view_Feed'}

<span class="btn-group-action">
    <a data-selenium-id="view_download_link" class="btn btn-default _blank" href="{$download_feed_cron_link|escape:'htmlall':'UTF-8'}" target="_blank"><span class="ladda-label">
    {l s='View' mod='mvxmldata'}
    </span><span class="ladda-spinner"></span></a>

</span>

{else}
{/if}

