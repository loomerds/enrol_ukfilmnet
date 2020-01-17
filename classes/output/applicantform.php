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
        global $CFG;

        $mform = $this->_form; // Don't forget the underscore! 
        $current_roles = ['Select your current role',
                          get_string('applicant-role-ukteacher', 'enrol_ukfilmnet'), 
                          get_string('applicant-role-teacherbsa', 'enrol_ukfilmnet'),
                          get_string('applicant-role-uksupplyteacher', 'enrol_ukfilmnet'),
                          get_string('applicant-role-instructor18plus', 'enrol_ukfilmnet'),
                          get_string('applicant-role-instructor17minus', 'enrol_ukfilmnet'),
                          get_string('applicant-role-student17minus', 'enrol_ukfilmnet'),
                          get_string('applicant-role-student18plus', 'enrol_ukfilmnet'),
                          get_string('applicant-role-industryprofessional', 'enrol_ukfilmnet'),
                          get_string('applicant-role-educationconsultant', 'enrol_ukfilmnet'),
                          get_string('applicant-role-parentguardian', 'enrol_ukfilmnet')];
        $mform->addElement('select', 'type', get_string('applicant-current-role', 'enrol_ukfilmnet'), $current_roles, ['class'=>'ukfn-applicant-current-roles']);
        $mform->addElement('text', 'email', get_string('applicant-email', 'enrol_ukfilmnet'), ['class'=>'ukfn-applicant-email']); // Add elements to your form
        $mform->setType('email', PARAM_NOTAGS);
        $mform->addElement('text', 'firstname', get_string('applicant-firstname', 'enrol_ukfilmnet'), ['class'=>'ukfn-applicant-firstname']);
        $mform->setType('firstname', PARAM_TEXT);
        $mform->addElement('text', 'familyname', get_string('applicant-familyname', 'enrol_ukfilmnet'), ['class'=>'ukfn-applicant-firstname']);
        $mform->setType('familyname', PARAM_TEXT);
        
            
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}