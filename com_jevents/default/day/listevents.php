<?php 
/**
 * 
 * Aenderungen
 * J.Mueller OWS, 05.2015
 * Überschrift angepasst
 */

defined('_JEXEC') or die('Restricted access');

// J.Mueller OWS, 05.2015, Überschrift angepasst
// $this->_header();
?>
<div id="jevents">
<div class="contentpaneopen jeventpage jevbootstrap" id="jevents_header">
<?php	
echo '<h2 class="contentheading" >Veranstaltungen am ' 
		. JEventsHTML::getDateFormat($this->year, $this->month, $this->day, 4) 
		. '</h2>';
?>
</div>
<div class="contentpaneopen  jeventpage jevbootstrap" id="jevents_body">
<?php	

$this->_showNavTableBar();

echo $this->loadTemplate("body");

$this->_viewNavAdminPanel();

$this->_footer();


