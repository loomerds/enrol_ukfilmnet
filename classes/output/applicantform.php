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

/** 
 *  
 */

namespace enrol_ukfilmnet\output;

defined('MOODLE_INTERNAL' || die());

require_once("$CFG->libdir/formslib.php");

class applicant_form extends \moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB;
        $mform = $this->_form; 
        $current_roles = ['0'=>get_string('applicant_role_instruction', 'enrol_ukfilmnet'),
                          '01'=>get_string('applicant_role_ukteacher', 'enrol_ukfilmnet'), 
                          '02'=>get_string('applicant_role_teacherbsa', 'enrol_ukfilmnet'),
                          '03'=>get_string('applicant_role_uksupplyteacher', 'enrol_ukfilmnet'),
                          '04'=>get_string('applicant_role_instructor18plus', 'enrol_ukfilmnet'),
                          '05'=>get_string('applicant_role_instructor17minus', 'enrol_ukfilmnet'),
                          '06'=>get_string('applicant_role_student17minus', 'enrol_ukfilmnet'),
                          '07'=>get_string('applicant_role_student18plus', 'enrol_ukfilmnet'),
                          '08'=>get_string('applicant_role_industryprofessional', 'enrol_ukfilmnet'),
                          '09'=>get_string('applicant_role_educationconsultant', 'enrol_ukfilmnet'),
                          '10'=>get_string('applicant_role_parentguardian', 'enrol_ukfilmnet')];
        $mform->addElement('select', 'role', get_string('applicant_current_role', 'enrol_ukfilmnet'), $current_roles, ['class'=>'ukfn-applicant-current-roles']);
        $mform->addRule('role', null, 'required', null, 'server');
        $mform->addElement('text', 'email', get_string('applicant_email', 'enrol_ukfilmnet'), ['class'=>'ukfn_applicant_email']);
        $mform->setType('email', PARAM_NOTAGS);
        $mform->addRule('email', get_string('error_missing_email', 'enrol_ukfilmnet'), 'required', null, 'server');
        $mform->addRule('email', get_string('error_invalid_email', 'enrol_ukfilmnet'), 'email', null, 'server');


        $mform->addElement('text', 'firstname', get_string('applicant_firstname', 'enrol_ukfilmnet'), ['class'=>'ukfn-applicant-firstname']);
        $mform->setType('firstname', PARAM_TEXT);
        $mform->addRule('firstname', get_string('error_missing_firstname', 'enrol_ukfilmnet'), 'required', null, 'server');
        $mform->addElement('text', 'familyname', get_string('applicant_familyname', 'enrol_ukfilmnet'), ['class'=>'ukfn-applicant-familyname']);
        $mform->setType('familyname', PARAM_TEXT);
        $mform->addRule('familyname', get_string('error_missing_familyname', 'enrol_ukfilmnet'), 'required', null, 'server');
        $mform->addElement('static', '', '', get_string('applicant_agreement', 'enrol_ukfilmnet', null));
        $this->add_action_buttons($cancel=true, $submitlabel=get_string('button_submit', 'enrol_ukfilmnet'), ['class'=>'ukfn-form-buttons']);          
    }
    //Custom validation should be added here
    function validation($data, $files) {
        global $SESSION, $DB;
        $errors = parent::validation($data, $files);
        
        if((int)$data['role'] > (int)get_string('roleallowed_range_max', 'enrol_ukfilmnet')) {
            $errors['role'] = get_string('error_role_limits', 'enrol_ukfilmnet');
        }
        if($data['role'] === '0') {
            $errors['role'] = get_string('error_missing_role', 'enrol_ukfilmnet');
        }
        if($this->check_for_email_conflict($data['email']) == true) {
            $errors['email'] = get_string('error_existing_email', 'enrol_ukfilmnet');
        }
        return $errors;
    }

    function check_for_email_conflict($email) {
        global $DB;

        $users = $DB->get_records('user');
        $email_taken = false;
        foreach($users as $user) {
            if($email == $user->email) {
                $email_taken = true;
                break;
            }
        }
        return $email_taken;
    }
}