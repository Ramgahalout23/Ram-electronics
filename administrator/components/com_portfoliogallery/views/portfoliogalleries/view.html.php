<?php
/**
 * @package Portfolio Gallery
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 * */
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class PortfoliogalleryViewPortfoliogalleries extends JViewLegacy
{
	
	protected $items;
	protected $pagination;
        protected $portfolio;
        protected $other;
       

	public function display($tpl = null)
	{
		try
		{
			
            $this->items = $this->get('Items');
            $this ->portfolio = $this->get('Portfolio');
            $this->other=$this->get('Other');
            $this->pagination = $this->get('Pagination');
            $this->addToolBar();


                       	parent::display($tpl);
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}

		protected function addToolBar()
	{
		//JToolBarHelper::title(JText::_('COM_PORTFOPLIO_MANAGER_PORTFOLIOGALLERYSS'), 'portfolio');
                JToolBarHelper::title(JText::_('COM_PORTFOPLIO_MANAGER_PORTFOLIOGALLERYSS').' <div style="float:left;"><img style = "position: relative; left: 17px; top: -5px;" src="'.JURI::root().'media/com_portfoliogallery/images/hugeIt.png" /></div>', 'general');
             	JToolBarHelper::addNew('portfoliogalleries.add');
                JToolBarHelper::divider();
		JToolBarHelper::editList('portfoliogallery.edit');
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('', 'portfoliogalleries.delete');
	}
}
