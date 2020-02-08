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
defined('MOODLE_INTERNAL') || die();

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
            if($applicant_user->profile_field_applicationprogress == '4') {
                $applicant_user->profile_field_applicationdenied = '1';
                $applicant_user->profile_field_applicationprogress = '5';
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
            if($applicant_user->profile_field_applicationprogress == '4') {
                $applicant_user->profile_field_applicationapproved = '1';
                $applicant_user->profile_field_applicationprogress = '5';
                profile_save_data($applicant_user);        
                email_user_accept_reject($applicant_user, "approved");
                $newcourse = create_classroom_course_from_teacherid($userid, 
                        get_string('template_course', 'enrol_ukfilmnet'), 
                        get_string('course_category', 'enrol_ukfilmnet'));

                $approvedteacher_role = $DB->get_record('role', array('shortname'=>'user'));
                $systemcontext = context_system::instance();
                $usercontext = context_user::instance($applicant_user->id);
                role_assign($approvedteacher_role->id, $applicant_user->id, $systemcontext->id);
                role_assign($approvedteacher_role->id, $applicant_user->id, $usercontext->id);
                
                enrol_user_this($newcourse, $applicant_user, '3');
                //create_ukfn_cohort()
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
    
    $teacher = $DB->get_record('user', array('id' => $teacherid, 'auth' => 'manual'));
    profile_load_data($teacher);
    
    $target_courseid;
    $lastname = $teacher->lastname;
    $lastname_length = strlen($lastname);
    $unique_shortname = $lastname.'-0';
    $highest_end = 0;
    
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

    $categories = core_course_category::get_all();

    $target_categoryid = '';
    $miscellaneous_categoryid = '';

    foreach($categories as $category) {
        if($category->name == $category_name) {
            $target_categoryid = $category->id;
        }
        if($category->name == 'Miscellaneous') {
            $miscellaneous_categoryid = $category->id;
        }
    }

    if($target_categoryid == '') {
        $target_categoryid = $miscellaneous_categoryid;
    }

    $newcourse['courseid'] = $target_courseid;
    $newcourse['fullname'] = $unique_shortname;
    $newcourse['shortname'] = $unique_shortname;
    $newcourse['categoryid'] = $target_categoryid;
    $courseinfo = core_course_external::duplicate_course($newcourse['courseid'], 
                                           $newcourse['fullname'], 
                                           $newcourse['shortname'], 
                                           true);
    create_ukfn_cohort($newcourse['shortname']);
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
            if ($instance->name == $enrolmethod) {
                $manualinstance = $instance;
                break;
            }
        }
        if ($manualinstance !== null) {
            $instanceid = $enrol->add_default_instance($course);
            if ($instanceid === null) {
                $instanceid = $enrol->add_instance($course);
            }
            $instance = $DB->get_record('enrol', array('id' => $instanceid));
        }

        $enrol->enrol_user($instance, $user->id, $roleid);
    }
}

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
}

/*public function get_list_of_uk_schools($returnall = false, $lang = null) {
    global $CFG;

    if ($lang === null) {
        $lang = current_language();
    }

    $uk_schools = $this->load_component_strings('core_countries', $lang);
    core_collator::asort($uk_schools);
    if (!$returnall and !empty($CFG->allcountrycodes)) {
        $enabled = explode(',', $CFG->allcountrycodes);
        $return = array();
        foreach ($enabled as $c) {
            if (isset($countries[$c])) {
                $return[$c] = $countries[$c];
            }
        }
        return $return;
    }

    return $countries;
}*/