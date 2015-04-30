<?php
/**
 * View to edit a group.
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2015 fabrikar.com - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

namespace Fabrik\Admin\Views\Group;
// No direct access
defined('_JEXEC') or die('Restricted access');

use \FabrikHelperHTML as FabrikHelperHTML;
use \JFactory as JFactory;
use \FabrikAdminHelper as FabrikAdminHelper;
use \FText as FText;
use \JToolBarHelper as JToolBarHelper;

/**
 * View to edit a group.
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @since       3.5
 */
class Html extends \JViewHtml
{
	/**
	 * Form
	 *
	 * @var JForm
	 */
	protected $form;

	/**
	 * Group item
	 *
	 * @var JTable
	 */
	protected $item;

	/**
	 * View state
	 *
	 * @var object
	 */
	protected $state;

	/**
	 * Render the view
	 *
	 * @return  string
	 */
	public function render()
	{
		// Initialise variables.
		$this->form = $this->model->getForm();
		$this->item = $this->model->getItem();
		$this->state = $this->model->getState();

		$this->addToolbar();

		$srcs = FabrikHelperHTML::framework();
		FabrikHelperHTML::iniRequireJS();
		FabrikHelperHTML::script($srcs);

		return parent::render();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 */

	protected function addToolbar()
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$input->set('hidemainmenu', true);
		$user = JFactory::getUser();
		$isNew = ($this->item->id == 0);
		$userId = $user->get('id');
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo = FabrikAdminHelper::getActions($this->state->get('filter.category_id'));
		$title = $isNew ? FText::_('COM_FABRIK_MANAGER_GROUP_NEW') : FText::_('COM_FABRIK_MANAGER_GROUP_EDIT') . ' "' . $this->item->name . '"';
		JToolBarHelper::title($title, 'group.png');

		if ($isNew)
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::apply('group.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('group.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::addNew('group.save2new', 'JTOOLBAR_SAVE_AND_NEW');
			}

			JToolBarHelper::cancel('group.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			// Can't save the record if it's checked out.
			if (!$checkedOut)
			{
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId))
				{
					JToolBarHelper::apply('group.apply', 'JTOOLBAR_APPLY');
					JToolBarHelper::save('group.save', 'JTOOLBAR_SAVE');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create'))
					{
						JToolBarHelper::addNew('group.save2new', 'JTOOLBAR_SAVE_AND_NEW');
					}
				}
			}

			if ($canDo->get('core.create'))
			{
				JToolBarHelper::custom('group.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}

			JToolBarHelper::cancel('group.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_COMPONENTS_FABRIK_GROUPS_EDIT', false, FText::_('JHELP_COMPONENTS_FABRIK_GROUPS_EDIT'));
	}
}