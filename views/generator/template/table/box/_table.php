<div class="box">
	<?php if ($tableTitle !== null) { ?>
	<div class="box-header">
		<h3 class="box-title"><?php echo $tableTitle; ?></h3>
	</div>
	<?php } ?>
	<div class="box-body no-padding table-responsive" style="overflow: auto">
		<table id="<?php echo $tableName; ?>"<?php echo $class; ?><?php echo $style; ?>>
			<thead>
				<tr>
					<?php echo $headers; ?>
				</tr>
			</thead>
			<tbody>
				<?php echo $body; ?>
				<?php echo $pagination; ?>
			</tbody>
		</table>
	</div>
</div>
<script>
	$('#<?php echo $tableName; ?> th.sorting, th.sorting_desc, th.sorting_asc').click(function(e){
		if (e.target.dataset.direction == 'desc')
		{
			newDirection = 'asc';
		}
		else if (e.target.dataset.direction == 'asc')
		{
			newDirection = 'desc';
		}
		navigateTable('<?php echo $sortingBaseLink; ?>' + e.target.dataset.attribute + '&<?php echo $tableName; ?>-sort-direction=' + newDirection, e);
	});
	
	if (typeof navigateTable === 'undefined')
	{
			navigateTable = function (url, event){
				window.location = url;
			}
	}
</script>