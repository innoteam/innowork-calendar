<?php

require_once('innowork/core/InnoworkItem.php');
require_once('innomatic/dataaccess/DataAccess.php');
require_once('innomatic/logging/Logger.php');

require_once('innowork/core/InnoworkCore.php');
$core = InnoworkCore::instance('innoworkcore', \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(), \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess());
$summ = $core->GetSummaries();

if (isset($summ['directorycompany'])) {
	$GLOBALS['innowork-calendar']['innowork_directory_installed'] = true;
} else {
	$GLOBALS['innowork-calendar']['innowork_directory_installed'] = false;
}

class InnoworkEvent extends InnoworkItem {
	var $mTable = 'innowork_calendar';
	var $mNewDispatcher = 'view';
	var $mNewEvent = 'newevent';
	var $mNoTrash = false;
	const ITEM_TYPE = 'event';
	const FREQUENCY_DAILY = 1;
	const FREQUENCY_WEEKLY = 2;
	const FREQUENCY_MONTHLY = 3;
	const FREQUENCY_YEARLY = 4;

	function InnoworkEvent($rrootDb, $rdomainDA, $eventId = 0) {
		parent::__construct($rrootDb, $rdomainDA, InnoworkEvent::ITEM_TYPE, $eventId);

		$this->mKeys['description'] = 'text';
		$this->mKeys['notes'] = 'text';
		$this->mKeys['startdate'] = 'timestamp';
		$this->mKeys['enddate'] = 'timestamp';
		$this->mKeys['frequency'] = 'integer';
		$this->mKeys['interv'] = 'integer';
		$this->mKeys['exttype'] = 'text';
		$this->mKeys['extid'] = 'integer';
		$this->mKeys['exticon'] = 'text';
		$this->mKeys['extdata'] = 'text';

		if ($GLOBALS['innowork-calendar']['innowork_directory_installed']) {
			$this->mKeys['companyid'] = 'table:innowork_directory_companies:companyname:integer';
		} else {
			$this->mKeys['companyid'] = 'integer';
		}

		$this->mSearchResultKeys[] = 'description';
		$this->mSearchResultKeys[] = 'notes';
		$this->mSearchResultKeys[] = 'startdate';
		$this->mSearchResultKeys[] = 'enddate';
		$this->mSearchResultKeys[] = 'frequency';
		$this->mSearchResultKeys[] = 'interv';
		$this->mSearchResultKeys[] = 'exttype';
		$this->mSearchResultKeys[] = 'extid';
		$this->mSearchResultKeys[] = 'exticon';
		$this->mSearchResultKeys[] = 'extdata';

		if ($GLOBALS['innowork-calendar']['innowork_directory_installed']) {
			$this->mSearchResultKeys[] = 'companyid';
		}

		$this->mViewableSearchResultKeys[] = 'description';
		$this->mViewableSearchResultKeys[] = 'notes';
		$this->mViewableSearchResultKeys[] = 'startdate';
		$this->mViewableSearchResultKeys[] = 'enddate';

		if ($GLOBALS['innowork-calendar']['innowork_directory_installed']) {
			$this->mViewableSearchResultKeys[] = 'companyid';
		}

		$this->mSearchOrderBy = 'startdate,enddate,description';

		$this->mShowDispatcher = 'view';
		$this->mShowEvent = 'showevent';
	}

