<?php
/**
 * copyright (C) 2008-2015 GWE Systems Ltd - All rights reserved
 * 
 * Aenderungen
 * J.Mueller OWS 05.2015
 * Tooltip neu gestaltet.
 * Tastatursteuerung mit Enter bei Monatswahl ermöglichen.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the component frontend
 *
 * @static
 */
class DefaultModCalView
{
	var $_modid = null;

	/* parameters form module or component */
	var $displayLastMonth		= null;
	var $disp_lastMonthDays		= null;
	var $disp_lastMonth			= null;

	var $displayNextMonth		= null;
	var $disp_nextMonthDays		= null;
	var $disp_nextMonth			= null;

	var $linkCloaking			= null;

	/* component only parameter */
	var $com_starday			= null;

	/* module only parameters */
	var $inc_ec_css				= null;
	var $minical_showlink		= null;
	var $minical_prevyear		= null;
	var $minical_prevmonth		= null;
	var $minical_actmonth		= null;
	var $minical_actyear		= null;
	var $minical_nextmonth		= null;
	var $minical_nextyear		= null;

	/* class variables */
	var $catidsOut				= null;
	var $modcatids				= null;
	var $catidList				= "";
	var $aid					= null;
	var $lang					= null;
	var $myItemid				= 0;
	var $cat 					= "";

	/* modules parameter object */
	var $modparams				= null;

	// data model for module
	var $datamodel				= null;

	// flag to say if we want to load tooltips
	protected $hasTooltips	 = false;

	function DefaultModCalView($params, $modid){
		if (JFile::exists(JPATH_SITE . "/components/com_jevents/assets/css/jevcustom.css"))
		{
			$document = JFactory::getDocument();
			JHTML::stylesheet( "components/com_jevents/assets/css/jevcustom.css");
		}

		$this->_modid = $modid;

		$user = JFactory::getUser();

		$cfg = JEVConfig::getInstance();
		$jev_component_name  = JEV_COM_COMPONENT;
		$db	= JFactory::getDBO();

		$this->datamodel = new JEventsDataModel();

		// component config object
		$jevents_config		= JEVConfig::getInstance();

		$this->modparams	= & $params;
		$this->aid			= isset($user->aid) ? $user->aid : 0;
		$tmplang			= JFactory::getLanguage();

		// get params exclusive to module
		$this->inc_ec_css			= $this->modparams->get('inc_ec_css', 1);
		$this->minical_showlink		= $this->modparams->get('minical_showlink', 1);;
		$this->minical_prevyear		= $this->modparams->get('minical_prevyear', 1);;
		$this->minical_prevmonth	= $this->modparams->get('minical_prevmonth', 1);;
		$this->minical_actmonth		= $this->modparams->get('minical_actmonth', 1);;
		$this->minical_actmonth		= $this->modparams->get('minical_actmonth', 1);;
		$this->minical_actyear		= $this->modparams->get('minical_actyear', 1);;
		$this->minical_nextmonth	= $this->modparams->get('minical_nextmonth', 1);;
		$this->minical_nextyear		= $this->modparams->get('minical_nextyear', 1);;

		// get params exclusive to component
		$this->com_starday	= intval($jevents_config->get('com_starday',0));

		// make config object (module or component) current
		if (intval($this->modparams->get('modcal_useLocalParam',  0)) == 1) {
			$myparam	= & $this->modparams;
		} else {
			$myparam	= & $jevents_config;
		}

		// get com_event config parameters for this module
		$this->displayLastMonth		= $myparam->get('modcal_DispLastMonth', 'NO');
		$this->disp_lastMonthDays	= $myparam->get('modcal_DispLastMonthDays', 0);
		$this->linkCloaking			= $myparam->get('modcal_LinkCloaking', 0);

		$t_datenow = JEVHelper::getNow();
		$this->timeWithOffset = $t_datenow->toUnix(true);

		switch($this->displayLastMonth) {
			case 'YES_stop':
				$this->disp_lastMonth = 1;
				break;
			case 'YES_stop_events':
				$this->disp_lastMonth = 2;
				break;
			case 'ALWAYS':
				$this->disp_lastMonthDays = 0;
				$this->disp_lastMonth = 1;
				break;
			case 'ALWAYS_events':
				$this->disp_lastMonthDays = 0;
				$this->disp_lastMonth = 2;
				break;
			case 'NO':
			default:
				$this->disp_lastMonthDays = 0;
				$this->disp_lastMonth = 0;
				break;
		}

		$this->displayNextMonth		= $myparam->get('modcal_DispNextMonth', 'NO');
		$this->disp_nextMonthDays	= $myparam->get('modcal_DispNextMonthDays', 0);

		switch($this->displayNextMonth) {
			case 'YES_stop':
				$this->disp_nextMonth = 1;
				break;
			case 'YES_stop_events':
				$this->disp_nextMonth = 2;
				break;
			case 'ALWAYS':
				$this->disp_nextMonthDays = 0;
				$this->disp_nextMonth = 1;
				break;
			case 'ALWAYS_events':
				$this->disp_nextMonthDays = 0;
				$this->disp_nextMonth = 2;
				break;
			case 'NO':
			default:
				$this->disp_nextMonthDays = 0;
				$this->disp_nextMonth = 0;
				break;
		}

		// find appropriate Itemid and setup catids for datamodel
		$this->myItemid = $this->datamodel->setupModuleCatids($this->modparams);

		$this->cat = $this->datamodel->getCatidsOutLink(true);

		$this->linkpref = 'index.php?option='.$jev_component_name.'&Itemid='.$this->myItemid.$this->cat.'&task=';

	}

