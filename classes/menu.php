<?php
class Menu {
    public $menuArray;
    
    public $menuHtml;
    
    public function __construct($menuArray = null)
    {
        if ($menuArray !== null and is_array($menuArray) === true)
        {
            $this->menuArray = $menuArray;
            return;
        }
        
        $this->menuArray = array();
        
        return;
    }
    
    public function addLink($text = null, $location = null, $class = null)
    {
        if ($location === null)
        {
            throw new \Exception('Link location cannt be null');
            return;
        }
        
        if ($text === null)
        {
            throw new \Exception('Link text cannt be null');
        }
        
        if ($class !== null)
        {
            array_push($this->menuArray, array(
                'location' => $location,
                'text' => $text,
                'class' => $class,
            ));
        }
        else
        {
            array_push($this->menuArray, array(
                'location' => $location,
                'text' => $text,
            ));
        }
    }
    
    public function generate()
    {
        return \View::forge('menu', $this);
    }
}
