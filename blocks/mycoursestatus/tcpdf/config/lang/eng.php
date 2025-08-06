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
 * Mycoursestatus block
 *
 * @package    block_mycoursestatus
 * @copyright  2014 Lavanya Lav
 * @license    https://github.com/lavanyamanne2/moodle-block_mycoursestatus
 */

require_once('../../config.php');
global $CFG, $DB, $PAGE, $COURSE, $USER;
require_once($CFG->dirroot.'/blocks/mycoursestatus/classes/module.php');
$PAGE->requires->css('/blocks/mycoursestatus/styles.css');

$courseid = required_param('id', PARAM_INT);
$PAGE->set_url(new moodle_url('/blocks/mycoursestatus/report.php', array('id' => $courseid)));

/* Basic access checks */
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}
require_login($courseid);

$context = context_course::instance($courseid);
require_capability('gradereport/user:view', $context);

if (isguestuser()) {
    /* Force them to see system default, no editing allowed */
    $userid = null;
    $USER->editing = $edit = 0;
    $context = context_system::instance(); // A:get_context_instance(CONTEXT_SYSTEM) turns into context_system::instance().
    $PAGE->set_blocks_editing_capability('moodle/my:configsyspages');
    $header = $course->fullname;
} else {
    /* We are trying to view or edit our own My Moodle page i.e., admin part.*/
    $userid = $USER->id;
    $context = get_context_instance(CONTEXT_USER, $USER->id);
    $PAGE->set_blocks_editing_capability('moodle/my:manageblocks');
    $header = $course->fullname;
}

// English.
global $l;
$l = Array();

// PAGE META DESCRIPTORS.

$l['a_meta_charset'] = 'UTF-8';
$l['a_meta_dir'] = 'ltr';
$l['a_meta_language'] = 'en';

// TRANSLATIONS.
$l['w_page'] = 'page';
