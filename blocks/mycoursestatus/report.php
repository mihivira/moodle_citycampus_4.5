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

$PAGE->set_context(get_system_context(CONTEXT_COURSE));
$PAGE->set_title($header);
$PAGE->set_heading($header);

/* Breadcrumbs or navbar */
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$thingnode = $coursenode->add(get_string('report', 'block_mycoursestatus'));
$thingnode->make_active();

echo $OUTPUT->header();
$modreport = new module($CFG, $DB);
$imga = '<img src="'.$CFG->wwwroot.'/blocks/mycoursestatus/pix/arrow-menu.gif" style="padding:0px 4px 3px 0px;"/>';
echo html_writer::div('Fullname : <b>'.$USER->firstname.'&nbsp;'.$USER->lastname.'</b>', 'report');
echo html_writer::div('Course :'.'&nbsp;'. $course->fullname, 'report');
echo html_writer::div($imga. get_string('ccond', 'block_mycoursestatus'), 'report');

if ($modreport->course_conds()) {
    echo html_writer::div($imga. get_string('cconda', 'block_mycoursestatus'), 'condA');
    echo html_writer::div($modreport->course_conds(), 'condB');
} else {
         echo html_writer::div($imga. get_string('cconda', 'block_mycoursestatus'), 'condA');
}

echo html_writer::div($imga. get_string('ccondg', 'block_mycoursestatus').$modreport->course_grade(), 'condA');

if ($modreport->self_mod()) {
    echo html_writer::div($imga. get_string('cconds', 'block_mycoursestatus'), 'condA');
    echo html_writer::div($modreport->self_mod(), 'condB');
} else {
         echo html_writer::div($imga. get_string('cconds', 'block_mycoursestatus'), 'condA');
}

echo html_writer::div($imga. get_string('cmod', 'block_mycoursestatus'), 'report');
echo html_writer::div($imga. get_string('cmod_grade', 'block_mycoursestatus'), 'condA');

$gradetable = new html_table();
$gradetable->head = array('Modules', 'Grade points');
$gradetable->attributes['class'] = 'mytable';
$graded = $DB->get_recordset_sql('SELECT gi.itemname,
                                         gg.finalgrade
                                  FROM {grade_items} gi JOIN {grade_grades} gg ON gi.id = gg.itemid
                                  WHERE gi.itemtype IN ("mod")
                                  AND gg.finalgrade IS NOT NULL
                                  AND gi.courseid = '.$courseid.'
                                  AND gg.userid = '.$USER->id.'');
foreach ($graded as $gd) {
    $gradetable->data[] = new html_table_row(array(implode(array($gd->itemname)), '<center>'.$gd->finalgrade.'</center>'));
}
echo html_writer::table($gradetable);
echo html_writer::div($imga. get_string('cmod_notgrade', 'block_mycoursestatus'), 'condA');
$notgradetable = new html_table();
$notgradetable->head = array('Modules', 'Status');
$notgradetable->attributes['class'] = 'mytable';
$modinfo = get_fast_modinfo($courseid);
foreach ($cms = $modinfo->get_cms() as $cminfo) {
    $notgraded = $DB->get_recordset_sql('SELECT cmc.coursemoduleid
                                          FROM {course_modules_completion} cmc
                                          JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
                                          JOIN {modules} m ON m.id= cm.module
                                          WHERE cm.deletioninprogress=0
                                          AND m.id IN (1,3,8,11,12,15,17,20)
                                          AND cmc.viewed = 1
                                          AND cmc.coursemoduleid = '.$cminfo->id.'
                                          AND cmc.userid= '.$USER->id.'
                                          AND cm.course = '.$courseid.' ');
    foreach ($notgraded as $ngd) {
         $notgradetable->data[] = new html_table_row(array(implode(array($cminfo->get_formatted_name())),
                                  '<center>Viewed</center>'));
    }
}
echo html_writer::table($notgradetable);
echo html_writer::div('<a href="'.$CFG->wwwroot.'/blocks/mycoursestatus/report_pdf.php?id='.$courseid.'"
                       style="color:#008000;text-align:center;font-weight:bold;" target="_blank">Download PDF Report</a>');
echo $OUTPUT->footer();

