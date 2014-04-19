<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php 
if (Session::get_flash('error'))
{
	foreach (Session::get_flash('error') as $error)
	{
		echo $error;
	}
}
?>
<form <?php echo $ajax === true ? 'class="ajax-form"' : ''; ?> action="<?php echo $submitUrl; ?>" role="form" method="post">
	<?php if (isset($formName) === true) { ?>
	<input type="hidden" name="formName" value="<?php echo $formName; ?>">
	<?php } if ($redirectLocation !== null) { ?>
	<input type="hidden" name="redirect-location" value="<?php echo $redirectLocation; ?>">
	<?php }
	foreach ($formElements as $formElement)
	{
		echo '<div class="row">';
		echo '<div class="form-group col-md-12">';
		if ($formElement['type'] === 'textarea')
		{
			if ($formElement['displayName'] !== null)
			{
				echo '<label for="' . $formElement['name'] . '">' . $formElement['displayName'] . '</label>';
			}
			if (\Input::post($formElement['name']) !== null)
			{
				echo '<textarea class="form-control ' . Variables::get('texteditor', 'summernote') . '" description="' . $formElement['name'] . '" name="' . $formElement['name'] . '>' . \Input::post($formElement['name']) . '</textarea>';
			}
			else if ($formElement['value'] !== '')
			{
				echo '<textarea class="form-control ' . Variables::get('texteditor', 'summernote') . '" description="' . $formElement['name'] . '" name="' . $formElement['name'] . '">' . $formElement['value'] . '</textarea>';
			}
			else
			{
				echo '<textarea class="form-control ' . Variables::get('texteditor', 'summernote') . '" description="' . $formElement['name'] . '" name="' . $formElement['name'] . '"></textarea>';
			}
		}
		else if ($formElement['type'] === 'text')
		{
			echo '<label for="' . $formElement['name'] . '">' . $formElement['displayName'] . '</label>';
			if (\Input::post($formElement['name']) !== null)
			{
				echo '<input type="' . $formElement['type'] . '" value="' . \Input::post($formElement['name']) . '" class="' . $formElement['class'] . '" name="' . $formElement['name'] . '">';
			}
			else if ($formElement['value'] !== '')
			{
				echo '<input type="' . $formElement['type'] . '" value="' . $formElement['value'] . '" class="' . $formElement['class'] . '" name="' . $formElement['name'] . '">';
			}
			else
			{
				echo '<input type="' . $formElement['type'] . '" class="' . $formElement['class'] . '" name="' . $formElement['name'] . '">';
			}
		}
		else if ($formElement['type'] === 'checkbox')
		{
			
			if (\Input::post($formElement['name']) !== null)
			{
				$value = 'value="' . \Input::post($formElement['name']) . '" ';
			}
			else if ($formElement['value'] !== '')
			{
				$value = 'value="' . $formElement['value'] . '" ';
			}
			else
			{
				$value = '';
			}
			
			if (is_array($formElement['properties']) === true and key_exists('select', $formElement['properties']) === true and is_array($formElement['properties']['select']) === true)
			{
				echo '<div>';
				echo '<label for="' . $formElement['name'] . '">' . $formElement['displayName'] . '</label>';
				echo '</div>';
				echo '<select class="col-md-2 chosen-select" name="' . $formElement['name'] . '" name="' . $formElement['name'] . '">';
				if ($formElement['value'] === "0")
				{
					echo '<option value="0" selected="selected">' . $formElement['properties']['select'][0] . '</option>';
					echo '<option value="1">' . $formElement['properties']['select'][1] . '</option>';
				}
				else
				{
					echo '<option value="0">' . $formElement['properties']['select'][0] . '</option>';
					echo '<option value="1" selected="selected">' . $formElement['properties']['select'][1] . '</option>';
				}
				echo '</select>';
			}
			else
			{
				echo $formElement['displayName'];
				echo '<div class="checkbox"><label>';
				echo '<input type="' . $formElement['type'] . '" ' . $value . 'class="' . $formElement['class'] . '" name="' . $formElement['name'] . '">';
				echo '</label></div>';
			}
		}
		else if ($formElement['type'] === 'select')
		{
			$multiple = '';
			if (isset($formElement['multiple']) and $formElement['multiple'] === true)
			{
				$multiple = 'multiple';
			}
			echo '<div><label for="' . $formElement['name'] . '">' . $formElement['displayName'] . '</label></div>';
			echo '<select ' . $multiple . ' class="col-md-2 select2-offscreen" name="' . $formElement['name'] . '[]" id="' . $formElement['name'] . '">';
			foreach ($formElement['options'] as $optionId => $option)
			{
				if ($optionId === $formElement['value'])
				{
					echo '<option value="' . $optionId . '" selected="selected">' . $option . '</option>';
				}
				else
				{
					echo '<option value="' . $optionId . '">' . $option . '</option>';
				}
			}
			echo '</select>';
		}
		else if ($formElement['type'] === 'relation')
		{
//			\Debug::dump($formElement['options']);
//					die;
			$multiple = '';
			if ($formElement['relationType'] === 'Orm\ManyMany')
			{
				$multiple = 'multiple';
			}
			echo '<div><label for="' . $formElement['relationName'] . '_' . $formElement['relationProperty'] . '">' . $formElement['displayName'] . '</label></div>';
			echo '<select ' . $multiple . ' class="col-md-2 select2-offscreen" name="' . $formElement['relationName'] . '_' . $formElement['relationProperty'] . '[]" id="' . $formElement['relationName'] . '_' . $formElement['relationProperty'] . '">';
			foreach ($formElement['options'] as $optionId => $option)
			{
				if ($multiple === 'multiple')
				{
					if (key_exists($optionId, $formElement['value']))
					{
						echo '<option value="' . $optionId . '" selected="selected">' . $option . '</option>';
					}
					else
					{
						echo '<option value="' . $optionId . '">' . $option . '</option>';
					}
				}
				else
				{
					
					if ($optionId == $formElement['value'])
					{
						echo '<option value="' . $optionId . '" selected="selected">' . $option . '</option>';
					}
					else
					{
						echo '<option value="' . $optionId . '">' . $option . '</option>';
					}
				}
			}
			echo '</select>';
		}
		echo '</div>';
		echo '</div>';
	}
	?>
	<button type="submit" class="btn btn-default">Submit</button>
	<?php if ($addBackButton === true) { ?>
	<a href="<?php echo $backLocation; ?>" type="button" class="btn btn-default"><span class="glyphicon glyphicon-arrow-left" /> Back</a>
	<?php } ?>
	<?php 
	if ($buttons !== null and empty($buttons) === false)
	{
		foreach ($buttons as $button)
		{
			echo html_entity_decode($button);
		}
	}
	?>

</form>