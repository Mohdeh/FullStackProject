<!-- my co.php inside new-react-sandbox-master/src/co.php -->

<?php
/*
AUTHOR: MOHSEN DEHHAGHI
PURPOSE: Global conf file.  Provides abstraction for DMA, Registrar, Search and Reservation tables and PHP pages.
20060330:  updated and split into three files:  style.php, functions.php, co.php
*/
// Global conf file
$conf=file('/home/www/include/conf.txt');

// echo "<pre>"; print_r($conf);  echo "</pre>";

// read each line of the conf file and match hostname, username, etc. to their appropriate values
foreach ($conf as $line) {
	// echo $line."<br>";
	$incoming = explode(" ",$line);
	/* get all relevant db names and login info */
	if	($incoming[0]=="db_dev_host")	 	$db_dev_host = trim($incoming[1]);	
	else if ($incoming[0]=="db_dev_user")		$db_dev_user = trim($incoming[1]);
	else if ($incoming[0]=="db_dev_pass")		$db_dev_pass = trim($incoming[1]);
	else if ($incoming[0]=="db_dmauser")		$db_dmauser = trim($incoming[1]);
	else if ($incoming[0]=="db_print")		$db_print = trim($incoming[1]);
	else if ($incoming[0]=="db_support")		$db_support = trim($incoming[1]);
	else if ($incoming[0]=="db_reservation")	$db_reservation = trim($incoming[1]);
	else if ($incoming[0]=="db_events")		$db_events = trim($incoming[1]);
	else if ($incoming[0]=="db_charge")		$db_charge = trim($incoming[1]);
  else if ($incoming[0]=="db_live_host")                $db_live_host = trim($incoming[1]);
  else if ($incoming[0]=="db_live_user")                $db_live_user = trim($incoming[1]);
  else if ($incoming[0]=="db_live_pass")                $db_live_pass = trim($incoming[1]);

	/* registrar db items have two naming convetions :( */
	else if ($incoming[0]=="registrar_dsn")	{
	  $registrar_dsn = trim($incoming[1]);
	  $dsn = $registrar_dsn;
	}
	else if ($incoming[0]=="registrar_usr") {
	  $registrar_usr = trim($incoming[1]);
	  $reg_user = $registrar_usr;
	}
	else if ($incoming[0]=="registrar_pass") {
	  $registrar_pass = trim($incoming[1]);
	  $pwd = $registrar_pass;
	}
	
	else if ($incoming[0]=="homedir_host")		$homedir_host = trim($incoming[1]);

	/* LDAP stuff */
	else if ($incoming[0]=="ldap_URI")		$ldap_URI = trim($incoming[1]);
	else if ($incoming[0]=="ldap_host")		$ldap_host = trim($incoming[1]);
	else if ($incoming[0]=="ldap_basedn")		$ldap_suffix = trim($incoming[1]);
	else if ($incoming[0]=="ldap_usrs")		$ldap_usrs = trim($incoming[1]);
	else if ($incoming[0]=="ldap_group")		$ldap_group = trim($incoming[1]);
	else if ($incoming[0]=="ldap_rootdn")		$ldap_rootdn = trim($incoming[1]);
	else if ($incoming[0]=="ldap_rootpwd")		$ldap_rootpwd = trim($incoming[1]);
	
	/* updated manually in conf.txt each semester */
	else if ($incoming[0]=="quarter")		$exp_quarter = trim($incoming[1]);

	else if ($incoming[0]=="pwchangescript")	$pwchangescript = trim($incoming[1]);
	else if ($incoming[0]=="dump")			$dump = trim($incoming[1]);
	// ISIS is no longer used
	// else if ($incoming[0]=="ISISloginURL")	$ISISloginURL = trim($incoming[1]);
}

// testing
// echo "in co.php, these variables are declared: host / $ldap_host, suffix / $ldap_suffix, base / $ldap_base, usrs / $ldap_usrs, group / $ldap_group <br /><br />"; 
// echo 'base: '.$ldap_base.' usrs,group,suffix: '.$ldap_usrs.','.$ldap_group.','.$ldap_suffix.'<br>';

// Registrar database connection
// $linkid = odbc_connect($dsn,$reg_user,$pwd) or die ("Couldn't connect mssql<br>");

// DMA database connection (switch between odbc and mysql methods)
// $linkid1 = odbc_connect("dmauserlocal", "itstaff", "5taff") or die ("ODBC connect failed itstaffdev itstaff");
// $linkid1 = mysql_connect($db_host, $db_user, $db_pass) or die ("MYSQL connect failed (localhost itstaffdev db)");
// $reserve_link = odbc_connect($reservation_db, $sys_dbuser, $sys_dbpasswd) or die ("ODBC connect to reservation failed");
// OLD mysql direct for dev
// $linkid2 = mysql_connect("directory.design.ucla.edu", "devtest", "5taff") or die ("MYSQL connect failed (directory dmauser)"); 

