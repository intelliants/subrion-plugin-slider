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

$iaSlider = $iaCore->factoryModule('slider', 'slider', iaCore::ADMIN);

$iaDb->setTable(iaSlider::getTable());

if (iaView::REQUEST_JSON == $iaView->getRequestType()) {
    switch ($pageAction) {
        case iaCore::ACTION_READ:

            $params = [];
            if (isset($_GET['text']) && $_GET['text']) {
                $stmt = '(`title` LIKE :text OR `body` LIKE :text)';
                $iaDb->bind($stmt, ['text' => '%' . $_GET['text'] . '%']);

                $params[] = $stmt;
            }

            $output = $iaSlider->gridRead($_GET,
                ['image', 'order', 'status'],
                ['status' => 'equal', 'title' =>'like'],
                $params
            );

            foreach ($output['data'] as & $item) {
                $name = iaLanguage::get('slider_name_' . $item['id']);
                $item['name'] = isset($name) && $name ? $name : iaLanguage::get('empty');
            }

            break;

        case iaCore::ACTION_EDIT:
            if (isset($_POST['name'])) {
                $output['result'] = iaLanguage::addPhrase('slider_name_' . $_POST['id'][0], $_POST['name'],
                    $iaView->language, 'slider');
                $output['message'] = iaLanguage::get('saved');
                unset($_POST['name']);
            } else {
                $output = $iaSlider->gridUpdate($_POST);
            }

            break;

        case iaCore::ACTION_DELETE:
            foreach ($_POST['id'] as $id) {
                iaLanguage::delete('slider_name_' . $id);
                iaLanguage::delete('slider_body_' . $id);
            }
            $output = $iaSlider->gridDelete($_POST);
    }

    $iaView->assign($output);
}

if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    if (iaCore::ACTION_EDIT == $pageAction || iaCore::ACTION_ADD == $pageAction) {
        $id = 0;
        $slides = ['status' => iaCore::STATUS_ACTIVE];

        iaBreadcrumb::replaceEnd(iaLanguage::get($pageAction . '_slide'), IA_ADMIN_URL . 'slider/' . $pageAction);

        $iaDb->setTable('blocks');
        $sql = "SELECT bl.*, l.`value` as title, COUNT(bn.`id`) as bn_col, opt.`slider_width`, opt.`slider_height` " .
            "FROM `{$iaDb->prefix}blocks` as bl " .
            "LEFT JOIN `{$iaDb->prefix}slider_block_options` as opt " .
            "ON bl.`id` = opt.`block_id` " .
            "LEFT JOIN `{$iaDb->prefix}slider` as bn " .
            "ON bn.`position` = bl.`id` " .
            "LEFT JOIN `{$iaDb->prefix}language` l " .
            "ON (l.`key` = CONCAT('block_title_', bl.`id`)) " .
            "WHERE bl.`module` = 'slider' " .
            "GROUP BY bl.`id`";

        $positions = $iaDb->getAll($sql);
        $iaDb->resetTable();

        if (!is_array($positions) || empty($positions)) {
            $no_positions = iaLanguage::getf('please_create_slider_block',
                ['configurl' => IA_ADMIN_URL . 'slider/blocks/']);
            $iaView->setMessages($no_positions, 'info');
        }

        if (iaCore::ACTION_EDIT == $pageAction) {
            $id = isset($iaCore->requestPath[0]) ? (int)$iaCore->requestPath[0] : 0;
            $slides = $iaDb->row(iaDb::ALL_COLUMNS_SELECTION, iaDb::convertIds($id));
            $iaDb->setTable(iaLanguage::getTable());
            $slides['names'] = $iaDb->keyvalue(['code', 'value'],
                iaDb::convertIds('slider_name_' . $id, 'key') . " AND `module` = 'slider'");
            $slides['bodies'] = $iaDb->keyvalue(['code', 'value'],
                iaDb::convertIds('slider_body_' . $id, 'key') . " AND `module` = 'slider'");
            $iaDb->resetTable();

            if (!$slides) {
                return iaView::errorPage(iaView::ERROR_NOT_FOUND);
            }
        } elseif (empty($_POST) && !isset($no_positions)) {
            $iaView->setMessages(iaLanguage::getf('add_slide_notification',
                ['slider_config_url' => IA_ADMIN_URL . 'configuration' . IA_URL_DELIMITER . 'slider' . IA_URL_DELIMITER]),
                'info');
        }


        $slides = [
            'id' => isset($id) ? $id : 0,
            'names' => iaUtil::checkPostParam('names', $slides),
            'url' => iaUtil::checkPostParam('url', $slides),
            'image' => iaUtil::checkPostParam('image', $slides),
            'position' => iaUtil::checkPostParam('position', $slides),
            'bodies' => iaUtil::checkPostParam('bodies', $slides),
            'order' => iaUtil::checkPostParam('order', $slides),
            'status' => iaUtil::checkPostParam('status', $slides)
        ];

        if (isset($_POST['save'])) {
            $iaUtil = iaCore::util();
            iaUtil::loadUTF8Functions('ascii', 'validation', 'bad', 'utf8_to_ascii');

            $error = false;
            $messages = [];

            $slides['status'] = in_array($slides['status'],
                [iaCore::STATUS_ACTIVE, iaCore::STATUS_APPROVAL]) ? $slides['status'] : iaCore::STATUS_APPROVAL;
            $slides['url'] = !empty($slides['url']) && '/' != substr($slides['url'], -1) ? $slides['url'] . '/' : $slides['url'];

            foreach ($slides['bodies'] as & $body) {
                $body = iaUtil::safeHTML($body);
            }

            foreach ($slides['names'] as $code => & $name) {
                $name = iaSanitize::html($name);
                if (empty($name)) {
                    $error = true;
                    $messages[] = iaLanguage::get('name_is_empty_for') . $iaCore->languages[$code]['title'];
                    break;
                }
            }
            $slides['url'] = iaSanitize::html($slides['url']);

            $names = $slides['names'];
            $bodies = $slides['bodies'];
            unset($slides['names'], $slides['bodies']);

            if (iaCore::ACTION_EDIT == $pageAction && !$error) {
                $slides['id'] = $id;
                $result = $iaSlider->update($slides, $slides['id']);
                $error = (0 === $result || $result) ? false : true;
                $messages[] = ($error) ? iaLanguage::get('db_error') : iaLanguage::get('saved');
            } elseif (!$error) {
                $result = $id = $iaSlider->insert($slides);
                $error = ($result) ? false : true;
                $messages[] = ($error) ? iaLanguage::get('db_error') : iaLanguage::get('slide_added');
            }

            $slides['names'] = $names;
            $slides['bodies'] = $bodies;

            if (!$error) {
                $fieldname = 'name';
                foreach (['names', 'bodies'] as $array) {
                    foreach ($$array as $code => $field) {
                        $key = 'slider_' . $fieldname . '_' . $id;
                        iaLanguage::addPhrase($key, $field, $code, 'slider');
                    }
                    $fieldname = 'body';
                }

                $iaView->setMessages($messages, 'success');

                iaUtil::go_to(IA_ADMIN_URL . 'slider/');
            }

            $iaView->setMessages($messages);
        }

        $iaView->assign('positions', $positions);
        $iaView->assign('slides', $slides);

        $iaView->display();
    } else {
        $iaView->grid('_IA_URL_modules/slider/js/admin/grid');
    }
}

$iaDb->resetTable();
