<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Aenderung OWS Andreas Wagner
 * 20150415
 * Verweis auf Ueberschreibung
 */

defined('_JEXEC') or die;


// Note. It is important to remove spaces between elements.
		// OWS Andreas Wagner: Gibt den Suffix des ausgewaehlten Menues zurueck (class-Zusatz der Seite)
		$pageclass   = "";
		if (is_object( $active )) {
			$app = JFactory::getApplication();
			$menu = $app->getMenu()->getActive();
			// OWS Josef Mueller
			if (!$menu) {
				$menu = &JSite::getMenu();
				$menu = $menu->getItem(338);
			}
			if ($menu) {
				$pageclass = $menu->params->get('pageclass_sfx');
			}
		}
?>
<?php // The menu class is deprecated. Use nav instead. ?>
<ul class="nav menu<?php echo $class_sfx . ' p'. $pageclass ?>"<?php
	$tag = '';

	if ($params->get('tag_id') != null)
	{
		$tag = $params->get('tag_id') . '';
		echo ' id="' . $tag . '"';
	}
?>>
<?php
foreach ($list as $i => &$item)
{
	// OWS Andreas Wagner: keine itemid stattdessen parent
	//$class = 'item-'.$item->id;
    $class = 'i_'.$item->id;

	if (($item->id == $active_id) OR ($item->type == 'alias' AND $item->params->get('aliasoptions') == $active_id))
	{
		$class .= ' current';
	}

	if (in_array($item->id, $path))
	{
		$class .= ' active';
	}
	elseif ($item->type == 'alias')
	{
		$aliasToId = $item->params->get('aliasoptions');

		if (count($path) > 0 && $aliasToId == $path[count($path) - 1])
		{
			$class .= ' active';
		}
		elseif (in_array($aliasToId, $path))
		{
			$class .= ' alias-parent-active';
		}
	}

	if ($item->type == 'separator')
	{
		$class .= ' divider';
	}

	if ($item->deeper)
	{
		$class .= ' deeper';
	}

	if ($item->parent)
	{
		$class .= ' parent';
	}

	if (!empty($class))
	{
		$class = ' class="' . trim($class) . '"';
	}

	echo '<li' . $class . '>';

	// Render the menu item.
	switch ($item->type) :
		case 'separator':
		case 'url':
		case 'component':
		case 'heading':
			require JModuleHelper::getLayoutPath('mod_menu', 'pphauptmenue_' . $item->type);
			break;

		default:
      // OWS Andreas Wagner: Verweis auf Ueberschreibung
			require JModuleHelper::getLayoutPath('mod_menu', 'pphauptmenue_url');
			break;
	endswitch;

	// The next item is deeper.
	if ($item->deeper)
	{
    // OWS Andreas Wagner: class Aenderung
		echo '<ul class="nav-child">';
	}
	elseif ($item->shallower)
	{
		// The next item is shallower.
		echo '</li>';
		echo str_repeat('</ul></li>', $item->level_diff);
	}
	else
	{
		// The next item is on the same level.
		echo '</li>';
	}
}
?></ul>