$env_conf = file('/home/www/include/support_conf.txt');
foreach ($env_conf as $line){
  $line = explode(" ", $line);
  $$line[0] = isset($line[1]) ? $line[1] : "";
}
// DMA User table names

$tbl_users			= $db_dmauser.".users";
$tbl_classes			= $db_dmauser.".classes";
$tbl_users_in_classes		= $db_dmauser.".users_in_classes";
$tbl_userpages			= $db_dmauser.".users_userpages";
$tbl_valid_classes		= $db_dmauser.".valid_classes";
$tbl_perm_users			= $db_dmauser.".permanent_users";
$tbl_temp_specialaccess		= $db_dmauser.".temp_specialaccess";
$tbl_audio_specialaccess	= $db_dmauser.".audio_specialaccess";
$tbl_video_specialaccess	= $db_dmauser.".video_specialaccess";
$tbl_lab_specialaccess		= $db_dmauser.".lab_specialaccess";
$tbl_users_in_groups		= $db_dmauser.".users_in_groups";
$tbl_group_types		= $db_dmauser.".group_types";
$tbl_id_types 			= $db_dmauser.".id_types";
$tbl_fall_users 		= $db_dmauser.".fall_users";
$tbl_current_term_users		= $db_dmauser.".current_term_users";
$tbl_alumni 			= $db_dmauser.".alumni";
$tbl_class_locations		= $db_dmauser.".class_locations";
$tbl_workshops 			= $db_dmauser.".workshops";
$tbl_users_in_workshops		= $db_dmauser.".users_in_workshops";
$tbl_user_privilege     	= $db_dmauser.".user_privilege";
$tbl_users_with_privileges	= $db_dmauser.".users_with_privileges";
$tbl_group_with_privileges	= $db_dmauser.".group_with_privileges";
// $tbl_menu_section		= $db_dmauser.".menu_section";
// $tbl_rooms			= $db_dmauser.".rooms"; redefined later in events table
// $tbl_cons_colors                = $db_dmauser.".cons_colors";
$tbl_valid_users                = $db_dmauser.".VALID_USERS";
$tbl_omnilock                   = $db_dmauser.".omnilock";

// Charge tables names
$tbl_adhoc			= $db_charge.".adhoc";
$tbl_balances			= $db_charge.".balances";
$tbl_charge_bar			= $db_charge.".chargeBAR";
$tbl_charge_cash		= $db_charge.".chargeCash";
$tbl_recharge			= $db_charge.".chargeRecharge";
$tbl_fablab_materials_fee       = $db_charge.".FABLAB_MATERIALS_FEE";
$tbl_fablab_lasercut            = $db_charge.".FABLAB_LASERCUT";
$tbl_rechargees                 = $db_charge.".rechargees";
$tbl_shootroom			= $db_charge.".shootroom";
$tbl_edacheckout                = $db_charge.".edacheckout";
$tbl_fablab			= $db_charge.".fablab";
$tbl_charge_type		= $db_charge.".charge_type";
$tbl_quarter			= $db_charge.".quarter";
$tbl_billing_type               = $db_charge.".billing_type";
$tbl_subcodes                   = $db_charge.".subcodes";
// this has two names for some reason?
$view_charged	 		= $db_charge.".CHARGED_TO_BAR";
$view_charged_to_bar 		= $db_charge.".CHARGED_TO_BAR";
$view_recharges                 = $db_charge.".RECHARGES";

// Print tables names
$tbl_print_alias		= $db_print.".aliases";
$tbl_jobs_cups			= $db_print.".jobCUPS";
$tbl_jobs_plotter		= $db_print.".jobPlotter";
$tbl_queued_job_plotter         = $db_print.".queuedJobPlotter";
$tbl_paper			= $db_print.".paper";
$tbl_paper_type                 = $db_print.".paper_type";
$tbl_papertype_papersize        = $db_print.".paperType_paperSize";
$tbl_paper_size                 = $db_print.".paper_size";
$tbl_paper_with_printer		= $db_print.".paper_with_printer";
$tbl_printer			= $db_print.".printer";
$tbl_printer_printable_paper    = $db_print.".printer_printablePaper";
$tbl_dispute			= $db_print.".dispute";
$tbl_lasercut			= $db_print.".jobLasercut";
$tbl_consumable			= $db_print.".consumable";
$tbl_consumable_type		= $db_print.".consumable_type";
$tbl_cons_notpaper              = $db_print.".cons_notpaper";
$tbl_cons_print                 = $db_print.".cons_print";
// Views
$view_print_cons                = $db_print.".PRINT_CONSUMABLES";
$view_consumable                = $db_print.".CONSUMABLES";
$view_printlab_plotter          = $db_print.".PRINTLAB_PLOTTER";

