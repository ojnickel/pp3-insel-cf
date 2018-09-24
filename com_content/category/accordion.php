<?php
/**
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$templateparams = $app->getTemplate(true)->params;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::_('behavior.caption');

$cparams = JComponentHelper::getParams('com_media');

// If the page class is defined, add to class as suffix.
// It will be a separate class if the user starts it with a space
?>

<section id="accordion" class="blog<?php echo $this->pageclass_sfx;?>">
<?php if ($this->params->get('show_page_heading') != 0) : ?>
<?php // OWS Andreas Wagner: h1 zu h2 ?>
<h2>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h2>
<?php endif; ?>

<?php if ($this->params->get('show_category_title')) : ?>
<h2 class="subheading-category">
	<?php echo JHtml::_('content.prepare', $this->category->title, '', 'com_content.category.title'); ?>
</h2>
<?php endif; ?>

<?php if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
	<div class="category-desc">
	<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
		<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
	<?php endif; ?>
	<?php if ($this->params->get('show_description') && $this->category->description) : ?>
		<?php echo JHtml::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
	<?php endif; ?>
	<div class="clr"></div>
	</div>
<?php endif; ?>

<?php if (empty($this->lead_items) && empty($this->link_items) && empty($this->intro_items)) : ?>
	<?php if ($this->params->get('show_no_articles', 1)) : ?>
		<p><?php echo JText::_('COM_CONTENT_NO_ARTICLES'); ?></p>
	<?php endif; ?>
<?php endif; ?>



	<?php
    $last_category = null;
    foreach ($this->lead_items as &$item) :
                $this->item = &$item;

                                if ($this->item->category_title != $last_category) {
                                    if (!empty($last_category)) {
                                        echo '</ul>';
                                    }
                                    echo '<h3>'.$this->item->category_title.'</h3><ul class="accordion">';
                                    $last_category = $this->item->category_title;
                                }
                                echo '<li>'.$this->loadTemplate('item').'</li>';
    endforeach; ?>
</ul>
<a class="showall" href="#">Alles aufklappen</a>
</section>

<style>
 @media print {
  #left,
  #header,
  #breadcrumbs,
	#footer,
	#accordion a.showall {
   display: none;
  }
  #wrapperall, #all {
		min-width: 0;
	 width: 100%;
  }
 }
</style>

<script>

jQuery('.accordion .accordionHeader a').click(function(e) {
  e.preventDefault();
  accLink = jQuery(this);
  accParent = jQuery(this).closest('li');

  if (accParent.hasClass('accordionOpen')) {
	jQuery('.accordionOpen .accordionItem').slideUp(500);
	jQuery('.accordionOpen').removeClass('accordionOpen').find(".triangle").removeClass("triup").addClass("tridwn");
  } else {
		jQuery('.accordionOpen .accordionItem').slideUp(500).promise().done(function() {

      jQuery('html, body').animate({
        scrollTop: accParent.offset().top
      }, 500);

      jQuery('.accordionOpen').removeClass('accordionOpen');

      accParent.children('.accordionItem').slideDown(500);
	  accParent.addClass('accordionOpen').find(".triangle").removeClass("tridwn").addClass("triup");

    });  }
		return false;
});


jQuery(".showall").click(function(){
	jQuery(".accordionItem").slideDown().parent().addClass('accordionOpen');
	jQuery(".triangle").removeClass('tridwn').addClass("triup");
	jQuery(".quiz .info").slideDown();
	jQuery(".infoDetails").slideDown();
});
jQuery('.quiz .info').hide();
jQuery('.quiz > li > a').click(function(e) {
	e.preventDefault();
	quizItem = jQuery(this).parent();

	if (quizItem.hasClass('wrong')) {
		quizItem.addClass('showWrong');
	}
	else {
		quizItem.addClass('showRight').children('.info').slideToggle();
	}

})
jQuery('.moreInfo .infoDetails').hide();
jQuery('.moreInfo .infoSummary').click(function(e) {
	e.preventDefault();
	moreLink = jQuery(this);
	moreParent = jQuery(this).parent('li');

  if (moreParent.hasClass('moreOpen')) {
	jQuery('.infoDetails').slideUp(500);
	moreParent.removeClass('moreOpen');

  } else {
		jQuery('.infoDetails').slideUp(500).promise().done(function() {

      jQuery('html, body').animate({
        scrollTop: moreParent.offset().top
      }, 500);

			jQuery('.moreOpen').removeClass('moreOpen');

      moreParent.addClass('moreOpen').children('.infoDetails').slideDown(500);

    });  }
		return false;

})

</script>
