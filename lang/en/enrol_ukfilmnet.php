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
$string['safeguarding_title'] = 'Safeguarding';
$string['quals_title'] = 'Quals Info';
$string['students_title'] = 'Enrol Students';
$string['verifyemail_title'] = 'Verify Email';
$string['assurance_title'] = 'Assurance';

// Subtitles/Progress Items List Descriptors
$string['progressbox_title'] = ' Signup Progress';
$string['applicant_subheading'] = 'Applicant Information';
$string['emailverify_subheading'] = 'Applicant Email Verification';
$string['emailverification_subheading'] = 'Email Verification';
$string['institution_subheading'] = 'School Information';
$string['safeguardingreview_subheading'] = 'Safguarding Review';
$string['assurance_subheading'] = 'Safeguarding Assurance';
$string['quals_subheading'] = 'Quals Information';
$string['students_subheading'] = 'Enrol Students';

// Instructions Text
$string['applicant_instructions'] = 
'Welcome to the UKfilmNet Signup process.</br>
</br>
Before providing the information requested below, please take time to <strong><a href="https://www.ukfilmnet.org//UKfilmNet_gdpr.html" target="_blank">read the UKfilmNet PRIVACY NOTICE</a></strong>. Then input your current educational role, school email address, first name, and family name.';
$string['applicant_agreement'] = 
'</br>
<u>Important notice!</u> When you click on the "Submit" button above you are verifying that you have read, understand, and agree to our PRIVACY NOTICE, and that the information you are providing is correct to the best of your knowledge.';
$string['applicant_current_role'] = 'Current role';
$string['applicant_email'] = 'School email';
$string['applicant_firstname'] = 'First name';
$string['applicant_familyname'] = 'Family name';
$string['applicant_username'] = 'Username';
$string['applicant_password'] = 'Password';

$string['emailverify_instructions'] = 
'Thank you for submitting your basic UKfilmNet account creation information.</br>
</br>
Before we continue the Signup process, we need to verify that the email address you provided is valid. To that end, we have sent an email to you at the email address you provided. If you do not see the email we sent, please make sure to check your "spam" email folder.</br>
</br>
In that email we have provided you with:
<ul>
<li>your UKfilmNet username</li>
<li>your UKfilmNet password</li>
<li>a 6 digit Verification code</li>
</ul>
Please enter your UKfilmNet username, UKfilmNet password, and 6 digit Verification code below and click on the "Submit" button to log in, verify your email address, and continue the signup process.</br>
</br>';

$string['school_instructions'] = 
'In keeping with governmental requirements for student safeguarding, please provide the information requested below. You must also authorize UKfilmNet to contact your employing school directly to confirm the information you are providing.</br>
</br>';
$string['contact_info_label'] = '<h5 class="ukfn-contact-info">School Safeguarding Officer Information</h5>';
$string['contact_email'] = 'School email';
$string['contact_firstname'] = 'First name';
$string['contact_familyname'] = 'Family name';
$string['contact_phone'] = 'Phone number';
$string['consent_to_contact'] = '<div class="ukfn-consent-checkbox-label">I understand that by checking this box and clicking on "Submit" below <strong>I authorize UKfilmNet to contact my employer</strong> to verify the information I am submitting</div>';

$string['safeguarding_instructions'] = 
'Congratualtions on your UKfilmnet Signup progress!</br>
</br>
An email has been sent to the Safeguarding Officer at your school with a request that the officer verify your employment and the status of your QTS and DBS qualifications. You may wish to confirm with your Safeguarding Officer that they have received our email, and that they are in the process of providing UKfilmNet with the Assurance/Reference information we need to approve your application.</br>
</br>
When we have received and reviewed the Assurance/Reference infomation we requested from your Safeguarding Officer, we will email you to let you know if your application has been approved.';

