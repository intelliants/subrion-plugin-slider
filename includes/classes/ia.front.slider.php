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

	public function getSliders()
	{
		if ($rows = $this->iaDb->all(iaDb::ALL_COLUMNS_SELECTION, "`status` = 'active'", null, null, self::getTable()))
		{
			$sliders = array();
			foreach ($rows as $entry)
			{
				$sliders[$entry['position']][] = $entry;
			}

			return $sliders;
		}

		return false;
	}

	public function getPositions()
	{
		if ($rows = $this->iaDb->all(iaDb::ALL_COLUMNS_SELECTION, iaDb::EMPTY_CONDITION, null, null, self::getTableBlocks()))
		{
			$positions = array();
			foreach ($rows as $entry)
			{
				$positions[$entry['block_id']] = $entry;
			}

			return $positions;
		}

		return false;
	}
}