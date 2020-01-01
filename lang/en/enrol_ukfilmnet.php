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
 * UkFilmNet theme.
 *
 * @package    enrol_ukfilmnet
 * @copyright  2019, Doug Loomer 
 * @author     Doug Loomer doug@dougloomer.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

/*$string['choosereadme'] = '
<div class="clearfix">
<h2>UkFilmNet</h2>
<h3>About</h3>
<p>UkFilmNet is a child theme of the Boost theme.</p>
<h3>Theme Credits</h3>
<p>Author: G J Barnard<br>
Contact: <a href="http://moodle.org/user/profile.php?id=442195">Moodle profile</a><br>
Website: <a href="http://about.me/gjbarnard">about.me/gjbarnard</a>
</p>
<h3>More information</h3>
<p><a href="ukfilmnet/Readme.md">How to use this theme.</a></p>
</div></div>';

$string['configtitle'] = 'UkFilmNet';
$string['pluginname'] = 'UkFilmNet';

$string['region-side-pre'] = 'Left';
$string['region-side-nav'] = 'Nav';

// Misc.
$string['backtotop'] = 'Back to top';
$string['enter'] = 'Enter';
$string['example'] = 'Example';
$string['gotobottom'] = 'Go to the bottom of the page';
$string['privacypolicy'] = 'Privacy policy';
$string['sitepolicy'] = 'Site policy';
$string['stylecover'] = 'Cover';
$string['stylestretch'] = 'Stretch';

// General.
$string['generalheading'] = 'General';
$string['generalheadingsub'] = 'General settings';
$string['generalheadingdesc'] = 'Configure the general settings for UkFilmNet here.';

$string['privacypolicydesc'] = 'Set the URL for the privacy policy.';
$string['sitepolicydesc'] = 'Set the URL for the site policy.';

$string['customcss'] = 'Custom CSS';
$string['customcssdesc'] = 'Add custom CSS to the theme.';

// Frontpage Carousel.
$string['frontpagecarouselheading'] = 'Frontpage carousel';
$string['frontpagecarouselheadingsub'] = 'Frontpage carousel settings';
$string['frontpagecarouselheadingdesc'] = 'Configure the settings for the frontpage carousel of UkFilmNet here.';
$string['frontpagecarouselslides'] = 'Frontpage slides';
$string['frontpagecarouselslidesdesc'] = 'Number of frontpage slides between {$a->lower} and {$a->upper}.  After changing and \'Saving changes\', refresh the page.';
$string['frontpagecarouselinterval'] = 'Frontpage slide interval';
$string['frontpagecarouselintervaldesc'] = 'Number of milliseconds between slides, choose between {$a->lower} and {$a->upper} milliseconds.';
$string['frontpageslideno'] = 'Frontpage slide {$a->number}';
$string['frontpageslidenodesc'] = 'Enter the settings for frontpage slide {$a->number}.';
$string['frontpageenableslide'] = 'Frontpage slide {$a->number} enable';
$string['frontpageenableslidedesc'] = 'Enable or disable frontpage slide {$a->number}.';
$string['frontpageslidetitle'] = 'Frontpage slide {$a->number} title';
$string['frontpageslidetitledesc'] = 'Title for frontpage slide {$a->number}.';
$string['frontpageslidecaption'] = 'Frontpage slide {$a->number} caption';
$string['frontpageslidecaptiondesc'] = 'Caption for frontpage slide {$a->number}.';
$string['frontpageslideimage'] = 'Frontpage slide {$a->number} image';
$string['frontpageslideimagedesc'] = 'Image for frontpage slide {$a->number}.';
$string['frontpageslideimagetext'] = 'Frontpage slide {$a->number} image text';
$string['frontpageslideimagetextdesc'] = 'Image alternative description text for frontpage slide {$a->number}.';

// Login background.
$string['loginbackgroundheading'] = 'Login background';
$string['loginbackgroundheadingsub'] = 'Login background image settings';
$string['loginbackgroundheadingdesc'] = 'Set the login background image settings.  Note: The login form will only be the theme\'s when an image has been set.';
$string['loginbackground'] = 'Login background image';
$string['loginbackgrounddesc'] = 'Upload your own login background image.  Select the style of the image below.';
$string['loginbackgroundstyle'] = 'Login background style';
$string['loginbackgroundstyledesc'] = 'Select the style for the uploaded image.';
$string['loginbackgroundopacity'] = 'Login box background opacity when there is a background image';
$string['loginbackgroundopacitydesc'] = 'Login background opacity for the login box when there is a background image.';

// Information.
$string['informationheading'] = 'Information';
$string['informationheadingsub'] = 'Information settings page';
$string['informationheadingdesc'] = 'Information about the theme and details on configuration information for the site.';
$string['themecustomstylesheading'] = 'Styles';
$string['themecustomstylesheadingdesc'] = 'Custom theme styles.';
$string['themetinymceheading'] = 'TinyMCE';
$string['themetinymceheadingdesc'] = 'TinyMCE configuration to use.  Note: Add / copy the values to the given setting names.';
$string['themecoursesheading'] = 'Courses';
$string['themecoursesheadingdesc'] = 'Courses configuration to use.  Note: Add / change / copy the values to the given setting names.';

// ukfilmnet_admin_setting_configinteger.
$string['asconfigintlower'] = '{$a->value} is less than the lower range limit of {$a->lower}';
$string['asconfigintupper'] = '{$a->value} is greater than the upper range limit of {$a->upper}';
$string['asconfigintnan'] = '{$a->value} is not a number';

// Privacy.
$string['privacy:nop'] = 'The UkFilmNet theme stores has settings that pertain to its configuration.  It also may inherit settings and user preferences from the parent Boost theme, please examine the \'Plugin privacy compliance registry\' for \'Boost\' for details.  For the settings, it is your responsibility to ensure that no user data is entered in any of the free text fields.  Setting a setting will result in that action being logged within the core Moodle logging system against the user whom changed it, this is outside of the themes control, please see the core logging system for privacy compliance for this.  When uploading images, you should avoid uploading images with embedded location data (EXIF GPS) included or other such personal data.  It would be possible to extract any location / personal data from the images.  Please examine the code carefully to be sure that it complies with your interpretation of your privacy laws.  I am not a lawyer and my analysis is based on my interpretation.  If you have any doubt then remove the theme forthwith.';
*/