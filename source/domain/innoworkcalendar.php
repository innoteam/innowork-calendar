<?php

// ----- Initialization -----
//
global $gLocale, $gPage_status;
    global $gXml_def, $gLocale, $gPage_title, $gCompanies, $innowork_directory_installed;
    global $gLocale, $gPage_title, $gXml_def, $gPage_status;

require_once('innomatic/wui/Wui.php');
require_once('innomatic/wui/widgets/WuiWidget.php');
require_once('innomatic/wui/widgets/WuiContainerWidget.php');
require_once('innomatic/wui/dispatch/WuiEventsCall.php');
require_once('innomatic/wui/dispatch/WuiEvent.php');
require_once('innomatic/wui/dispatch/WuiEventRawData.php');
require_once('innomatic/wui/dispatch/WuiDispatcher.php');
require_once('innomatic/locale/LocaleCatalog.php'); require_once('innomatic/locale/LocaleCountry.php'); 

require_once('innowork/core/InnoworkCore.php');
$gInnowork_core = InnoworkCore::instance('innoworkcore', 
    \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
    \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()
    );

$gLocale = new LocaleCatalog(
    'innowork-calendar::calendar_main',
    \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getLanguage()
    );

$gWui = Wui::instance('wui');
$gWui->loadWidget( 'xml' );
$gWui->loadWidget( 'innomaticpage' );
$gWui->loadWidget( 'innomatictoolbar' );
$gWui->loadWidget( 'innoworkcalendar' );

$gXml_def = $gPage_status = '';
$gPage_title = $gLocale->getStr( 'innoworkcalendar.title' );
$gCore_toolbars = $gInnowork_core->getMainToolBar();
$gToolbars['new'] = array(
    'newevent' => array(
        'label' => $gLocale->getStr( 'newevent.toolbar' ),
        'themeimage' => 'calendaradd',
        'horiz' => 'true',
        'action' => WuiEventsCall::buildEventsCallString( '', array( array(
            'view',
            'newevent',
            '' ) ) )
        )
    );

$gToolbars['viewby'] = array(
    'month' => array(
        'label' => $gLocale->getStr( 'viewby_month.toolbar' ),
        'themeimage' => 'calendar2',
        'horiz' => 'true',
        'action' => WuiEventsCall::buildEventsCallString( '', array( array(
            'view',
            'default',
            array( 'viewby' => 'month' ) ) ) )
        ),
    'week' => array(
        'label' => $gLocale->getStr( 'viewby_week.toolbar' ),
        'themeimage' => 'calendar2',
        'horiz' => 'true',
        'action' => WuiEventsCall::buildEventsCallString( '', array( array(
            'view',
            'default',
            array( 'viewby' => 'week' ) ) ) )
        ),
    'day' => array(
        'label' => $gLocale->getStr( 'viewby_day.toolbar' ),
        'themeimage' => 'calendar2',
        'horiz' => 'true',
        'action' => WuiEventsCall::buildEventsCallString( '', array( array(
            'view',
            'default',
            array( 'viewby' => 'day' ) ) ) )
        ),
    );

$gToolbars['move'] = array(
    'parentprevious' => array(
        'label' => $gLocale->getStr( 'move_previous.toolbar' ),
        'themeimage' => 'rewind',
        'horiz' => 'true',
        'action' => WuiEventsCall::buildEventsCallString( '', array( array(
            'view',
            'default',
            array( 'parentshift' => 'parentprevious' ) ) ) )
        ),

    'previous' => array(
        'label' => $gLocale->getStr( 'move_previous.toolbar' ),
        'themeimage' => 'arrowleft',
        'horiz' => 'true',
        'action' => WuiEventsCall::buildEventsCallString( '', array( array(
            'view',
            'default',
            array( 'shift' => 'previous' ) ) ) )
        ),
    'today' => array(
        'label' => $gLocale->getStr( 'move_today.toolbar' ),
        'themeimage' => 'calendarempty',
        'horiz' => 'true',
        'action' => WuiEventsCall::buildEventsCallString( '', array( array(
            'view',
            'default',
            array(
                'year' => date( 'Y' ),
                'month' => date( 'n' ),
                'day' => date( 'd' )
                ) ) ) )
        ),
    'next' => array(
        'label' => $gLocale->getStr( 'move_next.toolbar' ),
        'themeimage' => 'arrowright',
        'horiz' => 'true',
        'action' => WuiEventsCall::buildEventsCallString( '', array( array(
            'view',
            'default',
            array( 'shift' => 'next' ) ) ) )
        ),
    'parentnext' => array(
        'label' => $gLocale->getStr( 'move_previous.toolbar' ),
        'themeimage' => 'fastforward',
        'horiz' => 'true',
        'action' => WuiEventsCall::buildEventsCallString( '', array( array(
            'view',
            'default',
            array( 'parentshift' => 'parentnext' ) ) ) )
        )
    );

    $core = InnoworkCore::instance('innoworkcore', \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(), \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess());
    $summ = $core->getSummaries();

    if (isset($summ['directorycompany'])) $innowork_directory_installed = true;
else $innowork_directory_installed = false;

