<?php
/**
 * copyright (C) 2008-2017 GWE Systems Ltd - All rights reserved
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the module  frontend
 *
 * @static
 */
include_once(JPATH_SITE."/modules/mod_jevents_latest/tmpl/default/latest.php");

class OverrideDefaultModLatestView extends DefaultModLatestView
{
	function displayLatestEvents(){

		// this will get the viewname based on which classes have been implemented
		$viewname = $this->getTheme();

		$cfg = JEVConfig::getInstance();
		$compname = JEV_COM_COMPONENT;

		$viewpath = "components/".JEV_COM_COMPONENT."/views/".$viewname."/assets/css/";

		$dispatcher	= JEventDispatcher::getInstance();
		$datenow	= JEVHelper::getNow();

		$this->getLatestEventsData();

		$content = "";

		if(isset($this->eventsByRelDay) && count($this->eventsByRelDay)){
			$content .= $this->modparams->get("modlatest_templatetop") || $this->modparams->get("modlatest_templatebottom") ? $this->modparams->get("modlatest_templatetop") : '<div class="latest_events">';

			// Now to display these events, we just start at the smallest index of the $this->eventsByRelDay array
			// and work our way up.

			$firstTime=true;

			// initialize name of com_jevents module and task defined to view
			// event detail.  Note that these could change in future com_event
			// component revisions!!  Note that the '$this->itemId' can be left out in
			// the link parameters for event details below since the event.php
			// component handler will fetch its own id from the db menu table
			// anyways as far as I understand it.

			$this->processFormatString();

			foreach($this->eventsByRelDay as $relDay => $daysEvents){

				reset($daysEvents);

				// get all of the events for this day
				foreach($daysEvents as $dayEvent){

					$eventcontent = "";

					// generate output according custom string
					foreach($this->splitCustomFormat as $condtoken) {

						if (isset($condtoken['cond'])) {
							if ( $condtoken['cond'] == 'a'  && !$dayEvent->alldayevent()) continue;
							else if ( $condtoken['cond'] == '!a' &&  $dayEvent->alldayevent()) continue;
							else if ( $condtoken['cond'] == 'e'  && !($dayEvent->noendtime() || $dayEvent->alldayevent())) continue;
							else if ( $condtoken['cond'] == '!e' &&  ($dayEvent->noendtime() || $dayEvent->alldayevent())) continue;
							else if ( $condtoken['cond'] == '!m' &&  $dayEvent->getUnixStartDate()!=$dayEvent->getUnixEndDate() ) continue;
							else if ( $condtoken['cond'] == 'm' &&  $dayEvent->getUnixStartDate()==$dayEvent->getUnixEndDate() ) continue;
						}
						foreach($condtoken['data'] as $token) {
							unset($match);
							unset($dateParm);
							$dateParm="";
							$match='';
							if (is_array($token)) {
								$match = $token['keyword'];
								$dateParm = isset($token['dateParm']) ? trim($token['dateParm']) : "";
							}
							else if (strpos($token,'${')!==false){
								$match = $token;
							}
							else {
								$eventcontent .= $token;
								continue;
							}

							$this->processMatch($eventcontent, $match, $dayEvent, $dateParm,$relDay);

						} // end of foreach
					} // end of foreach

					$eventrow = '<div class="event">%s'."</div>\n";

					$templaterow = $this->modparams->get("modlatest_templaterow") ? $this->modparams->get("modlatest_templaterow")  : $eventrow;
					$content .= str_replace("%s", $eventcontent , $templaterow);

					$firstTime=false;
				} // end of foreach
			} // end of foreach
			$content .=$this->modparams->get("modlatest_templatebottom") || $this->modparams->get("modlatest_templatetop") ? $this->modparams->get("modlatest_templatebottom") : "</div>\n";

		}
		else if ($this->modparams->get("modlatest_NoEvents", 1)){
			$content .= $this->modparams->get("modlatest_templatetop") ? $this->modparams->get("modlatest_templatetop") : '<div class="latest_events">';
			$templaterow = $this->modparams->get("modlatest_templaterow") ? $this->modparams->get("modlatest_templaterow")  : '<div class="noevents">%s</div>' . "\n";
			$content .= str_replace("%s", JText::_('JEV_NO_EVENTS') , $templaterow);
			$content .=$this->modparams->get("modlatest_templatebottom") ? $this->modparams->get("modlatest_templatebottom") : "</div>\n";
		}

		$callink_HTML = '<div class="calendarlink">'
		.$this->getCalendarLink()
		. '</div>';

		if ($this->linkToCal == 1) $content = $callink_HTML . $content;
		if ($this->linkToCal == 2) $content .= $callink_HTML;

		if ($this->displayRSS){
			$rssimg = JURI::root() . "media/system/images/livemarks.png";

			$callink_HTML = '<div class="mod_events_latest_rsslink">'
			.'<a href="'.$this->rsslink.'" title="'.JText::_("RSS_FEED").'"  target="_blank">'
			.'<img src="'.$rssimg.'" alt="'.JText::_("RSS_FEED").'" />'
			.JText::_("SUBSCRIBE_TO_RSS_FEED")
			. '</a>'
			. '</div>';
			$content .= $callink_HTML;
		}

		if ($this->modparams->get("contentplugins", 0)){
			$dispatcher = JEventDispatcher::getInstance();
			$eventdata = new stdClass();
			//$eventdata->text = str_replace("{/toggle","{/toggle}",$content);
			$eventdata->text = $content;
			$dispatcher->trigger('onContentPrepare', array('com_jevents', &$eventdata, &$this->modparams, 0));
			 $content = $eventdata->text;
		}

		return $content;
	} // end of function

