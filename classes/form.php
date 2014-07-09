<?php

class Form extends \Fuel\Core\Form
{
	protected $formElements = array();
	protected $openCondition;
	protected $preset = 'default';
	
	public function __construct($openCondition = null, $formAttributes = array())
	{
		\Config::load('form', true);
		
		$this->openCondition = $openCondition;
		
		if (isset($formAttributes) === true)
		{
			foreach ($formAttributes as $formAttributeName => $formAttributeValue)
			{
					$this->$formAttributeName = $formAttributeValue;
			}
		}
	}
	
	public function addInput($field, $value = null, $attributes = array())
	{
		if ($attributes === array())
		{
			$attributes = \Config::get('form.' . $this->preset . '.inputAttributes', array());
		}
		
		$this->formElements[] = Form::input($field, $value, $attributes);
	}
	
	public function addButton($field, $value = null, $attributes = array())
	{
		if ($attributes === array())
		{
			$attributes = \Config::get('form.' . $this->preset . '.buttonAttributes', array());
		}
		
		$this->formElements[] = Form::button($field, $value, $attributes);
	}
	
	public function addHidden($field, $value = null, $attributes = array())
	{
		if ($attributes === array())
		{
			$attributes = \Config::get('form.' . $this->preset . '.hiddenAttributes', array());
		}
		
		$this->formElements[] = Form::hidden($field, $value, $attributes);
	}
	
	public function addPassword($field, $value = null, $attributes = array())
	{
		if ($attributes === array())
		{
			$attributes = \Config::get('form.' . $this->preset . '.passwordAttributes', array());
		}
		
		$this->formElements[] = Form::password($field, $value, $attributes);
	}
	
	public function addRadio($field, $value = null, $checked = null, $attributes = array())
	{
		if ($attributes === array())
		{
			$attributes = \Config::get('form.' . $this->preset . '.radioAttributes', array());
		}
		
		$this->formElements[] = Form::radio($field, $value, $checked, $attributes);
	}
	
	public function addCheckbox($field, $value = null, $checked = null, $attributes = array())
	{
		if ($attributes === array())
		{
			$attributes = \Config::get('form.' . $this->preset . '.checkboxAttributes', array());
		}
		
		$this->formElements[] = Form::checkbox($field, $value, $checked, $attributes);
	}
	
	public function addFile($field, $attributes = array())
	{
		if ($attributes === array())
		{
			$attributes = \Config::get('form.' . $this->preset . '.fileAttributes', array());
		}
		
		$this->formElements[] = Form::checkbox($field, $attributes);
	}
	
	public function addReset($field, $value = null, $attributes = array())
	{
		if ($attributes === array())
		{
			$attributes = \Config::get('form.' . $this->preset . '.resetAttributes', array());
		}
		
		$this->formElements[] = Form::reset($field, $value, $attributes);
	}
	
	public function addSubmit($field, $value = null, $attributes = array())
	{
		if ($attributes === array())
		{
			$attributes = \Config::get('form.' . $this->preset . '.submitAttributes', array());
		}
		
		$this->formElements[] = Form::submit($field, $value, $attributes);
	}
	
	public function addTextarea($field, $value = null, $attributes = array())
	{
		if ($attributes === array())
		{
			$attributes = \Config::get('form.' . $this->preset . '.textareaAttributes', array());
		}
		
		$this->formElements[] = Form::textarea($field, $value, $attributes);
	}
	
	public function addSelect($field, $values = null, $options = array(), $attributes = array())
	{
		if ($attributes === array())
		{
			$attributes = \Config::get('form.' . $this->preset . '.selectAttributes', array());
		}
		
		$this->formElements[] = Form::select($field, $values, $options, $attributes);
	}
	
	public function addLabel($label, $id = null, $attributes = array())
	{
		if ($attributes === array())
		{
			$attributes = \Config::get('form.' . $this->preset . '.labelAttributes', array());
		}
		
		$this->formElements[] = Form::label($label, $id, $attributes);
	}
	
	public function generate()
	{
		//Add a submit button if set in the config file
		if (\Config::get('form.' . $this->preset . '.addSubmit') !== null)
		{
			$submitField = \Config::get('form.' . $this->preset . '.addSubmit.field');
			$submitValue = \Config::get('form.' . $this->preset . '.addSubmit.value');
			$submitAttributes = \Config::get('form.' . $this->preset . '.addSubmit.attributes', array());
			$this->formElements[] = $this->addSubmit($submitField, $submitValue, $submitAttributes);
		}
		
		$data['openCondition'] = $this->openCondition;
		$data['formElements'] = $this->formElements;
		
		return View::forge('_form', $data, false);
	}
}