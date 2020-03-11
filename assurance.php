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

use \core\session\manager;

global $USER;
require(__DIR__ . '/../../config.php');
require_once('./signuplib.php');

// Create a temporary user account for the Safeguarding Officer
// Do it only once.
if($USER->id < 1 or $USER->firstname != 'Safeguarding') {

    $username = make_random_password();
    $password = make_random_password();
    $newuser = (object) array('email'=>$username,'username'=>$username,'firstname'=>'Safeguarding',
                              'lastname'=>'Officer',
                              'currentrole'=>'',
                              'applicationprogress'=>'',
                              'verificationcode'=>$username);
    $user = create_applicant_user($newuser, $password);
    $user->policyagreed = 1;
    manager::set_user($user);
}

$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/enrol/ukfilmnet/assurance.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('assurance_title', 'enrol_ukfilmnet'));
$page_number = 7;

$output = $PAGE->get_renderer('enrol_ukfilmnet');
$assurancepage = new \enrol_ukfilmnet\output\assurancepage();
$page_content = $output->render_assurancepage($assurancepage);

echo $output->header();
echo $page_content; 
echo $output->footer();