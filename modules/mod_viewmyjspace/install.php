<?php
/**
* @version $Id: install.php $
* @version		3.0.0 24/07/2019
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2012 - 2019 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/
 
defined('_JEXEC') or die;

class mod_viewmyjspaceInstallerScript
{
	public function __construct($installer)
	{
		$this->installer = $installer;
	}
 
	public function postflight($type, $parent)
	{
		return true;
	}
}
