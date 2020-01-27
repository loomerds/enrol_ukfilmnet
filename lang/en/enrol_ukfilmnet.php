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
 *
 * @package    enrol_ukfilmnet
 * @copyright  2019, Doug Loomer 
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

 // General Descriptors
$string['configtitle'] = 'UKfilmNet';
$string['pluginname'] = 'UKfilmNet simple enrolment';
$string['plugin_heading'] = 'UKfilmNet Signup';

// Page Title Descriptors 
$string['applicant_title'] = 'Applicant Info';
$string['institution_title'] = 'School Info';
$string['quals_title'] = 'Quals Info';
$string['students_title'] = 'Enrol Students';
$string['verifyemail_title'] = 'Verify Email';

// Subtitles/Progress Items List Descriptors
$string['progressbox_title'] = ' Signup Progress';
$string['applicant_subheading'] = 'Applicant Information';
$string['emailverify_subheading'] = 'Applicant Email Verification';
$string['institution_subheading'] = 'School Information';
$string['quals_subheading'] = 'Quals Information';
$string['students_subheading'] = 'Enrol Students';

// 
$string['applicant_instructions'] = 'Welcome to the UKfilmNet Signup process.</br>
</br>Before providing the information requested below, please take time to 
<strong><a href="https://www.ukfilmnet.org//UKfilmNet_gdpr.html" target="_blank">
read the UKfilmNet PRIVACY NOTICE</a></strong>. Then input your current educational role, 
school email address, first name, and family name.';
$string['applicant_agreement'] = '</br><u>Important notice!</u> When you click on the 
"Submit" button above you are verifying that you have read, understand, and agree to our 
PRIVACY NOTICE, and that the information you are providing is correct to the best of your knowledge.';
$string['applicant_current_role'] = 'Current role';
$string['applicant_email'] = 'School email';
$string['applicant_firstname'] = 'First name';
$string['applicant_familyname'] = 'Family name';
$string['applicant_username'] = 'Username';
$string['applicant_password'] = 'Password';



$string['emailverify_instructions'] = 'Thank you for submitting your basic UKfilmNet account 
creation information.</br></br>Before we continue the Signup process, we need to verify 
that the email address you provided is valid. To that end, we have sent an email to you at the 
email address you provided. If you do not see the email we sent, please make sure to check your "spam" email folder.
</br></br>In that email we have provided you with:<ul><li>your UKfilmNet username</li>
<li>your UKfilmNet password</li> <li>a 6 digit Verification code</li></ul>
Please enter your UKfilmNet username, UKfilmNet password, and 6 digit Verification code below and click on the "Submit" 
button to log in, verify your email address, and continue the signup process.</br></br>';

$string['school_instructions'] = 'In keeping with governmental requirements for student safeguarding, please provide the 
information requested below. You must also authorize UKfilmNet to contact your employing school directly to confirm the 
information you are providing.</br></br>';
$string['contact_info_label'] = '<h5 class="ukfn-contact-info">School Safeguarding Officer Information</h5>';
$string['contact_email'] = 'School email';
$string['contact_firstname'] = 'First name';
$string['contact_familyname'] = 'Family name';
$string['contact_phone'] = 'Phone number';
$string['consent_to_contact'] = '<div class="ukfn-consent-checkbox-label">I understand that by checking this box and 
clicking on "Submit" below <strong>I authorize UKfilmNet to contact my employer</strong> to verify the information I am 
submitting</div>';


// Current Role Descriptors
$string['applicant_role_instruction'] = 'Choose a role...';
$string['applicant_role_ukteacher'] = '1. UK Teacher/lecturer under contract with UK education organisation';
$string['applicant_role_teacherbsa'] = '2. Teacher/lecturer under contract with BSA recognised by UK DIE OFSTED';
$string['applicant_role_uksupplyteacher'] = '3. UK Supply/freelance teacher/lecturer (no single organisation';
$string['applicant_role_instructor18plus'] = '4. Instructor of only 18s and above';
$string['applicant_role_instructor17minus'] = '5. Instructor of under 18s';
$string['applicant_role_student17minus'] = '6. Student under 18';
$string['applicant_role_student18plus'] = '7. Student 18 and above';
$string['applicant_role_industryprofessional'] = '8. Industry professional';
$string['applicant_role_educationconsultant'] = '9. Education Consultant';
$string['applicant_role_parentguardian'] = '10. Parent/Guardian';

// Country Discriptors
$string['school_country_instruction'] = 'Choose a country...';
$string['school_country_label'] = 'Country of school';
$string['GB'] = 'United Kingdom';
$string['US'] = 'United States';

// School Name Discriptors
$string['school_name_label'] = 'Name of school';
$string['school_name_instruction'] = 'Choose a school...';
$string['school1'] = 'Bob White Academy';
$string['school2'] = 'Den of Thieves College Prep School';

// Button Labels
$string['button_next'] = 'Next';
$string['button_submit'] = 'Submit';

// Error Messages
$string['error_missing_role'] = 'Missing current role';
$string['error_missing_email'] = 'Missing school email address';
$string['error_missing_firstname'] = 'Missing first name';
$string['error_missing_familyname'] = 'Missing family name';
$string['error_role_limits'] = 'Sorry, but at this time ukfilmnet is only available to applicants in roles 1-3.';
$string['error_invalid_email'] = 'You must enter a VALID email address';
$string['error_missing_code'] = 'Missing verification code';
$string['error_missing_username'] = 'Missing username';
$string['error_missing_password'] = 'Missing password';
$string['error_code_mismatch'] = 'The Verification code you entered is invalid';
$string['error_username_mismatch'] = 'The UKfilmnet username you entered is invalid';
$string['error_password_mismatch'] = 'The UKfilmnet password you entered is invalid';
$string['error_missing_country'] = 'Missing country of school';
$string['error_missing_contact_email'] = 'Missing school email address of contact';
$string['error_missing_contact_firstname'] = 'Missing first name of contact';
$string['error_missing_contact_familyname'] = 'Missing family name of contact';
$string['error_missing_school_name'] = 'Missing school name';
$string['error_missing_contact_phone'] = 'Missing phone number';
$string['error_missing_school_consent_to_contact'] = 'You must authorize UKfilmNet to contact your school employer to verifiy the information you are submitting';


// String variables
$string['roleallowed_range_max'] = '03';


$string['verification_code'] = 'Verification code';

// Email strings
$string['verification_subject'] = 'UKfilmNet Email Verification';
$string['verification_text'] = 'Thank you for requesting a UKfilmNet account. As a part of the signup process, we are
providing you with the following important items:<ul><li>UKfilmNet Username: <strong>{$a->username}</strong></li>
<li>UKfilmNet Password: <strong>{$a->password}</strong></li> <li>your Email Verification Code: <strong>{$a->code}</strong></li></ul>
Please return to the UKfilmNet signup page titled Applicant Email Verification and input your username, password, and 
verification code. Then click "Submit" to continue the UKfilmNet Signup process.</br></br>Your Friends at UKfilmNet';