	function getTheme(){
		$modtheme = $this->modparams->get("com_calViewName", "flat");
		if ($modtheme == "" || $modtheme == "global")
		{
			$modtheme = JEV_CommonFunctions::getJEventsViewName();
		}

		return $modtheme;
	}

	/**
	 * Cloaks html link whith javascript
	 *
	 * @param string The cloaking URL
	 * @param string The link text
	 * @return string HTML
	 */
	protected function htmlLinkCloaking($url='', $text='', $attribs=array()) {

		$link = JRoute::_($url);

		if ($this->linkCloaking) {
			$attribs['onclick'] = 'window.location.href=\''. $link . '\';return false;';
			$href = '"#"';
		} else {
			$href = '"' . $link . '"';
		}

		$attrstr = '';
		foreach ($attribs as $key => $value) {
			$attrstr .= ' '.$key.' = "'.$value.'"';
		}

		return '<a href=' . $href . $attrstr . '>' . $text . '</a>';

	}

	function _navigationJS($modid){
		static $included = false;
		if ($included) return;
		$included = true;
		$viewname = $this->getTheme();
		if (file_exists(JPATH_SITE."/modules/mod_jevents_cal/tmpl/$viewname/assets/js/calnav.js")){
			JEVHelper::script("modules/mod_jevents_cal/tmpl/$viewname/assets/js/calnav.js");
		}
		else {
			JEVHelper::script("modules/mod_jevents_cal/tmpl/default/assets/js/calnav.js");
		}
	}

