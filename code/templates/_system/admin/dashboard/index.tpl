{breadcrumbs}[[Dashboard]]{/breadcrumbs}

<div id="stats">
	<div id="statblocks">
		<div class="statblock active">
			<a class="choose-period" data-target="#today" href="#">
				[[Today]]
			</a>
		</div>
		<div class="statblock">
			<a class="choose-period" data-target="#this-week" href="#">
				[[Last 7 days]]
			</a>
		</div>
		<div class="statblock">
			<a class="choose-period" data-target="#this-month" href="#">
				[[Last 30 days]]
			</a>
		</div>
		<div class="statblock">
			<a class="choose-period" data-target="#total" href="#">
				[[Total]]
			</a>
		</div>
	</div>
</div>

<div id="dashboard">
	<div class="tab-contents">
		{foreach from=$applicationsInfo item=item key=i}
			<div id="{if $i == 'Today'}today{elseif $i=='Last 7 days'}this-week{elseif $i == 'Last 30 days'}this-month{elseif $i == 'Total'}total{/if}" class="tab-pane fade in {if $i == 'Today'}active{/if}">
				<div class="dashboardBlocks">
					<div class="box">
						<div class="box-header text-center">
							<h2 class="box-header__title">[[Sales]]</h2>
						</div>
						<h2 class="box-body text-center">
							{capture assign="paymentAmount"}{tr type="float"}{$invoicesInfo[$i]}{/tr}{/capture}
							{currencyFormat amount=$paymentAmount}
						</h2>
					</div>
				</div>
				<div class="dashboardBlocks">
					<div class="box">
						<div class="box-header text-center">
							<h2 class="box-header__title">[[Jobs Posted]]</h2>
						</div>
						<h2 class="box-body text-center">
							{$listingsInfo.Job.periods[$i]}
						</h2>
					</div>
				</div>
				<div class="dashboardBlocks">
					<div class="box">
						<div class="box-header text-center">
							<h2 class="box-header__title">[[Employer Profiles Created]]</h2>
						</div>
						<h2 class="box-body text-center">
							{$groupsInfo.Employer[$i]}
						</h2>
					</div>
				</div>
				<div class="clr"><br/></div>
				<div class="dashboardBlocks">
					<div class="box">
						<div class="box-header text-center">
							<h2 class="box-header__title">[[Job Seeker Profiles Created]]</h2>
						</div>
						<h2 class="box-body text-center">
							[[{$groupsInfo['Job Seeker'][$i]}]]
						</h2>
					</div>
				</div>
				<div class="dashboardBlocks">
					<div class="box">
						<div class="box-header text-center">
							<h2 class="box-header__title">[[Applications Sent]]</h2>
						</div>
						<h2 class="box-body text-center">
							{$applicationsInfo[$i]}
						</h2>
					</div>
				</div>
				<div class="dashboardBlocks">
					<div class="box">
						<div class="box-header text-center">
							<h2 class="box-header__title">[[Job Alerts Created]]</h2>
						</div>
						<h2 class="box-body text-center">
							{$jobAlertsInfo[$i]}
						</h2>
					</div>
				</div>
			</div>
		{/foreach}
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('.choose-period').click(function(e) {
			e.preventDefault();
			$('.statblock.active').removeClass('active');
			var target = $(this).data('target');
			$('.tab-pane.active').removeClass('active');
			$(this).closest('.statblock').addClass('active');
			$(target).addClass('active');
		});
	});
</script>