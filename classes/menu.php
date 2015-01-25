<?php
class Menu {
	protected $config = array();
    protected $links = array();
	protected $preset = 'default';
	
    public function __construct($preset = 'default', $config = array())
    {
		$this->preset = $preset;
		
		\Config::load('generator', true);
		
		$this->config = \Arr::merge(\Config::get('generator.menu.' . $this->preset), $config);
		$this->config['outerElement'] = $this->config['menu'];
		
		// Set default view
		if (isset($this->config['view']) === false)
		{
			$this->config['view'] = 'generator/template/menu/default/_menu';
		}
    }
	
	public function addLink($href = null, $text = null, $config = array())
	{
		$newLink = new stdClass;
		$newLink = (object) \Arr::merge($this->config['linkContainer'], $config);
		
		// Set the href if set
		if ($href !== null)
		{
			$newLink->href = $href;
		}
		
		// Get the default link attributes from config
		$attributes = array();
		if (isset($this->config['link']['attributes']) === true)
		{
			$attributes = $this->config['link']['attributes'];
		}
		
		// Set the text if set
		if ($text !== null)
		{
			$newLink->text = Html::anchor($href, $text, $attributes);
		}
		
		$this->links[] = $newLink;
	}
    
    public function build()
    {
		$newList = new \GeneratorList('default', $this->config);
		$newList->addElement($this->links);
		
        return $newList->build();
    }
}
