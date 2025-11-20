<?php
defined( 'BMLT_EXEC' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

// Database settings:
$dbType = 'mysql'; // This is the PHP PDO driver name for your database.

// Location and Map settings:
$region_bias = 'us'; // This is a 2-letter code for a 'region bias,' which helps Google Maps to figure out ambiguous search queries.
$search_spec_map_center = array ( 'longitude' => -79.793701171875, 'latitude' => 36.06575205170711, 'zoom' => 10 ); // This is the default map location for new meetings.
$comdef_distance_units = 'mi';

// Display settings:
$bmlt_title = 'BMLT Administration'; // This is the page title and heading for the main administration login page.
$banner_text = 'Administration Login'; // This is text that is displayed just above the login box on the main login page.

// Miscellaneous settings:
$comdef_global_language ='en'; // This is the 2-letter code for the default root server localization (will default to 'en' -English, if the localization is not available).
$number_of_meetings_for_auto = 10; // This is an approximation of the number of meetings to search for in the auto-search feature. The higher the number, the wider the radius.
$change_depth_for_meetings = 5; // This is how many changes should be recorded for each meeting. The higher the number, the larger the database will grow, as this can become quite substantial.
$default_duration_time = '1:00:00'; // This is the default duration for meetings that have no duration specified.
$g_enable_language_selector = TRUE; // Set this to TRUE (or 1) to enable a popup on the login screen that allows the administrator to select their language.
$g_include_service_body_email_in_semantic = FALSE; //Set this to TRUE (or 1) to include including Service body contact emails in the semantic response
$g_defaultClosedStatus = TRUE;   // If this is FALSE (or 0), then the default (unspecified) Open/Closed format for meetings reported to NAWS is OPEN. Otherwise, it is CLOSED.