	function doCreate($params, $userId) {
		$result = false;

		if (count($params)) {
			$item_id = $this->mrDomainDA->getNextSequenceValue($this->mTable.'_id_seq');
			$key_pre = $value_pre = $keys = $values = '';

			require_once('innomatic/locale/LocaleCountry.php');
			$country = new LocaleCountry(\Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getCountry());

			$params['trashed'] = $this->mrDomainDA->fmtfalse;

			if (!isset($params['companyid']) or !strlen($params['companyid']))
			$params['companyid'] = '0';
			if (!isset($params['extid']) or !strlen($params['extid']))
			$params['extid'] = '0';

			if (!isset($params['exttype']) or !strlen($params['exttype']))
			$params['exttype'] = '';
			if (!isset($params['exticon']) or !strlen($params['exticon']))
			$params['exticon'] = '';
			if (!isset($params['extdata']) or !strlen($params['extdata']))
			$params['extdata'] = '';

			while (list ($key, $val) = each($params)) {
				$key_pre = ',';
				$value_pre = ',';

				switch ($key) {
					case 'description' :
					case 'notes' :
					case 'trashed' :
					case 'frequency' :
					case 'interv' :
					case 'exttype' :
					case 'exticon' :
					case 'extdata' :
						$keys.= $key_pre.$key;
						$values.= $value_pre.$this->mrDomainDA->formatText($val);
						break;

					case 'startdate' :
					case 'enddate' :
						$val = $this->mrDomainDA->GetTimestampFromDateArray($val);

						$keys.= $key_pre.$key;
						$values.= $value_pre.$this->mrDomainDA->formatText($val);
						break;

					case 'companyid' :
					case 'extid' :
						if (!strlen($key))
						$key = 0;
						$keys.= $key_pre.$key;
						$values.= $value_pre.$val;
						break;

					default :
						break;
				}
			}

			if (strlen($values)) {
				if ($this->mrDomainDA->execute('INSERT INTO '.$this->mTable.' '.'(id,ownerid'.$keys.') '.'VALUES ('.$item_id.','.$userId.$values.')'))
				$result = $item_id;
			}
		}

		return $result;
	}

	function doEdit($params, $userId) {
		$result = FALSE;

		if ($this->mItemId) {
			if (count($params)) {
				$start = 1;
				$update_str = '';

				require_once('innomatic/locale/LocaleCountry.php');
				$country = new LocaleCountry(\Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getCountry());

				while (list ($field, $value) = each($params)) {
					if ($field != 'id') {
						switch ($field) {
							case 'description' :
							case 'notes' :
							case 'frequency' :
							case 'interv' :
							case 'exttype' :
							case 'exticon' :
								if (!$start)
								$update_str.= ',';
								$update_str.= $field.'='.$this->mrDomainDA->formatText($value);
								$start = 0;
								break;

							case 'startdate' :
							case 'enddate' :
								$value = $this->mrDomainDA->GetTimestampFromDateArray($value);

								if (!$start)
								$update_str.= ',';
								$update_str.= $field.'='.$this->mrDomainDA->formatText($value);
								$start = 0;
								break;

							case 'companyid' :
							case 'extid' :
								if (!strlen($value))
								$value = 0;
								if (!$start)
								$update_str.= ',';
								$update_str.= $field.'='.$value;
								$start = 0;
								break;

							default :
								break;
						}
					}
				}

				$query = & $this->mrDomainDA->execute('UPDATE '.$this->mTable.' '.'SET '.$update_str.' '.'WHERE id='.$this->mItemId);

				if ($query)
				$result = TRUE;
			}
		}

		return $result;
	}

	function doRemove($userId) {
		$result = FALSE;

		$result = $this->mrDomainDA->execute('DELETE FROM '.$this->mTable.' '.'WHERE id='.$this->mItemId);

		return $result;
	}

	function doGetItem($userId) {
		$result = FALSE;

		$item_query = & $this->mrDomainDA->execute('SELECT * '.'FROM '.$this->mTable.' '.'WHERE id='.$this->mItemId);

		if (is_object($item_query) and $item_query->getNumberRows()) {
			$result = $item_query->getFields();
		}

		return $result;
	}

	function doTrash($arg = '') {
		return true;
	}

