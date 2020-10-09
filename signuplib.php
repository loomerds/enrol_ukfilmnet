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
 * A libray of functions and one small class to support the UKfilmNet enrolment plugin
 *
 * @package    enrol_ukfilmnet
 * @copyright  2020, Doug Loomer
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG, $DB;

/**
 * Constants for use in plugin page navigation
 */
defined('MOODLE_INTERNAL') || die();
define('PAGE_WWWROOT', $CFG->wwwroot);
define('PAGE_SITEADMIN', $CFG->wwwroot.'/admin/search.php');
define('PAGE_APPLICANT', $CFG->wwwroot.'/enrol/ukfilmnet/applicant.php');
define('PAGE_EMAILVERIFY', $CFG->wwwroot.'/enrol/ukfilmnet/emailverify.php');
define('PAGE_SCHOOL', $CFG->wwwroot.'/enrol/ukfilmnet/school.php');
define('PAGE_COURSES', $CFG->wwwroot.'/enrol/ukfilmnet/courses.php');
define('PAGE_SAFEGUARDING', $CFG->wwwroot.'/enrol/ukfilmnet/safeguarding.php');
define('PAGE_STUDENTS', $CFG->wwwroot.'/enrol/ukfilmnet/students.php');
define('PAGE_ASSURANCE', $CFG->wwwroot.'/enrol/ukfilmnet/assurance.php');
define('PAGE_TRACKING', $CFG->wwwroot.'/enrol/ukfilmnet/tracking.php');
define('PAGE_ADMIN', $CFG->wwwroot.'/admin/search.php');

/**
 * Creates an Applicant user account
 *
 * @param object $applicantinfo An object with email, username, firstname, lastname, currentrole, applicationprogress, and verificationcode key/data fields for the user
 * @param string $password A password for the user
 * @param string $auth A Moodle authentication type
 * @return stdClass A user object
 */
function create_applicant_user($applicantinfo, $password, $auth = 'manual') {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/user/profile/lib.php');
    require_once($CFG->dirroot.'/user/lib.php');
    require_once($CFG->dirroot.'/lib/accesslib.php');
    require_once($CFG->dirroot.'/lib/moodlelib.php');
    
    $username = trim(core_text::strtolower($applicantinfo->username));
    $authplugin = get_auth_plugin($auth);
    $customfields = $authplugin->get_custom_user_profile_fields();
    $newuser = new stdClass();
    
    if (!empty($newuser->email)) {
        if (email_is_not_allowed($newuser->email)) {
            unset($newuser->email);
        }
    }
    if (!isset($newuser->city)) {
        $newuser->city = '';
    }
    
    $newuser->auth = $auth;
    $newuser->username = $username;
    $newuser->email = $applicantinfo->email;
    $newuser->firstname = $applicantinfo->firstname;
    $newuser->lastname = $applicantinfo->lastname;
    $newuser->profile_field_currentrole = convert_rolenum_to_rolestring($applicantinfo->currentrole);
    $newuser->profile_field_verificationcode = $applicantinfo->verificationcode;
    $newuser->profile_field_applicationprogress = $applicantinfo->applicationprogress;

    if (empty($newuser->lang) || !get_string_manager()->translation_exists($newuser->lang)) {
        $newuser->lang = $CFG->lang;
    }
    $newuser->confirmed = 1;
    $newuser->lastip = getremoteaddr();
    $newuser->timecreated = time();
    $newuser->timemodified = $newuser->timecreated;
    $newuser->mnethostid = $CFG->mnet_localhost_id;
     
    $newuser->id = user_create_user($newuser, false);
    // Save user profile data.
    profile_save_data($newuser);

    $applicantrole = $DB->get_record('role', array('shortname'=>'applicant'));
    $systemcontext = context_system::instance();
    $usercontext = context_user::instance($newuser->id);
  
    role_assign($applicantrole->id, $newuser->id, $systemcontext->id);
    role_assign($applicantrole->id, $newuser->id, $usercontext->id);

    $user = get_complete_user_data('id', $newuser->id);
    if($user->firstname === 'Safeguarding') {
        set_user_preference('auth_forcepasswordchange', 0, $user);
    } else {
        set_user_preference('auth_forcepasswordchange', 0, $user);
    }

    // Set the password.
    update_internal_user_password($user, $password);

    return $user;
}

/**
 * Redirects to the site frontpage if $user is a Guest user or not an Applicant user
 *
 * @param stdClass $user A moodle user object
 */
function is_applicant_user($user) {
    profile_load_data($user);
    $is_applicant = strlen($user->profile_field_applicationprogress) >0;
    if (isguestuser() or !$is_applicant) {
        redirect(PAGE_WWWROOT);
    }
} 

/**
 * Creates an UKfilmNet Safeguarding process manager admin user account
 *
 * @param string $auth A Moodle authentication type
 * @return stdClass A user object
 */
function create_ukfnsafeguarding_user($auth = 'manual') {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/user/profile/lib.php');
    require_once($CFG->dirroot.'/user/lib.php');
    require_once($CFG->dirroot.'/lib/accesslib.php');
    require_once($CFG->dirroot.'/lib/moodlelib.php');
    
    $user = $DB->get_record('user', array('email'=>get_string('moodle_admin_safeguarding_user_email', 'enrol_ukfilmnet')));
    
    if( $user === false) {
        $username = get_string('moodle_admin_safeguarding_user_username', 'enrol_ukfilmnet');
        $authplugin = get_auth_plugin($auth);
        $newuser = new stdClass();
        
        if (!empty($newuser->email)) {
            if (email_is_not_allowed($newuser->email)) {
                unset($newuser->email);
            }
        }

        if (!isset($newuser->city)) {
            $newuser->city = '';
        }
        
        $newuser->auth = $auth;
        $newuser->username = $username;
        $newuser->email = get_string('moodle_admin_safeguarding_user_email', 'enrol_ukfilmnet');
        $newuser->firstname = get_string('moodle_admin_safeguarding_user_firstname', 'enrol_ukfilmnet');
        $newuser->lastname = get_string('moodle_admin_safeguarding_user_lastname', 'enrol_ukfilmnet');
        $password = 'safeguarding';
        
        if (empty($newuser->lang) || !get_string_manager()->translation_exists($newuser->lang)) {
            $newuser->lang = $CFG->lang;
        }
        $newuser->confirmed = 1;
        $newuser->lastip = getremoteaddr();
        $newuser->timecreated = time();
        $newuser->timemodified = $newuser->timecreated;
        $newuser->mnethostid = $CFG->mnet_localhost_id;
        $newuser->id = user_create_user($newuser, false);
        $managerrole = $DB->get_record('role', array('shortname'=>'manager'));
        $systemcontext = context_system::instance();
        $usercontext = context_user::instance($newuser->id);
    
        role_assign($managerrole->id, $newuser->id, $systemcontext->id);
        role_assign($managerrole->id, $newuser->id, $usercontext->id);

        $user = get_complete_user_data('id', $newuser->id);
        set_user_preference('auth_forcepasswordchange', 1, $user);

        // Set the password.
        update_internal_user_password($user, $password);
    }

    if($DB->get_record('user', array('email'=>get_string('moodle_admin_safeguarding_user_email', 'enrol_ukfilmnet'))) === false) {
        return false;
    } else {
        return $user;
    }
}

/**
 * Creates a UKfilmNet Student user account
 *
 * @param object $studentinfo An object with email, username, firstname, and lastname key/data fields for the user
 * @param string $auth A Moodle authentication type
 * @return stdClass A user object
 */
function create_student_user($studentinfo, $auth = 'manual') {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/user/profile/lib.php');
    require_once($CFG->dirroot.'/user/lib.php');
    require_once($CFG->dirroot.'/lib/accesslib.php');
    require_once($CFG->dirroot.'/lib/moodlelib.php');

    $password = make_random_password();
    $username = trim(core_text::strtolower($studentinfo->student_email));
    $authplugin = get_auth_plugin($auth);
    $newuser = new stdClass();
    
    if (!empty($newuser->student_email)) {
        if (email_is_not_allowed($newuser->student_email)) {
            unset($newuser->student_email);
        }
    }
    if (!isset($newuser->city)) {
        $newuser->city = '';
    }
    
    $newuser->auth = $auth;
    $newuser->username = $username;
    $newuser->email = $studentinfo->student_email;
    $newuser->firstname = $studentinfo->student_firstname;
    $newuser->lastname = $studentinfo->student_familyname;

    if (empty($newuser->lang) || !get_string_manager()->translation_exists($newuser->lang)) {
        $newuser->lang = $CFG->lang;
    }
    $newuser->confirmed = 1;
    $newuser->lastip = getremoteaddr();
    $newuser->timecreated = time();
    $newuser->timemodified = $newuser->timecreated;
    $newuser->mnethostid = $CFG->mnet_localhost_id;
    $newuser->id = user_create_user($newuser, false);
    $user = get_complete_user_data('id', $newuser->id);
    set_user_preference('auth_forcepasswordchange', 0, $user);

    // Set the password.
    update_internal_user_password($user, $password);

    return $user;
}

