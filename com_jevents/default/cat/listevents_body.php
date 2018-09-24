<?php
/**
 *
 * Aenderungen
 * J.Mueller OWS, 05.2015
 * 3. Tabellenspalte für Veranstalter eingefuegt.
 * Anzeige angepasst, Layout angepasst
 */

defined('_JEXEC') or die('Restricted access');
$cfg	 = JEVConfig::getInstance();
$data = $this->datamodel->getCatData( $this->catids,$cfg->get('com_showrepeats',0), $this->limit, $this->limitstart);
$this->data = $data;
$Itemid = JEVHelper::getItemid();

/* J.Mueller OWS, 05.2015 auskommentiert
?>
<div class="jev_catselect" ><?php echo $data['catname']; $this->viewNavCatText( $this->catids, JEV_COM_COMPONENT, 'cat.listevents', $this->Itemid );?></div><?php
 */

if (strlen($data['catdesc'])>0){
	echo "<div class='jev_catdesc'>".$data['catdesc']."</div>";
}
?>
<table align="center" width="98%" cellspacing="0" cellpadding="5" class="ev_table">
	<thead>
		<tr style="height: 3em">
			<th id="el_date" class="sectiontableheader">
				Termin
			</th>
			<th id="el_title" class="sectiontableheader">
				Veranstaltung
			</th>
			<th id="el_location" class="sectiontableheader">
				Veranstalter
			</th>
		</tr>
	</thead>
	<tbody>
<?php
$num_events = count($data['rows']);
$chdate ="";
if( $num_events > 0 ){
	for( $r = 0; $r < $num_events; $r++ ){
		$row = $data['rows'][$r];

		$event_day_month_year 	= $row->dup() . $row->mup() . $row->yup();

		if($event_day_month_year <> $chdate) {
			echo '<tr class="ev_tr">';
		} else {
			echo '<tr>';
		}
		echo '<td class="ev_td_left">';
		if( $event_day_month_year <> $chdate ) {
			$date = JEventsHTML::getDateFormat($row->yup(), $row->mup(), $row->dup(), 4);
			// J.Mueller OWS, 05.2015 Anzeige angepasst
			echo '<strong>'.$date.'</strong><br/>';
			if (($row->hup() <> 0) || ($row->minup() <> 0)) {
				$evtime = JEVHelper::getTime($row->getUnixStartTime(), $row->hup(), $row->minup());
				echo $evtime . ' Uhr<br/>';
			}
			$event_end_day_month_year = $row->ddn() . $row->mdn() . $row->ydn();
			if(($event_end_day_month_year <> $event_day_month_year) &&
					($event_end_day_month_year <> '')) {
				$date =JEventsHTML::getDateFormat( $row->ydn(), $row->mdn(), $row->ddn(), 4 );
				echo '<strong>'.$date.'</strong><br/>';
			}
			if (($row->hdn() <> 23) || ($row->mindn() <> 59)) {
				$evtime = JEVHelper::getTime($row->getUnixStartTime(), $row->hdn(), $row->mindn());
				echo $evtime . ' Uhr<br/>';
			}
		}
		echo '</td>';

		echo '<td align="left" valign="top" class="ev_td_center">';
		if ('' <> $row->title()) {
			$rowlink = $row->viewDetailLink($row->yup(),$row->mup(),$row->dup(),false);
			$rowlink = JRoute::_($rowlink . $this->datamodel->getCatidsOutLink());
			echo '<a class="ev_link_row" title="' . $row->title()
					. '" style="color:inherit" view_detail="" href="'
					. $rowlink . '">' . $row->title() . '</a>';
		}
		// J.Mueller OWS 05.2015, 3. Spalte
		$veranstalter = $row->location();
		echo '<td class="ev_td_right">'.$veranstalter.'</td>';

		echo '</tr>';

		// $chdate = $event_day_month_year;
	}
} else {
	echo '<tr>';
	echo '<td align="left" valign="top" class="ev_td_right  jev_noresults">' . "\n";

	if( count($this->catids)==0 || $data['catname']==""){
		echo JText::_('JEV_EVENT_CHOOSE_CATEG') . '</td>';
	} else {
		echo JText::_('JEV_NO_EVENTFOR') . '&nbsp;<b>' . $data['catname']. '</b></td>';
	}
	echo '</tr>';
}
?>
	</tbody></table><br />
<br /><br />
<?php

// Create the pagination object
if ($data["total"]>$data["limit"]){
	$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
}
