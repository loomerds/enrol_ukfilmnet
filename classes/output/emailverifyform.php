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
 * Generator tool functions.
 *
 * @package    enrol_ukfilmnet
 * @copyright  2019, Doug Loomer
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** 
 *  
 */

namespace enrol_ukfilmnet\output;

defined('MOODLE_INTERNAL' || die());

require_once("$CFG->libdir/formslib.php");


class emailverify_form extends \moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;

        $mform = $this->_form; 
        $mform->addElement('text', 'username', get_string('applicant_username', 'enrol_ukfilmnet'), ['class'=>'ukfn-verification-content']);
        $mform->setType('username', PARAM_NOTAGS);
        $mform->addRule('username', get_string('error_missing_username', 'enrol_ukfilmnet'), 'required', null, 'server');
        $mform->addElement('password', 'password', get_string('applicant_password', 'enrol_ukfilmnet'), ['class'=>'ukfn-verification-content']);
        $mform->setType('password', PARAM_NOTAGS);
        $mform->addRule('password', get_string('error_missing_password', 'enrol_ukfilmnet'), 'required', null, 'server');
        $mform->addElement('text', 'code', get_string('verification_code', 'enrol_ukfilmnet'), ['class'=>'ukfn-verification-content']);
        $mform->setType('code', PARAM_NOTAGS);
        $mform->addRule('code', get_string('error_missing_code', 'enrol_ukfilmnet'), 'required', null, 'server');
        $this->add_action_buttons($cancel=true, $submitlabel=get_string('button_submit', 'enrol_ukfilmnet'), ['class'=>'ukfn-applicant-buttons']);            
    }
    //Custom validation should be added here
    function validation($data, $files) {
        global $DB, $CFG;
        //require_once($CFG->dirroot.'/grade/lib.php');
        require_once($CFG->dirroot.'/user/profile/lib.php');

        $errors = parent::validation($data, $files);
        $username = $data['username'];
        $user = $DB->get_record('user', array('username' => $username, 'auth' => 'manual'));
        if($user === false) {
            $errors['username'] = get_string('error_username_mismatch', 'enrol_ukfilmnet');
            return $errors;
        }
        //var_dump($user);
        //var_dump($data['password']);
        $password = $data['password'];
        $password2 = password_hash($password, PASSWORD_DEFAULT);
        
        if(!validate_internal_user_password($user, $password)) {
        //if($password2 !== $user->password) {
            $errors['password'] = get_string('error_password_mismatch', 'enrol_ukfilmnet');
        }

        profile_load_data($user);
        //var_dump($user);
        if($data['code'] !== $user->profile_field_verificationcode) {
            $errors['code'] = get_string('error_code_mismatch', 'enrol_ukfilmnet');
        }
        //if($data['role'] !== '01' && $data['role'] !== '02' && $data['role'] !== '03') {
        /*if((int)$data['code'] !== $USER->) {
            $errors['code'] = get_string('error_code_mismatch', 'enrol_ukfilmnet');
        }
        if($data['role'] === '0') {
            $errors['role'] = get_string('error_missing_role', 'enrol_ukfilmnet');
        }
        if(strpos($data['email'], '@') === false) {
            $errors['email'] = get_string('error_invalid_email', 'enrol_ukfilmnet');
        }*/
        return $errors;
    }
}