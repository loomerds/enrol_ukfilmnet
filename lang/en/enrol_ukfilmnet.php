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
$string['tracking_title'] = 'Tracking';
$string['courses_title'] = 'Courses';
$string['students_title'] = 'Students';

// Subtitles/Progress Items List Descriptors
$string['progressbox_title'] = ' Signup Progress';
$string['applicant_subheading'] = 'Applicant Information';
$string['emailverify_subheading'] = 'Applicant Email Verification';
$string['emailverification_subheading'] = 'Email Verification';
$string['institution_subheading'] = 'School Information';
$string['safeguardingreview_subheading'] = 'Safeguarding Review';
$string['assurance_subheading'] = 'Safeguarding Assurance';
$string['quals_subheading'] = 'Quals Information';
$string['students_subheading'] = 'Enrol Students';
$string['tracking_subheading'] = 'Track Application Progress';
$string['courses_subheading'] = 'Request Courses';
$string['students_subheading'] = 'Enrol Students';

// Input Labels
$string['applicant_current_role'] = 'Current role';
$string['applicant_email'] = 'School email';
$string['applicant_firstname'] = 'First name';
$string['applicant_familyname'] = 'Family name';
$string['applicant_username'] = 'Username';
$string['applicant_password'] = 'Password';
$string['verification_code'] = 'Verification code';
$string['assurance_code'] ='Assurance code:';
$string['employee_work_email'] = 'Employee school email:';

$string['assurance_form'] = '<strong>Upload Form:</strong>';

// Assurance Form Labels
$string['assurance_form_title'] = 'Reference in Respect of ';
$string['assurance_form_instructions'] = 'PLEASE COMPLETE THIS ONLINE FORM, PRINT IT ON YOUR INSTITUTION\'S LETTERHEAD, THEN SIGN, SCAN & UPLOAD IT VIA THE UPLOAD FEATURE BELOW.';
$string['form_firstname'] = 'Applicant First Name';
$string['form_familyname'] = 'Applicant Surname';

$string['employment_start_date'] = 'Applicant\'s employment with you began:';
$string['employment_end'] = 'Employment with you continues:';
$string['applicant_is_employed'] = 'Is applicant your employee and is that employment on-going?';
$string['job_title'] = 'Applicant\'s most recent job title with you:';
$string['main_duties'] = 'Main duties & responsibilities of the post:';
$string['how_long_employee_known'] = 'How long have you known the applicant?';
$string['capacity_employee_known'] = 'In what capacity do you know them?';
$string['dbs_cert_date'] = 'Applicant DBS Certificate Date';
$string['dbsnumber'] = 'DBS Number<br><span>(12 Char):</span>';
$string['applicant_suitability'] = 'Referring to the supervisory role of applicant, if they were granted access to use the UKfilmNet online resources in the instruction of your students, do you believe the applicant is suitable for such access including whether they are suitable to work with children/vulnerable adults and that they have not been prohibited from doing so?';
$string['qts_qualified'] = 'Are you satisfied that the applicant is TRN (previously known as a QTS, GTC, DfE, DfES or DCSF number) Qualified (and has a clear Enhanced DBS certificate) to work with children/vulnerable adults?';
$string['qtsnumber'] = 'TRN Number:';
$string['behavior_allegations'] = 'To the best of your knowledge, has the applicant ever had an allegation made against them, which was founded, in regard to his or her behavior towards children?';
$string['disciplinary_actions'] = 'Has the applicant been the subject of disciplinary action for which penalties or sanctions remain in force?';
$string['tra_check'] = 'If the applicant was appointed after 2/9/13 have you conducted a check that confirms they are not on the Teaching Regulation Agency (previously NCTL) Prohibited List?';
$string['subject_to_ocr_check'] = 'If the applicant is a Tier 2 skilled worker who has lived abroad for 12 months or more in the last 10 years can you confirm they have provided you with an overseas criminal record certificate?';
$string['brit_school_abroad_mod_or_dubai_school'] = 'Is Your Organisation a British School Abroad, an M.O.D school, or a Dubai School?';
$string['school_subject_to_inspection'] = 'Is Your Organisation subject to inspections by a Recognised Inspection Provider as specifically defined in Note 1 below? (Note 1)';
$string['about_referee'] = 'ABOUT YOU (THE REFEREE)';
$string['referee_firstname'] = 'First Name:';
$string['referee_familyname'] = 'Surname:';
$string['referee_position'] = 'Position:';
$string['referee_email'] = 'Email:';
$string['school_registered_address'] = 'Organisation<br><span>Registered Address:</span>';
$string['school_web_address'] = 'Organisation<br><span>Web Address:</span>';
$string['school_name'] = 'Organisation Name:';
$string['ukprn_number'] = ' UKPRN Number<br><span>(Note 2):</span>';
$string['note_1'] = '<strong>Note 1:</strong> In England: this means Ofsted, in Wales this is Welsh ESTYN, in Scotland it is Education Scotland, in Northern Ireland it is the Department of Education, for Overseas Schools this means and Inspection Provider that has been inspected and approved by a one of the UK\'s Department For Education\'s list of Recognised Ofted Inspectorates deemed fit for purpose. <a href="https://ukfilmnet.org/welcome/docs/overseas-inspection.pdf">overseas-inspection.pdf</a>';
$string['note_2'] = '<strong>Note 2:</strong> UKPRN Number can be located on DFE "G.I.A.S" using <a href="https://www.gov.uk/guidance/get-information-about-schools">get-information-about-schools</a>';

