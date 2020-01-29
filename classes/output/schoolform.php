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

//use core_string_manager_standard;

class school_form extends \moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;
        //require_once('../../lib/classes/string_manager.php');

        $mform = $this->_form; 
        //$school_country = ['0'=>get_string('country_instruction', 'enrol_ukfilmnet')]
        //$countries = core_string_manager::get_list_of_countries();
        //$countries = new core_string_manager();
        //$school_country = $countries->get_list_of_countries();
        //var_dump($countries);

        $school_name = ['0'=>get_string('school_name_instruction', 'enrol_ukfilmnet'),
                        '01'=>get_string('school1', 'enrol_ukfilmnet'),
                        '02'=>get_string('school2', 'enrol_ukfilmnet')];
        $mform->addElement('select', 'school_name', get_string('school_name_label', 'enrol_ukfilmnet'), $school_name, ['class'=>'ukfn-school-name']);
        $mform->addRule('school_name', null, 'required', null, 'server');

        $school_country = get_string_manager()->get_list_of_countries();
        $selectcountry = $mform->addElement('select', 'school_country', get_string('school_country_label', 'enrol_ukfilmnet'), $school_country, ['class'=>'ukfn-school-country']);
        $selectcountry->setSelected('GB');
        $mform->addRule('school_country', null, 'required', null, 'server');
        $mform->addElement('checkbox', 'school_consent_to_contact', get_string('consent_to_contact', 'enrol_ukfilmnet'));
        $mform->setDefault('school_consent_to_contact', 0);
        $mform->addRule('school_consent_to_contact', null, 'required', null, 'server');
        
        $mform->addElement('static', 'contact_info_label', get_string('contact_info_label', 'enrol_ukfilmnet', null));
        $mform->addElement('text', 'contact_firstname', get_string('contact_firstname', 'enrol_ukfilmnet'), ['class'=>'ukfn-indent-20']);
        $mform->setType('contact_firstname', PARAM_TEXT);
        $mform->addRule('contact_firstname', get_string('error_missing_contact_firstname', 'enrol_ukfilmnet'), 'required', null, 'server');
        $mform->addElement('text', 'contact_familyname', get_string('contact_familyname', 'enrol_ukfilmnet'), ['class'=>'ukfn-indent-20']);
        $mform->setType('contact_familyname', PARAM_TEXT);
        $mform->addRule('contact_familyname', get_string('error_missing_contact_familyname', 'enrol_ukfilmnet'), 'required', null, 'server');
        $mform->addElement('text', 'contact_email', get_string('contact_email', 'enrol_ukfilmnet'), ['class'=>'ukfn-indent-20']);
        $mform->setType('contact_email', PARAM_NOTAGS);
        $mform->addRule('contact_email', get_string('error_missing_contact_email', 'enrol_ukfilmnet'), 'required', null, 'server');
        $mform->addElement('text', 'contact_phone', get_string('contact_phone', 'enrol_ukfilmnet'), ['class'=>'ukfn-indent-20 ukfn-last-input']);
        $mform->setType('contact_phone', PARAM_NOTAGS);
        $mform->addRule('contact_phone', get_string('error_missing_contact_phone', 'enrol_ukfilmnet'), 'required', null, 'server');
        $mform->addElement('hidden', 'role', null);
        $mform->setType('role', PARAM_ACTION);
        $this->add_action_buttons($cancel=true, $submitlabel=get_string('button_submit', 'enrol_ukfilmnet'), ['class'=>'ukfn-form-buttons']);            
    }
    //Custom validation should be added here
    function validation($data, $files) {
        //var_dump($stdClass);
        $errors = parent::validation($data, $files);
        
        if($data['school_name'] === '0') {
            $errors['school_name'] = get_string('error_missing_school_name', 'enrol_ukfilmnet');
        }
        /*if($data['school_country'] === '0') {
            $errors['school_country'] = get_string('error_missing_country', 'enrol_ukfilmnet');
        }*/
        if($data['contact_email'] && strpos( $data['contact_email'], '@') === false) {
            $errors['contact_email'] = get_string('error_invalid_email', 'enrol_ukfilmnet');
        }
        if(!array_key_exists('school_consent_to_contact', $data)) {
            $errors['school_consent_to_contact'] = get_string('error_missing_school_consent_to_contact', 'enrol_ukfilmnet');
        }
        
        return $errors;
    }
}