if ( $innowork_directory_installed )
{
    $innowork_companies = new InnoworkCompany(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()
        );
    $search_results = $innowork_companies->Search(
        '',
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId()
        );

    $gCompanies[0] = $gLocale->getStr( 'nocompany.label' );

    while ( list( $id, $fields ) = each( $search_results ) )
    {
        $gCompanies[$id] = $fields['companyname'];
    }

    unset( $innowork_companies );
    unset( $search_results );
}

// ----- Action dispatcher -----
//
$gAction_disp = new WuiDispatcher( 'action' );

$gAction_disp->addEvent(
    'newevent',
    'action_newevent'
    );
function action_newevent( $eventData )
{
    global $gLocale, $gPage_status;

    $locale_country = new LocaleCountry(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getCountry()
        );

    require_once('innowork/calendar/InnoworkEvent.php');
    $innowork_event = new InnoworkEvent(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()
        );

    $start_date_array = $locale_country->getDateArrayFromShortDatestamp( $eventData['startdate'] );
    $end_date_array = $locale_country->getDateArrayFromShortDatestamp( $eventData['enddate'] );

    $eventData['starttime'] = str_replace( '.', ':', $eventData['starttime'] );
    $eventData['starttime'] = str_replace( ',', ':', $eventData['starttime'] );

    $eventData['endtime'] = str_replace( '.', ':', $eventData['endtime'] );
    $eventData['endtime'] = str_replace( ',', ':', $eventData['endtime'] );

    list(
        $start_date_array['hours'],
        $start_date_array['minutes']
        ) = explode( ':', $eventData['starttime'] );

    list(
        $end_date_array['hours'],
        $end_date_array['minutes']
        ) = explode( ':', $eventData['endtime'] );

    $eventData['startdate'] = $start_date_array;
    $eventData['enddate'] = $end_date_array;

    if ( $innowork_event->Create(
        $eventData,
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId()
        ) )
    {
        $GLOBALS['innowork-calendar']['neweventid'] = $innowork_event->mItemId;

        $gPage_status = $gLocale->getStr( 'event_added.status' );
    }
    else $gPage_status = $gLocale->getStr( 'event_not_added.status' );
}

$gAction_disp->addEvent(
    'editevent',
    'action_editevent'
    );
function action_editevent( $eventData )
{
    global $gLocale, $gPage_status;

    $locale_country = new LocaleCountry(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getCountry()
        );

    require_once('innowork/calendar/InnoworkEvent.php');
    $innowork_event = new InnoworkEvent(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess(),
        $eventData['id']
        );

    $start_date_array = $locale_country->getDateArrayFromShortDatestamp( $eventData['startdate'] );
    $end_date_array = $locale_country->getDateArrayFromShortDatestamp( $eventData['enddate'] );

    $eventData['starttime'] = str_replace( '.', ':', $eventData['starttime'] );
    $eventData['starttime'] = str_replace( ',', ':', $eventData['starttime'] );

    $eventData['endtime'] = str_replace( '.', ':', $eventData['endtime'] );
    $eventData['endtime'] = str_replace( ',', ':', $eventData['endtime'] );

    list(
        $start_date_array['hours'],
        $start_date_array['minutes']
        ) = explode( ':', $eventData['starttime'] );

    list(
        $end_date_array['hours'],
        $end_date_array['minutes']
        ) = explode( ':', $eventData['endtime'] );

    $eventData['startdate'] = $start_date_array;
    $eventData['enddate'] = $end_date_array;

    if ( $innowork_event->Edit(
        $eventData,
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId()
        ) ) $gPage_status = $gLocale->getStr( 'event_updated.status' );
    else $gPage_status = $gLocale->getStr( 'event_not_updated.status' );
}

$gAction_disp->addEvent(
    'removeevent',
    'action_removeevent'
    );
function action_removeevent( $eventData )
{
    global $gLocale, $gPage_status;

    require_once('innowork/calendar/InnoworkEvent.php');
    $innowork_event = new InnoworkEvent(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess(),
        $eventData['id']
        );

    if ( $innowork_event->trash(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId()
        ) ) $gPage_status = $gLocale->getStr( 'event_removed.status' );
    else $gPage_status = $gLocale->getStr( 'event_not_removed.status' );
}

$gAction_disp->Dispatch();

// ----- Main dispatcher -----
//
$gMain_disp = new WuiDispatcher( 'view' );

function calendar_show_event_action_builder( $id )
{
    return WuiEventsCall::buildEventsCallString( '', array( array(
                'view',
                'showevent',
                array( 'id' => $id )
        ) ) );
}

function calendar_show_day_action_builder( 
    $year,
    $month, 
    $day 
    )
{
    return WuiEventsCall::buildEventsCallString( '', array( array(
                'view',
                'default',
                array(
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'viewby' => 'day'
                    )
        ) ) );
}

$gMain_disp->addEvent(
    'default',
    'main_default' );
