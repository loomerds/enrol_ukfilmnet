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
global $USER, $DB, $CFG;
require(__DIR__ .'/../../config.php');
require_once('./signuplib.php');
require_once('../../cohort/lib.php');
require_once('../../lib/moodlelib.php');
require_once('../../course/lib.php');
require_once('../../blocks/moodleblock.class.php');
require_once('../../blocks/html/block_html.php');
//use block_html;

require_login();
$context = context_system::instance();
$PAGE->set_context($context);
if(!has_capability('moodle/role:manage', $context)) {
    redirect(PAGE_WWWROOT);
}

/*
 * 
 * 
 */

// Ensure that the Cohort sync enrolment plugin is enabled
$enabled = enrol_get_plugins(true);
if(!in_array('cohort', $enabled)) {
    $enabled['cohort'] = true;
    $enabled = array_keys($enabled);
    set_config('enrol_plugins_enabled', implode(',', $enabled));
}

// Ensure that the Manual enrolment plugin is enabled
$enabled = enrol_get_plugins(true);
if(!in_array('manual', $enabled)) {
    $enabled['manual'] = true;
    $enabled = array_keys($enabled);
    set_config('enrol_plugins_enabled', implode(',', $enabled));
}

// Create needed corhorts if they do not yet exist, and get their ids
$applicants_cohort_id = create_cohort_if_not_existing('applicants');
$students_cohort_id = create_cohort_if_not_existing('students');
$resource_courses_cohort_id = create_cohort_if_not_existing('resource_courses');
$support_courses_cohort_id = create_cohort_if_not_existing('support_courses');

// Create permission type variables
$not_set = null;
$allow = CAP_ALLOW;
$prevent = CAP_PREVENT;
$prohibit = CAP_PROHIBIT;

// Array of capabilities to be changed in order to restrict ukfnteacher role permissions - modify this array and run this script to add further restrictions or ease restrictions
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

$ukfnteacher_role_id = create_role_if_not_existing_and_update_role_permissions('UKfilmNet Teacher', 'ukfnteacher', 'A role for all UKfilmNet teachers - based on Moodle\'s editingteacher role, but more restritive', 'editingteacher', $ukfnteacher_capabilities_to_change, $ukfnteacher_context_types, [], [], ['student'],[]);

// Array of capabilities to be changed in order to augment applicant role permissions beyond those of its user role prototype - modify this array and run this script to  further expand or restrict applicant role permissions
$applicant_capabilities_to_change = [
    ['message/airnotifier:managedevice', $allow],
    ['mod/folder:managefiles', $allow],
    ['mod/label:view', $allow],
    ['mod/page:view', $allow],
    ['mod/url:view', $allow],
    ['moodle/block:view', $allow],
    ['moodle/user:changeownpassword', $allow],
    ['moodle/webservice:createmobiletoken', $allow],
    ['report/usersessions:manageownsessions', $allow],
    ['repository/areafiles:view', $allow],
    ['repository/filesystem:view', $allow],
    ['repository/upload:view', $allow],
    ['repository/url:view', $allow],
    ['tool/dataprivacy:requestdelete', $allow],
    ['tool/policy:accept', $allow]
];

$applicant_context_types = [CONTEXT_SYSTEM, CONTEXT_USER];

$applicant_role_id = create_role_if_not_existing_and_update_role_permissions('UKfilmNet applicant', 'applicant', 'A role for all UKfilmNet educator access applicants - based on Moodle\'s Authenticated user role, but less restrited', 'user', $applicant_capabilities_to_change, $applicant_context_types, [], [], [],[]);

// Lock the email field for all authentication plugins
$plugin_objects = $DB->get_records('config_plugins', array('name'=>'field_lock_email'));

// Create the classrooms and DFM categories programmatically

$classrooms_category_data = array(
    'name'=>get_string('classrooms_category_name', 'enrol_ukfilmnet'),
    'idnumber'=>get_string('classrooms_category_idnumber', 'enrol_ukfilmnet'),
    'parent'=>intval(get_string('classrooms_category_parent', 'enrol_ukfilmnet')),
    'descriptionformat'=>0,
    'description'=>get_string('classrooms_category_description', 'enrol_ukfilmnet'),
);

