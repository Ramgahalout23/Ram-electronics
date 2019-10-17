<?php 
/**
 * @package Portfolio Gallery
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website     http://www.huge-it.com/
 **/
?>
<?php defined('_JEXEC') or die('Restricted access'); 
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
jimport('joomla.application.component.helper');
//$doc = JFactory::getDocument();
//$doc->addScript("http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js");
?>
<?php

require_once(dirname(__file__).DS.'helper.php');
//$mod_id = modSliderHelper::getModule('mod_slider');


require_once JPATH_SITE.'/components/com_portfoliogallery/helpers/helper.php';

$id = $module -> id;


$portfolio_class = new PortfoliogallerysHelper();

$portfolio_class->portfolio_id = $params->get('choose_portfoliogallery');
$class_suffix = $params->get('class_suffix',$id);
$portfolio_class->type = 'module';
$portfolio_class->class_suffix = $class_suffix;
$portfolio_class->module_id = $id;
echo $portfolio_class->render_html();
?>



