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
$page_number = 8;

$output = $PAGE->get_renderer('enrol_ukfilmnet');
$trackingpage = new \enrol_ukfilmnet\output\trackingpage();
$page_content = $output->render_trackingpage($trackingpage);

$context = $PAGE->context;

try {
    require_capability('moodle/site:config', $context);
} catch (Exception $e) {
    redirect(PAGE_WWWROOT);
}

if(!empty($_POST)) {
    handle_tracking_post();
}

echo $output->header();
echo $page_content;
echo $output->footer();