// Reservation table names
$tbl_checkout			= $db_reservation.".checkout";
$tbl_contract			= $db_reservation.".contract";
$tbl_item		  	= $db_reservation.".item";
$tbl_package			= $db_reservation.".package";
$tbl_equipment			= $db_reservation.".equipment";
$tbl_package_type		= $db_reservation.".package_type";
$tbl_package_in_classes		= $db_reservation.".package_in_classes";
$tbl_package_special_user	= $db_reservation.".package_special_user";
$tbl_reservation		= $db_reservation.".reservation";
$tbl_package_status		= $db_reservation.".package_status";	
$tbl_item_eda  		  	= $db_reservation.".item_eda";
$tbl_package_eda		= $db_reservation.".package_eda";
$tbl_available_inventory        = $db_reservation.".AVAILABLE_INVENTORY";
$tbl_current_checkouts          = $db_reservation.".CURRENT_CHECKOUTS";
$tbl_current_reservations       = $db_reservation.".CURRENT_RESERVATIONS";
$tbl_item_type                  = $db_reservation.".item_type";
$tbl_rental_history             = $db_reservation.".RENTAL_HISTORY";
$tbl_shootroom_fee              = $db_reservation.".SHOOTROOM_FEE";
$tbl_damage_status              = $db_reservation.".damage_status";

// Event table names
$tbl_rooms                      = $db_events.".rooms";
$tbl_room_events                = $db_events.".room_events";
$tbl_buildings                  = $db_events.".buildings";
$tbl_class_hours_loc            = $db_events.".CLASS_HOURS_AND_LOCATIONS";
$tbl_events                     = $db_events.".events";
$tbl_event_rec                  = $db_events.".event_rec";
$tbl_event_types                = $db_events.".event_types";
$tbl_events_with_types          = $db_events.".events_with_types";
$tbl_quarters                   = $db_events.".quarters";
$tbl_recurrence                 = $db_events.".recurrence";
$tbl_consultant_hours           = $db_events.".consultant_hours";
$tbl_holidays                   = $db_events.".holidays";
$tbl_lab_hours                  = $db_events.".lab_hours";

// Support table names
$tbl_menu_section		= $db_support.".menu_section";
$tbl_cons_colors                = $db_support.".cons_colors";
$tbl_download			= $db_support.".download";
$tbl_download_category		= $db_support.".download_category";
$tbl_faq_answer			= $db_support.".faq_answer";
$tbl_faq_category		= $db_support.".faq_category";
$tbl_faq_headline		= $db_support.".faq_headline";
$tbl_how_to			= $db_support.".how_to";
$tbl_assets			= $db_support.".assets";
$tbl_notifications		= $db_support.".notifications";
$tbl_software_list		= $db_support.".software_info";

// tables for notifications
$tbl_notif_type                 = $db_support.".notif_type";
$tbl_notifs                     = $db_support.".notifs";
$tbl_user_notif_pref            = $db_support.".user_notif_pref";
$tbl_notif_templates            = $db_support.".notif_templates";

// holidays and labhours are now in events and should not be here
$tbl_holiday			= $db_support.".holiday";
$tbl_labhours			= $db_support.".labhours";
$tbl_software			= $db_support.".software";
$tbl_software_license	  	= $db_support.".software_license";

// Views
$view_running_totals		= $db_charge.".RUNNING_TOTALS";
$view_charged_to_bar		= $db_charge.".CHARGED_TO_BAR";
$view_quarter_totals            = $db_charge.".QUARTER_TOTALS";
$view_current_term_classes	= $db_dmauser.".CURRENT_TERM_CLASSES";
$view_rooms_with_groups         = $db_dmauser.".ROOMS_AND_GROUPS";

// Roles
$roles['fo'] = 'hyunjang';
$roles['omnilock'] = 'mpeteu';
$roles['checkout'] = 'mpeteu';
$roles['supplies'] = 'mpeteu';
$roles['charges'] = 'mpeteu';

// Rashaan (search) PHP pages
$search_page="index.php";
$view_page="view.php";
$edit_page="edit.php";
$add_page="add.php";
$add_pending="add_pending_sp.php";
$add_spaccess="add_spaccess.php";
$classes="classes.php";
$class_view="class_view.php";
$make_valid="make_valid.php";
$reports="reports.php";
$result="result.php";
$result_all_classes="resultAllClasses.php";
$result_criteria="result_criteria.php";
$result_classes="resultAllClasses.php";
$result_prof="resultProf.php";
$result_student="resultStudent.php";
$result_ta="resultTA.php";
$result_staff="resultSTAFF.php";
$resultsponsor="rs.php";
$result_class="resultClasses.php";
$result_validusers="resultValidUsers.php";
$result_temp_sp="resultTempSpecialAccess.php";
$result_sp_access="resultSpecialAccess.php";
$spaccess_view="spaccess_view.php";
$special_access="special_access.php";
$specialaccess_view="specialaccess_view.php";
?>