// Request Courses Questions Text
$string['total_courses_question'] = 
'For how many distinct media/film related courses are you doing "prep work"?<br>
<br>
<strong>Important things to understand!</strong><br>
<br>
<ul>
<li class="ukfn_courses_question_bullet">
If you are teaching Year 1 and Year 2 students of a course (e.g. Media Y1 and Media Y2) concurrently, consider these to be two separate courses.
</li><br>
<li class="ukfn_courses_question_bullet">
If you are teaching a course to more than one group of students (e.g. Film Y1 taught to three distinct groups of students in different periods/blocks), consider these to be a single course.
</li>
</ul>
<br>
<strong>Examples:</strong><br>
<br>
<ul>
<li class="ukfn_courses_question_bullet">
Emily is doing "prep work" for Media Y1, Media Y2, and Film Y1. Emily teaches the Film Y1 course to 4 distinct groups of students in different periods/blocks. For UKfilmnet purposes, Emily is doing "prep work" for 3 courses.
</li><br>
<li class="ukfn_courses_question_bullet">
Oliver is doing "prep work" for Media Y1, and Film Y2. Oliver teaches the Media Y1 course to 3 distinct groups of students in different periods/blocks and the Film Y2 course to 3 distinct groups of students in different periods/blocks. For UKfilmnet purposes, Emily is doing "prep work" for 2 courses.
</li>
</ul>';

// Instructions Text
$string['applicant_instructions'] = 
'Welcome to the UKfilmNet Signup process.<br>
<br>
Before providing the information requested below, please take time to <strong><a href="https://www.ukfilmnet.org//UKfilmNet_gdpr.html" target="_blank">read the UKfilmNet PRIVACY NOTICE</a></strong>. Then input your current educational role, school email address, first name, and family name.';

$string['applicant_agreement'] = 
'<br>
<u>Important notice!</u> When you click on the "Submit" button below you are verifying that you have read, understand, and agree to our PRIVACY NOTICE, and that the information you are providing is correct to the best of your knowledge.<br>
<br>
After you click "Submit" we will email login and email verification information to the email address you have entered above. It is very important that it is your school email. If you do not see the email we sent, please make sure to check your "spam" email folder. If you find our email there, please let your browser know that emails from us are not spam!<br>
<br>';


$string['emailverify_instructions'] = 
'Thank you for submitting your basic UKfilmNet account creation information.<br>
<br>
Before we continue the Signup process, we need to verify that the email address you provided is valid. To that end, we have sent an email to you at the email address you provided.<br>
<br>
In that email we have provided you with:
<ul>
<li>your UKfilmNet username</li>
<li>your UKfilmNet password</li>
<li>a 6 digit Verification code</li>
</ul>
Please enter your UKfilmNet username, UKfilmNet password, and 6 digit Verification code below and click on the "Submit" button to log in, verify your email address, and continue the signup process.<br>
<br>';

$string['school_instructions'] = 
'In keeping with governmental requirements for student safeguarding, please provide the information requested below. You must also authorize UKfilmNet to contact your employing school directly to confirm the information you are providing.<br>
<br>';
$string['contact_info_label'] = '<h5 class="ukfn_contact_info_label">School Safeguarding Officer Information</h5>';
$string['contact_email'] = 'School email';
$string['contact_firstname'] = 'First name';
$string['contact_familyname'] = 'Family name';
$string['contact_phone'] = 'Phone number';
$string['consent_to_contact'] = '<div class="ukfn_consent_checkbox_label">I understand that by checking this box and clicking on "Submit" below <strong>I authorize UKfilmNet to contact my employer</strong> to verify the information I am submitting</div>';

