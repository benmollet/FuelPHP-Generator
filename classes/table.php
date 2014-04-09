<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Table
{
    public $tableName;
	public $tableDisplayName;
	public $models;
	public $modelName;
	public $modelCount;
    public $rowContent;
	public $tableHeaders;
	public $page;
	public $paginationType;
	public $urlSuffix;
    
    public function __construct($models, $properties)
    {
		if (key_exists('tableName', $properties) === true)
		{
			$this->tableName = $properties['tableName'];
		}
		
		if (key_exists('tableDisplayName', $properties) === true)
		{
			$this->tableDisplayName = $properties['tableDisplayName'];
		}
		
		if (key_exists('tableHeaders', $properties) === true)
		{
			$this->tableHeaders = $properties['tableHeaders'];
		}
		
		if (key_exists('tableClass', $properties) === true)
		{
			$this->tableClass = $properties['tableClass'];
		}
		else
		{
			$this->tableClass = 'table';
		}
		
		if (key_exists('tableClass', $properties) === true)
		{
			$this->tableClass = $properties['tableClass'];
		}
		
		if (key_exists('inPanel', $properties) === true)
		{
			$this->inPanel = $properties['inPanel'];
		}
		else
		{
			$this->inPanel = false;
		}
		
		if (key_exists('page', $properties) === true and $properties['page'] !== null)
		{
			$this->page = $properties['page'];
		}
		else if (\Input::get($this->tableName . '-page') !== null)
		{
			$this->page = \Input::get($this->tableName . '-page');
			$this->paginationType = 'get';
		}
		else 
		{
			$this->page = 1;
			$this->paginationType = 'get';
		}
		
		if (key_exists('paginationUrl', $properties) === true)
		{
			$this->paginationUrl = $properties['paginationUrl'];
		}
		else
		{
			$this->paginationUrl = null;
		}
		
		if (key_exists('rowsLimit', $properties) === true)
		{
			$this->rowsLimit = $properties['rowsLimit'];
		}
		else
		{
			//Default of 10
			$this->rowsLimit = 10;
		}
		
		if (key_exists('determinePagination', $properties) === true)
		{
			$this->determinePagination = $properties['determinePagination'];
		}
		else
		{
			//Default of 10
			$this->determinePagination = false;
		}
		
		if (is_array($models) === false)
		{
			$modelName = $models;
			$this->modelCount = $modelName::count();
			
			$this->models = $modelName::query()
					->rows_offset(($this->rowsLimit * $this->page) - $this->rowsLimit)
					->rows_limit($this->rowsLimit)
					->get();
			$this->pageCount = ceil($this->modelCount / $this->rowsLimit);
			$this->pageStart = ($this->rowsLimit * $this->page) - $this->rowsLimit;
			$this->pageEnd = $this->rowsLimit * $this->page;
		}
		else
		{
			$this->models = $models;
			$this->modelCount = count($models);
			$this->pageCount = ceil($this->modelCount / $this->rowsLimit);
			$this->pageStart = ($this->rowsLimit * $this->page) - $this->rowsLimit;
			$this->pageEnd = $this->rowsLimit * $this->page;
			
			if (count($models) > $this->rowsLimit)
			{
				$this->determinePagination = true;
			}
		}
		
		if (\Input::method() === 'POST' and \Input::post('urlSuffix') !== null)
		{
			$this->urlSuffix = \Input::post('urlSuffix');
		}
		else
		{
			$this->urlSuffix = '';
		}
		
        $this->rowContent = array();
        return;
    }
    
    public function generate()
    {
        $data = new stdClass;
        $data = $this;
        
        return View::forge('table', $data);
    }
    
    public function addCell($cellContents)
    {
        if ($cellContents === null)
        {
            throw Excelption('Cell contents cannot be empty');
            return;
        }
        
        array_push($this->rowContent, $cellContents);
    }
    
    public function generateFromModel()
    {
        return $this->generate();
    }
}