/**
 * Creates an SGO user account
 *
 * @param stdClass $applicant_user The user object of the teacher the SGO will be managing
 * @param string $auth A Moodle authentication type
 * @return stdClass A user object
 */
function create_sgo_user($applicant_user, $auth = 'manual') {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/user/profile/lib.php');
    require_once($CFG->dirroot.'/user/lib.php');
    require_once($CFG->dirroot.'/lib/accesslib.php');
    require_once($CFG->dirroot.'/lib/moodlelib.php');
    
    $username = trim(core_text::strtolower($applicant_user->profile_field_safeguarding_contact_email));
    $newuser = new stdClass();
    
    if (!empty($newuser->email)) {
        if (email_is_not_allowed($newuser->email)) {
            unset($newuser->email);
        }
    }
    if (!isset($newuser->city)) {
        $newuser->city = '';
    }
    
    $newuser->auth = $auth;
    $newuser->username = $username;
    $newuser->email = $applicant_user->profile_field_safeguarding_contact_email;
    $newuser->firstname = 'DSL-'.$applicant_user->profile_field_safeguarding_contact_firstname;
    $newuser->lastname = $applicant_user->profile_field_safeguarding_contact_familyname;

    if (empty($newuser->lang) || !get_string_manager()->translation_exists($newuser->lang)) {
        $newuser->lang = $CFG->lang;
    }
    $newuser->confirmed = 1;
    $newuser->lastip = getremoteaddr();
    $newuser->timecreated = time();
    $newuser->timemodified = $newuser->timecreated;
    $newuser->mnethostid = $CFG->mnet_localhost_id;
    $newuser->id = user_create_user($newuser, false);

    // Save user profile data.
    profile_save_data($newuser);

    $applicantrole = $DB->get_record('role', array('shortname'=>get_string('sgo_role_name', 'enrol_ukfilmnet')));
    $systemcontext = context_system::instance();
    $usercontext = context_user::instance($newuser->id);
  
    role_assign($applicantrole->id, $newuser->id, $systemcontext->id);
    role_assign($applicantrole->id, $newuser->id, $usercontext->id);

    $sgo_user = get_complete_user_data('id', $newuser->id);
    set_user_preference('auth_forcepasswordchange', 1, $sgo_user);

    // Set the password.
    $sgo_user_password = make_random_password();
    update_internal_user_password($sgo_user, $sgo_user_password);

    // Email login and approval notice to sgo
    email_sgo_newuser_info($applicant_user, $sgo_user, $sgo_user_password);

    return $sgo_user;
}

/**
 * Makes code printing to the screen for debugging easier
 *
 * @param string $val A string to be printed to the screen
 */
function print_r2($val){
    echo '<pre>';
    echo '<br>';
    echo '<br>';
    echo '<br>';
    echo '<br>';
    print_r($val);
    echo  '</pre>';
}

/**
 * Handles initial Student Enrolment page $_POST data flow
 *
 * @param object $datum A $_POST object
 */
function handle_enrol_students_post($datum) {
    global $CFG;

    if($datum['submit_type'] == 'Exit') {
        redirect($CFG->wwwroot);
    } else {
        process_students($datum);
    }
}

/**
 * Handles initial Tracking page $_POST data flow
 *
 * Redirects to Site admin page, or calls application_denied or application_approved functions
 */
function handle_tracking_post() {
    global $CFG;

    if($_POST['submit_type'] == 'Exit') {
        redirect(PAGE_SITEADMIN);
    } else if($_POST['submit_type'] == 'Submit') {
        if(!empty($_POST['denied'])) {
            application_denied($_POST['denied']);
        } 
        if(!empty($_POST['approved'])) {
            application_approved($_POST['approved']);
        }
        redirect(PAGE_TRACKING);
    }
}

/**
 * Creates UKfilmNet Student user accounts and places UKfilmNet Student users into the appropriate cohorts, and/or removes UKfilmNet Student users from appropriate cohorts 
 *
 * @param object $datum A $_POST object from the Student Enrolment page 
 */

function process_students($datum) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/cohort/lib.php');

    // Remove unwanted indexes from our datum subarrays (selected checkboxes have created two indexes each in our datum subarrays, one holding a checkbox value and one holding 0 - remove the index holding 0 following each index holding a checkbox value - this oddity exists because all checkboxes were forced to return 0 to deal with the fact that unchecked checkboxes normally don't return anything) 
    $count = 0;
    foreach($datum as &$data) {
        $col=0;
        if($count>3) { //changed from 2 to 3
            while(is_array($data) and $col<count($data)) {
                if(strlen($data[$col]) > 2) {
                    unset($data[$col+1]);
                    $data = array_values($data);
                    $col++;
                } else {
                    $col++;
                }   
            }
        }
        $count++;
    }
    unset($data);

    // The datum holds table column values in parallel subarrays - this foreach loop makes a new array holding the table values as rows
    $students = array();
    $count_key = 0;
    foreach($datum as $key => $data) {
        if($count_key > 0) {
            if(is_array($data)) {
                foreach($data as $s_key => $s_data) {
                    $students[$s_key][$key] = $s_data;
                }
            }
        }
        $count_key++;
    }
    unset($data);
    unset($s_data);

    // Remove rows of data if they don't contain an email address, firstname, or family name
    // Create an array of email addresses of students whose row was removed
    $removed = '';
    foreach($students as $key => $student) {
        if (((strlen($student['student_email']) < 2) or ($student['student_email'] === null)) 
            and ((strlen($student['student_firstname']) < 2) or ($student['student_firstname'] === null))
            and ((strlen($student['student_familyname']) < 2) or ($student['student_familyname'] === null))) {
            unset($students[$key]);
            continue;
        } elseif((strlen($student['student_email']) < 2) or ($student['student_email'] === null)) {
            $removed = $removed.', '.$student['student_email'];
            unset($students[$key]);
            continue;
        } elseif ((strlen($student['student_firstname']) < 1) or ($student['student_firstname'] === null)) {
            $removed = $removed. ', '.$student['student_email'];
            unset($students[$key]);
            continue;
        } elseif ((strlen($student['student_familyname']) < 1) or ($student['student_familyname'] === null)) {
            $removed = $removed.', '.$student['student_email'];
            unset($students[$key]);
            continue;
        }
    }

    // Turn each row of student data into an object, give students Moodle accounts if they don't already have accounts, add students to students cohort
    $taken = '';
    foreach($students as $key => $student) {
        $users = $DB->get_records('user');
        $email_taken = false;
        foreach($users as $user) {
            
            // Remove student rows if the email address (after being set to all lower case) matches an existing user name
            if(strcasecmp($student['student_email'], $user->username) === 0) {                
                $email_taken = true;
                if(strtolower($student['student_email']) != $student['student_email']) {
                    $taken = $taken.', '.$student['student_email'];
                    unset($students[$key]);
                }
                //unset($students[$key]);
                break;
            } 
        }
        if($email_taken == false) {
            $new_student = create_student_user((object)$student);
            $cohort_id = create_cohort_if_not_existing(get_string('ukfnstudent_cohort_name', 'enrol_ukfilmnet'));
            cohort_add_member($cohort_id, $new_student->id);

        }
    }
    
    $removed = rtrim($removed, ",");
    $taken = rtrim($taken, ",");

    // Add or remove students to/from appropriate cohorts
    add_or_remove_students_to_cohorts($students, $datum['cohort_names']);

    if(($taken === '' or $taken === null) and ($removed === '' or $removed === null)) {
        redirect(PAGE_WWWROOT.'/enrol/ukfilmnet/students.php');
    } else {
        if($taken != '' or $taken != null) {
            $removed = '<p class="ukfn_error_feedback">The user(s) having the following email addresses'.$taken.', were not added because that email address is already being used by another UKfilmNet user.</p>';
            $_SESSION['removed_message'] = $removed;
        } else {
            $removed = '<p class="ukfn_error_feedback">The user(s) having the following email addresses'.$removed.', were not added because you did not: 1) include one or more of the required fields (Email, First Name, and Family Name), or 2) you did not include the student in at least one course.</p>';
            $_SESSION['removed_message'] = $removed;
        }
        redirect(PAGE_WWWROOT.'/enrol/ukfilmnet/students.php');
    }
}

