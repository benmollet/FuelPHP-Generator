<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ModelForm
{
	public $form;
	protected $model;
	protected $modelProperties;
	protected $modelPropertiesDefaults;
	
	public function __construct($model, $openCondition = null, $formAttributes = array())
	{
		$this->form = new Form($openCondition, $formAttributes);
		$this->model = $model;
		
	}
	
	public function addInput($modelProperty, $attributes = array())
	{
		$value = $this->_getProperty($modelProperty);
		$this->modelProperties[] = $modelProperty;
		
		$this->form->addInput($modelProperty, $value, $attributes);
	}
	
	public function addHidden($modelProperty, $attributes = array())
	{
		$value = $this->_getProperty($modelProperty);
		$this->modelProperties[] = $modelProperty;
		
		$this->form->addHidden($modelProperty, $value, $attributes);
	}
	
	public function addPassword($modelProperty, $attributes = array())
	{
		$value = $this->_getProperty($modelProperty);
		$this->modelProperties[] = $modelProperty;
		
		$this->form->addPassword($modelProperty, $value, $attributes);
	}
	
	public function addRadio($modelProperty, $options, $defaultOption = null, $attributes = array(), $labelAttributes = array())
	{
		$value = $this->_getProperty($modelProperty);
		
		foreach ($options as $option => $optionName)
		{
			$this->form->addLabel($option, $modelProperty, $labelAttributes);
			
			if ($value !== null and $value === $option)
			{
				$this->form->addRadio($modelProperty, $option, true, $attributes);
			}
			else
			{
				$this->form->addRadio($modelProperty, $option, false, $attributes);
			}
		}
		
		$this->modelProperties[] = $modelProperty;
		$this->modelPropertiesDefaults[$modelProperty] = $defaultOption;
	}
	
	public function addCheckbox($modelProperty, $options, $defaultOption = null, $attributes = array(), $labelAttributes = array())
	{
		$value = $this->_getProperty($modelProperty);
		
		foreach ($options as $option => $optionName)
		{
			$this->form->addLabel($optionName, $modelProperty, $labelAttributes);
			
			if ($value !== null and $value === $option)
			{
				$this->form->addCheckbox($modelProperty, $option, true, $attributes);
			}
			else
			{
				$this->form->addCheckbox($modelProperty, $option, false, $attributes);
			}
		}
		
		$this->modelProperties[] = $modelProperty;
		$this->modelPropertiesDefaults[$modelProperty] = $defaultOption;
	}
	
	public function addTextarea($modelProperty, $attributes = array())
	{
		$value = $this->_getProperty($modelProperty);
		$this->modelProperties[] = $modelProperty;
		
		$this->form->addTextarea($modelProperty, $value, $attributes);
	}
	
	public function addSelect($modelProperty, $values = null, $options = array(), $attributes = array()) 
	{
		$value = $this->_getProperty($modelProperty);
		$this->modelProperties[] = $modelProperty;
		
		$this->form->addSelect($modelProperty, $values, $options, $attributes);
	}
	
	public function addLabel($label, $id = null, $attributes = array())
	{
		$this->form->addLabel($label, $id, $attributes);
	}
	
	public function generate()
	{
		//Check for post
		if (Input::method() === 'POST')
		{
			if (is_object($this->model) === true)
			{
				$model = $this->model;
			}
			else
			{
				$model = new $this->model;
			}
			
			foreach ($this->modelProperties as $modelProperty)
			{
				if (Input::post($modelProperty) !== null)
				{
					$model->$modelProperty = Input::post($modelProperty);
				}
				else if (isset($this->modelPropertiesDefaults[$modelProperty]) === true)
				{
					$model->$modelProperty = $this->modelPropertiesDefaults[$modelProperty];
				}
				else
				{
					$model->$modelProperty = null;
				}
			}
			
			$model->save();
			
		}
		return $this->form->generate();
	}
	
	private function _getProperty($modelProperty)
	{
		if (is_object($this->model) === true)
		{
			return $this->model->$modelProperty;
		}
		else
		{
			return null;
		}
	}
}