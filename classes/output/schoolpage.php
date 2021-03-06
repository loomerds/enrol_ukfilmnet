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
 * A Template Class to collect/create the data for the school.php page.
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

require_once('schoolform.php');
require_once('signuplib.php');
require_once($CFG->libdir.'/datalib.php');

class schoolpage implements \renderable, \templatable {
    
    private $page_number;
   
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
        $data->schoolinput = $this->get_school_content();
        return $data;
    }

    /**
     * Gets form data and processes it and data it may create for rendering
     *
     * @return string Data obtained for rendering
     */
    public function get_school_content() {

        global $CFG, $USER, $DB;
        require_once($CFG->dirroot.'/enrol/ukfilmnet/signuplib.php');
        
        $schoolinput = '';
        $mform = new school_form();

        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            // retain this for possible future use
        } else if ($form_data = $mform->get_data()) {
            // Process validated data here.
            
            // Get form data and add to the user's profile_fields
            profile_load_data($USER);
            $USER->profile_field_applicant_consent_to_check = $form_data->school_consent_to_contact;
            $USER->profile_field_ukprn = $form_data->ukprn[0];

            $USER->profile_field_schoolname = get_schoolname($form_data->ukprn);
            $USER->profile_field_schoolcountry = $form_data->school_country;
            $USER->profile_field_safeguarding_contact_firstname = $form_data->contact_firstname;
            $USER->profile_field_safeguarding_contact_familyname = $form_data->contact_familyname;
            $USER->profile_field_safeguarding_contact_email = $form_data->contact_email;
            $USER->profile_field_safeguarding_contact_phone = $form_data->contact_phone;
            $USER->profile_field_assurancecode = generate_random_verification_code();
            $USER->profile_field_applicationprogress = convert_progressnum_to_progressstring(5);
            profile_save_data($USER);

            //Build a object for the email we will send to safeguarding officer
            $ukprn = $form_data->ukprn;
            $schoolname_and_street = explode(',', $USER->profile_field_schoolname);
            $schoolname = $schoolname_and_street[0];
            $schoolstreet = $schoolname_and_street[1];
            $schoolcountry = $form_data->school_country;
            $contact_firstname = $form_data->contact_firstname;
            $contact_familyname = $form_data->contact_familyname;
            $applicant_firstname = $USER->firstname;
            $applicant_familyname = $USER->lastname;
            $applicant_email = $USER->email;
            $assurance_code = $USER->profile_field_assurancecode;
            $form_url = $CFG->wwwroot.'/enrol/ukfilmnet/assets/AssuranceForm.pdf';
            $assurance_url = PAGE_WWWROOT.get_string('assurance_url', 'enrol_ukfilmnet'); 

            // Create a temporary safeguarding officer user
            $tempuser = (object) array(
                'email'=>$form_data->contact_email,
                'username'=>$form_data->contact_email,
                'firstname'=>'Safeguarding',
                'lastname'=>$form_data->contact_familyname, 
                'currentrole'=>$form_data->role, 
                'applicationprogress'=>0, 
                'verificationcode'=>'000000');
            $existinguser = $DB->record_exists('user', array('email'=>$form_data->contact_email));
            if($existinguser == false) {
                $contact_user = create_applicant_user($tempuser, 'make_random_password');
            } else {
                $contact_user = $DB->get_record('user', array('email'=>$form_data->contact_email));
            }

            // Create array of variables for email to safeguarding officer
            $emailvariables = (object) array('schoolname_ukprn'=>$ukprn, 
                                             'schoolname'=>$schoolname,
                                             'schoolcountry'=>$schoolcountry, 
                                             'contact_firstname'=>$form_data->contact_firstname,
                                             'contact_familyname'=>$contact_familyname,
                                             'applicant_firstname'=>$applicant_firstname,
                                             'applicant_familyname'=>$applicant_familyname,
                                             'applicant_email'=>$applicant_email,
                                             'assurance_code'=>$assurance_code,
                                             'form_url'=>$form_url,
                                             'assurance_url'=>$assurance_url,
                                             'helpdesk_url'=>get_string('helpdesk_url', 'enrol_ukfilmnet'),
                                             'ukfilmnet_url'=>PAGE_WWWROOT);
            // Send email to safeguarding officer
            email_to_user($contact_user, get_admin(), get_string('assurance_subject', 'enrol_ukfilmnet', $emailvariables), get_string('assurance_text', 'enrol_ukfilmnet', $emailvariables));

            // Delete temporary safeguarding officer user
            if($contact_user->firstname === 'Safeguarding') {
                delete_user($contact_user);
            }
        } else {
            // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed or on the first display of the form.
            $toform = $mform->get_data();
            //Set default data (if any)
            $mform->set_data($toform);
            //displays the form
            $schoolinput = $mform->render();
        }
        return $schoolinput;
    }
}
