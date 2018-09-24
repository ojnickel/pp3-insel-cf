<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.beez3
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Aenderung OWS Andreas Wagner
 * 20150421
 * Verhindert die Anzeige von [br]
 */

defined('_JEXEC') or die;
?>

<ul class = "breadcrumbs<?php echo $moduleclass_sfx; ?>">
<?php if ($params->get('showHere', 1))
	{
		echo '<span class="showHere">' .JText::_('MOD_BREADCRUMBS_HERE').'</span>';
	}

	// Get rid of duplicated entries on trail including home page when using multilanguage
	for ($i = 0; $i < $count; $i++)
	{
		if ($i == 1 && !empty($list[$i]->link) && !empty($list[$i - 1]->link) && $list[$i]->link == $list[$i - 1]->link)
		{
			unset($list[$i]);
		}
	}

	// Find last and penultimate items in breadcrumbs list
	end($list);
	$last_item_key = key($list);
	prev($list);
	$penult_item_key = key($list);

	// Generate the trail
	foreach ($list as $key => $item) :
	// Make a link if not the last item in the breadcrumbs
	$show_last = $params->get('showLast', 1);
  // OWS Andreas Wagner [br] ausblenden
  $nameobr = $item->name;
  $nameobr = str_replace("[br]","", $nameobr);

	if ($key != $last_item_key)
	{
		// Render all but last item - along with separator
		if (!empty($item->link))
		{
			echo '<li><a href="' . $item->link . '" class="pathway">' . $nameobr . '</a></li>';
		}
		else
		{
			echo '<li><span>' . $nameobr . '</span></li>';
		}

		// if (($key != $penult_item_key) || $show_last)
		// {
		// 	echo ' '.$separator.' ';
		// }

	}
	elseif ($show_last)
	{
		// Render last item if reqd.
		echo '<li><span>' . $nameobr . '</span></li>';
	}
	endforeach; ?>
</ul>
