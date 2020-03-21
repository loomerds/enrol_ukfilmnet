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


class assurance_form extends \moodleform {

    //Add elements to form
    public function definition() {
        global $CFG,  $USER, $SESSION;
        require_once($CFG->dirroot.'/lib/formslib.php');

//print_r2($SESSION);
        $maxbytes = 9185760;
        $mform = $this->_form;

        if($SESSION->is_logged_in == 0) {
            $mform->addElement('html', '<div class="ukfn_log-in_box ukfn_not_stealth">');
        } else {
            $mform->addElement('html', '<div class="ukfn_log-in_box ukfn_stealth">');
        }
            $mform->addElement('html', '<div class ="ukfn_form_text">'.get_string('assurance_login_instructions', 'enrol_ukfilmnet').'</div>');
            $mform->addElement('html', '<div class="ukfn_log-in_details ukfn_form_even">');
                $mform->addElement('text', 'email', get_string('employee_work_email', 'enrol_ukfilmnet'));
                $mform->setType('email', PARAM_NOTAGS);
                $mform->addRule('email', get_string('error_missing_employee_work_email', 'enrol_ukfilmnet'), 'required', null, 'server');

                $mform->addElement('text', 'assurance_code', get_string('assurance_code', 'enrol_ukfilmnet'));
                $mform->setType('assurance_code', PARAM_NOTAGS);
                $mform->addRule('assurance_code', get_string('error_missing_assurance_code', 'enrol_ukfilmnet'), 'required', null, 'server');
            $mform->addElement('html', '</div>');
            $mform->addElement('html', '<div class="ukfn_button_group">');
                $buttonarray = [];
                $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('button_login', 'enrol_ukfilmnet'));
                $buttonarray[] = $mform->createElement('cancel', 'cancelbutton', get_string('button_exit', 'enrol_ukfilmnet'));
                $mform->addGroup($buttonarray, 'login_buttons', '', '',  false);
            $mform->addElement('html', '</div>');
        $mform->addElement('html', '</div>');

        if($SESSION->is_logged_in == 0) {
            $mform->addElement('html', '<div class="ukfn_form_box ukfn_stealth">');
        } else {
            $mform->addElement('html', '<div class="ukfn_form_box ukfn_not_stealth">');
        }
            $mform->addElement('html', '<div class ="ukfn_form_text">'.get_string('assurance_form_instructions', 'enrol_ukfilmnet').'</div>');

            $mform->addElement('html', '<div class="ukfn_form_doc">');
                $mform->addElement('html', '<div class="ukfn_form_content">');

                    $mform->addElement('html', '<div class="ukfn_form"><h4>Reference in Respect of '.$SESSION->firstname.' '.$SESSION->familyname.'</h4></div>');

                    $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                        $applciant_is_employed = [];
                        $applciant_is_employed[] =& $mform->createElement('checkbox', 'applicant_is_employed_yes', '', 'YES ');
                        $applciant_is_employed[] =& $mform->createElement('checkbox', 'applicant_is_employed_no', '', ' NO');
                        $mform->addGroup($applciant_is_employed, 'applicant_is_employed', get_string('applicant_is_employed', 'enrol_ukfilmnet'), '', false);
                    $mform->addElement('html', '</div>');

                    $mform->addElement('html', '<div class="ukfn_form_even">');        
                        $mform->addElement('date_selector', 'employment_start_date', get_string('employment_start_date', 'enrol_ukfilmnet'));
                        $mform->setType('employment_start_date', PARAM_NOTAGS);
                        $mform->addRule('employment_start_date', get_string('error_missing_start_date', 'enrol_ukfilmnet'), 'required', null, 'server');
                    $mform->addElement('html', '</div>');

                    $mform->addElement('text', 'job_title', get_string('job_title', 'enrol_ukfilmnet'), ['class'=>'ukfn_form_even']);
                    $mform->setType('job_title', PARAM_NOTAGS);
                    $mform->addRule('job_title', get_string('', 'enrol_ukfilmnet'), 'required', null, 'server');

                    $mform->addElement('text', 'main_duties', get_string('main_duties', 'enrol_ukfilmnet'), ['class'=>'ukfn_form_even']);
                    $mform->setType('main_duties', PARAM_NOTAGS);
                    $mform->addRule('main_duties', get_string('', 'enrol_ukfilmnet'), 'required', null, 'server');
                    
