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
require(__DIR__ . '/../../config.php');
global $SESSION, $USER;
require_once('./signuplib.php');

require_login();


profile_load_data($USER);
$application_progress = $USER->profile_field_applicationprogress;
force_progress($application_progress, '4');

//$SESSION->email_info_complete = false;
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/enrol/ukfilmnet/courses.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('courses_title', 'enrol_ukfilmnet'));

$output = $PAGE->get_renderer('enrol_ukfilmnet');

echo $output->header();

$coursespage = new \enrol_ukfilmnet\output\coursespage();
echo $output->render_coursespage($coursespage);

echo $output->footer();
/*if($USER->profile_field_applicationprogress > 4){
    echo "<script>location.href='/enrol/ukfilmnet/safeguarding.php'</script>";
}
if($SESSION->email_info_complete === true){
    $SESSION->email_info_complete === false;
    echo "<script>location.href='/enrol/ukfilmnet/students.php'</script>";
}*/
$application_progress = $USER->profile_field_applicationprogress;
force_progress($application_progress, '4');
