<?php

function la_calendar_shortcode( $atts = [], $content = null, $tag = '' ) {
	// normalize attribute keys, lowercase

	$atts = array_change_key_case( (array) $atts, CASE_LOWER );


	// override default attributes with user attributes
	$wporg_atts = shortcode_atts(
		array('year' => gmdate("Y"),), $atts, $tag
	);

	// start table
	$o = 
	'<table class="content_table">
    <thead>
		<tr>
			<th>Nazwa</th> <th style="width:5em">Data</th><th style="width:15%">Miejsce</th><th>Pliki</th><th style="width:15%; text-align:center">Odnośniki</th><th style="width:3em">Wyniki</th>
		</tr>
    </thead>
    <tbody>';
	global $wpdb;
	$table = $wpdb->get_results("
	SELECT *, day(`date_start`) AS s_day, month(`date_start`) AS s_month, day(`date_end`) AS e_day, month(`date_end`) AS e_month, year(`date_start`) AS year
	FROM {$wpdb->prefix}la_competitions
	WHERE year(`date_start`)={$wporg_atts['year']}
	ORDER BY `date_start`
	", OBJECT);

	foreach ( $table as $record ) {
		$year = $record->year;
		$o.= '<tr>';

		$values = array();

		////////////////////////////////////////// Name ////////////////////////////////////////////////////////
		if($record->rel_post == NULL)
			$values[] = $record->name;
		else
			$values[]= '<a href="'. esc_url($record->rel_post) . '">' . stripslashes($record->name). '</a>';

		///////////////////////////////////////// Date //////////////////////////////////////////////////////////
		if($record->s_day<10)
			$record->s_day = "0".$record->s_day;
		if($record->s_month<10)
			$record->s_month = "0".$record->s_month;

		if($record->e_day!=NULL)
		{
			if($record->e_day<10)
				$record->e_day = "0".$record->e_day;
			if($record->e_month<10)
				$record->e_month = "0".$record->e_month;
		}
		
		if($record->e_day != NULL)
		{
			$tmp = $record->s_day;
			if($record->e_month == $record->s_month)
				$tmp .= " - ".$record->e_day.".".$record->s_month;
			else
				$tmp .= ".".$record->s_month." - ".$record->e_day.".".$record->e_month;
			$values[]=$tmp;
		}
		else
			$values[]= $record->s_day.".".$record->s_month;

		///////////////////////////////////////// City ///////////////////////////////////////////////////////////
		$values[] = $record->city;
		

		///////////////////////////////////////// Files //////////////////////////////////////////////////////////
		// Name [link]
		$pattern = "/([^\[]+)\[([^\[\]]+)\]/i";
		$tmp = "";
		if($record->files != NULL)
			if(preg_match_all($pattern, $record->files, $matches)) {
				for($i = 0; $i < count($matches[0]);$i+=1)
				{
					$tmp .= "<a href=\"".esc_url($matches[2][$i])."\" target=\"_blank\">{$matches[1][$i]}</a>";
					if($i+1 < count($matches[0]))
						$tmp .= "<br/>";
				}
			}
		$values[] = $tmp;

		

		///////////////////////////////////////// Links ///////////////////////////////////////////////////////////
		//starter
		//live_results	
		//livestream
		$tmp = '<div class="links_flex">';
		if($record->pzla != NULL)
			$tmp .= '<a href="'.esc_url($record->pzla).'" title="Kalendarz PZLA" target="_blank"><img src="'.plugins_url('public/img/pzla.ico', 'calendar-LA/calendar-LA.php' ).'"/></a>';
		if($record->starter != NULL)
			$tmp .= '<a href="'.esc_url($record->starter).'" title="Starter" target="_blank"><img src="'.plugins_url('public/img/starter.png', 'calendar-LA/calendar-LA.php' ).'"/></a>';
		if($record->live_results != NULL)
			$tmp .= '<a href="'.esc_url($record->live_results).'" title="Wyniki na żywo" target="_blank"><img src="'.plugins_url('public/img/domtel.png', 'calendar-LA/calendar-LA.php' ).'"/></a>';
		if($record->livestream != NULL)
			$tmp .= '<a href="'.esc_url($record->livestream).'" title="Transmisja" target="_blank"><img src="'.plugins_url('public/img/youtube.svg', 'calendar-LA/calendar-LA.php' ).'"/></a>';
		if($record->organizator != NULL)
			$tmp .= '<a href="'.esc_url($record->organizator).'" title="Organizator zawodów" target="_blank">Organizator</a>';
		$tmp .= "</div>";

		$values[]= $tmp;

		////////////////////////////////////////// Results /////////////////////////////////////////////////////////

		if($record->results)
			$values[] = "<a href=\"".esc_url($record->results)."\" class=\"results\">link</a>";

		foreach($values as $v)
			$o.='<td>'. $v .'</td>';

		$o.= '</tr>';

	}

	// end table
	$o .= '
	</tbody>
	</table>
	';

    $o .= '<br><img class="l-calendar-icons" src="'.plugins_url('public/img/pzla.ico', 'calendar-LA/calendar-LA.php' ).'"/> - Kalendarz PZLA <br>
    <img class="la-calendar-icons" src="'.plugins_url('public/img/starter.png', 'calendar-LA/calendar-LA.php' ).'"/> - Starter - zgłoszenia <br>
    <img class="la-calendar-icons" src="'.plugins_url('public/img/domtel.png', 'calendar-LA/calendar-LA.php' ).'"/> - Domtel Wyniki na żywo <br>
    <img class="la-calendar-icons" src="'.plugins_url('public/img/youtube.svg', 'calendar-LA/calendar-LA.php' ).'"/> - Transmisja Live';


	wp_enqueue_style('la_calendar',plugins_url('public/css/la_calendar.css', 'calendar-LA/calendar-LA.php' ));
	wp_enqueue_script('la_calendar_resize',plugins_url('public/js/la_calendar_resize.js', 'calendar-LA/calendar-LA.php' ));

	// return output
	return $o;
}

function la_results_shortcode( $atts = [], $content = null, $tag = '' ) {
	// normalize attribute keys, lowercase

	$atts = array_change_key_case( (array) $atts, CASE_LOWER );


	// override default attributes with user attributes
	$wporg_atts = shortcode_atts(
		array(), $atts, $tag
	);
	$o = "";
	
	// start table

	
	// <tr>
	// <td>9-11.02.2024</td>
	// <td><a href="https://www.mzla.pl/wp-content/uploads/2024/02/pozla-04.02-rezultaty.pdf">PZLA Halowe Mistrzostwa Polski U18 i U20 2024</a></td>
	// <td>Wrocław</td>
	// </tr>
	global $wpdb;
	$years = $wpdb->get_results("
	SELECT DISTINCT year(`date_start`)
	FROM {$wpdb->prefix}la_competitions
	ORDER BY year(`date_start`) DESC
	", ARRAY_N);
	
	foreach ( $years as $y ) {
		$year = $y[0];
		$o .= 
		"<table class=\"res_table\"><caption>Wyniki imprez lekkoatletycznych w {$year} roku:</caption>
		<tbody>
		<tr>
		<th>Termin</th>
		<th>Nazwa imprezy</th>
		<th>Miejsce</th>
		</tr>";


		global $wpdb;
		$table = $wpdb->get_results("
		SELECT `name`, `city`, day(`date_start`) AS s_day, month(`date_start`) AS s_month, day(`date_end`) AS e_day, month(`date_end`) AS e_month, `results`
		FROM {$wpdb->prefix}la_competitions
		WHERE year(`date_start`)={$year} AND results IS NOT NULL
		ORDER BY `date_start` DESC 
		", OBJECT);
	
		foreach ( $table as $record ) {
			$o.= '<tr>';
	
			$values = array();

			if($record->s_day<10)
				$record->s_day = "0".$record->s_day;
			if($record->s_month<10)
				$record->s_month = "0".$record->s_month;

			if($record->e_day!=NULL)
			{
				if($record->e_day<10)
					$record->e_day = "0".$record->e_day;
				if($record->e_month<10)
					$record->e_month = "0".$record->e_month;
			}
	
			////////////////////////////////////////// Date ////////////////////////////////////////////////////////
			if($record->e_day != NULL)
			{
				$tmp = $record->s_day;
				if($record->e_month == $record->s_month)
					$tmp .= "-".$record->e_day.".".$record->s_month.".".$year;
				else
					$tmp .= ".".$record->s_month."-".$record->e_day.".".$record->e_month.".".$year;
				$values[]=$tmp;
			}
			else
				$values[]= $record->s_day.".".$record->s_month.".".$year;
			

			///////////////////////////////////////// Name //////////////////////////////////////////////////////////
			$values[] = "<a href=\"".esc_url($record->results)."\">{$record->name}</a>";
	
			///////////////////////////////////////// City ///////////////////////////////////////////////////////////
			$values[] = $record->city;
			
	
			foreach($values as $v)
				$o.='<td>'. $v .'</td>';
	
			$o.= '</tr>';
		}
		$o .= "</tbody> </table>";
	}
	wp_enqueue_style('la_calendar_results.css',plugins_url('public/css/la_calendar_results.css', 'calendar-LA/calendar-LA.php' ));

	return $o;
}


function la_calendar_shortcodes_init() {
	add_shortcode( 'kalendarz_la', 'la_calendar_shortcode' );
	add_shortcode( 'rezultaty_la', 'la_results_shortcode' );
}
?>