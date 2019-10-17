<?php
/**
* @version $Id: default_sitemap.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2014 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

header('Cache-Control: no-cache, must-revalidate');
header('Content-type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?'.">\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php
	$nb = count($this->result);

	$uri = JURI::getInstance();
	$httphost = $uri->toString(array('scheme', 'host', 'port'));

	for ($i = 0; $i < $nb ; ++$i) {
		if ($this->result[$i]['blockview'] == 1) { // Public pages only
			echo "\t<url>\n";

			if ($this->link_folder_print)
				$aff_url = JURI::base(true).'/'.$this->result[$i]['foldername'].'/'.$this->result[$i]['pagename'].'/';
			else
				$aff_url = JRoute::_('index.php?option=com_myjspace&view=see&pagename='.$this->result[$i]['pagename']);

			echo "\t\t<loc>".$httphost.$aff_url."</loc>\n";

			echo "\t\t<changefreq>".$this->sitemap_freq."</changefreq>\n";

			if ($this->result[$i]['last_update_date']) {
				echo "\t\t<lastmod>".date('c', strtotime($this->result[$i]['last_update_date']))."</lastmod>\n";
			}

			echo "\t</url>\n";
		}
	}
 ?>
</urlset>