/**
 * Checks whether a user is already in a given cohort
 *
 * @param stdClass $user A Moodle user object
 * @param int $target_cohort The id of the cohort in question
 * @return int 1 if the users is in the target cohort, else 0
 */
function is_already_in_cohort($user, $target_cohort, $cohortid = null) {
    global $DB;

    $user_cohorts = $DB->get_records('cohort_members', array('userid'=>$user->id));

    foreach($user_cohorts as $user_cohort) {
            if($user_cohort->cohortid === $target_cohort) {
                return 1;
            }
    }

    return 0;
} 

/**
 * Adds or removes students to/from the appropriate cohorts
 *
 * @param array $studentinputs An array of student user to be add to or removed from specified cohorts
 * @param array $cohort_names An array of cohort names to which students should be added or removed
 */
function add_or_remove_students_to_cohorts($studentinputs, $cohort_names) {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/cohort/lib.php');    

    $resourse_courses_cohort = $DB->get_record('cohort', array('idnumber'=>get_string('resource_courses_idnumber', 'enrol_ukfilmnet')));
    foreach ($studentinputs as $input) {
        $cohort_idnumbers = [];
        $user;
        $count = 0;
        
        // From the DB, get an array of the idnumbers of the cohorts this student is in relative to this teacher
        foreach($input as $item) {
            if($count == 0) {
                $user = $DB->get_record('user', array('email'=>$item));
                $count++;
            }elseif($count < 3) {
                $count++;
            }else{
                $cohort_idnumbers[] = $item;
            }
        }

        // Add and remove students from cohorts on basis of checkbox input changes
        $count = 0;
        foreach($cohort_names as $cohort_name) {
            $target_cohort = $DB->get_record('cohort', array('idnumber'=>$cohort_name));
            if(strlen($cohort_idnumbers[$count]) > 2) { // If the input is checked

                // Note that $target_cohort->id is the cohort object's id field
                // Stops teacher from adding DSL users to support courses and their courses
                if(substr($user->firstname, 0, 4) !== 'DSL-') {
                    cohort_add_member($target_cohort->id, $user->id);
                    cohort_add_member($resourse_courses_cohort->id, $user->id);
                }
            } else {  // If the input is NOT checked
                
                // If uers is NOT a Designated Safty Lead, remove user from cohort
                // Stops teacher from removing DSL users from their courses
                if(substr($user->firstname, 0, 4) !== 'DSL-') {
                    cohort_remove_member($target_cohort->id, $user->id);
                }
            }
            $count++;
        }

        // If student is a member of only one cohort it must be the resource_courses_cohort, remove student from that cohort
        $user_cohorts = $DB->get_records('cohort_members', array('userid'=>$user->id));
        if(count($user_cohorts) < 2) {
            cohort_remove_member($resourse_courses_cohort->id, $user->id);
        }
    }
}

/**
 * Creates a username based upon an email address
 *
 * @param string $email An email address
 * @return string A string to be used as a username
 */
function make_username($email) {
    return substr($email,0,stripos($email,'@',0));
}

/**
 * Authenticates an Applicant user login
 *
 * @param string $username A username
 * @param string $password A password
 * @return stdClass A Moodle user object
 */
function applicant_login($username, $password) {
    $user = authenticate_user_login($username, $password);
    return complete_user_login($user);
}

/**
 * Creates a random integer between 1111111 and 999999
 *
 * @return int An integer within the given range
 */
function generate_random_verification_code() {
    return rand(111111, 999999);
}

/**
 * Creates a random password
 *
 * @return string A randomly generated string
 */
function make_random_password() {
    return 'ukfilm'.rand(1000, 9999);
}

/**
 * Creates a random integer string between 1 and 1000
 *
 * @return string A randomly generated string
 */
function make_random_numstring() {
    return rand(1, 1000);
}

/**
 * Handles UKfilmNet teacher application denials
 *
 * @param array $denied An array of user ids
 */
function application_denied($denied) {
    global $DB;
    foreach($denied as $userid) {
        $applicant_user = $DB->get_record('user', array('id' => $userid, 'auth' => 'manual'));
        if($applicant_user !== null) {
            profile_load_data($applicant_user);
            if(convert_progressstring_to_progressnum($applicant_user->profile_field_applicationprogress) > '1') {
                $applicant_user->profile_field_applicationdenied = '1';
                $applicant_user->profile_field_applicationprogress = convert_progressnum_to_progressstring('1');
                profile_save_data($applicant_user);

                email_user_accept_reject($applicant_user, "denied");
                $applicant_user->suspended = 1;
                $DB->update_record('user', $applicant_user);
            }
        }
    }
}

/**
 * Handles UkfilmNet teacher application approvals
 *
 * @param array $approved An array of user ids
 */
function application_approved($approved) {
    global $DB, $CFG;
    include_once($CFG->dirroot.'/course/externallib.php');
    include_once($CFG->dirroot.'/lib/enrollib.php');

    // Remove any dangling cohorts (classroom cohorts that are no longer  associated with a course)
    delete_dangling_cohorts();

    // Get a list of all user emails
    $existing_emails = [];
    $users_list = $DB->get_records('user');
    foreach($users_list as $a_user) {
        array_push($existing_emails, $a_user->email);
    }

    // Process each approval - 1) change progress level, 2) send email notification to applicant, 3) create classroom courses requested, 4) enrol applicant in classroom courses with a role of ukfnteacher, 5)enrol applicant in resource_courses and support_courses with a student role, 6) create DSL user if necessary and enrol DSL in classroom courses and resource_courses 
    foreach($approved as $userid) {
        $applicant_user = $DB->get_record('user', array('id' => $userid, 'auth' => 'manual'));
        $resource_courses_cohort = $DB->get_record('cohort', array('idnumber'=>get_string('resource_courses_idnumber', 'enrol_ukfilmnet')));
        $support_courses_cohort = $DB->get_record('cohort', array('idnumber'=>get_string('support_courses_idnumber', 'enrol_ukfilmnet')));

        if($applicant_user !== null) {
            profile_load_data($applicant_user);
            if(convert_progressstring_to_progressnum($applicant_user->profile_field_applicationprogress) == '6') {
                $applicant_user->profile_field_applicationapproved = '1';
                $applicant_user->profile_field_applicationprogress = convert_progressnum_to_progressstring('7');
                profile_save_data($applicant_user);        
                email_user_accept_reject($applicant_user, "approved");
                
                // Create teacher's classroom courses and enrol the teacher
                for($count = 0; $count<$applicant_user->profile_field_courses_requested; $count++) {
                    $newcourse = create_classroom_course_from_teacherid($userid, 
                            get_string('template_course_shortname', 'enrol_ukfilmnet'), 
                            get_string('classrooms_category_idnumber', 'enrol_ukfilmnet'));
                    $systemcontext = context_system::instance();
                    $usercontext = context_user::instance($applicant_user->id);
                    enrol_user_this($newcourse, $applicant_user, get_role_id(get_string('ukfnteacher_role_name', 'enrol_ukfilmnet')), 'manual');
                }

                // Add teacher to resource_courses and support_courses
                cohort_add_member($resource_courses_cohort->id, $applicant_user->id);
                cohort_add_member($support_courses_cohort->id, $applicant_user->id);
                
                // Create a DSL officer account if it doesn't already exist
                
                $sgo_user = null;
                $sgo_username = $applicant_user->profile_field_safeguarding_contact_email;
                $user_names = [];
                $all_users = $DB->get_records('user');
                foreach($all_users as $a_user) {
                    array_push($user_names, $a_user->username);
                }
                if(in_array($sgo_username, $user_names)) {    
                    $sgo_user = $DB->get_record('user', array('email'=>$sgo_username));
                    if($sgo_user->firstname === 'Safeguarding') {
                        delete_user($sgo_user);
                        $sgo_user = create_sgo_user($applicant_user);
                    } else {
                        email_sgo_existinguser_info($applicant_user, $sgo_user);
                    }
                } else {
                    $sgo_user = create_sgo_user($applicant_user);
                }

                // Add DSL officer to classroom courses
                add_sgo_to_cohorts($applicant_user, $sgo_user, $resource_courses_cohort, $support_courses_cohort);
            }
        }
    }
}