                    $mform->addElement('text', 'how_long_employee_known', get_string('how_long_employee_known', 'enrol_ukfilmnet'), ['class'=>'ukfn_form_even']);
                    $mform->setType('how_long_employee_known', PARAM_NOTAGS);
                    $mform->addRule('how_long_employee_known', get_string('', 'enrol_ukfilmnet'), 'required', null, 'server');
                    
                    $mform->addElement('text', 'capacity_employee_known', get_string('capacity_employee_known', 'enrol_ukfilmnet'), ['class'=>'ukfn_form_even']);
                    $mform->setType('capacity_employee_known', PARAM_NOTAGS);
                    $mform->addRule('capacity_employee_known', get_string('', 'enrol_ukfilmnet'), 'required', null, 'server');

                    $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                        $mform->addElement('date_selector', 'dbs_cert_date', get_string('dbs_cert_date', 'enrol_ukfilmnet'));
                        $mform->setType('dbs_cert_date', PARAM_NOTAGS);
                        $mform->addRule('dbs_cert_date', get_string('', 'enrol_ukfilmnet'), 'required', null, 'server');
                        
                        $mform->addElement('text', 'dbsnumber', get_string('dbsnumber', 'enrol_ukfilmnet'), ['class'=>'ukfn-assurance-content']);
                        $mform->setType('dbsnumber', PARAM_NOTAGS);
                        $mform->addRule('dbsnumber', get_string('', 'enrol_ukfilmnet'), 'required', null, 'server');
                    $mform->addElement('html', '</div>');

                    $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                        $applciant_suitability = [];
                        $applciant_suitability[] =& $mform->createElement('checkbox', 'applicant_suitability_yes', '', 'YES ');
                        $applciant_suitability[] =& $mform->createElement('checkbox', 'applicant_suitability_no', '', ' NO');
                        $mform->addGroup($applciant_suitability, 'applicant_suitability', get_string('applicant_suitability', 'enrol_ukfilmnet'), '', false);
                    $mform->addElement('html', '</div>');

                    $mform->addElement('html', '<div class="ukfn_form_even">');
                        $mform->addElement('html', '<div class="ukfn_form_big_left">');   
                            $qts_qualified = [];
                            $qts_qualified[] =& $mform->createElement('checkbox', 'qts_qualified_yes', '', 'YES ');
                            $qts_qualified[] =& $mform->createElement('checkbox', 'qts_qualified_no', '', ' NO');
                            $mform->addGroup($qts_qualified, 'qts_qualified', get_string('qts_qualified', 'enrol_ukfilmnet'), '', false);
                        $mform->addElement('html', '</div>');
                    
                        $mform->addElement('text', 'qtsnumber', get_string('qtsnumber', 'enrol_ukfilmnet'), ['class'=>'ukfn-qts-content']);
                        $mform->setType('qtsnumber', PARAM_NOTAGS);
                        $mform->addRule('qtsnumber', get_string('error_missing_qtsnumber', 'enrol_ukfilmnet'), 'required', null, 'server');
                    $mform->addElement('html', '</div>');

                    $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                        $behavior_allegations = [];
                        $behavior_allegations[] =& $mform->createElement('checkbox', 'behavior_allegations_yes', '', 'YES ');
                        $behavior_allegations[] =& $mform->createElement('checkbox', 'behavior_allegaions_no', '', ' NO');
                        $mform->addGroup($behavior_allegations, 'behavior_allegations', get_string('behavior_allegations', 'enrol_ukfilmnet'), '', false);
                    $mform->addElement('html', '</div>');

                    $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                        $disciplinary_actions = [];
                        $disciplinary_actions[] =& $mform->createElement('checkbox', 'disciplinary_actions_yes', '', 'YES ');
                        $disciplinary_actions[] =& $mform->createElement('checkbox', 'disciplinary_actions_no', '', ' NO');
                        $mform->addGroup($disciplinary_actions, 'disciplinary_actions', get_string('disciplinary_actions', 'enrol_ukfilmnet'), '', false);
                    $mform->addElement('html', '</div>');

                    $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                        $tra_check = [];
                        $tra_check[] =& $mform->createElement('checkbox', 'tra_check_n/a', '', 'N/A ');
                        $tra_check[] =& $mform->createElement('checkbox', 'tra_check_yes', '', 'YES ');
                        $tra_check[] =& $mform->createElement('checkbox', 'tra_check_no', '', ' NO');
                        $mform->addGroup($tra_check, 'tra_check', get_string('tra_check', 'enrol_ukfilmnet'), '', true);
                    $mform->addElement('html', '</div>');

