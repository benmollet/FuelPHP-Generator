<?php
echo Form::open($openCondition);

foreach ($formElements as $formElement)
{
	echo $formElement;
}

echo Form::close();
?>