<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Development data generator.
 *
 * @package    enrol_ukfilmnet
 * @copyright  2019, Doug Loomer 
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $USER, $DB;
require(__DIR__ .'/../../config.php');
require_once('./signuplib.php');
require_once('../../cohort/lib.php');
require_once('../../lib/moodlelib.php');

require_login();
$context = context_system::instance();
if(!has_capability('moodle/role:manage', $context)) {
    redirect(PAGE_WWWROOT);
}

/*
 * Handle application deletion and warning(s) of deletion
 * 
 * 
 */

// Get the cohort id for the cohort with an idnumber of 'applicants' (create the corhort if it does not yet exist)
$applicants_cohort_id = create_cohort_if_not_existing('applicants');

// Get a list of users in the applicant cohort
$applicants = [];
$all_users = $DB->get_records('user');
foreach($all_users as $user) {
    if(cohort_is_member($applicants_cohort_id, $user->id)) {
        $applicants[] = $user;
    }
}

// Remove applicant from the applicants cohort if application is older than application_max_life allows, otherwise send warning email if appropriate
$current_unix_date = (int)strtotime(date('Y-m-d H:i:s'));
$application_max_life = (int)ceil(get_string('application_account_max_life', 'enrol_ukfilmnet') / 86400);
//$application_max_life = 4; // for testing only
$application_reminder_interval = (int)ceil(get_string('application_reminder_interval', 'enrol_ukfilmnet') / 86400);
//$application_reminder_interval = 1; // for testing only
foreach($applicants as $applicant) {
    $application_start_date = (int)$applicant->timecreated;
    $application_days_elapsed = (int)ceil(($current_unix_date - $application_start_date) / 86400);
    $send_reminder = (ceil($application_days_elapsed % $application_reminder_interval)) == 0 ? 'true' : 'false';
    $application_period_end_date = $application_start_date + 86400*28;
    $application_period_end_date_formated = date('M/d/Y', $application_period_end_date);

    if($application_days_elapsed >= $application_max_life) {
    //if(true) { //for testing only
        $emailvariables = (object) array(
            'firstname'=>$applicant->firstname,
            'ukfilmnet_url'=>PAGE_WWWROOT.get_string('ukfilmnet_url', 'enrol_ukfilmnet'));
    
        email_to_user($applicant, get_admin(), get_string('application_deleted_subject', 'enrol_ukfilmnet', $emailvariables), get_string('application_deleted_text', 'enrol_ukfilmnet', $emailvariables));
        $applicants_cohort_id = get_cohort_id_from_cohort_idnumber('applicants');
        cohort_remove_member($applicants_cohort_id, $applicant->id);
    }
    elseif($send_reminder === 'true') {
    //elseif(true) { //for testing only
        $emailvariables = (object) array(
            'firstname'=>$applicant->firstname,
            'ukfilmnet_url'=>PAGE_WWWROOT.get_string('ukfilmnet_url', 'enrol_ukfilmnet'), 'application_period_end_date'=>$application_period_end_date_formated);

        email_to_user($applicant, get_admin(), get_string('application_warning_subject', 'enrol_ukfilmnet', $emailvariables), get_string('application_warning_text', 'enrol_ukfilmnet', $emailvariables));
    }
}
