<<<<<<< HEAD
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
=======
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ModelTable
{
	protected $columns = array();
	protected $currentPage;
	protected $models;
	protected $model;
	//protected $properties;
	protected $rowsLimit;
	protected $sortable;
	protected $tableName;
	protected $totalPages;
	
	public function __construct($tableName, $inputParameter, $options = null)
	{
		\Config::load('modelTable', true);
		
		$this->tableName = $tableName;
		
		if (isset($options) === true)
		{
			if (isset($options) === true)
			{
				foreach ($options as $optionName => $optionValue)
				{
						$this->$optionName = $optionValue;
				}
			}
		}
		
		//Set the preset
		$preset = 'default';
		if (isset($this->preset) === false)
		{
			$this->preset = $preset;
		}
		
		//Set sortable
		if (isset($this->sortable) === false)
		{
			if (\Config::get('modelTable.' . $this->preset . '.sortable') !== null)
			{
				$this->sortable = \Config::get('modelTable.' . $this->preset . '.sortable') !== null;
			}
			else
			{
				//Default to false;
				$this->sortable = false;
			}
		}
		
		//Set Page number
		$this->currentPage = 1;
		if (\Input::get($this->tableName . 'Page') !== null)
		{
			$this->currentPage = \Input::get($this->tableName . 'Page');
		}
		
		//Set number of elements per page
		if (isset($this->rowsLimit) === false)
		{
			if (\Config::get('modelTable.' . $this->preset . '.rowsLimit') !== null)
			{
				$this->rowsLimit = \Config::get('modelTable.' . $this->preset . '.rowsLimit') !== null;
			}
			else
			{
				//Default to 10;
				$this->rowsLimit = 10;
			}
		}
		
		//Determine if input was models or the model name
		if (is_array($inputParameter) === true)
		{
			$this->models = $inputParameter;
			$this->modelCount = count($this->models);
			
			$this->pageCount = ceil($this->modelCount / $this->rowsLimit);
			$this->pageStart = ($this->rowsLimit * $this->currentPage) - $this->rowsLimit;
			$this->pageEnd = $this->rowsLimit * $this->currentPage;
		}
		else
		{
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
	
	public function addModel($modelName)
	{
		$modelName::set_table_columns($this);
	}
	
	public function addTimestamp($modelProperty, $columnName = null)
	{
		if ($columnName == null)
		{
			$columnName = $modelProperty;
		}
		
		$newColumn['type'] = 'timestamp';
		$newColumn['modelProperty'] = $modelProperty;
		
		$this->columns[$columnName] = $newColumn;
	}
	
	public function addLink($href, $text, $linkModelProperty = null, $textModelProperty = null, $columnName = null, $options = null)
	{
		if ($columnName == null)
		{
			if ($text === '' and $linkModelProperty !== null)
			{
				$columnName = $linkModelProperty;
			}
			else
			{
				$columnName = $text;
			}
		}
		
		$newColumn['type'] = 'link';
		$newColumn['text'] = $text;
		$newColumn['href'] = $href;
		$newColumn['textModelProperty'] = $textModelProperty;
		$newColumn['linkModelProperty'] = $linkModelProperty;
		$newColumn['options'] = $options;
		$this->columns[$columnName] = $newColumn;
	}
	
	public function addProperty($property, $columnName = null)
	{
		if ($columnName === null)
		{
			$columnName = $property;
		}
		
		$this->columns[$columnName] = $property;
	}
	
	public function build()
	{
		$columns = array();
		$rows = array();
		
		if (isset($this->columns) === true)
		{
			foreach ($this->columns as $columnName => $column)
			{
				$column = array();
				if (is_array($column) === true)
				{
					if (key_exists('property', $column) === true and is_array($column['property']) === false)
					{
						$column['attributes']['attribute'] = $column['property'];
						$column['attributes']['direction'] = 'asc';
					}
				}
				else 
				{
					$column['attributes']['attribute'] = $property;
					$column['attributes']['direction'] = 'asc';
				}
				$column['text'] = $columnName;

				if (isset($this->sortable) === true and $this->sortable === true)
				{
					$sortBy = null;
					$sortDirectionClass = '';
					if (is_array($column) === true)
					{
						if (key_exists('property', $column) === true and is_array($column['property']) === false)
						{
							$sortDirectionClass = 'sorting';
							$sortBy = $column['property'];
						}
						else if (key_exists('sortBy', $column))
						{
							$sortDirectionClass = 'sorting';
							$sortBy = $column['sortBy'];
						}
					}
					else
					{
						$sortDirectionClass = 'sorting';
						$sortBy = $column;
					}

					$sortByInput = \Input::get($this->tableName . '-sort-by');
					$sortDirection = \Input::get($this->tableName . '-sort-direction');


					if ($sortBy !== null and $sortBy === $sortByInput)
					{
						if ($sortDirection !== null)
						{
							$sortDirectionClass .= '_' . $sortDirection;
							$column['attributes']['direction'] = $sortDirection;
						}
						else 
						{
							$sortDirectionClass .= '_' . 'asc';
							$column['attributes']['direction'] = 'asc';
						}
					}


					if (isset($coulumn['class']) === false or $column['class'] === '')
					{
						$column['class'] = $sortDirectionClass;
					}
					else
					{
						$column['class'] .= ' ' . $sortDirectionClass;
					}
				}

				$columns[] = $column;
			}
		}
		
		foreach ($this->models as $model)
		{
			$row = array();
			if (isset($this->columns) === true)
			{
				foreach ($this->columns as $columnName => $column)
				{
					if (is_array($column) === true)
					{
						if ($column['type'] === 'link')
						{
							$linkClass = '';
							$linkStyle = '';
							if (key_exists('options', $column) === true and $column['options'] !== null)
							{
								if (key_exists('class', $column['options']) === true and $column['options']['class'] !== '')
								{
									$linkClass = ' class="' . $column['options']['class'] . '"';
								}

								if (key_exists('style', $column['options']) === true and $column['options']['style'] !== '')
								{
									$linkClass = ' style="' . $column['options']['style'] . '"';
								}
							}

							if ($linkClass === '')
							{
								if (\Config::get('modelTable.' . $this->preset . '.link.class') !== null)
								{
									$linkClass = ' class="' . \Config::get('modelTable.' . $this->preset . '.link.class') . '"';
								}
							}

							if ($linkStyle === '')
							{
								if (\Config::get('modelTable.' . $this->preset . '.link.style') !== null)
								{
									$linkStyle = ' style="' . \Config::get('modelTable.' . $this->preset . '.link.style') . '"';
								}
							}
							
							$linkText = $column['text'];
							if (isset($column['textModelProperty']) === true)
							{
								$linkText .= $model->{$column['textModelProperty']};
							}
							
							$row[] = '<a href="' . $this->_generateLink($column, $model) . '"' . $linkClass . $linkStyle .'>' . $linkText . '</a>';
						}
						else if ($column['type'] === 'timestamp')
						{
							$row[] = '<p style="display: inline" data-toggle="tooltip" data-placement="top" data-title="' . Date::forge($model->$column['modelProperty'])->format("%m/%d/%Y %H:%M") . '">' . Date::time_ago($model->$column['modelProperty']) . '</p>';
						}
					}
					else
					{
						$row[] = $model->$column;
					}
				}
			}
			$rows[] = $row;
		}
		
		$options = null;
		if (isset($this->tableOptions) === true)
		{
			$options = $this->tableOptions;
		}
		
		$options['currentPage'] = $this->currentPage;
		$options['totalPages'] = $this->totalPages;
		
		if ($this->sortable === true)
		{
			$options['sortable'] = true;
		}
		
		$table = new Table($this->tableName, $columns, $rows, $options);
		return $table->generate();
	}
	
	protected function _generateLink($column, $model)
	{
		$href = $column['href'];
		$linkModelProperty = $column['linkModelProperty'];
		
		if (is_array($href) === true and is_array($linkModelProperty) === true)
		{
			$linkBuilder = '';
			for ($i = 0; $i < count($href); $i++)
			{
				if (isset($href[$i]) and $href[$i] !== null)
				{
					$linkBuilder .= $href[$i];
				}
				
				if (isset($linkModelProperty[$i]) and $linkModelProperty[$i] !== null)
				{
					$linkBuilder .= $model->{$property[$i]};
				}
			}
			
			return Uri::Create($linkBuilder);
		}
		else
		{
			return Uri::create($href . '/' . $model->{$linkModelProperty});
		}
	}
>>>>>>> github/master
}