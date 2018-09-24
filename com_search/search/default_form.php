<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<form action="<?php echo JRoute::_('index.php?option=com_search');?>" method="post" class="search_result<?php echo $this->params->get('pageclass_sfx'); ?>">
<h3><?php echo JText::_('COM_SEARCH_SEARCH_AGAIN'); ?></h3>
<fieldset class="word">
<input type="text" name="searchword" id="search_searchword" placeholder="<?php echo JText::_('COM_SEARCH_SEARCH_KEYWORD'); ?>" maxlength="20" value="<?php echo $this->escape($this->origkeyword); ?>" class="inputbox" />
<button name="Search" onclick="this.form.submit()" class="search_button"><?php echo JText::_('COM_SEARCH_SEARCH');?></button>
</fieldset>
</form>
