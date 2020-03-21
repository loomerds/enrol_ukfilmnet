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
require_once('assuranceform.php');
require_once('signuplib.php');


// This is a Template Class it collects/creates the data for a template
class assurancepage implements \renderable, \templatable {

    //private $is_logged_in = 0;
    /*private $ukprn;
    private $username;
    private $firstname;
    private $familyname;
    private $schoolname;*/


    public function __construct($sometext = null) {
    }

    public function export_for_template(\renderer_base $output) {
        $data = new stdClass();
        $data->assuranceinput = $this->get_assurance_content();
        return $data;
    }

    public function get_assurance_content() {

        global $CFG, $DB, $USER, $SESSION;
        //require_once('/../../../../../user/profile/lib.php');

        
        $assuranceinput = '';
        //$SESSION->is_logged_in = 0;
        $mform = new assurance_form();

        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            
            // Delete temporary Safguarding user
            if($USER->firstname === 'Safeguarding') {
                delete_user($USER);
            }
            
            redirect(PAGE_WWWROOT);
        } else if ($form_data = $mform->get_data()) {
            // Process validated data here
            
            // Save the Assurance Form file uploaded by the Officer
            $fullpath = $CFG->dirroot.'/enrol/ukfilmnet/assurancefiles';
            $override = false;
            $filename = $mform->get_new_filename('assurance_form');
            $success = $mform->save_file('assurance_form', $fullpath.'/'.$filename, $override);
            $count = 0;
            while($success === false && $count < 10) {
                $filename = str_replace(' ', '_', make_random_numstring().$filename);
                $success = $mform->save_file('assurance_form', $fullpath.'/'.$filename, $override);
                $count = $count+1;
            }

            // Get the relevant applicant's user object
            $applicant_user = $DB->get_record('user', array('username' => $form_data->email, 'auth' => 'manual'));
            
            // Update the relevant applicant's user profile
            if($applicant_user !== null) {
                profile_load_data($applicant_user);
                $applicant_user->profile_field_employment_status = $form_data->applicant_is_employed;
                $applicant_user->profile_field_employment_start_date = $form_data->employment_start_date;
                $applicant_user->profile_field_job_title = $form_data->job_title;
                $applicant_user->profile_field_main_duties = $form_data->main_duties;
                $applicant_user->profile_field_how_long_employee_known = $form_data->how_long_employee_known;
                $applicant_user->profile_field_capacity_employee_known = $form_data->capacity_employee_known;
                $applicant_user->profile_field_dbs_cert_date = $form_data->dbs_cert_date;
                $applicant_user->profile_field_dbsnumber = $form_data->dbsnumber;
                $applicant_user->profile_field_applicant_suitability = $form_data->applicant_suitability;
                $applicant_user->profile_field_qts_qualified = $form_data->qts_qualified;
                $applicant_user->profile_field_qtsnumber = $form_data->qtsnumber;
                $applicant_user->profile_field_behavior_allegations = $form_data->behavior_allegations;
                $applicant_user->profile_field_disciplinary_actions = $form_data->disciplinary_actions;
                $applicant_user->profile_field_tra_check = $form_data->tra_check;
                $applicant_user->profile_field_ocr_certificate = $form_data->ocr_certificate;
                $applicant_user->profile_field_brit_school_abroad_mod_or_dubai_school = $form_data->brit_school_abroad_mod_or_dubai_school;
                $applicant_user->profile_field_school_subject_to_inspection = $form_data->school_subject_to_inspection;
                $applicant_user->profile_field_safeguarding_contact_firstname = $form_data->referee_firstname;
                $applicant_user->profile_field_safeguarding_contact_familyname = $form_data->referee_familyname;
                $applicant_user->profile_field_safeguarding_contact_position = $form_data->safeguarding_contact_position;
                $applicant_user->profile_field_safeguarding_contact_email = $form_data->referee_email;
                $applicant_user->profile_field_school_registered_address = $form_data->school_registered_address;
                $applicant_user->profile_field_school_registered_address = $form_data->school_registered_address;
                $applicant_user->profile_field_assurancesubmitted = 1;
                $applicant_user->profile_field_assurancesubmissiondate = convert_unixtime_to_gmdate(time());
                $applicant_user->profile_field_assurancedoc = $filename;
                $applicant_user->profile_field_applicationprogress = convert_progressnum_to_progressstring(6);
                profile_save_data($applicant_user);
            }
            // Delete temporary Safguarding user
            if($USER->firstname === 'Safeguarding') {
                delete_user($USER);
            }
            redirect(PAGE_WWWROOT);
        } else {
print_r2($_POST);
            // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed or on the first display of the form
            $form_data = $mform->get_data();

            //Set default data (if any)
            $mform->set_data($form_data);
            
            $post_assurance_code = $_POST['assurance_code'];
            $post_email = $_POST['email'];
            $applicant_user = $DB->get_record('user', array('username' => $post_email, 'auth' => 'manual'));   
            $assurance_code;
            $email;
            if($applicant_user !== false) {
                profile_load_data($applicant_user);
                $assurance_code = $applicant_user->profile_field_assurancecode;
                $email = $applicant_user->email;
            
                if($_POST['submitbutton'] === 'Login') {
                    if($assurance_code == $post_assurance_code and $email == $post_email) {
                        $SESSION->is_logged_in = 1;
                        $SESSION->ukprn = $applicant_user->profile_field_ukprn;
                        $SESSION->username = $applicant_user->username;
                        $SESSION->firstname = $applicant_user->firstname;
                        $SESSION->familyname = $applicant_user->lastname;
                        $SESSION->schoolname = $applicant_user->profile_field_schoolname;
                    }
                }
            }

            //displays the form
            $assuranceinput = $mform->render();
        }

        return $assuranceinput;
    }
}