$string['safeguarding_instructions'] = 
'Congratualtions on your UKfilmnet Signup progress!<br>
<br>
An email has been sent to the Safeguarding Officer at your school with a request that the officer verify your employment and the status of your QTS qualifications. You may wish to confirm with your Safeguarding Officer that they have received our email, and that they are in the process of providing UKfilmNet with the Assurance/Reference information we need to approve your application.<br>
<br>
When we have received and reviewed the Assurance/Reference infomation we requested from your Safeguarding Officer, we will email you to let you know if your application has been approved.';

$string['assurance_login_instructions'] = 
'Thank you for responding to our email requesting that you provde safeguarding assurances with reference to your employee.<br>
<br>
To complete the Safeguarding Assurance Form relating to your employee, please <strong>enter the Employee School Email address and Assurance Code we provided you via email</strong>.<br>
<br>';

$string['assurance_form_instructions'] = '
Please <strong>read and complete</strong> the Reference Form below.<br>
<br>';

$string['assurance_print_upload_instructions'] = '
<strong>WHAT YOU NEED TO DO NOW:</strong>
<ul>
<li><strong>CHECK</strong> the information you have provided is correct</li>
<li><strong>PRINT</strong> this page</li>
<li><strong>SIGN and DATE</strong> the printed page where is says "Signature of Referee" (blue or black ink)</li>
<li><strong>SCAN (or smartphone photography)</strong> the signed print-out</li>
<li><strong>UPLOAD</strong> your scanned, signed form below</li>
<li><strong>CLICK</strong> the "Submit" button below</li>
</ul>
<br>';

$string['tracking_instructions'] = 'Some tracking instructions may go here.';
$string['courses_instructions'] = 
'Welcome to the UKfilmNet learning community!<br>
<br>
You now have access to all of our digital text books (e.g. Cinematography, Camera Concepts, Location Sound), our Video User Guide, our Teachers & Trainers Chat, and your very own Private Teaching Space where you can create materials, hold discussions and forums with your students, set and collect  student tasks, and paste links to topics from our digital text books to support student learning.<br>
<br>
Before you enrol you students, however, we must determine whether you need more than one Private Teaching Space. Please help us by answering the following question. When you have finished, click the "Submit" button below.<br>
<br>';
$string['students_instructions'] = 
'Use this page to enrol and unenrol your student to and from your UKfilmNet hosted digital classrooms. You may return to this page to adjust your student enrolments at any time.<br>
<br>
<strong>To enrol a student</strong> - enter their school email address, first name, family name (all three are required), and click the checkbox(es) for the courses in which that student should be enroled. When you click the "Submit" button all the student enrolment information you have entered will be saved, and you will be shown an updated list of all your enroled students and five fresh lines of input boxes for enroling more students. Remember, you may ONLY ENROL YOUR OWN STUDENTS. <br>
<br>
<strong>To unenrol a student</strong> - uncheck the course checkbox of that student for that course and click the "Submit" button. If all course checkboxes of a student are unchecked, that student will no longer appear in your enrolments list after you click "Submit."<br>
<br>
Your school Safeguarding Officer is able to view your UKfilmNet Enrol Students page at all times.<br>
<br>';

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

// Application Progress Descriptors
$string['signup_progress_1'] = '1-Application denied';
$string['signup_progress_2'] = '2-Application submitted';
$string['signup_progress_3'] = '3-Email verification submitted';
$string['signup_progress_4'] = '4-Courses requested';
$string['signup_progress_5'] = '5-School information submitted';
$string['signup_progress_6'] = '6-Waiting for determination';
$string['signup_progress_7'] = '7-Application approved';

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

// Student Discriptors
$string['student_email'] = 'Student email';
$string['student_firstname'] = 'Student first name';
$string['student_familyname'] = 'Student family name';

// Button Labels
$string['button_next'] = 'Next';
$string['button_submit'] = 'Submit';
$string['button_exitsignup'] = 'Exit Signup';
$string['button_cancel'] = 'Cancel';
$string['button_exit'] = 'Exit';
$string['button_login'] = 'Login';

// Help Messages
$string['qtsnumber_help'] = 'Please note that you need to enter your number without “/” or “RP”. For example, if your number is “RP 99/12345”, just enter “9912345”. If your number is “68/12345” just enter “6812345”. If you don’t know what your teacher reference number is, this can usually be found on your payslip or teachers’ pension documentation; alternatively, please contact the DfE on QTS.enquiries@education.gsi.gov.uk or 0207 593 5394.';
$string['dbsnumber_help'] = 'The 12-digit DBS certificate number can be found on the top right-hand side of your DBS certificate. If you forget your unique subscription ID number, call DBS on 03000 200 190';

