<?php
/**
 * @package Huge-IT Portfolio Gallery
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 * */

defined('_JEXEC') or die('Restircted access');

require_once JPATH_SITE.'/components/com_portfoliogallery/helpers/helper.php';

$id_15 = JRequest::getVar('portfoliogallery',   $this -> portId , '', 'int');



$cis_class = new PortfoliogallerysHelper;
$cis_class->portfolio_id = intval($id_15);


$cis_class->type = 'component';
$cis_class->class_suffix = '';
$cis_class->module_id =  $this -> portId ;
echo $cis_class->render_html();
