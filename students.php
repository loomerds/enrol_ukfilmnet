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
 * Development data generator.
 *
 * @package    enrol_ukfilmnet
 * @copyright  2019, Doug Loomer 
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \core\session\manager;

global $USER, $DB;
require(__DIR__ . '/../../config.php');
require_once('./signuplib.php');
require_once(__DIR__ .'/../../cohort/lib.php');

require_login();
is_applicant_user($USER);

/*if($USER->username == 'guest') {
    
   // manager::set_user($user);

    redirect(PAGE_WWWROOT.'/login/index.php');
}*/

/*$teach_enrol = $DB->get_record('user_enrolments', array('userid' => $USER->id));
foreach($teach_enrol as $enrolment) {
    $teacher_enrol_info = $DB->get_record('enrol', array('id' =>$enrolment));
    if(!isset($teacher_enrol_info)) {
        print_r2($USER->username);
    }
    print_r2($teacher_enrol_info);
}
unset($teach_enrol);
$teach_enrol = $DB->get_record('user_enrolments', array('userid' => $USER->id));
foreach($teach_enrol as $enrolment) {
    $teacher_enrol_info = $DB->get_record('enrol', array('id' =>$enrolment));
    $courseid = $teacher_enrol_info->courseid;
    $enrolees = $DB->get_records('enrol', array('courseid'=>$courseid));
    foreach($enrolees as $enrolee) {
        print_r2($enrolee);
    }
}*/

/*$enrolees = $DB->get_records('user_enrolments'); //get all enrolees
foreach($enrolees as $enrolee) {
    $userid = $enrolee->userid;
    $enrolid = $enrolee->enrolid;
    $user = $DB->get_record('user', array('id' => $userid));
    print_r2($user->username);
    print_r2($enrolee);
    $enrol = $DB->get_record('enrol', array('id' => $enrolid));
    print_r2($enrol);
}
*/

//print_r2($DB->get_records('role'));

$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/enrol/ukfilmnet/students.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('students_title', 'enrol_ukfilmnet'));
$page_number = 6;

$output = $PAGE->get_renderer('enrol_ukfilmnet');
$studentspage = new \enrol_ukfilmnet\output\studentspage($page_number);
$page_content = $output->render_studentspage($studentspage);
if(!empty($_POST)) {
    handle_enrol_students_post($_POST);
}

if(isset($USER) and $USER->id != 0 and $USER->username != 'guest') {
    profile_load_data($USER);
    if(isset($USER->profile_field_applicationprogress)) {
        $progress = $USER->profile_field_applicationprogress;
        if($progress != $page_number) {
            //go_to_page(strval($progress));
        }
    }
}
                
echo $output->header();
echo $page_content;
echo $output->footer();
