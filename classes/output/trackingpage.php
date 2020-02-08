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

//require_once('schoolform.php');
//require_once('signuplib.php');
//require_once($CFG->libdir.'/datalib.php');

// This is a Template Class it collects/creates the data for a template
class trackingpage implements \renderable, \templatable {
    var $sometext = null;

    public function __construct($sometext = null) {
        $this->sometext = $sometext;
    }

    public function export_for_template(\renderer_base $output) {
        $data = new stdClass();
        $data = $this->get_tracking_content();
        return $data;
    }

    public function get_tracking_content() {
        global $CFG, $USER, $DB;
        
        $headings = array('title0'=>'Approved', 'title1'=>'Progress', 'title2'=>'Role', 'title3'=>'Name', 'title4'=>'Email',
                          'title5'=>'Country', 'title6'=>'School', 'title7'=>'SG Name', 'title8'=>'SG Phone', 
                          'title9'=>'SG Email', 'title10'=>'SG Form', 'title11'=>'Form Date', 'title12'=>'Denied');
        $rows = [];
        $applicants = $DB->get_records('user', array('deleted'=>0)); 
        
        foreach($applicants as $applicant) {
            profile_load_data($applicant);
            if($applicant->profile_field_applicationprogress > 1) {
                $rows[] = ['userid'=>$applicant->id, 'firstname'=>$applicant->firstname,
                           'familyname'=>$applicant->lastname, 'email'=>$applicant->email, 
                           'currentrole'=>$applicant->profile_field_currentrole, 
                           'applicationprogress'=>$applicant->profile_field_applicationprogress, 
                           'schoolname'=>$applicant->profile_field_schoolname, 
                           'schoolcountry'=>$applicant->profile_field_schoolcountry, 
                           'contact_firstname'=>$applicant->profile_field_safeguarding_contact_firstname,
                           'contact_familyname'=>$applicant->profile_field_safeguarding_contact_familyname, 
                           'contact_email'=>$applicant->profile_field_safeguarding_contact_email,
                           'contact_phone'=>$applicant->profile_field_safeguarding_contact_phone, 
                           'qtsnumber'=>$applicant->profile_field_qtsnumber, 
                           'assurancesubmissiondate'=>$this->check_date_exists($applicant->profile_field_assurancesubmissiondate), 
                           'assurancedoc'=>$this->check_download_exists($applicant->profile_field_assurancedoc),
                           'applicationapproved'=>$this->application_approved($applicant->profile_field_applicationapproved, $applicant->id),
                           'applicationdenied'=>$this->application_denied($applicant->profile_field_applicationdenied, $applicant->id)];
            }
        }
        $trackingdata = ['headings'=>$headings, 'rows'=>$rows];
        //var_dump($trackingdata);
        /*$mform = new tracking_form();

        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            redirect('https://ukfilmnet.org/learning');
        } else if ($fromform = $mform->get_data()) {
            //In this case you process validated data. $mform->get_data() returns data posted in form.
                        
            $form_data = $mform->get_data();
            
            profile_load_data($USER);
            $USER->profile_field_applicant_consent_to_check = $form_data->school_consent_to_contact;
            $USER->profile_field_schoolname = $form_data->school_name;
            $USER->profile_field_schoolcountry = $form_data->school_country;
            $USER->profile_field_safeguarding_contact_firstname = $form_data->contact_firstname;
            $USER->profile_field_safeguarding_contact_familyname = $form_data->contact_familyname;
            $USER->profile_field_safeguarding_contact_email = $form_data->contact_email;
            $USER->profile_field_safeguarding_contact_phone = $form_data->contact_phone;
            $USER->profile_field_assurancecode = generate_random_verification_code();
            $USER->profile_field_applicationprogress = 4;

            profile_save_data($USER);

            //Build a object we can use to pass username, password, and code variables to the email we will send to applicant
            $schoolname = $form_data->school_name;
            $schoolcountry = $form_data->school_country;
            $contact_firstname = $form_data->contact_firstname;
            $contact_familyname = $form_data->contact_familyname;
            $applicant_firstname = $USER->firstname;
            $applicant_familyname = $USER->lastname;
            $applicant_email = $USER->email;
            $assurance_code = $USER->profile_field_assurancecode;
            $newuser = (object) array('email'=>$form_data->contact_email,'username'=>$form_data->contact_email,'firstname'=>$form_data->contact_firstname,'lastname'=>$form_data->contact_familyname, 'currentrole'=>$form_data->role, 'applicationprogress'=>0, 'verificationcode'=>'000000');
            $contact_user = create_applicant_user($newuser, 'make_random_password');

            $emailvariables = (object) array('schoolname'=>$schoolname, 
                                             'schoolcountry'=>$schoolcountry, 
                                             'contact_firstname'=>$contact_firstname,
                                             'contact_familyname'=>$contact_familyname,
                                             'applicant_firstname'=>$applicant_firstname,
                                             'applicant_familyname'=>$applicant_familyname,
                                             'applicant_email'=>$applicant_email,
                                             'assurance_code'=>$assurance_code);
            
            email_to_user($contact_user, get_admin(), get_string('assurance_subject', 'enrol_ukfilmnet', $emailvariables), get_string('assurance_text', 'enrol_ukfilmnet', $emailvariables));
            
            //var_dump(mail($form_data->contact_email, get_string('assurance_subject', 'enrol_ukfilmnet', $emailvariables), get_string('assurance_text', 'enrol_ukfilmnet', $emailvariables)));
        } else {
            // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
            // or on the first display of the form.
            $toform = $mform->get_data();
            
            //Set default data (if any)
            $mform->set_data($toform);
            //displays the form
            $trackinginput = $mform->render();
        }*/

        return $trackingdata;
    }

    private function check_date_exists($date) {
        if($date > 0) {
            return gmdate("Y-m-d", (int)$date);
        }
        return null;
    }

    private function check_download_exists($file) {
        if(strlen($file) > 0) {
            return '<a href=./assurancefiles'.'/'.$file.'>View</a>';
        }
        return null;
    }

    private function application_approved($approval_status, $id) {
        //var_dump($approval_status);
        if($approval_status == 1) {
            return '<input type="checkbox" name="approved[]" value="'.$id.'" checked="checked">';
        }
        return '<input type="checkbox" name="approved[]" value="'.$id.'">';
    }

    private function application_denied($denial_status, $id) {
        //var_dump($approval_status);
        if($denial_status == 1) {
            return '<input type="checkbox" name="denied[]" value="'.$id.'" checked="checked">';
        }
        return '<input type="checkbox" name="denied[]" value="'.$id.'">';
    }

}
