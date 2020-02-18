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
global $SESSION, $USER, $DB;
require(__DIR__ . '/../../config.php');
require_once('./signuplib.php');
require_once($CFG->dirroot.'/cohort/lib.php');


//$SESSION->assurance_info_complete = false;
require_login();
profile_load_data($USER);
//$application_progress = $USER->profile_field_applicationprogress;
//force_progress($application_progress, '6');


$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/enrol/ukfilmnet/students.php'));
$PAGE->set_context(context_system::instance());

if(!empty($_POST)) {
//var_dump('arrived here');
    process_students($_POST);
}
//$variable = cohort_get_cohorts(context_system::instance());
//var_dump($variable);
$PAGE->set_title(get_string('students_title', 'enrol_ukfilmnet'));
//$PAGE->navbar->add('Enrol students');
$output = $PAGE->get_renderer('enrol_ukfilmnet');

echo $output->header();

$studentspage = new \enrol_ukfilmnet\output\studentspage();
echo $output->render_studentspage($studentspage);

echo $output->footer();
/*if($SESSION->assurance_info_complete === true){
    $SESSION->assurance_info_complete === false;
    echo "<script>location.href='index.php'</script>";
} */ 