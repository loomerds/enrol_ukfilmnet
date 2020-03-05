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

    public function __construct($page_number) {
        $this->page_number = $page_number;
    }

    public function export_for_template(\renderer_base $output) {
        $data = new stdClass();
        $data = $this->get_students_content();
        return $data;
    }

    // Consider rewriting this function to use an mform approach
    public function get_students_content() {
        global $CFG, $DB, $USER;

        require_once($CFG->dirroot.'/lib/accesslib.php');
        require_once($CFG->dirroot.'/cohort/lib.php');
        
        // $studentsdata will be used to return data and structure for our enrol table
        $studentsdata = [];
        
        // Create an array of this teacher's cohorts
        $teacher_cohorts = [];
        $cohort_names = $this->get_teacher_cohort_names(); 
        $cohorts = $DB->get_records('cohort');
        foreach($cohorts as $cohort){
            if(in_array($cohort->idnumber, $cohort_names)) {
                $teacher_cohorts[] = $cohort; 
            }
        }

        $form_data = $this->build_table($cohort_names);
//print_r2($form_data);
        return $form_data;
    }

    private function get_teacher_cohort_names() {
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
        asort($cohort_names);
        $cohort_names = array_values($cohort_names);

        return $cohort_names;
    }

    private function make_extra_header_cols($cohort_names) {
        $extra_header_cols = '';
        $cohort_length = count($cohort_names);
        $count = 0;
        while($count < $cohort_length) {
            $extra_header_cols = $extra_header_cols.'<th class="header ukfn_text_center" scope="col">'.$cohort_names[$count].'</th>';
            $count++;
        }

        return $extra_header_cols;
    }

    private function make_extra_row_cols($cohort_names, $student) {
        $extra_row_cols = '';
        $cohort_length = count($cohort_names);
        $count = 0;
        $cohort_names_count = 0;
        
        if (is_array($student) || is_object($student)) {
            foreach($student as $key=>$value){
                if($count > 3) {
                    if($value === 1){
                        $extra_row_cols = $extra_row_cols.'<td class="cell ukfn_text_center ukfn_enrol_col ukfn_checkbox_cell" scope="col"><input class="ukfn_checkbox" type="checkbox" name="'.$cohort_names[$cohort_names_count].'[]" value="'.$cohort_names[$cohort_names_count].'" checked="checked"><input type="hidden" name="'.$cohort_names[$cohort_names_count].'[]" value="0"></td>';
                        $cohort_names_count++;
                    } else {
                        $extra_row_cols = $extra_row_cols.'<td class="cell ukfn_text_center ukfn_enrol_col ukfn_checkbox_cell" scope="col"><input class="ukfn_checkbox" type="checkbox" name="'.$cohort_names[$cohort_names_count].'[]" value="'.$cohort_names[$cohort_names_count].'"><input type="hidden" name="'.$cohort_names[$cohort_names_count].'[]" value="0"></td>';
                        $cohort_names_count++;
                    }
                }
                $count++;
            }
        } elseif($student === null) {
            foreach($cohort_names as $name) {
                $extra_row_cols = $extra_row_cols.'<td class="cell ukfn_text_center ukfn_enrol_col ukfn_checkbox_cell" scope="col"><input class="ukfn_checkbox" type="checkbox" name="'.$name.'[]" value="'.$name.'"><input type="hidden" name="'.$name.'[]" value="0"></td>';
            }
        }
        
        
        /*if (is_array($student) || is_object($student)) {
            foreach($student as $key=>$value){
                if($count > 3) {
                    if($value === 1){
                        $extra_row_cols = $extra_row_cols.'<td class="cell ukfn_text_center ukfn_enrol_col ukfn_checkbox_cell" scope="col"><input class="ukfn_checkbox" type="checkbox" name="'.$cohort_names[$cohort_names_count].'[]" value="'.$cohort_names[$cohort_names_count].'" checked="checked"><input type="hidden" name="'.$cohort_names[$cohort_names_count].'[]" value="0"></td>';
                        $cohort_names_count++;
                    } else {
                        $extra_row_cols = $extra_row_cols.'<td class="cell ukfn_text_center ukfn_enrol_col ukfn_checkbox_cell" scope="col"><input class="ukfn_checkbox" type="checkbox" name="'.$cohort_names[$cohort_names_count].'[]" value="'.$cohort_names[$cohort_names_count].'"><input type="hidden" name="'.$cohort_names[$cohort_names_count].'[]" value="0"></td>';
                        $cohort_names_count;
                    }
                }
                $count++;
            }
        } elseif($student === null) {
            foreach($cohort_names as $name) {
                $extra_row_cols = $extra_row_cols.'<td class="cell ukfn_text_center ukfn_enrol_col ukfn_checkbox_cell" scope="col"><input class="ukfn_checkbox" type="checkbox" name="'.$name.'[]" value="'.$name.'"><input type="hidden" name="'.$name.'[]" value="0"></td>';
            }
        }*/

        return $extra_row_cols;
    }

    private function set_checkbox($cohort_name, $student) {
        if($approval_status == 1) {
            return '<input type="checkbox" name="approved[]" value="'.$id.'" checked="checked">';
        }
        return '<input type="checkbox" name="approved[]" value="'.$id.'">';
    }

    private function create_student_email_input($student_email) {
        return '<input type="text" name="student_email[]" value="'.$student_email.'">';
    }

    private function create_student_firstname_input($student_firstname) {
        return '<input type="text" name="student_firstname[]" value="'.$student_firstname.'">';
    }

    private function create_student_familyname_input($student_familyname) {
        return '<input type="text" name="student_familyname[]" value="'.$student_familyname.'">';
    }

    // Create the table that will be used on the page and prepopuate it with any existing student data
    private function build_table($cohort_names) {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot.'/lib/accesslib.php');
        require_once($CFG->dirroot.'/cohort/lib.php');
        
        // Create the table headings row data
        $headings = array('title0'=>'Email', 'title1'=>'First Name', 'title2'=>'Family Name', 'extra_header_cols'=>$this->make_extra_header_cols($cohort_names) );

        // Create an array of table rows data
        $rows = [];
        $students = $this->get_enroled_students();

        // This controls how many rows our in our enrol table - add JS to make this better
        $rowsnum = intval(get_string('number_of_enrol_table_rows', 'enrol_ukfilmnet'));
        $count = 0;
        while($rowsnum + count($students) > $count) {
            if($count < count($students)) {
                foreach($students as $student) {
                    $rows[] = ['userid'=>'',
                            'student_email'=>$this->create_student_email_input($student['email']),
                            'student_firstname'=>$this->create_student_firstname_input($student['firstname']),
                            'student_familyname'=>$this->create_student_familyname_input($student['lastname']),
                            'extra_row_cols'=>$this->make_extra_row_cols($cohort_names, $student)];
                    $count++;
                }
            } else {
                $rows[] = ['userid'=>'',
                           'student_email'=>$this->create_student_email_input(null),
                           'student_firstname'=>$this->create_student_firstname_input(null),
                           'student_familyname'=>$this->create_student_familyname_input(null),
                           'extra_row_cols'=>$this->make_extra_row_cols($cohort_names, null)];
                $count++;
            }
        }

        // Dynamically add cols data for each of this teacher's cohorts
        $studentsdata = ['headings'=>$headings, 'rows'=>$rows]; 

        return $studentsdata;
    }

    public function make_empty_table_rows() {

        // This controls how many empty rows are added to our enrol table
        $rowsnum = intval(get_string('number_of_enrol_table_rows', 'enrol_ukfilmnet'));
        $count = 0;
        while($rowsnum > $count) {
            $rows[] = ['userid'=>'',
                    'student_email'=>$this->create_student_email_input(null),
                    'student_firstname'=>$this->create_student_firstname_input(null),
                    'student_familyname'=>$this->create_student_familyname_input(null)];
            $count++;
        }
    }

    // Functions to make initial page content from teacher's persisted course/cohort/student data

    private function get_teacher_cohort_ids() {
        global $DB;
        $teacher_cohort_names = $this->get_teacher_cohort_names();
        $teacher_cohort_ids = [];
        $cohorts = $DB->get_records('cohort');
        foreach($teacher_cohort_names as $cohort_name) {
            foreach($cohorts as $cohort) {
                if($cohort->idnumber == $cohort_name) {
                    $teacher_cohort_ids []= $cohort->id;
                }
            }
        }

        return $teacher_cohort_ids;
    } 

    private function get_students_in_cohorts() {
        global $DB;

        $teacher_cohort_ids = $this->get_teacher_cohort_ids();
        $cohort_members = $DB->get_records('cohort_members');
        $students_in_cohorts = [];
        foreach($teacher_cohort_ids as $cohort_id) {
            $students_in_cohorts += [$cohort_id=>[]];
        }
        foreach($students_in_cohorts as $key=>&$value) {
            foreach($cohort_members as $member) {
                if($member->cohortid == $key) {
                    $value[] = $member->userid;
                }
            }
        }

        return $students_in_cohorts;
    }

    private function get_students_list_by_ids() {
        global $DB;

        $students_in_cohorts = $this->get_students_in_cohorts();
        $students_list_by_id = [];
        foreach($students_in_cohorts as $students_in_cohort) {
            foreach($students_in_cohort as $key=>$value) {
                if(!in_array($value,$students_list_by_id)) {
                    $students_list_by_id[] = $value;
                }
            }
        }
        
        return $students_list_by_id;
    }

    private function get_enroled_students() {
        global $DB;

        $students_list_by_ids = $this->get_students_list_by_ids();
        $enroled_students = [];
        foreach($students_list_by_ids as $id) {
            $student = $DB->get_record('user', array('id'=>$id));
            $enroled_students[] = array('id'=>$id, 'email'=>$student->email, 'firstname'=>$student->firstname, 'lastname'=>$student->lastname);
        }
        $cohort_ids = $this->get_teacher_cohort_ids();
        foreach($enroled_students as &$student) {
            foreach($cohort_ids as $id) {
                $student += [$id=>0];
            }
        }
        $students_in_cohorts = $this->get_students_in_cohorts();
        foreach($enroled_students as $i_student=>&$value) {
            foreach($students_in_cohorts as $i_cohort=>$vals){
                if(in_array($value['id'],$vals)) {
                    $value[$i_cohort] = 1;
                }
            }
        }

        return $enroled_students;
    }
}
