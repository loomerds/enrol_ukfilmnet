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

global $DB, $SESSION, $CFG;
require(__DIR__ . '/../../config.php');
require_once('../locallib.php');
require_once('./signuplib.php');
require_once($CFG->dirroot.'/lib/dml/moodle_database.php');
require_once($CFG->dirroot.'/course/externallib.php');
require_once($CFG->dirroot.'/user/externallib.php');

$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/enrol/ukfilmnet/tracking.php'));
$PAGE->set_context(context_system::instance());
$context = $PAGE->context;
require_login();
try {
    require_capability('moodle/site:config', $context);
} catch (Exception $e) {
    echo "<script>location.href='/index.php'</script>";
}
if(!empty($_POST['approved'])) {
    application_approved($_POST['approved']);
}

if(!empty($_POST['denied'])) {
    application_denied($_POST['denied']);
}

$PAGE->set_title(get_string('tracking_title', 'enrol_ukfilmnet'));
$PAGE->navbar->add('Tracking');
$output = $PAGE->get_renderer('enrol_ukfilmnet');
echo $output->header();

$trackingpage = new \enrol_ukfilmnet\output\trackingpage();

echo $output->render_trackingpage($trackingpage);
echo $output->footer();
