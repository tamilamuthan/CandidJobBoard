{if $errors}
	{foreach from=$errors key=error item=error_data}
	    <p class="alert alert-danger">
			{if $error == 'NOT_IMPLEMENTED'}[[There is something missing in the code]]{/if}
			{if $error == 'INVOICE_ID_IS_NOT_SET'}[[Callback parameters are missing required payment information.]]{/if}
			{if $error == 'NONEXISTED_INVOICE_ID_SPECIFIED'}[[System is unable to identify the payment processed.]]{/if}
			{if $error == 'INVOICE_IS_NOT_PENDING'}[[The invoice that you are requesting to process has already been processed before.]]{/if}
			{if $error == 'INVOICE_STATUS_NOT_VERIFIED'}[[Invoice is not verified]]{/if}
			{if $error == 'AMOUNT_IS_NOT_MATCH'}[[You payment is not valid and the product(s) was not purchased. The amount you paid does not match the price of the product(s)]]{/if}
			{if $error == 'UNABLE_TO_PROCESS_PAYMENT'}[[We were unable to process your payment.]]{/if}
	    </p>
	{/foreach}
{elseif $message}
	<p class="alert alert-success">[[Your payment was successfully completed. Please wait for product/service activation.]]</p>
{else}
	{title}[[Thank you for your purchase!]]{/title}
	<div class="checkout-message text-center">
		{assign var='firstProduct' value=false}
		{foreach from=$products item=product name=products_block}
			{if !$product.error && $product.resume_access}
				{capture assign=resumeAccess}
					<p class="paragraph"><a href="{$GLOBALS.site_url}/resumes/" class="link">[[Proceed to searching candidates.]]</a></p>
				{/capture}
			{/if}
		{/foreach}
		{if isset($listingTypes)}
			{foreach from=$listingTypes item=listingType name='types'}
				{if $smarty.foreach.types.first && $smarty.foreach.types.last}
					{capture assign=userSectionListingTypes}{$listingType.name}{/capture}
				{else}
					{if $smarty.foreach.types.first}
						{capture assign=userSectionListingTypes}{$listingType.name}{/capture}
					{elseif $smarty.foreach.types.last}
						{capture assign=userSectionListingTypes}{$listingType.name}{/capture}
					{else}
						{capture assign=userSectionListingTypes}{$listingType.name}{/capture}
					{/if}
				{/if}
			{/foreach}

			<h1 class="title__primary title__primary-small title__centered title__bordered">
				[[Thank you for your purchase!]]
			</h1>
			{if $posting}
				<p class="paragraph">
					[[You have successfully posted your {$listingType.ID|lower}.]]
				</p>
				{if $listingType.ID|lower == 'job'}
					<p class="paragraph">
						<a class="link" href="{$GLOBALS.site_url}{$listingInfo|listing_url}">[[Preview and share your {$listingType.ID|lower}.]]</a>
					</p>
					<p class="paragraph">
						<a class="link" href="{$GLOBALS.site_url}/my-listings/{$listingType.ID|lower}/">
							[[View your job stats in "My Account" section]]
						</a>
					</p>
					{if $product.number_of_listings > 1}
						<p class="paragraph">
							<a class="link" href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Job">[[Proceed to posting your new job.]]</a>
						</p>
					{/if}
					{if $product.featured_employer}
						<div class="form-group">
							[[Your company profile has featured status now.]]
						</div>
					{/if}
					{$resumeAccess}
				{else}
					<p class="paragraph">
						<a class="link" href="{$GLOBALS.site_url}{$listingInfo|listing_url}">[[Preview your resume.]]</a>
					</p>
					<p class="paragraph">
						<a class="link" href="{$GLOBALS.site_url}/my-listings/{$listingType.ID|lower}/">
							[[Edit your resume in "My Account" section]]
						</a>
					</p>
				{/if}
			{else}
				{if $listingType.ID|lower == 'job'}
					<p class="paragraph">
						<a class="link" href="{$GLOBALS.site_url}/my-listings/{$listingType.ID|lower}/">
							[[View your job stats in "My Account" section]]
						</a>
					</p>
					{if ($product.post_job && $product.number_of_listings > 1) || ($product.post_job && $product.number_of_listings == '')}
						<p class="paragraph">
							<a class="link" href="{$GLOBALS.site_url}/add-listing/?listing_type_id={$userSectionListingTypes}">
								[[Proceed to posting your new job.]]
							</a>
						</p>
					{/if}
					{if $product.featured_employer}
						<div class="form-group">
							[[Your company profile has featured status now.]]
						</div>
					{/if}
					{$resumeAccess}
				{else}
					{if $product.post_resume}
						<p class="paragraph">
							<a class="link" href="{$GLOBALS.site_url}/add-listing/?listing_type_id={$userSectionListingTypes}">[[Proceed to posting your resume.]]</a>
						</p>
					{/if}
				{/if}
			{/if}
		{/if}
	</div>
{/if}
