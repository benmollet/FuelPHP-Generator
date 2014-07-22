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
}