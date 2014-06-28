<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class HtmlList
{
	public $attribute;
	public $elements;
	public $reorder;
	
	public function __construct($elements, $attribute, $properties)
	{
		if ($properties !== null)
		{
			if (key_exists('reorder', $properties) === true)
			{
				$this->reorder = $properties['reorder'];
			}
		}
		
		$this->elements = $elements;
		$this->attribute = $attribute;
	}
	
	public function checkPost()
	{
		if (Input::method() === 'POST')
		{
			if ($this->reorder === true and \Input::post('order') !== '')
			{
				$order = json_decode(\Input::post('order'));
				foreach ($order as $orderIndex => $orderItem)
				{
					$this->elements[$orderIndex]->order = $orderItem;
					$this->elements[$orderIndex]->save();
				}
			}
		}
	}
	
	public function generate()
	{
		$data = $this;
		
		return \View::forge('_list', $this);
	}
}