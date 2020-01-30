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
require_once('assuranceform.php');
require_once('signuplib.php');

// This is a Template Class it collects/creates the data for a template
class assurancepage implements \renderable, \templatable {

    var $sometext = null;

    public function __construct($sometext = null) {
        $this->sometext = $sometext;
    }

    public function export_for_template(\renderer_base $output) {
        $data = new stdClass();
        $data->assuranceinput = $this->get_assurance_content();
        return $data;
    }

    public function get_assurance_content() {

        global $CFG, $DB, $USER;
        
        $assuranceinput = '';
        $mform = new assurance_form();

        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            redirect('https://ukfilmnet.org');
        } else if ($fromform = $mform->get_data()) {
            //In this case you process validated data. $mform->get_data() returns data posted in form.
            $form_data = $mform->get_data();
            
            //$roleassignments = $DB->get_records('role_assignments', ['userid' => $USER->id]);

            //$content = $mform->get_file_content('assurance_form');
            //$fullpath = $CFG->dirroot.'/enrol/ukfilmnet/assurancefiles';
            $fullpath = $CFG->dirroot;
            //var_dump($fullpath);
            $override = true;
            $name = $mform->get_new_filename('assurance_form');
            //$storedfile = $mform->save_stored_file('assurance_form', context_system::instance(), 'enrol_ukfilmnet', 'assurancefiles');
            $success = $mform->save_file('assurance_form', $fullpath.'/'.$name, $override);
            var_dump($success);

            $applicant_user = $DB->get_record('user', array('username' => $form_data->email, 'auth' => 'manual'));
            if($applicant_user !== null) {
                profile_load_data($applicant_user);
                $applicant_user->profile_field_qtsnumber = $form_data->qtsnumber;
                $applicant_user->profile_field_assurancesubmitted = 1;
                $applicant_user->profile_field_assurancesubmissiondate = time();
                profile_save_data($applicant_user);
            }

            

              
            //$verified_user = applicant_login($applicant_user->username, $applicant_user->password);
        } else {
            // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
            // or on the first display of the form.
            $toform = $mform->get_data();
            //$SESSION->email_info_complete = false;
            //Set default data (if any)
            $mform->set_data($toform);
            //displays the form
            $assuranceinput = $mform->render();
        }
        return $assuranceinput;
        
    }

}
