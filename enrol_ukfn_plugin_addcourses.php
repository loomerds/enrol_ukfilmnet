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

// HANDLE ADDING ADDITIONALLY REQUESTED CLASSROOM COURSES

delete_dangling_cohorts();

// Get a list of all users having UKfilmNet Teacher roles
$ukfnteacher_role_id = $DB->get_record('role', array('shortname'=>'ukfnteacher'))->id;
$ukfnteacher_role_assignments = $DB->get_records('role_assignments', array('roleid'=>$ukfnteacher_role_id));

// Build an array where the keys are UKfilmNet Teacher user ids and the values show how many classroom courses have been created for each UKfilmNet Teacher
$num_of_ukfnteacher_classrooms = [];
foreach($ukfnteacher_role_assignments as $role_assignment) {  
    if(!array_key_exists($role_assignment->userid, $num_of_ukfnteacher_classrooms)) {
        $num_of_ukfnteacher_classrooms[$role_assignment->userid] = 1;
    } else {
        $num_of_ukfnteacher_classrooms[$role_assignment->userid] = $num_of_ukfnteacher_classrooms[$role_assignment->userid]+1;
    }
}

// Make sure that the number of classroom courses created for each UKfilmNet Teacher is greater than or equal to the number they have requested
foreach($num_of_ukfnteacher_classrooms as $teacherid=>$classrooms) {
    $classrooms_count = $classrooms;
    $ukfnteacher = $DB->get_record('user', array('id'=>$teacherid));
    profile_load_data($ukfnteacher);
    $classrooms_requested = $ukfnteacher->profile_field_courses_requested;

    while($classrooms_count < $classrooms_requested) {
        $newcourseinfo = create_classroom_course_from_teacherid($teacherid, 
                            get_string('template_course_shortname', 'enrol_ukfilmnet'), 
                            get_string('classrooms_category_idnumber', 'enrol_ukfilmnet'));
        $newcourse = $DB->get_record('course', array('shortname'=>$newcourseinfo['shortname']));
        $approvedteacher_role = $DB->get_record('role', array('shortname'=>'user'));
        $systemcontext = context_system::instance();
        $usercontext = context_user::instance($ukfnteacher->id);
        
        // Change applicant's basic system role assignment
        /*role_assign($approvedteacher_role->id, $applicant_user->id, $systemcontext->id);
        role_assign($approvedteacher_role->id, $applicant_user->id, $usercontext->id);*/
        
        // Enrol applicant in their classroom course(s) as a teacher
        enrol_user_this($newcourseinfo, $ukfnteacher, get_role_id(get_string('ukfnteacher_role_name', 'enrol_ukfilmnet')), 'manual');

        // Enrol the applicant's DSL in the course via addition to the associated cohort as a non-editing teacher
        $safeguarding_contact = $DB->get_record('user', array('email'=>$ukfnteacher->profile_field_safeguarding_contact_email));
        $context = context_course::instance($newcourse->id);
        $cohort = $DB->get_record('cohort', array('idnumber'=>$newcourse->shortname));
        cohort_add_member($cohort->id, $safeguarding_contact->id);
        role_assign(get_role_id('teacher'), $safeguarding_contact->id, $context->id);
        role_unassign(get_role_id('student'), $safeguarding_contact->id, $context->id);
        $classrooms_count++;
    }
}

$existing_emails = [];
$users_list = $DB->get_records('user', array('deleted'=>1));
foreach($users_list as $a_user) {
    array_push($existing_emails, $a_user->email);
}

go_to_page('admin');



