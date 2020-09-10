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
 * @copyright  2020, Doug Loomer 
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

// Restore all users who have been suspended after some unix date/time

/*$all_users = $DB->get_records('user');
$count = 0;
foreach($all_users as $user) {
    if($user->suspended == 1 AND $user->timemodified > intval('1598640970') ) {

        $count++;
        $DB->set_field('user', 'suspended', 0, array('id'=>$user->id));
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
}*/



//go_to_page('admin');