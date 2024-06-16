<?php
/*
* Plugin Name:       LA Calendar
* Description:       Simple track & field calendar with easy edit page
* Version:           0.5
* Author:            Maciej Kopeć
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

require 'public/functions.php';
require 'admin/functions.php';

	
function la_calendar_create_table() {
	global $wpdb;
	$wpdb->query( "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}la_competitions` (
		`id` int NOT NULL AUTO_INCREMENT,
		`name` varchar(150) COLLATE utf8_polish_ci NOT NULL,
		`city` varchar(30) COLLATE utf8_polish_ci DEFAULT '',
		`date_start` date NOT NULL,
		`date_end` date DEFAULT NULL,
		`rel_post` varchar(150) DEFAULT NULL,
		`results` varchar(150) DEFAULT NULL,
		`files` varchar(2000) COLLATE utf8_polish_ci DEFAULT NULL,
		`pzla` varchar(150) DEFAULT NULL,
		`live_results` varchar(150) DEFAULT NULL,
		`livestream` varchar(150) DEFAULT NULL,
		`starter` varchar(150) DEFAULT NULL,
		`organizator` varchar(150) DEFAULT NULL,
		PRIMARY KEY (`id`)
	);" );
}

function la_calendar_setup() {
	la_calendar_shortcodes_init();
} 
add_action( 'init', 'la_calendar_setup' );

register_activation_hook( __FILE__, 'la_calendar_create_table' );

function la_calendar_uninstall()
{
	global $wpdb;
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}la_competitions" );
}
register_uninstall_hook(
	__FILE__,
	'la_calendar_uninstall'
);



?>