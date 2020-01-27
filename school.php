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
require_login();
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/enrol/ukfilmnet/school.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('institution_title', 'enrol_ukfilmnet'));
//$PAGE->set_heading("Some Heading");

//$PAGE->navbar->add(get_string('enrolmentoptions','enrol'));
$PAGE->navbar->add('School info');

$output = $PAGE->get_renderer('enrol_ukfilmnet');

echo $output->header();

$schoolpage = new \enrol_ukfilmnet\output\schoolpage(null);
echo $output->render_schoolpage($schoolpage);

echo $output->footer();