<?php

/*!
 @class WuiInnoworkcalendar

 @abstract Calendar widget.
 */
class WuiInnoworkcalendar extends WuiWidget
{
    var $mNotes;
    var $mSaveAction;
    var $mDeleteAction;
    var $mNewAction;
    var $mDisp;
    var $mToday;
    var $mDay;
    var $mMonth;
    var $mYear;
    var $mViewBy;
    var $mEvents = array();
    var $mDayBeginHour = '8';
    var $mDayEndHour = '18';
    var $mShift;
    var $mParentShift;
    var $mShowEventBuilderFunction;
    var $mShowDayBuilderFunction;

    function __construct(
        $elemName,
        $elemArgs = '',
        $elemTheme = '',
        $dispEvents = ''
        )
    {
        parent::__construct( $elemName, $elemArgs, $elemTheme, $dispEvents );

        $tmp_sess = $this->RetrieveSession();

        if ( isset($this->mArgs['day'] ) and strlen( $this->mArgs['day'] ) ) $this->mDay = $this->mArgs['day'];
        else if ( isset($tmp_sess['day'] ) and strlen( $tmp_sess['day'] ) ) $this->mDay = $tmp_sess['day'];
        else $this->mDay = date( 'd' );

        if ( isset($this->mArgs['month'] ) and strlen( $this->mArgs['month'] ) ) $this->mMonth = $this->mArgs['month'];
        else if ( isset($tmp_sess['month'] ) and strlen( $tmp_sess['month'] ) ) $this->mMonth = $tmp_sess['month'];
        else $this->mMonth = date( 'n' );

        if ( isset($this->mArgs['year'] ) and strlen( $this->mArgs['year'] ) ) $this->mYear = $this->mArgs['year'];
        else if ( isset($tmp_sess['year'] ) and strlen( $tmp_sess['year'] ) ) $this->mYear = $tmp_sess['year'];
        else $this->mYear = date( 'Y' );

        if ( is_array( $this->mArgs['events'] ) ) $this->mEvents = &$this->mArgs['events'];
        if (
            isset($this->mArgs['showdaybuilderfunction'] )
            and strlen( $this->mArgs['showdaybuilderfunction'] )
            )
            $this->mShowDayBuilderFunction = $this->mArgs['showdaybuilderfunction'];

        if (
            isset($this->mArgs['showeventbuilderfunction'] )
            and strlen( $this->mArgs['showeventbuilderfunction'] )
            )
            $this->mShowEventBuilderFunction = $this->mArgs['showeventbuilderfunction'];

        if (
            isset($this->mArgs['shift'] )
            and strlen( $this->mArgs['shift'] )
            )
        {
            switch( $this->mArgs['shift'] )
            {
            case 'previous':
                $this->mShift = $this->mArgs['shift'];
                break;

            case 'next':
                $this->mShift = $this->mArgs['shift'];
                break;
            }
        }
        if (
            isset($this->mArgs['parentshift'] )
            and strlen( $this->mArgs['parentshift'] )
            )
        {
            switch( $this->mArgs['parentshift'] )
            {
            case 'parentprevious':
                $this->mParentShift = $this->mArgs['parentshift'];
                break;

            case 'parentnext':
                $this->mParentShift = $this->mArgs['parentshift'];
                break;
            }
        }

        if (
            isset($this->mArgs['viewby'] )
            and strlen( $this->mArgs['viewby'] )
            )
        {
            switch( $this->mArgs['viewby'] )
            {
            case 'day':
            case 'businessweek':
            case 'week':
            case 'month':
            case 'flatmonth':
                $this->mViewBy = $this->mArgs['viewby'];
                break;
            }
        }
        else if ( isset($tmp_sess['viewby'] ) and strlen( $tmp_sess['viewby'] ) ) $this->mViewBy = $tmp_sess['viewby'];
        else $this->mViewBy = 'month';

        switch( $this->mViewBy )
        {
        case 'day':
            switch ( $this->mShift )
            {
            case 'previous':
                $this->mDay--;
                if ( $this->mDay <= 0 )
                {
                    $this->mMonth--;
                    $this->mDay = date( 't', mktime( 0, 0, 0, $this->mMonth, $this->mDay + 1, $this->mYear ) );
                }
                break;
            case 'next':
                $this->mDay++;
                if ( $this->mDay > date( 't', mktime( 0, 0, 0, $this->mMonth, $this->mDay - 1, $this->mYear ) ) )
                {
                    $this->mDay = 1;
                    $this->mMonth++;
                }
                break;
            }
            
            switch ( $this->mParentShift )
            {
            case 'parentprevious':
                $this->mDay -= 7;
                if ( $this->mDay <= 0 )
                {
                    $this->mMonth--;
                    $this->mDay = date( 't', mktime( 0, 0, 0, $this->mMonth, $this->mDay, $this->mYear ) ) - ( 7 - ( $this->mDay + 7 ) );
                }
                break;
            case 'parentnext':
                $this->mDay += 7;
                if ( $this->mDay > date( 't', mktime( 0, 0, 0, $this->mMonth, $this->mDay, $this->mYear ) ) )
                {
                    $this->mDay = 7 - ( date( 't', mktime( 0, 0, 0, $this->mMonth, $this->mDay, $this->mYear ) ) - ( $this->mDay - 7 ) );

                    $this->mMonth++;
                }
                break;
            }
          break;

        case 'businessweek':
        case 'week':
            switch ( $this->mShift )
            {
            case 'previous':
                $this->mDay -= 7;
                if ( $this->mDay <= 0 )
                {
                    $this->mMonth--;
                    $this->mDay = date( 't', mktime( 0, 0, 0, $this->mMonth, $this->mDay, $this->mYear ) ) - ( 7 - ( $this->mDay + 7 ) );
                }
                break;
            case 'next':
                $this->mDay += 7;
                if ( $this->mDay > date( 't', mktime( 0, 0, 0, $this->mMonth, $this->mDay, $this->mYear ) ) )
                {
                    $this->mDay = 7 - ( date( 't', mktime( 0, 0, 0, $this->mMonth, $this->mDay, $this->mYear ) ) - ( $this->mDay - 7 ) );

                    $this->mMonth++;
                }
                break;
            }
             
            switch ( $this->mParentShift )
            {
            case 'parentprevious':
                $this->mMonth--;
                if ( $this->mMonth == 0 )
                {
                    $this->mMonth = 12;
                    $this->mYear--;
                }
                break;
            case 'parentnext':
                $this->mMonth++;
                if ( $this->mMonth == 13 )
                {
                    $this->mMonth = 1;
                    $this->mYear++;
                }
                break;
            }
            break;

        case 'month':
        case 'flatmonth':
            switch ( $this->mShift )
            {
            case 'previous':
                $this->mMonth--;
                if ( $this->mMonth == 0 )
                {
                    $this->mMonth = 12;
                    $this->mYear--;
                }
                break;
            case 'next':
                $this->mMonth++;
                if ( $this->mMonth == 13 )
                {
                    $this->mMonth = 1;
                    $this->mYear++;
                }
                break;
            }

            switch ( $this->mParentShift )
            {
             case 'parentprevious':
                $this->mYear--;
                break;
             case 'parentnext':
                $this->mYear++;
                break;
            }
            break;
        }

        $this->StoreSession(
            array(
                'viewby' => $this->mViewBy,
                'day' => $this->mDay,
                'month' => $this->mMonth,
                'year' => $this->mYear
                )
            );

        if ( isset($this->mArgs['newaction'] ) ) $this->mNewAction = $this->mArgs['newaction'];
        if ( isset($this->mArgs['disp'] ) ) $this->mDisp = $this->mArgs['disp'];

        /*$this->mSaveAction = $this->mArgs['saveaction'];
        $this->mDeleteAction = $this->mArgs['deleteaction'];
        $this->mDisp = $this->mArgs['disp'];

        if ( is_array( $this->mArgs['notes'] ) ) $this->mNotes = $this->mArgs['notes'];

        if ( $this->mArgs['viewmode'] == 'byicon'
            or
            $this->mArgs['viewmode'] == 'bynote' )
            $this->mViewMode = $this->mArgs['viewmode'];
        else $this->mViewMode = 'byicon';*/
    }