/**
 * Get an array of the names of cohorts of which a user is a member
 *
 * @param stdClass $applicant_user A Moodle user object
 * @return array A sorted array of cohort names
 */
function get_applicant_cohort_names($applicant_user) {
    global $CFG, $DB, $USER;
    require_once($CFG->dirroot.'/lib/accesslib.php');

    $courses = $DB->get_records('course');
    $cohort_names = [];
    $capacity = get_string('essential_teacher_dsl_capacity', 'enrol_ukfilmnet');
    foreach($courses as $course) {
        $context = \context_course::instance($course->id);
        if(is_enrolled($context, $applicant_user, $capacity)) {
            $cohort_names[] = $course->shortname;
        }
    }
    asort($cohort_names);
    $cohort_names = array_values($cohort_names);

    return $cohort_names;
}

/**
 * Adds a UKfilmnet SGO user to Resource and Support cohorts
 *
 * @param stdClass $applicant_user An Applicant user object
 * @param stdClass $sgo_user An SGO user object
 * @param object $resource_courses_cohort A cohort object
 * @param object $support_courses_cohort A cohort object
 */
function add_sgo_to_cohorts($applicant_user, $sgo_user, $resource_courses_cohort, $support_courses_cohort) {
    global $DB;

    // Add the DSL user to the resource courses cohort
    cohort_add_member($resource_courses_cohort->id, $sgo_user->id);
    
    // Add the DSL user to the support courses cohort
    cohort_add_member($support_courses_cohort->id, $sgo_user->id);

    $cohort_names = get_applicant_cohort_names($applicant_user);
    $courses = $DB->get_records('course');
    foreach($courses as $course) {
        if(in_array($course->shortname, $cohort_names)) {
            $context = context_course::instance($course->id);
            $cohort = $DB->get_record('cohort', array('idnumber'=>$course->shortname));
            cohort_add_member($cohort->id, $sgo_user->id);
            role_assign(get_role_id('ukfnnoneditingteacher'), $sgo_user->id, $context->id);
            //role_unassign(get_role_id('ukfnstudent'), $sgo_user->id, $context->id);
        }
    }
}
/**
 * Gets the id of a specified Moodle Role
 *
 * @param string $role_shortname A specfied Moodle Role name
 * @return mixed An int or nothing
 */
function get_role_id($role_shortname) {
    global $DB;

    $all_roles = $DB->get_records('role');
    foreach($all_roles as $role) {
        if($role->shortname == $role_shortname) {
            return $role->id;
        }
    }
}

/**
 * Create and send a teacher application approved email to a new SGO user
 *
 * @param stdClass $applicant_user A Moodle user object
 * @param stdClass $sgo_user A Moodle user object
 * @param string $sgo_password A string containing random password
 * @return 
 */
function email_sgo_newuser_info($applicant_user, $sgo_user, $sgo_password) {
    
    profile_load_data($applicant_user);
    $sgo_user_firstname = substr($sgo_user->firstname, 4);


    // Create array of variables for email to safeguarding officer
    $emailvariables = (object) array('schoolname_ukprn'=>$applicant_user->profile_field_ukprn, 
                                     'schoolname'=>$applicant_user->profile_field_schoolname,
                                     'schoolcountry'=>$applicant_user->profile_field_schoolcountry, 
                                     'contact_firstname'=>$sgo_user_firstname,
                                     'contact_familyname'=>$sgo_user->lastname,
                                     'applicant_firstname'=>$applicant_user->firstname,
                                     'applicant_familyname'=>$applicant_user->lastname,
                                     'applicant_email'=>$applicant_user->email,
                                     'contact_password'=>$sgo_password,
                                     'contact_username'=>$sgo_user->username,
                                     'emailverify_url'=>PAGE_WWWROOT.get_string('emailverify_url', 'enrol_ukfilmnet'),
                                     'ukfilmnet_url'=>PAGE_WWWROOT);

    // Send email to safeguarding officer
    email_to_user($sgo_user, get_admin(), get_string('safeguarding_subject', 'enrol_ukfilmnet', $emailvariables), get_string('safeguarding_new_sgo_account_text', 'enrol_ukfilmnet', $emailvariables));
}

/**
 * Create and send a teacher application approved email to an existing SGO user
 *
 * @param stdClass $applicant_user A Moodle user object
 * @param stdClass $sgo_user A Moodle user object
 */
function email_sgo_existinguser_info($applicant_user, $sgo_user) {
    
    profile_load_data($applicant_user);

    $sgo_user_firstname = substr($sgo_user->firstname, 4);

    // Create array of variables for email to safeguarding officer
    $emailvariables = (object) array('schoolname_ukprn'=>$applicant_user->profile_field_ukprn, 
                                     'schoolname'=>$applicant_user->profile_field_schoolname,
                                     'schoolcountry'=>$applicant_user->profile_field_schoolcountry, 
                                     'contact_firstname'=>$sgo_user_firstname,
                                     'contact_familyname'=>$sgo_user->lastname,
                                     'applicant_firstname'=>$applicant_user->firstname,
                                     'applicant_familyname'=>$applicant_user->lastname,
                                     'applicant_email'=>$applicant_user->email,
                                     'ukfilmnet_url'=>PAGE_WWWROOT);

    // Send email to safeguarding officer
    email_to_user($sgo_user, get_admin(), get_string('safeguarding_subject', 'enrol_ukfilmnet', $emailvariables), get_string('safeguarding_existing_sgo_account_text', 'enrol_ukfilmnet', $emailvariables));
}

/**
 * Create and send an approved or denied email to an Applicant user
 *
 * @param stdClass $applicant A Moodle user object
 * @param string $status A string indicating denial or approval of application
 */
function email_user_accept_reject($applicant, $status){
    global $CFG;
    
    profile_load_data($applicant);

    $emailvariables = (object) array('firstname'=>$applicant->firstname, 
                                     'familyname'=>$applicant->lastname, 
                                     'email'=>$applicant->email,
                                     'students_url'=>PAGE_WWWROOT.get_string('students_url', 'enrol_ukfilmnet'));
    if($status === "denied") {
        email_to_user($applicant, get_admin(), get_string('determination_subject', 'enrol_ukfilmnet', $emailvariables), get_string('determination_text_denied', 'enrol_ukfilmnet', $emailvariables));
    } elseif($status === "approved") {
        email_to_user($applicant, get_admin(), get_string('determination_subject', 'enrol_ukfilmnet', $emailvariables), get_string('determination_text_approved', 'enrol_ukfilmnet', $emailvariables));
    }
}

/**
 * Create a Classroom course on the basis of a UKfilmNet Teacher id, and add Cohort sync Enrolment to the course
 *
 * @param int $teacherid A user id number
 * @param string $template A template course name
 * @param string $category A category id number
 * @return object A Moodle course object
 */
function create_classroom_course_from_teacherid ($teacherid, $template, $category_idnumber) {
    global $DB, $CFG;

    require_once($CFG->libdir.'/datalib.php');
    require_once($CFG->dirroot.'/course/externallib.php');
    require_once($CFG->dirroot.'/user/profile/lib.php');
    require_once($CFG->dirroot.'/enrol/cohort/lib.php');

    // Get a teacher
    $teacher = $DB->get_record('user', array('id' => $teacherid, 'auth' => 'manual'));
    profile_load_data($teacher);
    $target_courseid;

    // Build a shortcourse/cohort name
    $lastname = $teacher->lastname;
    $lastname_length = strlen($lastname);
    $unique_shortname = $lastname.'-0';
    $highest_end = 0;
    
    // See if the shortcourse/cohort name is already taken and keep modifying and trying until it isn't
    $courses = get_courses();
    foreach($courses as $course) {
        $shortname = $course->shortname;
        if(substr($shortname, 0, $lastname_length) == $lastname) {
            $end_num = (int)substr($shortname, $lastname_length+1);

            if($end_num > $highest_end) {
                $highest_end = $end_num +1; // added +1
                $unique_shortname = $lastname.'-'.$highest_end;
            } elseif($end_num == $highest_end) {
                $highest_end = $highest_end + 1;
                $unique_shortname = $lastname.'-'.$highest_end;
            }
        }
        if($course->shortname == $template) {
            $target_courseid = $course->id;
        }
    }

    // Get a category id for the new course
    $categories = core_course_category::get_all();
    $target_categoryid = '';
    //$miscellaneous_categoryid = '';

    foreach($categories as $category) {

        if($category->idnumber == $category_idnumber) {
            $target_categoryid = $category->id;
        }
    }

    // Build a new course object
    $newcourse['courseid'] = $target_courseid;
    $newcourse['fullname'] = $unique_shortname;
    $newcourse['shortname'] = $unique_shortname;
    $newcourse['categoryid'] = $target_categoryid;

    // Copy our template course into our new course
    $courseinfo = core_course_external::duplicate_course($newcourse['courseid'], 
                                           $newcourse['fullname'], 
                                           $newcourse['shortname'], 
                                           $newcourse['categoryid'],
                                           true);

    // Create a new cohort with the same name as our course shortname and get its id
    $cohortid = create_ukfn_cohort($newcourse['shortname']);
    $course_created = $DB->get_record('course', array('shortname'=>$courseinfo['shortname']));
    $data = create_cohortsync_data('Cohort sync', $cohortid, get_string('ukfnstudent_role_name', 'enrol_ukfilmnet'));
    
    // Add the new cohort to the new course's corhort sync
    $cohort_plugin = new enrolukfn_cohort_plugin();
    $cohort_plugin->add_instance($course_created, $data);

    // Return a reference to the new course
    return $courseinfo;
}

