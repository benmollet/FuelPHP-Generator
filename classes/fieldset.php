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
				
				
				$explodeName = explode('.', $name);
				if (count($explodeName) > 1)
				{
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
}