                    $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                        $subject_to_ocr_check = [];
                        $subject_to_ocr_check[] =& $mform->createElement('checkbox', 'subject_to_ocr_check_n/a', '', 'N/A ');
                        $subject_to_ocr_check[] =& $mform->createElement('checkbox', 'subject_to_ocr_check_yes', '', 'YES ');
                        $subject_to_ocr_check[] =& $mform->createElement('checkbox', 'subject_to_ocr_check_no', '', ' NO');
                        $mform->addGroup($subject_to_ocr_check, 'subject_to_ocr_check', get_string('subject_to_ocr_check', 'enrol_ukfilmnet'), '', true);
                    $mform->addElement('html', '</div>');

                    $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                        $brit_school_abroad_mod_or_dubai_school = [];
                        $brit_school_abroad_mod_or_dubai_school[] =& $mform->createElement('checkbox', 'brit_school_abroad_mod_or_dubai_school_yes', '', 'YES ');
                        $brit_school_abroad_mod_or_dubai_school[] =& $mform->createElement('checkbox', 'brit_school_abroad_mod_or_dubai_school_no', '', ' NO');
                        $mform->addGroup($brit_school_abroad_mod_or_dubai_school, 'brit_school_abroad_mod_or_dubai_school', get_string('brit_school_abroad_mod_or_dubai_school', 'enrol_ukfilmnet'), '', false);
                    $mform->addElement('html', '</div>');

                    $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                        $school_subject_to_inspection = [];
                        $school_subject_to_inspection[] =& $mform->createElement('checkbox', 'school_subject_to_inspection_yes', '', 'YES ');
                        $school_subject_to_inspection[] =& $mform->createElement('checkbox', 'school_subject_to_inspection_no', '', ' NO');
                        $mform->addGroup($school_subject_to_inspection, 'school_subject_to_inspection', get_string('school_subject_to_inspection', 'enrol_ukfilmnet'), '', false);
                    $mform->addElement('html', '</div>');

                    $mform->addElement('html', '<div class="ukfn_form"><h4>About You (the referee)</h4></div>');
                    
                    $mform->addElement('html', '<div class="ukfn_form_even referee">');
                        $mform->addElement('text', 'referee_name', get_string('referee_name', 'enrol_ukfilmnet'), ['class'=>'ukfn-qts-content']);
                        $mform->setType('referee_name', PARAM_NOTAGS);
                        $mform->addRule('referee_name', get_string('', 'enrol_ukfilmnet'), 'required', null, 'server');

                        $mform->addElement('text', 'referee_position', get_string('referee_position', 'enrol_ukfilmnet'), ['class'=>'ukfn-qts-content']);
                        $mform->setType('referee_position', PARAM_NOTAGS);
                        $mform->addRule('referee_position', get_string('', 'enrol_ukfilmnet'), 'required', null, 'server');
                        $mform->addElement('text', 'referee_email', get_string('referee_email', 'enrol_ukfilmnet'), ['class'=>'ukfn-qts-content']);
                        $mform->setType('referee_email', PARAM_NOTAGS);
                        $mform->addRule('referee_email', get_string('', 'enrol_ukfilmnet'), 'required', null, 'server');
                    $mform->addElement('html', '</div>');

                    $mform->addElement('html', '<div class="ukfn_form signature"><strong>Signature of Referee:</strong><br><br><br></div>');

                    $mform->addElement('html', '<div class="ukfn_form signature_line"><strong>___________________________</strong><br></div>');

                    $mform->addElement('html', '<div class="ukfn_form"><h4>About Your Organization</h4></div>');

                    $mform->addElement('html', '<div class="ukfn_form_even school_info">');
                        $mform->addElement('textarea', 'school_registered_address', get_string('school_registered_address', 'enrol_ukfilmnet', 'wrap="virtual" rows="30" cols="50"'));
                        $mform->setType('school_registered_address', PARAM_NOTAGS);
                        $mform->addRule('school_registered_address', get_string('', 'enrol_ukfilmnet'), 'required', null, 'server');

                        $mform->addElement('text', 'school_web_address', get_string('school_web_address', 'enrol_ukfilmnet'));
                        $mform->setType('school_web_address', PARAM_NOTAGS);
                    $mform->addElement('html', '</div>');

