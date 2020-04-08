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

global $CFG, $SESSION;


require(__DIR__ . '/../../config.php');
require_once('./signuplib.php');
defined('MOODLE_INTERNAL') || die();
// This page exists to allow a call to this function from a site html block  which sets up custom UKfilmNet profile_fields - it should only be run when initially setting up the site in order to ensure that the profile fields exist

create_profile_fields(); // function is located in signuplib.php

redirect(PAGE_WWWROOT.'/admin/search.php?query');