	function monthYearNavigation($cal_today,$adj,$symbol, $label,$action="month.calendar"){
		$cfg = JEVConfig::getInstance();
		$jev_component_name  = JEV_COM_COMPONENT;
		$adjDate = JevDate::strtotime($adj,$cal_today);
		list($year,$month) = explode(":",JevDate::strftime("%Y:%m",$adjDate));
		$link = JRoute::_($this->linkpref.$action."&day=1&month=$month&year=$year".$this->cat);

		$content ="";
		if (isset($this->_modid) && $this->_modid>0){
			$this->_navigationJS($this->_modid);
			$link = htmlentities(JURI::base()  . "index.php?option=$jev_component_name&task=modcal.ajax&day=1&month=$month&year=$year&modid=$this->_modid&tmpl=component".$this->cat);
			$content = '<td>';
			// J.Mueller OWS 05.2015, Tastatursteuerung mit Enter bei Monatswahl ermöglichen
//			$content .= '<a href="#jevents" class="mod_events_link" onmousedown="callNavigation(\''.$link.'\');" onkeypress="callNavigation2(event,this,\''.$link.'\');">'.$symbol."</a>\n";
			$content .= '<a href="javascript:callNavigation(\''.$link.'\');" class="mod_events_link">'.$symbol."</a>\n";
			$content .= '</td>';
		}
		return $content;
	}

