<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<ul class="list-group">
<?php foreach ($elements as $element) { ?>
	<li data-modelid="<?php echo $element->id; ?>" class="list-group-item"><?php if ($reorder === true) { ?><div class="fa fa-arrows-v task-sort-icon"></div><?php } ?><div style="display: inline-block; padding-left: 10px;"><?php echo $element->$attribute; ?></div></li>
<?php } ?>
</ul>

<?php if ($reorder === true) { 
	echo Form::open(array('id' => 'orderForm'));
	echo Form::hidden('order', null, array('id' => 'orderFormInput'));
	echo Form::submit('submit', 'Save', array('class' => 'btn btn-primary'));
	echo Form::close();
?>

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<script>
	var order = {};
	
	function updateIndex() {
		var counter = 0;
		$.each($('.list-group-item'), function(index, item){
			order[item.dataset.modelid] = counter;
			counter++;
		});
		$('#orderFormInput')[0].value = JSON.stringify(order);
	}
	
	$('.list-group').sortable({
		stop: updateIndex,
	});
</script>
<?php } ?>