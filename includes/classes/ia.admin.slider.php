<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2016 Intelliants, LLC <http://www.intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link http://www.subrion.org/
 *
 ******************************************************************************/

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

	public function delete($id)
	{
		$image = $this->iaDb->one('image', iaDb::convertIds($id));
		$iaPicture = $this->iaCore->factory('picture');
		$iaPicture->delete($image);

		return parent::delete($id);
	}
}