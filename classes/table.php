<<<<<<< HEAD
<?php

class table 
{
	protected $class;
	protected $config;
	protected $columns;
	protected $tableName;
	protected $rows;
	protected $style;
	protected $preset = 'default';
	
	public function __construct($tableName, $columns, $rows, $totalPages = null, $currentPage = 1, $preset = 'default', $options = array())
	{
		\Config::load('generator', true);
		
		$this->tableName = $tableName;
		$this->preset = $preset;
		
		$config = \Config::get('generator.table.' . $preset);
		
		$config = array_merge($config, $options);
		$this->config = $config;
		
		$this->columns = $columns;
		$this->rows = $rows;
		
		//Set the current page
		if (Input::get($this->tableName . 'Page') !== null)
		{
			$currentPage = Input::get($this->tableName . 'Page');
		}
		
		$this->currentPage = (int) $currentPage;
		$this->totalPages = (int) $totalPages;
	}
	
	public function sortable()
	{
		$this->sortable = true;
	}
	
	public function build()
	{
		//Set the table name
		$data['tableName'] = $this->tableName;
		
		if (isset($this->config['table']['attributes']['class']) === true)
		{
			//Add in datatable if sortable
			if (isset($this->sortable) === true and $this->sortable === true)
			{
				$this->config['table']['attributes']['class'] .= ' dataTable';
			}
			
			$data['class'] = ' class="' . $this->config['table']['attributes']['class'] . '"';
		}
		
		//Set the table style
		$data['style'] = '';
		if (isset($this->config['table']['attributes']['style']) === true)
		{
			$data['style'] = ' style="' . $this->attributes['class'] . '"';
		}
		
		//Generate the table headers
		$data['headers'] = '';
		foreach ($this->columns as $column)
		{
			if (is_array($column) === true)
			{
				if (isset($column['config']['attributes']) === true)
				{
					$data['headers'] .= html_tag('th', $column['config']['attributes'], $column['columnName']);
				}
				else
				{
					$data['headers'] .= html_tag('th', array(), $column['columnName']);
				}
			}
			else
			{
				$data['headers'] .= html_tag('th', array(), $column);
			}
			
		}
		
		//Generate the table body
		$data['body'] = '';
		foreach ($this->rows as $row)
		{
			//Set row class
			$rowClass = '';
			if (key_exists('class', $row) === true)
			{
				$rowClass = ' class="' . $row['class'] . '"';
			}
			
			//Set row style
			$rowStyle = '';
			if (key_exists('style', $row) === true)
			{
				$rowStyle = ' style="' . $row['style'] . '"';
			}
			
			$data['body'] .= '<tr';
			$data['body'] .= $rowClass;
			$data['body'] .= $rowStyle;
			$data['body'] .= '>';
			
			if (key_exists('contents', $row) === true)
			{
				$row = $row['contents'];
			}
			
			foreach ($row as $cell)
			{
				$data['body'] .= '<td>';
				$data['body'] .= $cell;
				$data['body'] .= '</td>';
			}
			
			$data['body'] .= '</tr>';
		}
		
		//Add pagination
		$data['pagination'] = '';
		if (isset($this->currentPage) === true and isset($this->totalPages) === true and $this->totalPages > 1)
		{
			$data['pagination'] .= '<tr>';
			$data['pagination'] .= '<td colspan="' . count($this->columns) . '">';
			$data['pagination'] .= '<ul style="margin: 0" class="pagination">';
			
			$getParameters = \Input::get();
			array_shift($getParameters);

			foreach ($getParameters as $getParameterIndex => $getParameterValue)
			{
				if ($getParameterIndex === $this->tableName . 'Page')
				{
					unset($getParameters[$getParameterIndex]);
				}
			}
			
//			if (isset($this->paginationUrl) === false)
//			{
//				$this->paginationUrl = Uri::current() . $baseGetParameters . $this->tableName . 'Page=';
//			}
			
			if ($this->currentPage === 1)
			{
				$data['pagination'] .= html_tag('li', array('class' => 'disabled'), Html::anchor(Uri::create('', array(), $getParameters), '&laquo;', array('class' => 'disabled pagination-link')));
			}
			else
			{
				// Add the new page to the get parameters
				$getParameters[$this->tableName . 'Page'] = $this->currentPage - 1;
					
				$data['pagination'] .= html_tag('li', array(), Html::anchor(Uri::create('', array(), $getParameters), '&laquo;', array('class' => 'pagination-link')));
			}
			
			for ($pageNumber = 1; $pageNumber <= $this->totalPages; $pageNumber++)
			{
				$attributes = array();
				if ($pageNumber === (int) $this->currentPage)
				{
					$attributes['class'] = 'active';
				}
				
				// Add the new page to the get parameters
				$getParameters[$this->tableName . 'Page'] = $pageNumber;
					
				$data['pagination'] .= html_tag('li', $attributes, Html::anchor(Uri::create('', array(), $getParameters), $pageNumber, array('class' => 'pagination-link')));
			}
			
			if ($this->currentPage === $this->totalPages)
			{
				// Add the new page to the get parameters
				$getParameters[$this->tableName . 'Page'] = $pageNumber;
					
				$data['pagination'] .= html_tag('li', array('class' => 'disabled'), Html::anchor(Uri::create('', array(), $getParameters), '&raquo;', array('class' => 'pagination-link disabled')));
			}
			else
			{
				// Add the new page to the get parameters
				$getParameters[$this->tableName . 'Page'] = $this->currentPage + 1;
					
				$data['pagination'] .= html_tag('li', array(), Html::anchor(Uri::create('', array(), $getParameters), '&raquo;', array('class' => 'pagination-link')));
			}
			
			$data['pagination'] .= '</ul>';
			$data['pagination'] .= '</td>';
			$data['pagination'] .= '</tr>';
			$data['currentPage'] = $this->currentPage;
		}
		
		if (isset($this->config['custom']) === true)
		{
			foreach ($this->config['custom'] as $customName => $customValue)
			{
				$data[$customName] = $customValue;
			}
		}
		
		return View::forge('generator/template/table/' . $this->preset . '/_table', $data, false);
	}
}
=======
<?php

