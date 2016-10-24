<div>
	{if $is_data_submitted && !$errors}
		<p class="alert alert-success">[[Thank you! Your application has been sent.]]</p>
	{else}
		{foreach from=$errors key=error_code item=error_message}
			<p class="alert alert-danger">
				{if $error_code eq 'APPLY_INPUT_ERROR' || $error_code eq 'APPLY_ERROR'}
					[[Idea file is required]]
				{else}
					[[{$error_message}]]
				{/if}
				{break}
			</p>
		{/foreach}
		{include file='field_errors.tpl'}
		<form method="post" enctype="multipart/form-data" action="{$GLOBALS.site_url}/apply-now-opportunity/" id="apply-form" class="form">
			<input type="hidden" name="is_data_submitted" value="1">
			<input type="hidden" name="listing_id" value="{$listing_id|escape}">
			{if $ideas|@count == 1}
				<input type="hidden" class="hidden hidden-resume" name="id_idea" value="{$ideas[0].id|escape}"/>
			{/if}
			<div class="form-group text-center">
				<label class="form-label">[[Your name]]</label>
				<input type="text" name="name" value="{if $request.name}{$request.name|escape}{else}{$GLOBALS.current_user.FullName|escape}{/if}" class="form-control">
			</div>
			<div class="form-group text-center">
				<label class="form-label">[[Your email]]</label>
				<input type="email" name="email" value="{if $request.email}{$request.email|escape}{else}{$GLOBALS.current_user.username|escape}{/if}" class="form-control">
			</div>
			{if $GLOBALS.current_user.logged_in}
				{if $ideas|@count > 1 && $GLOBALS.current_user.group.id == 'Entrepreneur'}
					<div class="form-group">
						<label class="form-label">[[Select your idea]]</label>
						<select class="form-control" name="id_idea">
							<option value="0">[[Select your idea]]</option>
							{foreach from=$ideas item=idea key=i}
								<option {if $idea.id == $request.id_idea}selected="selected"{elseif $i == 0}selected="selected"{/if} value="{$idea.id}">{$idea.Title}</option>
							{/foreach}
						</select>
					</div>
				{elseif $GLOBALS.current_user.group.id == 'Investor' || !$ideas}
					<div class="form-group">
						<label class="form-label">[[Upload your idea]]</label>
						<input type="file" name="file_tmp" class="form-control"/>
					</div>
				{/if}
			{else}
				<div class="form-group">
					<label class="form-label">[[Upload your idea]]</label>
					<input type="file" name="file_tmp" class="form-control"/>
				</div>
			{/if}
			<div class="form-group">
				<label class="form-label">[[Cover letter]]</label>
				<textarea class="form-control" name="comments" rows="5">{$request.comments|escape}</textarea>
			</div>
			<div class="form-group text-center">
				<input class="btn__submit-modal btn btn__orange btn__bold" type="submit" value="[[Send application]]" onclick="return applySubmit();"/>
			</div>
		</form>
	{/if}
</div>
{literal}
	<script type="text/javascript">
		function applySubmit() {
			var options = {
				target: '.modal-body',
				url:  $('#apply-form').attr('action'),
			};
			$('#apply-form').ajaxSubmit(options);
			return false;
		}
	</script>
{/literal}