	function doGetSummary() {
		require_once('innomatic/wui/dispatch/WuiEventsCall.php');
		$result = false;
		//return true;


		$events = array();

		$search_result = $this->Search(array('startdate' => date('Y').'-'.date('m')), \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId());

		require_once('innomatic/locale/LocaleCountry.php');
		$country = new LocaleCountry(\Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getCountry());

		if (is_array($search_result)) {
			$definition = '';

			$today_array['year'] = date('Y');
			$today_array['mon'] = date('n');
			$today_array['mday'] = date('d');

			while (list ($id, $fields) = each($search_result)) {
				$event_start_array = $country->getDateArrayFromSafeTimestamp($fields['startdate']);
				$event_end_array = $country->getDateArrayFromSafeTimestamp($fields['enddate']);

				$events[$event_start_array['year']][$event_start_array['mon']][$event_start_array['mday']][$fields['id']] = array('sh' => $event_start_array['hours'], 'sm' => $event_start_array['minutes'], 'eh' => $event_end_array['hours'], 'em' => $event_end_array['minutes'], 'event' => $fields['description'], 'type' => $fields['ownerid'] == \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId() ? 'private' : 'public');

				if ($event_start_array['year'] <= $today_array['year'] and $event_end_array['year'] >= $today_array['year'] and $event_start_array['mon'] <= $today_array['mon'] and $event_end_array['mon'] >= $today_array['mon'] and $event_start_array['mday'] <= $today_array['mday'] and $event_end_array['mday'] >= $today_array['mday']) {
					if (strlen($fields['description']) > 25)
					$description = substr($fields['description'], 0, 22).'...';
					else
					$description = $fields['description'];

					$definition.= '<horizgroup><name>eventhgroup</name>
					                                                                      <children>
					                                                                        <label><name>eventlabel</name>
					                                                                          <args>
					                                                                            <label>- </label>
					                                                                            <compact>true</compact>
					                                                                          </args>
					                                                                        </label>
					                                                                        <link><name>eventlink</name>
					                                                                          <args>
					                                                                            <nowrap>false</nowrap>
					                                                                            <label type="encoded">'.urlencode($description).'</label>
					                                                                            <title type="encoded">'.urlencode($fields['description']).'</title>
					                                                                            <link type="encoded">'.urlencode(WuiEventsCall::buildEventsCallString('innoworkcalendar', array(array('view', 'showevent', array('id' => $id))))).'</link>
					                                                                            <compact>true</compact>
					                                                                          </args>
					                                                                        </link>
					                                                                      </children>
					                                                                    </horizgroup>';
				}
			}

			$result = '<vertgroup><name>eventsgroup</name>
			                                              <children>
			                                              <innoworkcalendar><name>calendar</name>
			                                                <args>
			                                                  <events type="array">'.WuiXml::encode($events).'</events>
			                                                  <viewby>flatmonth</viewby>
			                                                  <day>'.date('d').'</day>
			                                                  <month>'.date('n').'</month>
			                                                  <year>'.date('Y').'</year>
			                                                  <showdaybuilderfunction>calendar_summary_show_day_action_builder</showdaybuilderfunction>
			                                                  <showeventbuilderfunction>calendar_summary_show_event_action_builder</showeventbuilderfunction>
			                                                  <disp>view</disp>
			                                                  <newaction type="encoded">'.urlencode(WuiEventsCall::buildEventsCallString('innoworkcalendar', array(array('view', 'newevent')))).'</newaction>
			                                                </args>
			                                              </innoworkcalendar>
			                                              <vertgroup><name>items</name>
			                                                <children>'.$definition.'    </children>
			                                              </vertgroup>
			                                              </children>
			                                            </vertgroup>';
		}

		return $result;
	}
}

function calendar_summary_show_event_action_builder($id) {
	require_once('innomatic/wui/dispatch/WuiEventsCall.php');
	return WuiEventsCall::buildEventsCallString('', array(array('view', 'showevent', array('id' => $id))));
}

function calendar_summary_show_day_action_builder($year, $month, $day) {
	require_once('innomatic/wui/dispatch/WuiEventsCall.php');
	return WuiEventsCall::buildEventsCallString('innoworkcalendar', array(array('view', 'default', array('year' => $year, 'month' => $month, 'day' => $day, 'viewby' => 'day'))));
}
?>