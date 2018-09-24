<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * 
 * Aenderung Josef Mueller OWS: 
 * 20150703
 * Korrektur der DIV-Klasse "contact...", wenn Suffix leer
 */

defined('_JEXEC') or die;

$cparams = JComponentHelper::getParams('com_media');
?>
<h2>Kontaktformular</h2>
<div class="contact<?php echo ("" != $this->pageclass_sfx)? $this->pageclass_sfx : "_kontakte"; // OWS Josef Mueller, Korrektur der DIV-Klasse "contact...", wenn Suffix leer ?>">
	<?php if ($this->params->get('show_contact_list') && count($this->contacts) > 1) : ?>
		<form action="#" method="get" name="selectForm" id="selectForm">
			<?php echo JText::_('COM_CONTACT_SELECT_CONTACT'); ?>
			<?php echo JHtml::_('select.genericlist', $this->contacts, 'id', 'class="inputbox" onchange="document.location.href = this.value"', 'link', 'name', $this->contact->link);?>
		</form>
	<?php endif; ?>

	<?php if ($this->contact->name && $this->params->get('show_name')) : ?>
		<div style="width:476px; background-color:#eef7f4; padding:0 0 10px 20px;">
			<h3>
        <?php
          // Wenn Komma vorhanden, dann Vor- und Nachname tauschen
          $name = $this->contact->name;
          $pos = strpos($name, ',');
          if ($pos !== false) {
            // Komma vorhanden
            $name = trim(substr($name,$pos+1,strlen($name)-$pos-1) ." ".substr($name,0,$pos));
          }
          echo $this->escape($name);
        ?>
			</h3>
   	<?php endif; ?>
 
  <?php
		$gmbh_und_position = '';
		if ($this->contact->state && $this->contact->params->get('show_state')) { $gmbh_und_position = $this->escape($this->contact->state) . "<br>"; }
		if ($this->contact->con_position && $this->contact->params->get('show_position')) { $gmbh_und_position .= $this->escape($this->contact->con_position); }
		if (substr($gmbh_und_position,strlen($gmbh_und_position)-4,4) == "<br>") { $gmbh_und_position = substr($gmbh_und_position,0,strlen($gmbh_und_position)-4); }
		echo "<p>".$gmbh_und_position."</p>";
	?>
  
  <?php if ($this->contact->image && $this->params->get('show_image')) : ?>
		<div style="float: right; margin: -37px 15px 0 0;">
			<?php echo JHtml::_('image', 'images/stories' . '/'.$this->escape($this->contact->image), JText::_('COM_CONTACT_IMAGE_DETAILS'), array('align' => 'middle', 'itemprop' => 'image')); ?>
		</div>
	<?php endif; ?>
  
	<?php echo $this->loadTemplate('address'); ?>

		</div>
  <?php if ($this->params->get('allow_vcard')) :	?>
		<?php echo JText::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS');?>
		<a href="<?php echo JRoute::_('index.php?option=com_contact&amp;view=contact&amp;id=' . $this->contact->id . '&amp;format=vcf'); ?>">
		<?php echo JText::_('COM_CONTACT_VCARD');?></a>
	<?php endif; ?>
  
	<?php if ($this->params->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?>
		<?php  echo $this->loadTemplate('form');  ?>
	<?php endif; ?>
</div>