// Error Messages
$string['error_missing_role'] = 'Missing current role';
$string['error_missing_email'] = 'Missing school email address';
$string['error_missing_firstname'] = 'Missing first name';
$string['error_missing_familyname'] = 'Missing family name';
$string['error_role_limits'] = 'Sorry, but at this time ukfilmnet is only available to applicants in roles 1-3.';
$string['error_invalid_email'] = 'You must enter a VALID email address';
$string['error_existing_email'] = 'A user account with this email already exists at UKfilmNet. Make sure you are using your school email!';
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
$string['error_missing_assurance_code'] = 'Missing Assurance code';
$string['error_assurance_code_mismatch'] = 'The Assurance code isn\'t for this Employee email';
$string['error_missing_employee_work_email'] = 'Missing employee school email';
$string['error_employee_work_email_mismatch'] = 'The Employee school email you entered is invalid';
$string['error_missing_assurance_form'] = 'You must upload the Assurance/Reference Form:';
$string['error_employee_email_assurance_code_mismatch'] = 'The Employee email isn\'t for this Assurance code';
$string['error_assurance_already_submitted'] = 'An Assurance Form was previously submitted<br> for this employee';
$string['error_missing_student_email'] = 'Missing student email address';
$string['error_missing_student_firstname'] ='Missing student first name';
$string['error_missing_student_familyname'] = 'Missing student family name';
$string['error_missing_schoolname'] = 'Missing school name';

$string['error_missing_start_date'] ='Missing start date';
//$string['error_missing_to_date'] ='Missing to date';

$string['error_missing_job_title'] ='Missing job title';
$string['error_missing_main_duties'] ='Missing main duties';
$string['error_missing_time_known'] ='Missing time known';
$string['error_missing_capacity_known'] ='Missing capacity known';
$string['error_missing_dbs_date'] ='Missing DBS date';
$string['error_missing_dbs_number'] ='Missing DBS number';
$string['error_missing_suitable_to_work'] ='Missing suitable to work';
$string['error_missing_qts_qualified'] ='Missing QTS qualified';
$string['error_missing_qtsnumber'] = 'Missing QTS number';
$string['error_missing_founded_allegations'] ='Missing founded allegations';
$string['error_missing_disciplinary_action'] ='Missing disciplinary action';
$string['error_missing_prohibited_list'] ='Missing prohibited list';
$string['error_missing_ocr_certificate'] ='Missing OCR certificate';
$string['error_missing_school_type'] ='Missing school type';
$string['error_missing_subject_to_inspections'] ='Missing subject to RIP';
$string['error_missing_referee_firstname'] ='Missing referee\'s first name';
$string['error_missing_referee_familyname'] ='Missing referee\'s family name';
$string['error_missing_referee_position'] ='Missing referee\'s position';
$string['error_missing_referee_email'] ='Missing referee\'s email';
$string['error_missing_organisation_address'] ='Missing orgainisation address';
$string['error_missing_ukprn_number'] ='Missing UKPRN number';
$string['error_missing_total_courses_questions'] = 'Please answer this question';
$string['error_total_courses_excessive'] = 'The maximum number allowed here is 5';
$string['error_yes_or_no'] = 'Select one checkbox';
$string['error_length_of_12'] = 'The number must be 12 characters long';
$string['error_future_dbs_cert_date'] = 'The DBS certification date must not be in the future';
$string['error_dbs_after_employment_start'] = 'This date must be after the DBS Certificate date';

// String variables
$string['roleallowed_range_max'] = '03';
$string['template_course'] = 'your-class';
$string['course_category'] = 'CLASSROOMS';
$string['misc_course_category'] = 'CLASSROOMS';
$string['student_role_id'] = '5';
$string['max_courses_allowed'] = '5';
$string['number_of_enrol_table_rows'] = '5';

