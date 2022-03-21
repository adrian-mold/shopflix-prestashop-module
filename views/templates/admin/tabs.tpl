<h3>{l s='Add offer prices for this product' mod='mpxmloffers'}:</h3>

{*$combsavedata|print_r*}
	{if $combsavedata|count > 0}
	<input type="hidden" class="offer_field" name="has_comb" value="1" />
		{foreach $combsavedata item="comb" key="combkey"}
			{$combarr = ''}
			<input type="hidden" class="offer_field" name="product_id" value="{Tools::getValue('id_product')}" />
			<p style="margin:0;padding:5px;background:#f5f5f5;font-size:12px;border-top:1px solid #999;font-weight:700;">{foreach $combname[$combkey] item="cn"}
			&bull;{$cn['name']} {$combarr[] = $cn['name']} {/foreach}</p>
			<input type="hidden" class="offer_field offer_combination_id" name="offer_combination_id[]" value="{$comb['offer_combination_id']}" />
			<input type="hidden" class="offer_field" name="offer_combination_title[]" value="{foreach $combarr item="cname"} {$cname} {/foreach}" />
				<table class="table product" style="width:100%;border-radius:4px;border-top:0;font-size:11px;">
					<thead id="offers-fields">
						<tr class="head-item">
							<td class="head-item-price">{l s='Offer Price' mod='mpxmloffers'}</td>
							<td class="head-item-qty">{l s='Available Quantity' mod='mpxmloffers'}</td>
							<td class="head-item-from">{l s='Date from' mod='mpxmloffers'}</td>
							<td class="head-item-to">{l s='Date to' mod='mpxmloffers'}</td>
							<td class="head-item-ship">{l s='Shipping lead time' mod='mpxmloffers'}</td>
							<td class="head-item-enable">{l s='Enable' mod='mpxmloffers'}</td>
						</tr>
						<tr class="offer-item" id="offer">
							<td class="offer-item-price">
								<input type="number" class="offer_field" name="offer_price[]" step="0.01" value="{$comb.offer_price}" />
							</td>
							<td class="offer-item-qty"><input class="offer_field" type="number" name="offer_qty[]" step="1" value="{$comb.offer_quantity}" /></td>
							
							<td class="offer-item-from"><div class="input-group datepicker"><input class="offer_field" type="text" id="fromd" name="from[]" data-format="DD/MM/YYYY" value="{$comb.offer_from|strtotime|date_format:"%d/%m/%Y"}"></div></td>
							<td class="offer-item-to">
							<div class="input-group datepicker"><input type="text" class="offer_field" id="tod" name="to[]" data-format="DD/MM/YYYY"  value="{$comb.offer_to|strtotime|date_format:"%d/%m/%Y"}"></div></td>
							<td class="offer-item-ship">
							<input type="number" class="offer_field" step="1" id="ship" name="offer_ship[]" value="{$comb.shipping_lead_time}"></td> 
							<td class="offer-item-enable">
							<input class="offer_field" type="checkbox" id="senable" name="offer_enable[]" value="1" {if {$comb.enable} == 1}checked{else}{/if}></td> 
						</tr>		
					</thead>
				</table>

		{/foreach}
	{else}
	<input type="hidden" class="offer_field" name="product_id" value="{Tools::getValue('id_product')}" />
	<input type="hidden" class="offer_field" name="has_comb" value="0" />
		<table class="table product mt-3" style="width:100%;margin-top:25px;border-radius:4px;">
			<thead id="offers-fields">
				<tr class="head-item">
					<td class="head-item-price">{l s='Offer Price' mod='mpxmloffers'}</td>
					<td class="head-item-qty">{l s='Available Quantity' mod='mpxmloffers'}</td>
					<td class="head-item-from">{l s='Date from' mod='mpxmloffers'}</td>
					<td class="head-item-to">{l s='Date to' mod='mpxmloffers'}</td>
					<td class="head-item-ship">{l s='Shipping lead time' mod='mpxmloffers'}</td>
					<td class="head-item-enable">{l s='Enable' mod='mpxmloffers'}</td>
				</tr>
				<tr class="offer-item" id="offer">
					<td class="offer-item-price">
						<input type="number" class="offer_field" name="offer_price" step="0.01" value="{if $offerdata['offer']['offer_to']|strtotime > $smarty.now && $offerdata['offer']['offer_price'] > 0}{$offerdata['offer']['offer_price']}{else}{$offerdata['product']['price']|round:2}{/if}" />
					</td>
					<td class="offer-item-qty"><input class="offer_field" type="number" name="offer_qty" step="1" value="{if $offerdata['offer']['offer_to']|strtotime > $smarty.now && $offerdata['offer']['offer_quantity'] > 0}{$offerdata['offer']['offer_quantity']}{else}{$offerdata['product']['quantity']}{/if}" /></td>
					
					<td class="offer-item-from"><div class="input-group datepicker"><input class="offer_field" type="text" id="fromd" name="from" data-format="DD/MM/YYYY" value="{if $offerdata['offer']['offer_to']|strtotime > $smarty.now}{$offerdata['offer']['offer_from']|strtotime|date_format:"%d/%m/%Y"}{else}{$smarty.now|date_format:"%d/%m/%Y"}{/if}"></div></td>
					<td class="offer-item-to">
					<div class="input-group datepicker"><input type="text" class="offer_field" id="tod" name="to" data-format="DD/MM/YYYY"  value="{if $offerdata['offer']['offer_to']|strtotime > $smarty.now}{$offerdata['offer']['offer_to']|strtotime|date_format:"%d/%m/%Y"}{else}{"+12 months"|date_format:"%d/%m/%Y"}{/if}"></div></td>
					<td class="offer-item-ship">
					<input type="number" class="offer_field" step="1" id="ship" name="offer_ship" value="{if $offerdata['offer']['offer_to']|strtotime > $smarty.now}{$offerdata['offer']['shipping_lead_time']}{else}0{/if}"></td> 
					<td class="offer-item-enable">
					<input class="offer_field" type="checkbox" id="senable" name="offer_enable" value="1" {if $offerdata['offer']['offer_to']|strtotime > $smarty.now && $offerdata['offer']['enable'] == 1}checked{else}{/if}{if $offerdata['offer']['enable'] < 1}{/if}></td> 
				</tr>		
			</thead>
		</table>
		{/if}
		<div style="text-align:center;" class="createoffersbutton">
           <button class="btn btn-primary pointer" style="margin-top: 20px;" id="edit_offer">{l s='Update Offer Values' mod='mpxmloffers'}</button>
		</div>
{literal}
<script>
$(document).ready(function($){
	$('#edit_offer,input.save').on('click',function(event) {
			//alert($('.offer_field').serialize());
			$.post( "../../../../../modules/mpxmloffers/editOffer.php", {'offers':$('.offer_field').serialize()})
			  .done(function( dataoffer ) {
			  //alert(dataoffer);
			  });
	});
});
</script>
{/literal}	