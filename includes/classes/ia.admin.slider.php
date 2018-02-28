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
            iaConfig::getTable());

        return explode(',', $options);
    }

    public function gridRead($params, $columns, array $filterParams = [], array $persistentConditions = [])
    {
        $params || $params = [];
        $start = isset($params['start']) ? (int)$params['start'] : 0;
        $limit = isset($params['limit']) ? (int)$params['limit'] : 15;
        $order = '`' . $params['sort'] . '` ' . $params['dir'];
        $where = $values = [];

        foreach ($filterParams as $name => $type) {
            if (isset($params[$name]) && $params[$name]) {
                $value = iaSanitize::sql($params[$name]);

                switch ($type) {
                    case 'equal':
                        $where[] = sprintf('sl.`%s` = :%s', $name, $name);
                        $values[$name] = $value;
                        break;
                    case 'like':
                        $name = 'value';
                        $where[] = sprintf('%s LIKE :%s', 'l.'.'`'.$name.'`', $name);
                        $values[$name] = '%' . $value . '%';
                }
            }
        }

        $where = array_merge($where, $persistentConditions);
        $where || $where[] = iaDb::EMPTY_CONDITION;
        $where = implode(' AND ', $where);
        $this->iaDb->bind($where, $values);

        $sql = <<<SQL
SELECT SQL_CALC_FOUND_ROWS DISTINCTROW sl.*, l.`value` as title,
  bl.name `position_title`, bl.`position` `slider_position`, bl.`id` as `edit_block`, sl.`id` as `update`, 1 as `delete`
  FROM `:slider_table` sl 
LEFT JOIN `:prefixblocks` bl 
  ON sl.`position` = bl.`id` 
LEFT JOIN `:prefixlanguage` l
  ON (l.`key` = CONCAT('slider_name_', sl.`id`))
WHERE :where
ORDER BY :order
LIMIT :start, :limit
SQL;
        $sql = iaDb::printf($sql, [
            'prefix' => $this->iaDb->prefix,
            'slider_table' => $this->getTable(true),
            'where' => $where,
            'order' => $order,
            'start' => $start,
            'limit' => $limit
        ]);

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
                'image_width' => $this->iaCore->get('slider_width'),
                'image_height' => $this->iaCore->get('slider_height'),
                'resize_mode' => $iaPicture::CROP,
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

        return $this->iaDb->update($itemData, iaDb::convertIds($where), null, self::getTable());
    }
}
