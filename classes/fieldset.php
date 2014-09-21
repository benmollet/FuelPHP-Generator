<?php

/**
 * Fieldset Class
 *
 * Define a set of fields that can be used to generate a form or to validate input.
 *
 * @package   Fuel
 * @category  Core
 */
class Fieldset extends \Fuel\Core\Fieldset
{
	/**
	 * Populate the form's values using an input array or object
	 *
	 * @param   array|object
	 * @param   bool
	 * @return  Fieldset  this, to allow chaining
	 */
	public function populate($input, $repopulate = false)
	{
		$fields = $this->field(null, true, false);
		foreach ($fields as $f)
		{
		
			if (is_array($input) or $input instanceof \ArrayAccess)
			{
				// convert form field array's to Fuel dotted notation
				$name = str_replace(array('[',']'), array('.', ''), $f->name);
				
<<<<<<< HEAD
				$explodeName = explode('.', $name);
				if (count($explodeName) > 1)
				{
					$value = array();
=======
				
				$explodeName = explode('.', $name);
				if (count($explodeName) > 1)
				{
>>>>>>> github/master
					foreach ($input->{reset($explodeName)} as $model)
					{
						$value[] = $model->id;
					}
					$f->set_value($value);
				}
				
				// fetch the value for this field, and set it if found
				$value = \Arr::get($input, $name, null);
				
				$value === null and $value = \Arr::get($input, $f->basename, null);
				$value !== null and $f->set_value($value, true);
			}
			elseif (is_object($input) and property_exists($input, $f->basename))
			{
				$f->set_value($input->{$f->basename}, true);
			}
		}

		// Optionally overwrite values using post/get
		if ($repopulate)
		{
			$this->repopulate();
		}

		return $this;
	}
<<<<<<< HEAD
	
	/**
	 * Build the fieldset HTML
	 *
	 * @return  string
	 */
	public function build($action = null)
	{
		$attributes = $this->get_config('form_attributes');
		if ($action and ($this->fieldset_tag == 'form' or empty($this->fieldset_tag)))
		{
			$attributes['action'] = $action;
		}

		$open = ($this->fieldset_tag == 'form' or empty($this->fieldset_tag))
			? $this->form()->open($attributes).PHP_EOL
			: $this->form()->{$this->fieldset_tag.'_open'}($attributes);

		$fields_output = '';

		// construct the tabular form table header
		if ($this->tabular_form_relation)
		{
			$properties = call_user_func($this->tabular_form_model.'::properties');
			$primary_keys = call_user_func($this->tabular_form_model.'::primary_key');
			$fields_output .= '<thead><tr>'.PHP_EOL;
			foreach ($properties as $field => $settings)
			{
				if ((isset($settings['skip']) and $settings['skip']) or in_array($field, $primary_keys))
				{
					continue;
				}
				if (isset($settings['form']['type']) and ($settings['form']['type'] === false or $settings['form']['type'] === 'hidden'))
				{
					continue;
				}
				$fields_output .= "\t".'<th class="'.$this->tabular_form_relation.'_col_'.$field.'">'.(isset($settings['label'])?\Lang::get($settings['label'], array(), $settings['label']):'').'</th>'.PHP_EOL;
			}
			$fields_output .= "\t".'<th>'.\Config::get('form.tabular_delete_label', 'Delete?').'</th>'.PHP_EOL;

			$fields_output .= '</tr></thead>'.PHP_EOL;
		}

		foreach ($this->field() as $f)
		{
			in_array($f->name, $this->disabled) or $fields_output .= $f->build().PHP_EOL;
		}

		$close = ($this->fieldset_tag == 'form' or empty($this->fieldset_tag))
			? $this->form()->close($attributes).PHP_EOL
			: $this->form()->{$this->fieldset_tag.'_close'}($attributes);

		$template = $this->form()->get_config((empty($this->fieldset_tag) ? 'form' : $this->fieldset_tag).'_template',
			"\n\t\t{open}\n\t\t<table>\n{fields}\n\t\t</table>\n\t\t{close}\n");

		$template = str_replace(array('{form_open}', '{open}', '{fields}', '{form_close}', '{close}'),
			array($open, $open, $fields_output, $close, $close),
			$template);

		return $template;
	}
	
	/**
	 * Factory for Fieldset_Field objects
	 *
	 * @param   string
	 * @param   string
	 * @param   array
	 * @param   array
	 * @return  Fieldset_Field
	 */
	public function add($name, $label = '', array $attributes = array(), array $rules = array())
	{
		if ($name instanceof Fieldset_Field)
		{
			if ($name->name == '' or $this->field($name->name) !== false)
			{
				throw new \RuntimeException('Fieldname empty or already exists in this Fieldset: "'.$name->name.'".');
			}

			$name->set_fieldset($this);
			$this->fields[$name->name] = $name;
			return $name;
		}
		elseif ($name instanceof Fieldset)
		{
			if (empty($name->name) or $this->field($name->name) !== false)
			{
				throw new \RuntimeException('Fieldset name empty or already exists in this Fieldset: "'.$name->name.'".');
			}

			$name->set_parent($this);
			$this->fields[$name->name] = $name;
			return $name;
		}
		

		if (empty($name) || (is_array($name) and empty($name['name'])))
		{
			throw new \InvalidArgumentException('Cannot create field without name.');
		}

		// Allow passing the whole config in an array, will overwrite other values if that's the case
		if (is_array($name))
		{
			$attributes = $name;
			$label = isset($name['label']) ? $name['label'] : '';
			$rules = isset($name['rules']) ? $name['rules'] : array();
			$name = $name['name'];
		}

		// Check if it exists already, if so: return and give notice
		if ($name !== 'html' and $field = $this->field($name))
		{
			\Error::notice('Field with this name exists already in this fieldset: "'.$name.'".');
			return $field;
		}

		if ($name === 'html')
		{
			$this->fields[] = new \Fieldset_Field($name, $label, $attributes, $rules, $this);
			return end($this->fields);
		}
		else
		{
			$this->fields[$name] = new \Fieldset_Field($name, $label, $attributes, $rules, $this);
			return $this->fields[$name];
		}
		

		
	}
=======
>>>>>>> github/master
}