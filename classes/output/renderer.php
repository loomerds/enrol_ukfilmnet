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

/** The user is put onto a waiting list and therefore the enrolment not active 
 * (used in user_enrolments->status) 
 */

namespace enrol_ukfilmnet\output;

class renderer extends \plugin_renderer_base {

   /*
    * Renders the HTML for the index page.
    * 
    */

   public function render_applicantpage(\templatable $applicantpage) {

      // Calls export_for_template function of applicantpage class in file
      // /enrol/ukfilmnet/classes/output/applicatpage.php
      $data = $applicantpage->export_for_template($this);
      // Calls render_from_template function of 
      return $this->render_from_template('enrol_ukfilmnet/applicantpage', $data);
   }

   public function render_emailverifypage(\templatable $emailverifypage) {

      // Calls export_for_template function of applicantpage class in file
      // /enrol/ukfilmnet/classes/output/applicatpage.php
      $data = $emailverifypage->export_for_template($this);
      // Calls render_from_template function of 
      return $this->render_from_template('enrol_ukfilmnet/emailverifypage', $data);
   }

   public function render_schoolpage(\templatable $schoolpage) {

      // Calls export_for_template function of applicantpage class in file
      // /enrol/ukfilmnet/classes/output/applicatpage.php
      $data = $schoolpage->export_for_template($this);
      // Calls render_from_template function of 
      return $this->render_from_template('enrol_ukfilmnet/schoolpage', $data);
   }

}