$classrooms_category_exists = $DB->get_record('course_categories', array('idnumber'=>get_string('classrooms_category_idnumber', 'enrol_ukfilmnet')));
if(!$classrooms_category_exists) {
    $classrooms_category = core_course_category::create($classrooms_category_data);
}

$dfm_category_data = array(
    'name'=>get_string('dfm_category_name', 'enrol_ukfilmnet'),
    'idnumber'=>get_string('dfm_category_idnumber', 'enrol_ukfilmnet'),
    'parent'=>intval(get_string('dfm_category_parent', 'enrol_ukfilmnet')),
    'descriptionformat'=>0,
    'description'=>get_string('dfm_category_description', 'enrol_ukfilmnet'),
);

$dfm_category_exists = $DB->get_record('course_categories', array('idnumber'=>get_string('dfm_category_idnumber', 'enrol_ukfilmnet')));
if(!$dfm_category_exists) {
    $dfm_category = core_course_category::create($dfm_category_data);
}

// Create a Classroom Course template with shortname of classroom_course_template if one does not already exist
$classroom_course_template_data = (object) array(
    'category'=>$DB->get_record('course_categories', array('idnumber'=>get_string('template_course_category', 'enrol_ukfilmnet')))->id,
    'sortorder'=>0,
    'fullname'=>get_string('template_course_fullname', 'enrol_ukfilmnet'),
    'shortname'=>get_string('template_course_shortname', 'enrol_ukfilmnet'),
    'idnmber'=>null,
    'summary'=>get_string('template_course_summary', 'enrol_ukfilmnet'),
    'summaryformat'=>0,
    'format'=>get_string('template_course_format', 'enrol_ukfilmnet'),
    'visible'=>intval((get_string('template_course_visibility', 'enrol_ukfilmnet'))),
);

$course_exists = $DB->get_record('course', array('shortname'=>get_string('template_course_shortname', 'enrol_ukfilmnet')));
if(!$course_exists) {
    $classroom_course_template = create_course($classroom_course_template_data);
}
$ukfn_sg_user = create_ukfnsafeguarding_user($auth = 'manual');


// Create a UKFN Enrol Admin Options block if it doesn't exist
$ukfn_enrol_admin_block_configdata = 'Tzo4OiJzdGRDbGFzcyI6Mzp7czo1OiJ0aXRsZSI7czoyNDoiVUtGTiBFbnJvbCBBZG1pbiBPcHRpb25zIjtzOjY6ImZvcm1hdCI7czoxOiIxIjtzOjQ6InRleHQiO3M6NDUxOiI8cD48L3A+DQo8dWw+DQogICAgPGxpIHN0eWxlPSJ0ZXh0LWFsaWduOiBsZWZ0OyI+PGEgaHJlZj0iL2Vucm9sL3VrZmlsbW5ldC90cmFja2luZy5waHAiPlZpZXcgU2lnbi11cCBQcm9ncmVzczwvYT48L2xpPg0KPC91bD4NCjxkaXYgY2xhc3M9ImVkaXRvci1pbmRlbnQiIHN0eWxlPSJtYXJnaW4tbGVmdDogMzBweDsiPjxwPiZuYnNwOyAmbmJzcDsgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS08L3A+PC9kaXY+DQo8dWw+DQogICAgPGxpPjxhIGhyZWY9Ii9lbnJvbC91a2ZpbG1uZXQvZW5yb2xfdWtmbl9wbHVnaW5fc2V0dXAucGhwIj5SdW4gUGx1Z2luIFNldC11cCBTY3JpcHRzPC9hPjwvbGk+DQogICAgPGxpPjxhIGhyZWY9Ii9lbnJvbC91a2ZpbG1uZXQvZW5yb2xfdWtmbl91c2Vyc19jbGVhbnVwX2Nyb24ucGhwIj5SdW4gVXNlcnMgQ2xlYW51cCBTY3JpcHQ8L2E+PC9saT4NCjwvdWw+Ijt9';
$htmlblock_instances = $DB->get_records('block_instances', array('blockname'=>'html'));
$instance_exists = false;
foreach($htmlblock_instances as $instance) {
    if($instance->configdata === $ukfn_enrol_admin_block_configdata) {  
        $instance_exists = true;
        break;
    }
}

