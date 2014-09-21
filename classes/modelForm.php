<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ModelForm extends Form
{
        protected $config = array();
	protected $form;
	protected $model;
	protected $modelProperties;
	protected $modelPropertiesDefaults;
	protected $preset = 'default';
	public $fieldset;
	
	public function __construct($model, $preset = 'default', $config = array(), $fieldsetName = 'default')
	{
		$this->form = new Form($preset, $config, $fieldsetName);
		$this->fieldset = $this->form->fieldset;
		$this->model = $model;
	}
	
	public static function forge($model, $preset = 'default', $config = array(), $fieldsetName = 'default')
	{
		$newModelForm = new ModelForm($model, $preset, $config, $fieldsetName);
		return $newModelForm;
	}
        
        public function addText($modelProperty, $label = '', $config = array(), $rules = array())
	{
		$this->modelProperties[] = $modelProperty;
                $rules = $this->getModelRules($modelProperty, $rules);
		
		return $this->form->addText($modelProperty, $label, $config, $rules);
	}
	
	public function addInput($modelProperty, $type, $label = '', $config = array(), $rules = array())
	{
		$this->modelProperties[] = $modelProperty;
                $rules = $this->getModelRules($modelProperty, $rules);
		
		return $this->form->addInput($modelProperty, $type, $label, $config, $rules);
	}
	
	public function addButton($modelProperty, $label = '', $config = array())
	{
		$this->modelProperties[] = $field;
		
                return $this->form->addButton($modelProperty, $label, $config);
	}
	
	public function addLink($link, $text, $config = array())
	{
		return $this->form->addLink($link, $text, $config);
	}
	
	public function addHidden($modelProperty, $value = null, $config = array(), $rules = array())
	{
		$this->modelProperties[] = $field;
                $rules = $this->getModelRules($modelProperty, $rules);
		
                return $this->form->addHidden($modelProperty, $value, $config);
	}
	
	public function addPassword($modelProperty, $label = '', $config = array(), $rules = array())
	{
		$this->modelProperties[] = $field;
                $rules = $this->getModelRules($modelProperty, $rules);
		
                return $this->form->addPassword($modelProperty, '', $config);
	}
	
	public function addRadios($modelProperty, $options, $label = '', $checked = array(), $config = array(), $rules = array())
	{
		$this->modelProperties[] = $modelProperty;
		$rules = $this->getModelRules($modelProperty, $rules);
		
		return $this->form->addRadios($modelProperty, $options, $label = '', $checked, $config, $rules);
	}
	
	public function addCheckboxes($modelProperty, $options, $label = '', $checked = array(), $config = array(), $rules = array())
	{
		$this->modelProperties[] = $modelProperty;
		$this->modelPropertiesDefaults[$modelProperty] = 0;
		
		$rules = $this->getModelRules($modelProperty, $rules);
		
		return $this->form->addCheckboxes($modelProperty, $options, $label, $checked, $config, $rules);
	}
	
//	public function addFile($modelProperty, $label = '', $config = array(), $rules = array())
//	{
//		$this->modelProperties[] = $modelProperty;
//                $rules = $this->getModelRules($modelProperty, $rules);
//		
//		return $this->form->addFile($modelProperty, $label, $config, $rules);
//	}
	
	public function addReset($field = 'reset', $value = 'Reset', $label = '', $config = array())
	{
		$this->modelProperties[] = $field;
		
		return $this->form->addReset($field, $value, $config);
	}
	
	public function addSubmit($field = 'submit', $value = 'Submit', $label = '', $config = array())
	{
		$this->modelProperties[] = $field;
		
		return $this->form->addSubmit($field, $value, $config);
	}
	
	public function addTextarea($modelProperty, $label = '', $config = array(), $rules = array())
	{
		$this->modelProperties[] = $modelProperty;
		$rules = $this->getModelRules($modelProperty, $rules);
		
		return $this->form->addTextarea($modelProperty, $label, $config, $rules);
	}
	
	public function addSelect($modelProperty, $options, $label = '', $value = null, $config = array(), $rules = array())
	{
		$this->modelProperties[] = $modelProperty;
		$this->form->addSelect($modelProperty, $options, $label, $value, $config);
	}
	
	public function addMultiSelect($modelProperty, $options, $label = '', $values = array(), $config = array(), $rules = array())
	{
		$this->modelProperties[] = $modelProperty;
		$this->form->addMultiSelect($modelProperty, $label, $options, $values, $config);
	}
	
	public function addFixed($modelProperty, $value)
	{
		$this->fixedModelProperties[$modelProperty] = $value;
	}
	
	public function addRelation($modelProperty, $label = '', $options = array(), $values = array(), $config = array(), $rules = array())
	{
		$this->modelProperties[] = $modelProperty;
		
		$splitProperty = explode('.', $modelProperty);
			
		if (is_object($this->model) === true)
		{
			$relationType = get_class($this->model->relations(reset($splitProperty)));
			$modelTo = $this->model->relations(reset($splitProperty))->model_to;
		}
		else
		{
			$model = $this->model;
			$relationType = get_class($model::relations(reset($splitProperty)));
			$modelTo = $model::relations(reset($splitProperty))->model_to;
		}
		
		if ($options === array())
		{
			$relationModels = $modelTo::query()
				->get();
			
			foreach ($relationModels as $relationModel)
			{
				$options[$relationModel->id] = $relationModel->{end($splitProperty)};
			}
		}
		
		if (is_object(current($options)) === true)
		{
			$newOptions = array();
			foreach ($options as $option)
			{
				$newOptions[$option->id] = $option->{end($splitProperty)};
			}
			$options = $newOptions;
		}
		
		switch($relationType)
		{
			case 'Orm\HasMany':
				$this->form->addMultiSelect($modelProperty, $options, $label, $values, $config, $rules);
				break;
			case 'Orm\ManyMany':
				$this->form->addMultiSelect($modelProperty, $options, $label, $values, $config, $rules);
				break;
			case 'Orm\BelongsTo':
				$this->form->addSelect($modelProperty, $options, $label, $values, $config, $rules);
				break;
		}
	}
	
	public function addModel($functionName = 'set_model_form_fields')
	{
		$model = $this->model;
		$model::$functionName($this);
                
		return $this;
	}
	
	public function build($actionUrl = '')
	{
		$checkPost = true;
		//To make it harder to accidently allow post to be checked. If post is being sent to another page then it should not be allowed to be checked here.
		if ($actionUrl !== '')
		{
			$checkPost = false;
		}
		
		$input = \Input::post();
		
		//Check for post
		if (Input::method() === 'POST' and $checkPost === true and empty($input) === false)
		{
			if (is_object($this->model) === true)
			{
				$model = $this->model;
			}
			else
			{
				$model = new $this->model;
				$this->model = $model;
			}
			
			$validation = Validation::forge();
			
			if ($validation->run() === false)
			{
				return $this->form->build($actionUrl);
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
					$relationshipType = get_class($relationship);
					
					switch ($relationshipType)
					{
						case 'Orm\ManyMany':
							//Add new
							foreach ($incomingModelIds as $incomingModelId)
							{
								if (key_exists($incomingModelId, $existingModels) === false)
								{
									$modelTo = $relationship->model_to;
									$toAddModel = $modelTo::query()
											->where('id', $incomingModelId)
											->get_one();
									
									$this->model->{reset($explodeName)}[$incomingModelId] = $toAddModel;
									$this->model->save();
								}
							}
							
							//Remove old
							foreach ($existingModels as $existingModelId => $existingModel)
							{
								if (in_array($existingModelId, $incomingModelIds) === false)
								{
									unset($this->model->{reset($explodeName)}[$existingModelId]);
									$this->model->save();
								}
							}
							break;
						case 'Orm\HasMany':
							//Add new
							foreach ($incomingModelIds as $incomingModelId)
							{
								if (key_exists($incomingModelId, $existingModels) === false)
								{
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
							break;
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
			
			if (isset($this->fixedModelProperties) === true)
			{
				foreach ($this->fixedModelProperties as $fixedModelPropertyName => $fixedModelPropertyValue)
				{
					$model->$fixedModelPropertyName = $fixedModelPropertyValue;
				}
			}
			
			
			$model->save();
			
			if ($actionUrl !== '')
			{
				$responseUrl = $actionUrl;
			}
			else
			{
				$responseUrl = Uri::current();
			}
			
			//If the request came from a different url, then send back.
			if (\Input::referrer() !== $responseUrl)
			{
				$responseUrl = \Input::referrer();
			}
			
			Response::redirect($responseUrl);
			
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
        
        protected function getModelRules($modelProperty, $rules)
        {
            if (is_object($this->model) === true)
            {
                    $modelPropertyProperties = $this->model->property($modelProperty);
            }
            else
            {
                    $model = $this->model;
                    $modelPropertyProperties = $model::property($modelProperty);
            }

            $modelRules = array();
            if (isset($modelPropertyProperties['validation']) === true)
            {
                    $modelRules = $modelPropertyProperties['validation'];
            }
            
            return array_merge($modelRules, $rules);
        }
}