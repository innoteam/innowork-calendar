<?php

namespace Shared\Dashboard;

use \Innomatic\Core\InnomaticContainer;
use \Shared\Wui;
use \Innomatic\Wui\Dispatch;

class InnoworkMyCalendarDashboardWidget extends \Innomatic\Desktop\Dashboard\DashboardWidget
{
    public function getWidgetXml()
    {
        $result = '';

        $locale_catalog = new \Innomatic\Locale\LocaleCatalog(
        		'innowork-calendar::calendar_dashboard',
        		InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getLanguage()
        );
        
        require_once('innowork/calendar/InnoworkEvent.php');
        $calendar = new InnoworkEvent(
        		\Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        		\Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()
        );
        
        $events = array();
        $search_result = $calendar->search(array('startdate' => date('Y').'-'.date('m')), \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId());
        $country = new \Innomatic\Locale\LocaleCountry(\Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getCountry());
        
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
              <link type="encoded">'.urlencode(\Innomatic\Wui\Dispatch\WuiEventsCall::buildEventsCallString('innoworkcalendar', array(array('view', 'showevent', array('id' => $id))))).'</link>
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
        <newaction type="encoded">'.urlencode(\Innomatic\Wui\Dispatch\WuiEventsCall::buildEventsCallString('innoworkcalendar', array(array('view', 'newevent')))).'</newaction>
      </args>
    </innoworkcalendar>
    <vertgroup><name>items</name>
      <children>'.$definition.'    </children>
    </vertgroup>
      		
    <horizbar/>
      		
  <button>
    <args>
      <horiz>true</horiz>
      <frame>false</frame>
      <themeimage>mathadd</themeimage>
      <label>'.$locale_catalog->getStr('new_event.button').'</label>
      <action>'.WuiXml::cdata(\Innomatic\Wui\Dispatch\WuiEventsCall::buildEventsCallString('innoworkcalendar', array(array('view', 'newevent', array())))).'</action>
    </args>
  </button>
      		
    </children>
  </vertgroup>';
        }
        
        return $result;
    }

    public function getWidth()
    {
        return 1;
    }

    public function getHeight()
    {
        return 180;
    }
}
