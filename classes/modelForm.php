<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ModelForm extends Form
{
	protected $formAttributes = array();
	protected $form;
	protected $model;
	protected $modelProperties;
	protected $modelPropertiesDefaults;
	protected $preset = 'default';
	public $fieldset;
	
	public function __construct($model, $formAttributes = array(), $fieldsetName = 'default')
	{
		$this->formAttributes = $formAttributes;
		
		$this->form = new Form($fieldsetName, $formAttributes);
		$this->fieldset = $this->form->fieldset;
		$this->model = $model;
	}
	
	public function addInput($field, $label = null, $attributes = array())
	{
		$this->modelProperties[] = $field;
		
		parent::addInput($field, $label, $attributes);
	}
	
	public function addButton($field, $label = null, $attributes = array())
	{
		$this->modelProperties[] = $field;
		
		parent::addButton($field, $label, $attributes);
	}
	
	public function addHidden($field, $label = '', $attributes = array())
	{
		$this->modelProperties[] = $field;
		
		parent::addHidden($field, $label, $attributes);
	}
	
	public function addPassword($field, $label = '', $attributes = array())
	{
		$this->modelProperties[] = $field;
		
		parent::addPassword($field, $label, $attributes);
	}
	
	public function addRadios($field, $options, $label = '', $checked = null, $attributes = array())
	{
		parent::addRadios($field, $options, $label, $checked, $attributes);
	}
	
	public function addCheckboxes($field, $options, $label = '', $checked = false, $template = null, $attributes = array())
	{
		parent::addCheckboxes($field, $options, $label, $checked, $template, $attributes);
	}
	
	public function addFile($field, $attributes = array())
	{
		$this->modelProperties[] = $field;
		
		parent::addFile($field, $attributes);
	}
	
	public function addReset($field, $value = 'Reset', $attributes = array())
	{
		$this->modelProperties[] = $field;
		
		parent::addReset($field, $value, $attributes);
	}
	
	public function addSubmit($field = 'submit', $value = 'Submit', $attributes = array())
	{
		$this->modelProperties[] = $field;
		
		parent::addSubmit($field, $value, $attributes);
	}
	
	public function addTextarea($field, $label = '', $attributes = array())
	{
		$this->modelProperties[] = $field;
		
		parent::addTextarea($field, $label, $attributes);
	}
	
	public function addSelect($field, $label = '', $options = array(), $values = array(), $attributes = array())
	{
		parent::addSelect($field, $label, $values, $options, $attributes);
	}
	
	public function addMultiSelect($field, $label = '', $options = array(), $values = array(), $attributes = array())
	{
		parent::addMultiSelect($field, $label, $values, $options, $attributes);
	}
	
	public function addRelation($modelProperty, $label = '', $options = array(), $values = array(), $attributes = array())
	{
		$this->modelProperties[] = $modelProperty;
		
		if ($options === array())
		{
			$splitProperty = explode('.', $modelProperty);
			$relationType = get_class($this->model->relations(reset($splitProperty)));
			$modelTo = $this->model->relations(reset($splitProperty))->model_to;
			$relationModels = $modelTo::query()
				->get();
			
			foreach ($relationModels as $relationModel)
			{
				$options[$relationModel->id] = $relationModel->{end($splitProperty)};
			}
			
		}
		
		switch($relationType)
		{
			case 'Orm\HasMany':
				parent::addMultiSelect($modelProperty, $label, $options, $values, $attributes);
			break;
		}
	}
	
	public function addModel()
	{
		$model = $this->model;
		$model::set_model_form_fields($this);
	}
	
	public function build($actionUrl = '')
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
				$explodeName = explode('.', $modelProperty);
				$postName = str_replace('.', '_', $modelProperty);
				
				if (count($explodeName) > 1)
				{
					$incomingModelIds = array();
					if (Input::post($postName) !== null)
					{
						$incomingModelIds =Input::post($postName);
					}
					
					$existingModels = $this->model->{reset($explodeName)};
					
					$relationship = $this->model->relations(reset($explodeName));
					
					//Add new
					foreach ($incomingModelIds as $incomingModelId)
					{
						if (key_exists($incomingModelId, $existingModels) === false)
						{
							echo 'here';
							$modelTo = $relationship->model_to;
							$toAddModel = $modelTo::query()
									->where('id', $incomingModelId)
									->get_one();
							
							$toAddModel->{$relationship->key_to[0]} = $this->model->id;
							$toAddModel->save();
						}
					}
					
					//Remove old
					foreach ($existingModels as $existingModelId => $existingModel)
					{
						if (in_array($existingModelId, $incomingModelIds) === false)
						{
							$existingModel->{$relationship->key_to[0]} = null;
							$existingModel->save();
						}
					}
				}
				else if (Input::post($modelProperty) !== null)
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
			
			if ($actionUrl !== '')
			{
				Response::redirect($actionUrl);
			}
			else
			{
				Response::redirect(Uri::current());
			}
		}
		
		return $this->form->build($actionUrl);
	}
	
	public function populate()
	{
		$this->fieldset->populate($this->model);
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