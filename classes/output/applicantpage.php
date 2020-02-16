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

namespace enrol_ukfilmnet\output;

defined('MOODLE_INTERNAL' || die());

use stdClass;
use moodle_url;
use context_system;


require_once('applicantform.php');
require_once('signuplib.php');
require_once($CFG->libdir.'/datalib.php');
//require_once($CFG->libdir.'/adminlib.php');

// This is a Template Class it collects/creates the data for a template
class applicantpage implements \renderable, \templatable {

    var $sometext = null;

    public function __construct($sometext = null) {
        $this->sometext = $sometext;
    }

    public function export_for_template(\renderer_base $output) {
        $data = new stdClass();
        $data->applicantinput = $this->get_applicant_content();
        return $data;
    }

    public function get_applicant_content() {

        global $CFG, $SESSION;
        require_once($CFG->dirroot.'/enrol/locallib.php');
        
        $applicantinput = '';
        $mform = new applicant_form();

        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            redirect('https://ukfilmnet.org');
        } else if ($fromform = $mform->get_data()) {
            //In this case you process validated data. $mform->get_data() returns data posted in form.
            $form_data = $mform->get_data();
            
            //Build a object we can use to pass username, password, and code variables to the email we will send to applicant
            //$username = make_username($form_data->email);
            $username = $form_data->email;
            $password = make_random_password();
            $code = generate_random_verification_code();
            $emailvariables = (object) array('username'=>$username, 'password'=>$password, 'code'=>$code);

            $newuser = (object) array('email'=>$form_data->email,'username'=>$username,'firstname'=>$form_data->firstname,'lastname'=>$form_data->familyname, 'currentrole'=>$form_data->role, 'applicationprogress'=>2, 'verificationcode'=>$code);
            $user = create_applicant_user($newuser, $password);

            email_to_user($user, get_admin(), get_string('verification_subject', 'enrol_ukfilmnet'), get_string('verification_text', 'enrol_ukfilmnet', $emailvariables));
            $SESSION->applicant_info_complete = true;
            
        } else {
            // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed or on the first display of the form.
            $toform = $mform->get_data();
            $SESSION->applicant_info_complete = false;
            //Set default data (if any)
            $mform->set_data($toform);
            //displays the form
            $applicantinput = $mform->render();
            //var_dump($applicantinput);
        }
        return $applicantinput;
    }

}
