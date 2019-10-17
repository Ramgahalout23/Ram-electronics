<?php
/**
* @version $Id: myjspace.php $
* @version		3.0.0 29/06/2019
* @package		plg_finder_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2019 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

JLoader::register('FinderIndexerAdapter', JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/adapter.php');

/**
 * Smart Search adapter for com_myjspace.
 *
 * @since  2.5
 */
class PlgFinderMyJspace extends FinderIndexerAdapter
{
	/**
	 * The plugin identifier.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $context = 'Myjspace';

	/**
	 * The extension name.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $extension = 'com_myjspace';

	/**
	 * The sublayout to use when rendering the results.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $layout = 'see';

	/**
	 * The type of content that the adapter indexes.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $type_title = 'MyJspace';

	/**
	 * The table name.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $table = '#__myjspace';

	/*
	* The database field in your table that the published state of an item is stored in; default's to "state".
	* pas d'équivalent exacte pour MyJspace
	*/

	protected $state_field = 'blockview';

	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Method to update the item link information when the item category is
	 * changed. This is fired when the item category is published or unpublished
	 * from the list view.
	 *
	 * @param   string   $extension  The extension whose category has been updated.
	 * @param   array    $pks        A list of primary key ids of the content that has changed state.
	 * @param   integer  $value      The value of the state that the content has been changed to.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */

	public function onFinderCategoryChangeState($extension, $pks, $value)
	{
		// Make sure we're handling com_myjspace categories.
		if ($extension === 'com_myjspace') {
			$this->categoryStateChange($pks, $value);
		}
	}

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param   string  $context  The context of the action being performed.
	 * @param   JTable  $table    A JTable object containing the record to be deleted
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterDelete($context, $table)
	{
		if ($context === 'com_myjspace.see') {
			$id = $table->id;
		} elseif ($context === 'com_finder.index') {
			$id = $table->link_id;
		} else {
			return true;
		}

		// Remove item from the index.
		return $this->remove($id);
	}

