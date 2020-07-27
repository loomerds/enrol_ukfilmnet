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

require_login();
$context = context_system::instance();
if(!has_capability('moodle/role:manage', $context)) {
    redirect(PAGE_WWWROOT);
}
$PAGE->set_context(context_system::instance());

// HANDLE DELETION OF STALE CLASSROOM COURSES - this would be a good place to put code to do  this, but DOM wants to handle stale course deletions manually for now as he doesn't know what criteria he will use for when they should be deleted - this functionality was not within the scope of this plugin's development criteria

// HANDLE DELETION OF COHORTS THAT ARE NOT ASSOCIATED WITH ANY COURSES

// Get a list of all course short names
$course_shortnames = ['applicants', 'students', 'resource_courses', 'support_courses'];
$courses = $DB->get_records('course');
foreach($courses as $course) {
    $course_shortnames[] = $course->shortname;
}

// Get a list of all cohorts and delete cohorts if their associated classroom course does not exist
$cohorts = $DB->get_records('cohort');
foreach($cohorts as $cohort) {
    if(!in_array($cohort->idnumber, $course_shortnames)) {
        cohort_delete_cohort($cohort);
    }
}

// HANDLE DELETION OF TEMPORARY SGO ACCOUNTS 

// Deletes all SGO accounts that are more than 2 hours old - NOTE this is not a complete purge of the user records - complete deletion/purge of a user account must be handled with the built-in functionality at Site administration > Users > Privacy and policies - see https://docs.moodle.org/39/en/GDPR for information about how to use Moodle's Privacy and policies functionality

// Get a list of all temp SGO accounts
$temp_sgo_accounts = $DB->get_records('user', array('firstname'=>'Safeguarding', 'deleted'=>0));

// If the user account was created more than the time value of the plugin language string ago, delete it
foreach($temp_sgo_accounts as $account) {
    if((int)$account->timecreated < ((int)strtotime(date('Y-m-d H:i:s')) - get_string('sgo_temp_account_max_life', 'enrol_ukfilmnet'))) {
        delete_user($account);
    }
}

// HANDLE DELETION OF ACCOUNTS THAT ARE NOT ASSOCIATED WITH ANY COHORTS (but don't delete temp SGO accounts, admin accounts, or the guest account)

// Get a list of all users who are not temp SGO's, do not have admin rights, and are not the guest user
$all_users = $DB->get_records('user');
$non_sgo_admin_guest_users = [];
foreach($all_users as $user) {
    if($user->firstname !== 'Safeguarding' and !(has_capability('moodle/role:manage', $context, $user)) and $user->username !== 'guest' and $user->deleted != 1) {
        $non_sgo_admin_guest_users[] = $user;
    }
}

// For each user in the list of users who are not temp SGO's, guests, or admins, if they are not a memeber of any cohort, soft delete their account
$all_cohorts = $DB->get_records('cohort');
foreach($non_sgo_admin_guest_users as $user) {
    $has_a_cohort = false;
    foreach($all_cohorts as $cohort) {
        if(cohort_is_member($cohort->id, $user->id)) {
            $has_a_cohort = true;
            break;
        }
    }
    if($has_a_cohort == false) {
        delete_user($user);
    }
}

// HANDLE REMOVING STALE APPLICANT TEACHER ACCOUNTS

// Get a list of users in the applicant cohort
$applicants_cohort_id = create_cohort_if_not_existing('applicants');
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

