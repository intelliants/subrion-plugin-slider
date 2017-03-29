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
class iaSlider extends abstractModuleAdmin
{
    protected static $_table = 'slider';
    protected static $_tableBlocks = 'slider_block_options';


    public static function getTableBlocks()
    {
        return self::$_tableBlocks;
    }

    public function getConfigOptions($name)
    {
        $options = $this->iaDb->one('multiple_values', "`name` = '{$name}' AND `module` = 'slider'",
            iaCore::getConfigTable());

        return explode(',', $options);
    }

    public function gridRead($params, $columns, array $filterParams = [], array $persistentConditions = [])
    {
        $params || $params = [];
        $start = isset($params['start']) ? (int)$params['start'] : 0;
        $limit = isset($params['limit']) ? (int)$params['limit'] : 15;

        $sort = $params['sort'];
        $dir = in_array($params['dir'], [iaDb::ORDER_ASC, iaDb::ORDER_DESC]) ? $params['dir'] : iaDb::ORDER_ASC;
        $order = ($sort && $dir) ? "`{$sort}` {$dir}" : 't1.`date` DESC';

        $where = $values = [];
        foreach ($filterParams as $name => $type) {
            if (isset($params[$name]) && $params[$name]) {
                $value = iaSanitize::sql($params[$name]);

                switch ($type) {
                    case 'equal':
                        $where[] = sprintf('`%s` = :%s', $name, $name);
                        $values[$name] = $value;
                        break;
                    case 'like':
                        $where[] = sprintf('`%s` LIKE :%s', $name, $name);
                        $values[$name] = '%' . $value . '%';
                }
            }
        }

        $where = array_merge($where, $persistentConditions);
        $where || $where[] = iaDb::EMPTY_CONDITION;
        $where = implode(' AND ', $where);
        $this->iaDb->bind($where, $values);

        if (is_array($columns)) {
            $columns = array_merge(['id', 'update' => 1, 'delete' => 1], $columns);
        }

        $sql =
            "SELECT SQL_CALC_FOUND_ROWS sl.*, l.`value` as title, bl.name `position_title`, bl.`position` `slider_position`, bl.`id` as `edit_block`, sl.`id` as `update`, 1 as `delete` " .
            "FROM `{$this->iaDb->prefix}slider` sl " .
            "LEFT JOIN `{$this->iaDb->prefix}blocks` bl " .
            "ON sl.`position` = bl.`id` " .
            "LEFT JOIN `{$this->iaDb->prefix}language` l " .
            "ON (l.`key` = CONCAT('block_title_', bl.`id`))" .
            "WHERE {$where} " .
            "LIMIT {$start}, {$limit}";

        return [
            'data' => $this->iaDb->getAll($sql),
            'total' => (int)$this->iaDb->one(iaDb::STMT_COUNT_ROWS, $where)
        ];
    }

    public function gridDelete($params, $languagePhraseKey = 'deleted')
    {
        foreach ($params['id'] as $sliderId) {
            $this->iaDb->setTable(self::getTable());
            $image = $this->iaDb->one("image", "id='" . $sliderId . "'");
            $this->iaDb->delete("`id` = '" . $sliderId . "'");
            $this->iaDb->resetTable();

            $iaField = $this->iaCore->factory('field');

            list($path, $file) = explode('|', $image);

            if (is_file(IA_UPLOADS . $path . $iaField::UPLOAD_FOLDER_ORIGINAL . IA_URL_DELIMITER . $file)) {
                unlink(IA_UPLOADS . $path . $iaField::UPLOAD_FOLDER_ORIGINAL . IA_URL_DELIMITER . $file);
            }

            if (is_file(IA_UPLOADS . $path . $iaField::IMAGE_TYPE_LARGE . IA_URL_DELIMITER . $file)) {
                unlink(IA_UPLOADS . $path . $iaField::IMAGE_TYPE_LARGE . IA_URL_DELIMITER . $file);
            }

            if (is_file(IA_UPLOADS . $path . $iaField::IMAGE_TYPE_THUMBNAIL . IA_URL_DELIMITER . $file)) {
                unlink(IA_UPLOADS . $path . $iaField::IMAGE_TYPE_THUMBNAIL . IA_URL_DELIMITER . $file);
            }
        }

        $result['result'] = true;
        $result['message'] = iaLanguage::get('deleted');

        return $result;
    }

    private function _updateImage(&$itemData)
    {
        if (isset($_FILES['image']['error']) && !$_FILES['image']['error']) {
            $iaField = $this->iaCore->factory('field');
            $iaPicture = $this->iaCore->factory('picture');

            $field = [
                'type' => $iaField::IMAGE,
                'thumb_width' => $this->iaCore->get('slider_thumb_w'),
                'thumb_height' => $this->iaCore->get('slider_thumb_h'),
                'image_width' => $this->iaCore->get('slider_height'),
                'image_height' => $this->iaCore->get('slider_height'),
                'resize_mode' => $iaPicture::FIT,
                'folder_name' => 'slides',
                'file_prefix' => 'slide_'
            ];

            empty($itemData['image']) || $iaField->deleteUploadedFile('image', self::getTable(), $itemData['id'],
                $itemData['image']);

            $imageEntry = $iaField->processUploadedFile($_FILES['image']['tmp_name'], $field,
                $_FILES['image']['name']);

            $itemData['image'] = $imageEntry['path'] . '|' . $imageEntry['file'];
        }
    }

    public function insert(array $itemData)
    {
        $this->_updateImage($itemData);

        return $this->iaDb->insert($itemData, null, self::getTable());
    }

    public function update(array $itemData, $where = '')
    {

        $this->_updateImage($itemData);

        return $this->iaDb->update($itemData, $where, null, self::getTable());
    }
}