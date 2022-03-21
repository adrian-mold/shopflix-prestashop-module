<p class="offers-title"><strong>{l s='Add products in Offers XML' mod='mpxmloffers'}:</strong> </p>
<p>{l s='Add a cron job running this url for get every new update of your products' mod='mpxmloffers'}:</p>
<h4 style="border-bottom:1px solid #999;padding-bottom:5px;">{l s='URL for Cron Job' mod='mpxmloffers'}: <strong><a href="../modules/mpxmloffers/updateOffers.php?s={Configuration::get('PS_SHOP_EMAIL')|md5}" target="_new">https://{$smarty.server.HTTP_HOST}{$smarty.server.REWRITEBASE}modules/mpxmloffers/updateOffers.php?s={Configuration::get('PS_SHOP_EMAIL')|md5}</a></strong></h4>


<p>{l s='This is the main xm url with all available products of your e-shop' mod='mpxmloffers'}:</p>
<h4>{l s='URL for Main XML Offers' mod='mpxmloffers'}: <strong><a href="../modules/mpxmloffers/offersXml.php?id=0&s={Configuration::get('PS_SHOP_EMAIL')|md5}" target="_new">https://{$smarty.server.HTTP_HOST}{$smarty.server.REWRITEBASE}modules/mpxmloffers/offersXml.php?id=0&s={Configuration::get('PS_SHOP_EMAIL')|md5}</a></strong></h4>
<p>
	<button class="btn btn-primary pointer" name="submit" type="submit" style="margin-top: 20px;" id="offers_links">{l s='Offers XML Links' mod='mpxmloffers'}</button>
</p>
<div id="offers_xml_links" style="font-size:11px;">...wait...</div>

<p>{l s='You can select all products from one or more categories, subcategories' mod='mpxmloffers'} - 
{l s='You can select the products one by one from Products drop down menu.' mod='mpxmloffers'} - 
{l s='Each offer product must has lower price than shop price!' mod='mpxmloffers'} -
{l s='Enter the quantity for each offer product who is available.' mod='mpxmloffers'}</p>
 {*$products|print_r*}
<div class="offers-outer" style="font-size:11px;">
	<div class="offers-products">
		<p><strong>{l s='Select Products' mod='mpxmloffers'}:</strong> </p>	
		<select multiple="multiple" id="offerproducts" name="offerproducts[]" class="offerproducts">
		{foreach from=$products  key=productkey item=product}
			<option value='{$product.product_id}_{$product.comb_id}'>{$product.product_name}</option>
		{/foreach}
		</select>
            <div style="text-align:left;">
                <button class="btn btn-primary pointer" name="submit" type="submit" style="margin-top: 20px;" id="save_products">{l s='Save Selected Products in Offers' mod='mpxmloffers'}</button>
            </div>

	</div>
	<div class="offers-categories">
		<p><strong>{l s='Select Category(ies)' mod='mpxmloffers'}:</strong> </p>	
		{$product_cats}
            <div style="text-align:left;">
                <button class="btn btn-primary pointer" name="submit" type="submit" style="margin-top: 20px;" id="save_categories">{l s='Save Selected Categories in Offers' mod='mpxmloffers'}</button>
            </div>
	</div>

	<div class="offers-fields">
	<form name="addoffers" id="addoffers">
		<table class="table product mt-3" style="width:100%;margin-top:25px;border-radius:4px;font-size:11px;">
			<thead id="offers-fields">
				
			
			</thead>
		</table>

        <div style="text-align:center;" class="createoffersbutton">
			<p style="text-align:center;margin:15px 0;display:inline-block;">
			{l s='Offer title' mod='mpxmloffers'}:<br />
			<input type="text" name="offer_name" id="offer_name" placeholder="{l s='My Offer title' mod='mpxmloffers'}" value="" style="width:100%;max-width:400px;min-width:300px;">
			</p>
			<p>{l s='Prices and Quantities are the default for each product. Change as your request. Price must be lower than normal product price!' mod='mpxmloffers'}</p>
           <button class="btn btn-primary pointer" name="submit" type="submit" style="margin-top: 20px;" id="create_offers">{l s='Create Offers XML' mod='mpxmloffers'}</button>
        </div>		
	</form>
	</div>
</div>
