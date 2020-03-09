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

    public function __construct($sometext = null) {
    }

    public function export_for_template(\renderer_base $output) {
        $data = new stdClass();
        $data = $this->get_tracking_content();
        return $data;
    }

    // Consider rewriting this function to use an mform approach
    public function get_tracking_content() {
        global $CFG, $USER, $DB;
        
        // Array to provide table column headings
        $headings = array('title0'=>'Approved', 
                          'title1'=>'Progress', 
                          'title2'=>'Role', 
                          'title3'=>'Name', 
                          'title4'=>'Email',
                          'title5'=>'Country', 
                          'title14'=>'Courses',
                          'title6'=>'School', 
                          'title13'=>'UKPRN', 
                          'title7'=>'SG Name', 
                          'title8'=>'SG Phone', 
                          'title9'=>'SG Email', 
                          'title10'=>'SG Form', 
                          'title11'=>'Form Date', 
                          'title12'=>'Denied');
        
        // Array to provide table row fields
        $rows = [];

        // Get an array containing all applicant user records
        $applicants = $DB->get_records('user', array('deleted'=>0)); 
        
        // Fill row fields with relevant applicant information
        foreach($applicants as $applicant) {
            profile_load_data($applicant);
            if($applicant->profile_field_applicationprogress > 0) {
                $rows[] = [
                    'userid'=>$applicant->id,
                    'firstname'=>$applicant->firstname,
                    'familyname'=>$applicant->lastname,
                    'fullname'=>$this->make_applicant_fullname_link($applicant->lastname, $applicant->firstname, $applicant->id),
                    'email'=>$this->make_applicant_email_link($applicant->email), 
                    'courses'=>$applicant->profile_field_courses_requested,
                    'currentrole'=>$applicant->profile_field_currentrole,    
                    'applicationprogress'=>$applicant->profile_field_applicationprogress, 
                    'schoolname'=>$applicant->profile_field_schoolname, 
                    'ukprn'=>$applicant->profile_field_ukprn,
                    'schoolcountry'=>$applicant->profile_field_schoolcountry, 
                    'contact_firstname'=>$applicant->profile_field_safeguarding_contact_firstname,
                    'contact_familyname'=>$applicant->profile_field_safeguarding_contact_familyname, 
                    'contact_email'=>$this->make_safeguarding_email_link($applicant->profile_field_safeguarding_contact_email),
                    'contact_phone'=>$applicant->profile_field_safeguarding_contact_phone, 
                    'qtsnumber'=>$applicant->profile_field_qtsnumber, 
                    'assurancesubmissiondate'=>$this->check_date_exists($applicant->profile_field_assurancesubmissiondate), 
                    'assurancedoc'=>$this->check_download_exists($applicant->profile_field_assurancedoc),
                    'applicationapproved'=>$this->application_approved($applicant->profile_field_applicationapproved, $applicant->id),
                    'applicationdenied'=>$this->application_denied($applicant->profile_field_applicationdenied, $applicant->id)];
            }
        }

        // An array containing all table data for all applicants
        $trackingdata = ['headings'=>$headings, 'rows'=>$rows];

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

    private function make_applicant_fullname_link($lastname, $firstname, $userid) {
        $fullname = '<a href="'.PAGE_WWWROOT.'/user/editadvanced.php?id='.$userid.'">'.$lastname.', '.$firstname.'</a>';
        return $fullname;
    }

    private function make_applicant_email_link($email) {
        $email = '<a href="mailto:'.$email.'">'.$email.'</a>';
        return $email;
    }

    private function make_safeguarding_email_link($email) {
        $email = '<a href="mailto:'.$email.'">'.$email.'</a>';
        return $email;
    }

}
