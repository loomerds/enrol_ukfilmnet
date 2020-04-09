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
 * Generator tool functions.
 *
 * @package    enrol_ukfilmnet
 * @copyright  2019, Doug Loomer
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** The user is put onto a waiting list and therefore the enrolment not active 
 * (used in user_enrolments->status) 
 */

global $CFG;

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

/**
 * Creates a bare-bones user record
 *
 * @todo Outline auth types and provide code example
 *
 * @param array $applicantinfo Array of new user objects
 * @param string $password New users password to add to record
 * @param string $auth Form of authentication required
 * @return stdClass A complete user object
 */

function is_applicant_user($user) {
    profile_load_data($user);
    $is_applicant = strlen($user->profile_field_applicationprogress) >0;
    if (isguestuser() or !$is_applicant) {
        redirect(PAGE_WWWROOT);
    }

} 

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
    set_user_preference('auth_forcepasswordchange', 0, $user);

    // Set the password.
    update_internal_user_password($user, $password);

    return $user;
}

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

function create_sgo_user($applicantinfo, $auth = 'manual') {
//print_r2($applicantinfo);
    global $CFG, $DB;
    require_once($CFG->dirroot.'/user/profile/lib.php');
    require_once($CFG->dirroot.'/user/lib.php');
    require_once($CFG->dirroot.'/lib/accesslib.php');
    require_once($CFG->dirroot.'/lib/moodlelib.php');
    
    $username = trim(core_text::strtolower($applicantinfo->profile_field_safeguarding_contact_email));
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
    $newuser->email = $applicantinfo->profile_field_safeguarding_contact_email;
    $newuser->firstname = 'DSL-'.$applicantinfo->profile_field_safeguarding_contact_firstname;
    $newuser->lastname = $applicantinfo->profile_field_safeguarding_contact_familyname;

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

    $user = get_complete_user_data('id', $newuser->id);
    set_user_preference('auth_forcepasswordchange', 0, $user);

    // Set the password.
    update_internal_user_password($user, make_random_password());

    return $user;
}

function print_r2($val){
    echo '<pre>';
    echo '<br>';
    echo '<br>';
    echo '<br>';
    echo '<br>';
    print_r($val);
    echo  '</pre>';
}

function handle_enrol_students_post($datum) {
    global $CFG;

    if($datum['submit_type'] == 'Exit') {
        redirect($CFG->wwwroot);
    } else {
        process_students($datum);
        
    }
}

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

// Takes data returned from the form and uses it to create student user accounts and place students into cohorts
function process_students($datum) {
    global $DB;

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
    $removed = rtrim($removed, ",");

    // Turn each row of student data into an object and give students Moodle accounts if they don't already have accounts
    $students = array_values($students);

    foreach($students as $student) {
        $users = $DB->get_records('user');
        $email_taken = false;
        foreach($users as $user) {
            if($student['student_email'] == $user->email) {
                $email_taken = true;
                break;
            }
        }
        if($email_taken == false) {
            create_student_user((object)$student);
        }
    }

    // Add students to appropriate cohorts
    add_or_remove_students_to_cohorts($students, $datum['cohort_names']);

    if($removed === '' or $removed === null) {
        redirect(PAGE_WWWROOT.'/enrol/ukfilmnet/students.php');
    } else {
        $removed = '<p class="ukfn_error_feedback">The student(s) having the following email addresses'.$removed.' were not added because you did not include one or more of the required fields - Email, First Name, or Family Name.</p>';
        $_SESSION['removed_message'] = $removed;
        redirect(PAGE_WWWROOT.'/enrol/ukfilmnet/students.php');
    }
}

// Note that $target_cohort receives a reference to the cohort object's id field
// Note that $cohortid receives a reference to the cohort object's cohortid field
// We don't actually need this function - cohort_add_member already stops duplicates
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
            if(strlen($cohort_idnumbers[$count]) > 2) { // if the input is checked

                // Note that $target_cohort->id is the cohort object's id field
                //if(is_already_in_cohort($user, $target_cohort->id, $cohort_name) == false) {
                    // add them to $cohort_name
                    cohort_add_member($target_cohort->id, $user->id);
                    cohort_add_member($resourse_courses_cohort->id, $user->id);
                //}
                
            } else {  // if the input is NOT checked
                //if(is_already_in_cohort($user, $target_cohort->id, $cohort_name) == true) {
                    // remove them from $cohort_name
                    cohort_remove_member($target_cohort->id, $user->id);
                //} 
            }
            $count++;
        }
    }
}