	function _displayCalendarMod($time, $startday, $linkString, &$day_name, $monthMustHaveEvent=false, $basedate=false){

		$db	= JFactory::getDBO();
		$cfg = JEVConfig::getInstance();
		$option = JEV_COM_COMPONENT;

		$cal_year=date("Y",$time);
		$cal_month=date("m",$time);
		// do not use $cal_day since it's not reliable due to month offset calculation
		//$cal_day=date("d",$time);
		
		if (!$basedate) $basedate=$time;
		$base_year = date("Y",$basedate);
		$base_month = date("m",$basedate);
		$basefirst_of_month   = JevDate::mktime(0,0,0,$base_month, 1, $base_year);

		$requestYear = JRequest::getInt("year",0);
		$requestMonth = JRequest::getInt("month",0);
		// special case when site link set the dates for the mini-calendar in the URL but not in the ajax request
		if ($requestMonth && $requestYear && JRequest::getString("task","")!="modcal.ajax"  && $this->modparams->get("minical_usedate",0)){
			$requestDay = JRequest::getInt("day",1);

			$requestTime = JevDate::mktime(0,0,0,$requestMonth, $requestDay, $requestYear);
			if ($time-$basedate > 100000) $requestTime = JevDate::strtotime("+1 month",$requestTime);
			else if ($time-$basedate < -100000) $requestTime = JevDate::strtotime("-1 month",$requestTime);
	
			$cal_year = date("Y",$requestTime);
			$cal_month = date("m",$requestTime);

			$base_year = $requestYear;
			$base_month = $requestMonth;
			$basefirst_of_month   = JevDate::mktime(0,0,0,$requestMonth, $requestDay, $requestYear);
		}
		else {
			$cal_year=date("Y",$time);
			$cal_month=date("m",$time);
		}		

		$reg = JFactory::getConfig();
		$reg->set("jev.modparams",$this->modparams);
		if ($this->modparams->get("showtooltips",0)) {
			$data = $this->datamodel->getCalendarData($cal_year,$cal_month,1,false, false);
			$this->hasTooltips	 = true;
		}
		else {
			$data = $this->datamodel->getCalendarData($cal_year,$cal_month,1,true, $this->modparams->get("noeventcheck",0));
		}
		// J.Mueller OWS 05.2015, Tooltip neu gestaltet
//		$data2 = $this->datamodel->getCatData( $this->catids,$cfg->get('com_showrepeats',0), $this->limit, $this->limitstart);
		$data2 = $this->datamodel->getCatData( $this->catids,$cfg->get('com_showrepeats',0), 1000, 0);
		$rows2 = $data2['rows'];
		$totalnum_events = count($rows2);
		$reg->set("jev.modparams",false);
                $width = $this->modparams->get("mod_cal_width","");
                $height = $this->modparams->get("mod_cal_height","");
		

		$month_name = JEVHelper::getMonthName($cal_month);
		$first_of_month = JevDate::mktime(0,0,0,$cal_month, 1, $cal_year);
		//$today = JevDate::mktime(0,0,0,$cal_month, $cal_day, $cal_year);
		$today = JevDate::strtotime(date('Y-m-d', $this->timeWithOffset));

		$content    = '';
		
		if( $this->minical_showlink ){

			$content .= "\n".'<h3>' . "\n";

			if( $this->minical_showlink == 1 ){
				if( $this->minical_actmonth == 2 ){
					// combination of actual month and year: view month
					$seflinkActMonth = JRoute::_( $this->linkpref.'month.calendar&month='.$cal_month.'&year='.$cal_year);

					$content .= '<span class="evtq_home">';
					$content .= $month_name . "\n";
					$seflinkActYear = JRoute::_( $this->linkpref . 'year.listevents' . '&month=' . $cal_month
					. '&year=' . $cal_year );

					$content .= $cal_year . "\n";
					$content .= '</span>';
				}
				$content .= '<span class="evtq_nav">';
				if( $this->minical_prevyear ){
					$content .= $this->monthYearNavigation($basefirst_of_month,"-1 year",'&laquo;',JText::_('JEV_CLICK_TOSWITCH_PY'));
				}				
				if( $this->minical_prevmonth ){
					$content .= $this->monthYearNavigation($basefirst_of_month,"-1 month",'&lt;&lt;',JText::_('JEV_CLICK_TOSWITCH_PM'));
				}
				$content .= '&nbsp;|&nbsp;';
				if( $this->minical_nextmonth ){
					$content .= $this->monthYearNavigation($basefirst_of_month,"+1 month",'&gt;&gt;',JText::_('JEV_CLICK_TOSWITCH_NM'));
				}
				if( $this->minical_nextyear ){
					$content .= $this->monthYearNavigation($basefirst_of_month,"+1 year",'&raquo;',JText::_('JEV_CLICK_TOSWITCH_NY'));
				}
				$content .= '</span>';
				// combination of actual month and year: view year & month [ mic: not used here ]
				// $seflinkActYM   = JRoute::_( $link . 'month.calendar' . '&month=' . $cal_month
				// . '&year=' . $cal_year );
			}else{
				// show only text
				$content .= '<td>';
				$content .= $month_name . ' ' . $cal_year;
				$content .= '</td>';
			}

			$content .= "</h3>\n";
		}
		$lf = "\n";



		$content .= '<table align="center" class="mod_events_table" cellspacing="0" cellpadding="2" >'.$lf
			. '<tr class="mod_events_dayname">'.$lf;

		// Days name rows
		for ($i=0;$i<7;$i++) {
			$content.="<th class=\"mod_events_td_dayname\">".$day_name[($i+$startday)%7]."</th>".$lf	;
		}

		$content .='</tr>'.$lf;

		$datacount = count($data["dates"]);
		$dn=0;
		for ($w=0;$w<6 && $dn<$datacount;$w++){
			$content .="<tr>\n";
			/*
			echo "<td width='2%' class='cal_td_weeklink'>";
			list($week,$link) = each($data['weeks']);
			echo "<a href='".$link."'>$week</a></td>\n";
			*/
			for ($d=0;$d<7 && $dn<$datacount;$d++){
				$currentDay = $data["dates"][$dn];
				switch ($currentDay["monthType"]){
					case "prior":
					case "following":
						$content .= "<td class='mod_events_td_dayoutofmonth'></td>\n";
						break;
					case "current":
						if ($currentDay["events"] || $this->modparams->get("noeventcheck",0)){
							$class = ($currentDay["cellDate"] == $today) ? "mod_events_td_todaywithevents" : "mod_events_td_daywithevents";
						}
						else {
							$class = ($currentDay["cellDate"] == $today) ? "mod_events_td_todaynoevents" : "mod_events_td_daynoevents";
						}
						$content .= "<td class='".$class."'>\n";

						// J.Mueller OWS 05.2015, Tooltip neu gestaltet
						$title = '';
						if ($currentDay["events"] && (0 < $totalnum_events)) {
							$num_events = 0;
							$link = '';
							for( $r = 0; $r < $totalnum_events; $r++ ) {
								$row2 = $rows2[$r];
								$datetime1 = new DateTime();
								$datetime1->setDate($row2->yup(), $row2->mup(), $row2->dup());
								$datetime2 = new DateTime(); 
								$datetime2->setDate($row2->ydn(), $row2->mdn(), $row2->ddn());
								$datetime3 = new DateTime(); 
								$datetime3->setDate($currentDay['year'], $currentDay['month'], $currentDay['d']);
								if (($datetime1 <= $datetime3) && ($datetime3 <= $datetime2)) {
									$title .= $row2->title() . "<br/>";
									$num_events = $num_events + 1;
									if (1 == $num_events) {
										$link = $row2->viewDetailLink($row2->yup(),$row2->mup(),$row2->dup(),false);
										$link = JRoute::_($link . $this->datamodel->getCatidsOutLink());
										$link = str_ireplace('?tmpl=component', '', $link);
									}
								}
							}
							if (0 < $num_events) {
								$title = '</p>' . $title;
								if (1 < $num_events) {
									$title = '<p>' . $num_events . ' Veranstaltungen' . $title;
								} else {
									$title = '<p>1 Veranstaltung' . $title;
								}
								$link = $currentDay["link"];
							}
						}
								
						$tooltip = $this->getTooltip($currentDay, array('class'=>"mod_events_daylink"));
						if ($tooltip) {
							$content .= $tooltip;
						} else {
							if ($this->modparams->get("emptydaylinks", 1) 
									|| $currentDay["events"] 
									|| $this->modparams->get("noeventcheck",0)) {
								// J.Mueller OWS 05.2015, Tooltip neu gestaltet
								$content .= $this->htmlLinkCloaking($link, $currentDay['d'] 
										. '<div align="left">' . $title . '</div>', 
										array('class'=>"mod_events_daylink",'title'=> ''));
							} else {
								$content .= $currentDay['d'];
							}
						}
						$content .="</td>\n";

						break;
				}
				$dn++;
			}
			$content .= "</tr>\n";
		}

		$content .= '</table>'.$lf;
		$content .= '<p>Bitte klicken Sie auf das gewünschte Datum</p>';

		return $content;
	}

