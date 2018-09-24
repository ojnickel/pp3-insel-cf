<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Aenderung Josef Mueller OWS:
 * 20140129
 * 20150417
 * Fuer Kontakte wurde ein Filter für spezifische Unternehmenskontakte eingefügt
 *
 * 20150703
 * Evtl. Korrektur des Links auf einen Kontakt
 */

defined( '_JEXEC' ) or die;
?>
<?php if ( ! empty( $this->searchword ) ) : ?>
<div class="searchintro<?php echo $this->escape( $this->params->get( 'pageclass_sfx' ) ); ?>">
	<p>
		<?php echo JText::_( 'COM_SEARCH_SEARCH_KEYWORD' ); ?> <strong><?php echo $this->escape( $this->searchword ); ?></strong>
		<?php echo $this->result; ?>
	</p>

</div>
<?php endif; ?>

<?php if ( count( $this->results ) ) : ?>
<div class="results">
	<h3><?php echo JText::_( 'COM_SEARCH_SEARCH_RESULT' ); ?></h3>
<ul class="list<?php echo $this->pageclass_sfx; ?>">
<?php
foreach ( $this->results as $result ) :
		// OWS Josef Mueller, Filter für spezifische Unternehmenskontakte
		$section = $this->escape( $result->section );
	if ( '' != stristr( $section, 'Kontakte' ) ) {  // Josef Mueller OWS, Filter für spezifische Unternehmenskontakte
		$server_name = $_SERVER['SERVER_NAME'];
		$unternehmen = strtolower( stristr( $server_name, '-pfennigparade.de', true ) );
		$pos         = strrpos( $unternehmen, '.' );
		if ( ! ( false === $pos ) ) {
			$unternehmen = substr( $unternehmen, $pos + 1 );
		}
		switch ( $unternehmen ) {
			case 'sigmeta':
				$unternehmen = 'sig';
				break;
			case 'werkstatt':
				$unternehmen = 'wkm';
				break;
		}
		if ( ( '' == stristr( $section, 'Dienstleistungen und Produkte' ) ) || ! stripos( $result->text, $unternehmen, 1 ) ) {
			if ( $unternehmen ) {
				if ( 0 != strcasecmp( $unternehmen, stristr( $section, $unternehmen ) ) ) {
					continue;
				}
			}
		}
	}
	?>
<li>
	<h4>
		<?php
		if ( $result->href ) :
			// OWS Josef Mueller, evtl. Korrektur des Links auf einen Kontakt
			$needle = 'component/contact/contact';
			$href   = JRoute::_( $result->href );
			if ( false !== stripos( $href, $needle ) ) {
				$href = str_ireplace( '?Itemid=338', '?Itemid=213', str_ireplace( $needle, 'kontakte', $href ) );
			}
			?>
			<a href="<?php echo $href; ?>"
								<?php
								if ( $result->browsernav == 1 ) :
						?>
					  target="_blank"<?php endif; ?>>
				<?php // echo $this->escape($result->title); ?>
				<?php echo $result->title; ?>
			</a>
		<?php else : ?>
			<?php echo $this->escape( $result->title ); ?>
		<?php endif; ?>
	</h4>
	<?php if ( $result->section ) : ?>
		<p>
			<span class="small<?php echo $this->pageclass_sfx; ?>">
				<?php echo $this->escape( $result->section ); ?>
			</span>
		</p>
	<?php endif; ?>
	<?php echo $result->text; ?>

	<?php if ( $this->params->get( 'show_date' ) ) : ?>
		<span class="small<?php echo $this->pageclass_sfx; ?>">
			<?php echo $this->escape( $result->created ); ?>
		</span>
	<?php endif; ?>
</li>
<?php endforeach; ?>
</ul>

	<?php echo $this->pagination->getPagesLinks(); ?>

</div>
<?php endif; ?>