function make_username($email) {
    return substr($email,0,stripos($email,'@',0));
}

function applicant_login($username, $password) {
    $user = authenticate_user_login($username, $password);
    return complete_user_login($user);
}

function generate_random_verification_code() {
    return rand(111111, 999999);
}

function make_random_password() {
    return 'ukfilm'.rand(1000, 9999);
}

function make_random_numstring() {
    return rand(1, 1000);
}

// this function needs to be fixed
function convert_clean_string_assoc_array ($dirty_array) {
    $approved_and_cleaned = [];
    foreach($dirty_array as $dirty) {
        var_dump($dirty);
        //var_dump("wooo");
        //$clean = [];
        //foreach($dirty as $value) {
            //$clean[] = ['userid'=>$value];
            $clean['userid'] = $dirty;
            $clean = filter_var_array($clean, array('userid'=>FILTER_SANITIZE_STRING));
        //}
        $approved_and_cleaned[] = $clean;
        var_dump($approved_and_cleaned);
    }
    //var_dump($clean);
    return $approved_and_cleaned;
}

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

function application_approved($approved) {
    global $DB, $CFG;
    include_once($CFG->dirroot.'/course/externallib.php');
    include_once($CFG->dirroot.'/lib/enrollib.php');

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
                            get_string('template_course', 'enrol_ukfilmnet'), 
                            get_string('course_category', 'enrol_ukfilmnet'));

                    $approvedteacher_role = $DB->get_record('role', array('shortname'=>'user'));
                    $systemcontext = context_system::instance();
                    $usercontext = context_user::instance($applicant_user->id);
                    
                    // Change applicant's basic system role assignment
                    role_assign($approvedteacher_role->id, $applicant_user->id, $systemcontext->id);
                    role_assign($approvedteacher_role->id, $applicant_user->id, $usercontext->id);
                    
                    // Enrol applicant in their classroom course(s) as a teacher
                    enrol_user_this($newcourse, $applicant_user, '3', 'manual');
                }

                // Add teacher to resource_courses
                //if(is_already_in_cohort($applicant_user, $resource_courses_cohort->id) == false) {
                cohort_add_member($resource_courses_cohort->id, $applicant_user->id);
                //}
                // Add teacher to support_courses
                //if(is_already_in_cohort($applicant_user, $support_courses_cohort->id) == false) {
                cohort_add_member($support_courses_cohort->id, $applicant_user->id);
                //}
                $sgo_user = create_sgo_user($applicant_user);
                add_sgo_to_cohorts($applicant_user, $sgo_user);
            }
        }
    }
}

function add_sgo_to_cohorts($applicant_user, $sgo_user) {
    
}

function email_user_accept_reject($applicant, $status){
    global $CFG;
    
    profile_load_data($applicant);

    $emailvariables = (object) array('firstname'=>$applicant->firstname, 
                                     'familyname'=>$applicant->lastname, 
                                     'email'=>$applicant->email);
    if($status === "denied") {
        email_to_user($applicant, get_admin(), get_string('determination_subject', 'enrol_ukfilmnet', $emailvariables), get_string('determination_text_denied', 'enrol_ukfilmnet', $emailvariables));
    } elseif($status === "approved") {
        email_to_user($applicant, get_admin(), get_string('determination_subject', 'enrol_ukfilmnet', $emailvariables), get_string('determination_text_approved', 'enrol_ukfilmnet', $emailvariables));
    }
}

