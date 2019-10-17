<?php
/**
 * @package Portfolio Gallery
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 * */
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');
class PortfoliogalleryController extends JControllerLegacy
{
	public function display($cachable = false, $urlparams = array())
	{
		
		$input = JFactory::getApplication()->input;
		$input->set('view', $input->getCmd('view', 'Portfoliogalleries'));
		parent::display($cachable);
	}
 



	
}
