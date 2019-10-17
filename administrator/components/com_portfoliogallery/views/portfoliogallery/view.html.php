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
class PortfoliogalleryViewPortfoliogallery extends JViewLegacy
{
	protected $item;
        protected $portfolioParams;
	protected $form;
        protected  $prop;
        protected $all;

	public function display($tpl = null)
	{
		try
		{     
                    
			$this->form = $this->get('Form');
			$this->item = $this->get('Item');
                        $this->portfolioParams = $this->get('Portfolio');
                        $this->prop= $this->get('Propertie');
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
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$isNew = ($this->item->id == 0);
                JToolBarHelper::title(JText::_('COM_PORTFOLIOGALLERY_MANAGER_PORTFOLIOGALLERYS').' <div style="float:left;"><img style = "position: relative; left: 17px; top: -5px;" src="'.JURI::root().'media/com_portfoliogallery/images/hugeIt.png" /></div>', 'portfoliogallery');
		//JToolBarHelper::title($isNew ? JText::_('COM_PORTFOLIOGALLERY_MANAGER_PORTFOLIOGALLERYS') : JText::_('COM_PORTFOLIOGALLERY_MANAGER_PORTFOLIOGALLERYS'));		
		JToolBarHelper::apply('portfoliogallery.save');
		JToolBarHelper::cancel('portfoliogallery.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
              
	}
}
