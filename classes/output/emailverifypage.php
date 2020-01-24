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
require_once('emailverifyform.php');
require_once('signuplib.php');

// This is a Template Class it collects/creates the data for a template
class emailverifypage implements \renderable, \templatable {

    var $sometext = null;

    public function __construct($sometext = null) {
        $this->sometext = $sometext;
    }

    public function export_for_template(\renderer_base $output) {
        $data = new stdClass();
        //$data->plugin_heading = get_string('plugin_heading', 'enrol_ukfilmnet')
        //$data->sometext = $this->sometext;
        $data->emailverifyinput = $this->get_emailverify_content();
        return $data;
    }

    public function get_emailverify_content() {

        global $CFG;

        $emailverifyinput = '';
        $mform = new emailverify_form();

        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            redirect('https://ukfilmnet.org');
        } else if ($fromform = $mform->get_data()) {
            //In this case you process validated data. $mform->get_data() returns data posted in form.
            $form_data = $mform->get_data();
            //var_dump($form_data);
            

            //$newuser = (object) array('email'=>$form_data->email,'username'=>make_username($form_data->email),'firstname'=>$form_data->firstname,'lastname'=>$form_data->familyname, 'currentrole'=>$form_data->role);
            //create_applicant_user($newuser,'ukfilmnet');
            
            applicant_login($form_data->username, $form_data->password);
            redirect($CFG->wwwroot.'/enrol/ukfilmnet/applicant.php');
        } else {
            // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
            // or on the first display of the form.
            $toform = $mform->get_data();
            
            //Set default data (if any)
            $mform->set_data($toform);
            //displays the form
            $emailverifyinput = $mform->render();
        }
        return $emailverifyinput;
    }

}