/**
 * Enrol a user in a course directly rather than by Cohort sync
 *
 * @param object $courseinfo A Moodle course object
 * @param stdClass $user A Moodle user object
 * @param string $roleid A role name string
 * @param string $enrolmethod An enrolment type name (default manual)
 */
function enrol_user_this($courseinfo, $user, $roleid, $enrolmethod = 'manual') {
    global $DB;

    $course = $DB->get_record('course', array('shortname' => $courseinfo['shortname']), '*', MUST_EXIST);
    $context = context_course::instance($course->id);

    if (!is_enrolled($context, $user)) {
        $enrol = enrol_get_plugin($enrolmethod);
        if ($enrol === null) {
            return false;
        }
        $instances = enrol_get_instances($course->id, true);
        $manualinstance = null;
        foreach ($instances as $instance) {
            if ($instance->enrol == $enrolmethod) {
                $manualinstance = $instance;
                break;
            }
        }
        
        $enrol->enrol_user($manualinstance, $user->id, $roleid, time(), 0);
    }
}

/**
 * Create a new cohort object
 *
 * @param string $name A course shortname
 * @return int The id of the new cohort
 */
function create_ukfn_cohort($name) {
    global $CFG;
    require_once($CFG->dirroot.'/cohort/lib.php');

    $cohort = new stdClass();
    $cohort->contextid = context_system::instance()->id;
    $cohort->name = $name;
    $cohort->idnumber = $name;
    $cohort->description = 'This cohort is for the UKfilmnet classroom course with a shortname of '.$name;
    $cohort->descriptionformat = FORMAT_HTML;
    $id = cohort_add_cohort($cohort);
    
    return $id;
}

/**
 * Create a data object for use in creating a cohort sync enrolment
 *
 * @param string $name A Cohort sync name
 * @param int $cohortid A cohort id
 * @param string $default_role_shortname A name for the Cohort sync's default role 
 * @return object A basic Cohort sync enrolment object
 */
function create_cohortsync_data($name, $cohortid, $default_role_shortname) {
    global $DB;
    $roleid = $DB->get_record('role', array('shortname'=>$default_role_shortname))->id;
    $data = array('name'=>$name, 'customint1'=>$cohortid,
                  'roleid'=>$roleid, 'customint2'=>0);
    return $data;
}

/**
 * Class to extend enrol_plugin for the purpose of implementing a useable verion of add_instance
 * 
 * This probably could have been done by adding an add_instance function in enrol_ukfilmnet lib.php
 *
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrolukfn_cohort_plugin extends enrol_plugin {

    /**
     * Create an new instance of the Cohort sync enrolment plugin
     *
     * @param object $course A Moodle course object
     * @param array $fields An array of instance fields (default = null)
     * @return mixed An int that is the id of the object created, null if no object was created
     */
    function add_instance($course, array $fields = null) {
        global $CFG, $DB;

        if (!empty($fields['customint2']) && $fields['customint2'] == COHORT_CREATE_GROUP) {
            // Create a new group for the cohort if requested.
            $context = context_course::instance($course->id);
            require_capability('moodle/course:managegroups', $context);
            $groupid = enrol_cohort_create_new_group($course->id, $fields['customint1']);
            $fields['customint2'] = $groupid;
        }

        $result = parent::add_instance($course, $fields);

        require_once("$CFG->dirroot/enrol/cohort/locallib.php");
        $trace = new null_progress_trace();
        enrol_cohort_sync($trace, $course->id);
        $trace->finished();
        return $result;
    }
}

/**
 * Creates an array from a json encoded txt file
 *
 * @param string $save_filename The name of a json encolded text file
 * @return array The array created
 */
function get_array_from_json_file($save_filename) {
    global $CFG;

    $target_array = [];
    $target_string = file_get_contents($CFG->dirroot.'/enrol/ukfilmnet/assets/'.$save_filename);
    if($target_string) {
        $target_array[] = json_decode($target_string, true);
    }
    return $target_array;
}

/**
 * Redirects browser to a given target page
 * 
 * Used to redirect an Applicant user to the plugin page that matches their current application progress level, or to the site front page
 *
 * @param string $target_page An alias reference to a target plugin page based upon an Applicant user's application progress level
 */
function go_to_page($target_page) {
    global $CFG;
    switch($target_page) {
        case '0':
            redirect(PAGE_WWWROOT);
            break;
        case '1':
            redirect(PAGE_APPLICANT);
            break;
        case '2':
            redirect(PAGE_EMAILVERIFY);
            break;
        case '3':
            redirect(PAGE_COURSES);
            break;
        case '4':
            redirect(PAGE_SCHOOL);
            break;
        case '5':
            redirect(PAGE_SAFEGUARDING);
            break;
        case '6':
            redirect(PAGE_STUDENTS);
            break;
        case '7':
            redirect(PAGE_STUDENTS);
            break;
        case '8':
            redirect(PAGE_STUDENTS);
            break;
        case 'admin':
            redirect(PAGE_ADMIN);
            break;
        default:
            break;
    }
}

/**
 * Redirects browser to a given target page
 * 
 * Used to redirect an Applicant user to the plugin page that matches their current application progress level (but not to the site's frontpage)
 * 
 * Note: this function is only called once in the plugin (in applicantpage.php). The single call to this function should probably be replace by a call to the go_to_page function above, after which this function could be removed from the code.
 *
 * @param string $target_page An alias reference to a target plugin page based upon an Applicant user's application progress level
 */
function force_signup_flow($target_page) {
    global $CFG;
    switch($target_page) {
        case '1':
            redirect($CFG->wwwroot.'/enrol/ukfilmnet/emailverify.php');
            break;
        case '2':
            redirect($CFG->wwwroot.'/enrol/ukfilmnet/courses.php');
            break;
        case '3':
            redirect($CFG->wwwroot.'/enrol/ukfilmnet/school.php');
            break;
        case '4':
            redirect($CFG->wwwroot.'/enrol/ukfilmnet/safeguarding.php');
            break;
        case '5':
            redirect($CFG->wwwroot.'/enrol/ukfilmnet/students.php');
            break;
        case '6':
            redirect($CFG->wwwroot.'/enrol/ukfilmnet/students.php');
            break;
        default:
            redirect($CFG->wwwroot);
    }
}

/**
 * Gets school name based upon the school's UKPRN 
 *
 * @param string $target_ukprn A UKPRN in string format or possibly as an int
 * @return mixed A string, an int, or null
 */
function get_schoolname($target_ukprn) {
    $target = $target_ukprn[0];
    $ukprns = get_array_from_json_file('uk_schools_selector_list_array.txt');
    $schoolname = '';
    foreach($ukprns as $ukprn) {
        foreach($ukprn as $num) {
            if($num[1] == $target) {
                return $num[0];
            }
        }
    }
}

/**
 * Creates an array from a .csv file and saves it as a json encoded .txt file
 *
 * This is a helper function for the update_list function below
 * 
 * @param string $csvfile The path, including file name, to a csv file
 * @param string $save_filename The name of the .txt file to be saved
 */
