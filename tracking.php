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
 * Creates a page for the site administrator to track the progress of, approve, and deny teacher applications.
 *
 * @package    enrol_ukfilmnet
 * @copyright  2020, Doug Loomer 
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $DB, $CFG, $USER;
require(__DIR__ . '/../../config.php');
require_once('../locallib.php');
require_once('./signuplib.php');
require_once($CFG->dirroot.'/lib/dml/moodle_database.php');
require_once($CFG->dirroot.'/course/externallib.php');
require_once($CFG->dirroot.'/user/externallib.php');
require_once($CFG->dirroot.'/lib/pagelib.php');
require_once($CFG->dirroot.'/lib/enrollib.php');

require_login();

$context = context_system::instance();
if(!has_capability('moodle/role:manage', $context) or !enrol_is_enabled('ukfilmnet')) {
    redirect(PAGE_WWWROOT);
}

// We can uncomment and use the update_list function (call to it below) located in signuplib.php to create a list of uk schools from a .csv file and save the list in a json encoded .txt file named uk_schools_selector_list_array.txt

// The schoolform.php page calls the create_school_name_select_list function each time it loads, and that function uses the uk_schools_selector_list_array.txt file to build the school.php page's School select list.

// Before uncommenting the update_list function below you need to DEFINITELY - see signuplib.php comments for the update_list method for more details. 

// Don't forget to recomment the update_list function below. It should NOT remain uncommented.

//update_list();

$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/enrol/ukfilmnet/tracking.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('tracking_title', 'enrol_ukfilmnet'));
$page_number = 8;

$PAGE->requires->js(new moodle_url('/enrol/ukfilmnet/amd/src/sortable-tables.js'));
$output = $PAGE->get_renderer('enrol_ukfilmnet');
$trackingpage = new \enrol_ukfilmnet\output\trackingpage();
$page_content = $output->render_trackingpage($trackingpage);

$context = $PAGE->context;

try {
    require_capability('moodle/role:manage', $context);
} catch (Exception $e) {
    redirect(PAGE_WWWROOT);
}

if(!empty($_POST)) {
    handle_tracking_post();
}

echo $output->header();
echo $page_content;
echo $output->footer();
