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

$allowedAction = ['add_block', 'remove_block', 'save_block'];
$configAction = isset($_GET['action']) && in_array($_GET['action'], $allowedAction) ? $_GET['action'] : '';

if (iaView::REQUEST_JSON == $iaView->getRequestType()) {
    $out = ['message' => '', 'error' => true];
    $id = isset($_GET['id']) && !empty($_GET['id']) ? intval($_GET['id']) : 0;

    if ('remove_block' == $configAction && $id) {
        $iaBlock = $iaCore->factory('block', iaCore::ADMIN);

        if (!$iaBlock->delete($id)) {
            $out['error'] = true;
            $out['message'] = iaLanguage::get('block_is_not_removed');
        } else {
            $iaDb->setTable(iaSlider::getTableBlocks());
            $iaDb->delete("`block_id` = '$id'");
            $iaDb->resetTable();

            $out['error'] = false;
        }
    }

    if ('save_block' == $configAction && $id) {
        $fields = [
            'items_per_slide' => isset($_GET['items_per_slide']) ? intval($_GET['items_per_slide']) : $iaCore->get('items_per_slide'),
            'slider_width' => isset($_GET['slider_width']) ? $_GET['slider_width'] : $iaCore->get('slider_width'),
            'slider_height' => isset($_GET['slider_height']) ? $_GET['slider_height'] : $iaCore->get('slider_height'),
            'slider_thumb_w' => isset($_GET['slider_thumb_w']) ? $_GET['slider_thumb_w'] : $iaCore->get('slider_thumb_w'),
            'slider_thumb_h' => isset($_GET['slider_thumb_h']) ? $_GET['slider_thumb_h'] : $iaCore->get('slider_thumb_h'),
            'slider_direction' => isset($_GET['slider_direction']) ? $_GET['slider_direction'] : $iaCore->get('slider_direction'),
            'slider_fx' => isset($_GET['slider_fx']) ? $_GET['slider_fx'] : $iaCore->get('slider_fx'),
            'slider_easing' => isset($_GET['slider_easing']) ? $_GET['slider_easing'] : $iaCore->get('slider_easing'),
            'slider_scroll_duration' => isset($_GET['slider_scroll_duration']) ? $_GET['slider_scroll_duration'] : $iaCore->get('slider_scroll_duration'),
            'slider_autoplay_timeout' => isset($_GET['slider_autoplay_timeout']) ? $_GET['slider_autoplay_timeout'] : $iaCore->get('slider_autoplay_timeout'),
            'slider_margin' => isset($_GET['slider_margin']) ? $_GET['slider_margin'] : $iaCore->get('slider_margin'),
            'slider_autoplay' => isset($_GET['slider_autoplay']) ? $_GET['slider_autoplay'] : $iaCore->get('slider_autoplay'),
            'slider_loop' => isset($_GET['slider_loop']) ? $_GET['slider_loop'] : $iaCore->get('slider_loop'),
            'slider_direction_nav' => isset($_GET['slider_direction_nav']) ? $_GET['slider_direction_nav'] : $iaCore->get('slider_direction_nav'),
            'slider_pagination_nav' => isset($_GET['slider_pagination_nav']) ? $_GET['slider_pagination_nav'] : $iaCore->get('slider_pagination_nav'),
            'slider_caption' => isset($_GET['slider_caption']) ? $_GET['slider_caption'] : $iaCore->get('slider_caption'),
            'slider_caption_hover' => isset($_GET['slider_caption_hover']) ? $_GET['slider_caption_hover'] : $iaCore->get('slider_caption_hover'),
            'slider_custom_url' => isset($_GET['slider_custom_url']) ? $_GET['slider_custom_url'] : $iaCore->get('slider_custom_url'),
            'block_id' => $id
        ];
        $iaDb->setTable(iaSlider::getTableBlocks());
        $iaDb->update($fields, "`block_id` = '$id'");
        $iaDb->resetTable();

        $out['error'] = false;
        $out['msg'] = iaLanguage::get('saved');
    }

    if (empty($out['data'])) {
        $out['data'] = '';
    }

    $iaView->assign($out);
}

