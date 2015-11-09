<?php
//##copyright##

$iaSlider = $iaCore->factoryPlugin('slider', iaCore::ADMIN, 'slider');

$iaDb->setTable(iaSlider::getTable());

if (iaView::REQUEST_JSON == $iaView->getRequestType())
{
	switch ($pageAction)
	{
		case iaCore::ACTION_READ:

			$params = array();
			if (isset($_GET['text']) && $_GET['text'])
			{
				$stmt = '(`title` LIKE :text OR `body` LIKE :text)';
				$iaDb->bind($stmt, array('text' => '%' . $_GET['text'] . '%'));

				$params[] = $stmt;
			}

			$output = $iaSlider->gridRead($_GET,
				array('name', 'body', 'image', 'order', 'status'),
				array('status' => 'equal'),
				$params
			);

			break;

		case iaCore::ACTION_EDIT:
			$output = $iaSlider->gridUpdate($_POST);

			break;

		case iaCore::ACTION_DELETE:
			$output = $iaSlider->gridDelete($_POST);
	}

	$iaView->assign($output);
}

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	if (iaCore::ACTION_EDIT == $pageAction || iaCore::ACTION_ADD == $pageAction)
	{
		$id = 0;
		$slides = array(
			'lang' => $iaView->language,
			'status' => iaCore::STATUS_ACTIVE,
		);

		iaBreadcrumb::replaceEnd(iaLanguage::get($pageAction . '_slide'), IA_ADMIN_URL . 'slider/' . $pageAction);

		$iaDb->setTable('blocks');
		$sql = "
			SELECT bl.*, COUNT(bn.`id`) as bn_col, opt.`slider_width`, opt.`slider_height`
			  FROM `{$iaDb->prefix}blocks` as bl
			LEFT JOIN `{$iaDb->prefix}slider_block_options` as opt
			  ON bl.`id` = opt.`block_id`
			LEFT JOIN `{$iaDb->prefix}slider` as bn
			  ON bn.`position` = bl.`id`
			WHERE bl.`extras` = 'slider'
			GROUP BY bl.`id`
		";
		$positions = $iaDb->getAll($sql);
		$iaDb->resetTable();

		if (!is_array($positions) || empty($positions))
		{
			$no_positions = iaLanguage::getf('please_create_slider_block', array('configurl' => IA_ADMIN_URL . 'slider/blocks/'));
			$iaView->setMessages($no_positions, 'info');
		}

		if ('edit' == $pageAction)
		{
			$id = isset($iaCore->requestPath[0]) ? (int)$iaCore->requestPath[0] : 0;
			$slides = $iaDb->row(iaDb::ALL_COLUMNS_SELECTION, iaDb::convertIds($id));
			if (!$slides)
			{
				return iaView::errorPage(iaView::ERROR_NOT_FOUND);
			}
		}
		elseif(empty($_POST) && !isset($no_positions))
		{
			$iaView->setMessages(iaLanguage::getf('add_slide_notification', array('slider_config_url' => IA_ADMIN_URL . 'configuration' . IA_URL_DELIMITER . 'slider' . IA_URL_DELIMITER)), 'info');
		}


		$slides = array(
			'id' => isset($id) ? $id : 0,
			'name' => iaUtil::checkPostParam('name', $slides),
			'lang' => iaUtil::checkPostParam('lang', $slides),
			'url' => iaUtil::checkPostParam('url', $slides),
			'image' => iaUtil::checkPostParam('image', $slides),
			'position' => iaUtil::checkPostParam('position', $slides),
			'body' => iaUtil::checkPostParam('body', $slides),
			'order' => iaUtil::checkPostParam('order', $slides),
			'status' => iaUtil::checkPostParam('status', $slides)
		);

		if (isset($_POST['save']))
		{
			$iaUtil = iaCore::util();
			iaUtil::loadUTF8Functions('ascii', 'validation', 'bad', 'utf8_to_ascii');

			$error = false;
			$messages = array();

			$slides['status'] = in_array($slides['status'], array(iaCore::STATUS_ACTIVE, iaCore::STATUS_APPROVAL)) ? $slides['status'] : iaCore::STATUS_APPROVAL;
			$slides['lang'] = array_key_exists($slides['lang'], $iaCore->languages) ? $slides['lang'] : $iaView->language;
			$slides['url'] = !empty($slides['url']) && 'http://' != substr($slides['url'], 0, 7) ? 'http://' . $slides['url'] : $slides['url'];
			$slides['body'] = iaUtil::safeHTML($slides['body']);
			$slides['name'] = iaSanitize::html($slides['name']);
			$slides['url'] = iaSanitize::html($slides['url']);
			$slides['order'] = iaSanitize::html($slides['order']);

			if (empty($slides['name']))
			{
				$error = true;
				$messages[] = iaLanguage::get('incorrect_fullname');
			}

			if (!empty($slides['url']) && !iaValidate::isUrl($slides['url']))
			{
				$error = true;
				$messages[] = iaLanguage::get('error_url');
			}

			$img = isset($_FILES['image']) && !empty($_FILES['image']['name']) ? $_FILES['image'] : false;

			if (!$error)
			{
				if ($img)
				{
					$image = 'slides/';
					$tok = iaUtil::generateToken();
					$iaPicture = $iaCore->factory('picture');

					$side = ($iaCore->get('slider_thumb_w') > $iaCore->get('slider_thumb_h'))
						? $iaCore->get('slider_thumb_w')
						: $iaCore->get('slider_thumb_h');

					$imageInfo = array(
						'image_width' => $side,
						'image_height' => $side,
						'thumb_width' => 200,
						'thumb_height' => 150,
						'resize_mode' => 'fit'
					);

					$imgSize = getimagesize($img['tmp_name']);

					$slides['image'] = $iaPicture->processImage($img, $image, $tok, $imageInfo);
				}

				if (iaCore::ACTION_EDIT == $pageAction)
				{
					$slides['id'] = $id;
					$iaDb->update($slides);
					$messages[] = iaLanguage::get('saved');
				}
				else
				{
					$id = $iaDb->insert($slides);
					$messages[] = iaLanguage::get('slide_added');
				}

				$iaView->setMessages($messages, 'success');

				iaUtil::go_to(IA_ADMIN_URL . 'slider/');
			}

			$iaView->setMessages($messages);
		}

		$iaView->assign('positions', $positions);
		$iaView->assign('slides', $slides);

		$iaView->display();
	}
	else
	{
		$iaView->grid('_IA_URL_plugins/slider/js/admin/grid');
	}
}

$iaDb->resetTable();