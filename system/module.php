<?php

class Module extends Mvc
{
	public function __construct()
	{
		$this->doAutoload() ;
		$this->loadFields() ;
	}
	function findFile($file, $subPath)
	{
		$filePath = $this->baseDir . '/' . $subPath . '/' . basename($file, '.php') . '.php' ;

		if( file_exists($filePath) )
		{
			return $filePath ;
		}
		return parent::findFile($file, $subPath);
	}
	function actionDispatch($action )
	{
	}
}
?>