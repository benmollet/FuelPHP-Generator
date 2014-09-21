<<<<<<< HEAD
<?php
class Form extends \Fuel\Core\Form
{
	protected $config = array();
	protected $preset = 'default';
	public $fieldset;
	
	public function __construct($preset = 'default', $config = array(), $fieldsetName = 'default')
	{
            \Config::load('generator', true);

            $this->config = \Config::get('generator.form.' . $this->preset);
			
			$config = Arr::merge($this->config, $config);
			
            $this->fieldset = Fieldset::forge($fieldsetName, $config);

            $this->fieldset->set_config('form_template', View::forge('generator/template/form/' . $this->preset . '/_inner_form'));
            $this->fieldset->set_config('field_template', View::forge('generator/template/form/' . $this->preset . '/_field'));
            $this->fieldset->set_config('multi_field_template', View::forge('generator/template/form/' . $this->preset . '/_multi_field'));
            $this->fieldset->set_config('group_label', View::forge('generator/template/form/' . $this->preset . '/_group_label'));
            $this->fieldset->set_config('error_template', View::forge('generator/template/form/' . $this->preset . '/_error'));
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
                    $field->set_template(View::forge('generator/template/form/' . $this->preset . '/' . $config['template']));
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
		
		return View::forge('generator/template/form/' . $this->preset . '/_form', $data, false);
	}
        
        protected function addToFieldset($field, $label, $config, $rules, $type)
        {
            //Merge the passed config and the default config for this type
            if (isset($this->config[$type]) === true)
            {
                $config = Arr::merge($this->config[$type], $config);
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
            $fieldsetField->set_template(View::forge('generator/template/form/' . $this->preset . '/_field'));
            
            //Set template if specified.
            if (isset($config['template']) === true)
            {
                $fieldsetField->set_template(View::forge('generator/template/form/' . $this->preset . '/' . $config['template']));
            }
            
            return $fieldsetField;
        }
=======
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
>>>>>>> github/master
}