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

namespace enrol_ukfilmnet\output;

defined('MOODLE_INTERNAL' || die());

use stdClass;
require_once('coursesform.php');
require_once(__DIR__.'/../../signuplib.php');

// This is a Template Class it collects/creates the data for a template
class coursespage implements \renderable, \templatable {

    private $page_number;

    public function __construct($page_number) {
        $this->page_number = $page_number;
    }

    public function export_for_template(\renderer_base $output) {
        $data = new stdClass();
        $data->coursesinput = $this->get_courses_content();
        return $data;
    }

    public function get_courses_content() {

        global $CFG, $USER;

        $coursesinput = '';
        $mform = new courses_form();

        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            // retain this for possible future use
        } else if ($form_data = $mform->get_data()) {
            //Process validated data here.

            // Add/adjust field values in the user's profile.
            profile_load_data($USER);
            $USER->profile_field_courses_requested = $form_data->total_courses;
            $USER->profile_field_applicationprogress = convert_progressnum_to_progressstring(4);
            profile_save_data($USER);
        } else {
            // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed or on the first display of the form.
            $toform = $mform->get_data();
            //Set default data (if any)
            $mform->set_data($toform);
            //displays the form
            $coursesinput = $mform->render();
        }
        return $coursesinput;
    }
}
