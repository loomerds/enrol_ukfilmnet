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

// This is a Template Class it collects/creates the data for a template
class trackingpage implements \renderable, \templatable {
    var $sometext = null;

    public function __construct($sometext = null) {
        $this->sometext = $sometext;
    }

    public function export_for_template(\renderer_base $output) {
        $data = new stdClass();
        $data = $this->get_tracking_content();
        return $data;
    }

    public function get_tracking_content() {
        global $CFG, $USER, $DB;
        
        $headings = array('title0'=>'Approved', 'title1'=>'Progress', 'title2'=>'Role', 'title3'=>'Name', 'title4'=>'Email',
                          'title5'=>'Country', 'title6'=>'School', 'title13'=>'UKPRN', 'title7'=>'SG Name', 'title8'=>'SG Phone', 
                          'title9'=>'SG Email', 'title10'=>'SG Form', 'title11'=>'Form Date', 'title12'=>'Denied');
        $rows = [];
        $applicants = $DB->get_records('user', array('deleted'=>0)); 
        
        foreach($applicants as $applicant) {
            profile_load_data($applicant);
            if($applicant->profile_field_applicationprogress > 1) {
                $rows[] = ['userid'=>$applicant->id, 'firstname'=>$applicant->firstname,
                           'familyname'=>$applicant->lastname, 'email'=>$applicant->email, 
                           'currentrole'=>$applicant->profile_field_currentrole, 
                           'applicationprogress'=>$applicant->profile_field_applicationprogress, 
                           'schoolname'=>$applicant->profile_field_schoolname, 
                           'ukprn'=>$applicant->profile_field_ukprn,
                           'schoolcountry'=>$applicant->profile_field_schoolcountry, 
                           'contact_firstname'=>$applicant->profile_field_safeguarding_contact_firstname,
                           'contact_familyname'=>$applicant->profile_field_safeguarding_contact_familyname, 
                           'contact_email'=>$applicant->profile_field_safeguarding_contact_email,
                           'contact_phone'=>$applicant->profile_field_safeguarding_contact_phone, 
                           'qtsnumber'=>$applicant->profile_field_qtsnumber, 
                           'assurancesubmissiondate'=>$this->check_date_exists($applicant->profile_field_assurancesubmissiondate), 
                           'assurancedoc'=>$this->check_download_exists($applicant->profile_field_assurancedoc),
                           'applicationapproved'=>$this->application_approved($applicant->profile_field_applicationapproved, $applicant->id),
                           'applicationdenied'=>$this->application_denied($applicant->profile_field_applicationdenied, $applicant->id)];
            }
        }
        $trackingdata = ['headings'=>$headings, 'rows'=>$rows];
        //var_dump($trackingdata);
        /*$mform = new tracking_form();

        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            redirect('https://ukfilmnet.org/learning');
        } else if ($fromform = $mform->get_data()) {
            //In this case you process validated data. $mform->get_data() returns data posted in form.
                        
            $form_data = $mform->get_data();
        } else {
            // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
            // or on the first display of the form.
            $toform = $mform->get_data();
            
            //Set default data (if any)
            $mform->set_data($toform);
            //displays the form
            $trackinginput = $mform->render();
        }*/

        return $trackingdata;
    }

    private function check_date_exists($date) {
        if($date > 0) {
            return gmdate("Y-m-d", (int)$date);
        }
        return null;
    }

    private function check_download_exists($file) {
        if(strlen($file) > 0) {
            return '<a href=./assurancefiles'.'/'.$file.'>View</a>';
        }
        return null;
    }

    private function application_approved($approval_status, $id) {
        if($approval_status == 1) {
            return '<input type="checkbox" name="approved[]" value="'.$id.'" checked="checked">';
        }
        return '<input type="checkbox" name="approved[]" value="'.$id.'">';
    }

    private function application_denied($denial_status, $id) {
        if($denial_status == 1) {
            return '<input type="checkbox" name="denied[]" value="'.$id.'" checked="checked">';
        }
        return '<input type="checkbox" name="denied[]" value="'.$id.'">';
    }

}
