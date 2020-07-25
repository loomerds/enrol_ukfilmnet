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
$not_set = null;
$allow = CAP_ALLOW;
$prevent = CAP_PREVENT;
$prohibit = CAP_PROHIBIT;

// Array of capabilities to be changed in order to restrict ukfnteacher permissions - modify this array and run this script to add further restrictions or ease restrictions
$ukfnteacher_capabilities_to_change = [
                                ['enrol/manual:enrol', $prohibit],
                                ['enrol/flatfile:manage', $prohibit],
                                ['enrol/paypal:manage', $prohibit],
                                ['enrol/self:manage', $prohibit],
                                ['enrol/apply:manage', $prohibit],
                                ['enrol/manual:unenrol', $prohibit],
                                ['enrol/flatfile:unenrol', $prohibit],
                                ['enrol/self:unenrol', $prohibit],
                                ['enrol/lti:unenrol', $prohibit],
                                ['enrol/apply:unenrol', $prohibit],
                                ['moodle/course:reviewotherusers', $prohibit],
                                ['moodle/grade:viewall', $prohibit],
                                ['moodle/role:assign', $prohibit],
                                ['enrol/apply:config', $prohibit],
                                ['enrol/apply:manageapplications', $prohibit],
                                ['enrol/category:config', $prohibit],
                                ['enrol/cohort:config', $prohibit],
                                ['enrol/database:config', $prohibit],
                                ['enrol/guest:config', $prohibit],
                                ['enrol/imsenterprise:config', $prohibit],
                                ['enrol/lti:config', $prohibit], //
                                ['enrol/meta:config', $prohibit],
                                ['enrol/mnet:config', $prohibit],
                                ['enrol/self:config', $prohibit],
                                ['moodle/course:enrolconfig', $prohibit],
                                ['moodle/course:enrolreview', $prohibit],
                                ['moodle/cohort:view', $prohibit],
                                ['moodle/course:changecategory', $prohibit],
                                ['moodle/course:changefullname', $prohibit],
                                ['moodle/course:changeshortname', $prohibit],
                                ['moodle/course:renameroles', $prohibit],
                                ['moodle/question:editall', $prohibit],
                                ['moodle/question:moveall', $prohibit],
                                ['moodle/question:tagall', $prohibit],
                                ['moodle/rating:viewall', $prohibit],
                                ['moodle/rating:viewany', $prohibit],
                                ['moodle/role:review', $prohibit],
                                ['moodle/role:safeoverride', $prohibit],
                                ['moodle/user:viewhiddendetails', $prohibit],
                                ['mod/assign:manageallocations', $prohibit],
                                ['mod/chat:deletelog', $prohibit],
                                ['mod/feedback:createpublictemplate', $prohibit],
];

$ukfnteacher_context_types = [CONTEXT_COURSE, CONTEXT_MODULE];

$ukfnteacher_role_id = create_role_if_not_existing_and_update_role_permissions('UKfn Teacher', 'ukfnteacher', 'A role for all UKfilmNet teachers - based on Moodle\'s editingteacher role, but more restritive', 'editingteacher', $ukfnteacher_capabilities_to_change, $ukfnteacher_context_types, [], [], ['student'],[]);
