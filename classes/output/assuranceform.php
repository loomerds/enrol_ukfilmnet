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

        $maxbytes = 9185760;
        $mform = $this->_form;

        if(!isset($SESSION->is_logged_in)) {
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

        if(!isset($SESSION->is_logged_in)) {
            $mform->addElement('html', '<div class="ukfn_form_box ukfn_stealth">');
        } else {
            $mform->addElement('html', '<div class="ukfn_form_box ukfn_not_stealth">');
        }
                $mform->addElement('html', '<div class ="ukfn_form_text">'.get_string('assurance_form_instructions', 'enrol_ukfilmnet').'</div>');

                $mform->addElement('html', '<div class="ukfn_form_doc">');
                    $mform->addElement('html', '<div class="ukfn_form_content">');

                        if(isset($SESSION->firstname) and isset($SESSION->familyname)) {
                            $mform->addElement('html', '<div class="ukfn_form"><h4>Reference in Respect of '.$SESSION->firstname.' '.$SESSION->familyname.'</h4></div>');
                        }

                        $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                            $applicant_is_employed = [];
                            $applicant_is_employed[] =& $mform->createElement('checkbox', 'applicant_is_employed_yes', '', 'YES ');
                            $applicant_is_employed[] =& $mform->createElement('checkbox', 'applicant_is_employed_no', '', ' NO');
                            $mform->addGroup($applicant_is_employed, 'applicant_is_employed', get_string('applicant_is_employed', 'enrol_ukfilmnet'), '', false);
                        $mform->addElement('html', '</div>');

                        $mform->addElement('html', '<div class="ukfn_form_even">');        
                            $mform->addElement('date_selector', 'employment_start_date', get_string('employment_start_date', 'enrol_ukfilmnet'));
                            $mform->setType('employment_start_date', PARAM_NOTAGS);
                            $mform->addRule('employment_start_date', get_string('error_missing_start_date', 'enrol_ukfilmnet'), 'required', null, 'server');
                            $mform->disabledIf('employment_start_date', 'applicant_is_employed_yes');

                        $mform->addElement('html', '</div>');

                        $mform->addElement('text', 'job_title', get_string('job_title', 'enrol_ukfilmnet'), ['class'=>'ukfn_form_even']);
                        $mform->setType('job_title', PARAM_NOTAGS);
                        $mform->addRule('job_title', get_string('error_missing_job_title', 'enrol_ukfilmnet'), 'required', null, 'server');
                        $mform->disabledIf('job_title', 'applicant_is_employed_yes');

                        $mform->addElement('text', 'main_duties', get_string('main_duties', 'enrol_ukfilmnet'), ['class'=>'ukfn_form_even']);
                        $mform->setType('main_duties', PARAM_NOTAGS);
                        $mform->addRule('main_duties', get_string('error_missing_main_duties', 'enrol_ukfilmnet'), 'required', null, 'server');
                        $mform->disabledIf('main_duties', 'applicant_is_employed_yes');
                        
                        $mform->addElement('text', 'how_long_employee_known', get_string('how_long_employee_known', 'enrol_ukfilmnet'), ['class'=>'ukfn_form_even']);
                        $mform->setType('how_long_employee_known', PARAM_NOTAGS);
                        $mform->addRule('how_long_employee_known', get_string('error_missing_time_known', 'enrol_ukfilmnet'), 'required', null, 'server');
                        $mform->disabledIf('how_long_employee_known', 'applicant_is_employed_yes');
                        
                        $mform->addElement('text', 'capacity_employee_known', get_string('capacity_employee_known', 'enrol_ukfilmnet'), ['class'=>'ukfn_form_even']);
                        $mform->setType('capacity_employee_known', PARAM_NOTAGS);
                        $mform->addRule('capacity_employee_known', get_string('error_missing_capacity_known', 'enrol_ukfilmnet'), 'required', null, 'server');
                        $mform->disabledIf('capacity_employee_known', 'applicant_is_employed_yes');

                        $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                            $mform->addElement('date_selector', 'dbs_cert_date', get_string('dbs_cert_date', 'enrol_ukfilmnet'));
                            $mform->setType('dbs_cert_date', PARAM_NOTAGS);
                            $mform->addRule('dbs_cert_date', get_string('error_missing_dbs_date', 'enrol_ukfilmnet'), 'required', null, 'server');
                            $mform->disabledIf('dbs_cert_date', 'applicant_is_employed_yes');
                            
                            $mform->addElement('text', 'dbsnumber', get_string('dbsnumber', 'enrol_ukfilmnet'), ['class'=>'ukfn-assurance-content']);
                            $mform->setType('dbsnumber', PARAM_NOTAGS);
                            $mform->addRule('dbsnumber', get_string('error_missing_dbs_number', 'enrol_ukfilmnet'), 'required', null, 'server');
                            $mform->addRule('dbsnumber', get_string('error_length_of_12', 'enrol_ukfilmnet'), 'minlength',12, 'client');
                            $mform->addRule('dbsnumber', get_string('error_length_of_12', 'enrol_ukfilmnet'), 'maxlength', 12, 'client');
                            $mform->disabledIf('dbsnumber', 'applicant_is_employed_yes');
                            $mform->addHelpButton('dbsnumber', 'dbsnumber', 'enrol_ukfilmnet');
                        $mform->addElement('html', '</div>');

                        $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                            $applicant_suitability = [];
                            $applicant_suitability[] =& $mform->createElement('checkbox', 'applicant_suitability_yes', '', 'YES ');
                            $applicant_suitability[] =& $mform->createElement('checkbox', 'applicant_suitability_no', '', ' NO');
                            $mform->addGroup($applicant_suitability, 'applicant_suitability', get_string('applicant_suitability', 'enrol_ukfilmnet'), '', false);
                            $mform->disabledIf('applicant_suitability', 'applicant_is_employed_yes');
                        $mform->addElement('html', '</div>');

                        $mform->addElement('html', '<div class="ukfn_form_even">');
                            $mform->addElement('html', '<div class="ukfn_form_big_left">');   
                                $qts_qualified = [];
                                $qts_qualified[] =& $mform->createElement('checkbox', 'qts_qualified_yes', '', 'YES ');
                                $qts_qualified[] =& $mform->createElement('checkbox', 'qts_qualified_no', '', ' NO');
                                $mform->addGroup($qts_qualified, 'qts_qualified', get_string('qts_qualified', 'enrol_ukfilmnet'), '', false);
                                $mform->disabledIf('qts_qualified', 'applicant_is_employed_yes');
                            $mform->addElement('html', '</div>');
                        
                            $mform->addElement('text', 'qtsnumber', get_string('qtsnumber', 'enrol_ukfilmnet'), ['class'=>'ukfn-qts-content']);
                            $mform->setType('qtsnumber', PARAM_NOTAGS);
                            $mform->addRule('qtsnumber', get_string('error_missing_qtsnumber', 'enrol_ukfilmnet'), 'required', null, 'server');
                            $mform->disabledIf('qtsnumber', 'applicant_is_employed_yes');
                            $mform->addHelpButton('qtsnumber', 'qtsnumber', 'enrol_ukfilmnet');
                        $mform->addElement('html', '</div>');

                        $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                            $behavior_allegations = [];
                            $behavior_allegations[] =& $mform->createElement('checkbox', 'behavior_allegations_yes', '', 'YES ');
                            $behavior_allegations[] =& $mform->createElement('checkbox', 'behavior_allegations_no', '', ' NO');
                            $mform->addGroup($behavior_allegations, 'behavior_allegations', get_string('behavior_allegations', 'enrol_ukfilmnet'), '', false);
                            $mform->disabledIf('behavior_allegations', 'applicant_is_employed_yes');
                        $mform->addElement('html', '</div>');

                        $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                            $disciplinary_actions = [];
                            $disciplinary_actions[] =& $mform->createElement('checkbox', 'disciplinary_actions_yes', '', 'YES ');
                            $disciplinary_actions[] =& $mform->createElement('checkbox', 'disciplinary_actions_no', '', ' NO');
                            $mform->addGroup($disciplinary_actions, 'disciplinary_actions', get_string('disciplinary_actions', 'enrol_ukfilmnet'), '', false);
                            $mform->disabledIf('disciplinary_actions', 'applicant_is_employed_yes');
                        $mform->addElement('html', '</div>');

                        $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                            $tra_check = [];
                            $tra_check[] =& $mform->createElement('checkbox', 'tra_check_n/a', '', 'N/A ');
                            $tra_check[] =& $mform->createElement('checkbox', 'tra_check_yes', '', 'YES ');
                            $tra_check[] =& $mform->createElement('checkbox', 'tra_check_no', '', ' NO');
                            $mform->addGroup($tra_check, 'tra_check', get_string('tra_check', 'enrol_ukfilmnet'), '', true);
                            $mform->disabledIf('tra_check', 'applicant_is_employed_yes');
                        $mform->addElement('html', '</div>');

                        $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                            $subject_to_ocr_check = [];
                            $subject_to_ocr_check[] =& $mform->createElement('checkbox', 'subject_to_ocr_check_n/a', '', 'N/A ');
                            $subject_to_ocr_check[] =& $mform->createElement('checkbox', 'subject_to_ocr_check_yes', '', 'YES ');
                            $subject_to_ocr_check[] =& $mform->createElement('checkbox', 'subject_to_ocr_check_no', '', ' NO');
                            $mform->addGroup($subject_to_ocr_check, 'subject_to_ocr_check', get_string('subject_to_ocr_check', 'enrol_ukfilmnet'), '', true);
                            $mform->disabledIf('subject_to_ocr_check', 'applicant_is_employed_yes');
                        $mform->addElement('html', '</div>');

                        $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                            $brit_school_abroad_mod_or_dubai_school = [];
                            $brit_school_abroad_mod_or_dubai_school[] =& $mform->createElement('checkbox', 'brit_school_abroad_mod_or_dubai_school_yes', '', 'YES ');
                            $brit_school_abroad_mod_or_dubai_school[] =& $mform->createElement('checkbox', 'brit_school_abroad_mod_or_dubai_school_no', '', ' NO');
                            $mform->addGroup($brit_school_abroad_mod_or_dubai_school, 'brit_school_abroad_mod_or_dubai_school', get_string('brit_school_abroad_mod_or_dubai_school', 'enrol_ukfilmnet'), '', false);
                            $mform->disabledIf('brit_school_abroad_mod_or_dubai_school', 'applicant_is_employed_yes');
                        $mform->addElement('html', '</div>');

                        $mform->addElement('html', '<div class="ukfn_form_big_left">');        
                            $school_subject_to_inspection = [];
                            $school_subject_to_inspection[] =& $mform->createElement('checkbox', 'school_subject_to_inspection_yes', '', 'YES ');
                            $school_subject_to_inspection[] =& $mform->createElement('checkbox', 'school_subject_to_inspection_no', '', ' NO');
                            $mform->addGroup($school_subject_to_inspection, 'school_subject_to_inspection', get_string('school_subject_to_inspection', 'enrol_ukfilmnet'), '', false);
                            $mform->disabledIf('school_subject_to_inspection', 'applicant_is_employed_yes');
                        $mform->addElement('html', '</div>');

                        $mform->addElement('html', '<div class="ukfn_form"><h4>About You (The referee/Designated Safeguarding Lead)</h4></div>');
                        
                        $mform->addElement('html', '<div class="ukfn_form_even referee">');
                            $mform->addElement('text', 'referee_firstname', get_string('referee_firstname', 'enrol_ukfilmnet'), ['class'=>'ukfn-qts-content']);
                            $mform->setType('referee_firstname', PARAM_NOTAGS);
                            $mform->addRule('referee_firstname', get_string('error_missing_referee_firstname', 'enrol_ukfilmnet'), 'required', null, 'server');
                            $mform->disabledIf('referee_firstname', 'applicant_is_employed_yes');

                            $mform->addElement('text', 'referee_familyname', get_string('referee_familyname', 'enrol_ukfilmnet'), ['class'=>'ukfn-qts-content']);
                            $mform->setType('referee_familyname', PARAM_NOTAGS);
                            $mform->addRule('referee_familyname', get_string('error_missing_referee_familyname', 'enrol_ukfilmnet'), 'required', null, 'server');
                            $mform->disabledIf('referee_familyname', 'applicant_is_employed_yes');

                        $mform->addElement('html', '</div>');

                        $mform->addElement('html', '<div class="ukfn_form_even referee">');
                            $mform->addElement('text', 'referee_position', get_string('referee_position', 'enrol_ukfilmnet'), ['class'=>'ukfn-qts-content']);
                            $mform->setType('referee_position', PARAM_NOTAGS);
                            $mform->addRule('referee_position', get_string('error_missing_referee_position', 'enrol_ukfilmnet'), 'required', null, 'server');
                            $mform->disabledIf('referee_position', 'applicant_is_employed_yes');


                            $mform->addElement('text', 'referee_email', get_string('referee_email', 'enrol_ukfilmnet'), ['class'=>'ukfn-qts-content']);
                            $mform->setType('referee_email', PARAM_NOTAGS);
                            $mform->addRule('referee_email', get_string('error_missing_referee_email', 'enrol_ukfilmnet'), 'required', null, 'server');
                            $mform->disabledIf('referee_email', 'applicant_is_employed_yes');

                        $mform->addElement('html', '</div>');

                        $mform->addElement('html', '<div class="ukfn_form signature"><strong>Signature of Referee:</strong><br><br><br></div>');

                        $mform->addElement('html', '<div class="ukfn_form signature_line"><strong>________________________________     <span>Date: ____________________</span></strong><br></div>');

                        $mform->addElement('html', '<div class="ukfn_form_even school_info">');
                            $mform->addElement('textarea', 'school_registered_address', get_string('school_registered_address', 'enrol_ukfilmnet', 'wrap="virtual" rows="20" cols="50"'));
                            $mform->setType('school_registered_address', PARAM_NOTAGS);
                            $mform->addRule('school_registered_address', get_string('error_missing_organisation_address', 'enrol_ukfilmnet'), 'required', null, 'server');
                            $mform->disabledIf('school_registered_address', 'applicant_is_employed_yes');

                            $mform->addElement('text', 'school_web_address', get_string('school_web_address', 'enrol_ukfilmnet'));
                            $mform->setType('school_web_address', PARAM_NOTAGS);
                            $mform->disabledIf('school_web_address', 'applicant_is_employed_yes');
                        $mform->addElement('html', '</div>');

                        $mform->addElement('html', '<div class="ukfn_form_even school_info">');

                            $mform->addElement('text', 'schoolname', get_string('school_name', 'enrol_ukfilmnet'), ['class'=>'ukfn_schoolname']);
                            $mform->setType('schoolname', PARAM_NOTAGS);
                            $mform->addRule('schoolname', get_string('error_missing_school_name', 'enrol_ukfilmnet'), 'required', null, 'server');
                            $mform->disabledIf('schoolname', 'applicant_is_employed_yes');
                        
                            $mform->addElement('text', 'ukprn', get_string('ukprn_number', 'enrol_ukfilmnet'), ['class'=>'ukfn_ukprn']);
                                $mform->setType('ukprn', PARAM_NOTAGS);
                            $mform->setType('ukprn', PARAM_NOTAGS);
                            $mform->addRule('ukprn', get_string('error_missing_ukprn_number', 'enrol_ukfilmnet'), 'required', null, 'server');
                            $mform->disabledIf('ukprn', 'applicant_is_employed_yes');
                        $mform->addElement('html', '</div>');
                    $mform->addElement('html', '</div>');

                $mform->addElement('html', '</div>');

                $mform->addElement('html', '<div class="ukfn_notes_text"><p>'.get_string('note_1', 'enrol_ukfilmnet').'</p><p>'.get_string('note_2', 'enrol_ukfilmnet').'</p></div>');
                
                $mform->addElement('html', '<div class="ukfn_print_upload_text">'.get_string('assurance_print_upload_instructions', 'enrol_ukfilmnet').'</div>');

                $mform->addElement('html', '<div class="ukfn_form_assurance_filepicker">');
                    $mform->addElement('filepicker', 'assurance_form', get_string('assurance_form', 'enrol_ukfilmnet'), null, array('maxbytes' => $maxbytes, 'accepted_types' => array('.pdf', '.jpeg', '.jpg', '.png')));
                    $mform->setType('MAX_FILE_SIZE', PARAM_INT);
                    $mform->addRule('assurance_form', get_string('error_missing_assurance_form', 'enrol_ukfilmnet'), 'required', null, 'server');
                    $mform->disabledIf('assurance_form', 'applicant_is_employed_yes');
                $mform->addElement('html', '</div>');
                
                $this->add_action_buttons($cancel=true, $submitlabel=get_string('button_submit', 'enrol_ukfilmnet'), ['class'=>'ukfn-form-buttons']);
            
            $mform->addElement('html', '</div>');
    }

    //Custom validation should be added here
    function validation($data, $files) {
        global $DB, $CFG, $SESSION;
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
        if(!isset($SESSION->is_logged_in)) {
            $errors['is_logged_in'] = '';
        }
        if((isset($data['applicant_suitability_yes']) and isset($data['applicant_suitability_no'])) or (!isset($data['applicant_suitability_yes']) and !isset($data['applicant_suitability_no']))) {
            $errors['applicant_suitability'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }
        if((isset($data['qts_qualified_yes']) and isset($data['qts_qualified_no'])) or (!isset($data['qts_qualified_yes']) and !isset($data['qts_qualified_no']))) {
            $errors['qts_qualified'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }
        if((isset($data['behavior_allegations_yes']) and isset($data['behavior_allegations_no'])) or (!isset($data['behavior_allegations_yes']) and !isset($data['behavior_allegations_no']))) {
            $errors['behavior_allegations'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }
        if((isset($data['disciplinary_actions_yes']) and isset($data['disciplinary_actions_no'])) or (!isset($data['disciplinary_actions_yes']) and !isset($data['disciplinary_actions_no']))) {
            $errors['disciplinary_actions'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }
        if(isset($data['tra_check']) and count($data['tra_check']) != 1) {
            $errors['tra_check'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }
        if(isset($data['subject_to_ocr_check']) and count($data['subject_to_ocr_check']) != 1) {
                $errors['subject_to_ocr_check'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }
        if((isset($data['brit_school_abroad_mod_or_dubai_school_yes']) and isset($data['brit_school_abroad_mod_or_dubai_school_no'])) or (!isset($data['brit_school_abroad_mod_or_dubai_school_yes']) and !isset($data['brit_school_abroad_mod_or_dubai_school_no']))) {
            $errors['brit_school_abroad_mod_or_dubai_school'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }
        if((isset($data['school_subject_to_inspection_yes']) and isset($data['school_subject_to_inspection_no'])) or (!isset($data['school_subject_to_inspection_yes']) and !isset($data['school_subject_to_inspection_no']))) {
            $errors['school_subject_to_inspection'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }
        if((isset($data['applicant_is_employed_yes']) and isset($data['applicant_is_employed_no'])) or (!isset($data['applicant_is_employed_yes']) and !isset($data['applicant_is_employed_no']))) {
            $errors['applicant_is_employed'] = get_string('error_yes_or_no', 'enrol_ukfilmnet');
        }
        if($data['dbs_cert_date'] > time()) {
            $errors['dbs_cert_date'] = get_string('error_future_dbs_cert_date', 'enrol_ukfilmnet');
        }
        if($data['dbs_cert_date'] > $data['employment_start_date']) {
            $errors['employment_start_date'] = get_string('error_dbs_after_employment_start', 'enrol_ukfilmnet');
        }

        return $errors;
    }
}