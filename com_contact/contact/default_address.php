<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Marker_class: Class based on the selection of text, none, or icons
 * jicon-text, jicon-none, jicon-icon
 */
?>
<div class="contact_address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
	<?php if (($this->params->get('address_check') > 0) &&
		($this->contact->address || $this->contact->suburb  || $this->contact->state || $this->contact->country || $this->contact->postcode)) : ?>
    <address>
		<?php if ($this->params->get('address_check') > 0) : ?>
			<dt>
				<span class="<?php echo $this->params->get('marker_class'); ?>" >
					<?php echo $this->params->get('marker_address'); ?>
				</span>
			</dt>
		<?php endif; ?>

		<?php if ($this->contact->address && $this->params->get('show_street_address')) : ?>
			<dd>
				<span class="contact-street" itemprop="streetAddress">
					<?php echo nl2br($this->contact->address) . '<br />'; ?>
				</span>
			</dd>
		<?php endif; ?>

		<?php if ($this->contact->suburb && $this->params->get('show_suburb')) : ?>
			<dd>
				<span class="contact-suburb" itemprop="addressLocality">
					<?php echo $this->contact->suburb . '<br />'; ?>
				</span>
			</dd>
		<?php endif; ?>
		<?php if ($this->contact->state && $this->params->get('show_state')) : ?>
			<dd>
				<span class="contact-state" itemprop="addressRegion">
					<?php echo $this->contact->state . '<br />'; ?>
				</span>
			</dd>
		<?php endif; ?>
		<?php if ($this->contact->postcode && $this->params->get('show_postcode')) : ?>
			<dd>
				<span class="contact-postcode" itemprop="postalCode">
					<?php echo $this->contact->postcode . '<br />'; ?>
				</span>
			</dd>
		<?php endif; ?>
		<?php if ($this->contact->country && $this->params->get('show_country')) : ?>
		<dd>
			<span class="contact-country" itemprop="addressCountry">
				<?php echo $this->contact->country . '<br />'; ?>
			</span>
		</dd>
		<?php endif; ?>
    </address>
	<?php endif; ?>

<?php if ($this->contact->telephone && $this->params->get('show_telephone')) : ?>
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_telephone'); ?>
		</span>
		<div class="wrap_telephone"><span class="telephone">Telefon: </span>
    <span>
    <?php echo nl2br($this->contact->telephone); ?>
    </span>
    </div>
<?php endif; ?>

<?php if ($this->contact->fax && $this->params->get('show_fax')) : ?>
		<span class="<?php echo $this->params->get('marker_class'); ?>">
			<?php echo $this->params->get('marker_fax'); ?>
		</span>
    <div class="wrap_fax"><span class="fax">Fax: </span>
    <span>
		<?php echo nl2br($this->contact->fax); ?>
    </span>
    </div>
<?php endif; ?>

<?php if ($this->contact->mobile && $this->params->get('show_mobile')) :?>
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_mobile'); ?>
		</span>
    <div class="wrap_mobile"><span class="mobile">Handy: </span>
      <span>
			<?php echo nl2br($this->contact->mobile); ?>
      </span>
    </div>
<?php endif; ?>

<?php if ($this->contact->email_to && $this->params->get('show_email')) : ?>
		<span class="<?php echo $this->params->get('marker_class'); ?>" itemprop="email">
			<?php echo nl2br($this->params->get('marker_email')); ?>
		</span>
	<span class="email">Mail: </span>
			<?php echo $this->contact->email_to; ?>
<?php endif; ?>

<?php if ($this->contact->webpage && $this->params->get('show_webpage')) : ?>
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
		</span>
			<a href="<?php echo $this->contact->webpage; ?>" target="_blank" itemprop="url">
			<?php echo JStringPunycode::urlToUTF8($this->contact->webpage); ?></a>
<?php endif; ?>
</address>
</div>

<?php if ($this->contact->misc && $this->contact->params->get('show_misc')) : ?>
<p>
  <span class="marker"><?php echo $this->contact->params->get('marker_misc'); ?></span>
  <?php echo nl2br($this->contact->misc); ?>
</p>
<?php endif; ?>

