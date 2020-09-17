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
 * @copyright  2020, Doug Loomer
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_ukfilmnet\task;

global $USER, $DB, $CFG;
//require(__DIR__ .'/../../../../config.php');
require_once(__DIR__.'/../../signuplib.php');
require_once(__DIR__.'/../../../cohort/lib.php');

/**
 * An example of a scheduled task.
 */
class cleanup_tempsgo_accounts extends \core\task\scheduled_task {
 
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('cleanup_tempsgo_accounts_task', 'enrol_ukfilmnet');
    }
 
    /**
     * Execute the task.
     */
    public function execute() {
        //global $DB;

        mtrace("enrol_ukfilmnet temp sgo accounts cleanup task started");
        delete_temp_sgo_accounts();
        mtrace("enrol_ukfilmnet temp sgo accounts cleanup task finished");
    }
}