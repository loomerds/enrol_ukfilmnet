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
require_once('./signuplib.php');
global $SESSION, $USER;
//var_dump($USER);
if($USER->id < 1) {
    $username = make_random_password();
    $password = make_random_password();
    $newuser = (object) array('email'=>$username,'username'=>$username,'firstname'=>'Safeguarding','lastname'=>'Officer', 
                            'currentrole'=>'', 'applicationprogress'=>'', 'verificationcode'=>$username);
    $user = create_applicant_user($newuser, $password);
    //var_dump($user);
    applicant_login($username, $password);
}
//var_dump($verified_user);
//var_dump($SESSION->has_timed_out);
/**/
$SESSION->assurance_info_complete = false;
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/enrol/ukfilmnet/assurance.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('assurance_title', 'enrol_ukfilmnet'));

$PAGE->navbar->add('Assurance');

$output = $PAGE->get_renderer('enrol_ukfilmnet');

echo $output->header();

$assurancepage = new \enrol_ukfilmnet\output\assurancepage();
echo $output->render_assurancepage($assurancepage);

echo $output->footer();
if($SESSION->assurance_info_complete === true){
    $SESSION->assurance_info_complete === false;
    echo "<script>location.href='index.php'</script>";
}  