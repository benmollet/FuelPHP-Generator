<?php

namespace Generator;

class Form extends \Fuel\Core\Form
{
	protected $config = array();
	protected $preset = 'default';
	public $fieldset;
	
	public function __construct($preset = 'default', $config = array(), $fieldsetName = 'default')
	{
            \Config::load('generator', true);

            $this->config = \Config::get('generator.form.' . $this->preset);
			
			$config = \Arr::merge($this->config, $config);
			
            $this->fieldset = \Fieldset::forge($fieldsetName, $config);

            $this->fieldset->set_config('form_template', \View::forge('generator/template/form/' . $this->preset . '/_inner_form'));
            $this->fieldset->set_config('field_template', \View::forge('generator/template/form/' . $this->preset . '/_field'));
            $this->fieldset->set_config('multi_field_template', \View::forge('generator/template/form/' . $this->preset . '/_multi_field'));
            $this->fieldset->set_config('group_label', \View::forge('generator/template/form/' . $this->preset . '/_group_label'));
            $this->fieldset->set_config('error_template', \View::forge('generator/template/form/' . $this->preset . '/_error'));
	}
        
	public function addText($field, $label = '', $config = array(), $rules = array())
	{
		return $this->addToFieldset($field, $label, $config, $rules, 'text');
	}
	
	public function addInput($field, $type, $label = '', $config = array(), $rules = array())
	{
		return $this->addToFieldset($field, $label, $config, $rules, $type);
	}
	
	public function addButton($field, $label = '', $config = array())
	{
		return $this->addToFieldset($field, $label, $config, array(), 'button');
	}
	
	public function addLink($href, $text, $config = array())
	{
		if (isset($config['link']) === true)
		{
				$config = array_merge($this->config['link'], $config);
		}

		$attributes = $config['attributes'];
		$attributes['href'] = $href;

		$this->addHtml(html_tag('a', $attributes, $text), $config);
	}
	
	public function addHtml($html, $config = array())
	{
		if (isset($config['html']) === true)
		{
			$config = array_merge($this->config['html'], $config);
		}

		$attributes = $config['attributes'];
		$attributes['type'] = 'html';
		$attributes['html'] = $html;

		$field = $this->fieldset->add('html', '', $attributes);
		if (isset($config['template']) === true)
		{
			$field->set_template(\View::forge('generator/template/form/' . $this->preset . '/' . $config['template']));
		}
		
	}
	
	public function addHidden($field, $value = null, $config = array())
	{
		$attributes = $this->getAttributes('hidden', $attributes);

		//Initialize attributes if not set
		if(isset($config['attributes']) === false)
		{
			$config['attributes'] = array();
		}

		//Set value
		$config['attributes']['value'] = $value;

		return $this->addToFieldset($field, '', $config, array(), 'button');
	}
	
	public function addPassword($field, $label = '', $config = array(), $rules = array())
	{
		return $this->addToFieldset($field, $label, $config, $rules, 'password');
	}
	
	public function addRadios($field, $options, $label = '', $checked = null, $config = array(), $rules = array())
	{
		//Initialize attributes if not set
		if(isset($config['attributes']) === false)
		{
			$config['attributes'] = array();
		}

		//Set Options
		$config['attributes']['options'] = $options;
		$config['attributes']['value'] = $checked;

		return $this->addToFieldset($field, $label, $config, $rules, 'radio');
		
	}
	
	public function addCheckboxes($field, $options, $label = '', $checked = array(), $config = array(), $rules = array())
	{
		//Initialize attributes if not set
		if(isset($config['attributes']) === false)
		{
			$config['attributes'] = array();
		}

		//Set Options and values
		$config['attributes']['options'] = $options;
		$config['attributes']['value'] = $checked;

		return $this->addToFieldset($field, $label, $config, $rules, 'checkbox');
	}
	
	public function addFile($field, $label = '', $config = array(), $rules = array())
	{
		return $this->addToFieldset($field, $label, $config, $rules, 'file');
	}
	
	public function addReset($field = 'reset', $value = 'Reset', $label = '', $config = array())
	{
		//Initialize attributes if not set
		if(isset($config['attributes']) === false)
		{
			$config['attributes'] = array();
		}

		$config['attributes']['value'] = $value;

		return $this->addToFieldset($field, $label, $config, array(), 'reset');
	}
	
	public function addSubmit($field = 'submit', $value = 'Submit', $label = '', $config = array())
	{
		//Initialize attributes if not set
		if(isset($config['attributes']) === false)
		{
			$config['attributes'] = array();
		}

		$config['attributes']['value'] = $value;

		return $this->addToFieldset($field, $label, $config, array(), 'submit');
	}
	
	public function addTextarea($field, $label = '', $config = array(), $rules = array())
	{
		return $this->addToFieldset($field, $label, $config, $rules, 'textarea');
	}
	
	public function addSelect($field, $options, $label = '', $value = null, $config = array(), $rules = array())
	{
		//Initialize attributes if not set
		if(isset($config['attributes']) === false)
		{
			$config['attributes'] = array();
		}

		//Set value and options
		$config['attributes']['value'] = $value;
		$config['attributes']['options'] = $options;

		return $this->addToFieldset($field, $label, $config, $rules, 'select');
		
	}
	
	public function addMultiSelect($field, $options, $label = '', $values = array(), $attributes = array(), $rules = array())
	{
		//Initialize attributes if not set
		if(isset($config['attributes']) === false)
		{
			$config['attributes'] = array();
		}

		//Set value and options
		$config['attributes']['value'] = $values;
		$config['attributes']['options'] = $options;
		$config['attributes']['multiple'] = 'multiple';

		return $this->addToFieldset($field, $label, $config, $rules, 'select');
	}
	
	public function build($actionUrl = '')
	{
		$data['form'] = $this->fieldset->build($actionUrl);
		
		return \View::forge('generator/template/form/' . $this->preset . '/_form', $data, false);
	}
        
	protected function addToFieldset($field, $label, $config, $rules, $type)
	{
		//Merge the passed config and the default config for this type
		if (isset($this->config[$type]) === true)
		{
			$config = \Arr::merge($this->config[$type], $config);
		}

		//Get the attributes for the html element
		$attributes = array();
		if (isset($config['attributes']) === true)
		{
			$attributes = $config['attributes'];
		}

		//Set the type
		$attributes['type'] = $type;

		$fieldsetField = $this->fieldset->add($field, $label, $attributes, $rules);

		//Set the template defaults
		$fieldsetField->set_template(\View::forge('generator/template/form/' . $this->preset . '/_field'));

		//Set template if specified.
		if (isset($config['template']) === true)
		{
			$fieldsetField->set_template(\View::forge('generator/template/form/' . $this->preset . '/' . $config['template']));
		}

		return $fieldsetField;
	}
}