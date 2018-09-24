<?php
/**
 * 
 * Aenderungen
 * J.Mueller OWS, 05.2015
 * 3. Tabellenspalte für Veranstalter eingefuegt. 
 * Anzeige angepasst, Layout angepasst
 */

defined('_JEXEC') or die('Restricted access');

$cfg = JEVConfig::getInstance();

$this->data = $data = $this->datamodel->getDayData($this->year, $this->month, $this->day);

$this->Redirectdetail();

$cfg = JEVConfig::getInstance();
$Itemid = JEVHelper::getItemid();
$hasevents = false;

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
// Timeless Events First
	if (count($data['hours']['timeless']['events']) > 0)
	{
		$chtime = '';
		$start_time = JText::_('TIMELESS');
		$hasevents = true;

		foreach ($data['hours']['timeless']['events'] as $row) {
			// J.Mueller OWS 05.2015, Anzeige angepasst, Layout angepasst
			if($start_time <> $chtime) {
				echo '<tr class="ev_tr">';
			} else {
				echo '<tr>';
			}
			echo '<td class="ev_td_left">';
			if($start_time <> $chtime) {
				echo $start_time;
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

			$chtime = $start_time;
		}
	}

	for ($h = 0; $h < 24; $h++)
	{
		if (count($data['hours'][$h]['events']) > 0)
		{
			$chtime = '';
			$start_time = JEVHelper::getTime($data['hours'][$h]['hour_start']);
			$hasevents = true;

			foreach ($data['hours'][$h]['events'] as $row) {
				// J.Mueller OWS 05.2015, Anzeige angepasst, Layout angepasst
				if($start_time <> $chtime) {
					echo '<tr class="ev_tr">';
				} else {
					echo '<tr>';
				}
				echo '<td class="ev_td_left">';
				if($start_time <> $chtime) {
					echo $start_time . ' Uhr';
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

				$chtime = $start_time;
			}
		}
	}
	if (!$hasevents)
	{
		echo '<tr><td class="ev_td_right" colspan="3"><ul class="ev_ul" >' . "\n";
		echo "<li class='ev_td_li ev_td_li_noevents' >\n";
		echo JText::_('JEV_NO_EVENTS');
		echo "</li>\n";
		echo "</ul></td></tr>\n";
	}
	echo '</tbody></table><br />' . "\n";
	echo '</fieldset><br /><br />' . "\n";
//  $this->showNavTableText(10, 10, $num_events, $offset, '');