	protected
			function getCalendarLink()
	{
		$menu =  JFactory::getApplication()->getMenu('site');
		$menuItem = $menu->getItem($this->myItemid);
		if ($menuItem && $menuItem->component == JEV_COM_COMPONENT)
		{
			$viewlayout = isset($menuItem->query["view"]) ? ($menuItem->query["view"] . "." . $menuItem->query["layout"]) : "calendar.month";
			$task = isset($menuItem->query["task"]) ? $menuItem->query["task"] : ($menuItem->query["view"] . "." . $menuItem->query["layout"]);
		}
		else
		{
			$task = "month.calendar";
		}
		return $this->_htmlLinkCloaking(JRoute::_("index.php?option=" . JEV_COM_COMPONENT . "&Itemid=" . $this->myItemid . "&task=" . $task . $this->catout, true), 'Alle Veranstaltungen');

	}

	protected
			function processMatch(&$content, $match, $dayEvent, $dateParm, $relDay)
	{
		$datenow = JEVHelper::getNow();
		$dispatcher = JEventDispatcher::getInstance();
		$compname = JEV_COM_COMPONENT;

		// get the title and start time
		$startDate = JevDate::strtotime($dayEvent->publish_up());
		if ($relDay > 0)
		{
			$eventDate = JevDate::strtotime($datenow->toFormat('%Y-%m-%d ') . JevDate::strftime('%H:%M', $startDate) . " +$relDay days");
		}
		else
		{
			$eventDate = JevDate::strtotime($datenow->toFormat('%Y-%m-%d ') . JevDate::strftime('%H:%M', $startDate) . " $relDay days");
		}
		$endDate = JevDate::strtotime($dayEvent->publish_down());

		list($st_year, $st_month, $st_day) = explode('-', JevDate::strftime('%Y-%m-%d', $startDate));
		list($ev_year, $ev_month, $ev_day) = explode('-', JevDate::strftime('%Y-%m-%d', $startDate));

		$task_events = 'icalrepeat.detail';
		switch ($match) {

			case 'endDate':
			case 'startDate':
			case 'eventDate':
				// Note we need to examine the date specifiers used to determine if language translation will be
				// necessary.  Do this later when script is debugged.

				if (!$dayEvent->alldayevent() && $match == "endDate" && (($dayEvent->noendtime() && ($dayEvent->getUnixStartDate() == $dayEvent->getUnixEndDate())) || $dayEvent->getUnixStartTime() == $dayEvent->getUnixEndTime()))
				{
					$time_fmt = "";
				}
				else if (!isset($dateParm) || $dateParm == '')
				{
					if ($this->com_calUseStdTime)
					{
						$time_fmt = $dayEvent->alldayevent() ? '' : IS_WIN ? ' @%I:%M%p' : ' @%l:%M%p';
					}
					else
					{
						$time_fmt = $dayEvent->alldayevent() ? '' : ' @%H:%M';
					}
					$dateFormat = $this->displayYear ? '%a %b %d, %Y' . $time_fmt : '%a %b %d' . $time_fmt;
					$jmatch = new JevDate($$match);
					$content .= $jmatch->toFormat($dateFormat);
					//$content .= JEV_CommonFunctions::jev_strftime($dateFormat, $$match);
				}
				else
				{
					// format endDate when midnight to show midnight!
					if ($match == "endDate" && $dayEvent->sdn() == 59)
					{
						$tempEndDate = $endDate + 1;
						if ($dayEvent->alldayevent() || $dayEvent->noendtime())
						{
							// if an all day event then we don't want to roll to the next day
							$tempEndDate -= 86400;
						}
						$match = "tempEndDate";
					}
					// if a '%' sign detected in date format string, we assume JevDate::strftime() is to be used,
					if (preg_match("/\%/", $dateParm))
					{
						$jmatch = new JevDate($$match);
						$content .= $jmatch->toFormat($dateParm);
					}
					// otherwise the date() function is assumed.
					else
					{
						$content .= date($dateParm, $$match);
					}
					if ($match == "tempEndDate")
					{
						$match = "endDate";
					}
				}

				break;

			case 'title':
				$title = $dayEvent->title();
				if (!empty($dateParm))
				{
					$parts = explode("|", $dateParm);
					if (count($parts) > 0 && JString::strlen($title) > intval($parts[0]))
					{
						$title = JString::substr($title, 0, intval($parts[0]));
						if (count($parts) > 1)
						{
							$title .= $parts[1];
						}
					}
				}

				if ($this->displayLinks)
				{
					$link = $dayEvent->viewDetailLink($ev_year, $ev_month, $ev_day, false, $this->myItemid);
					if ($this->modparams->get("ignorefiltermodule", 0))
					{
						$link = JRoute::_($link . $this->datamodel->getCatidsOutLink() . "&filter_reset=1", false);
					}
					else
					{
						$link = JRoute::_($link . $this->datamodel->getCatidsOutLink());
					}
					$content .= $this->_htmlLinkCloaking($link, JEventsHTML::special($title));
				}
				else
				{
					$content .= JEventsHTML::special($title);
				}

				break;

			case 'category':
				$catobj = $dayEvent->getCategoryName();
				$content .= JEventsHTML::special($catobj);
				break;

			case 'categoryimage':
				$catobj = $dayEvent->getCategoryImage();
				$content .= $catobj;
				break;

			case 'calendar':
				$catobj = $dayEvent->getCalendarName();
				$content .= JEventsHTML::special($catobj);
				break;

			case 'contact':
				// Also want to cloak contact details so
				$this->modparams->set("image", 1);
				$dayEvent->text = $dayEvent->contact_info();
				$dispatcher->trigger('onContentPrepare', array('com_jevents', &$dayEvent, &$this->modparams, 0));

				$dayEvent->contact_info($dayEvent->text);
				$content .= $dayEvent->contact_info();
				break;

				case 'content':  // Added by Kaz McCoy 1-10-2004
					$this->modparams->set("image", 1);
					$dayEvent->data->text = $dayEvent->content();
					$dispatcher->trigger('onContentPrepare', array('com_jevents', &$dayEvent->data, &$this->modparams, 0));

					if (!empty($dateParm))
					{
						$parts = explode("|", $dateParm);
						if (count($parts) > 0 && JString::strlen(strip_tags($dayEvent->data->text)) > intval($parts[0]))
						{
							$dayEvent->data->text = JString::substr(strip_tags($dayEvent->data->text), 0, intval($parts[0]));
							if (count($parts) > 1)
							{
								$dayEvent->data->text .= $parts[1];
							}
						}
					}

					$dayEvent->content($dayEvent->data->text);

					// preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $dayEvent->content(), $contentimage);
          //
					// if ( !empty( $contentimage ) && is_array( $contentimage ) && key_exists( 'src', $contentimage ) ) {
					// 	$content .= '<img src="index.php?option=com_imgen&height=70&format=image&img=' . urlencode($contentimage['src']) . '">';
					// }

					break;

			case 'addressInfo':
			case 'location':
				$this->modparams->set("image", 0);
				$dayEvent->data->text = $dayEvent->location();
				$dispatcher->trigger('onContentPrepare', array('com_jevents', &$dayEvent->data, &$this->modparams, 0));
				$dayEvent->location($dayEvent->data->text);
				$content .= $dayEvent->location();
				break;

			case 'duration':
				$timedelta = ($dayEvent->noendtime() || $dayEvent->alldayevent()) ? "" : $dayEvent->getUnixEndTime() - $dayEvent->getUnixStartTime();
				if ($timedelta == "")
				{
					break;
				}
				$fieldval = (isset($dateParm) && $dateParm != '') ? $dateParm : JText::_("JEV_DURATION_FORMAT");
				$shownsign = false;
				// whole days!
				if (stripos($fieldval, "%wd") !== false)
				{
					$days = intval($timedelta / (60 * 60 * 24));
					$timedelta -= $days * 60 * 60 * 24;

					if ($timedelta > 3610)
					{
						//if more than 1 hour and 10 seconds over a day then round up the day output
						$days +=1;
					}

					$fieldval = str_ireplace("%wd", $days, $fieldval);
					$shownsign = true;
				}
				if (stripos($fieldval, "%d") !== false)
				{
					$days = intval($timedelta / (60 * 60 * 24));
					$timedelta -= $days * 60 * 60 * 24;
					/*
					  if ($timedelta>3610){
					  //if more than 1 hour and 10 seconds over a day then round up the day output
					  $days +=1;
					  }
					 */
					$fieldval = str_ireplace("%d", $days, $fieldval);
					$shownsign = true;
				}
				if (stripos($fieldval, "%h") !== false)
				{
					$hours = intval($timedelta / (60 * 60));
					$timedelta -= $hours * 60 * 60;
					if ($shownsign)
						$hours = abs($hours);
					$hours = sprintf("%02d", $hours);
					$fieldval = str_ireplace("%h", $hours, $fieldval);
					$shownsign = true;
				}
				if (stripos($fieldval, "%k") !== false)
				{
					$hours = intval($timedelta / (60 * 60));
					$timedelta -= $hours * 60 * 60;
					if ($shownsign)
						$hours = abs($hours);
					$fieldval = str_ireplace("%kgi", $hours, $fieldval);
					$shownsign = true;
				}
				if (stripos($fieldval, "%m") !== false)
				{
					$mins = intval($timedelta / 60);
					$timedelta -= $hours * 60;
					if ($mins)
						$mins = abs($mins);
					$mins = sprintf("%02d", $mins);
					$fieldval = str_ireplace("%m", $mins, $fieldval);
				}
				$content .= $fieldval;
				break;

			case 'extraInfo':
				$this->modparams->set("image", 0);
				$dayEvent->data->text = $dayEvent->extra_info();
				$dispatcher->trigger('onContentPrepare', array('com_jevents', &$dayEvent->data, &$this->modparams, 0));
				$dayEvent->extra_info($dayEvent->data->text);
				$content .= $dayEvent->extra_info();
				break;

			case 'countdown':
                                $timedelta = $dayEvent->getUnixStartTime() - JevDate::mktime();
                            	$now = new JevDate("+0 seconds");
				$now = $now->toFormat("%Y-%m-%d %H:%M:%S");

				$eventStarted = $dayEvent->publish_up() < $now ? 1 : 0 ;
				$eventEnded   = $dayEvent->publish_down() < $now ? 1 : 0 ;

				$fieldval = $dateParm;
				$shownsign = false;
				if (stripos($fieldval, "%nopast") !== false)
				{
					if (!$eventStarted)
					{
						$fieldval = str_ireplace("%nopast", "", $fieldval);
					}
                                        else if (!$eventEnded)
                                        {
                                                $fieldval = JText::_('JEV_EVENT_STARTED');
                                        }
					else
					{
						$fieldval = JText::_('JEV_EVENT_FINISHED');
					}
				}
				if (stripos($fieldval, "%d") !== false)
				{
					$days = intval($timedelta / (60 * 60 * 24));
					$timedelta -= $days * 60 * 60 * 24;
					$fieldval = str_ireplace("%d", $days, $fieldval);
					$shownsign = true;
				}
				if (stripos($fieldval, "%h") !== false)
				{
					$hours = intval($timedelta / (60 * 60));
					$timedelta -= $hours * 60 * 60;
					if ($shownsign)
						$hours = abs($hours);
					$hours = sprintf("%02d", $hours);
					$fieldval = str_ireplace("%h", $hours, $fieldval);
					$shownsign = true;
				}
				if (stripos($fieldval, "%m") !== false)
				{
					$mins = intval($timedelta / 60);
					$timedelta -= $hours * 60;
					if ($mins)
						$mins = abs($mins);
					$mins = sprintf("%02d", $mins);
					$fieldval = str_ireplace("%m", $mins, $fieldval);
				}

				$content .= $fieldval;
				break;

			case 'createdByAlias':
				$content .= $dayEvent->created_by_alias();
				break;

			case 'createdByUserName':
				$catobj = JEVHelper::getUser($dayEvent->created_by());
				$content .= isset($catobj->username) ? $catobj->username : "";
				break;

			case 'createdByUserEmail':
				// Note that users email address will NOT be available if they don't want to receive email
				$catobj = JEVHelper::getUser($dayEvent->created_by());
				$content .= $catobj->sendEmail ? $catobj->email : '';
				break;

			case 'createdByUserEmailLink':
				// Note that users email address will NOT be available if they don't want to receive email
				$content .= JRoute::_("index.php?option="
								. $compname
								. "&task=" . $task_events
								. "&agid=" . $dayEvent->id()
								. "&year=" . $st_year
								. "&month=" . $st_month
								. "&day=" . $st_day
								. "&Itemid=" . $this->myItemid . $this->catout);
				break;

			case 'color':
				$content .= $dayEvent->bgcolor();
				break;

			case 'eventDetailLink':
				$link = $dayEvent->viewDetailLink($st_year, $st_month, $st_day, false, $this->myItemid);
				$link = JRoute::_($link . $this->datamodel->getCatidsOutLink());
				$content .= $link;

				/*
				  $content .= JRoute::_("index.php?option="
				  . $compname
				  . "&task=".$task_events
				  . "&agid=".$dayEvent->id()
				  . "&year=".$st_year
				  . "&month=".$st_month
				  . "&day=".$st_day
				  . "&Itemid=".$this->myItemid . $this->catout);
				 */
				break;

			case 'siteroot':
				$content .= JUri::root();
				break;
			case 'sitebase':
				$content .= Juri::base();
				break;

			default:
				try {
					if (strpos($match, '${') !== false)
					{
						$parts = explode('${', $match);
						$tempstr = "";
						foreach ($parts as $part)
						{
							if (strpos($part, "}") !== false)
							{
								// limit to 2 because we may be using joomla content plugins
								$subparts = explode("}", $part,2);

								if (strpos($subparts[0], "#") > 0)
								{
									$formattedparts = explode("#", $subparts[0]);
									$subparts[0] = $formattedparts[0];
								}
								else
								{
									$formattedparts = array($subparts[0], "%s", "");
								}
								$subpart = "_" . $subparts[0];

								if (isset($dayEvent->$subpart))
								{
									$temp = $dayEvent->$subpart;
									if ($temp != "")
									{
										$tempstr .= str_replace("%s", $temp, $formattedparts[1]);
									}
									else if (isset($formattedparts[2]))
									{
										$tempstr .= str_replace("%s", $temp, $formattedparts[2]);
									}
								}
								else if (isset($dayEvent->customfields[$subparts[0]]['value']))
								{
									$temp = $dayEvent->customfields[$subparts[0]]['value'];
									if ($temp != "")
									{
										$tempstr .= str_replace("%s", $temp, $formattedparts[1]);
									}
									else if (isset($formattedparts[2]))
									{
										$tempstr .= str_replace("%s", $temp, $formattedparts[2]);
									}
								}
								else
								{
									$matchedByPlugin = false;
									$layout = "list";
									static $fieldNameArrays = array();
									$jevplugins = JPluginHelper::getPlugin("jevents");
									foreach ($jevplugins as $jevplugin)
									{
										$classname = "plgJevents" . ucfirst($jevplugin->name);
										if (is_callable(array($classname, "substitutefield")))
										{
											if (!isset($fieldNameArrays[$classname]))
											{
												$fieldNameArrays[$classname] = call_user_func(array($classname, "fieldNameArray"), $layout);

                                                                                                if (isset($fieldNameArrays[$classname]["values"]) && is_array($fieldNameArrays[$classname]["values"]))
                                                                                                {
                                                                                                    // Special case where $fieldname has option value in it e.g. sizedimages
                                                                                                    foreach($fieldNameArrays[$classname]["values"] as $idx => $fieldname){
                                                                                                        if (strpos($fieldname, ";")>0){
                                                                                                            $temp = explode(";", $fieldname);
                                                                                                            $fn = $temp[0];
                                                                                                            if (!in_array($fn,$fieldNameArrays[$classname]["values"])){
                                                                                                                $fieldNameArrays[$classname]["values"][] = $fn;
                                                                                                                $fieldNameArrays[$classname]["labels"][] = $fieldNameArrays[$classname]["labels"][$idx] ;
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                }

											}
											if (isset($fieldNameArrays[$classname]["values"]))
											{
                                                                                                $strippedSubPart = $subparts[0];
                                                                                                if (strpos($subparts[0], ";")){
                                                                                                    $temp = explode(";", $subparts[0]);
                                                                                                    $strippedSubPart = $temp[0];
                                                                                                }
												if (in_array($subparts[0], $fieldNameArrays[$classname]["values"]) || in_array($strippedSubPart, $fieldNameArrays[$classname]["values"]))
												{
													$matchedByPlugin = true;
													// is the event detail hidden - if so then hide any custom fields too!
													if (!isset($dayEvent->_privateevent) || $dayEvent->_privateevent != 3)
													{
														$temp = call_user_func(array($classname, "substitutefield"), $dayEvent, $subparts[0]);
														if ($temp != "")
														{
															$tempstr .= str_replace("%s", $temp, $formattedparts[1]);
														}
														else if (isset($formattedparts[2]))
														{
															$tempstr .= str_replace("%s", $temp, $formattedparts[2]);
														}
													}
												}
											}
										}
									}
									if (!$matchedByPlugin) {
										// Layout editor code
										include_once(JEV_PATH . "/views/default/helpers/defaultloadedfromtemplate.php");
										ob_start();
										// false at the end to stop it running through the plugins
										$part = "{{Dummy Label:".implode("#", $formattedparts)."}}";
										DefaultLoadedFromTemplate(false, false, $dayEvent, 0, $part,  false);
										$newpart = ob_get_clean();
										if ($newpart != $part) {
											$tempstr .= $newpart;
											$matchedByPlugin = true;
										}
									}
									// none of the plugins has replaced the output so we now replace the blank formatted part!
									if (!$matchedByPlugin && isset($formattedparts[2]))
									{
										$tempstr .= str_replace("%s", "", $formattedparts[2]);
									}
									//$dispatcher->trigger( 'onLatestEventsField', array( &$dayEvent, $subparts[0], &$tempstr));
								}
								$tempstr .= $subparts[1];
							}
							else
							{
								$tempstr .= $part;
							}
						}
						$content .= $tempstr;
					}
					else if ($match) {
						$content .= $match;
					}
				}
				catch (Exception $e) {
					if ($match)
						$content .= $match;
				}
				break;
		} // end of switch

	}
} // end of class
