<?php
/**
 * @package Huge-IT Portfolio Gallery
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 **/

defined('_JEXEC') or die;
JLog::addLogger(
	array('text_file' => 'com_portfoliogallery.php'),
	JLog::ALL,
	array('com_portfoliogallery')
);
JError::$legacy = false;
jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('Portfoliogallery');
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task', 'display'));
$controller->redirect();
