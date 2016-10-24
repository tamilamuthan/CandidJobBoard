{capture name="trCancel"}[[Cancel]]{/capture}
{capture name="trDeleteProfile"}[[Delete profile]]{/capture}
<h1 class="my-account-title">[[My Account]]</h1>
<div class="my-account-list">
    <ul class="nav nav-pills">
        {if $GLOBALS.current_user.group.id == "Employer"}
			{title}[[Company Profile]]{/title}
            <li class="presentation"><a href="{$GLOBALS.site_url}/my-listings/job/">[[Job Postings]]</a></li>
			<li class="presentation"> <a href="{$GLOBALS.site_url}/system/applications/view/">[[Applications]]</a></li>
			<li class="presentation active"> <a href="{$GLOBALS.site_url}/edit-profile/">[[Company Profile]]</a></li>
        {elseif $GLOBALS.current_user.group.id == "Investor"}
            {title}[[Opportunities]]{/title}
            <li class="presentation"><a href="{$GLOBALS.site_url}/my-listings/opportunity/">[[Opportunities]]</a></li>
            <li class="presentation"> <a href="{$GLOBALS.site_url}/system/applications/view/">[[Applications]]</a></li>
            <li class="presentation active"> <a href="{$GLOBALS.site_url}/edit-profile/">[[Investor Profile]]</a></li>
        {elseif $GLOBALS.current_user.group.id == "Entrepreneur"}
            {title}[[My Ideas]]{/title}
            <li class="presentation"><a href="{$GLOBALS.site_url}/my-listings/idea/">[[My Ideas]]</a></li>
            <li class="presentation"> <a href="{$GLOBALS.site_url}/system/applications/view/">[[My Applications]]</a></li>
            <li class="presentation active"> <a href="{$GLOBALS.site_url}/edit-profile/">[[Account Settings]]</a></li>
        {else}
			{title}[[Account Settings]]{/title}
            <li class="presentation"><a href="{$GLOBALS.site_url}/my-listings/resume/">[[My Resumes]]</a></li>
			<li class="presentation"> <a href="{$GLOBALS.site_url}/system/applications/view/">[[My Applications]]</a></li>
			<li class="presentation active"> <a href="{$GLOBALS.site_url}/edit-profile/">[[Account Settings]]</a></li>
        {/if}
    </ul>
</div>

{include file='field_errors.tpl'}
{if $action eq "delete_profile" && !$errors}
	<p class="alert alert-success">[[You have successfully deleted your profile!]]</p>
{else}
	{if $form_is_submitted && !$errors}
		<p class="alert alert-success">[[You have successfully changed your profile info!]]</p>
	{/if}
    <div id="delete-profile" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h3 class="modal-title">[[Are you sure you want to delete your profile?]]</h3>
				</div>
				<div class="modal-body">
					<form action="" method="post" id="reason-to-unregister-form" class="form">
						<input type="hidden" name="command" value="unregister-user" />
						<div class="form-group text-center">
							[[Your profile will be deleted permanently.]]
						</div>
						<div class="form-group form-group__btns text-center">
							<button type="submit" class="btn btn__orange btn__bold">
								{$smarty.capture.trDeleteProfile|escape:"quotes"}
							</button>
							<button data-dismiss="modal" aria-hidden="true" class="btn btn__orange btn__bold">
								[[Cancel]]
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<form method="post" action="" enctype="multipart/form-data" class="form edit-profile">
		<input type="hidden" name="action" value="save_info"/>
			{set_token_field}
			{if in_array($GLOBALS.current_user.group.id, array('JobSeeker','Entrepreneur'))}
				{foreach from=$form_fields item=form_field}
					{if $form_field.type == 'password'}
						{input property=$form_field.id}
					{else}
						<div class="form-group">
							<label class="form-label">[[$form_field.caption]] {if $form_field.is_required}*{/if}</label>
							{input property=$form_field.id}
						</div>
					{/if}
				{/foreach}
			{else}
                {include file="registration_fields.tpl"}
			{/if}
			<div class="form-group form-group__btns text-center">
				<button type="submit" class="btn btn__orange btn__bold">
					[[Save]]
				</button>
				<button type="button"
					   data-toggle="modal"
					   data-target="#delete-profile"
					   class="btn btn__orange btn__bold">
					{$smarty.capture.trDeleteProfile|escape:"quotes"}
				</button>
			</div>
		{if $instructionsExist}
			{literal}
			<script type="text/javascript">
				$("document").ready(function(){
					var elem = $(".instruction").prev();
					elem.children().focus(function(){
						$(this).parent().next(".instruction").children(".instr_block").show();
					});
					elem.children().blur(function(){
						$(this).parent().next(".instruction").children(".instr_block").hide();
					});
				});
				CKEDITOR.on('instanceReady', function(e) {
					e.editor.on('focus', function() {
						$("#instruction_"+ e.editor.name).show();
					});
					e.editor.on('blur', function() {
						$("#instruction_"+e.editor.name).hide();
					});
					return;
				});
			</script>
			{/literal}
		{/if}
	</form>
{/if}

{javascript}
	<script>
		$(document).ready(function() {
			var offset = $('.nav-pills li').last().offset();
			$('.nav-pills').scrollLeft(offset.left);
		});
	</script>
{/javascript}