function create_array_from_csv($csvfile, $save_filename) {
    global $CFG;

    $csvfile = file($CFG->dirroot.'/enrol/ukfilmnet/assets/'.$csvfile);
    $data = [];
    foreach($csvfile as $line) {
        $data[] = str_getcsv($line);
    } 
    $target = fopen($CFG->dirroot.'/enrol/ukfilmnet/assets/'.$save_filename, 'w');
    if($target) {
        fputs($target, json_encode($data));
    }  
    fclose($target);
    
    // For testing only
    /*$target_array = [];
    $target_string = file_get_contents($CFG->dirroot.'/enrol/ukfilmnet/assets/'.$save_filename);
    if($target_string) {
        $target_array[] = json_decode($target_string, true);
        print_r2($target_array);
    }*/
}

/**
 * Updates the json formated file named uk_schools_selector_list_array.txt which is located in the plugin's assets folder
 * 
 * Important Notes: 
 * 
 * This function presumes there is a file in the assets folder of this plugin named uk_schools_short.csv which has three columns named: 1) Establishmnet names, 2) ukprn, and 3) street fields. This .cvs file must be manually created!
 * 
 * This function first calls the create_array_from_csv function above which creates a json formated file named uk_schools_temp_short.txt which contains an array with subarrays each containing the three fields listed above.
 * 
 * This function takes that uk_schools_temp_short.txt file and concatenates the values of the street field into the Establishment field, then strips the street field from each subarray - it then saves that array as a .csv file and calls the create_array_from_csv  function to create the txt file uk_schools_selector_list_array.txt which file is used by schoolform.php when it calls the create_school_name_select_list function below to provide an array to the Name of school input element
 * 
 * Currently, this function is only run by uncommenting the call to update_list() located in the tracking.php page, and then browsing to that page. If you choose to run this function, make sure to recomment the call to it in tracking.php after you have run this function.
 * 
 * Consider adding this function to a Moodle site admin feature that be used to update the Name of school data programatically
 */
function update_list(){
    create_array_from_csv('uk_schools_short.csv', 'uk_schools_temp_short.txt');
    $schoolinfos = get_array_from_json_file('uk_schools_temp_short.txt');
        $schoolslist = [];
        foreach($schoolinfos as &$infos) {
            $count = 0;
            foreach($infos as &$info) {
                $schoolinfos[0][$count][0] = $info[0].', '.$info[2];
                array_pop($schoolinfos[0][$count]);
                if($info[1] == '' or $info[1] == null) {
                    unset($schoolinfos[0][$count]);
                }
            $count++;
            }
        }
        array_values($schoolinfos[0]);

        asort($schoolinfos[0]);

        foreach($schoolinfos as $info) {
            $schoollist = $info;
        }
        $fp = fopen('./assets/uk_schools_selector_list_array.csv', 'w');
        foreach($schoollist as $list) {
            fputcsv($fp, $list);
        }
        fclose($fp);

        create_array_from_csv('uk_schools_selector_list_array.csv', 'uk_schools_selector_list_array.txt');            
}

/**
 * Create an associative array of UKPRN/schoolname key/value pairs from a json formated .txt file for use in schoolform.php, and in the process remove any records that do not have a UKPRN number
 * 
 * This function is call each time schoolform.php loads
 *
 * @return array The associative array created by the function
 */
function create_school_name_select_list() {
    $schools_list_raw = get_array_from_json_file('uk_schools_selector_list_array.txt');
    $schools_list = [];
    $count = 0;
    foreach($schools_list_raw as $raw) {
        foreach($raw as $ra) {

            if($count > 1 and strlen($ra[0]) > 7) {
                $key = $ra[1];
                $val = $ra[0];
                $schools_list += [$key=>$val];
            }
            $count++;
        }           
    }
    
    return $schools_list;
}

/**
 * Converts the number values of applicant educational roles (these are NOT Moodle roles) to human friendly strings
 *
 * @param string $current_role An educational role key number
 * @return string A human friendly string
 */
function convert_rolenum_to_rolestring($current_role) {
    switch($current_role) {
        case '01':
            return get_string('applicant_role_ukteacher', 'enrol_ukfilmnet');
        case '02':
            return get_string('applicant_role_teacherbsa', 'enrol_ukfilmnet');
        case '03':
            return get_string('applicant_role_uksupplyteacher', 'enrol_ukfilmnet');
        case '04':
            return get_string('applicant_role_instructor18plus', 'enrol_ukfilmnet');
        case '05':
            return get_string('applicant_role_instructor17minus', 'enrol_ukfilmnet');
        case '06':
            return get_string('applicant_role_student17minus', 'enrol_ukfilmnet');
        case '07':
            return get_string('applicant_role_student18plus', 'enrol_ukfilmnet');
        case '08':
            return get_string('applicant_role_industryprofessional', 'enrol_ukfilmnet');
        case '09':
            return get_string('applicant_role_educationconsultant', 'enrol_ukfilmnet');
        case '10':
            return get_string('applicant_role_parentguardian', 'enrol_ukfilmnet');
        default:
            return $current_role;
        break;
    }
}

/**
 * Converts the human friendly string values of applicant educational roles (these are NOT Moodle roles) to number values strings
 *
 * @param string $current_role A human friendly string
 * @return string An educational role key number
 */
function convert_rolestring_to_rolenum($current_role) {
    switch($current_role) {
        case get_string('applicant_role_ukteacher', 'enrol_ukfilmnet'):
            return '01';
        case get_string('applicant_role_teacherbsa', 'enrol_ukfilmnet'):
            return '02';
        case get_string('applicant_role_uksupplyteacher', 'enrol_ukfilmnet'):
            return '03';
        case get_string('applicant_role_instructor18plus', 'enrol_ukfilmnet'):
            return '04';    
        case get_string('applicant_role_instructor17minus', 'enrol_ukfilmnet'):
            return '05'; 
        case get_string('applicant_role_student17minus', 'enrol_ukfilmnet'):
            return '06';
        case get_string('applicant_role_student18plus', 'enrol_ukfilmnet'):
            return '07';
        case get_string('applicant_role_industryprofessional', 'enrol_ukfilmnet'):
            return '08';
        case get_string('applicant_role_educationconsultant', 'enrol_ukfilmnet');
            return '09';
        case get_string('applicant_role_parentguardian', 'enrol_ukfilmnet'):
            return '10';
        default:
            return $current_role;
        break;
    }
}

/**
 * Converts the number values of teacher application progress to human friendly strings
 *
 * @param string $signup_progress A signup progress key number
 * @return string A human friendly string
 */
function convert_progressnum_to_progressstring($signup_progress) {
    switch($signup_progress) {
        case '1':
            return get_string('signup_progress_1', 'enrol_ukfilmnet');
        case '2':
            return get_string('signup_progress_2', 'enrol_ukfilmnet');
        case '3':
            return get_string('signup_progress_3', 'enrol_ukfilmnet');
        case '4':
            return get_string('signup_progress_4', 'enrol_ukfilmnet');
        case '5':
            return get_string('signup_progress_5', 'enrol_ukfilmnet');
        case '6':
            return get_string('signup_progress_6', 'enrol_ukfilmnet');
        case '7':
            return get_string('signup_progress_7', 'enrol_ukfilmnet');
        default:
            return $signup_progress;
        break;
    }
}

/**
 * Converts the human friendly sting values of teacher application progress to progress key numbers
 *
 * @param string $signup_progress A human friendly string
 * @return string A application progress key number
 */
function convert_progressstring_to_progressnum($signup_progress) {
    switch($signup_progress) {
        case get_string('signup_progress_1', 'enrol_ukfilmnet'):
            return '1';
        case get_string('signup_progress_2', 'enrol_ukfilmnet'):
            return '2';
        case get_string('signup_progress_3', 'enrol_ukfilmnet'):
            return '3';
        case get_string('signup_progress_4', 'enrol_ukfilmnet'):
            return '4';    
        case get_string('signup_progress_5', 'enrol_ukfilmnet'):
            return '5'; 
        case get_string('signup_progress_6', 'enrol_ukfilmnet'):
            return '6';
        case get_string('signup_progress_7', 'enrol_ukfilmnet'):
            return '7';
        default:
            return $signup_progress;
        break;
    }
}

/**
 * Converts a unixtimestamp to a human friendly string
 *
 * @param string $date A unixtimestamp date in string format
 * @return string A human friendly date string
 */
function convert_unixtime_to_gmdate($date) {
    $gmdate = gmdate("Y-m-d G:i:s", (int)$date);
    return $gmdate;
}

