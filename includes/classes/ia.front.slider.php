<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2017 Intelliants, LLC <https://intelliants.com>
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
 * @link https://subrion.org/
 *
 ******************************************************************************/

class iaSlider extends abstractModuleFront
{
    protected static $_table = 'slider';
    protected static $_tableBlocks = 'slider_block_options';


    public static function getTableBlocks()
    {
        return self::$_tableBlocks;
    }

    public function getSliders()
    {
        if ($rows = $this->iaDb->all(iaDb::ALL_COLUMNS_SELECTION, "`status` = 'active'", null, null, self::getTable())) {
            $sliders = [];
            foreach ($rows as $entry) {
                $entry['name'] = iaLanguage::get('slider_name_' . $entry['id']);
                $entry['body'] = iaLanguage::get('slider_body_' . $entry['id']);
                $sliders[$entry['position']][] = $entry;
            }

            return $sliders;
        }

        return false;
    }

    public function getPositions()
    {
        if ($rows = $this->iaDb->all(iaDb::ALL_COLUMNS_SELECTION, iaDb::EMPTY_CONDITION, null, null, self::getTableBlocks())) {
            $positions = [];
            foreach ($rows as $entry) {
                $positions[$entry['block_id']] = $entry;
            }

            return $positions;
        }

        return false;
    }
}
