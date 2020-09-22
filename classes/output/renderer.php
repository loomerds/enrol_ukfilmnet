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
 * A renderer class for plugin pages.
 *
 * @package    enrol_ukfilmnet
 * @copyright  2020, Doug Loomer
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_ukfilmnet\output;

class renderer extends \plugin_renderer_base {

   /**
    * Renders the HTML for the applicant page.
    */
   public function render_applicantpage(\templatable $applicantpage) {
      $data = $applicantpage->export_for_template($this);
      return $this->render_from_template('enrol_ukfilmnet/applicantpage', $data);
   }

   /**
    * Renders the HTML for the emailverify page.
    */
   public function render_emailverifypage(\templatable $emailverifypage) {
      $data = $emailverifypage->export_for_template($this);
      return $this->render_from_template('enrol_ukfilmnet/emailverifypage', $data);
   }

   /**
    * Renders the HTML for the school page.
    */
   public function render_schoolpage(\templatable $schoolpage) {
      $data = $schoolpage->export_for_template($this);
      return $this->render_from_template('enrol_ukfilmnet/schoolpage', $data);
   }

   /**
    * Renders the HTML for the safeguarding page.
    */
   public function render_safeguardingpage(\templatable $safeguardingpage) {
      $data = $safeguardingpage->export_for_template($this);
      return $this->render_from_template('enrol_ukfilmnet/safeguardingpage', $data);
   }

   /**
    * Renders the HTML for the assurance page.
    */
   public function render_assurancepage(\templatable $assurancepage) {
      $data = $assurancepage->export_for_template($this);
      return $this->render_from_template('enrol_ukfilmnet/assurancepage', $data);
   }

   /**
    * Renders the HTML for the tracking page.
    */
   public function render_trackingpage(\templatable $trackingpage) {
      $data = $trackingpage->export_for_template($this);
      return $this->render_from_template('enrol_ukfilmnet/trackingpage', $data);
   }

   /**
    * Renders the HTML for the applicant page.
    */
   public function render_coursespage(\templatable $coursespage) {
      $data = $coursespage->export_for_template($this);
      return $this->render_from_template('enrol_ukfilmnet/coursespage', $data);
   }

   /**
    * Renders the HTML for the applicant page.
    */
   public function render_studentspage(\templatable $studentspage) {
      $data = $studentspage->export_for_template($this);
      return $this->render_from_template('enrol_ukfilmnet/studentspage', $data);
   }
}