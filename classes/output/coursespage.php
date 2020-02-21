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
require_once('coursesform.php');
require_once(__DIR__.'/../../signuplib.php');

// This is a Template Class it collects/creates the data for a template
class coursespage implements \renderable, \templatable {

    private $page_number;
    private $applicantprogress;

    public function __construct($page_number, $applicantprogress) {
        $this->page_number = $page_number;
        $this->applicantprogress = $applicantprogress;
    }

    public function export_for_template(\renderer_base $output) {
        $data = new stdClass();
        $data->coursesinput = $this->get_courses_content();
        return $data;
    }

    public function get_courses_content() {

        global $CFG, $SESSION, $USER;

        $coursesinput = '';
        $mform = new courses_form();

        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            $SESSION->cancel = 1;
            $this->handle_redirects();
        } else if ($fromform = $mform->get_data()) {
            //In this case you process validated data. $mform->get_data() returns data posted in form.
            $form_data = $mform->get_data();

            profile_load_data($USER);
            $USER->profile_field_courses_requested = $form_data->total_courses;
            $USER->profile_field_applicationprogress = 5;
            profile_save_data($USER);
            $this->applicantprogress = 5;
            $this->handle_redirects();
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

    public function handle_redirects() {
        global $CFG, $SESSION;

        if(isset($SESSION->cancel) and $SESSION->cancel == 1) {
            $SESSION->cancel = 0;
            redirect($CFG->wwwroot);
        } elseif($this->page_number != $this->applicantprogress) {
            force_signup_flow($this->applicantprogress);
        }
        return true;
    }
}