/**
 * Converts a human friendly date string into a unixtimestamp
 *
 * @param $date A human friendly date string
 * @return string A unixtimestamp
 */
function convert_gmdate_to_unixtime($date) {
    $date_array = preg_split( "/(-|:| )/", $date);
    $unixtime = gmmktime($date_array[3],$date_array[4],$date_array[5],$date_array[1],$date_array[2],$date_array[0]);
    return $unixtime;
}

/*
function create_profile_fields() {
    global $DB;

    $profile_fields = get_profile_field_records();

    foreach($profile_fields as $record) {
        $field_exists = false;
        //$count = 0;
        $existing_profile_fields = $DB->get_records('user_info_field');

        foreach($existing_profile_fields as $field) {
            if($record['shortname'] === $field->shortname) {
                $field_exists = true;
            }
        }
        if($field_exists == false) {
            $record_object = (object)$record;
            $DB->insert_record('user_info_field', $record_object);
        }
    }
}
*/

/**
 * Checks to see if a cohort exists
 *
 * @param string $cohort_idnumber A unique VARCAR identifier  
 * @return boolean True or false
 */
function cohort_exists($cohort_idnumber) {
    global $DB;
    $existing_cohorts = $DB->get_records('cohort');
    $cohort_exists = false;
    foreach($existing_cohorts as $cohort) {
        if($cohort->idnumber === $cohort_idnumber) {
            return $cohort->id;
        }
    }
    return $cohort_exists;
}

/**
 * Create a cohort if it does not exist
 *
 * Note that in cohort table there id and idnumber are different fields, and that idnumber is a unique VARCAR identifier for the cohort that appears with the label "Cohort ID" in Site administration > User > Cohort > Edit.
 * 
 * @param string $cohort_idnumber A unique VARCAR identifier
 * @return int A cohort id
 */
function create_cohort_if_not_existing($cohort_idnumber) {
    global $DB;
    if(cohort_exists($cohort_idnumber) == false) {
        $new_cohort = (object) [
            'id' => null,
            'contextid' => 1,
            'name' => ucfirst($cohort_idnumber),
            'idnumber' => $cohort_idnumber, 
        ];
        
        $new_cohort_id = cohort_add_cohort($new_cohort);
        return $new_cohort_id;
    }
    $existing_cohort_id = get_cohort_id_from_cohort_idnumber($cohort_idnumber);
    return $existing_cohort_id;
}

/**
 * Gets a cohort id from a cohort idnumber
 *
 * @param string A unique VARCAR identifier
 * @return int A cohort id
 */
function get_cohort_id_from_cohort_idnumber($cohort_idnumber) {
    global $DB;
    $cohort = $DB->get_record('cohort', array('idnumber'=>$cohort_idnumber));
    return $cohort->id;
}

/**
 * Enable an enrolment plugin if it is not already enabled
 *
 * @param string A plugin name
 * @return boolean True or false
 */
// 
function enable_enrolment_plugin($plugin_name) {
    $enabled = enrol_get_plugins(true);
    if(!array_key_exists($plugin_name, $enabled)) {
        $enabled[$plugin_name] = true;
        $enabled = array_keys($enabled);
        set_config('enrol_plugins_enabled', implode(',', $enabled));
    }
    unset($enabled);
    $enabled = enrol_get_plugins(true);
    $there = in_array($plugin_name, $enabled);
    if(array_key_exists($plugin_name, $enabled)) {
        return true;
    }
    unset($enabled);
    return false;
}

/*function get_profile_field_records() {

    return Array (
        1 => array
            (
                'id' => '1',
                'shortname' => 'currentrole',
                'name' => 'Current role',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '2',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        2 => array
            (
                'id' => '2',
                'shortname' => 'applicationprogress',
                'name' => 'Application progress',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '1',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        3 => array
            (
                'id' => '3',
                'shortname' => 'verificationcode',
                'name' => 'Email Verification Code',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '3',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        4 => array
            (
                'id' => '4',
                'shortname' => 'emailverified',
                'name' => 'Email verified',
                'datatype' => 'checkbox',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '4',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '0',
                'defaultdataformat' => '0',
                'param1' => '',
                'param2' => '',
                'param3' => '',
                'param4' => '',
                'param5' => '',
            ),

        5 => array
            (
                'id' => '5',
                'shortname' => 'schoolname',
                'name' => 'School name',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '5',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        6 => array
            (
                'id' => '6',
                'shortname' => 'ukprn',
                'name' => 'School UKPRN',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '6',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        7 => array
            (
                'id' => '7',
                'shortname' => 'schoolcountry',
                'name' => 'School country',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '7',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        8 => array
            (
                'id' => '8',
                'shortname' => 'safeguarding_contact_firstname',
                'name' => 'Safeguarding Contact first name',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '8',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        9 => array
            (
                'id' => '9',
                'shortname' => 'safeguarding_contact_familyname',
                'name' => 'Safeguarding Contact family name',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '9',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        10 => array
            (
                'id' => '10',
                'shortname' => 'safeguarding_contact_email',
                'name' => 'Safeguarding Contact email',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '11',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        11 => array
            (
                'id' => '11',
                'shortname' => 'safeguarding_contact_phone',
                'name' => 'Safeguarding Contact phone',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '12',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        12 => array
            (
                'id' => '12',
                'shortname' => 'applicant_consent_to_check',
                'name' => 'Consent to contact employer given',
                'datatype' => 'checkbox',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '13',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '0',
                'defaultdataformat' => '0',
                'param1' => '',
                'param2' => '',
                'param3' => '',
                'param4' => '',
                'param5' => '',
            ),

        13 => array
            (
                'id' => '13',
                'shortname' => 'courses_requested',
                'name' => 'Number of courses requested',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '14',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        14 => array
            (
                'id' => '14',
                'shortname' => 'assurancecode',
                'name' => 'Assurance verification code',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '15',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        15 => array
            (
                'id' => '15',
                'shortname' => 'assurancesubmissiondate',
                'name' => 'Assurance submission date',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '37',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        16 => array
            (
                'id' => '16',
                'shortname' => 'assurancesubmitted',
                'name' => 'Assurance was submitted',
                'datatype' => 'checkbox',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '36',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '0',
                'defaultdataformat' => '0',
                'param1' => '',
                'param2' => '',
                'param3' => '',
                'param4' => '',
                'param5' => '',
            ),

        18 => array
            (
                'id' => '18',
                'shortname' => 'assurancedoc',
                'name' => 'Assurance upload document',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '16',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        20 => array
            (
                'id' => '20',
                'shortname' => 'applicationdenied',
                'name' => 'Application denied',
                'datatype' => 'checkbox',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '38',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '0',
                'defaultdataformat' => '0',
                'param1' => '',
                'param2' => '',
                'param3' => '',
                'param4' => '',
                'param5' => '',
            ),

        21 => array
            (
                'id' => '21',
                'shortname' => 'employment_start_date',
                'name' => 'Employment start date',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '18',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        23 => array
            (
                'id' => '23',
                'shortname' => 'job_title',
                'name' => 'Most recent job title',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '19',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        24 => array
            (
                'id' => '24',
                'shortname' => 'main_duties',
                'name' => 'Employee\'s main duties',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '20',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        25 => array
            (
                'id' => '25',
                'shortname' => 'how_long_employee_known',
                'name' => 'How long employee known',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '21',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        26 => array
            (
                'id' => '26',
                'shortname' => 'capacity_employee_known',
                'name' => 'Capacity employee known',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '22',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        27 => array
            (
                'id' => '27',
                'shortname' => 'dbs_cert_date',
                'name' => 'DBS certificate date',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '23',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        28 => array
            (
                'id' => '28',
                'shortname' => 'dbsnumber',
                'name' => 'DBS certificate number',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '24',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        39 => array
            (
                'id' => '39',
                'shortname' => 'safeguarding_contact_position',
                'name' => 'Safeguarding Contact position',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '10',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        41 => array
            (
                'id' => '41',
                'shortname' => 'school_registered_address',
                'name' => 'School\'s registered address',
                'datatype' => 'textarea',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '34',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '1',
                'param1' => '',
                'param2' => '',
                'param3' => '',
                'param4' => '',
                'param5' => '',
            ),

        42 => array
            (
                'id' => '42',
                'shortname' => 'school_web_address',
                'name' => 'School website address',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '35',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        47 => array
            (
                'id' => '47',
                'shortname' => 'applicationapproved',
                'name' => 'Application approved',
                'datatype' => 'checkbox',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '39',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '0',
                'defaultdataformat' => '0',
                'param1' => '',
                'param2' => '',
                'param3' => '',
                'param4' => '',
                'param5' => '',
            ),

        49 => array
            (
                'id' => '49',
                'shortname' => 'applicant_is_employed',
                'name' => 'Applicant is employed',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '17',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        50 => array
            (
                'id' => '50',
                'shortname' => 'applicant_suitability',
                'name' => 'Applicant is suitable',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '25',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        51 => array
            (
                'id' => '51',
                'shortname' => 'qts_qualified',
                'name' => 'Applicant is QTS qualified',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '26',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        52 => array
            (
                'id' => '52',
                'shortname' => 'behavior_allegations',
                'name' => 'Bad behavior allegations',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '28',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        53 => array
            (
                'id' => '53',
                'shortname' => 'disciplinary_actions',
                'name' => 'Has had disciplinary actions',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '29',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        54 => array
            (
                'id' => '54',
                'shortname' => 'tra_check',
                'name' => 'A TRA check was done',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '30',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        55 => array
            (
                'id' => '55',
                'shortname' => 'ocr_certificate',
                'name' => 'OCR Certificate provided',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '31',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        56 => array
            (
                'id' => '56',
                'shortname' => 'brit_school_abroad_mod_or_dubai_school',
                'name' => 'School is BSA, MOD, or Dubai',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '32',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        57 => array
            (
                'id' => '57',
                'shortname' => 'school_subject_to_inspection',
                'name' => 'School subject to RIP inspection',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '33',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            ),

        58 => array
            (
                'id' => '58',
                'shortname' => 'qtsnumber',
                'name' => 'Applicant QTS number',
                'datatype' => 'text',
                'description' => '',
                'descriptionformat' => '1',
                'categoryid' => '1',
                'sortorder' => '27',
                'required' => '0',
                'locked' => '0',
                'visible' => '0',
                'forceunique' => '0',
                'signup' => '0',
                'defaultdata' => '',
                'defaultdataformat' => '0',
                'param1' => '30',
                'param2' => '2048',
                'param3' => '0',
                'param4' => '',
                'param5' => '',
            )
    );
}
*/

