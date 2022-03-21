{*
* Multi Vendor XML Data
* by Dvs.gr
* Do not edit below
*}

<div class="alert alert-info">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <ul id="infos_block" class="list-unstyled">
        <li><h4>Σύνδεσμοι Feed</h4><p>Σύνδεσμοι για cron jobs και αποστολής στο MVXML</p><h4></li>
    </ul>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="leadin"></div>
        <div class="panel" id="configuration_settings_tab">
            <div class="panel-heading">
                <i class="icon-cogs"></i>
                    {l s=' Links ' mod='mvxmldata'}
            </div>
            <div class="form-wrapper">
                {foreach from=$mvxmllinks  key=linktype item=linkurl}
                    <div class="form-group">
                        <div>
                            <label class="control-label" style="text-align:left;">
                                <span data-toggle="tooltip" class="label-tooltip" data-original-title="{$linktype|escape:'htmlall':'UTF-8'}" data-html="true">
                                    {$linktype|escape:'htmlall':'UTF-8'}
                                </span>
                            </label>
                        </div>
                        <div>
                            <textarea class="textarea-autosize" name="{$linktype|escape:'htmlall':'UTF-8'}">{$linkurl|escape:'htmlall':'UTF-8'}</textarea>
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
</div>
