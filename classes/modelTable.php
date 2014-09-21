<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ModelTable
{
	protected $attributes = array();
	protected $config = array();
	protected $columns = array();
	protected $currentPage = 1;
	protected $options = array();
	protected $model;
	protected $modelClass;
	//protected $properties;
	protected $rows = array();
	protected $rowsLimit = 10;
	protected $sortable = false;
	protected $sortDirection = 'asc';
	protected $tableName;
	protected $totalPages;
	
	public function sort($a, $b)
	{
		$sortingBy = Input::get($this->tableName . '-sort-by');
		
		if ($this->sortDirection === 'asc')
		{
			return strcasecmp($b->$sortingBy, $a->$sortingBy);
		}
		else
		{
			return strcasecmp($a->$sortingBy, $b->$sortingBy);
		}
		
	}
	
	public function __construct($tableName, $inputParameter, $preset = 'default', $options = array())
	{
		\Config::load('generator', true);
		
		//Set the name of the table
		$this->tableName = $tableName;
		
		//Set Preset
		$this->preset = $preset;
		
		//Get default config array
		$config = \Config::get('generator.table.' . $this->preset);
		
		//Merge passed options into config
		$config = array_merge($config, $options);
		$this->config = $config;
		
		//Set sortable, defaults to false
		if (isset($config['sortable']) === true)
		{
			$this->sortable = $config['sortable'];
		}
		
		//Set Page number, defaults to 1
		if (\Input::get($this->tableName . 'Page') !== null)
		{
			$this->currentPage = \Input::get($this->tableName . 'Page');
		}
		
		//Set number of elements per page, defaults to 10
		if (isset($config['rowsLimit']) === true)
		{
				$this->rowsLimit = $config['rowsLimit'];
		}
		
		//Determine if input was models or the model name
		if (is_array($inputParameter) === true)
		{
			if (count($inputParameter) > 0)
			{
				$this->modelClass = get_class(current($inputParameter));
				$this->modelCount = count($inputParameter);
				$this->totalPages = ceil($this->modelCount / $this->rowsLimit);
				$this->pageStart = ($this->rowsLimit * $this->currentPage) - $this->rowsLimit;
				$this->pageEnd = $this->rowsLimit * $this->currentPage;
				
				if (Input::get($this->tableName . '-sort-by') !== null)
				{
					$tableName = $this->tableName;
					$this->sortDirection = Input::get($this->tableName . '-sort-direction');
					usort($inputParameter, array($this, 'sort'));
				}
				
				$this->models = array_slice($inputParameter, $this->pageStart, $this->rowsLimit);
			}
			
		}
		else
		{
			$this->modelClass = $inputParameter;
			$modelName = $inputParameter;
			$this->modelCount = $modelName::count();

			if (\Input::get($this->tableName . '-sort-by') !== null)
			{
				$this->sortBy = \Input::get($this->tableName . '-sort-by');

				$sortBy = \Input::get($this->tableName . '-sort-by');
				$sortBy = str_replace('->', '.', $sortBy);
				$related = explode('.', $sortBy);
				array_pop($related);

				if (\Input::get($this->tableName . '-sort-direction') !== null)
				{
					$this->models = $modelName::query()
						->rows_offset(($this->rowsLimit * $this->currentPage) - $this->rowsLimit)
						->rows_limit($this->rowsLimit)
						->related($related)
						->order_by($sortBy, \Input::get($this->tableName . '-sort-direction'))
						->get();
					$this->sortDirection = \Input::get($this->tableName . '-sort-direction');
				}
				else
				{
					$this->models = $modelName::query()
						->rows_offset(($this->rowsLimit * $this->pacurrentPagege) - $this->rowsLimit)
						->rows_limit($this->rowsLimit)
						->related($related)
						->order_by($sortBy)
						->get();
					$this->sortDirection = 'desc';
				}
			}
			else
			{
				$this->models = $modelName::query()
					->rows_offset(($this->rowsLimit * $this->currentPage) - $this->rowsLimit)
					->rows_limit($this->rowsLimit)
					->get();
			}


			$this->totalPages = ceil($this->modelCount / $this->rowsLimit);
			$this->pageStart = ($this->rowsLimit * $this->currentPage) - $this->rowsLimit;
			$this->pageEnd = $this->rowsLimit * $this->currentPage;
		}
	}
	
	public function addModel($modelName = null)
	{
		if ($modelName === null and $this->modelClass !== null)
		{
			$modelClass = $this->modelClass;
			$modelClass::set_table_columns($this);
		}
		else if ($this->modelClass !== null)
		{
			$modelName::set_table_columns($this);
		}
		
	}
	
	public function addTimestamp($modelProperty, $columnName = null, $config = array())
	{
		if ($columnName == null)
		{
			$columnName = $modelProperty;
		}
		
		$newColumn['type'] = 'timestamp';
		$newColumn['modelProperty'] = $modelProperty;
		$newColumn['config'] = array_merge($this->config, $config);
		$newColumn['columnName'] = $columnName;
		
		$this->columns[$columnName] = $newColumn;
	}
	
	public function addLink($href, $text, $linkModelProperty = null, $textModelProperty = null, $columnName = null, $sortBy = null, $config = array())
	{
		if ($columnName === null)
		{
			//Base column name off link model property if it's null fallback on text if also null
			if ($linkModelProperty !== null)
			{
				//If it's an array use the first property
				if (is_array($linkModelProperty) === true)
				{
					$columnName = current($linkModelProperty);
				}
				else
				{
					$columnName = $linkModelProperty;
				}
			}
			else
			{
				//If it's an array use the first property
				if (is_array($text) === true)
				{
					$columnName = current($text);
				}
				else
				{
					$columnName = $text;
				}
			}
		}
		
		$newColumn = array();
		$newColumn['type'] = 'link';
		$newColumn['href'] = $href;
		$newColumn['text'] = $text;
		$newColumn['linkModelProperty'] = $linkModelProperty;
		$newColumn['textModelProperty'] = $textModelProperty;
		$newColumn['columnName'] = $columnName;
		
		//Set the config
		$defaultConfig = $this->config;
		$newColumn['config'] = array_merge($defaultConfig, $config);
		
		//Initilize the attributes if blank
		if (isset($newColumn['config']['column']['attributes']) === false)
		{
			$newColumn['config']['column']['attributes'] = array();
		}
		
		if (isset($sortBy) === true and isset($this->sortable) === true and $this->sortable === true)
		{
			//Add sorting to the classname
			if (isset($newColumn['config']['column']['attributes']['class']) === true)
			{
				$newColumn['config']['column']['attributes']['class'] .= ' pagination-link sorting';
			}
			else
			{
				$newColumn['config']['column']['attributes']['class'] = 'pagination-link sorting';
			}
			
			//Set the sort attribute
			$newColumn['config']['column']['attributes']['data-attribute'] = $sortBy;
			
			//Set the direction
			$sortByInput = htmlspecialchars_decode(\Input::get($this->tableName . '-sort-by'));
			$sortDirection = \Input::get($this->tableName . '-sort-direction');

			if ($sortBy === $sortByInput)
			{
				if ($sortDirection !== null)
				{
					$newColumn['config']['column']['attributes']['class'] .= '_' . $sortDirection;
					$newColumn['config']['column']['attributes']['data-direction'] = $sortDirection;
				}
				else 
				{
					$newColumn['config']['column']['attributes']['class'] .= '_' . 'asc';
					$newColumn['config']['column']['attributes']['data-direction'] = 'asc';
				}
			}
			else
			{
				$newColumn['config']['column']['attributes']['data-direction'] = 'asc';
			}
		}
		
		$this->columns[] = $newColumn;
	}
	
	public function addProperty($modelProperty, $columnName = null, $config = array())
	{
		$newColumn = array();

		if ($columnName === null)
		{
			$columnName = $modelProperty;
		}
		
		$getParameters = \Input::get();
		array_shift($getParameters);
		
		foreach ($getParameters as $getParameterIndex => $getParameterValue)
		{
			if ($getParameterIndex === $this->tableName . '-sort-by' or $getParameterIndex === $this->tableName . '-sort-direction')
			{
				unset($getParameters[$getParameterIndex]);
			}
		}
		
		// Set the column config
		$newColumn['config'] = array_merge($this->config, $config);
		
		if (isset($this->sortable) === true and $this->sortable === true)
		{
			//Add sorting to the classname
			if (isset($newColumn['config']['attributes']['class']) === true)
			{
				$newColumn['config']['attributes']['class'] .= ' sorting';
			}
			else
			{
				$newColumn['config']['attributes']['class'] = 'sorting';
			}
			
			//Set the sort attribute
			$getParameters[$this->tableName . '-sort-by'] = $modelProperty;
			
			//Set the direction
			$sortByInput = htmlspecialchars_decode(\Input::get($this->tableName . '-sort-by'));
			$sortDirection = \Input::get($this->tableName . '-sort-direction');
			
			// Reverse the sort direction
			if ($sortDirection === 'desc')
			{
				$sortDirection = 'asc';
			}
			else
			{
				$sortDirection = 'desc';
			}

			if ($modelProperty === $sortByInput)
			{
				if ($sortDirection !== null)
				{
					$newColumn['config']['attributes']['class'] .= '_' . $sortDirection;
					$getParameters[$this->tableName . '-sort-direction'] = $sortDirection;
				}
				else 
				{
					$newColumn['config']['attributes']['class'] .= '_' . 'asc';
					$getParameters[$this->tableName . '-sort-direction'] = 'asc';
				}
			}
			else
			{
				$getParameters[$this->tableName . '-sort-direction'] = 'asc';
			}
		}
		
		$newColumn['columnName'] = \Html::anchor(Uri::create(null, array(), $getParameters), $columnName);
		$newColumn['modelProperty'] = $modelProperty;
		$newColumn['type'] = 'property';

		$this->columns[] = $newColumn;
	}
	
	public function build()
	{
		$columns = array();
		$rows = array();

		//Check for columns
		if (empty($this->columns) === true)
		{
			return;
		}

		//Populate the rows
		foreach ($this->models as $model)
		{
			$newRow = array();
			foreach ($this->columns as $column)
			{
				switch ($column['type'])
				{
					case 'link':
						$newCell = self::generateLink($column, $model);
						break;

					case 'timestamp':
						$column['config']['timestamp']['attributes']['data-title'] = Date::forge($model->$column['modelProperty'])->format("%m/%d/%Y %H:%M");
						$text = Date::time_ago($model->$column['modelProperty']);
						$newCell = html_tag('p', $column['config']['timestamp']['attributes'], $text);
						break;

					case 'property':
						if (isset($model->{$column['modelProperty']}) === true)
						{
							$newCell = $model->{$column['modelProperty']};
						}
						else
						{
							$newCell = '';
						}
						
						break;
				}

				$newRow[] = $newCell;
			}

			$this->rows[] = $newRow;
		}
		

		$table = new Table($this->tableName, $this->columns, $this->rows, $this->totalPages, $this->currentPage, $this->preset, $this->config);
		
		if ($this->sortable === true)
		{
			$table->sortable();
		}
		
		return $table->build();
	}
	
	public function setPreset($presetName)
	{
		$this->preset = $presetName;
	}
	
	protected function generateLink($column, $model)
	{
		//Recursive if we re doing more than one link in one call
		if (is_array($column['href']) === true and is_array(current($column['href'])) === true)
		{
			$cellContents = '';
			foreach ($column['href'] as $link)
			{
				//Set attributes to those from multipleLinks
				if (isset($column['config']['multipleLinks']) === true)
				{
					$link['config']['link']['attributes'] = $column['config']['multipleLinks']['attributes'];
				}
				else
				{
					
					$link['config']['link']['attributes'] = array();
				}
				
				$cellContents .= self::generateLink($link, $model);
			}
			return $cellContents;
		}
		
		//Build Link
		if (is_array($column['href']) === false)
		{
			$column['href'] = array($column['href']);
		}
		
		if (is_array($column['linkModelProperty']) === false)
		{
			$column['linkModelProperty'] = array($column['linkModelProperty']);
		}
		
		$linkBuilder = '';
		for ($i = 0; $i < count($column['href']); $i++)
		{
			if (isset($column['href'][$i]) and $column['href'][$i] !== null)
			{
				$linkBuilder .= $column['href'][$i];
			}
			
			if (isset($column['linkModelProperty'][$i]) and $column['linkModelProperty'][$i] !== null)
			{
				$linkBuilder .= $model->{$column['linkModelProperty'][$i]};
			}
		}

		//Build Text
		if (is_array($column['text']) === false)
		{
			$column['text'] = array($column['text']);
		}
		
		if (isset($column['textModelProperty']) === true and is_array($column['textModelProperty']) === false)
		{
			$column['textModelProperty'] = array($column['textModelProperty']);
		}
		
		$textBuilder = '';
		for ($i = 0; $i < count($column['text']); $i++)
		{
			if (isset($column['text'][$i]) and $column['text'][$i] !== null)
			{
				$textBuilder .= $column['text'][$i];
			}
			
			if (isset($column['textModelProperty'][$i]) and $column['textModelProperty'][$i] !== null)
			{
				$textBuilder .= $model->{$column['textModelProperty'][$i]};
			}
		}

		$column['config']['link']['attributes']['href'] = Uri::create($linkBuilder);
		
		//Return the built html tag or nothing if the next is blank
		if ($textBuilder !== '')
		{
			return html_tag('a', $column['config']['link']['attributes'], $textBuilder);
		}
		else
		{
			return '';
		}
		
	}
}