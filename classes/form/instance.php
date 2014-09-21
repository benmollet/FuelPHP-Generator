<?php

/**
 * Form Class
 *
 * Helper for creating forms with support for creating dynamic form objects.
 *
 * @package   Fuel
 * @category  Core
 */
class Form_Instance extends \Fuel\Core\Form_Instance
{
	/**
	 * Create a form input
	 *
	 * @param   string|array  either fieldname or full attributes array (when array other params are ignored)
	 * @param   string
	 * @param   array
	 * @return  string
	 */
	public function input($field, $value = null, array $attributes = array())
	{
		if (is_array($field))
		{
			$attributes = $field;
			! array_key_exists('value', $attributes) and $attributes['value'] = '';
		}
		else
		{
			$attributes['name'] = (string) $field;
			$attributes['value'] = (string) $value;
		}

		$attributes['type'] = empty($attributes['type']) ? 'text' : $attributes['type'];

		if ( ! in_array($attributes['type'], static::$_valid_inputs))
		{
			throw new \InvalidArgumentException(sprintf('"%s" is not a valid input type.', $attributes['type']));
		}

		if ($this->get_config('prep_value', true) && empty($attributes['dont_prep']))
		{
			$attributes['value'] = $this->prep_value($attributes['value']);
		}
		unset($attributes['dont_prep']);

		if (empty($attributes['id']) && $this->get_config('auto_id', false) == true)
		{
			$attributes['id'] = $this->get_config('auto_id_prefix', 'form_').$attributes['name'];
		}

		$tag = ! empty($attributes['tag']) ? $attributes['tag'] : 'input';
		unset($attributes['tag']);
		
		$content = '';
		if (isset($attributes['content']) === true)
		{
			$content = $attributes['content'];
		}

		return html_tag($tag, $this->attr_to_string($attributes), $content);
	}
}