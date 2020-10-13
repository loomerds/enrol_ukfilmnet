<?php
// This file is part of a 3rd party plugin for the Moodle LMS - http://moodle.org/
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
 * Contains setup scripts to programmatically create the environment and administrative setting necessary for the plugin to work.
 *
 * @package    enrol_ukfilmnet
 * @copyright  2020, Doug Loomer 
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
require_once($CFG->dirroot.'/lib/enrollib.php');

if(!enrol_is_enabled('ukfilmnet')) {
    redirect(PAGE_WWWROOT);
}

require_login();
$context = context_system::instance();
$PAGE->set_context($context);
if(!has_capability('moodle/role:manage', $context)) {
    redirect(PAGE_WWWROOT);
}

// Run setup tasks and display success data
echo('<div style="margin-left: 25px">Setup Script Results<br><div style="margin-left: 25px"><ol>');
    
    // Make sure Cohort Enrolment is enabled
    $cohort_enrolment_enabled = enable_enrolment_plugin('cohort');
    if($cohort_enrolment_enabled) {
        echo('<li>Cohort enrolment is enabled.</li>');
    } else {
        echo('<li>Cohort enrolment is not enabled. <strong>Try running the setup script again, or enable it manually.</strong></li>');
    }

    // Make sure Manual Enrolment is enabled
    $manual_enrolment_enabled = enable_enrolment_plugin('manual');
    if($manual_enrolment_enabled) {
        echo('<li>Manual enrolment is enabled.</li>');
    } else {
        echo('<li>Manual enrolment is not enabled. <strong>Try running the setup script again, or enable it manually.</strong></li>');
    }

    // Create needed corhorts if they do not yet exist, and get their ids
    $applicants_cohort_id = create_cohort_if_not_existing('applicants');
    if(!($applicants_cohort_id < 0)) {
        echo('<li>The Applicants cohort exists.</li>');
    } else {
        echo('>li>The Applicants cohort was not created. <strong>Try running the setup script again.</strong></li>');
    }
    $student_cohort_id = create_cohort_if_not_existing('student');
    if(!($student_cohort_id < 0)) {
        echo('<li>The Student cohort exists.</li>');
    } else {
        echo('<li>The Student cohort was not created. <strong>Try running the setup script again.</strong></li>');
    }
    $resource_courses_cohort_id = create_cohort_if_not_existing('resource_courses');
    if(!($resource_courses_cohort_id < 0)) {
        echo('<li>The Resource courses cohort exists.</li>');
    } else {
        echo('<li>The Resource courses cohort was not created. <strong>Try running the setup script again.</strong></li>');
    }
    $support_courses_cohort_id = create_cohort_if_not_existing('support_courses');
    if(!($support_courses_cohort_id < 0)) {
        echo('<li>The Support courses cohort exists.</li>');
    } else {
        echo('<li>The Support courses cohort was not created. <strong>Try running the setup script again.</strong></li>');
    }

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
        ['enrol/manual:unenrol', $prohibit],
        ['enrol/flatfile:unenrol', $prohibit],
        ['enrol/self:unenrol', $prohibit],
        ['enrol/lti:unenrol', $prohibit],
        ['moodle/course:reviewotherusers', $prohibit],
        ['moodle/role:assign', $prohibit],
        ['enrol/category:config', $prohibit],
        ['enrol/cohort:config', $prohibit],
        ['enrol/database:config', $prohibit],
        ['enrol/guest:config', $prohibit],
        ['enrol/imsenterprise:config', $prohibit],
        ['enrol/lti:config', $prohibit], 
        ['enrol/meta:config', $prohibit],
        ['enrol/mnet:config', $prohibit],
        ['enrol/self:config', $prohibit],
        ['moodle/course:enrolconfig', $prohibit],
        ['moodle/course:enrolreview', $prohibit],
        ['moodle/cohort:view', $prohibit],
        ['moodle/course:changecategory', $prohibit],
        ['moodle/course:changeshortname', $prohibit],
        ['moodle/course:renameroles', $prohibit],
        ['moodle/question:editall', $prohibit],
        ['moodle/question:moveall', $prohibit],
        ['moodle/question:tagall', $prohibit],
        ['moodle/rating:viewall', $prohibit],
        ['moodle/rating:viewany', $prohibit],
        ['moodle/role:safeoverride', $prohibit],
        ['moodle/user:viewhiddendetails', $prohibit],
        ['mod/assign:manageallocations', $prohibit],
        ['mod/chat:deletelog', $prohibit],
        ['mod/feedback:createpublictemplate', $prohibit],
    ];

    $ukfnteacher_context_types = [CONTEXT_COURSE, CONTEXT_MODULE];

    $ukfnteacher_role_id = create_role_if_not_existing_and_update_role_permissions('UKfilmNet teacher', 'ukfnteacher', 'A role for all UKfilmNet teachers - based on Moodle\'s editingteacher role, but more restritive', 'editingteacher', $ukfnteacher_capabilities_to_change, $ukfnteacher_context_types, [], [], ['student'],[]);

    if(!($ukfnteacher_role_id === -1)) {
        echo('<li>The UKfilmNet Teacher role exists.</li>');
    } else {
        echo('<li>The UKfilmNet Teacher role was not created. <strong>Try running the setup script again.</strong></li>');
    }

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
        ['tool/policy:accept', $allow],
        ['tool/policy:dogchow', $prohibit]
    ];

    $applicant_context_types = [CONTEXT_SYSTEM, CONTEXT_USER];

    $applicant_role_id = create_role_if_not_existing_and_update_role_permissions('UKfilmNet applicant', 'applicant', 'A role for all UKfilmNet educator access applicants - based on Moodle\'s Authenticated user role, but less restrited', 'user', $applicant_capabilities_to_change, $applicant_context_types, [], [], [],[]);

    if(!($applicant_role_id === -1)) {
        echo('<li>The UKfilmNet Applicant role exists.</li>');
    } else {
        echo('<li>The UKfilmNet Applicant role was not created. <strong>Try running the setup script again.</strong></li>');
    }

    // Array of capabilities to be changed in order to restrict ukfnstudent role permissions beyond those of its student role prototype - modify this array and run this script to  further expand or restrict applicant role permissions
    $ukfnstudent_capabilities_to_change = [

    ];

    $ukfnstudent_context_types = [CONTEXT_COURSE, CONTEXT_MODULE];

    $ukfnstudent_role_id = create_role_if_not_existing_and_update_role_permissions('UKfilmNet student', 'ukfnstudent', 'A role for all UKfilmNet students - based on Moodle\'s student role, but more restrited', 'student', $ukfnstudent_capabilities_to_change, $ukfnstudent_context_types, [], [], [],[]);

    if(!($ukfnstudent_role_id === -1)) {
        echo('<li>The UKfilmNet Student role exists.</li>');
    } else {
        echo('<li>The UKfilmNet Student role was not created. <strong>Try running the setup script again.</strong></li>');
    }

    // Array of capabilities to be changed in order to restrict ukfnstudent role permissions beyond those of its student role prototype - modify this array and run this script to  further expand or restrict applicant role permissions
    $ukfn_resourcecourse_user_capabilities_to_change = [
        ['moodle/course:viewparticipants', $prohibit],
    ];

    $ukfn_resourcecourse_user_context_types = [CONTEXT_COURSE, CONTEXT_MODULE];

    $ukfn_resourcecourse_user_role_id = create_role_if_not_existing_and_update_role_permissions('UKfilmNet resource course user', 'ukfnresourcecourseuser', 'A role for UKfilmNet resource course users - based on Moodle\'s student role, but more restrited', 'student', $ukfn_resourcecourse_user_capabilities_to_change, $ukfn_resourcecourse_user_context_types, [], [], [],[]);

    if(!($ukfn_resourcecourse_user_role_id === -1)) {
        echo('<li>The UKfilmNet Resource Course User role exists.</li>');
    } else {
        echo('<li>The UKfilmNet Resource Course User role was not created. <strong>Try running the setup script again.</strong></li>');
    }

    // Array of capabilities to be changed in order to restrict ukfnstudent role permissions beyond those of its student role prototype - modify this array and run this script to  further expand or restrict applicant role permissions
    $ukfnnoneditingteacher_capabilities_to_change = [
        ['enrol/manual:enrol', $prohibit],
        ['enrol/flatfile:manage', $prohibit],
        ['enrol/paypal:manage', $prohibit],
        ['enrol/self:manage', $prohibit],
        ['enrol/manual:unenrol', $prohibit],
        ['enrol/flatfile:unenrol', $prohibit],
        ['enrol/self:unenrol', $prohibit],
        ['enrol/lti:unenrol', $prohibit],
        ['moodle/course:reviewotherusers', $prohibit],
        ['moodle/role:assign', $prohibit],
        ['enrol/category:config', $prohibit],
        ['enrol/cohort:config', $prohibit],
        ['enrol/database:config', $prohibit],
        ['enrol/guest:config', $prohibit],
        ['enrol/imsenterprise:config', $prohibit],
        ['enrol/lti:config', $prohibit], 
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
        ['moodle/role:safeoverride', $prohibit],
        ['moodle/user:viewhiddendetails', $prohibit],
        ['mod/assign:manageallocations', $prohibit],
        ['mod/chat:deletelog', $prohibit],
        ['mod/feedback:createpublictemplate', $prohibit]
    ];

    $ukfnnoneditingteacher_context_types = [CONTEXT_COURSE, CONTEXT_MODULE];

    $ukfnnoneditingteacher_role_id = create_role_if_not_existing_and_update_role_permissions('UKfilmNet non-editing teacher', 'ukfnnoneditingteacher', 'A role for all UKfilmNet non-editing teachers - based on Moodle\'s non-editing teacher role, but more restrited', 'teacher', $ukfnnoneditingteacher_capabilities_to_change, $ukfnnoneditingteacher_context_types, [], [], [],[]);

    if(!($ukfnnoneditingteacher_role_id === -1)) {
        echo('<li>The UKfilmNet Non-editing Teacher role exists.</li>');
    } else {
        echo('<li>The UKfilmNet Non-editing Teacher role role was not created. <strong>Try running the setup script again.</strong></li>');
    }

    // Create the classrooms and DFM categories programmatically
    $classrooms_category_data = array(
        'name'=>get_string('classrooms_category_name', 'enrol_ukfilmnet'),
        'idnumber'=>get_string('classrooms_category_idnumber', 'enrol_ukfilmnet'),
        'parent'=>intval(get_string('classrooms_category_parent', 'enrol_ukfilmnet')),
        'descriptionformat'=>0,
        'description'=>get_string('classrooms_category_description', 'enrol_ukfilmnet'),
    );

    $classrooms_category_already_exists = $DB->get_record('course_categories', array('idnumber'=>get_string('classrooms_category_idnumber', 'enrol_ukfilmnet')));
    if(!$classrooms_category_already_exists) {
        $classrooms_category = core_course_category::create($classrooms_category_data);
    }
    $classrooms_category_exists = $DB->get_record('course_categories', array('idnumber'=>get_string('classrooms_category_idnumber', 'enrol_ukfilmnet')));
    if($classrooms_category_exists) {
        echo('<li>The CLASSROOMS category exists.</li>');
    } else {
        echo('<li>The CLASSROOMS category was not created. <strong>Try running the setup script again.</strong></li>');
    }
    
    $dfm_category_data = array(
        'name'=>get_string('dfm_category_name', 'enrol_ukfilmnet'),
        'idnumber'=>get_string('dfm_category_idnumber', 'enrol_ukfilmnet'),
        'parent'=>intval(get_string('dfm_category_parent', 'enrol_ukfilmnet')),
        'descriptionformat'=>0,
        'description'=>get_string('dfm_category_description', 'enrol_ukfilmnet'),
    );

    $dfm_category_already_exists = $DB->get_record('course_categories', array('idnumber'=>get_string('dfm_category_idnumber', 'enrol_ukfilmnet')));
    if(!$dfm_category_already_exists) {
        $dfm_category = core_course_category::create($dfm_category_data);
    }
    $dfm_category_exists = $DB->get_record('course_categories', array('idnumber'=>get_string('dfm_category_idnumber', 'enrol_ukfilmnet')));
    if($dfm_category_exists) {
        echo('<li>The DIGITAL FILMMAKING category exists.</li>');
    } else {
        echo('<li>The DIGITAL FILMMAKING category was not created. <strong>Try running the setup script again.</strong></li>');
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

    $course_already_exists = $DB->get_record('course', array('shortname'=>get_string('template_course_shortname', 'enrol_ukfilmnet')));
    if(!$course_already_exists) {
        $classroom_course_template = create_course($classroom_course_template_data);
    }
    $course_exists = $DB->get_record('course', array('shortname'=>get_string('template_course_shortname', 'enrol_ukfilmnet')));
    if($course_exists) {
        echo('<li>The Classroom Course template exists.</li>');
    } else {
        echo('<li>The Classroom Course template was not created. <strong>Try running the setup script again.</strong></li>');
    }

    // Create a UKfilmNet Safeguarding user
    $ukfn_sg_user = create_ukfnsafeguarding_user($auth = 'manual');
    if($ukfn_sg_user === false) {
        echo('<li>The UKfilmNet Safeguarding user was not created. <strong>Try running the setup script again.</strong></li>');
    } else {
        echo('<li>The UKfilmNet Safeguarding user exists.</li>');
    }

    // HANDLE CREATION OF CUSTOM PROFILE FIELDS IF THEY DO NOT YET EXIST

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
    $saved_ukfn_applicant_info_field_array = $ukfn_applicant_info_field_array;

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

    // Check to see if the UKfilmNet Applicant Info profile area and fields were created 
    $db_ukfn_applicant_info_field_array = $DB->get_records('user_info_field', array('categoryid'=>$ukfn_applicant_info_category_id));

    foreach($db_ukfn_applicant_info_field_array as &$record) {
        $record = (array)$record;
        unset($record['id']);
    }
    $db_ukfn_applicant_info_field_array = array_values($db_ukfn_applicant_info_field_array);

    foreach($saved_ukfn_applicant_info_field_array as &$record) {
        $record = (array)$record;
        unset($record['id']);
    }
    $saved_ukfn_applicant_info_field_array = array_values($saved_ukfn_applicant_info_field_array);
    
    $differences = @array_diff_assoc($saved_ukfn_applicant_info_field_array, $db_ukfn_applicant_info_field_array);
    if(empty($differences)) {
        echo('<li>The UKfilmNet Applicant Info profile area and fields exist.</li>');
    } else {
        echo('<li>The UKfilmNet Applicant Info profile area and fields were not created. <strong>Try running the setup script again.</strong></li>');
    }
echo('</></div></div>');

// Give the Moodle Site admin instructions about completing setup
echo(get_string('setup_script_run_confirmation', 'enrol_ukfilmnet'));
