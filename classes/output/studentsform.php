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
 * @copyright  2020, Doug Loomer
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** 
 *  
 */

namespace enrol_ukfilmnet\output;

defined('MOODLE_INTERNAL' || die());

require_once("$CFG->libdir/formslib.php");


class students_form extends \moodleform {
    //Add elements to form
    public function definition() {
        global $CFG,  $USER, $SESSION;
        require_once($CFG->dirroot.'/lib/formslib.php');
        
        $mform = $this->_form; 

        $mform->addElement('text', 'student_email', get_string('student_email', 'enrol_ukfilmnet'), ['class'=>'ukfn_student_email ukfn_enrol_col']);
        $mform->setType('student_email', PARAM_NOTAGS);
        $mform->addRule('student_email', get_string('error_missing_student_email', 'enrol_ukfilmnet'), 'required', null, 'server');
        $mform->addElement('text', 'student_firstname', get_string('student_firstname', 'enrol_ukfilmnet'), ['class'=>'ukfn_student_firstname ukfn_enrol_col']);
        $mform->setType('student_firstname', PARAM_TEXT);
        $mform->addRule('student_firstname', get_string('error_missing_student_firstname', 'enrol_ukfilmnet'), 'required', null, 'server');
        $mform->addElement('text', 'student_familyname', get_string('student_familyname', 'enrol_ukfilmnet'), ['class'=>'ukfn_student_familyname ukfn_enrol_col']);
        $mform->setType('student_familyname', PARAM_TEXT);
        $mform->addRule('student_familyname', get_string('error_missing_student_familyname', 'enrol_ukfilmnet'), 'required', null, 'server');
    }

    //Custom validation should be added here
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        return $errors;
    }
}