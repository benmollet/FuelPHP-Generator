<?php

namespace Generator;

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
		if (\Input::get($this->tableName . 'Page') !== null)
		{
			$currentPage = \Input::get($this->tableName . 'Page');
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
		
		if (isset($this->config['attributes']['class']) === true)
		{
			//Add in datatable if sortable
			if (isset($this->sortable) === true and $this->sortable === true)
			{
				$this->config['attributes']['class'] .= ' dataTable';
			}
			
			$data['class'] = ' class="' . $this->config['attributes']['class'] . '"';
		}
		
		//Set the table style
		$data['style'] = '';
		if (isset($this->config['attributes']['style']) === true)
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
				$data['pagination'] .= html_tag('li', array('class' => 'disabled'), \Html::anchor(\Uri::create('', array(), $getParameters), '&laquo;', array('class' => 'disabled pagination-link')));
			}
			else
			{
				// Add the new page to the get parameters
				$getParameters[$this->tableName . 'Page'] = $this->currentPage - 1;
					
				$data['pagination'] .= html_tag('li', array(), \Html::anchor(\Uri::create('', array(), $getParameters), '&laquo;', array('class' => 'pagination-link')));
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
					
				$data['pagination'] .= html_tag('li', $attributes, \Html::anchor(\Uri::create('', array(), $getParameters), $pageNumber, array('class' => 'pagination-link')));
			}
			
			if ($this->currentPage === $this->totalPages)
			{
				// Add the new page to the get parameters
				$getParameters[$this->tableName . 'Page'] = $pageNumber;
					
				$data['pagination'] .= html_tag('li', array('class' => 'disabled'), \Html::anchor(\Uri::create('', array(), $getParameters), '&raquo;', array('class' => 'pagination-link disabled')));
			}
			else
			{
				// Add the new page to the get parameters
				$getParameters[$this->tableName . 'Page'] = $this->currentPage + 1;
					
				$data['pagination'] .= html_tag('li', array(), \Html::anchor(\Uri::create('', array(), $getParameters), '&raquo;', array('class' => 'pagination-link')));
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
		
		return \View::forge('generator/template/table/' . $this->preset . '/_table', $data, false);
	}
}
