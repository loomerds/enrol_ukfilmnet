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

global $DB, $CFG;
require(__DIR__ . '/../../config.php');
require_once('../locallib.php');
require_once('./signuplib.php');
require_once($CFG->dirroot.'/lib/dml/moodle_database.php');
require_once($CFG->dirroot.'/course/externallib.php');
require_once($CFG->dirroot.'/user/externallib.php');

require_login();

$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/enrol/ukfilmnet/tracking.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('tracking_title', 'enrol_ukfilmnet'));
$PAGE->navbar->add('Tracking');
$page_number = 8;

$output = $PAGE->get_renderer('enrol_ukfilmnet');
$trackingpage = new \enrol_ukfilmnet\output\trackingpage();
$page_content = $output->render_trackingpage($trackingpage);

if(!empty($_POST)) {
    handle_tracking_post();
}

$context = $PAGE->context;
try {
    require_capability('moodle/site:config', $context);
} catch (Exception $e) {
    redirect(PAGE_WWWROOT);
}

// This should probably be factored out
// Handle cancels
if(isset($_POST['cancel'])) {
    go_to_page(strval(0));
}
// Handle submits 
elseif(isset($_POST['submitbutton'])) {
    // If all required inputs were received progress to next signup page
    $form_items = $_POST;
    $all_items_submitted = true;
    foreach($form_items as $key=>$value) {
        if($value == null or ($key == 'ukprn' and !is_array($value))) {
            $all_items_submitted = false;
        }
        if($page_number == 3 and !array_key_exists('school_consent_to_contact', $form_items)) {
            $all_items_submitted = false;
        }
    }
    if($all_items_submitted == true) {
        //go_to_page(strval(1+$page_number)); //what about final page?
        redirect(PAGE_WWWROOT);
    }
}
// Force non-submit based arrivals on the page to correct applicantprogress page
else {
    if(isset($USER) and $USER->id != 0 and $USER->username != 'guest') {
        profile_load_data($USER);
        if(isset($USER->profile_field_applicationprogress)) {
            $progress = $USER->profile_field_applicationprogress;
            if($progress != $page_number) {
                go_to_page(strval($progress));
            }
        }
    }               
}

echo $output->header();
echo $page_content;
echo $output->footer();
