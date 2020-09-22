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
 * A Template Class to collect/create the data for the emailverify.php page.
 *
 * @package    enrol_ukfilmnet
 * @copyright  2020, Doug Loomer
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_ukfilmnet\output;

defined('MOODLE_INTERNAL' || die());

use stdClass;
require_once('emailverifyform.php');
require_once(__DIR__.'/../../signuplib.php');

class emailverifypage implements \renderable, \templatable {

    private $page_number;
    
    /**
     * A class constructor
     *
     * @param string $page_number A number in string format
     * @return object An object of this class
     */
    public function __construct($page_number) {
        $this->page_number = $page_number;
    }

    /**
     * Exports data to for rendering
     *
     * @param object A renderer class object
     * @return object The data captured and created by this object
     */
    public function export_for_template(\renderer_base $output) {
        $data = new stdClass();
        $data->emailverifyinput = $this->get_emailverify_content();
        return $data;
    }

    /**
     * Gets form data and processes it and data it may create for rendering
     *
     * @return string Data obtained for rendering
     */
    public function get_emailverify_content() {

        global $CFG;

        $emailverifyinput = '';
        $mform = new emailverify_form();

        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            go_to_page(strval(0));
        } else if ($form_data = $mform->get_data()) {
            
            // Make sure we have the right user
            $verified_user = applicant_login($form_data->username, $form_data->password);
    
            if($verified_user !== null) {
                profile_load_data($verified_user);
                $verified_user->profile_field_emailverified = true;
                $verified_user->profile_field_applicationprogress = convert_progressnum_to_progressstring(3);
                profile_save_data($verified_user);
            }
        } else {
            // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed or on the first display of the form.
            $toform = $mform->get_data();
            //Set default data (if any)
            $mform->set_data($toform);
            //displays the form
            $emailverifyinput = $mform->render();
        }
        return $emailverifyinput;
    }
}