    protected function generateSource()
    {
        $result = true;

        $this->mLayout .=
'<script language="JavaScript">
<!--
linkopened = false
sendevent = \'\'

function innoworkcalendarnewevent(event)
{
    sendevent = event
    setTimeout( "sendinnoworkcalendarevent()", 100 )
}

function sendinnoworkcalendarevent()
{
if ( linkopened == false ) window.location = sendevent
}

function innoworkcalendarlinkopened()
{
    linkopened = true
}
-->
</script>'."\n";

        require_once('innomatic/locale/LocaleCatalog.php'); require_once('innomatic/locale/LocaleCountry.php'); 
        

        $locale = new LocaleCatalog(
            'innowork-calendar::calendar_widget',
            \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getLanguage()
            );

        $locale_country = new LocaleCountry(
            \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getCountry()
            );

        /*
        if ( $this->mMonth == 1 ) $prev_month = 12;
        else $prev_month = $this->mMonth - 1;

        if ( $prev_month == 12 ) $prev_year = $this->mYear - 1;
        else $prev_year = $this->mYear;

        if ( $this->mMonth == 12 ) $next_month = 1;
        else $next_month = $this->mMonth + 1;

        if ( $next_month == 1 ) $next_year = $this->mYear + 1;
        else $next_year = $this->mYear;
        */

        //Assign timestamps to dates

        $today_ts = mktime( 0, 0, 0, $this->mMonth, $this->mDay, $this->mYear );

        $firstday_month_ts = mktime( 0, 0, 0, $this->mMonth, 1, $this->mYear ); // first day of the month
        $lastday_month_ts = mktime( 0, 0, 0, $this->mMonth + 1, 0, $this->mYear );    // last day of the month

        $month_days = date( 't', $firstday_month_ts );

        // raplace day 0 for day 7, week starts on monday
        // This referers to day of the week
        $month_start_day = date( 'w', $firstday_month_ts );
        if ( $month_start_day == 0 ) $month_start_day = 7;

        $month_end_day = date( 'w', $lastday_month_ts );
        if ( $month_end_day == 0 ) $month_end_day = 7;

        $colspan = 2;
        $width = 300;
        $days = 1;

        $current_day[0]['y'] = $this->mYear;
        $current_day[0]['m'] = $this->mMonth;
        $current_day[0]['d'] = $this->mDay;
        $current_day[0]['wd'] = date( 'w', $today_ts ) == 0 ? 6 : date( 'w', $today_ts ) - 1;

        switch ( $this->mViewBy )
        {
        case 'week':
            $day_i_ts = mktime( 0, 0, 0, $this->mMonth, $this->mDay, $this->mYear );
            $day_i = date( 'w', $day_i_ts );

            for ( $i = 0; $i < 7; $i++ )
            {
                $current_day[$i]['m'] = $this->mMonth;
                $current_day[$i]['y'] = $this->mYear;
                $current_day[$i]['d'] = $this->mDay - $day_i + $i + 1;
                $current_day[$i]['wd'] = $i;

                if ( $current_day[$i]['d'] > $month_days )
                {
                    $current_day[$i]['m']++;
                    $current_day[$i]['d'] = $current_day[$i]['d'] - $month_days;

                    if ( $current_day[$i]['m'] > 12 )
                    {
                        $current_day[$i]['m'] = 1;
                        $current_day[$i]['y']++;
                    }
                }

                if ( $current_day[$i]['d'] <= 0 )
                {
                    if ( $current_day[$i]['m'] == 1 )
                    {
                        $current_day[$i]['m'] = 12;
                        $current_day[$i]['y']--;
                    }
                    else
                    {
                        $current_day[$i]['m']--;
                    }

                    $tmp_ts = mktime( 0, 0, 0, $current_day[$i]['m'], 1, $current_day[$i]['y'] );

                    $tmp_month_days = date( 't', $tmp_ts );

                    $current_day[$i]['d'] += $tmp_month_days;
                }
            }

            $colspan = 1;
            $width = 650;
            $days = 7;

            // Continue in 'day' case section

        case 'day':
            $this->mLayout .=
'<table border="0" cellspacing="2" cellpadding="1" width="'.$width.'">
<tr>
<td width="100%" bgcolor="'.$this->mThemeHandler->mColorsSet['tables']['gridcolor'].'">
<table border="0" width="100%" cellspacing="1" cellpadding="3" bgcolor="'.$this->mThemeHandler->mColorsSet['tables']['bgcolor'].'">
<tr>';

            if ( $this->mViewBy == 'week' ) $this->mLayout .= '<td>&nbsp;</td>';

            for ( $i = 0; $i < $days; $i++ )
            {
                $this->mLayout .=
'<td colspan="'.$colspan.'" width="'.( $this->mViewBy == 'week' ? '14%' : '100%' ).'" bgcolor="'.$this->mThemeHandler->mColorsSet['tables']['headerbgcolor'].
'" class="bold" align="center">'.$current_day[$i]['d'].'<br>'.$locale->getStr( 'weekday_'.( $current_day[$i]['wd'] + 1 ).'.label' ).'</td>';
            }

            $this->mLayout .= '</tr>';

            $events = array();
            for ( $i = 0; $i < $days; $i++ )
            {
                while ( list( $id, $event ) = each( $this->mEvents[$current_day[$i]['y']][str_pad( $current_day[$i]['m'], 2, '0', STR_PAD_LEFT )][str_pad( $current_day[$i]['d'], 2, '0', STR_PAD_LEFT )] ) )
                {
                    if (
                        $event['sh'] == $event['eh']
                        and $event['sh'] == '00'
                        and $event['sm'] == $event['em']
                        and $event['sm'] == '00'
                        )
                    {
                        $event['event'] = '- '.$event['event'];
                        $events[str_pad( $current_day[$i]['d'], 2, '0', STR_PAD_LEFT )][''][$id] = $event;
                    }
                    else
                    {
                        $events[str_pad( $current_day[$i]['d'], 2, '0', STR_PAD_LEFT )][$event['sh']][$id] = $event;
                    }
                }
            }

            $this->mLayout .= '<tr>';
            $this->mLayout .= '<td width="0%" bgcolor="'.$this->mThemeHandler->mColorsSet['tables']['bgcolor'].'">'.$locale->getStr( 'whole_day.label' ).'</td>';

            for ( $i = 0; $i < $days; $i++ )
            {
                $this->mLayout .= '<td width="'.( $width / $days ).'" bgcolor="white" class="normalmini" valign="top"'.
                ' onMouseOver="this.style.backgroundColor=\''.$this->mThemeHandler->mColorsSet['buttons']['selected'].'\'" onMouseOut="this.style.backgroundColor=\'white\'"'.
                ' onClick="innoworkcalendarnewevent(\''.$this->mNewAction.
                '&wui['.$this->mDisp.'][evd][year]='.$current_day[$i]['y'].
                '&wui['.$this->mDisp.'][evd][mon]='.$current_day[$i]['m'].
                '&wui['.$this->mDisp.'][evd][mday]='.$current_day[$i]['d'].
                '&wui['.$this->mDisp.'][evd][wholeday]=true'.
                '\')"'.
                '>';

                if ( isset($events[str_pad( $current_day[$i]['d'], 2, '0', STR_PAD_LEFT )][''] ) )
                {
                    while ( list( $id, $event ) = each( $events[str_pad( $current_day[$i]['d'], 2, '0', STR_PAD_LEFT )][''] ) )
                    {
                        $this->mLayout .= '<a onClick="innoworkcalendarlinkopened()" href="'.call_user_func( $this->mShowEventBuilderFunction, $id ).'">'.( $event['type'] == 'public' ? '<i>' : '' ).$event['event'].( $event['type'] == 'public' ? '</i>' : '' ).'</a><br>';

                        if ( $event['type'] == 'public' )
                        {
                            $image = $this->mThemeHandler->mIconsBase.$this->mThemeHandler->mIconsSet['mini']['kuser']['base'].
                                '/mini/'.$this->mThemeHandler->mIconsSet['mini']['kuser']['file'];

                            $this->mLayout .= '<a onClick="innoworkcalendarlinkopened()" href="'.call_user_func( $this->mShowEventBuilderFunction, $id ).'"><img src="'.$image.'" alt="" border="0" style="width: 16px; height: 16px;"></a>';
                        }

                        if ( strlen( $event['notes'] ) )
                        {
                            $image = $this->mThemeHandler->mIconsBase.$this->mThemeHandler->mIconsSet['mini']['txt']['base'].
                                '/mini/'.$this->mThemeHandler->mIconsSet['mini']['txt']['file'];

                            $this->mLayout .= '<a onClick="innoworkcalendarlinkopened()" href="'.call_user_func( $this->mShowEventBuilderFunction, $id ).'"><img src="'.$image.'" alt="" border="0" style="width: 16px; height: 16px;"></a>';
                        }
                         if ( $event['frequency'] )
                        {
                            $image = $this->mThemeHandler->mIconsBase.$this->mThemeHandler->mIconsSet['mini']['reload3']['base'].
                                '/mini/'.$this->mThemeHandler->mIconsSet['mini']['reload3']['file'];

                            $this->mLayout .= '<a onClick="innoworkcalendarlinkopened()" href="'.call_user_func( $this->mShowEventBuilderFunction, $id ).'"><img src="'.$image.'" alt="" border="0" style="width: 16px; height: 16px;"></a>';
                        }						

                        if ( strlen( $event['exticon'] ) )
                        {
                            $image = $this->mThemeHandler->mIconsBase.$this->mThemeHandler->mIconsSet['mini'][$event['exticon']]['base'].
                                '/mini/'.$this->mThemeHandler->mIconsSet['mini'][$event['exticon']]['file'];

                            $this->mLayout .= '<a onClick="innoworkcalendarlinkopened()" href="'.call_user_func( $this->mShowEventBuilderFunction, $id ).'"><img src="'.$image.'" alt="" border="0" style="width: 16px; height: 16px;"></a>';
                        }
                    }
                }
                else $this->mLayout .= '&nbsp;';

                $this->mLayout .= '</td>';
            }

            $this->mLayout .= '</tr>';

            for ( $h = 0; $h < 24; $h++ )
            {
                $this->mLayout .= '<tr>';

                $this->mLayout .= '<td width="0%" bgcolor="'.$this->mThemeHandler->mColorsSet['tables']['bgcolor'].'">'.$h.'</td>';

                for ( $i = 0; $i < $days; $i++ )
                {
                    $this->mLayout .= '<td width="'.$width.'" bgcolor="'.( ( $h >= $this->mDayBeginHour and $h <= $this->mDayEndHour ) ? 'white' : '#fafafa' ).'" class="normalmini" valign="top"'.
                    ' onMouseOver="this.style.backgroundColor=\''.$this->mThemeHandler->mColorsSet['buttons']['selected'].'\'" onMouseOut="this.style.backgroundColor=\''.( ( $h >= $this->mDayBeginHour and $h <= $this->mDayEndHour ) ? 'white' : '#fafafa' ).'\'"'.
                    ' onClick="innoworkcalendarnewevent(\''.$this->mNewAction.
                    '&wui['.$this->mDisp.'][evd][year]='.$current_day[$i]['y'].
                    '&wui['.$this->mDisp.'][evd][mon]='.$current_day[$i]['m'].
                    '&wui['.$this->mDisp.'][evd][mday]='.$current_day[$i]['d'].
                    '&wui['.$this->mDisp.'][evd][hours]='.$h.
                    '\')"'.
                    '>';

                    if ( isset($events[str_pad( $current_day[$i]['d'], 2, '0', STR_PAD_LEFT )][str_pad( $h, 2, '0', STR_PAD_LEFT )] ) )
                    {
                        if ( $days == 1 ) $this->mLayout .= '<table cellpadding="1" cellspacing="0">';

                        while ( list( $id, $event ) = each( $events[str_pad( $current_day[$i]['d'], 2, '0', STR_PAD_LEFT )][str_pad( $h, 2, '0', STR_PAD_LEFT )] ) )
                        {
                            $this->mLayout .= ( $days == 1 ? '<tr><td valign="top" class="normalmini">' : '' ).
                                '<b>'.$event['sh'].'.'.$event['sm'].' - '.$event['eh'].'.'.$event['em'].'</b>'.
                                ( $days == 1 ? '</td><td valign="top" class="normalmini">' : '<br>' ).
                                '<a onClick="innoworkcalendarlinkopened()" href="'.call_user_func( $this->mShowEventBuilderFunction, $id ).'">'.$event['event'].'</a>'.
                                ( $days == 1 ? '</td></tr>' : '<br>' );

                            if ( $event['type'] == 'public' )
                            {
                                $image = $this->mThemeHandler->mIconsBase.$this->mThemeHandler->mIconsSet['mini']['kuser']['base'].
                                    '/mini/'.$this->mThemeHandler->mIconsSet['mini']['kuser']['file'];

                                $this->mLayout .= '<a onClick="innoworkcalendarlinkopened()" href="'.call_user_func( $this->mShowEventBuilderFunction, $id ).'"><img src="'.$image.'" alt="" border="0" style="width: 16px; height: 16px;"></a>';
                            }

                            if ( strlen( $event['notes'] ) )
                            {
                                $image = $this->mThemeHandler->mIconsBase.$this->mThemeHandler->mIconsSet['mini']['txt']['base'].
                                    '/mini/'.$this->mThemeHandler->mIconsSet['mini']['txt']['file'];

                                $this->mLayout .= '<a onClick="innoworkcalendarlinkopened()" href="'.call_user_func( $this->mShowEventBuilderFunction, $id ).'"><img src="'.$image.'" alt="" border="0" style="width: 16px; height: 16px;"></a>';
                            }
                            if ( $event['frequency'] )
                            {
                                $image = $this->mThemeHandler->mIconsBase.$this->mThemeHandler->mIconsSet['mini']['reload3']['base'].
                                    '/mini/'.$this->mThemeHandler->mIconsSet['mini']['reload3']['file'];

                                $this->mLayout .= '<a onClick="innoworkcalendarlinkopened()" href="'.call_user_func( $this->mShowEventBuilderFunction, $id ).'"><img src="'.$image.'" alt="" border="0" style="width: 16px; height: 16px;"></a>';
                            }
                        if ( strlen( $event['exticon'] ) )
                        {
                            $image = $this->mThemeHandler->mIconsBase.$this->mThemeHandler->mIconsSet['mini'][$event['exticon']]['base'].
                                '/mini/'.$this->mThemeHandler->mIconsSet['mini'][$event['exticon']]['file'];

                            $this->mLayout .= '<a onClick="innoworkcalendarlinkopened()" href="'.call_user_func( $this->mShowEventBuilderFunction, $id ).'"><img src="'.$image.'" alt="" border="0" style="width: 16px; height: 16px;"></a>';
                        }
                        }

                        if ( $days == 1 ) $this->mLayout .= '</table>';
                    }
                    else $this->mLayout .= '&nbsp;';

                    $this->mLayout .= '</td>';
                }

                $this->mLayout .= '</tr>';
            }

            $this->mLayout .=
			'</table></td></tr></table>';
            break;

        case 'month':
        case 'flatmonth':
            if ( $this->mViewBy == 'month' )
            {
                $total_width = '525';
                $width = '75';
                $height = '75';
            }
            else
            {
                $total_width = '0%';
                $width = $height = '0%';
            }

            $this->mLayout .=
'<table border="0" cellspacing="2" cellpadding="1" width="'.$total_width.'">
<tr><td width="'.$total_width.'" bgcolor="'.$this->mThemeHandler->mColorsSet['tables']['gridcolor'].'">
<table border="0" width="'.$total_width.'" cellspacing="1" cellpadding="3" bgcolor="'.$this->mThemeHandler->mColorsSet['tables']['bgcolor'].'">
<tr>
<td colspan="7" width="'.$total_width.'" bgcolor="'.$this->mThemeHandler->mColorsSet['tables']['headerbgcolor'].'" class="bold" align="center">'.$locale->getStr( 'month_'.$this->mMonth.'.label' ).' '.$this->mYear.'</td>
</tr>
<tr>
<td bgcolor="white" align="center" width="'.$width.'">'.$locale->getStr( 'weekday'.( $this->mViewBy == 'month' ? '' : '_short' ).'_1.label' ).'</td>
<td bgcolor="white" align="center" width="'.$width.'">'.$locale->getStr( 'weekday'.( $this->mViewBy == 'month' ? '' : '_short' ).'_2.label' ).'</td>
<td bgcolor="white" align="center" width="'.$width.'">'.$locale->getStr( 'weekday'.( $this->mViewBy == 'month' ? '' : '_short' ).'_3.label' ).'</td>
<td bgcolor="white" align="center" width="'.$width.'">'.$locale->getStr( 'weekday'.( $this->mViewBy == 'month' ? '' : '_short' ).'_4.label' ).'</td>
<td bgcolor="white" align="center" width="'.$width.'">'.$locale->getStr( 'weekday'.( $this->mViewBy == 'month' ? '' : '_short' ).'_5.label' ).'</td>
<td bgcolor="white" align="center" width="'.$width.'">'.$locale->getStr( 'weekday'.( $this->mViewBy == 'month' ? '' : '_short' ).'_6.label' ).'</td>
<td bgcolor="white" align="center" width="'.$width.'">'.$locale->getStr( 'weekday'.( $this->mViewBy == 'month' ? '' : '_short' ).'_7.label' ).'</td>
</tr>
<tr>';
            // Fill with white spaces until the first day
            for ( $k=1; $k < $month_start_day; $k++ )
            $this->mLayout .= '<td width="'.$width.'" height="'.$height.'" bgcolor="'.$this->mThemeHandler->mColorsSet['tables']['headerbgcolor'].'">&nbsp;</td>';

            for ( $i = 1; $i <= $month_days; $i++ )
            {
                // Assigns a timestamp to day i
                $day_i_ts = mktime( 0, 0, 0, date( 'n', $firstday_month_ts ), $i, date( 'y', $firstday_month_ts ) );
                $day_i = date( 'w', $day_i_ts );

                if ( $day_i == 0 ) $day_i = 7;

                $content = $pre = $post = '';
                if ( isset($this->mEvents[$this->mYear][str_pad( $this->mMonth, 2, '0', STR_PAD_LEFT )][str_pad( $i, 2, '0', STR_PAD_LEFT )] ) )
                {
                    $pre = '<u>';
                    $post = '</u>';

                    if ( $this->mViewBy == 'month' )
                    {
                        $content .= '<div class="normalmini" align="left">';
                        while ( list( $id, $event ) = each( $this->mEvents[$this->mYear][str_pad( $this->mMonth, 2, '0', STR_PAD_LEFT )][str_pad( $i, 2, '0', STR_PAD_LEFT )] ) )
                        {
                            if (
                                $event['sh'] == $event['eh']
                                and $event['sh'] == '00'
                                and $event['sm'] == $event['em']
                                and $event['sm'] == '00'
                                )
                            {
                                $content .= '<br>- '.( $event['type'] == 'public' ? '<i>' : '' ).'<a onClick="innoworkcalendarlinkopened()" href="'.call_user_func( $this->mShowEventBuilderFunction, $id ).'">'.
                                    $event['event'].'</a>'.( $event['type'] == 'public' ? '</i>' : '' ).'<br>';
                            }
                            else
                            {
                                $content .= '<br>'.( $event['type'] == 'public' ? '<i>' : '' ).
                                    '<b>'.( strlen( $event['sh'] ) ? $event['sh'].'.'.$event['sm'] : '' ).
                                    '</b>'.( $event['type'] == 'public' ? '</i>' : '' ).
                                    ' '.( $event['type'] == 'public' ? '<i>' : '' ).'<a onClick="innoworkcalendarlinkopened()" href="'.call_user_func( $this->mShowEventBuilderFunction, $id ).'">'.
                                    $event['event'].'</a>'.( $event['type'] == 'public' ? '</i>' : '' ).'<br>';
                            }

                            if ( $event['type'] == 'public' )
                            {
                                $image = $this->mThemeHandler->mIconsBase.$this->mThemeHandler->mIconsSet['mini']['kuser']['base'].
                                    '/mini/'.$this->mThemeHandler->mIconsSet['mini']['kuser']['file'];

                                $content .= '<a onClick="innoworkcalendarlinkopened()" href="'.call_user_func( $this->mShowEventBuilderFunction, $id ).'"><img src="'.$image.'" alt="" border="0" style="width: 16px; height: 16px;"></a>';
                            }

                            if ( strlen( $event['notes'] ) )
                            {
                                $image = $this->mThemeHandler->mIconsBase.$this->mThemeHandler->mIconsSet['mini']['txt']['base'].
                                    '/mini/'.$this->mThemeHandler->mIconsSet['mini']['txt']['file'];

                                $content .= '<a onClick="innoworkcalendarlinkopened()" href="'.call_user_func( $this->mShowEventBuilderFunction, $id ).'"><img src="'.$image.'" alt="" border="0" style="width: 16px; height: 16px;"></a>';
                            }
                            if ( $event['frequency'] )
                            {
                                $image = $this->mThemeHandler->mIconsBase.$this->mThemeHandler->mIconsSet['mini']['reload3']['base'].
                                    '/mini/'.$this->mThemeHandler->mIconsSet['mini']['reload3']['file'];

                                $content .= '<a onClick="innoworkcalendarlinkopened()" href="'.call_user_func( $this->mShowEventBuilderFunction, $id ).'"><img src="'.$image.'" alt="" border="0" style="width: 16px; height: 16px;"></a>';
                            }
                        if ( strlen( $event['exticon'] ) )
                        {
                            $image = $this->mThemeHandler->mIconsBase.$this->mThemeHandler->mIconsSet['mini'][$event['exticon']]['base'].
                                '/mini/'.$this->mThemeHandler->mIconsSet['mini'][$event['exticon']]['file'];

                            $content .= '<a onClick="innoworkcalendarlinkopened()" href="'.call_user_func( $this->mShowEventBuilderFunction, $id ).'"><img src="'.$image.'" alt="" border="0" style="width: 16px; height: 16px;"></a>';
                        }

                        }
                        $content .= '</div>';
                    }
                }

                // Target link
                if (
                    $i == date( 'd' )
                    and $this->mMonth == date( 'n' )
                    and $this->mYear == date( 'Y' ) )
                {
                    $this->mLayout .= '<td width="'.$width.
                        '" height="'.$height.
                        '" align="center" valign="top" bgcolor="white"'.
                        ' onMouseOver="this.style.backgroundColor=\''.$this->mThemeHandler->mColorsSet['buttons']['selected'].'\'" onMouseOut="this.style.backgroundColor=\'white\'"'.
                        ' onClick="innoworkcalendarnewevent(\''.$this->mNewAction.
                        '&wui['.$this->mDisp.'][evd][year]='.$this->mYear.
                        '&wui['.$this->mDisp.'][evd][mon]='.$this->mMonth.
                        '&wui['.$this->mDisp.'][evd][mday]='.$i.
                        '&wui['.$this->mDisp.'][evd][hours]=08'.
                        '\')"'.
                        '><a onClick="innoworkcalendarlinkopened()" href="'.
                        call_user_func( $this->mShowDayBuilderFunction, $this->mYear, $this->mMonth, $i ).'"><b>'.
                        $pre.$i.$post.'</b></a>'.( $this->mViewBy == 'month' ? $content : '' ).'</td>';
                }
                else
                {
                    $this->mLayout .= '<td width="'.$width.
                        '" height="'.$height.
                        '" align="center" valign="top" bgcolor="white"'.
                        ' onMouseOver="this.style.backgroundColor=\''.$this->mThemeHandler->mColorsSet['buttons']['selected'].'\'" onMouseOut="this.style.backgroundColor=\'white\'"'.
                        ' onClick="innoworkcalendarnewevent(\''.$this->mNewAction.
                        '&wui['.$this->mDisp.'][evd][year]='.$this->mYear.
                        '&wui['.$this->mDisp.'][evd][mon]='.$this->mMonth.
                        '&wui['.$this->mDisp.'][evd][mday]='.$i.
                        '&wui['.$this->mDisp.'][evd][hours]=08'.
                        '\')"'.
                        '><a onClick="innoworkcalendarlinkopened()" href="'.
                        call_user_func( $this->mShowDayBuilderFunction, $this->mYear, $this->mMonth, $i ).'">'.
                        $pre.$i.$post.'</a>'.( $this->mViewBy == 'month' ? $content : '').'</td>';
                }

                if ( $day_i == 7 and $i < $month_days )
                {
                    $this->mLayout .= "</tr>\n<tr>";
                }
                else if ( $day_i == 7 and $i == $month_days )
                {
                    $this->mLayout .= "</tr>\n";
                }
                else if ( $i == $month_days )
                {
                    for ( $h = $month_end_day; $h < 7; $h++ )
                    {
                        $this->mLayout .= '<td width="'.$width.'" height="'.$height.'" bgcolor="'.$this->mThemeHandler->mColorsSet['tables']['headerbgcolor'].'">&nbsp;</td>';
                    }
                    $this->mLayout .= "</tr>\n";
                }
            }

            $this->mLayout .=
'</table>
</td></tr></table>';
            break;
        }

        return $result;
    }
}

?>