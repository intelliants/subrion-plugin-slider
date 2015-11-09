<?php
//##copyright##

class iaSlider extends abstractPlugin
{
	protected static $_table = 'slider';
	protected static $_tableBlocks = 'slider_block_options';


	public static function getTableBlocks()
	{
		return self::$_tableBlocks;
	}

	public function getConfigOptions($name)
	{
		$options = $this->iaDb->one('multiple_values', "`name` = '{$name}' AND `extras` = 'slider'", iaCore::getConfigTable());

		return explode(',', $options);
	}
}