	function getCal($modid=0) {
		// capture module id so that we can use it for ajax type navigation
		if ($modid!=0) {
			$this->_modid=$modid;
		}
		$user = JFactory::getUser();

		$db	= JFactory::getDBO();

		// this will get the viewname based on which classes have been implemented
		$viewname = $this->getTheme();

		$cfg = JEVConfig::getInstance();
		$compname = JEV_COM_COMPONENT;

		$viewpath = "components/".JEV_COM_COMPONENT."/views/".$viewname."/assets/css/";

		// get array
		$day_name = JEVHelper::getWeekdayLetter(null, 1);
		$day_name[0] = '<span class="sunday">' .   $day_name[0] . '</span>';
		$day_name[6] = '<span class="saturday">' . $day_name[6] . '</span>';
    // OWS Wagner
		$content    = '<div class="kalenderbox">';
		
		if ($this->inc_ec_css){
			JEVHelper::componentStylesheet($this,"modstyle.css");
		}

		$thisDayOfMonth = date("j", $this->timeWithOffset);
		$daysLeftInMonth = date("t", $this->timeWithOffset) - date("j", $this->timeWithOffset) + 1;
		// calculate month offset from first of month
		$first_of_current_month = JevDate::strtotime(date('Y-m-01',$this->timeWithOffset));

		$mod ="";
		if (isset($this->_modid) && $this->_modid>0){
			$mod = 'id="modid_'.$this->_modid.'" ';
			$content  .= "<span id='testspan".$this->_modid."' style='display:none'></span>\n";
		}
		
		if($this->disp_lastMonth && (!$this->disp_lastMonthDays || $thisDayOfMonth <= $this->disp_lastMonthDays))
		$content .= $this->_displayCalendarMod(JevDate::strtotime("-1 month", $first_of_current_month),
		$this->com_starday, JText::_('JEV_LAST_MONTH'),	$day_name, $this->disp_lastMonth == 2, $this->timeWithOffset);

		$content .= $this->_displayCalendarMod($this->timeWithOffset,
		$this->com_starday, JText::_('JEV_THIS_MONTH'),$day_name, false, $this->timeWithOffset);

		if($this->disp_nextMonth && (!$this->disp_nextMonthDays || $daysLeftInMonth <= $this->disp_nextMonthDays))
		$content .= $this->_displayCalendarMod(JevDate::strtotime("+1 month", $first_of_current_month),
		$this->com_starday, JText::_('JEV_NEXT_MONTH'),$day_name, $this->disp_nextMonth == 2, $this->timeWithOffset);
		
		$content .= '</div>';
		
		return $content;
	} // function getCal