function main_default( $eventData )
{
    global $gLocale, $gPage_title, $gXml_def, $gPage_status;

    require_once('shared/wui/WuiSessionkey.php');
    
    if ( isset($eventData['filter_restrictto'] ) )
    {
        // Restrict

        $restrictto_filter_sk = new WuiSessionKey(
            'restrictto_filter',
            array(
                'value' => $eventData['filter_restrictto']
                )
            );
    }
    else
    {
        // Restrict

        $restrictto_filter_sk = new WuiSessionKey( 'restrictto_filter' );

        $eventData['filter_restrictto'] = $restrictto_filter_sk->mValue;
    }

    $tmp_cal = new WuiInnoworkcalendar( 'calendar', array(
        'shift' => isset($eventData['shift'] ) ? $eventData['shift'] : ''
        ) );
        
    $tmp_cal = new WuiInnoworkcalendar( 'calendar', array(
        'parentshift' => isset($eventData['parentshift'] ) ? $eventData['parentshift'] : ''
        ) );

    require_once('innowork/calendar/InnoworkEvent.php');
    $innowork_events = new InnoworkEvent(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()
        );
    /**/
    $search_results_a = $innowork_events->Search(
        array(
                'startdate' => str_pad(
                        (
                        $eventData['year'] ?
                        $eventData['year'] :
                        (
                            $tmp_cal->mYear ?
                            $tmp_cal->mYear :
                            date( 'Y' )
                        )
                        ),
                        4, '0', STR_PAD_LEFT
                        ).
                    '-'.str_pad(
                        (
                        $eventData['month'] ?
                        $eventData['month'] :
                        (
                            $tmp_cal->mMonth ?
                            $tmp_cal->mMonth :
                            date( 'm' )
                        )
                        ),
                        2, '0', STR_PAD_LEFT
                        )
                ),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId(),
        false,
        false,
        0,
        0,
        $eventData['filter_restrictto']
        );

    if (
        $tmp_cal->mViewBy == 'week'
        or
        (
            isset($eventData['viewby'] )
            and
            $eventData['viewby'] == 'week'
        )
        )
    {
        $year_b = $tmp_cal->mYear ?
                            $tmp_cal->mYear :
                            date( 'Y' );

        $month_b = $tmp_cal->mMonth ?
                            $tmp_cal->mMonth - 1:
                            date( 'm' ) - 1;

        if ( $month_b <= 0 )
        {
            $month_b = 12;
            $year_b--;
        }

    $search_results_b = $innowork_events->Search(
        array(
                'startdate' => str_pad(
                        (
                        $eventData['year'] ?
                        $eventData['year'] :
                        (
                            $year_b
                        )
                        ),
                        4, '0', STR_PAD_LEFT
                        ).
                    '-'.str_pad(
                        (
                        $eventData['month'] ?
                        $eventData['month'] :
                        (
                            $month_b
                        )
                        ),
                        2, '0', STR_PAD_LEFT
                        )
                ),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId(),
        false,
        false,
        0,
        0,
        $eventData['filter_restrictto']
        );

        $year_c = $tmp_cal->mYear ?
                            $tmp_cal->mYear :
                            date( 'Y' );

        $month_c = $tmp_cal->mMonth ?
                            $tmp_cal->mMonth + 1:
                            date( 'm' ) + 1;

        if ( $month_c >= 12 )
        {
            $month_c = 1;
            $year_c++;
        }

    $search_results_c = $innowork_events->Search(
        array(
                'startdate' => str_pad(
                        (
                        $eventData['year'] ?
                        $eventData['year'] :
                        (
                            $year_c
                        )
                        ),
                        4, '0', STR_PAD_LEFT
                        ).
                    '-'.str_pad(
                        (
                        $eventData['month'] ?
                        $eventData['month'] :
                        (
                            $month_c
                        )
                        ),
                        2, '0', STR_PAD_LEFT
                        )
                ),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId(),
        false,
        false,
        0,
        0,
        $eventData['filter_restrictto']
        );

    $search_results = array_merge(
        $search_results_a,
        $search_results_b,
        $search_results_c
        );
    }
    else
    {
        $search_results = &$search_results_a;
    }

        /**/
        /*
    $search_results = $innowork_events->Search(
        '',
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId(),
        false,
        false,
        0,
        0,
        $eventData['filter_restrictto']
        );
        */

    $events = array();
    $locale_country = new LocaleCountry( \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getCountry() );
	require_once('innowork/calendar/InnoworkEvent.php');
	
    while ( list( $id, $fields ) = each( $search_results ) )
    {
        $start_date_array = $locale_country->getDateArrayFromSafeTimestamp( $fields['startdate'] );
        $end_date_array = $locale_country->getDateArrayFromSafeTimestamp( $fields['enddate'] );

        // Type
        /*
        $acl = new InnoworkAcl(
            \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
            \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess(),
            'event',
            $id
            );
        if ( $acl->getType() != InnoworkAcl::TYPE_PRIVATE ) $type = 'public';
        else $type = 'private';
        */

        if ( $fields['_acl']['type'] != InnoworkAcl::TYPE_PRIVATE ) $type = 'public';
        else $type = 'private';

        // Event
        $events[$start_date_array['year']][$start_date_array['mon']][$start_date_array['mday']][$fields['id']] = array(
            'sh' => $start_date_array['hours'],
            'sm' => $start_date_array['minutes'],
            'eh' => $end_date_array['hours'],
            'em' => $end_date_array['minutes'],
            'event' => $fields['description'],
            'notes' => $fields['notes'],
            'type' => $type,
            'interv' => $fields['interv'],
            'exttype' => $fields['exttype'],
            'extid' => $fields['extid'],
            'exticon' => $fields['exticon']
            );
    }
	$search_results_daily = $innowork_events->Search(
        array(
                'frequency' => InnoworkEvent::FREQUENCY_DAILY
                ),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId(),
        false,
        false,
        0,
        0,
        $eventData['filter_restrictto']
        );


	$search_results_weekly = $innowork_events->Search(
        array(
                'frequency' => InnoworkEvent::FREQUENCY_WEEKLY
                ),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId(),
        false,
        false,
        0,
        0,
        $eventData['filter_restrictto']
        );
        
	$search_results_monthly = $innowork_events->Search(
        array(
                'frequency' => InnoworkEvent::FREQUENCY_MONTHLY
                ),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId(),
        false,
        false,
        0,
        0,
        $eventData['filter_restrictto']
        );
        
    $search_results_yearly = $innowork_events->Search(
        array(
                'frequency' => InnoworkEvent::FREQUENCY_YEARLY
                ),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId(),
        false,
        false,
        0,
        0,
        $eventData['filter_restrictto']
        );


	$search_results = array_merge(
	    $search_results_daily,
        $search_results_weekly,
        $search_results_monthly,
        $search_results_yearly
        );
	require_once('innowork/calendar/InnoworkEvent.php');
	
	while ( list( $id, $fields ) = each( $search_results ) )
    {
        if ( $fields['_acl']['type'] != InnoworkAcl::TYPE_PRIVATE ) $type = 'public';
        else $type = 'private';

    	if (
        	$tmp_cal->mViewBy == 'week'
        	or
        	(
            	isset($eventData['viewby'] )
            	and
            	$eventData['viewby'] == 'week'
        	) )
        {
            $year_max = $year_c;
            $month_max = $month_c;
        }
        else
        {
            $year_max = ( $eventData['year'] ?
                        	$eventData['year'] :
                        	(
                            	$tmp_cal->mYear ?
                            	$tmp_cal->mYear :
                            	date( 'Y' )
                        	) );

			$month_max = ( $eventData['month'] ?
                        	$eventData['month'] :
                        	(
                            	$tmp_cal->mMonth ?
                            	$tmp_cal->mMonth :
                            	date( 'm' )
                        	) );
    	}

    	if (
        	$tmp_cal->mViewBy == 'week'
        	or
        	(
            	isset($eventData['viewby'] )
            	and
            	$eventData['viewby'] == 'week'
        	) )
        {
            $year_min = $year_b;
            $month_min = $month_b;
        }
        else
        {
            $year_min = ( $eventData['year'] ?
                        	$eventData['year'] :
                        	(
                            	$tmp_cal->mYear ?
                            	$tmp_cal->mYear :
                            	date( 'Y' )
                        	) );

			$month_min = ( $eventData['month'] ?
                        	$eventData['month'] :
                        	(
                            	$tmp_cal->mMonth ?
                            	$tmp_cal->mMonth :
                            	date( 'm' )
                        	) ) - 1;
    	}

        $start_date_array = $tmp_date_array = $locale_country->getDateArrayFromSafeTimestamp( $fields['startdate'] );
        $end_date_array = $locale_country->getDateArrayFromSafeTimestamp( $fields['enddate'] );

		switch ( $fields['frequency'] )
		{
		case InnoworkEvent::FREQUENCY_DAILY:
			break;

		case InnoworkEvent::FREQUENCY_WEEKLY:
			break;

		case InnoworkEvent::FREQUENCY_MONTHLY:
			break;

		case InnoworkEvent::FREQUENCY_YEARLY:
		     //if ( $year_min > $tmp_date_array['year'] ) $tmp_date_array['year'] = $year_min;

			break;
		}
		
		$max_ts = mktime( 23, 59, 59, $month_max, 31, $year_max );
		$tmp_ts = mktime( $tmp_date_array['hours'], $tmp_date_array['minutes'], $tmp_date_array['seconds'],
    	        	$tmp_date_array['mon'], $tmp_date_array['mday'], $tmp_date_array['year'] );
    	        	
		while ( $tmp_ts <= $max_ts )
		{
			$tmp_date_array = getdate( $tmp_ts );
			
            $events[$tmp_date_array['year']][str_pad( $tmp_date_array['mon'], 2, '0', STR_PAD_LEFT )][str_pad( $tmp_date_array['mday'], 2, '0', STR_PAD_LEFT )][$fields['id']] = array(
                'sh' => $start_date_array['hours'],
                'sm' => $start_date_array['minutes'],
                'eh' => $end_date_array['hours'],
                'em' => $end_date_array['minutes'],
                'event' => $fields['description'],
                'notes' => $fields['notes'],
                'type' => $type,
				'frequency' => $fields['frequency'],
				'interv' => $fields['interv']
                );

    		switch ( $fields['frequency'] )
    		{

    		case InnoworkEvent::FREQUENCY_DAILY:
	    		$tmp_ts = mktime( $tmp_date_array['hours'], $tmp_date_array['minutes'], $tmp_date_array['seconds'],
	    	        	$tmp_date_array['mon'], $tmp_date_array['mday'] , $tmp_date_array['year'] );
				$tmp_ts = $tmp_ts + ( $fields['interv']* 86400 );
    			break;
    		
    		case InnoworkEvent::FREQUENCY_WEEKLY:
	    		$tmp_ts = mktime( $tmp_date_array['hours'], $tmp_date_array['minutes'], $tmp_date_array['seconds'],
	    	        	$tmp_date_array['mon'], $tmp_date_array['mday'] +( 7 * $fields['interv']), $tmp_date_array['year'] );
    			break;
    		
    		case InnoworkEvent::FREQUENCY_MONTHLY:
	    		$tmp_ts  = mktime( $tmp_date_array['hours'], $tmp_date_array['minutes'], $tmp_date_array['seconds'],
	    	        	$tmp_date_array['mon'] + ( 1 * $fields['interv'] ), $tmp_date_array['mday']  , $tmp_date_array['year'] );
				$curr_date_array = getdate( $tmp_ts );

				while ( $curr_date_array['mday'] != $tmp_date_array['mday'] )
				{
	    			$tmp_ts  = mktime( $curr_date_array['hours'], $curr_date_array['minutes'], $curr_date_array['seconds'],
	    	        	$curr_date_array['mon'], $curr_date_array['mday'] + 1 , $curr_date_array['year'] );
					$curr_date_array = getdate( $tmp_ts );
				}
    		break;
    			
    		case InnoworkEvent::FREQUENCY_YEARLY:
    			$tmp_ts = mktime ( $tmp_date_array['hours'], $tmp_date_array['minutes'], $tmp_date_array['seconds'],
    	        	$tmp_date_array['mon'], $tmp_date_array['mday'], $tmp_date_array['year']+ ( 1 * $fields['interv'] ));

				if ( $curr_date_array['mday'] != $tmp_date_array['mday'] )
				{
    				$tmp_ts  -= 86400;
				}
    			break;
    		}
		}
}
	
    $restrictto_array[InnoworkItem::SEARCH_RESTRICT_NONE] = $gLocale->getStr( 'restrictto_none.label' );
    $restrictto_array[InnoworkItem::SEARCH_RESTRICT_TO_OWNER] = $gLocale->getStr( 'restrictto_owner.label' );
    $restrictto_array[InnoworkItem::SEARCH_RESTRICT_TO_RESPONSIBLE] = $gLocale->getStr( 'restrictto_responsible.label' );
    $restrictto_array[InnoworkItem::SEARCH_RESTRICT_TO_PARTICIPANT] = $gLocale->getStr( 'restrictto_participants.label' );

    $gXml_def =
'<vertgroup>
  <children>

    <label>
      <args>
        <label type="encoded">'.urlencode( $gLocale->getStr( 'filter.label' ) ).'</label>
        <bold>true</bold>
      </args>
    </label>

    <form><name>filter</name>
      <args>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'default',
                    array(
                        'filter' => 'true'
                        )
                    )
            ) ) ).'</action>
      </args>
      <children>
      <grid>
       <children>

        <label row="0" col="0">
          <args>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'restrictto.label' ) ).'</label>
          </args>
        </label>

        <combobox row="0" col="1"><name>filter_restrictto</name>
          <args>
            <disp>view</disp>
            <elements type="array">'.WuiXml::encode( $restrictto_array ).'</elements>
            <default>'.$eventData['filter_restrictto'].'</default>
          </args>
        </combobox>

        <button row="0" col="2"><name>filter</name>
          <args>
            <themeimage>zoom</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <formsubmit>filter</formsubmit>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'filter.button' ) ).'</label>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'default',
                    array(
                        'filter' => 'true'
                        )
                    )
            ) ) ).'</action>
          </args>
        </button>

       </children>
      </grid>
     </children>
    </form>

    <horizbar/>

  <innoworkcalendar><name>calendar</name>
    <args>
      <events type="array">'.WuiXml::encode( $events ).'</events>
      <viewby>'.( isset($eventData['viewby'] ) ? $eventData['viewby'] : '' ).'</viewby>
      <year>'.( isset($eventData['year'] ) ? $eventData['year'] : '' ).'</year>
      <month>'.( isset($eventData['month'] ) ? $eventData['month'] : '' ).'</month>
      <day>'.( isset($eventData['day'] ) ? $eventData['day'] : '' ).'</day>
      <showdaybuilderfunction>calendar_show_day_action_builder</showdaybuilderfunction>
      <showeventbuilderfunction>calendar_show_event_action_builder</showeventbuilderfunction>
      <disp>view</disp>
      <newaction type="encoded">'.urlencode(
        WuiEventsCall::buildEventsCallString(
            'innoworkcalendar',
            array(
                array(
                    'view',
                    'newevent'
                    )
                )
            )
        ).'</newaction>
    </args>
  </innoworkcalendar>

  </children>