function create_classroom_course_from_teacherid ($teacherid, $template, $category_name) {
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
    $miscellaneous_categoryid = '';

    foreach($categories as $category) {
        if($category->name == $category_name) {
            $target_categoryid = $category->id;
        }
        if($category->name == get_string('misc_course_category', 'enrol_ukfilmnet')) {
            $miscellaneous_categoryid = $category->id;
        }
    }
    if($target_categoryid == '') {
        $target_categoryid = $miscellaneous_categoryid;
        
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
    $cohortid = create_ukfn_cohort($newcourse['shortname'], $courseinfo['id']);
    $course_created = $DB->get_record('course', array('shortname'=>$courseinfo['shortname']));
    $data = create_cohortsync_data($courseinfo['shortname'], $cohortid);
    
    // Add the new cohort to the new cours's corhort sync
    $cohort_plugin = new enrolukfn_cohort_plugin();
    $cohort_plugin->add_instance($course_created, $data);

    // Return a reference to the new course
    return $courseinfo;
}

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
        /*if ($manualinstance == null) {
            $instanceid = $enrol->add_default_instance($course);
            if ($instanceid === null) {
                $instanceid = $enrol->add_instance($course);
            }
            $instance = $DB->get_record('enrol', array('id' => $instanceid));
        }*/
        $enrol->enrol_user($manualinstance, $user->id, $roleid, time(), 0);
    }
}

function create_ukfn_cohort($name, $courseid) {
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

function create_cohortsync_data($name, $cohortid) {
    $data = array('name'=>$name, 'customint1'=>$cohortid,
                  'roleid'=>5, 'customint2'=>0);
    return $data;
}

class enrolukfn_cohort_plugin extends enrol_plugin {

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
    $target_array = [];
    $target_string = file_get_contents($CFG->dirroot.'/enrol/ukfilmnet/assets/'.$save_filename);
    if($target_string) {
        $target_array[] = json_decode($target_string, true);
    }
}

function get_array_from_json_file($save_filename) {
    global $CFG;

    $target_array = [];
    $target_string = file_get_contents($CFG->dirroot.'/enrol/ukfilmnet/assets/'.$save_filename);
    if($target_string) {
        $target_array[] = json_decode($target_string, true);
    }
    return $target_array;
}

/*function force_progress($application_progress, $current_page) {
    global $CFG;

    switch($application_progress) {
        case '2':
            if($current_page != '2') {    
                echo "<script>location.href='./emailverify.php'</script>";
            }
            break;
        case '3':
            if($current_page != '3') {  
                echo "<script>location.href='./courses.php'</script>";
            }
            break;
        case '4':
            if($current_page != '4') {  
               echo "<script>location.href='./school.php'</script>";
            }
            break;
        case '5':
            if($current_page != '5') {  
                echo "<script>location.href='./safeguarding.php'</script>";
            }
            break;
        case '6':
            if($current_page != '6') {  
                echo "<script>location.href='./students.php'</script>";
            }
            break;
        default:
        break;
    }
}*/

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
        default:
            break;
    }
}

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


// Presumes there is a file in the assets folder named _uk_schools_short.txt which has an array with subarrays each containing Establishmnet names, ukprn, and street fields - which file can be created by calling the create_array_from_csv($csvfile, $save_filename) function on a .csv file containing these three fields as columns
// This function takes that file and concatenates the street field into the Establishment field, then strips the street field from each subarray - it then saves that array as a .csv file and calls the create_array_from_csv() function to create the txt file uk_schools_selector_list_array.txt which is used by schoolform.php to provide an array to the Name of school input element
// Consider adding this to a Moodle site admin feature that be used to update the Name of school data programatically

function update_list(){
    $schoolinfos = get_array_from_json_file('uk_schools_short.txt');
        $schoolslist = [];
        foreach($schoolinfos as $infos) {
            $count = 0;
            foreach($infos as $info) {
                //if($infos[$count]>10) {
                $schoolinfos[0][$count][0] = $info[0].', '.$info[2];
                array_pop($schoolinfos[0][$count]);
                
                //}
            $count++;
            }
            
        }
        foreach($schoolinfos as $info) {
            $schoollist = $info;
        }
        
        $fp = fopen($CFG->dirroot.'/enrol/ukfilmnet/assets/uk_schools_selector_list_array.csv', 'w');
        foreach($schoollist as $list) {
            fputcsv($fp, $list);
        }
        fclose($fp);

        create_array_from_csv('uk_schools_selector_list_array.csv', 'uk_schools_selector_list_array.txt');            
}

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

