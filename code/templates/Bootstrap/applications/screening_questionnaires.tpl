{title}[[Screening Questionnaires]]{/title}

{literal}
<style>
.editbutton,
.deletebutton {
    cursor: pointer;
    font-family: Arial, sans-serif;
    font-size: 12px;
    font-weight: bold;
    padding: 3px 10px;
    text-decoration: none;
    display: inline-block;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
}
.editbutton {
    background: #f9fdff;
    background: -moz-linear-gradient(top,  #f9fdff 0%, #bbd4e5 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f9fdff), color-stop(100%,#bbd4e5));
    background: -webkit-linear-gradient(top,  #f9fdff 0%,#bbd4e5 100%);
    background: -o-linear-gradient(top,  #f9fdff 0%,#bbd4e5 100%);
    background: -ms-linear-gradient(top,  #f9fdff 0%,#bbd4e5 100%);
    background: linear-gradient(to bottom,  #f9fdff 0%,#bbd4e5 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f9fdff', endColorstr='#bbd4e5',GradientType=0 );
    -moz-box-shadow: inset 0 1px 0 0 #fff;
    -webkit-box-shadow: inset 0 1px 0 0 #fff;
    box-shadow: inset 0 1px 0 0 #fff;
    border: 1px solid #b5b3b5;
    color: #777;
    text-shadow: 1px 1px 0 #fff;
}
.editbutton:hover {
    background: #bbd4e5;
    background: -moz-linear-gradient(top,  #bbd4e5 0%, #f9fdff 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#bbd4e5), color-stop(100%,#f9fdff));
    background: -webkit-linear-gradient(top,  #bbd4e5 0%,#f9fdff 100%);
    background: -o-linear-gradient(top,  #bbd4e5 0%,#f9fdff 100%);
    background: -ms-linear-gradient(top,  #bbd4e5 0%,#f9fdff 100%);
    background: linear-gradient(to bottom,  #bbd4e5 0%,#f9fdff 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#bbd4e5', endColorstr='#f9fdff',GradientType=0 );
}
.deletebutton {
    -moz-box-shadow: inset 0 1px 0 0 #f7c5c0;
    -webkit-box-shadow: inset 0 1px 0 0 #f7c5c0;
    box-shadow: inset 0 1px 0 0 #f7c5c0;
    background: -webkit-gradient( linear, left top, left bottom, color-stop(0.05, #f0a5a1), color-stop(1, #e67e7e) );
    background: -moz-linear-gradient( center top, #f0a5a1 5%, #e67e7e 100% );
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f0a5a1', endColorstr='#e67e7e');
    background-color: #f0a5a1;
    border: 1px solid #b8372e;
    color: #fff;
    text-shadow: 1px 1px 0 #9c3830;
}
.deletebutton:hover {
    background: -webkit-gradient( linear, left top, left bottom, color-stop(0.05, #e67e7e), color-stop(1, #f0a5a1) );
    background: -moz-linear-gradient( center top, #e67e7e 5%, #f0a5a1 100% );
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#e67e7e', endColorstr='#f0a5a1');
    background-color: #e67e7e;
}
</style>
{/literal}
<h1 class="my-account-title">[[My Account]]</h1>
<div class="my-account-list">
    <ul class="nav nav-pills">
        <li class="presentation"><a href="{$GLOBALS.site_url}/my-listings/job/">[[Job Postings]]</a></li>
        <li class="presentation"> <a href="{$GLOBALS.site_url}/system/applications/view/">[[Applications]]</a></li>
        <li class="presentation active"> <a href="{$GLOBALS.site_url}/screening-questionnaires/">[[Screening Questionnaire]]</a></li>
        <li class="presentation"> <a href="{$GLOBALS.site_url}/edit-profile/">[[Company Profile]]</a></li>
    </ul>
</div>
{if not $questionnaires}
    <div class="search-results my-account-listings col-xs-12 my-account-listings-full">
        <div class="form-group__btn">
                <a href="{$GLOBALS.site_url}/screening-questionnaires/new/" class="btn btn__orange btn__bold">[[Create a New Questionnaire]]</a>
        </div>
        <div class="alert alert-danger">
                 [[You have no Questionnaires so far]]
        </div>
    </div>
{else}
    <div class="search-results my-account-listings col-xs-12 my-account-listings-full">
        <h3 class="has-left-postings search-results__title">
            {$questionnaires|count} [[Questionnaire(s)]]
        </h3>
        <div class="form-group__btn">
            <a href="{$GLOBALS.site_url}/screening-questionnaires/new/" class="btn btn__orange btn__bold">[[Create a New Questionnaire]]</a>
        </div>
        
        <article class="media well listing-item listing-item__jobs">
        {foreach item=question from=$questionnaires}
            <div style="display:block">
                <div class="media-body">
                    <div class="media-heading listing-item__title">
                        <a class="link" href="{$GLOBALS.site_url}/screening-questionnaires/edit/{$question.sid}"><span class="strong">{$question.name}</span></a>
                    </div>
                </div>
                <div class="media-right text-right hidden-xs-480">
                    <div class="listing-item__views">
                        <a href="{$GLOBALS.site_url}/screening-questionnaires/edit/{$question.sid}" class="editbutton">[[Edit]]</a>
                    </div>
                     <div class="listing-item__applies">
                        <a href="{$GLOBALS.site_url}/screening-questionnaires/?action=delete&sid={$question.sid}" onclick="return deleteMessage('[[Delete Questionnaire]]', '[[Are you sure you want to delete the selected Questionnaire(s)?
						It will be removed from your Job postings as well.]]', '{$GLOBALS.site_url}/screening-questionnaires/?action=delete&sid={$question.sid}');" class="deletebutton">[[Delete]]</a> 
                   </div>
                </div>
            </div>
        {/foreach}
        </article>

    </div>
{/if}


{literal}
	<script type="text/javascript">
		function deleteMessage(title, message, link){
			$("#messageBox").dialog( 'destroy' ).html(message);
			$("#messageBox").dialog({
				width: 300,
				height: 200,
				modal: true,
				title: title,
					buttons: {
					Ok: function() {
						$(this).dialog('close');
						location.href=link;
					},
					Cancel: function(){
						$(this).dialog('close');
					}
				}
				
			}).dialog( 'open' );
			return false;
		}
	</script>
{/literal}

