<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Form extends \Fuel\Core\Form{
	public $formElements;
	public $formName;
	public $model;
	public $relationModels;
	public $addBackButton = false;
	public $backLocation;
	public $submitUrl;
	public $ajax;
	public $modelProperties;
	public $redirectLocation;
	public $urlSuffix;
	
	public function __construct($model, $properties = null)
    {
		if (\Input::method() === 'POST' and \Input::post('urlSuffix') !== null)
		{
			$this->urlSuffix = \Input::post('urlSuffix');
		}
		else
		{
			$this->urlSuffix = '';
		}
		
		if ($properties !== null)
		{
			if (key_exists('submitUrl', $properties) === true)
			{
				$this->submitUrl = $properties['submitUrl'];
			}
			
			if (key_exists('redirectLocation', $properties) === true)
			{
				if ($this->urlSuffix !== null)
				{
					$this->redirectLocation = \Uri::create($this->urlSuffix . '/' . $properties['redirectLocation']);
				}
				else
				{
					$this->redirectLocation = $properties['redirectLocation'];
				}
			}
			
			if (key_exists('formName', $properties) === true)
			{
				$this->formName = $properties['formName'];
			}
		}
		
		if ($this->submitUrl === null)
		{
			$this->submitUrl = \Uri::current();
		}
		
		if ($this->redirectLocation === null)
		{
			if ($this->urlSuffix !== null)
			{
				$this->redirectLocation = \Uri::create($this->urlSuffix . '/' . \Uri::string());
			}
			else
			{
				$this->redirectLocation = \Uri::current();
			}
		}
		
		if (\Input::is_ajax() === true)
		{
			$this->ajax = true;
		}
		
		$this->formElements = array();
		$this->model = $model;
		
        return;
    }
	
	public function addTextfield($name, $displayName)
	{
		$value = '';
		
		if (is_object($this->model) === true)
		{
			$value = $this->model->$name;
		}
		
		array_push($this->formElements, array(
			'type'			=> 'text',
			'name'			=> $name,
			'displayName'	=> $displayName,
			'value'			=> $value,
			'class'			=> 'form-control',
		));
	}
	
	public function addTextarea($name, $displayName = null)
	{
		$value = '';
		
		if (is_object($this->model) === true)
		{
			$value = $this->model->$name;
		}
		
		array_push($this->formElements, array(
			'type'			=> 'textarea',
			'name'			=> $name,
			'displayName'	=> $displayName,
			'value'			=> $value,
		));
	}
	
	public function addCheckbox($name, $displayName, $properties = null)
	{
		$value = '';
		
		if (is_object($this->model) === true)
		{
			$value = $this->model->$name;
		}
		
		array_push($this->formElements, array(
			'type'			=> 'checkbox',
			'name'			=> $name,
			'displayName'	=> $displayName,
			'value'			=> $value,
			'class'			=> '',
			'properties'	=> $properties,	
		));
	}
	
	public function addRelation($relationObjects, $relationName, $relationProperty, $displayName)
	{
		if (is_array($relationObjects) === false)
		{
			$relationObjects = $relationObjects::query()
					->get();
		}
		
		$values = array();
		$value = null;
		foreach ($relationObjects as $relationObject)
		{
			$values[$relationObject->id] = $relationObject->$relationProperty;
		}
		
		$relations = $this->model->relations();
		switch (get_class($relations[$relationName]))
		{
			case 'Orm\ManyMany':
				$relationType = 'manymany';
				break;
			default:
				echo 'something is wrong';
				die;
				break;
		}
		
		if (is_object($this->model) === true)
		{
			$this->relationModels = $relationObjects;
			switch ($relationType)
			{
				case 'manymany':
					foreach ($this->model->$relationName as $manyObject)
					{
						$value[$manyObject->id] = $manyObject->$relationProperty;
					}
					break;
			}
		}
		
		array_push($this->formElements, array(
			'type'			=> 'select',
			'relationName'			=> $relationName,
			'relationProperty'	=>	$relationProperty,
			'displayName'	=> $displayName,
			'value'			=> $value,
			'options'	=>	$values,
			'relationType'	=>	$relationType,
			'class'			=> '',
		));
	}
	
	public function addModelProperty($propertyName, $propertyValue)
	{
		$this->modelProperties[$propertyName] = $propertyValue;
	}
	
	public function generate()
	{
		return View::forge('form', $this);
	}
	
	public function checkPost()
	{
		
		if (Input::method() == 'POST' and empty($_POST) === false and Input::post('form-name') === $this->formName)
		{
			try
			{
				if (is_object($this->model) === true)
				{
					$model = $this->model;
					$successMessage = 'Edited ';
				}
				else
				{
					$model = new $this->model();
					$successMessage = 'Created new ';
				}
				
				foreach ($this->formElements as $element)
				{
					if ($element['type'] === 'checkbox')
					{
						if (Input::post($element['name']) === null)
						{
							$model->$element['name'] = 0;
						}
						else
						{
							$model->$element['name'] = Input::post($element['name']);
						}
					}
					else if ($element['type'] === 'select')
					{
						$formName = $element['relationName'] . '_' . $element['relationProperty'];
						if ($element['relationType'] === 'manymany')
						{
							//check to delete
							foreach ($this->relationModels as $existingModel)
							{
								if (\Input::post($formName) === null or key_exists($existingModel->id, \Input::post($formName)) === false)
								{
									unset($model->{$element['relationName']}[$existingModel->id]);
								}
							}
							
							//Add in the new ones
							foreach (\Input::post($formName) as $selectedId)
							{
								if (key_exists($selectedId, $model->$element['relationName']) === false)
								{
									$model->{$element['relationName']}[$selectedId] = $this->relationModels[$selectedId];
								}
							}
						}
						else
						{
							$formName = $element['relationName'] . '_' . $element['relationProperty'];
							$relationId = $element['relationName'] . '_id';
							$model->$relationId  = \Input::post($formName);
						}
					}
					else 
					{
						$model->$element['name'] = Input::post($element['name']);
					}
				}
				if ($this->modelProperties !== null and empty($this->modelProperties) === false)
				{
					foreach ($this->modelProperties as $propertyName => $propertyValue)
					{
						$model->$propertyName = $propertyValue;
					}
				}
				
				$model->save();
				
				if (isset($model->display_name) === true)
				{
					Session::set_flash('success', $successMessage . $model->display_name);
				}
				else
				{
					Session::set_flash('success', $successMessage . '(displayName not set in model)');
				}
				
				if (Input::post('redirect-location') !== null)
				{
					Response::redirect(Input::post('redirect-location'));
				}
				
			} catch (Exception $ex) {
				$errors = Session::get_flash('errors');
				if (is_array($errors) === false)
				{
					$errors = array();
				}
				array_push($errors, $ex->getMessage());
				Session::set_flash('error', $errors);
			}
			
			return;
		}
	}
	
	public function addBackButton($defultLocation)
	{
		$this->addBackButton = true;
		
		$this->backLocation = Uri::create($this->urlSuffix . $defultLocation);
		
		return;
	}
}