function convert_unixtime_to_gmdate($date) {
    $gmdate = gmdate("Y-m-d G:i:s", (int)$date);
    return $gmdate;
}

function convert_gmdate_to_unixtime($date) {
    $date_array = preg_split( "/(-|:| )/", $date);
    $unixtime = gmmktime($date_array[3],$date_array[4],$date_array[5],$date_array[1],$date_array[2],$date_array[0]);
    return $unixtime;
}

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
//print_r2('i ran');
}

function get_profile_field_records() {


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




    /*return array ( 
        1 => array( 'id' => '1',
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
        2 => array( 'id' => '2',
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
        3 => array( 'id' => '3',
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

        4 => array( 'id' => '4',
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
                    'param1' => NULL,
                    'param2' => NULL,
                    'param3' => NULL,
                    'param4' => NULL,
                    'param5' => NULL,
                ),

        5 => array( 'id' => '5',
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
        6 => array( 'id' => '6',
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
        7 => array( 'id' => '7',
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
        8 => array( 'id' => '8',
                    'shortname' => 'safeguarding_contact_firstname',
                    'name' => 'Safeguarding contact first name',
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
        9 => array( 'id' => '9',
                    'shortname' => 'safeguarding_contact_familyname',
                    'name' => 'Safeguarding contact family name',
                    'datatype' => 'text',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '9',
                    'required' => '0',
                    'locked' => '0',
                    'v,
                    a' => '',
                    'defaultdataformat' => '0',
                    'param1' => '30',
                    'param2' => '2048',
                    'param3' => '0',
                    'param4' => '',
                    'param5' => '',
                ),
        10 => array( 'id' => '10',
                    'shortname' => 'safeguarding_contact_email',
                    'name' => 'Safeguarding contact email',
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
        11 => array( 'id' => '11',
                    'shortname' => 'safeguarding_contact_phone',
                    'name' => 'Safeguarding contact phone',
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
        12 => array( 'id' => '12',
                    'shortname' => 'applicant_consent_to_check',
                    'name' => 'Employer contact consent',
                    'datatype' => 'checkbox',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '12',
                    'required' => '0',
                    'locked' => '0',
                    'visible' => '0',
                    'forceunique' => '0',
                    'signup' => '0',
                    'defaultdata' => '0',
                    'defaultdataformat' => '0',
                    'param1' => NULL,
                    'param2' => NULL,
                    'param3' => NULL,
                    'param4' => NULL,
                    'param5' => NULL,
                ),
        13 => array( 'id' => '13',
                    'shortname' => 'courses_requested',
                    'name' => 'Number of courses',
                    'datatype' => 'text',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '13',
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
        14 => array( 'id' => '14',
                    'shortname' => 'assurancecode',
                    'name' => 'Assurance verification code',
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
        15 => array( 'id' => '15',
                    'shortname' => 'assurancesubmissiondate',
                    'name' => 'Assurance submission date',
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
        16 => array( 'id' => '16',
                    'shortname' => 'assurancesubmitted',
                    'name' => 'Assurance was submitted',
                    'datatype' => 'checkbox',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '16',
                    'required' => '0',
                    'locked' => '0',
                    'visible' => '0',
                    'forceunique' => '0',
                    'signup' => '0',
                    'defaultdata' => '0',
                    'defaultdataformat' => '0',
                    'param1' => NULL,
                    'param2' => NULL,
                    'param3' => NULL,
                    'param4' => NULL,
                    'param5' => NULL,
                ),
        17 => array( 'id' => '17',
                    'shortname' => 'qtsnumber',
                    'name' => 'Applicant QTS number',
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
        18 => array( 'id' => '18',
                    'shortname' => 'assurancedoc',
                    'name' => 'Assurance upload document',
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
        19 => array( 'id' => '19',
                    'shortname' => 'applicationapproved',
                    'name' => 'Application approved',
                    'datatype' => 'checkbox',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '42',
                    'required' => '0',
                    'locked' => '0',
                    'visible' => '0',
                    'forceunique' => '0',
                    'signup' => '0',
                    'defaultdata' => '0',
                    'defaultdataformat' => '0',
                    'param1' => NULL,
                    'param2' => NULL,
                    'param3' => NULL,
                    'param4' => NULL,
                    'param5' => NULL,
                ),
        20 => array( 'id' => '20',
                    'shortname' => 'applicationdenied',
                    'name' => 'Application denied',
                    'datatype' => 'checkbox',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '41',
                    'required' => '0',
                    'locked' => '0',
                    'visible' => '0',
                    'forceunique' => '0',
                    'signup' => '0',
                    'defaultdata' => '0',
                    'defaultdataformat' => '0',
                    'param1' => NULL,
                    'param2' => NULL,
                    'param3' => NULL,
                    'param4' => NULL,
                    'param5' => NULL,
                ),
        21 => array( 'id' => '21',
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
        22 => array( 'id' => '22',
                    'shortname' => 'employment_end',
                    'name' => 'Employment end date',
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
        23 => array( 'id' => '23',
                    'shortname' => 'job_title',
                    'name' => 'Most recent job title',
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
        24 => array( 'id' => '24',
                    'shortname' => 'main_duties',
                    'name' => 'Employee\'s main duties',
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
        25 => array( 'id' => '25',
                    'shortname' => 'how_long_employee_known',
                    'name' => 'How long employee known',
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
        26 => array( 'id' => '26',
                    'shortname' => 'capacity_employee_known',
                    'name' => 'Capacity employee known',
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
        27 => array( 'id' => '27',
                    'shortname' => 'dbs_cert_date',
                    'name' => 'DBS certificate date',
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
        28 => array( 'id' => '28',
                    'shortname' => 'dbsnumber',
                    'name' => 'DBS certificate number',
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
        29 => array( 'id' => '29',
                    'shortname' => 'applicant_suitability',
                    'name' => 'Suitability of applicant',
                    'datatype' => 'checkbox',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '26',
                    'required' => '0',
                    'locked' => '0',
                    'visible' => '0',
                    'forceunique' => '0',
                    'signup' => '0',
                    'defaultdata' => '0',
                    'defaultdataformat' => '0',
                    'param1' => NULL,
                    'param2' => NULL,
                    'param3' => NULL,
                    'param4' => NULL,
                    'param5' => NULL,
                ),
        30 => array( 'id' => '30',
                    'shortname' => 'qts_qualified',
                    'name' => 'QTS qualified',
                    'datatype' => 'checkbox',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '27',
                    'required' => '0',
                    'locked' => '0',
                    'visible' => '0',
                    'forceunique' => '0',
                    'signup' => '0',
                    'defaultdata' => '0',
                    'defaultdataformat' => '0',
                    'param1' => NULL,
                    'param2' => NULL,
                    'param3' => NULL,
                    'param4' => NULL,
                    'param5' => NULL,
                ),
        31 => array( 'id' => '31',
                    'shortname' => 'behavior_allegations',
                    'name' => 'Behavior allegations',
                    'datatype' => 'checkbox',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '29',
                    'required' => '0',
                    'locked' => '0',
                    'visible' => '0',
                    'forceunique' => '0',
                    'signup' => '0',
                    'defaultdata' => '0',
                    'defaultdataformat' => '0',
                    'param1' => NULL,
                    'param2' => NULL,
                    'param3' => NULL,
                    'param4' => NULL,
                    'param5' => NULL,
                ),
        32 => array( 'id' => '32',
                    'shortname' => 'disciplinary_actions',
                    'name' => 'Disciplinary actions',
                    'datatype' => 'checkbox',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '30',
                    'required' => '0',
                    'locked' => '0',
                    'visible' => '0',
                    'forceunique' => '0',
                    'signup' => '0',
                    'defaultdata' => '0',
                    'defaultdataformat' => '0',
                    'param1' => NULL,
                    'param2' => NULL,
                    'param3' => NULL,
                    'param4' => NULL,
                    'param5' => NULL,
                ),
        33 => array( 'id' => '33',
                    'shortname' => 'tra_check',
                    'name' => 'TRA list check ',
                    'datatype' => 'checkbox',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '31',
                    'required' => '0',
                    'locked' => '0',
                    'visible' => '0',
                    'forceunique' => '0',
                    'signup' => '0',
                    'defaultdata' => '0',
                    'defaultdataformat' => '0',
                    'param1' => NULL,
                    'param2' => NULL,
                    'param3' => NULL,
                    'param4' => NULL,
                    'param5' => NULL,
                ),
        34 => array( 'id' => '34',
                    'shortname' => 'ocr_certificate',
                    'name' => 'Overseas Criminal Record certificate',
                    'datatype' => 'checkbox',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '33',
                    'required' => '0',
                    'locked' => '0',
                    'visible' => '0',
                    'forceunique' => '0',
                    'signup' => '0',
                    'defaultdata' => '0',
                    'defaultdataformat' => '0',
                    'param1' => NULL,
                    'param2' => NULL,
                    'param3' => NULL,
                    'param4' => NULL,
                    'param5' => NULL,
                ),
        35 => array( 'id' => '35',
                    'shortname' => 'brit_school_abroad_mod_or_dubai_school',
                    'name' => 'British School Abroad,
                    M.O.D.,
                    or Dubai School',
                    'datatype' => 'checkbox',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '34',
                    'required' => '0',
                    'locked' => '0',
                    'visible' => '0',
                    'forceunique' => '0',
                    'signup' => '0',
                    'defaultdata' => '0',
                    'defaultdataformat' => '0',
                    'param1' => NULL,
                    'param2' => NULL,
                    'param3' => NULL,
                    'param4' => NULL,
                    'param5' => NULL,
                ),
        36 => array( 'id' => '36',
                    'shortname' => 'subject_to_ocr_check',
                    'name' => 'Subject to OCR check',
                    'datatype' => 'checkbox',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '32',
                    'required' => '0',
                    'locked' => '0',
                    'visible' => '0',
                    'forceunique' => '0',
                    'signup' => '0',
                    'defaultdata' => '0',
                    'defaultdataformat' => '0',
                    'param1' => NULL,
                    'param2' => NULL,
                    'param3' => NULL,
                    'param4' => NULL,
                    'param5' => NULL,
                ),
        37 => array( 'id' => '37',
                    'shortname' => 'school_subject_to_inspection',
                    'name' => 'School subject to RIP inspections',
                    'datatype' => 'checkbox',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '35',
                    'required' => '0',
                    'locked' => '0',
                    'visible' => '0',
                    'forceunique' => '0',
                    'signup' => '0',
                    'defaultdata' => '0',
                    'defaultdataformat' => '0',
                    'param1' => NULL,
                    'param2' => NULL,
                    'param3' => NULL,
                    'param4' => NULL,
                    'param5' => NULL,
                ),
        38 => array( 'id' => '38',
                    'shortname' => 'referee_name',
                    'name' => 'Referee name',
                    'datatype' => 'text',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '36',
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
        39 => array( 'id' => '39',
                    'shortname' => 'referee_position',
                    'name' => 'Referee position',
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
        40 => array( 'id' => '40',
                    'shortname' => 'referee_email',
                    'name' => 'Referee email',
                    'datatype' => 'text',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '38',
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
        41 => array( 'id' => '41',
                    'shortname' => 'school_registered_address',
                    'name' => 'School\'s registered address',
                    'datatype' => 'textarea',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '39',
                    'required' => '0',
                    'locked' => '0',
                    'visible' => '0',
                    'forceunique' => '0',
                    'signup' => '0',
                    'defaultdata' => '',
                    'defaultdataformat' => '1',
                    'param1' => NULL,
                    'param2' => NULL,
                    'param3' => NULL,
                    'param4' => NULL,
                    'param5' => NULL,
                ),
        42 => array( 'id' => '42',
                    'shortname' => 'school_web_address',
                    'name' => 'School website address',
                    'datatype' => 'text',
                    'description' => '',
                    'descriptionformat' => '1',
                    'categoryid' => '1',
                    'sortorder' => '40',
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
    );*/
}