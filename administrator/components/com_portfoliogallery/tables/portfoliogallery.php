<?php
/**
 * @package  Portfolio Gallery
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 * */
defined('_JEXEC') or die;

jimport('joomla.database.table');

class PortfoliogalleryTablePortfoliogallery extends JTable
{
    function __construct(&$db) 
    {
        parent::__construct('#__huge_itportfolio_portfolios', 'id', $db);
    }
}