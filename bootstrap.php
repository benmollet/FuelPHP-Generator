<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Autoloader::add_classes(array(
	'Form'		=>	__DIR__.'/classes/form.php',
	'ModelForm'	=>	__DIR__.'/classes/modelForm.php',
	'Table'		=>	__DIR__.'/classes/table.php',
	'ModelTable'=>	__DIR__.'/classes/modelTable.php',
	'Form_Instance'	=>	__DIR__.'/classes/form/instance.php',
	'Fieldset_Field'	=>	__DIR__.'/classes/fieldset/field.php',
	'Fieldset'	=>	__DIR__.'/classes/fieldset.php',
));