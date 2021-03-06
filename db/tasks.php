<?php
// This file is part of a 3rd party plugin for the Moodle LMS - http://moodle.org/
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
 * Moodle Scheduler Tasks.
 *
 * @package    enrol_ukfilmnet
 * @copyright  2020, Doug Loomer
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$tasks = [
    
    [
        'classname' => 'enrol_ukfilmnet\task\cleanup_tempsgo_accounts',
        'blocking' => 0,
        'minute' => '30',
        'hour' => '3',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    ],
    [
        'classname' => 'enrol_ukfilmnet\task\cleanup_nocohort_accounts',
        'blocking' => 0,
        'minute' => '32',
        'hour' => '3',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    ],
    [
        'classname' => 'enrol_ukfilmnet\task\cleanup_applicant_accounts',
        'blocking' => 0,
        'minute' => '31',
        'hour' => '3',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    ],
    
];