class table 
{
	protected $class;
	protected $columns;
	protected $tableName;
	protected $rows;
	protected $style;
	
    public function __construct($tableName, $columns, $rows, $options = null)
	{
		\Config::load('table', true);
		
		$this->tableName = $tableName;
		
		if (isset($options) === true)
		{
			foreach ($options as $optionName => $optionValue)
			{
					$this->$optionName = $optionValue;
			}
		}
		
		//Set the preset
		$preset = 'default';
		if (isset($this->preset) === false)
		{
			$this->preset = $preset;
		}
		
		if (isset($this->class) === false)
		{
			$this->class = \Config::get('table.' . $preset . '.class');
		}
		
		if (isset($this->style) === false)
		{
			$this->style = \Config::get('table.' . $preset . '.style');
		}

		$this->columns = $columns;
		$this->rows = $rows;
		
		if (Input::get($this->tableName . 'Page') !== null)
		{
			$currentPage = Input::get($this->tableName . 'Page');
			$this->currentPage = (int) $currentPage;
		}
		else
		{
			$this->currentPage = 1;
		}
    }
	
	public function generate()
	{
		//Set the table name
		$data['tableName'] = $this->tableName;
		
		//Set the table class
		$data['class'] = '';
		if (isset($this->class) === true)
		{
			//Addin datatable if sortable
			if (isset($this->sortable) === true and $this->sortable === true)
			{
				$this->class .= ' dataTable';
			}
			
			$data['class'] = ' class="' . $this->class . '"';
		}
		
		//Set the table style
		$data['style'] = '';
		if (isset($this->style) === true)
		{
			$data['style'] = ' style="' . $this->style . '"';
		}
		
		//Generate get parameter links
		$getParameters = \Input::get();

		$sortingBaseGetParameters = '?';
		$pageBaseGetParameters = '';
		foreach ($getParameters as $getParamaterName => $getParamaterValue)
		{
			if ($getParamaterName === '')
			{
				continue;
			}

			if ($getParamaterName !== $this->tableName . '-sort-by' and $getParamaterName !== $this->tableName . '-sort-direction')
			{
				$sortingBaseGetParameters .= $getParamaterName . '=' . $getParamaterValue . '&';
			}

			if ($getParamaterName !== $this->tableName . 'Page')
			{
				$pageBaseGetParameters .= $getParamaterName . '=' . $getParamaterValue . '&';
			}
		}
		
		$sortingBaseGetParameters .= $this->tableName . '-sort-by=';
		$data['sortingBaseLink'] = Uri::create(Uri::current() . $sortingBaseGetParameters);
		
		//Generate the table headers
		$data['headers'] = '';
		foreach ($this->columns as $column)
		{
			if (is_array($column) === true)
			{
				//Header Class
				$headerClass = '';
				if (key_exists('class', $column) === true)
				{
					$headerClass = ' class="' . $column['class'] . '"';
				}
				
				//Header Style
				$headerStyle = '';
				if (key_exists('style', $column) === true)
				{
					$headerStyle = ' style="' . $column['style'] . '"';
				}
				
				//Header attributes
				$headerAttributes = '';
				if (key_exists('attributes', $column) === true)
				{
					foreach ($column['attributes'] as $attributesName => $attributeValue)
					{
						$headerAttributes .= ' data-' . $attributesName . '="' . $attributeValue . '"';
					}
				}
				
				$data['headers'] .= '<th';
				$data['headers'] .= $headerClass;
				$data['headers'] .= $headerStyle;
				$data['headers'] .= $headerAttributes;;
				$data['headers'] .= '>';
				$data['headers'] .= $column['text'];
				$data['headers'] .= '</th>';
			}
			else
			{
				$data['headers'] .= '<th>';
				$data['headers'] .= $column;
				$data['headers'] .= '</th>';
			}
			
		}
		
		//Generate the table body
		$data['body'] = '';
		foreach ($this->rows as $row)
		{
			//Set row class
			$rowClass = '';
			if (key_exists('class', $row) === true)
			{
				$rowClass = ' class="' . $row['class'] . '"';
			}
			
			//Set row style
			$rowStyle = '';
			if (key_exists('style', $row) === true)
			{
				$rowStyle = ' style="' . $row['style'] . '"';
			}
			
			$data['body'] .= '<tr';
			$data['body'] .= $rowClass;
			$data['body'] .= $rowStyle;
			$data['body'] .= '>';
			
			if (key_exists('contents', $row) === true)
			{
				$row = $row['contents'];
			}
			
			foreach ($row as $cell)
			{
				$data['body'] .= '<td>';
				$data['body'] .= $cell;
				$data['body'] .= '</td>';
			}
			
			$data['body'] .= '</tr>';
		}
		
		//Add pagination
		$data['pagination'] = '';
		if (isset($this->currentPage) === true and isset($this->totalPages) === true)
		{
			$data['pagination'] .= '<tr>';
			$data['pagination'] .= '<td colspan="' . count($this->rows) . '">';
			$data['pagination'] .= '<ul style="margin: 0" class="pagination">';
			
			if (isset($this->paginationUrl) === false)
			{
				$this->paginationUrl = Uri::current() . '?' . $pageBaseGetParameters . $this->tableName . 'Page=';
			}
			
			if ($this->currentPage === 1)
			{
				$data['pagination'] .= '<li class="disabled"><a class="disabled pagination-link">&laquo;</a></li>';
			}
			else
			{
				$data['pagination'] .= '<li><a href="' . $this->paginationUrl . ($this->currentPage - 1) . '" class="pagination-link">&laquo;</a></li>';
			}
			
			for ($pageNumber = 1; $pageNumber <= $this->totalPages; $pageNumber++)
			{
				$active = '';
				if ($pageNumber === $this->currentPage)
				{
					$active = ' class="active"';
				}
				$data['pagination'] .= '<li' . $active . '><a href="' . $this->paginationUrl . $pageNumber . '" class="pagination-link">' . $pageNumber . '</a></li>';
			}
			
			if ($this->currentPage === $this->totalPages)
			{
				$data['pagination'] .= '<li class="disabled"><a class="disabled pagination-link">&raquo;</a></li>';
			}
			else
			{
				$data['pagination'] .= '<li><a href="' . $this->paginationUrl . ($this->currentPage + 1) . '" class="pagination-link">&raquo;</a></li>';
			}
			
			$data['pagination'] .= '</ul>';
			$data['pagination'] .= '</td>';
			$data['pagination'] .= '</tr>';
		}
		
		
		
		return View::forge('_table', $data, false);
	}
}
>>>>>>> github/master
