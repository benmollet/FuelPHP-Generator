<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class GeneratorList
{
	protected $config;
	protected $elements;
	protected $preset;
	
	public function __construct($preset = 'default', $config = array())
	{
		$this->preset = $preset;
		
		\Config::load('generator', true);
		
		//Merge the passed config and the config from the file
		$this->config = Arr::merge(\Config::get('generator.list.' . $this->preset), $config);
		
		//Check to make sure all necessary config values are set
		if (isset($this->config['view']) === false)
		{
			$this->config['view'] = 'generator/template/generatorList/default/_generator_list';
		}
		
		if (isset($this->config['wrapperElement']['tag']) === false)
		{
			$this->config['wrapperElement']['tag'] = 'ul';
		}
		
		if (isset($this->config['wrapperElement']['attributes']) === false)
		{
			$this->config['wrapperElement']['attributes'] = array();
		}
		
		if (isset($this->config['innerElement']['tag']) === false)
		{
			$this->config['innerElement']['tag'] = 'ul';
		}
		
		if (isset($this->config['innerElement']['attributes']) === false)
		{
			$this->config['innerElement']['attributes'] = array();
		}
		
		return $this;
	}
	
	public static function forge($preset = 'default', $config = array())
	{
		$newGeneratorList = new \GeneratorList($preset, $config);
		
		return $newGeneratorList;
	}
	
	public function addElement($inputElement, $config = array())
	{
		$this->elements[] = $this->processElement($inputElement, $config);
	}
	
	public function processElement($inputElement, $config)
	{
		if (is_object($inputElement) === true)
		{
			return $inputElement;
		}
		else if (is_array($inputElement) === true)
		{
			$inputElements = $inputElement;
			$newElement = new stdClass();
			$newElement = (object) \Arr::merge($this->config['outerElement'], $config);
				
			foreach ($inputElements as $inputElement)
			{
				$newElement->children[] = $this->processElement($inputElement, $config);
			}
			
			return $newElement;
		}
		else
		{
			$newElement = new stdClass();
			$newElement = (object) \Arr::merge($this->config['innerElement'], $config);
			$newElement->text = $inputElement;
			return $newElement;
		}
	}
	
	public function build()
	{
		$data['elements'] = $this->elements;
		$data['config'] = $this->config;
		
		return $this->createHtml($this->elements);
	}
	
	public function createHtml($elements)
	{
		$html = '';
		
		foreach ($elements as $element)
		{
			$innerHtml = '';
			
			if (isset($element->children) === true)
			{
				$innerHtml = $this->createHtml($element->children);
			}
			
			if (isset($element->text) === true)
			{
				$innerHtml .= $element->text;
			}
			
			$attributes = array();
			if (isset($element->attributes) === true)
			{
				$attributes = $element->attributes;
			}
			
			$html .= html_tag($element->tag, $attributes, $innerHtml);
		}
		
		return $html;
	}
}