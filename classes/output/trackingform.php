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
 * A class skeleton which could be implemented to be used to encapsulate form elements for the tracking.php page.
 *
 * @package    enrol_ukfilmnet
 * @copyright  2020, Doug Loomer
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_ukfilmnet\output;

defined('MOODLE_INTERNAL' || die());

require_once($CFG->libdir.'/formslib.php');

class tracking_form extends \moodleform {
    
    /**
     * Form elements would go here.
     */    
    public function definition() {
        global $CFG;

        $mform = $this->_form; 
        
    }
    /**
     * Rules to validate the submitted form data would go here.
     *
     * @param array $data array Array of ("fieldname"=>value) of submitted data
     * @param array $files array Array of uploaded files "element_name"=>tmp_file_path
     * @return array Array of "element_name"=>"error_description" if there are errors, or an empty array if everything is OK (true allowed for backwards compatibility too).
     */        
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        return $errors;
    }
}