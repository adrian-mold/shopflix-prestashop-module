{*
* Multi Vendor XML Data
* by Dvs.gr
* Do not edit below
*}

<div class="modal-body">
  <div class="alert alert-warning" id="import_details_stop" style="display:none;">
    {l s='Aborting, please wait...' mod='mvxmldata'}
  </div>
  <p id="import_details_progressing">
    {l s='Importing your data...' mod='mvxmldata'}
  </p>
  <div class="alert alert-success" id="import_details_finished" style="display:none;">
    {l s='Data imported!' mod='mvxmldata'}
    <br/>
    {l s='Look at your listings to make sure it's all there as you wished.' mod='mvxmldata'}
  </div>
  <div id="import_messages_div" style="max-height:250px; overflow:auto;">
    <div class="alert alert-danger" id="import_details_error" style="display:none;">
      {l s='Errors occurred:' mod='mvxmldata'}<br/><ul></ul>
    </div>
    <div class="alert alert-warning" id="import_details_post_limit" style="display:none;">
      {l s='Warning, the current import may require a PHP setting update, to allow more data to be transferred. If the current import stops before the end, you should increase your PHP \"post_max_size\" setting to [1]%size%[/1]MB at least, and try again.' sprintf=[
        '[1]' => '<span id="import_details_post_limit_value">',
        '%size%' => '16MB',
        '[/1]' => '</span>'
        ] mod='mvxmldata'}
    </div>
    <div class="alert alert-warning" id="import_details_warning" style="display:none;">
      {l s='Some errors were detected. Please check the details:' mod='mvxmldata'}<br/><ul></ul>
    </div>
    <div class="alert alert-info" id="import_details_info" style="display:none;">
      {l s='We made the following adjustments:' mod='mvxmldata'}<br/><ul></ul>
    </div>
  </div>

  <div id="import_validate_div" style="margin-top:17px;">
    <div class="pull-right" id="import_validation_details" default-value="{l s='Validating data...' mod='mvxmldata'}">
      &nbsp;
    </div>
    <div class="progress active progress-striped" style="display: block; width: 100%">
      <div class="progress-bar progress-bar-info" role="progressbar" style="width: 0%" id="validate_progressbar_done">
        <span>{l s='[1]%percentage%[/1]% validated' sprintf=[
                  '[1]' => '<span id="validate_progression_done">',
                  '%percentage%' => '0',
                  '[/1]' => '</span>'
                  ] mod='mvxmldata'}
        </span>
      </div>
      <div class="progress-bar progress-bar-info" role="progressbar" id="validate_progressbar_next" style="opacity: 0.5 ;width: 0%">
        <span class="sr-only">{l s='Processing next page...' mod='mvxmldata'}</span>
      </div>
    </div>
  </div>

  <div id="import_progress_div" style="display:none;">
    <div class="pull-right" id="import_progression_details" default-value="{l s='Importing your data...' mod='mvxmldata'}">
      &nbsp;
    </div>
    <div class="progress active progress-striped" style="display: block; width: 100%">
      <div class="progress-bar progress-bar-info" role="progressbar" style="width: 0%" id="import_progressbar_done2">
        <span>{l s='Linking accessories...' mod='mvxmldata'}</span>
      </div>
      <div class="progress-bar progress-bar-success" role="progressbar" style="width: 0%" id="import_progressbar_done">
        <span>{l s='[1]%size%[/1]% imported' sprintf=[
          '[1]' => '<span id="import_progression_done">',
          '%size%' => '0',
          '[/1]' => '</span>'
          ] mod='mvxmldata'}
        </span>
      </div>
      <div class="progress-bar progress-bar-success progress-bar-stripes active" role="progressbar" id="import_progressbar_next" style="opacity: 0.5 ;width: 0%">
        <span class="sr-only">{l s='Processing next page...' mod='mvxmldata'}</span>
      </div>
    </div>
  </div>

  <div class="modal-footer">
    <div class="input-group pull-right">
      <button type="button" class="btn btn-primary" tabindex="-1" id="import_continue_button" style="display: none;">
        {l s='Ignore warnings and continue?' mod='mvxmldata'}
      </button>
      &nbsp;
      <button type="button" class="btn btn-default" tabindex="-1" id="import_stop_button">
        {l s='Abort import' mod='mvxmldata'}
      </button>
      &nbsp;
      <button type="button" class="btn btn-success" data-dismiss="modal" tabindex="-1" id="import_close_button" style="display: none;">
        {l s='Close' mod='mvxmldata'}
      </button>
    </div>
  </div>
</div>
