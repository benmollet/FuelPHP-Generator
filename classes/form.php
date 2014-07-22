<?php

class Form extends \Fuel\Core\Form
{
	protected $preset = 'default';
	public $fieldset;
	
	public function __construct($fieldsetName = 'default', $formAttributes = array())
	{
		\Config::load('form', true);
		
		$this->fieldset = Fieldset::forge($fieldsetName, $formAttributes);
		
		$this->fieldset->set_config('form_template', View::forge('generator/template/form/' . $this->preset . '/_inner_form'));
		$this->fieldset->set_config('field_template', View::forge('generator/template/form/' . $this->preset . '/_field'));
		$this->fieldset->set_config('multi_field_template', View::forge('generator/template/form/' . $this->preset . '/_multi_field'));
		$this->fieldset->set_config('group_label', View::forge('generator/template/form/' . $this->preset . '/_group_label'));
	}
	
	public function addInput($field, $label = null, $attributes = array())
	{
		$attributes = $this->getAttributes('input', $attributes);
		
		$this->fieldset->add($field, $label, $attributes);
	}
	
	public function addButton($field, $label = null, $attributes = array())
	{
		$attributes = $this->getAttributes('button', $attributes);
		
		$attributes['type'] = 'button';
		
		$this->fieldset->add($field, $label, $attributes);
	}
	
	public function addHidden($field, $label = '', $attributes = array())
	{
		$attributes = $this->getAttributes('hidden', $attributes);
		
		$attributes['type'] = 'hidden';
		
		$this->fieldset->add($field, $label, $attributes);
	}
	
	public function addPassword($field, $label = '', $attributes = array())
	{
		$attributes = $this->getAttributes('password', $attributes);
		
		$attributes['type'] = 'password';
		
		$this->fieldset->add($field, $label, $attributes);
	}
	
	public function addRadios($field, $options, $label = '', $checked = null, $attributes = array())
	{
		if ($attributes === array())
		{
			$attributes = \Config::get('form.' . $this->preset . '.radioAttributes', array());
		}
		
		$attributes['type'] = 'radio';
		$attributes['options'] = $options;
		
		$this->fieldset->add($field, $label, $attributes);
		$this->fieldset->field($field)->set_template(View::forge('generator/template/form/' . $this->preset . '/_radios_field'));
		
	}
	
	public function addCheckboxes($field, $options, $label = '', $checked = false, $attributes = array())
	{
		if ($attributes === array())
		{
			$attributes = \Config::get('form.' . $this->preset . '.checkboxAttributes', array());
		}
		
		$attributes['type'] = 'checkbox';
		$attributes['options'] = $options;
		
		$this->fieldset->add($field, $label, $attributes);
		$this->fieldset->field($field)->set_template(View::forge('generator/template/form/' . $this->preset . '/_checkboxes_field'));
		
	}
	
	public function addFile($field, $attributes = array())
	{
		$attributes = $this->getAttributes('file', $attributes);
		
		$attributes['type'] = 'file';
		
		$this->fieldset->add($field, $label, $attributes);
	}
	
	public function addReset($field, $value = 'Reset', $attributes = array())
	{
		$attributes = $this->getAttributes('reset', $attributes);
		
		$attributes['type'] = 'reset';
		$attributes['value'] = $value;
		
		$this->fieldset->add($field, '', $attributes);
	}
	
	public function addSubmit($field, $value = 'Submit', $attributes = array())
	{
		$attributes = $this->getAttributes('submit', $attributes);
		
		$attributes['type'] = 'submit';
		$attributes['value'] = $value;
		
		$this->fieldset->add($field, '', $attributes);
	}
	
	public function addTextarea($field, $label = '', $attributes = array())
	{
		$attributes = $this->getAttributes('textarea', $attributes);
		
		$attributes['type'] = 'textarea';
		
		$this->fieldset->add($field, $label, $attributes);
	}
	
	public function addSelect($field, $label = '', $options = array(), $values = array(), $attributes = array())
	{
		$attributes = $this->getAttributes('select', $attributes);
		
		$attributes['type'] = 'select';
		$attributes['value'] = $values;
		$attributes['options'] = $options;
		
		$this->fieldset->add($field, $label, $attributes);
	}
	
	public function addMultiSelect($field, $label = '', $options = array(), $values = array(), $attributes = array())
	{
		$attributes = $this->getAttributes('multiSelect', $attributes);
		
		$attributes['type'] = 'select';
		$attributes['value'] = $values;
		$attributes['options'] = $options;
		$attributes['multiple'] = 'multiple';
		
		$this->fieldset->add($field, $label, $attributes);
	}
	
	public function build($actionUrl = '')
	{
		$data['form'] = $this->fieldset->build($actionUrl);
		
		return View::forge('generator/template/form/' . $this->preset . '/_form', $data, false);
	}
	
	protected function getAttributes($type, $setAttributes)
	{
		$configAttributes = \Config::get('form.' . $this->preset . '.' . $type . 'Attributes', array());
		
		foreach ($configAttributes as $configAttributeName => $configAttribute)
		{
			if (isset($setAttributes[$configAttributeName]) === false)
			{
				$setAttributes[$configAttributeName] = $configAttribute;
			}
		}
		
		return $setAttributes;
	}
}