if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    $position = isset($_GET['pos']) && !empty($_GET['pos']) ? ($_GET['pos']) : false;
    $title = isset($_GET['title']) && !empty($_GET['title']) ? ($_GET['title']) : '';
    $num = isset($_GET['num']) && !empty($_GET['num']) ? intval($_GET['num']) + 1 : 1;

    $configOptions = [];
    $configOptions['slider_direction'] = $iaSlider->getConfigOptions('slider_direction');
    $configOptions['slider_fx'] = $iaSlider->getConfigOptions('slider_fx');
    $configOptions['slider_easing'] = $iaSlider->getConfigOptions('slider_easing');
    $configOptions['slider_scroll_duration'] = $iaSlider->getConfigOptions('slider_scroll_duration');
    $configOptions['slider_autoplay_timeout'] = $iaSlider->getConfigOptions('slider_autoplay_timeout');
    $configOptions['slider_autoplay'] = $iaSlider->getConfigOptions('slider_autoplay');
    $configOptions['slider_loop'] = $iaSlider->getConfigOptions('slider_loop');
    $configOptions['slider_margin'] = $iaSlider->getConfigOptions('slider_margin');

    if ('add_block' == $configAction && $position) {
        $iaBlock = $iaCore->factory('block', iaCore::ADMIN);

        $block = [
            'name' => 'slider_block_' . $position . '_' . $num,
            'position' => $position,
            'type' => iaBlock::TYPE_SMARTY,
            'status' => iaCore::STATUS_ACTIVE,
            'header' => 1,
            'collapsible' => 0,
            'sticky' => 1,
            'title' => $title,
            'external' => 1,
            'filename' => 'extra:slider/block.slider',
            'module' => 'slider'
        ];

        $id = $iaBlock->insert($block);

        $fields = [
            'items_per_slide' => $iaCore->get('items_per_slide'),
            'slider_width' => $iaCore->get('slider_width'),
            'slider_height' => $iaCore->get('slider_height'),
            'slider_thumb_w' => $iaCore->get('slider_thumb_w'),
            'slider_thumb_h' => $iaCore->get('slider_thumb_h'),
            'slider_direction' => $iaCore->get('slider_direction'),
            'slider_fx' => $iaCore->get('slider_fx'),
            'slider_easing' => $iaCore->get('slider_easing'),
            'slider_scroll_duration' => $iaCore->get('slider_scroll_duration'),
            'slider_autoplay_timeout' => $iaCore->get('slider_autoplay_timeout'),
            'slider_margin' => $iaCore->get('slider_margin'),
            'slider_autoplay' => $iaCore->get('slider_autoplay'),
            'slider_loop' => $iaCore->get('slider_loop'),
            'slider_direction_nav' => $iaCore->get('slider_direction_nav'),
            'slider_pagination_nav' => $iaCore->get('slider_pagination_nav'),
            'slider_caption' => $iaCore->get('slider_caption'),
            'slider_caption_hover' => $iaCore->get('slider_caption_hover'),
            'slider_custom_url' => $iaCore->get('slider_custom_url'),
            'block_id' => $id
        ];

        $iaDb->setTable(iaSlider::getTableBlocks());
        $iaDb->insert($fields);
        $iaDb->resetTable();

        iaCore::util();
        iaUtil::go_to(IA_ADMIN_URL . IA_CURRENT_MODULE . '/blocks/#position-' . $position);
    }

    $positionsList = $iaDb->all(iaDb::ALL_COLUMNS_SELECTION, '`menu` = 0', null, null, 'positions');
    foreach ($positionsList as $position) {
        $positions[] = $position['name'];
    }

    $iaView->assign('positions', $positions);
    $blocks = [];

    $iaDb->setTable('blocks');
    foreach ($positions as $pos) {
        $sql = <<<SQL
SELECT DISTINCTROW  b.*, l.`value` AS `title` FROM `:prefix:table_blocks` b
LEFT JOIN `:prefix:table_language` l ON (l.`key` = CONCAT("block_title_", b.`id`))
WHERE b.`position` = ":postition" AND b.`module` = ":module"
SQL;
        $sql = iaDb::printf($sql, [
            'prefix' => $this->iaDb->prefix,
            'table_blocks' => 'blocks',
            'table_language' => 'language',
            'postition' => $pos,
            'module' => 'slider'
        ]);
        $b = $iaDb->getAll($sql);
        if ($b) {
            $blocks[$pos] = $b;
        }
    }
    $iaDb->resetTable();

    $iaDb->setTable(iaSlider::getTableBlocks());
    $options = $iaDb->all(iaDb::ALL_COLUMNS_SELECTION);
    $iaDb->resetTable();

    $blockOptions = [];
    foreach ($options as $option) {
        $blockOptions[$option['block_id']] = $option;
    }

    $iaView->assign('blocks_options', $blockOptions);
    $iaView->assign('slider_blocks', $blocks);
    $iaView->assign('config_options', $configOptions);

    $iaView->display('config');
}

$iaDb->resetTable();
