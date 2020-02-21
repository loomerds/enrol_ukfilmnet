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
use context_class;

require_once('studentsform.php');
require_once('signuplib.php');


// This is a Template Class it collects/creates the data for a template
class studentspage implements \renderable, \templatable {

    private $page_number;
    private $applicantprogress;

    public function __construct($page_number, $applicantprogress) {
        $this->page_number = $page_number;
        $this->applicantprogress = $applicantprogress;
    }

    public function export_for_template(\renderer_base $output) {
        $data = new stdClass();
        $data = $this->get_students_content();
        return $data;
    }

    public function get_students_content() {

        global $CFG, $DB, $USER;
        require_once($CFG->dirroot.'/lib/accesslib.php');
        require_once($CFG->dirroot.'/cohort/lib.php');
        
        // $studentsdata will be used to return data and structure for our enrol table
        $studentsdata = [];

        // Create the table headings row data
        $headings = array('title0'=>'Email', 'title1'=>'First Name', 'title2'=>'Family Name');
        
        // Create an array of this teacher's cohorts
        $teacher_cohorts = [];
        $cohort_names = $this->get_teacher_cohort_names(); 
        $cohorts = $DB->get_records('cohort');
        foreach($cohorts as $cohort){
            if(in_array($cohort->idnumber, $cohort_names)) {
                $teacher_cohorts[] = $cohort; 
            }
        }

        // Create an array of table rows data
        $rows = [];
        $students = $DB->get_records('user', array('deleted'=>0));
        // This conrtols how many rows our in our enrol table - add JS to make this better
        $rowsnum = intval(get_string('number_of_enrol_table_rows', 'enrol_ukfilmnet'));
        $count = 0;
        while($rowsnum > $count) {
            if(true) {
                $rows[] = ['userid'=>'',
                           'student_email'=>$this->create_student_email_input(null),
                           'student_firstname'=>$this->create_student_firstname_input(null),
                           'student_familyname'=>$this->create_student_familyname_input(null)];
            }
            $count++;
        }

        // Dynamically add cols data for each of this teacher's cohorts
        $studentsdata = ['headings'=>$headings, 'rows'=>$rows, 
                         'extra_header_cols'=>$this->make_extra_header_cols($cohort_names),
                         'extra_row_cols'=>$this->make_extra_row_cols($cohort_names)];
        $this->applicantprogress = 6;
        //$this->handle_redirects();
        return $studentsdata;
    }

    function get_teacher_cohort_names() {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot.'/lib/accesslib.php');

        $courses = get_courses();
        $cohort_names = [];
        //$context = context_system::instance();
        $capacity = 'enrol/manual:manage';
        foreach($courses as $course) {
            $context = \context_course::instance($course->id);
            if(is_enrolled($context, $USER, $capacity)) {
                $cohort_names[] = $course->shortname;
            }
        }
        return $cohort_names;
    }

    function make_extra_header_cols($cohort_names) {
        $extra_header_cols = '';
        $cohort_length = count($cohort_names);
        $count = 0;
        while($count < $cohort_length) {
            $extra_header_cols = $extra_header_cols.'<th class="header ukfn_text_center" scope="col">'.$cohort_names[$count].'</th>';
            $count++;
        }
        return $extra_header_cols;
    }

    function make_extra_row_cols($cohort_names) {
        $extra_row_cols = '';
        $cohort_length = count($cohort_names);
        $count = 0;
        while($count < $cohort_length) {
            $extra_row_cols = $extra_row_cols.'<td class="cell ukfn_text_center ukfn_enrol_col ukfn_checkbox_cell" scope="col"><input class="ukfn_checkbox" type="checkbox" name="'.$cohort_names[$count].'[]" value="'.$cohort_names[$count].'"><input type="hidden" name="'.$cohort_names[$count].'[]" value="0"></td>';
            $count++;
        }
        return $extra_row_cols;
    }

    function create_student_email_input($student_email) {
        return '<input type="text" name="student_email[]" value="'.$student_email.'">';
    }

    function create_student_firstname_input($student_firstname) {
        return '<input type="text" name="student_firstname[]" value="'.$student_firstname.'">';
    }

    function create_student_familyname_input($student_familyname) {
        return '<input type="text" name="student_familyname[]" value="'.$student_familyname.'">';
    }
    
    public function handle_redirects() {
        global $CFG, $SESSION;
        require_once(__DIR__.'/../../signuplib.php');

        if(isset($SESSION->cancel) and $SESSION->cancel == 1) {
            $SESSION->cancel = 0;
            redirect($CFG->wwwroot);
        } elseif($this->page_number != $this->applicantprogress) {
            force_signup_flow($this->applicantprogress);
        }
        return true;
    }
    /*
    //$studentsdata = ['headings'=>$headings];

        $studentsinput = '';
        $mform = new students_form();

        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            redirect('https://ukfilmnet.org');
        } else if ($fromform = $mform->get_data()) {
            //In this case you process validated data. $mform->get_data() returns data posted in form.
            $form_data = $mform->get_data();
            
            //$fullpath = $CFG->dirroot.'/enrol/ukfilmnet/assurancefiles';*/
            /*$fullpath = $CFG->dataroot.'/assurancefiles';
            $override = false;
            $filename = $mform->get_new_filename('assurance_form');
            $success = $mform->save_file('assurance_form', $fullpath.'/'.$filename, $override);
            $count = 0;
            while($success === false && $count < 10) {
                //$remove[] = "'";
                //$filename = trim(make_random_numstring().$filename, "'");
                //$filename = str_replace($remove, "", make_random_numstring().$filename);
                //rename($fullpath.'/'.$filename, $fullpath.'/'.((make_random_numstring().$filename)));
                $filename = make_random_numstring().$filename;
                $success = $mform->save_file('assurance_form', $fullpath.'/'.$filename, $override);
                $count = $count+1;

            $fullpath = $CFG->dirroot.'/enrol/ukfilmnet/assurancefiles';
            //$fullpath = $CFG->dataroot.'/assurancefiles';
            $override = false;
            $filename = $mform->get_new_filename('assurance_form');
            $success = $mform->save_file('assurance_form', $fullpath.'/'.$filename, $override);
            $count = 0;
            while($success === false && $count < 10) {
                //$remove[] = "'";
                //$filename = trim(make_random_numstring().$filename, "'");
                //$filename = str_replace($remove, "", make_random_numstring().$filename);
                //rename($fullpath.'/'.$filename, $fullpath.'/'.((make_random_numstring().$filename)));
                $filename = make_random_numstring().$filename;
                $success = $mform->save_file('assurance_form', $fullpath.'/'.$filename, $override);
                $count = $count+1;
            }

            $applicant_user = $DB->get_record('user', array('username' => $form_data->email, 'auth' => 'manual'));
            if($applicant_user !== null) {
                profile_load_data($applicant_user);
                $applicant_user->profile_field_qtsnumber = $form_data->qtsnumber;
                $applicant_user->profile_field_assurancesubmitted = 1;
                $applicant_user->profile_field_assurancesubmissiondate = time();
                $applicant_user->profile_field_assurancedoc = $filename;
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
            $studentsinput = $mform->render();
        }*/

}
