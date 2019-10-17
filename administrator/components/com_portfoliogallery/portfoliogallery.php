<?php
/**
 * @package Portfolio Gallery
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 * */
defined('_JEXEC') or die;

JError::$legacy = false;
$document = JFactory::getDocument();
$document->addStyleDeclaration('.pagetitle icon-48-general {background-image: url(../media/com_portfoliogallery/images/hugeIt.png);}');
jimport('joomla.application.component.controller');
?>
<?php
$controller = JControllerLegacy::getInstance('Portfoliogallery');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();