if(!$instance_exists) {
    $html_blockinstance = new stdClass;
    $html_blockinstance->blockname = 'html';
    $html_blockinstance->parentcontextid = 1;
    $html_blockinstance->showinsubcontexts = 1;
    $html_blockinstance->pagetypepattern = 'admin-search';
    $html_blockinstance->subpagepattern = null;
    $html_blockinstance->defaultregion = 'side-nav';
    $html_blockinstance->defaultweight = 3;
    $html_blockinstance->configdata = $ukfn_enrol_admin_block_configdata;
    $html_blockinstance->timecreated = time();
    $html_blockinstance->timemodified = $html_blockinstance->timecreated;
    $html_blockinstance->id = $DB->insert_record('block_instances', $html_blockinstance);
    context_block::instance($html_blockinstance->id);

    // If the new instance was created, allow it to do additional setup
    if ($block = block_instance($html_blockinstance->blockname, $html_blockinstance)) {
        $block->instance_create();
    }
}

// Create custom profile fields if they do not yet exist

// Create user_info_category "UKfilmNet Applicant Info" if it doesn't exist
$ukfn_applicant_info_category_id;
if($DB->record_exists('user_info_category', array('name'=>get_string('ukfn_user_info_category_name', 'enrol_ukfilmnet')))) {
    $ukfn_applicant_info_category_id = $DB->get_record('user_info_category', array('name'=>get_string('ukfn_user_info_category_name', 'enrol_ukfilmnet')))->id;
} else {
    $count = 0;
    $categories = $DB->get_records('user_info_category');
    foreach($categories as $category) {
        if($category->sortorder > $count) {
            $count = $category->sortorder;
        }
    }
    $applicant_category = new stdClass();
    $applicant_category->name = get_string('ukfn_user_info_category_name', 'enrol_ukfilmnet');
    $applicant_category->sortorder = $count + 1;

    $ukfn_applicant_info_category_id = $DB->insert_record('user_info_category', $applicant_category);
}

// Save the user_info_field table records to a file in the assets folder - do this only when you want to update that file because the user_info_field records have changed
/*
$array = $DB->get_records('user_info_field');
$encodedString = json_encode($array);
file_put_contents('./assets/ukfn_applicant_info_field_array.txt', $encodedString);
*/

// Get the existing UKfilmNet Applicants user_info_field array from file
$ukfn_applicant_info_field_array = file_get_contents('./assets/ukfn_applicant_info_field_array.txt');
$ukfn_applicant_info_field_array = json_decode($ukfn_applicant_info_field_array, true);

// Insert the UKfilmNet Applicants user_info_field records into the user_info_field table - deleting/replacing exiting table records as needed
foreach($ukfn_applicant_info_field_array as &$record) {
    // Make sure that the categoryid field of each record matches that of the UKfilmnet Applicant Info table's id 
    $record['categoryid'] = $ukfn_applicant_info_category_id;
    // If the record to be inserted does not conflict with existing records in the user_info_field, add the record - delete conflicting records
    $shortnames = $DB->get_records('user_info_field', array('shortname'=>$record['shortname']));
    if(!$shortnames) {
        $DB->insert_record('user_info_field', $record);
    } else {
        foreach($shortnames as $shortname) {
            if($shortname->categoryid !== $record['categoryid']) {
                $DB->delete_records('user_info_field', array('shortname'=>$record['shortname'], 'categoryid'=>$shortname->categoryid));
            }
        }
    }
}

// Let the Moodle admin know that the setup script ran
echo(get_string('setup_script_run_confirmation', 'enrol_ukfilmnet'));