// Email strings
$string['verification_subject'] = 'UKfilmNet Email Verification';
$string['verification_text'] = '
Thank you for requesting a UKfilmNet account. As a part of the signup process, we are providing you with the following important items:
<ul>
<li>UKfilmNet Username: <strong>{$a->username}</strong></li>
<li>UKfilmNet Password: <strong>{$a->password}</strong></li>
<li>your Email Verification Code: <strong>{$a->code}</strong></li>
</ul>
Please return to the UKfilmNet website and click on the "Return to Sign-up" button located in the left hand column of the site front page. If you do not see a left hand column on the page when you browse to the UKfilmNet website, click on the large gray button located on the left side of the black title bar at the top of the page.<br>
<br>
When you click on the "Return to Sign-up button, you should be taken to a signup page titled Applicant Email Verification. Input your username, password, and verification code, then click "Submit" to continue the UKfilmNet Signup process.<br>
<br>
Your Friends at UKfilmNet';
$string['assurance_subject'] = 'Reference in Respect of {$a->applicant_firstname} {$a->applicant_familyname}';
$string['assurance_text'] = '
{$a->schoolname}<br>
{$a->contact_firstname} {$a->contact_familyname}<br>
<br>
<strong>Dear {$a->contact_firstname} {$a->contact_familyname},<br>
<br>
RE: Reference In Respect Of {$a->applicant_firstname} {$a->applicant_familyname}</strong><br>
<br>
One of your employees has requested access to UKfilmNet – which is a learning resource aimed at better supporting teacher/lecturer of media and film. The resource has been created by former and current teacher/lecturer, university staff and industry experts in film and media.<br>
<br>
<strong>What have they requested access to?</strong><br>
<br>
The online platform uses Moodle to create learning spaces which give teacher/lecturer and lecturers access to the students where they currently teach at {$a->schoolname}. It allows them to view hundreds of master-classes co-created with the BBC, Channel4, Sky News, NBC, and hundreds of experts from UK and US higher education, centres of excellence and broadcasters.<br>
<br>
The learning space allows your employee to set quizzes, share resources, create forums and promote communal learning, discovering and discussion in the craft of film and media.<br>
<br>
For reasons of safeguarding, your employee will not have access to students other than those from your organisation they currently teach. They will however be able to ask questions, seek advice and get help from hundreds of other teacher/lecturer of the same subject using the online resource and its virtual staff-room and forums. Likewise, those teachers/lecturers will not have access to students from other schools. This again is for reasons of safeguarding.<br>
<br>
<strong>Safeguarding Request</strong><br>
<br>
In order to grant access to {$a->applicant_firstname} {$a->applicant_familyname} we are required to take reasonable steps to confirm that the individual is who they say they are, and that they are employed by you. Please could either yourself or your designated Safeguarding lead complete the following process:
<ul>

<li>Browse to <a href="{$a->assurance_url}">UKfilmNet Safeguarding Assurance</a></li>
<li>Enter the Email Address of your employee - {$a->applicant_email}</li>
<li>Enter the following Assurance Code - {$a->assurance_code}</li>
<li>Follow the instructions for completing and submitting a Reference form on behalf of your employee</li>
</ul>
Once you have completed these steps we can consider the application your employee has made for access to the resource and other professional colleagues in developing their CPD, skills and knowledge.<br>
<br>
Please note that as part of the process you may receive a reminder of this request, but please note that after 4 weeks, the application from <strong>{$a->applicant_firstname} {$a->applicant_familyname}</strong> will automatically be deleted if the reference has not been received. <strong>As part of the requirements for access to the resource, your employee’s account details will be shared by email with the organisational head or designated Safeguarding lead, to ensure additional supervision and transparency of online activity conducted by the employee and his/her students.</strong><br>
<br>
If you have any queries, please email <a href="emailto:safeguarding@ukfilmnet.org">safeguarding@ukfilmnet.org</a> where we hope to address any further questions.<br>
<br>
Yours Sincerely,<br>
<br>
Dom Foulsham<br>
Director of Curriculum | UKfilmNet.org';
$string['determination_subject'] = 'UKfilmNet Account Request Determination';
$string['determination_text_approved'] = '
Dear {$a->firstname},<br>
<br>
We are happy to inform you that your request for a UKfilmNet account has been review and granted!<br>
<br>
When you log in to UKfilmNet you should find that you are able to access the UKfilmNet resource classes as well as the support and educator forum classes. Feel free to browse them.<br>
<br>
To set up your personal teaching classes and enrol your students, please return to the <a href="{$a->students_url}">UKfilmNet Student Enrolment</a> signup page.<br>
<br>
Enjoy!<br>
<br>
Your Friends at UKfilmNet';
$string['determination_text_denied'] = '
Dear {$a->firstname},<br>
<br>
After review of the information provided by your school Safeguarding Officer, we regret to inform you that we are unable to grant your request for a UKfilmNet account.<br>
<br>
Other stuff to be determined.<br>
<br>
Your Friends at UKfilmNet';
$string['determination_text_tester'] = 'This is an email test - hope you got it!';