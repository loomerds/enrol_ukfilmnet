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

//Note that this is currently deleting all SGO accounts
// Handle deletion of temporary SGO accounts - this function deletes all SGO accounts that are more than 2 hours old - NOTE this is not a complete purge of the user records - complete deletion/purge of a user account must be handled with the built-in functionality at Site administration > Users > Privacy and policies - see https://docs.moodle.org/39/en/GDPR for information about how to use Moodle's Privacy and policies functionality

// get a list of all temp SGO accounts (user firstname === Safeguarding)
$temp_sgo_accounts = $DB->get_records('user', array('firstname'=>'Safeguarding', 'deleted'=>0));

// if the user account was created more than the time value of the plugin language string  ago, delete it
foreach($temp_sgo_accounts as $account) {
print_r2('Username is: '.$account->username);
print_r2('Timecreated is: '.$account->timecreated);
print_r2('Cron run time is: '.strtotime(date('Y-m-d H:i:s')));
print_r2('Cron run time minus timecreated is: '.((int)strtotime(date('Y-m-d H:i:s')) - (int)$account->timecreated));
print_r2('SGO account max life is: '.(int)get_string('sgo_temp_account_max_life', 'enrol_ukfilmnet'));
    if((int)$account->timecreated < ((int)strtotime(date('Y-m-d H:i:s')) - get_string('sgo_temp_account_max_life', 'enrol_ukfilmnet'))) {
        delete_user($account);
    }
}


// Handle deletion of accounts that are not associated with any cohorts, but are not temp SGO accounts, admin accounts, or the guest account

// get a list of all users who are not temp SGO's, do not have admin rights, and are not the guest user
$all_users = $DB->get_records('user');
$non_sgo_admin_guest_users = [];
foreach($all_users as $user) {
    if($user->firstname !== 'Safeguarding' and !(has_capability('moodle/role:manage', $context, $user)) and $user->username !== 'guest') {
        $non_sgo_admin_guest_users[] = $user;
    }
}

// for each user in the list, if they are not a memeber of any cohort, soft delete their account
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

// 


