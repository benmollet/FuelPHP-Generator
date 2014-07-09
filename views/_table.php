<table id="<?=$tableName?>"<?=$class?><?=$style?>>
	<thead>
		<tr>
			<?=$headers?>
		</tr>
	</thead>
	<tbody>
		<?=$body?>
		<?=$pagination?>
	</tbody>
</table>
<script>
	$('th.sorting, th.sorting_desc, th.sorting_asc').click(function(e){
		console.log('<?=$sortingBaseLink?>');
		if (e.target.dataset.direction == 'desc')
		{
			newDirection = 'asc';
		}
		else if (e.target.dataset.direction == 'asc')
		{
			newDirection = 'desc';
		}
		navigateTable('<?=$sortingBaseLink?>' + e.target.dataset.attribute + '&<?=$tableName?>-sort-direction=' + newDirection, e);
	});
	
	if (typeof navigateTable === 'undefined')
	{
			navigateTable = function (url, event){
				window.location = url;
			}
	}
	

</script>