<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $tableTitle; ?></h3>
	</div>
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