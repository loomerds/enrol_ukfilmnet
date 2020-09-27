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
 * Creates an initial Applicant basic information collection page
 *
 * @package    enrol_ukfilmnet
 * @copyright  2020, Doug Loomer 
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $DB, $CFG, $USER;
require(__DIR__ . '/../../config.php');
require_once('./signuplib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/lib/enrollib.php');

if(!enrol_is_enabled('ukfilmnet')) {
    redirect(PAGE_WWWROOT);
}

$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/enrol/ukfilmnet/applicant.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('applicant_title', 'enrol_ukfilmnet'));
$page_number = 1;

$output = $PAGE->get_renderer('enrol_ukfilmnet');
$applicantpage = new \enrol_ukfilmnet\output\applicantpage($page_number);
$page_content = $output->render_applicantpage($applicantpage);

// Force non-submit based arrivals on the page to correct applicantprogress page 
if(isset($USER) and $USER->id != 0 and $USER->username != 'guest') {
    profile_load_data($USER);
    if(isset($USER->profile_field_applicationprogress)) {
        $progress = convert_progressstring_to_progressnum($USER->profile_field_applicationprogress);
        if($progress != $page_number) {
            go_to_page(strval($progress));
        }
    }
}           

echo $output->header();
echo $page_content;
echo $output->footer();