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

/* 
 * Handle sending notice of intent to delete application email to applicant.
 * This requires us to:
 * 1) make sure an applicant teacher cohort exists, - done
 * 2) put applicant teachers in that cohort upon account creation, -done
 * 3) send an "intent to delete application 4 weeks after application account was created" email to the applicant if the assurance form has not been submitted by their SGO within two weeks(?) after their applicant account was created, 
 * 4) remove the applicant teacher from the applicant teacher cohort (making the account subject to "no-cohort" deletion) if the assurance form is not submitted within 4 weeks(?) after the date their account was created 
 * 5)make sure  to include an algorithm variable that sends out the intent to delete application email some number of times (between once and daily during the 2 week warning period)
 */

/*
 * Handle application deletion and warning(s) of deletion
 * 
 * 
 */

// Get a list of users in the applicant cohort
$applicants_cohort_id = create_cohort_if_not_existing('applicants');
$applicants = [];
$all_users = $DB->get_records('user');
foreach($all_users as $user) {
    if(cohort_is_member($applicants_cohort_id, $user->id)) {
        $applicants[] = $user;
    }
}
// Delete applicants if application is 4 weeks(?) old