                    $mform->addElement('html', '<div class="ukfn_form_even school_info">');
                        $mform->addElement('html', '
                            <div class="ukfn_form_orgname">'.get_string('school_name', 'enrol_ukfilmnet').'
                                <div class="ukfn_schoolname">'.$SESSION->schoolname.'</div></div>');
                        $mform->addElement('html', '
                            <div class="ukfn_form_ukprnnum">'.get_string('ukprn_number', 'enrol_ukfilmnet').'  <span class="ukfn_ukprn">'.$SESSION->ukprn.'</span></div>');
                    $mform->addElement('html', '</div>');
                $mform->addElement('html', '</div>');

            $mform->addElement('html', '</div>');
            

            $mform->addElement('html', '<div class="ukfn_form_assurance_filepicker">');
                $mform->addElement('filepicker', 'assurance_form', get_string('assurance_form', 'enrol_ukfilmnet'), null, array('maxbytes' => $maxbytes, 'accepted_types' => array('.pdf')));
                $mform->setType('MAX_FILE_SIZE', PARAM_INT);
                $mform->addRule('assurance_form', get_string('error_missing_assurance_form', 'enrol_ukfilmnet'), 'required', null, 'server');
            $mform->addElement('html', '</div>');
            
            $this->add_action_buttons($cancel=true, $submitlabel=get_string('button_submit', 'enrol_ukfilmnet'), ['class'=>'ukfn-form-buttons']);
           
        $mform->addElement('html', '</div>');           
    }

    //Custom validation should be added here
    function validation($data, $files) {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/user/profile/lib.php');

        $errors = parent::validation($data, $files);
        $email = $data['email'];
        if(false !== $DB->get_record('user', array('username' => $email, 'auth' => 'manual'))) {
            $user = $DB->get_record('user', array('username' => $email, 'auth' => 'manual'));
            profile_load_data($user);
            
            if($user->profile_field_assurancesubmitted == 1) {
                $errors['email'] = get_string('error_assurance_already_submitted', 'enrol_ukfilmnet');
                return $errors;
            }
            if($data['assurance_code'] !== $user->profile_field_assurancecode && strlen($data['assurance_code']) > 0) {
                $errors['assurance_code'] = get_string('error_assurance_code_mismatch', 'enrol_ukfilmnet');
                $errors['email'] = get_string('error_employee_email_assurance_code_mismatch', 'enrol_ukfilmnet');
            }
        } else {
            if(strlen($email) < 1) {
                $errors['email'] = get_string('error_missing_employee_work_email', 'enrol_ukfilmnet');
            } else {
                $errors['email'] = get_string('error_employee_work_email_mismatch', 'enrol_ukfilmnet');
            }
        }
        if(strlen($data['assurance_code']) < 1) {
            $errors['assurance_code'] = get_string('error_missing_assurance_code','enrol_ukfilmnet');
        }
        if($data['applicant_suitability_yes'] == $data['applicant_suitability_no']) {
            $errors['applicant_suitability'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }
        if($data['qts_qualified_yes'] == $data['qts_qualified_no']) {
            $errors['qts_qualified'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }
        if($data['behavior_allegations_yes'] == $data['behavior_allegations_no']) {
            $errors['behavior_allegations'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }
        if($data['disciplinary_actions_yes'] == $data['disciplinary_actions_no']) {
            $errors['disciplinary_actions'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }
        if(($data['tra_check_yes'] == $data['tra_check_no'] and $data['tra_check_no'] == $data['tra_check_n/a']) or ($data['tra_check_yes'] == $data['tra_check_no']) or ($data['tra_check_yes'] == $data['tra_check_n/a']) or ($data['tra_check_no'] == $data['tra_check_n/a'])) {
            $errors['tra_check'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }
        if(($data['subject_to_ocr_check_yes'] == $data['subject_to_ocr_check_no'] and $data['subject_to_ocr_check_no'] == $data['subject_to_ocr_check_n/a']) or ($data['subject_to_ocr_check_yes'] == $data['subject_to_ocr_check_no']) or ($data['subject_to_ocr_check_yes'] == $data['subject_to_ocr_check_n/a']) or ($data['subject_to_ocr_check_no'] == $data['subject_to_ocr_check_n/a'])) {
            $errors['subject_to_ocr_check'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }
        if($data['brit_school_abroad_mod_or_dubai_school_yes'] == $data['brit_school_abroad_mod_or_dubai_school_no']) {
            $errors['brit_school_abroad_mod_or_dubai_school'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }
        if($data['school_subject_to_inspection_yes'] == $data['school_subject_to_inspection_no']) {
            $errors['school_subject_to_inspection'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }
        if($data['applicant_is_employed_yes'] == $data['applicant_is_employed_no']) {
            $errors['applicant_is_employed'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }

        return $errors;
    }
}