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

        require_once('./signuplib.php');
        
        // Array to provide table column headings
        $headings = array('title0'=>'App', 
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
                          'title11'=>'Assurance Uploaded', 
                          'title12'=>'Den');
        
        // Array to provide table row fields
        $rows = [];

        // Get an array containing all applicant user records
        $applicants = $DB->get_records('user', array('deleted'=>0)); 
        
        // Fill row fields with relevant applicant information
        foreach($applicants as $applicant) {
            profile_load_data($applicant);

            if(convert_progressstring_to_progressnum($applicant->profile_field_applicationprogress) > 0) {
                $rows[] = [
                    'userid'=>$applicant->id,
                    'firstname'=>$applicant->firstname,
                    'familyname'=>$applicant->lastname,
                    'fullname'=>$this->make_applicant_fullname_link($applicant->lastname, $applicant->firstname, $applicant->id),
                    'email'=>$this->make_applicant_email_link($applicant->email), 
                    'courses'=>$applicant->profile_field_courses_requested,
                    'currentrole'=>convert_rolestring_to_rolenum($applicant->profile_field_currentrole),    
                    'applicationprogress'=>$this->make_progress_cell(convert_progressstring_to_progressnum($applicant->profile_field_applicationprogress)), 
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
        require_once('./signuplib.php');

        if(is_string($date)) {
            $date_chars = str_split($date);
            if(count($date_chars) === 10) {
                return convert_unixtime_to_gmdate($date);
            }
            return $date;
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
            return '<span class="ukfn_0em_text">1<input type="checkbox" name="approved[]" value="'.$id.'" checked="checked"></span>';
        }
        return '<span class="ukfn_0em_text">0<input type="checkbox" name="approved[]" value="'.$id.'"></span>';
    }

    private function application_denied($denial_status, $id) {
        if($denial_status == 1) {
            return '<span class="ukfn_0em_text">1<input type="checkbox" name="denied[]" value="'.$id.'" checked="checked"></span>';
        }
        return '<span class="ukfn_0em_text">0<input type="checkbox" name="denied[]" value="'.$id.'"></span>';
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

    private function make_progress_cell($progress) {

        if($progress == 1) {
            return '<td class="cell ukfn_text_center" scope="col"><span class="ukfn_progress_1">'.$progress.'</span>';
        } elseif($progress == 6) {
            return '<td class="cell ukfn_text_center" scope="col"><span class="ukfn_progress_6">'.$progress.'</span>';
        } elseif($progress == 7) {
            return '<td class="cell ukfn_text_center" scope="col"><span class="ukfn_progress_7">'.$progress.'</span>';
        } else {
            return '<td class="cell ukfn_text_center" scope="col"><span class="ukfn_progress">'.$progress.'</span>';
        }
    }

}
