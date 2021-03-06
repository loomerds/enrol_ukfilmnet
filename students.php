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
 * Creates a page for the approved teacher to enrol and unenrol students to/from their Classroom courses.
 *
 * @package    enrol_ukfilmnet
 * @copyright  2020, Doug Loomer 
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \core\session\manager;

global $USER, $DB;
require(__DIR__ . '/../../config.php');
require_once('./signuplib.php');
require_once(__DIR__ .'/../../cohort/lib.php');
require_once($CFG->dirroot.'/lib/enrollib.php');

if(!enrol_is_enabled('ukfilmnet')) {
    redirect(PAGE_WWWROOT);
}

require_login();
is_applicant_user($USER);

$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/enrol/ukfilmnet/students.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('students_title', 'enrol_ukfilmnet'));
$page_number = 6;

$PAGE->requires->js(new moodle_url('/enrol/ukfilmnet/amd/src/sortable-tables.js'));
$output = $PAGE->get_renderer('enrol_ukfilmnet');
$studentspage = new \enrol_ukfilmnet\output\studentspage($page_number);
$page_content = $output->render_studentspage($studentspage);

if(!empty($_POST)) {
    handle_enrol_students_post($_POST);
}

if(isset($USER) and $USER->id != 0 and $USER->username != 'guest') {
    profile_load_data($USER);
    if(isset($USER->profile_field_applicationprogress)) {
        $progress = convert_progressstring_to_progressnum($USER->profile_field_applicationprogress);
        if($progress != 7) {
            go_to_page(strval($progress));
        }
    }
}
                
echo $output->header();
echo $page_content;
echo $output->footer();
