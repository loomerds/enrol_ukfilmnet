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
 * A class to encapsulate form elements for the courses.php page.
 *
 * @package    enrol_ukfilmnet
 * @copyright  2020, Doug Loomer
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_ukfilmnet\output;

defined('MOODLE_INTERNAL' || die());

require_once("$CFG->libdir/formslib.php");


class courses_form extends \moodleform {
    
    /**
     * Form elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form; 
        $mform->addElement('text', 'total_courses', get_string('total_courses_question', 'enrol_ukfilmnet'), ['class'=>'ukfn_courses_questions']);
        $mform->setType('total_courses', PARAM_INT);
        $this->add_action_buttons($cancel=true, $submitlabel=get_string('button_submit', 'enrol_ukfilmnet'), ['class'=>'ukfn-form-buttons']);           
    }
    
    /**
     * Validate the submitted form data.
     *
     * @param array $data array Array of ("fieldname"=>value) of submitted data
     * @param array $files array Array of uploaded files "element_name"=>tmp_file_path
     * @return array Array of "element_name"=>"error_description" if there are errors, or an empty array if everything is OK (true allowed for backwards compatibility too).
     */        
    function validation($data, $files) {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/user/profile/lib.php');

        $errors = parent::validation($data, $files);
        
        if(empty($data['total_courses']) OR $data['total_courses'] > intval(get_string('max_courses_allowed', 'enrol_ukfilmnet'))) {
            
            if(empty($data['total_courses'])) {
                $errors['total_courses'] = get_string('error_missing_total_courses', 'enrol_ukfilmnet');
            } else {
                $errors['total_courses'] = get_string('error_total_courses_excessive', 'enrol_ukfilmnet');
            }
        }
        
        return $errors;
    }
}