	function getAjaxCal($modid=0, $month, $year){
		// capture module id so that we can use it for ajax type navigation
		if ($modid!=0) {
			$this->_modid=$modid;
		}
		$user = JFactory::getUser();

		$db	= JFactory::getDBO();

		static $isloaded_css = false;
		// this will get the viewname based on which classes have been implemented
		$cfg = JEVConfig::getInstance();
		$viewname = ucfirst($cfg->get('com_calViewName',"default"));

		$cfg = JEVConfig::getInstance();

		// get array
		$day_name = JEVHelper::getWeekdayLetter(null, 1);
		$day_name[0] = '<span class="sunday">' .   $day_name[0] . '</span>';
		$day_name[6] = '<span class="saturday">' . $day_name[6] . '</span>';

		$content="";
		$mod ="";
		if (isset($this->_modid) && $this->_modid>0){
			$mod = 'id="modid_'.$this->_modid.'" ';
			$content  .= "<span id='testspan".$this->_modid."' style='display:none'></span>\n";
		}

		
		$temptime = JevDate::mktime(12,0,0,$month,15,$year);

		//$content .= $this->_displayCalendarMod($temptime,$this->com_starday, JText::_('JEV_THIS_MONTH'),$day_name, false);

		$thisDayOfMonth = date("j", $temptime);
		$daysLeftInMonth = date("t", $temptime) - date("j",$temptime) + 1;
		// calculate month offset from first of month
		$first_of_current_month = JevDate::strtotime(date('Y-m-01',$temptime));

		$base_year = date("Y",$temptime);
		$base_month = date("m",$temptime);
		$basefirst_of_month   = JevDate::mktime(0,0,0,$base_month, 1, $base_year);
		
		if($this->disp_lastMonth && (!$this->disp_lastMonthDays || $thisDayOfMonth <= $this->disp_lastMonthDays))
		$content .= $this->_displayCalendarMod(JevDate::strtotime("-1 month", $first_of_current_month),
		$this->com_starday, JText::_('JEV_LAST_MONTH'),	$day_name, $this->disp_lastMonth == 2,  $first_of_current_month);

		$content .= $this->_displayCalendarMod($temptime,
		$this->com_starday, JText::_('JEV_THIS_MONTH'),$day_name, false,  $first_of_current_month);

		if($this->disp_nextMonth && (!$this->disp_nextMonthDays || $daysLeftInMonth <= $this->disp_nextMonthDays))
		$content .= $this->_displayCalendarMod(JevDate::strtotime("+1 month", $first_of_current_month),
		$this->com_starday, JText::_('JEV_NEXT_MONTH'),$day_name, $this->disp_nextMonth == 2,  $first_of_current_month);
		
		
		return $content;
	} // function getSpecificCal

	 protected function getTooltip($currentDay, $linkattr) {
		return "";
	 }
}
