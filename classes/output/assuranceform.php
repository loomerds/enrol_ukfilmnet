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


class assurance_form extends \moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;

        $maxbytes = 185760;
        $mform = $this->_form; 
        $mform->addElement('text', 'email', get_string('employee_work_email', 'enrol_ukfilmnet'), ['class'=>'ukfn-assurance-content']);
        $mform->setType('email', PARAM_NOTAGS);
        $mform->addRule('email', get_string('error_missing_employee_work_email', 'enrol_ukfilmnet'), 'required', null, 'server');
        $mform->addElement('text', 'assurance_code', get_string('assurance_code', 'enrol_ukfilmnet'), ['class'=>'ukfn-assurance-content']);
        $mform->setType('assurance_code', PARAM_NOTAGS);
        $mform->addRule('assurance_code', get_string('error_missing_assurance_code', 'enrol_ukfilmnet'), 'required', null, 'server');
        $mform->addElement('text', 'qtsnumber', get_string('qtsnumber', 'enrol_ukfilmnet'), ['class'=>'ukfn-qts-content']);
        $mform->setType('qtsnumber', PARAM_NOTAGS);
        $mform->addRule('qtsnumber', get_string('error_missing_qtsnumber', 'enrol_ukfilmnet'), 'required', null, 'server');
        /*$mform->addElement('filemanager', 'assurance_form', get_string('assurance_form', 'enrol_ukfilmnet'), null,
                            array('subdirs'=>1, 'maxbytes'=>$maxbytes, 'areamaxbytes'=>$maxbytes, 'maxfiles'=>1,
                            'accepted_types'=>array('pdf'), 'return_types'=>FILE_INTERNAL | FILE_EXTERNAL));
        */
        $mform->addElement('file', 'assurance_form', get_string('assurance_form', 'enrol_ukfilmnet'));
        $mform->setType('MAX_FILE_SIZE', PARAM_INT);
        $mform->addRule('assurance_form', get_string('error_missing_assurance_form', 'enrol_ukfilmnet'), 'required', null, 'server');
        $this->add_action_buttons($cancel=true, $submitlabel=get_string('button_submit', 'enrol_ukfilmnet'), ['class'=>'ukfn-form-buttons']);            
      
    }
    //Custom validation should be added here
    function validation($data, $files) {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/user/profile/lib.php');

        $errors = parent::validation($data, $files);
        $username = $data['email'];
        $user = $DB->get_record('user', array('username' => $username, 'auth' => 'manual'));
        
        /*$username = $data['username'];
        $user = $DB->get_record('user', array('username' => $username, 'auth' => 'manual'));
        
        if($user === false) {
            $errors['username'] = get_string('error_username_mismatch', 'enrol_ukfilmnet');
            return $errors;
        }
        
        $password = $data['password'];
        
        if(!validate_internal_user_password($user, $password)) {
            $errors['password'] = get_string('error_password_mismatch', 'enrol_ukfilmnet');
        }
*/
        profile_load_data($user);
        
        var_dump($user->profile_field_assurancecode);
        if($data['assurance_code'] !== $user->profile_field_assurancecode) {
            $errors['assurance_code'] = get_string('error_assurance_code_mismatch', 'enrol_ukfilmnet');
        }
        
        return $errors;
    }
}