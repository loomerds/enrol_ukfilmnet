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
 * A Template Class to collect/create the data for the applicant.php page.
 *
 * @package    enrol_ukfilmnet
 * @copyright  2020, Doug Loomer
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_ukfilmnet\output;

defined('MOODLE_INTERNAL' || die());

use stdClass;
use moodle_url;
use context_system;
use manager;

require_once('applicantform.php');
require_once($CFG->libdir.'/datalib.php');

class applicantpage implements \renderable, \templatable {

    public $page_number;

    /**
     * A class constructor
     *
     * @param string $page_number A number in string format
     * @return object An object of this class
     */
    public function __construct($page_number) {
        $this->page_number = $page_number;
    }

    /**
     * Exports data to for rendering
     *
     * @param object A renderer class object
     * @return object The data captured and created by this object
     */
    public function export_for_template(\renderer_base $output) {
        $data = new stdClass();
        $data->applicantinput = $this->get_applicant_content();
        return $data;
    }

    /**
     * Gets form data and processes it and data it may create for rendering
     *
     * @return string Data obtained for rendering
     */
    public function get_applicant_content() {
        
        global $CFG, $SESSION, $USER;
        require_once($CFG->dirroot.'/enrol/locallib.php');
        require_once($CFG->dirroot.'/enrol/ukfilmnet/signuplib.php');
        require_once($CFG->dirroot.'/lib/classes/session/file.php');
        require_once($CFG->dirroot.'/cohort/lib.php');
        
        $applicantinput = '';
        $mform = new applicant_form();

        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            go_to_page(strval(0));
        } else if ($form_data = $mform->get_data()) {
            //Build a object we can use to pass variables to the email we will send to applicant
            $username = $form_data->email;
            $password = make_random_password();
            $code = generate_random_verification_code();
            $emailvariables = (object) array('username'=>$username, 
                                             'password'=>$password, 
                                             'code'=>$code,
                                             'helpdesk_url'=>get_string('helpdesk_url', 'enrol_ukfilmnet'),
                                             'emailverify_url'=>PAGE_WWWROOT.get_string('emailverify_url', 'enrol_ukfilmnet'));
            // Create a new user
            $newuser = (object) array('email'=>$form_data->email,
                                      'username'=>$username,
                                      'firstname'=>$form_data->firstname,
                                      'lastname'=>$form_data->familyname,
                                      'currentrole'=>$form_data->role,
                                      'applicationprogress'=>convert_progressnum_to_progressstring(2),
                                      'verificationcode'=>$code);
            $user = create_applicant_user($newuser, $password);
            
            // Set the new user's applicationprogress variable
            profile_load_data($user);
            $user->profile_field_applicationprogress = convert_progressnum_to_progressstring(2);

            // Add the new user to the Applicants cohort
            $cohort_id = create_cohort_if_not_existing('applicants');
            cohort_add_member($cohort_id, $user->id);

            // Make the new user the currently logged in user
            \core\session\manager::set_user($user);
            
            // Email the applicant user
            email_to_user($user, get_admin(), get_string('verification_subject', 'enrol_ukfilmnet'), get_string('verification_text', 'enrol_ukfilmnet', $emailvariables));
            
        } else {
            // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed or on the first display of the form.
            $toform = $mform->get_data();
            //Set default data (if any)
            $mform->set_data($toform);
            //displays the form
            $applicantinput = $mform->render();
        }
        return $applicantinput;
    }

    /**
     * Handles page redirects if page is cancelled or applicant is not allowed to visit this page, or it returns true 
     *
     * @return boolean Redirects or returns true
     */
    public function handle_redirects() {
        global $CFG, $SESSION;
        require_once(__DIR__.'/../../signuplib.php');

        if(isset($SESSION->cancel) and $SESSION->cancel == 1) {
            $SESSION->cancel = 0;
            redirect($CFG->wwwroot);
        } elseif($this->page_number != convert_progressstring_to_progressnum($this->applicantprogress)) {
            force_signup_flow($this->page_number);
        }
        return true;
    }

}
