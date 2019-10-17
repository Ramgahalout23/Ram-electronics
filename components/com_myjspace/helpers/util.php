<?php
/**
* @version $Id:	util.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2010 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

class BS_Util
{
	// Dir size or number of files (0 size, 1 = number)
	public static function dir_size($folder = '', $allowed_types = array('*'), $forbiden_files = array('.', '..', 'index.html', 'index.htm', 'index.php'))
	{
		$oldfolder = getcwd();
		if (!@chdir($folder))
			return array(0, 0);

		$size = 0;
		$nb = 0;

		$dir = @opendir('.');
		while (false !== ($File = @readdir($dir))) {
			$path_parts = strtolower(pathinfo($File, PATHINFO_EXTENSION));
			if (!@is_dir($File) && !in_array(strtolower($File), $forbiden_files) && ($allowed_types[0] == '*' || in_array($path_parts, $allowed_types))) {
				$size += filesize($File);
				$nb += 1;
			}
		}
		@closedir($dir);
		@chdir($oldfolder);

		return array($nb, $size);
	}

	// Resize image to size max $ResizeSizeX * ResizeSizeY
	public static function resize_image($uploadedfile = '', $ResizeSizeX = 0, $ResizeSizeY = 0, $ActualFileName = '')
	{
		if ($ResizeSizeX <= 0 && $ResizeSizeY <= 0) // Nothing to do !
			return false;

		if (!function_exists('gd_info'))
			return false;

		$pparams = JComponentHelper::getParams('com_myjspace');

		$compress_jpg = (int)$pparams->get('compress_jpg', 85);
		if ($compress_jpg < 0)
			$compress_jpg = 0;
		if ($compress_jpg > 100)
			$compress_jpg = 100;

		$compress_png = (int)$pparams->get('compress_png', 3);
		if ($compress_png < 0)
			$compress_png = 0;
		if ($compress_png > 9)
			$compress_png = 9;

		$bigint = PHP_INT_MAX;
		try
		{
			list($Originalwidth, $Originalheight, $image_type) = @getimagesize($uploadedfile); // Get current image size
			switch ($image_type) {
				case 1 :
					$src = imagecreatefromgif($uploadedfile);
					break;
				case 2 :
					$src = imagecreatefromjpeg($uploadedfile);
					break;
				case 3 :
					$src = imagecreatefrompng($uploadedfile);
					break;
				default:
					return false;
					break;
			}

			// Overwrite 0 = unlimited !
			if ($ResizeSizeX == 0)
				$ResizeSizeX = $bigint;
			if ($ResizeSizeY == 0)
				$ResizeSizeY = $bigint;

			if ($Originalwidth <= $ResizeSizeX && $Originalheight <= $ResizeSizeY)
				return false; // Too small, dont resize !

			if ($Originalwidth > $ResizeSizeX)
				$ratioX = $ResizeSizeX/$Originalwidth;
			else
				$ratioX = $bigint;
			if ($Originalheight > $ResizeSizeY)
				$ratioY = $ResizeSizeY/$Originalheight;
			else
				$ratioY = $bigint;
			$ratio = min($ratioX, $ratioY);

			$ResizeSizeX = intval($ratio * $Originalwidth);
			$ResizeSizeY = intval($ratio * $Originalheight);

			$tmp = imagecreatetruecolor($ResizeSizeX, $ResizeSizeY);													// Create new image with calculated dimensions
			imagecopyresampled($tmp, $src, 0, 0, 0, 0, $ResizeSizeX, $ResizeSizeY, $Originalwidth, $Originalheight);	// Resize the image and copy it into $tmp image

			switch ($image_type) {
				case 1 :
					imagegif($tmp, $ActualFileName);
					break;
				case 2 :
					imagejpeg($tmp, $ActualFileName, $compress_jpg);
					break;
				case 3 :
					imagepng($tmp, $ActualFileName, $compress_png);
					break;
				default:
					return false;
					break;
			}
			imagedestroy($src);
			imagedestroy($tmp); // Note: PHP will clean up the temp file it created when the request has completed
		}
		catch(Exception $e)
		{
			echo $e;
			return false;
		}

		return true;
	}

	// Directory list of files
	// $rep folder, $allowed_types : $allowed tab of file types (* all), $forbiden_files : tab forbidden file
	public static function list_file_dir($rep = '', $allowed_types = null, $sort = 0, $forbiden_files = array('.', '..', '.htaccess'), $forbiden_types = array('htm', 'html', 'php'))
	{
		$tab_retour = array();

		if ($rep == '')
			return $tab_retour;

		$oldfolder = getcwd();
		if (!@chdir($rep))
			return $tab_retour;

		if ($dir = @opendir('.')) {
			while (false !== ($File = @readdir($dir))) {
				$path_parts = strtolower(pathinfo($File, PATHINFO_EXTENSION));
				if (!@is_dir($File) && ($allowed_types[0] == '*' || in_array($path_parts, $allowed_types)) && !in_array($path_parts, $forbiden_types) && !in_array(strtolower($File), $forbiden_files)) {
					$tab_retour[] = $File;
				}
			}
			@closedir($dir);
		}

		@chdir($oldfolder);

		if ($sort == 1)
			sort($tab_retour);

		return $tab_retour;
	}

	// Replace some BBcode with HTML equivalent
	public static function bs_bbcode(&$text = null, $width = null, $height = null, &$error = 0)
	{
		if ($text == null)
			return null;

		if (strstr($text, '[') == FALSE) // If no bbcode, return
			return $text;

		$text_source = $text;

		// [img]
		$taille_tmp = '';
		if ($width)
			$taille_tmp .= ' width="'.$width.'" ';
		if ($height)
			$taille_tmp .= ' height="'.$height.'" ';
		$text = preg_replace('!\[img\](.+)\[/img\]!isU', '<a href="$1" rel="lightbox[group]"><img src="$1" '.$taille_tmp.' alt="" /></a>', $text);
		if (preg_last_error() == PREG_BACKTRACK_LIMIT_ERROR) {
			$error = 1;
			return $text_source;
		}

		$text = preg_replace('!\[img=(.+)x(.+)\](.+)\[/img\]!isU', '<a href="$3" rel="lightbox[group]"><img width="$1" height="$2" src="$3" alt="" /></a>', $text);
		if (preg_last_error() == PREG_BACKTRACK_LIMIT_ERROR) {
			$error = 2;
			return $text_source;
		}

		$text = preg_replace('!\[img size=(.+)x(.+)\](.+)\[/img\]!isU', '<a href="$3" rel="lightbox[group]"><img width="$1" height="$2" src="$3" alt="" /></a>', $text);
		if (preg_last_error() == PREG_BACKTRACK_LIMIT_ERROR) {
			$error = 3;
			return $text_source;
		}

		$text = preg_replace('!\[img width=(.+) height=(.+)\](.+)\[/img\]!isU', '<a href="$3" rel="lightbox[group]"><img width="$1" height="$2" src="$3" alt="" /></a>', $text);
		if (preg_last_error() == PREG_BACKTRACK_LIMIT_ERROR) {
			$error = 4;
			return $text_source;
		}

		// [url]
		$text = preg_replace('!\[url\](.+)\[/url\]!isU', '<a href="$1" target="_blank">$1</a>', $text);
		if (preg_last_error() == PREG_BACKTRACK_LIMIT_ERROR) {
			$error = 5;
			return $text_source;
		}

		$text = preg_replace('!\[url=([^\]]+)\](.+)\[/url\]!isU', '<a href="$1" target="_blank">$2</a>', $text);
		if (preg_last_error() == PREG_BACKTRACK_LIMIT_ERROR) {
			$error = 6;
			return $text_source;
		}

		return($text);
	}

	// Send an Email
	public static function send_mail($from = '', $to = '', $subject = '', $body = '')
	{
		$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();

		if ($from == '') { // Default as server configuration
			$sender = array($config->get('mailfrom'), $config->get('fromname'));
		} else {
			$sender = explode(',', $from);
			if (count($sender) == 1)
				$sender[1] = 'Admin';
		}
		$mailer->setSender($sender);

		if ($to == '') { // Default as server configuration
			$recipient = array($config->get('mailfrom'));
		} else {
			$recipient = explode(',', $to);
		}
		$mailer->addRecipient($recipient);

		if ($subject == '') {
			$subject = JURI::base().' - '.$config->get('sitename');
		}
		$mailer->setSubject($subject);

		$mailer->setBody($body);

		$send = $mailer->Send();

		return $send;
	}

	// Convert Kb, Mb, Gb to Bytes
	public static function convertBytes($value = 0)
	{
		if (is_numeric($value)) {
			return $value;
		} else {
			$value_length = strlen($value);
			$qty = substr($value, 0, $value_length - 1);
			$unit = strtolower(substr($value, $value_length - 1));
			switch ($unit) {
				case 'k' :
					$qty *= 1024;
					break;
				case 'm' :
					$qty *= 1048576;
					break;
				case 'g' :
					$qty *= 1073741824;
					break;
			}

			return $qty;
		}
	}

	// Convert Bytes to Kb, Mb, Gb, Tb
	public static function convertSize($bytes = 0)
	{
		$types = array(JText::_('COM_MYJSPACE_UNIT_B'), JText::_('COM_MYJSPACE_UNIT_KB'), JText::_('COM_MYJSPACE_UNIT_MB'), JText::_('COM_MYJSPACE_UNIT_GB'), JText::_('COM_MYJSPACE_UNIT_TB')); // ('B', 'KB', 'MB', 'GB', 'TB');

		for ($i = 0; $bytes >= 1024 && $i < (count($types)-1); $bytes /= 1024, $i++);

		return (round($bytes, 1)." ".$types[$i]);
	}

	// Check if the date is valid for the provided format, and return it to the format 'Y-m-d H:i:s' to save into the DB
	// If KO, return 'now' except if date = ''
	public static function valid_date($date_tmp = '', $date_fmt = 'Y-m-d H:i:s')
	{
		$db	= JFactory::getDBO();
		$nullDate = $db->getNullDate();

		if (!($date_tmp) || $date_tmp == $nullDate || $date_tmp == '0000-00-00 00:00:00') // Null date from DB or from HTML-calendar
			return $nullDate;

		if ($date = DateTime::createFromFormat($date_fmt, $date_tmp)) // PHP >= 5.3.0 mandatory, ok for J3.0+
			$madata = $date->format('Y-m-d H:i:s'); // DB format
		else
			$madata = $nullDate;

		return $madata;
	}

	// Return date converted (language format compliant) & Keep the empty empty
	public static function html_date_empty($my_date = null, $format = 'Y-m-d H:i:s')
	{
		$db	= JFactory::getDBO();
		$nullDate = $db->getNullDate();

		if ($my_date == $nullDate || !$my_date)
			$my_date = '';
		else
			$my_date = date($format, strtotime($my_date));

		return $my_date;
	}

	// Check is the image exists, if 'yes' return the HTML code to display it, else null
	// $mode = 0 => Image Display
	// $mode = 1 => Display a link on image to pre-display with Lytebox
	// $mode = 2 => Page redirection
	public static function exist_image_html($img_dir = null, $img_dir_prefix = JPATH_SITE, $class = 'img-preview', $mode = 0, $title = '', $img_name = 'preview.jpg', $def_img_name = '', $search_image_type = 2, $html = '', $video = 1, $url = '')
	{
		$retour = null;

		if ($search_image_type > 1) // Image exists into html ?
			if ($search_image_type == 5)
				$image_html = self::str_img_src($html, 2, $video);
			else
				$image_html = self::str_img_src($html, 1, $video);
		else
			$image_html = false;

		if ($img_dir != null && $search_image_type < 4 && @file_exists($img_dir_prefix.'/'.$img_dir.'/'.$img_name)) // Image file exists ?
			$image_file = $img_dir.'/'.$img_name;
		else
			$image_file = false;

		// Default image
		if ($def_img_name != '')
			$image_url = $def_img_name;
		else
			$image_url = false;

		// Choice
		if ($search_image_type == 1) {
			if ($image_file)
				$image_url = $image_file;
		} else if ($search_image_type == 2) {
			if ($image_file)
				$image_url = $image_file;
			else if ($image_html)
				$image_url = $image_html;
		} else if ($search_image_type == 3) {
			if ($image_html)
				$image_url = $image_html;
			else if ($image_file)
				$image_url = $image_file;
		} else if ($search_image_type == 4 || $search_image_type == 5) {
			if ($image_html)
				$image_url = $image_html;
		} else {
			$image_url = false;
		}

		if ($image_url) {
			$alt = basename($image_url);
			if ($title == '')
				$title = $alt;

				if ($mode == 0)
					$retour = '<img src="'.$image_url.'" class="'.$class.'" title="'.$title.'" alt="'.$alt.'" />';
				else if ($mode == 1)
					$retour = '<a href="'.$image_url.'" class="lytebox" data-title="'.$title.'"><img src="'.$image_url.'" class="'.$class.'" title="'.$title.'" alt="'.$alt.'" /></a>';
				else
					$retour = '<a href="'.$url.'" ><img src="'.$image_url.'" class="'.$class.'" title="'.$title.'" alt="'.$alt.'" /></a>';
		}

		return $retour;
	}

	/*
	* Occurrences of an HTML <img> element in a string and extracts the src if it finds it
	* Returns boolean false in case <img> element is not found
	* @param    string  $html 	HTML string
	*           integer $mode   1:first one, 2: random
	*			integer video   0: no video else use image video for YouTube, Dailymotion
	* @return   mixed           The contents of the src attribute if image found into <img> or boolean false if no <img>
	*/

	public static function str_img_src(&$html, $mode = 1, $video = 1)
	{
		$matches_all = array();
		if ($video) {
			// dailymotion.com
			$nb_matches_videos = preg_match_all('/dailymotion\.com\/(.*)video\/([\w\-]+)/', $html, $matches_videos);
			if ($nb_matches_videos > 0 && is_array($matches_videos)) { // Preview url
				for ($i = 0; $i < $nb_matches_videos; $i++)
					$matches_all[] = 'http://www.dailymotion.com/thumbnail/video/'.$matches_videos[2][$i];
			}

			// youtube.com
			$nb_matches_videos = preg_match_all('/youtube\.com\/(watch\?v=)?(v\/)?(embed\/)?([\w\-]+)/', $html, $matches_videos);
			if ($nb_matches_videos > 0 && is_array($matches_videos)) { // Preview url
				for ($i = 0; $i < $nb_matches_videos; $i++)
					$matches_all[] = 'http://img.youtube.com/vi/'.$matches_videos[4][$i].'/0.jpg';
			}
		}

		// Image(s)
		preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/Ui', $html, $matches);

		// Merge video & image
		$matches_all = array_merge($matches_all, $matches[1]);
		$nb_matches = count($matches_all);

		$position = ($mode == 1) ? 0 : rand(0, $nb_matches-1);

		if ($nb_matches > 0 && is_array($matches_all) && !empty($matches_all))
			return $matches_all[$position];
		else
			return false;
	}

	// Check if an editor exists and is enabled
	public static function check_editor_selection($editor_selection = '-')
	{
		if ($editor_selection == '-') // 'default' editor
			return true;

		$plugin = JPluginHelper::getPlugin('editors', $editor_selection);
		if (!$plugin)
			return false;

		return true;
	}

	// Generate configuration report
	public static function configuration_report()
	{
		require_once JPATH_ROOT.'/components/com_myjspace/helpers/version.php';

		// Some ideas from Kunena to post on BS MyJspace Website (forum)
		if (ini_get('safe_mode')) {
			$safe_mode = '[u]safe_mode:[/u] [color=#FF0000]On[/color]';
		} else {
			$safe_mode = '[u]safe_mode:[/u] Off';
		}

		// Config

		// DB version
		$db	= JFactory::getDBO();
		$mysqlsersion = $db->getVersion();

		$app = JFactory::getApplication();

		if ($app->get('sef')) {
			$jconfig_sef = 'Enabled';
		} else {
			$jconfig_sef = 'Disabled';
		}
		if ($app->get('sef_rewrite')) {
			$jconfig_sef_rewrite = 'Enabled';
		} else {
			$jconfig_sef_rewrite = 'Disabled';
		}
		if (function_exists('gd_info'))
			$gd_support = 'Yes';
		else
			$gd_support = 'No';

		if (@file_exists(JPATH_ROOT.'/.htaccess')) {
			$htaccess = 'Exists';
		} else {
			$htaccess = 'None';
		}

		$file = JPATH_ROOT.'/components/com_myjspace/helpers/util.xml';
		if (@file_exists($file)) {
			libxml_use_internal_errors(true);
			$xml = @simplexml_load_file($file);

			if (isset($xml->extension)) {
				$myelement = '';
				foreach ($xml->extension as $value){
					if (isset($value->type) && isset($value->folder) && isset($value->element)) {
						$query = $db->getQuery(true)
							->select(array('element', 'type', 'folder', 'manifest_cache', 'enabled'))
							->from('#__extensions')
							->where($db->qn('element').' = '.$db->q($value->element).' AND '.$db->qn('type').' = '.$db->q($value->type).' AND '.$db->qn('folder').' = '.$db->q($value->folder));

						$db->setQuery($query);
						$result = $db->loadObject();
						if ($result) {
							if ($myelement)
								$myelement .= ' | ';
							$data = json_decode($result->manifest_cache, true);
							if (!isset($data['version']))
								$data['version'] = '';
							if (!isset($data['creationDate']))
								$data['creationDate'] = '';
							$myelement .= $result->element.':'.$result->folder.':'.$result->type.' '.$data['version'].' '.$data['creationDate'].' '.$result->enabled;
						}
					}
				}
			}
		}

		$template = $app->getTemplate();
		$template_user = '';

		$query = $db->getQuery(true)
			->select('template')
			->from('#__template_styles')
			->where($db->qn('home').' = '.$db->q('1').' AND '.$db->qn('template').' = '.$db->q($template));

		$db->setQuery($query);
		$db_template = $db->loadRow();

		if ($db_template)
			$template_user = ' user:'.implode(',', $db_template);

		if (isset($_SERVER['HTTP_USER_AGENT']))
			$http_user_agent = $_SERVER['HTTP_USER_AGENT'];
		else
			$http_user_agent = 'undefined';

		$sef = array();
		$sef['sh404sef'] = self::getExtensionVersion('com_sh404sef', 'sh404sef');
		$sef['joomsef'] = self::getExtensionVersion('com_joomsef', 'ARTIO JoomSEF');
		$sef['acesef'] = self::getExtensionVersion('com_acesef', 'AceSEF');
		foreach ($sef as $id=>$item) {
			if (empty($item)) unset ($sef[$id]);
		}

		$retour = '[confidential][b]Joomla! version:[/b] '.JVERSION.' [b]Platform:[/b] '.$_SERVER['SERVER_SOFTWARE'].' ('.$_SERVER['SERVER_NAME'].') [b]PHP version:[/b] '.phpversion().' | '.$safe_mode
				.' | [b]MySQL version:[/b] '.$mysqlsersion.' | [b]Base URL:[/b] '.JURI::root().'[/confidential]';

		$retour .= ' [quote][b]Joomla! SEF:[/b] '.$jconfig_sef.' | [b]Joomla! SEF rewrite:[/b] '.$jconfig_sef_rewrite.' | [b]htaccess:[/b] '.$htaccess.' | [b]GD: [/b] '.$gd_support
				.' | [b]PHP environment:[/b] [u]Max execution time:[/u] '.ini_get('max_execution_time').' seconds | [u]Max execution memory:[/u] '
				.ini_get('memory_limit').' | [u]Max file upload:[/u] '.ini_get('upload_max_filesize').' [/quote] [quote][b]Joomla! default template:[/b] admin:'.$template.$template_user.' [/quote]';

		$retour .= ' [quote] [b]http_user_agent:[/b] '.$http_user_agent.'[/quote]';

		$retour .= '[confidential][b]BS MyJSpace version:[/b] '.BS_Helper_version::getXmlParam('com_myjspace', 'creationDate').' | '.BS_Helper_version::getXmlParam('com_myjspace', 'author').' | '.BS_Helper_version::getXmlParam('com_myjspace', 'version').' | '.BS_Helper_version::getXmlParam('com_myjspace', 'build');

		if (isset($myelement))
			$retour .= ' [quote][b]BS MyJSpace elements:[/b] '.$myelement.'[/quote]';

		if (!empty($sef)) $retour .= ' [quote][b]Extra (checked) SEF components:[/b] '.implode(' | ', $sef).' [/quote]';
		else $retour .= ' [quote][b]Extra (checked) SEF components:[/b] None [/quote]';

		$retour .= '[/confidential]';

		return $retour;
	}

	public static function getExtensionVersion($extension = '', $name = '')
	{
		$version = BS_Helper_version::getXmlParam($extension, 'version');

		return $version ? '[u]'.$name.'[/u] '.$version : '';
	}

	/**
	* Get the 'the right' Itemed relative to the menu
	* Take into account the category, if any
	*/
	public static function get_menu_itemid($url = '', $default = 0, $catid = 0)
	{
		$menu = JApplicationCms::getInstance('site')->getMenu();

		if (!$menu)
			return 0;

		// Reconstruct the url with only the necessary params: option & view
		$url2 = $url;

		$url_parse = parse_url($url, PHP_URL_QUERY);
		foreach (explode('&', $url_parse) as $chunk) {
			$param = explode('=', $chunk);

			if (isset($param[0]) && isset($param[1]) && $param[0] == 'view')
				$url2 = 'index.php?option=com_myjspace&view='.$param[1];
		}

		// Check menu list for the 'closest' itemid
		$menu_items = $menu->getItems('link', $url2);

		$last_id = 0;
		$first_id = false;
		$second_id = false;

		if (count($menu_items)) { // If we have a menu with this view
			foreach ($menu_items as $i => $v) { // If the default is included into the list

				if ((int)$menu_items[$i]->id == $default && (int)$menu->getParams($menu_items[$i]->id)->get('catid') == $catid)
					return $default;

				$last_id = (int)$menu_items[$i]->id; // If no specific menu for this catid => default itemid to be used

				if ((int)$menu->getParams($menu_items[$i]->id)->get('catid') == 0) {
					if ($second_id === false)
						$second_id = $last_id;
				} else if ((int)$menu->getParams($menu_items[$i]->id)->get('catid') == $catid) {
					if ($first_id === false)
						$first_id = $last_id;
				}
			}
		}

		if ($first_id)
			return $first_id;
		else if ($second_id)
			return $second_id;
		else if ($last_id)
			return $last_id;
		else
			return 0;
	}

	// User IP Address ... may be too much complex ,for old servers config & proxy
	public static function addr_ip()
	{
		if (isset($_SERVER)) {
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
				$realip = $_SERVER['HTTP_CLIENT_IP'];
			} else {
				$realip = $_SERVER['REMOTE_ADDR'];
			}
		} else {
			if (getenv('HTTP_X_FORWARDED_FOR')) {
				$realip = getenv('HTTP_X_FORWARDED_FOR');
			} elseif (getenv('HTTP_CLIENT_IP')) {
				$realip = getenv('HTTP_CLIENT_IP');
			} else {
				$realip = getenv('REMOTE_ADDR');
			}
		}

		return $realip;
	}

	// Clean (delete HTML tag, #tags {myjsp ..})the HTML text for search display
	// Only return a limit part of the content after clean-up
	public static function clean_html_text($text = '', $contentLimit = 150, $uid = 0, $suffix = '...')
	{
		$searchRegex = array(
			'#<script[^>]*>.*?</script>#si',
			'#<style[^>]*>.*?</style>#si',
			'#<!.*?(--|]])>#si',
			'#<[^>]*>#i'
		);

		// Replace line breaking tags with whitespace
		$text = preg_replace("'&nbsp;/|<(br[^/>]*?/|hr[^/>]*?)>'si", ' ', $text);

		// Delete HTML tags
		foreach ($searchRegex as $regex) {
			$text = preg_replace($regex, '', $text);
		}

		// Hide all not visible for everybody
		$hidden = '...';

		// Hide #Tags
		$search = array('#userid', '#name', '#username', '#id', '#title', '#pagename', '#access', '#shareedit', '#lastupdate', '#lastaccess', '#createdate', '#description', '#category', '#bsmyjspace', '#modifiedby', '#language', '#fileslist', '#cbprofile', '#hits', '#jomsocial-profile', '#jomsocial-photos');
		$replace = array($hidden, $hidden, $hidden, $hidden, $hidden, $hidden, $hidden, $hidden, $hidden, $hidden, $hidden, $hidden, $hidden, $hidden, $hidden, $hidden, $hidden, $hidden, $hidden, $hidden, $hidden);
		$text = str_replace($search, $replace, $text);

		// Hide Tag {myjsp ... }
		$text = preg_replace('!{myjsp (.+)\}!isU', $hidden, $text); // Even if {} tags (deleted by search display function)

		// Hide BBCode [register]
		if ($uid != 0) // If the user is registered
			$text = preg_replace('!\[register\](.+)\[/register\]!isU', '$1', $text);
		else // If not registered
			$text = preg_replace('!\[register\](.+)\[/register\]!isU', $hidden, $text); // Keep it secret :-)

		// Delete multiple spaces and multiples ...
		$text = preg_replace("# +#", ' ', $text);
		$text = preg_replace("#\.\.\.+#", '...', $text);

		// Length
		if ($contentLimit)
			$text = self::clean_text($text, $contentLimit, $suffix) ;

		return $text;
	}

	public static function clean_text($text = '', $contentLimit = 150, $suffix = '...')
	{
		if (strlen($text) <= $contentLimit)
			return $text;

		$text = substr($text, 0, $contentLimit - strlen($suffix)).$suffix;

		return $text;
	}
}
