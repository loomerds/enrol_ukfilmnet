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
$students_cohort_id = create_cohort_if_not_existing('students');
$prohibit = CAP_PROHIBIT;
$capabilities_to_change = [
                                ['enrol/manual:enrol', $prohibit],
                                ['enrol/flatfile:manage', $prohibit]
];
//print_r2($capabilities_to_change);
$ukfnteacher_role_id = create_role_if_not_existing_and_update_role_permissions('UKfn Teacher', 'ukfnteacher', 'A role for all UKfilmNet teachers - based on Moodle\'s editingteacher role, but more restritive', 'editingteacher', $capabilities_to_change);
