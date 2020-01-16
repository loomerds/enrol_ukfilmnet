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

$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/enrol/ukfilmnet/applicant.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('applicant_title', 'enrol_ukfilmnet'));
//$PAGE->set_heading("Some Heading");

//$PAGE->navbar->add(get_string('enrolmentoptions','enrol'));
$PAGE->navbar->add('Applicant info');

$output = $PAGE->get_renderer('enrol_ukfilmnet');

echo $output->header();



//echo $OUTPUT->heading(get_string('enrolmentoptions','enrol'));
//echo $output->heading("There is some kind of heading here");

$applicantpage = new \enrol_ukfilmnet\output\applicantpage("Joe loves his name");
echo $output->render_applicantpage($applicantpage);



/*
//include applicantform.php
require_once('applicantform.php');
 
//Instantiate simplehtml_form 
$mform = new applicant_form();
 
//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
} else if ($fromform = $mform->get_data()) {
  //In this case you process validated data. $mform->get_data() returns data posted in form.
} else {
  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
  // or on the first display of the form.
 
  //Set default data (if any)
  //$mform->set_data($toform);
  //displays the form
  $mform->display();
}*/

echo $output->footer();