	/**
	 * Smart Search after save content method.
	 * Reindexes the link information for an article that has been saved.
	 * It also makes adjustments if the access level of an item or the
	 * category to which it belongs has changed.
	 *
	 * @param   string   $context  The context of the content passed to the plugin.
	 * @param   JTable   $row      A JTable object.
	 * @param   boolean  $isNew    True if the content has just been created.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterSave($context, $row, $isNew)
	{
		// We only want to handle pages here.
		if ($context === 'com_myjspace.see') {
/*
			// Check if the access levels are different.
			if (!$isNew && $this->old_access != $row->access) {
				// Process the change.
				$this->itemAccessChange($row);
			}
*/
			// Reindex the item.
			$this->reindex($row->id);
		}

		// Check for access changes in the category.
		if ($context === 'com_categories.category') {
			// Check if the access levels are different.
			if (!$isNew && $this->old_cataccess != $row->access) {
				$this->categoryAccessChange($row);
			}
		}

		return true;
	}

	/**
	 * Smart Search before content save method.
	 * This event is fired before the data is actually saved.
	 *
	 * @param   string   $context  The context of the content passed to the plugin.
	 * @param   JTable   $row      A JTable object.
	 * @param   boolean  $isNew    If the content is just about to be created.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function onFinderBeforeSave($context, $row, $isNew)
	{
/*
		// We only want to handle pages here.
		if ($context === 'com_myjspace.see') {
			// Query the database for the old access level if the item isn't new.
			if (!$isNew) {
				$this->checkItemAccess($row);
			}
		}
*/
		// Check for access levels from the category.
		if ($context === 'com_categories.category') {
			// Query the database for the old access level if the item isn't new.
			if (!$isNew) {
				$this->checkCategoryAccess($row);
			}
		}

		return true;
	}

	/**
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen. This is fired when the item is published,
	 * unpublished, archived, or unarchived from the list view.
	 *
	 * @param   string   $context  The context for the content passed to the plugin.
	 * @param   array    $pks      An array of primary key ids of the content that has changed state.
	 * @param   integer  $value    The value of the state that the content has been changed to.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function onFinderChangeState($context, $pks, $value)
	{
/*
		// We only want to handle pages here.
		if ($context === 'com_myjspace.see') {
			$this->itemStateChange($pks, $value);
		}
*/
		// Handle when the plugin is disabled.
		if ($context === 'com_plugins.plugin' && $value === 0) {
			$this->pluginDisable($pks);
		}
	}

	/**
	 * Method to index an item. The item must be a FinderIndexerResult object.
	 *
	 * @param   FinderIndexerResult  $item    The item to index as a FinderIndexerResult object.
	 * @param   string               $format  The item format.  Not used.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	protected function index(FinderIndexerResult $item, $format = 'html')
	{
		// Check if the extension is enabled.
		if (JComponentHelper::isEnabled($this->extension) === false) {
			return;
		}

		require_once JPATH_ROOT.'/components/com_myjspace/helpers/route.php';

		$item->setLanguage();
		$item->context = 'com_myjspace.see';		

		// Initialize the item parameters
		$registry = new Registry($item->params);
		$item->params = JComponentHelper::getParams('com_myjspace', true);
		$item->params->merge($registry);

//		$item->metadata = new Registry(''); // Pas de contenu metadata pour BS MyJspace

		$item->body = BS_Util::clean_html_text($item->body, 0); // Del MyJspace tags, bbcode, [register ...
		// Trigger the onContentPrepare event (invertion par rapport à Joomla content ?)
		$item->summary = FinderIndexerHelper::prepareContent($item->body, $item->params, $item);
		$item->body = FinderIndexerHelper::prepareContent($item->metadesc, $item->params, $item);

		// MyJspace 'url' 3 modes
		$item->url = $this->getUrl($item->id, $this->extension, $this->layout); //'index.php?option=com_myjspace&view=see&id='.$item->id;
		$item->route = MyJspaceHelperRoute::getPageRoute($item->alias, $item->catid);
//		$item->path = FinderIndexerHelper::getContentPath($item->route); // AFAIRE voir si vraiment necessaire pour J!3?

		// Access
		if ($item->access) // EVOL ajout ACL + test que category publiée aussi
			$item->state = 1;
		else
			$item->state = 0;

		// Author
		$table = JUser::getTable();
		if ($table->load($item->created_by)) { // Test if user exists before to retrieve info
			$user = JFactory::getUser($item->created_by);
		} else { // User no longer exists !
			$user = new stdClass();
			$user->username = '';
			$user->name = '';
		}
		$item->author = $user->username;
		$item->created_by_alias = $user->name;

		// Add the type taxonomy data
		$item->addTaxonomy('Type', 'Pages'); // EVOL voir pour multilangue dans les criteres de recherche ...

		// Add the category taxonomy data
		if ($item->catid) {
// J!4
//			$categories = Categories::getInstance('com_myjspace');
//			$category = $categories->get($item->catid);
//			$item->addNestedTaxonomy('Category', $category, $category->published, $category->access, $category->language);
			$item->addTaxonomy('Category', $item->category, $item->cat_state, $item->cat_access);
		}

		// Add the language taxonomy data
		$item->addTaxonomy('Language', $item->language);

		// Add the author taxonomy data
        if (!empty($item->author) || !empty($item->created_by_alias)) {
			$item->addTaxonomy('Author', !empty($item->created_by_alias) ? $item->created_by_alias : $item->author);
        }

		// EVOL Pour J!4 ? interet à verifier
//		$item->addTaxonomy('access', $this->access);
//		$item->addTaxonomy('state', $this->state);

		// Get content extras
		FinderIndexerHelper::getContentExtras($item);

		// Index the item
		$this->indexer->index($item);
	}

	/**
	 * Method to setup the indexer to be run.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 */
	protected function setup()
	{
		return true;
	}

	/**
	 * Method to get the SQL query used to retrieve the list of content items.
	 *
	 * @param   mixed  $query  A JDatabaseQuery object or null.
	 *
	 * @return  JDatabaseQuery  A database object.
	 *
	 * @since   2.5
	 */
	protected function getListQuery($query = null)
	{
		$db = JFactory::getDbo();

		// Check if we can use the supplied SQL query.
		$query = $query instanceof JDatabaseQuery ? $query : $db->getQuery(true)
			->select('a.id, a.title, a.pagename AS alias, foldername, a.content AS body')
			->select('a.blockview AS access, a.catid, a.create_date AS start_date, a.userid AS created_by')
			->select('a.last_update_date AS modified, a.userid AS modified_by')
			->select('a.metakey, a.metakey AS metadesc, a.language')
			->select('a.publish_up AS publish_start_date, a.publish_down AS publish_end_date')
			->select('c.title AS category, c.published AS cat_state, c.access AS cat_access');

		// Handle the alias (pagename CASE WHEN portion of the query

		$case_when_item_alias = ' CASE WHEN ';
		$case_when_item_alias .= $query->charLength('a.pagename', '!=', '0');
		$case_when_item_alias .= ' THEN ';
		$a_id = $query->castAsChar('a.id');
		$case_when_item_alias .= $query->concatenate(array($a_id, 'a.pagename'), ':');
		$case_when_item_alias .= ' ELSE ';
		$case_when_item_alias .= $a_id . ' END as slug';
		$query->select($case_when_item_alias);

		$case_when_category_alias = ' CASE WHEN ';
		$case_when_category_alias .= $query->charLength('c.alias', '!=', '0');
		$case_when_category_alias .= ' THEN ';
		$c_id = $query->castAsChar('c.id');
		$case_when_category_alias .= $query->concatenate(array($c_id, 'c.alias'), ':');
		$case_when_category_alias .= ' ELSE ';
		$case_when_category_alias .= $c_id . ' END as catslug';
		$query->select($case_when_category_alias)

			->select('u.name AS author')
			->from('#__myjspace AS a')
			->join('LEFT', '#__categories AS c ON c.id = a.catid')
			->join('LEFT', '#__users AS u ON u.id = a.userid');

		return $query;
	}
}