</vertgroup>';
}

$gMain_disp->addEvent(
    'newevent',
    'main_newevent'
    );
function main_newevent( $eventData )
{
    global $gXml_def, $gLocale, $gPage_title, $gCompanies, $innowork_directory_installed;

    $start_date_array = array(
        'year' => date( 'Y' ),
        'mon' => date( 'm' ),
        'mday' => date( 'd' ),
        'hours' => date( 'H' ),
        'minutes' => '00',
        'seconds' => '00'
        );

    $end_date_array = array(
        'year' => date( 'Y' ),
        'mon' => date( 'm' ),
        'mday' => date( 'd' ),
        'hours' => ( date( 'H' ) + 1 > 23 ? 23 : date( 'H' ) + 1 ),
        'minutes' => '00',
        'seconds' => '00'
        );
    
    require_once('innowork/calendar/InnoworkEvent.php');
    $freq_array = array();
    $freq_array[0]= $gLocale->getStr( 'nofrequency.label' );
    $freq_array[InnoworkEvent::FREQUENCY_DAILY]= $gLocale->getStr( 'daily.label' );
    $freq_array[InnoworkEvent::FREQUENCY_WEEKLY]= $gLocale->getStr( 'weekly.label' );
    $freq_array[InnoworkEvent::FREQUENCY_MONTHLY]= $gLocale->getStr( 'monthly.label' );
    $freq_array[InnoworkEvent::FREQUENCY_YEARLY]= $gLocale->getStr( 'yearly.label' );


    if ( isset($eventData['year'] ) )
    {
        $start_date_array['year'] = $end_date_array['year'] = $eventData['year'];
    }

    if ( isset($eventData['mon'] ) )
    {
        $start_date_array['mon'] = $end_date_array['mon'] = $eventData['mon'];
    }

    if ( isset($eventData['mday'] ) )
    {
        $start_date_array['mday'] = $end_date_array['mday'] = $eventData['mday'];
    }

    if ( isset($eventData['hours'] ) )
    {
        $start_date_array['hours'] = $eventData['hours'];
        $end_date_array['hours'] = $eventData['hours'] + 1;

        if ( $end_date_array['hours'] > 23 )
        {
            $end_date_array['hours'] = 23;
        }
    }

    if ( isset($eventData['minutes'] ) )
    {
        $start_date_array['minutes'] = $end_date_array['minutes'] = $eventData['minutes'];
    }

    if (
        isset($eventData['wholeday'] )
        and $eventData['wholeday'] == 'true'
        )
    {
        $start_date_array['hours'] = $end_date_array['hours'] = '00';
        $start_date_array['minutes'] = $end_date_array['minutes'] = '00';
        $start_date_array['seconds'] = $end_date_array['seconds'] = '00';
    }

    $gXml_def .=
'<vertgroup>
  <children>

    <table><name>project</name>
      <args>
        <headers type="array">'.WuiXml::encode(
            array( '0' => array(
                'label' => $gLocale->getStr( 'newevent.label' )
                ) ) ).'</headers>
      </args>
      <children>
      
    <form row="0" col="0"><name>event</name>
      <args>
        <method>post</method>
        <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'showevent',
                    ''
                    ),
                array(
                    'action',
                    'newevent',
                    '' )
            ) ) ).'</action>
      </args>
      <children>

        <horizgroup><name>event</name>
          <children>

            <label><name>description</name>
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'description.label' ) ).'</label>
              </args>
            </label>

          </children>
        </horizgroup>

        <horizgroup><name>event</name>
          <children>

            <text><name>description</name>
              <args>
                <disp>action</disp>
                <cols>80</cols>
                <rows>3</rows>
                <required>true</required>
                <checkmessage type="encoded">'.urlencode( $gLocale->getStr( 'description.required' ) ).'</checkmessage>
              </args>
            </text>

          </children>
        </horizgroup>

        <horizgroup><name>notes</name>
          <children>

            <label><name>description</name>
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'notes.label' ) ).'</label>
              </args>
            </label>

          </children>
        </horizgroup>

        <horizgroup><name>event</name>
          <children>

            <text><name>notes</name>
              <args>
                <disp>action</disp>
                <cols>80</cols>
                <rows>5</rows>
              </args>
            </text>

          </children>
        </horizgroup>';

        if ( $innowork_directory_installed )
        {
            $gXml_def .=
'            <label>
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'company.label' ) ).'</label>
              </args>
            </label>

            <combobox><name>companyid</name>
              <args>
                <disp>action</disp>
                <elements type="array">'.WuiXml::encode( $gCompanies ).'</elements>
                <default>'.( isset($eventData['companyid'] ) ? $eventData['companyid'] : '' ).'</default>
              </args>
            </combobox>';
     	}
     	
        $gXml_def .='
        <label><name>estimated</name>
          <args>
            <bold>true</bold>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'estimated.label' ) ).'</label>
          </args>
        </label>

        <horizgroup><name>date</name>
          <children>

            <label><name>startdate</name>
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'startdate.label' ) ).'</label>
              </args>
            </label>
            <date><name>startdate</name>
              <args>
                <disp>action</disp>
                <value type="array">'.WuiXml::encode( $start_date_array ).'</value>
              </args>
            </date>

            <label><name>starttime</name>
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'starttime.label' ) ).'</label>
              </args>
            </label>
            <date><name>starttime</name>
              <args>
                <disp>action</disp>
                <type>time</type>
                <value type="array">'.WuiXml::encode( $start_date_array ).'</value>
              </args>
            </date>

            <label><name>enddate</name>
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'enddate.label' ) ).'</label>
              </args>
            </label>
            <date><name>enddate</name>
              <args>
                <disp>action</disp>
                <value type="array">'.WuiXml::encode( $end_date_array ).'</value>
              </args>
            </date>

            <label><name>endtime</name>
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'endtime.label' ) ).'</label>
              </args>
            </label>
            <date><name>endtime</name>
              <args>
                <disp>action</disp>
                <type>time</type>
                <value type="array">'.WuiXml::encode( $end_date_array ).'</value>
              </args>
            </date>			
			
          </children>
        </horizgroup>

        <horizgroup><name>event</name>
            <children>
                <label><name>frequency</name>
                  <args>
                   <label type="encoded">'.urlencode( $gLocale->getStr( 'frequency.label' ) ).'</label>
                  </args>
                </label>
                <combobox><name>frequency</name>
    			  <args>
	    			<disp>action</disp>
        			<elements type="array">'.WuiXml::encode( $freq_array ).'</elements>
					<default>'.$freq_array['0'].'</default>
    			  </args>
				</combobox>
				    
                <label><name>interv</name>
                  <args>
                    <label type="encoded">'.urlencode( $gLocale->getStr( 'interv.label' ) ).'</label>
                  </args>
                </label>
                <string><name>interv</name>
    			  <args>
    				<size>3</size>
	    			<disp>action</disp>
        			<value type="encoded">'.urlencode( $gLocale->getStr( 'default.interv.label' ) ).'</value>
				  </args>
				</string>
 				
              </children>
            </horizgroup>
		
        </children>
        </form>

        <button row="1" col="0"><name>apply</name>
          <args>
            <themeimage>buttonok</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                    array(
                        'view',
                        'showevent',
                        ''
                        ),
                    array(
                        'action',
                        'newevent',
                        '' )
                ) ) ).'</action>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'new.submit' ) ).'</label>
            <formsubmit>event</formsubmit>
          </args>
        </button>

      </children>
    </table>
  </children>
