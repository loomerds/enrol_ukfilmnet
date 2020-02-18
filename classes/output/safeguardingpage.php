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
use moodle_url;

//require_once('schoolform.php');
//require_once('signuplib.php');
//require_once($CFG->libdir.'/datalib.php');

// This is a Template Class it collects/creates the data for a template
class safeguardingpage implements \renderable, \templatable {
    var $sometext = null;

    public function __construct($sometext = null) {
        $this->sometext = $sometext;
    }

    public function export_for_template(\renderer_base $output) {
        global $USER;
        //profile_load_data($USER);
        //var_dump($USER);

        $data = new stdClass();
        $data->safeguardinginput = $this->get_safeguarding_content();
        return $data;
    }

    public function get_safeguarding_content() {

        global $CFG;
        require_once($CFG->dirroot.'/enrol/ukfilmnet/classes/output/safeguardingform.php');
        
        $safeguardinginput = '';
        $mform = new safeguarding_form();

        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            redirect($CFG->wwwroot);
        } else if ($fromform = $mform->get_data()) {
            //In this case you process validated data. $mform->get_data() returns data posted in form.
            $form_data = $mform->get_data();
            $safeguardinginput = $form_data;
        } else {
            // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed or on the first display of the form.
            $toform = $mform->get_data();
            
            //Set default data (if any)
            $mform->set_data($toform);
            //displays the form
            $safeguardinginput = $mform->render();
        }
        return $safeguardinginput;
    }

}
