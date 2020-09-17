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

defined('MOODLE_INTERNAL') || die();

/**
 * COHORT_CREATEGROUP constant for automatically creating a group for a cohort.
 */
//define('COHORT_CREATE_GROUP', -1);

/**
 * Cohort enrolment plugin implementation.
 * @author Doug Loomer
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_ukfilmnet_plugin extends enrol_plugin {
    /**                              
     * We are a good plugin and don't invent our own UI/validation code path.                                                       
     *          
     * @return boolean                                        
     */                                                                                                                             
    public function use_standard_editing_ui() {                            
        return true;                                             
    }
}