</vertgroup>';
}

$gMain_disp->addEvent(
    'showevent',
    'main_showevent'
    );
function main_showevent( $eventData )
{
    global $gXml_def, $gLocale, $gPage_title, $gCompanies, $innowork_directory_installed;

    if ( isset($GLOBALS['innowork-calendar']['neweventid'] ) )
    {
        $eventData['id'] = $GLOBALS['innowork-calendar']['neweventid'];
    }

    require_once('innowork/calendar/InnoworkEvent.php');
    $innowork_event = new InnoworkEvent(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess(),
        $eventData['id']
        );

    $ev_data = $innowork_event->getItem( \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId() );
    
    $freq_array = array();
    $freq_array[0]= $gLocale->getStr( 'nofrequency.label' );
    $freq_array[InnoworkEvent::FREQUENCY_DAILY]= $gLocale->getStr( 'daily.label' );
    $freq_array[InnoworkEvent::FREQUENCY_WEEKLY]= $gLocale->getStr( 'weekly.label' );
    $freq_array[InnoworkEvent::FREQUENCY_MONTHLY]= $gLocale->getStr( 'monthly.label' );
    $freq_array[InnoworkEvent::FREQUENCY_YEARLY]= $gLocale->getStr( 'yearly.label' );


    $gXml_def .=
'<horizgroup><name>event</name>
  <children>

    <table><name>event</name>
      <args>
        <headers type="array">'.WuiXml::encode(
            array( '0' => array(
                'label' => $gLocale->getStr( 'event.label' )
                ) ) ).'</headers>
      </args>
      <children>

    <form row="0" col="0"><name>event</name>
      <args>
        <method>post</method>
        <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'default',
                    ''
                    ),
                array(
                    'action',
                    'editevent',
                    array( 'id' => $eventData['id'] ) )
            ) ) ).'</action>
      </args>
      <children>

        <horizgroup><name>event</name>
          <children>
          
            <label><name>description</name>
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'description.label' ) ).'</label>
              </args>
            </label>

          </children>
        </horizgroup>

        <horizgroup><name>event</name>
          <children>
          
            <text><name>description</name>
              <args>
                <disp>action</disp>
                <cols>80</cols>
                <rows>3</rows>
                <required>true</required>
                <checkmessage type="encoded">'.urlencode( $gLocale->getStr( 'description.required' ) ).'</checkmessage>
                <value type="encoded">'.urlencode( $ev_data['description'] ).'</value>
              </args>
            </text>

          </children>
        </horizgroup>

        <horizgroup><name>event</name>
          <children>

            <label><name>notes</name>
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'notes.label' ) ).'</label>
              </args>
            </label>

          </children>
        </horizgroup>

        <horizgroup><name>event</name>
          <children>

            <text><name>notes</name>
              <args>
                <disp>action</disp>
                <cols>80</cols>
                <rows>5</rows>
                <value type="encoded">'.urlencode( $ev_data['notes'] ).'</value>
              </args>
            </text>

          </children>
        </horizgroup>';

       if ( $innowork_directory_installed )
       {
            $gXml_def .='
			<label>
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'company.label' ) ).'</label>
              </args>
            </label>

            <combobox><name>companyid</name>
              <args>
                <disp>action</disp>
                <elements type="array">'.WuiXml::encode( $gCompanies ).'</elements>
                <default>'.$ev_data['companyid'].'</default>
              </args>
            </combobox>';
            }
            
            $gXml_def .='
        <horizgroup><name>date</name>
          <children>

            <label><name>startdate</name>
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'startdate.label' ) ).'</label>
              </args>
            </label>
            <date><name>startdate</name>
              <args>
                <disp>action</disp>
                <value type="array">'.WuiXml::encode(
                    \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()->getDateArrayFromTimestamp(
                        $ev_data['startdate'] ) ).'</value>
              </args>
            </date>

            <label><name>starttime</name>
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'starttime.label' ) ).'</label>
              </args>
            </label>
            <date><name>starttime</name>
              <args>
                <disp>action</disp>
                <type>time</type>
                <value type="array">'.WuiXml::encode(
                    \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()->getDateArrayFromTimestamp(
                        $ev_data['startdate'] ) ).'</value>
              </args>
            </date>

            <label><name>enddate</name>
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'enddate.label' ) ).'</label>
              </args>
            </label>
            <date><name>enddate</name>
              <args>
                <disp>action</disp>
                <value type="array">'.WuiXml::encode(
                    \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()->getDateArrayFromTimestamp(
                        $ev_data['enddate'] ) ).'</value>
              </args>
            </date>

            <label><name>endtime</name>
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'endtime.label' ) ).'</label>
              </args>
            </label>
            <date><name>endtime</name>
              <args>
                <disp>action</disp>
                <type>time</type>
                <value type="array">'.WuiXml::encode(
                    \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()->getDateArrayFromTimestamp(
                        $ev_data['enddate'] ) ).'</value>
              </args>
            </date>

          </children>
        </horizgroup>
           
            <horizgroup><name>event</name>
               <children>
                 <label><name>frequency</name>
                    <args>
                      <label type="encoded">'.urlencode( $gLocale->getStr( 'frequency.label' ) ).'</label>
                    </args>
                 </label>
                <combobox><name>frequency</name>
    				<args>
	    			  <disp>action</disp>
        			  <elements type="array">'.WuiXml::encode( $freq_array ).'</elements>
        			  <default>'.$ev_data['frequency'].'</default>
    				</args>
				</combobox>
				    
                <label><name>interv</name>
                    <args>
                     <label type="encoded">'.urlencode( $gLocale->getStr( 'interv.label' ) ).'</label>
                    </args>
                </label>

                <string><name>interv</name>
    				<args>
    				  <size>3</size>
	    			  <disp>action</disp>
        			  <value type="encoded">'.urlencode( $ev_data['interv'] ).'</value>
					</args>
				</string>
				
               </children>
             </horizgroup>

        </children>
      </form>

        <horizgroup row="1" col="0"><name>tools</name>
          <children>

        <button>
          <args>
            <themeimage>buttonok</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                    array(
                        'view',
                        'showevent',
                        array(
                            'id' => $eventData['id']
                            )
                        ),
                    array(
                        'action',
                        'editevent',
                        array( 'id' => $eventData['id'] ) )
                ) ) ).'</action>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'apply.submit' ) ).'</label>
            <formsubmit>event</formsubmit>
          </args>
        </button>

        <button>
          <args>
            <themeimage>fileclose</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                    array(
                        'view',
                        'default',
                        ''
                        )
                ) ) ).'</action>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'close.button' ) ).'</label>
          </args>
        </button>

        <button><name>remove</name>
          <args>
            <themeimage>trash</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <needconfirm>true</needconfirm>
            <confirmmessage type="encoded">'.urlencode( $gLocale->getStr( 'event_remove.confirm' ) ).'</confirmmessage>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                    array(
                        'view',
                        'default',
                        ''
                        ),
                    array(
                        'action',
                        'removeevent',
                        array( 'id' => $eventData['id'] ) )
                ) ) ).'</action>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'remove.button' ) ).'</label>
            <formsubmit>event</formsubmit>
          </args>
        </button>

          </children>
        </horizgroup>

      </children>
    </table>

  <innoworkitemacl><name>itemacl</name>
    <args>
      <itemtype>event</itemtype>
      <itemid>'.$eventData['id'].'</itemid>
      <itemownerid>'.$ev_data['ownerid'].'</itemownerid>
      <defaultaction type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
        array( 'view', 'showevent', array( 'id' => $eventData['id'] ) ) ) ) ).'</defaultaction>
    </args>
  </innoworkitemacl>
  </children>
</horizgroup>';
}

$gMain_disp->Dispatch();

// ----- Rendering -----
//
$gWui->addChild( new WuiInnomaticPage( 'page', array(
    'pagetitle' => $gPage_title,
    'icon' => 'calendar',
    'toolbars' => array(
        new WuiInnomaticToolbar(
            'view',
            array(
                'toolbars' => $gToolbars, 'toolbar' => 'true'
                ) ),
        new WuiInnomaticToolBar(
            'core',
            array(
                'toolbars' => $gCore_toolbars, 'toolbar' => 'true'
                ) ),
            ),
    'maincontent' => new WuiXml(
        'page', array(
            'definition' => $gXml_def
            ) ),
    'status' => $gPage_status
    ) ) );

$gWui->render();

?>
