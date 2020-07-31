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

global $USER;
require(__DIR__ . '/../../config.php');
require_once('./signuplib.php');



$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/enrol/ukfilmnet/emailverify.php'));

$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('verifyemail_title', 'enrol_ukfilmnet'));
$page_number = 2;

require_login();
is_applicant_user($USER);

$output = $PAGE->get_renderer('enrol_ukfilmnet');
$emailverifypage = new \enrol_ukfilmnet\output\emailverifypage($page_number);
$page_content = $output->render_emailverifypage($emailverifypage);

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