$string['assurance_instructions'] = 
'Thank you for responding to our email requesting that you provde safeguarding assurances with reference to your employee.</br>
</br>
<!--As you know, in order to grant your employee access to UKfilmNet learning environment courses for the purpose of interacting with under age 18/vulnerable adult students at your school we are required to take reasonable steps to confirm that your employee is who they say they are, that they are employed by you, and that you are satisifed that they are QTS qualified and Enanced DBS certified to work with children or vulnerable adults.</br>
</br>-->
To that end, below please enter the School Email Address of your employee and Assurance Verification Code we provide you, as well as the QTS number of your employee. Then upload/attach the Assurance/Reference Form you completed in .pdf format, and click the "Submit" button.';

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
$string['verification_text'] = '
Thank you for requesting a UKfilmNet account. As a part of the signup process, we are providing you with the following important items:
<ul>
<li>UKfilmNet Username: <strong>{$a->username}</strong></li>
<li>UKfilmNet Password: <strong>{$a->password}</strong></li>
<li>your Email Verification Code: <strong>{$a->code}</strong></li>
</ul>
Please return to the UKfilmNet signup page titled Applicant Email Verification and input your username, password, and verification code. Then click "Submit" to continue the UKfilmNet Signup process.</br>
</br>
Your Friends at UKfilmNet';
$string['assurance_subject'] = 'Reference in Respect of {$a->applicant_firstname} {$a->applicant_familyname}';
$string['assurance_text'] = '
{$a->schoolname}</br>
{$a->contact_firstname} {$a->contact_familyname}</br>
</br>
<strong>Dear {$a->contact_firstname} {$a->contact_familyname},</br>
</br>
RE: Reference In Respect Of {$a->applicant_firstname} {$a->applicant_familyname}</strong></br>
</br>
One of your employees has requested access to UKfilmNet – which is a learning resource aimed at better supporting teacher/lecturer of media and film. The resource has been created by former and current teacher/lecturer, university staff and industry experts in film and media.</br>
</br>
<strong>What have they requested access to?</strong></br>
</br>
The online platform uses Moodle to create learning spaces which give teacher/lecturer and lecturers access to the students where they currently teach at {$a->schoolname}. It allows them to view hundreds of master-classes co-created with the BBC, Channel4, Sky News, NBC, and hundreds of experts from UK and US higher education, centres of excellence and broadcasters.</br>
</br>
The learning space allows your employee to set quizzes, share resources, create forums and promote communal learning, discovering and discussion in the craft of film and media.</br>
</br>
For reasons of safeguarding, your employee will not have access to students other than those from your organisation they currently teach. They will however be able to ask questions, seek advice and get help from hundreds of other teacher/lecturer of the same subject using the online resource and its virtual staff-room and forums. Likewise, those teachers/lecturers will not have access to students from other schools. This again is for reasons of safeguarding.</br>
</br>
<strong>Safeguarding Request</strong></br>
</br>
In order to grant access to {$a->applicant_firstname} {$a->applicant_familyname} we are required to take reasonable steps to confirm that the individual is who they say they are, and that they are employed by you. Please could either yourself or your designated Safeguarding lead complete the following process:
<ul>
<li>Download the attached Assurance/Reference Form</li>
<li>Print the Assurance/Reference Form on your Organisational Letterhead</li>
<li>Complete the Assurance/Reference Form in black or blue ink</li>
<li>Scan the completed Assurance/Reference Form and save it in .pdf format</li>
<li>Browse to <a href="ukfilmnet.org/learning/enrol/ukfilmnet/assurance.php"</a> and:</li>
<ul>
<li>Enter the Email Address of your employee - {$a->applicant_email}</li>
<li>Enter the following Assurance Verification Code - {$a->assurance_code}</li>
<li>Upload the completed Assurance/Reference Form</li>
<li>Click the "Submit" button to complete the Safeguarding Office Assurance process</li>
</ul>
</ul>
Once you have completed these steps we can consider the application your employee has made for access to the resource and other professional colleagues in developing their CPD, skills and knowledge.</br>
</br>
Please note that as part of the process you may receive a reminder of this request, but please note that after 4 weeks, the application from <strong>{$a->applicant_firstname} {$a->applicant_familyname}</strong> will automatically be deleted if the reference has not been received. <strong>As part of the requirements for access to the resource, your employee’s account details will be shared by email with the organisational head or designated Safeguarding lead, to ensure additional supervision and transparency of online activity conducted by the employee and his/her students.</strong></br>
</br>
If you have any queries, please email <a href="safeguarding@ukfilmnet.org"</a> where we hope to address any further questions.</br>
</br>
Yours Sincerely,</br>
</br>
Dom Foulsham</br>
Director of Curriculum | UKfilmNet.org';