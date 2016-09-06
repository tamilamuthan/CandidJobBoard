<h1 class="title__primary title__primary-small title__centered title__bordered">[[Checkout]]</h1>
	<div class="checkout-container {if 'banner_right_side'|banner}with-banner{/if}">
	<div class="row">
		<div class="sidebar col-xs-10 col-xs-offset-1 col-sm-offset-0">
			<div class="sidebar__content">
				<div class="checkout-sidebar__title form-group">
					<strong>[[Your order]]</strong>
				</div>
				<div class="checkout-product__name form-group">
					{foreach name="product_names_loop" item="product" from=$products}
						<strong class="checkout-product__title">[[{$product.name|paymentTranslate}]]</strong>{if !$smarty.foreach.product_names_loop.last}, {/if}
					{/foreach}
					<span class="pull-right checkout-product__price">
						{capture assign="subtotal"}{tr type="float"}{$product.price}{/tr}{/capture}
						{currencyFormat amount=$product.primaryPrice}
					</span>
				</div>
				{if $promotionCodeInfo}
					<div class="text-right form-group">
						<a href="#" id="delete-promocode" class="checkout-sidebar__delete-discount"></a>
						{capture assign="promoCodeDiscount"}{tr type="float"}{$promotionCodeInfo.discount}{/tr}{/capture}
						{$promotionCodeInfo.code|escape} ({if $promotionCodeInfo.type == 'percentage'}{$promoCodeDiscount}%{else}{currencyFormat amount=$promoCodeDiscount}{/if}):
					<span style="color: #cc0000"> -
						<span>
							{capture assign="discountTotalAmount"}{tr type="float"}{$discountTotalAmount}{/tr}{/capture}
							{currencyFormat amount=$discountTotalAmount}
						</span>
					</span>
					</div>
				{/if}
				<div class="text-right form-group">
					[[Subtotal]]:
					{currencyFormat amount=$subtotal}
				</div>
				{if $tax.tax_amount}
					<p class="text-right form-group">
						[[Tax]]:
						{capture assign="tax"}{tr type="float"}{$tax.tax_amount}{/tr}{/capture}
						{currencyFormat amount=$tax}
					</p>
				{/if}
				<div class="text-right form-group">
					[[Total]]:
					{capture assign="total"}{tr type="float"}{$total_price}{/tr}{/capture}
					{currencyFormat amount=$total}
				</div>
			</div>
		</div>
		<div class="pull-left checkout">
			<form class="form" action="" method="post" enctype="multipart/form-data" name="shoppingCartForm" onsubmit="disableSubmitButton('checkoutSubmit');">
				<div class="form-group form-group__select">
					<label class="form-label">[[How would you like to pay]]</label>
					<select name="gateway" id="payment-gateway__selector" class="form-control">
						{foreach from=$gateways item="gateway" name="gateways"}
							<option value="{$gateway.id}" {if $selected_gateway == $gateway.id}selected="selected"{/if}>[[{$gateway.caption}]]</option>
						{/foreach}
					</select>
				</div>

				<input type="hidden" name="action" value="checkout" />
				<input type="hidden" name="total_price" value="{$total_price}" />
				<input type="hidden" name="discount_total_amount" value="{$discountTotalAmount}" />
				<input type="hidden" name="sub_total_price" value="0" />

				{foreach from=$errors item=field_caption key=error}
					{if $error eq 'EMPTY_VALUE'}
						{assign var="field_caption" value=$field_caption|tr}
						<p class="alert alert-danger col-xs-12">[[Please enter '$field_caption']]</p>
					{elseif $error eq 'NOT_VALID'}
						<p class="alert alert-danger col-xs-12">[[{$field_caption}]]</p>
					{/if}
				{/foreach}
				{if $applied_products}
					<p class="alert alert-success col-xs-12">
						[[You have successfully applied your discount!]]<br/>
					</p>
					<p class="alert alert-info col-xs-12">
						[[You have received a discount of]]
						{if $code_info.type == 'percentage'}
							<strong>{$code_info.discount}%</strong>
						{else}
							{capture assign="discount"}{tr type="float"}{$code_info.discount}{/tr}{/capture}
							<strong>{currencyFormat amount=$discount}</strong>
						{/if}
					</p>
				{/if}
				{if $GLOBALS.settings.enable_promotion_codes == 1 && !$promotionCodeAlreadyUsed}
					<div class="form-group">
						<label for="inputPromotionCode" class="form-label">[[Discount code]]</label>
						<input type="text" name="promotion_code" id="inputPromotionCode" class="form-control" value="" />
						<input type="submit" name="applyPromoCode" value="[[Apply Discount]]" id="applyPromoCode" class="btn__apply-discount btn btn__blue" />
					</div>
				{/if}

				<div class="form-group">
					<input class="btn btn__bold btn__orange" type="submit" id="checkoutSubmit" name="submit" value="[[Place Order]]" />
				</div>

				<div style="visibility: hidden;">
					<input type="submit" name="shoppingCartForm" value="[[Checkout]]" id="shoppingCartForm" />
				</div>
			</form>
		</div>
	</div>
</div>

{javascript}
	<script language="javascript" type="text/javascript">
		$(document).ready(function() {
			$('#delete-promocode').click(function(event) {
				event.preventDefault();
				$("input[name='action']").val('deletePromoCode');
				$("input[name='shoppingCartForm']").click();
			});
		});
	</script>
{/javascript}
