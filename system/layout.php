<?php

class Layout
{

	/**
	 * Variables to be passed to view and layouts.
	 * 
	 * @var array
	 */
	public $vars = array();

	/**
	 * Theme file
	 * 
	 * @var string
	 */
	public $theme = 'tti/index.php';

	/**
	 * Change theme
	 * 
	 * @param string $theme theme file
	 */
	function setLayout($theme)
	{
		$this->theme = $theme;
	}

	/**
	 * Load view and render design.
	 * 
	 * @global array $QFC
	 * @param string $file
	 * @param array $vars view variables.
	 * @param bool $return
	 * @return bool
	 */
	function loadView($file, $vars = array(), $lvars = array(), $return = false)
	{
		global $QFC;
		$vars['QF'] = $QFC;
		$this->vars['QF'] = $QFC;

		$varset1 = $this->vars;
		if (!is_array($varset1))
		{
			$varset1 = array();
		}

		$varset2 = $lvars;
		if (!is_array($varset2))
		{
			$varset2 = array();
		}

		$varAll = array_merge($varset1, $varset2);

		$contents = $QFC->loadView($file, $vars, true);
		return $this->loadData($contents, $varAll);
	}

	/**
	 * Render given data instead of view
	 * 
	 * @param string $data data to render
	 * @param array $vars layout variables
	 * @param bool $return 
	 * @return boolean 
	 */
	function loadData($data, $vars = array(), $return = false)
	{
		global $QFC;
		$vars['QF'] = $QFC;
		$this->vars['QF'] = $QFC;

		$varset1 = $this->vars;
		if (!is_array($varset1))
		{
			$varset1 = array();
		}
		$varset2 = $QFC->vars;
		if (!is_array($varset2))
		{
			$varset2 = array();
		}

		$var12 = array_merge($varset1, $varset2);
		$varAll = array_merge($var12, $vars);

		$contents = $data;
		$theme = QFS_APP . 'layouts/' . $this->theme;
		if (file_exists($theme))
		{
			//extra layout & view variables..
			if (is_array($varAll))
			{
				extract($varAll);
			}
			//render theme
			if ($return)
			{
				ob_start();
				include $theme;
				return ob_get_clean();
			}
			return include ( $theme );
		}
		return false;
	}

}

?>