/**
 * Create a Moodle role if it does not exist, and update the role's permissions
 *
 * @param string $custom_full_name A full name for the role
 * @param string $short_name A short name for the role
 * @param string $description A description for the role
 * @param string $role_archetype The role this role will be based on
 * @param array $capabilities_to_change An array of capabilities to change and the permission to be granted or removed (default = '')
 * @param array $context_types An array of contexts for the role
 * @param array $role_assignments An array of role assignments allowed (defaul = '')
 * @param array $role_overrides An array of role overrides allowed (default = '')
 * @param array $role_switches An array of role switches allowed (default = '')
 * @param array $role_to_view An array of role views allowed (default = '')
 */
function create_role_if_not_existing_and_update_role_permissions($custom_full_name, $short_name, $description, $role_archetype, $capabilities_to_change = '', $context_types, $role_assignments='', $role_overrides='', $role_switches='', $role_to_view='') {
    global $DB;
    $role_id = -1;
    $context = context_system::instance();

    // If the role doesn't exit, create it
    if(!$DB->record_exists('role', array('shortname'=>$short_name))) {
        $role_id = create_role($custom_full_name, $short_name, $description, $role_archetype);
        reset_role_capabilities($role_id);
        set_role_contextlevels($role_id, $context_types);
        
    } else {
        $role = $DB->get_record('role', array('shortname'=>$short_name));
        $role_id = $role->id;
    }

    if($role_id === -1) {
        return $role_id;
    }

    // Update role permissions if called for
    if($capabilities_to_change != '') {        
        set_role_contextlevels($role_id, $context_types);
        foreach($capabilities_to_change as $capability) {
            if(get_capability_info($capability[0]) != null) {
                assign_capability($capability[0], $capability[1], $role_id, $context, true);
            }
            else {
                // Uncomment the next line if you wish to warn the Moodle site admin when this script attempts to modify permissions of a role capability that does not exist
                //trigger_error('The <strong>'.$capability[0].'</strong> capability you tried to add or modify does not exist in the <strong>'.$custom_full_name.'</strong> role. Check the code ', E_USER_WARNING);
            }
        }
    }

    // Update Allow role assignments
    if($role_assignments == '') {
        $DB->delete_records('role_allow_assign', array('roleid'=>$role_id));
        foreach($role_assignments as $role) {
            $target_role_id = $DB->get_record('role', array('shortname'=>$role))->id;
            core_role_set_assign_allowed($role_id, $target_role_id);
        }
    }

    // Update Allow role overrides
    if($role_overrides !== '') {
        $DB->delete_records('role_allow_override', array('roleid'=>$role_id));
        foreach($role_overrides as $role) {
            $target_role_id = $DB->get_record('role', array('shortname'=>$role))->id;
            core_role_set_override_allowed($role_id, $target_role_id);
        }
    }

    // Update Allow role switches
    if($role_switches !== '') {
        $DB->delete_records('role_allow_switch', array('roleid'=>$role_id));
        foreach($role_switches as $role) {
            $target_role_id = $DB->get_record('role', array('shortname'=>$role))->id;
            core_role_set_switch_allowed($role_id, $target_role_id);
        }
    }

    // Update Allow role to view
    if($role_to_view !== '') {
        $DB->delete_records('role_allow_view', array('roleid'=>$role_id));
        foreach($role_to_view as $role) {
            $target_role_id = $DB->get_record('role', array('shortname'=>$role))->id;
            core_role_set_view_allowed($role_id, $target_role_id);
        }
    }
    $context->mark_dirty();
    
    return $role_id;
}

/**
 * Delete cohorts that are not associated with any courses
 */
function delete_dangling_cohorts() {
    global $DB;
    require_once('../../cohort/lib.php');

    // Ensure that cohorts with these $course_shortnames will not be deleted
    $course_shortnames = ['applicants', 'students', 'resource_courses', 'support_courses'];
    // Get a list of all course short names and add them to $course_shortnames
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
}

/**
 * Delete temporary SGO accounts
 *
 * Deletes all SGO accounts that are more than 2 hours old - NOTE this is not a complete purge of the user records - complete deletion/purge of a user account must be handled with the built-in functionality at Site administration > Users > Privacy and policies - see https://docs.moodle.org/39/en/GDPR for information about how to use Moodle's Privacy and policies functionality
 */
function delete_temp_sgo_accounts() {
    global $DB;
    require_once(__DIR__.'/../../cohort/lib.php');

    // Get a list of all temp SGO accounts
    $temp_sgo_accounts = $DB->get_records('user', array('firstname'=>'Safeguarding', 'deleted'=>0));

    // If the user account was created more than the time value of the plugin language string ago, delete it
    foreach($temp_sgo_accounts as $account) {
        if((int)$account->timecreated < ((int)strtotime(date('Y-m-d H:i:s')) - get_string('sgo_temp_account_max_life', 'enrol_ukfilmnet'))) {
            delete_user($account);
        }
    }
}

/**
 * Suspend accounts that are not associated with any cohorts
 * 
 * Note: This function does NOT suspend temp SGO accounts, admin accounts, accounts with manager capabilities, or the guest account 
 */
function suspend_nocohort_accounts() {
    global $DB;
    require_once(__DIR__.'/../../cohort/lib.php');

    // Get a list of all users who are not temp SGO's, do not have admin rights, and are not the guest user
    $all_users = $DB->get_records('user');
    $non_sgo_admin_guest_users = [];
    $context = context_system::instance();
    foreach($all_users as $user) {
        if($user->firstname !== 'Safeguarding' and !(has_capability('moodle/role:manage', $context, $user)) and $user->username !== 'guest' and $user->deleted != 1) {
            $non_sgo_admin_guest_users[] = $user;
        }
    }
    // For each user in the list of users who are not temp SGO's, guests, or admins, if they are not a memeber of any cohort, suspend their account
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
            //delete_user($user);
            $DB->set_field('user', 'suspended', 1, array('id'=>$user->id));
        }
    }
}

/**
 * Warn and eventually remove stale Applicant accounts from cohorts
 * 
 * Note: The accounts of users who are not members of any cohorts are suspended within 24 hours
 */
function remove_stale_applicant_accounts() {
    global $DB;
    require_once(__DIR__.'/../../cohort/lib.php');

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
}