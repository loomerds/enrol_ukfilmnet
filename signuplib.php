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
    $newuser->profile_field_currentrole = $applicantinfo->currentrole;
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
        } if(!empty($_POST['approved'])) {
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
        if($count>2) {
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
    foreach($datum as $key => $data) {
        if(is_array($data)) {
            foreach($data as $s_key => $s_data) {
                $students[$s_key][$key] = $s_data;
            }
        }
    }
    unset($data);
    unset($s_data);

    // Remove rows of data if they don't contain an email address
    foreach($students as $key => $student) {
        if(!isset($student['student_email']) or strlen($student['student_email']) < 2 or $student['student_email'] === null) {
            unset($students[$key]);
                    } 
    } 

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
    add_to_cohort($students);
}

function add_to_cohort($studentinputs) {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/cohort/lib.php');    
    
    foreach ($studentinputs as $input) {
        $cohort_idnumbers = [];
        
        $user;
        $count = 0;
        
        foreach($input as $item) {
            
            if($count == 0) {
                $user = $DB->get_record('user', array('email'=>$item));
                $count++;
            }elseif($count < 3) {
                $count++;
            }else{
                if(strlen($item) > 1) {
                    $cohort_idnumbers[] = $item;
                }
            }
        }

        foreach($cohort_idnumbers as $idnumber) {
            $target_cohort = $DB->get_record('cohort', array('idnumber'=>$idnumber));    

            if(isset($target_cohort)) {
                cohort_add_member($target_cohort->id, $user->id);
            }
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
            if($applicant_user->profile_field_applicationprogress == '5') {
                $applicant_user->profile_field_applicationdenied = '1';
                $applicant_user->profile_field_applicationprogress = '1';
                profile_save_data($applicant_user);
                email_user_accept_reject($applicant_user, "denied");
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
        if($applicant_user !== null) {
            profile_load_data($applicant_user);
            if($applicant_user->profile_field_applicationprogress == '5') {
                $applicant_user->profile_field_applicationapproved = '1';
                $applicant_user->profile_field_applicationprogress = '6';
                profile_save_data($applicant_user);        
                email_user_accept_reject($applicant_user, "approved");
                
                for($count = 0; $count<$applicant_user->profile_field_courses_requested; $count++) {
                    $newcourse = create_classroom_course_from_teacherid($userid, 
                            get_string('template_course', 'enrol_ukfilmnet'), 
                            get_string('course_category', 'enrol_ukfilmnet'));

                    $approvedteacher_role = $DB->get_record('role', array('shortname'=>'user'));
                    $systemcontext = context_system::instance();
                    $usercontext = context_user::instance($applicant_user->id);
                    role_assign($approvedteacher_role->id, $applicant_user->id, $systemcontext->id);
                    role_assign($approvedteacher_role->id, $applicant_user->id, $usercontext->id);
                    
                    enrol_user_this($newcourse, $applicant_user, '3', 'manual');
                }
            }
        }
    }
}

function email_user_accept_reject($applicant, $status){
    
    profile_load_data($applicant);

    $emailvariables = (object) array('firstname'=>$applicant->firstname, 
                                     'familyname'=>$applicant->lastname, 
                                     'email'=>$applicant->email);
    if($status === "approved") {
        email_to_user($applicant, get_admin(), get_string('determination_subject', 'enrol_ukfilmnet', $emailvariables), get_string('determination_text_approved', 'enrol_ukfilmnet', $emailvariables));
    }elseif($status === "denied") {
        email_to_user($applicant, get_admin(), get_string('determination_subject', 'enrol_ukfilmnet', $emailvariables), get_string('determination_text_denied', 'enrol_ukfilmnet', $emailvariables));
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
                $highest_end = $end_num;
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

function force_progress($application_progress, $current_page) {
    global $CFG;

    switch($application_progress) {
        case '2':
            if($current_page != '2') {    
                echo "<script>location.href='./emailverify.php'</script>";
                //echo "'<script>'.$CFG->dirroot.'./enrol/ukfilmnet/emailverify.php'</script>";
                //redirect($CFG->wwwroot.'/enrol/ukfilmnet/emailverify.php');
            }
            break;
        case '3':
            if($current_page != '3') {  
                echo "<script>location.href='./school.php'</script>";
                //echo "'<script>'.$CFG->dirroot.'./enrol/ukfilmnet/school.php'</script>";
                //redirect($CFG->wwwroot.'/enrol/ukfilmnet/school.php');
            }
            break;
        case '4':
            if($current_page != '4') {  
               echo "<script>location.href='./courses.php'</script>";
               //echo "'<script>'.$CFG->dirroot.'./enrol/ukfilmnet/courses.php'</script>";
               //redirect($CFG->wwwroot.'/enrol/ukfilmnet/courses.php');
            }
            break;
        case '5':
            if($current_page != '5') {  
                echo "<script>location.href='./safeguarding.php'</script>";
                //echo "'<script>'.$CFG->dirroot.'./enrol/ukfilmnet/safeguarding.php'</script>";
                //redirect($CFG->wwwroot.'/enrol/ukfilmnet/safeguarding.php');
            }
            break;
        case '6':
            if($current_page != '6') {  
                echo "<script>location.href='./students.php'</script>";
                //echo "'<script>'.$CFG->dirroot.'./enrol/ukfilmnet/students.php'</script>";
                //redirect($CFG->wwwroot.'/enrol/ukfilmnet/students.php');
            }
            break;
        default:
        break;
            //echo "<script>location.href='./emailverify.php'</script>";
            //echo "'<script>'.$CFG->dirroot.'./enrol/ukfilmnet/emailverify.php'</script>";
            //redirect($CFG->wwwroot.'/enrol/ukfilmnet/emailverify.php');
    }
}

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
            redirect(PAGE_SCHOOL);
            break;
        case '4':
            redirect(PAGE_COURSES);
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
            //if($current_page != '2') {    
               //echo "<script>location.href='./emailverify.php'</script>";
                //echo "'<script>'.$CFG->dirroot.'./enrol/ukfilmnet/emailverify.php'</script>";
            redirect($CFG->wwwroot.'/enrol/ukfilmnet/emailverify.php');
            //}
            break;
        case '2':
            //if($current_page != '2') {    
                //echo "<script>location.href='./emailverify.php'</script>";
                //echo "'<script>'.$CFG->dirroot.'./enrol/ukfilmnet/emailverify.php'</script>";
            redirect($CFG->wwwroot.'/enrol/ukfilmnet/school.php');
            //}
            break;
        case '3':
            //if($current_page != '3') {  
                //echo "<script>location.href='./school.php'</script>";
                //echo "'<script>'.$CFG->dirroot.'./enrol/ukfilmnet/school.php'</script>";
            redirect($CFG->wwwroot.'/enrol/ukfilmnet/courses.php');
            //}
            break;
        case '4':
            //if($current_page != '4') {  
               //echo "<script>location.href='./courses.php'</script>";
               //echo "'<script>'.$CFG->dirroot.'./enrol/ukfilmnet/courses.php'</script>";
            redirect($CFG->wwwroot.'/enrol/ukfilmnet/safeguarding.php');
            //}
            break;
        case '5':
            //if($current_page != '5') {  
                //echo "<script>location.href='./safeguarding.php'</script>";
                //echo "'<script>'.$CFG->dirroot.'./enrol/ukfilmnet/safeguarding.php'</script>";
            redirect($CFG->wwwroot.'/enrol/ukfilmnet/students.php');
            //}
            break;
        case '6':
            //if($current_page != '6') {  
                //echo "<script>location.href='./students.php'</script>";
                //echo "'<script>'.$CFG->dirroot.'./enrol/ukfilmnet/students.php'</script>";
            redirect($CFG->wwwroot.'/enrol/ukfilmnet/students.php');
            //}
            break;
        default:
        //break;
            //echo "<script>location.href='./emailverify.php'</script>";
            //echo "'<script>'.$CFG->dirroot.'./enrol/ukfilmnet/emailverify.php'</script>";
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
                //return $num[1];
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
}
