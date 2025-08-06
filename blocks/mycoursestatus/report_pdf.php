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
require_once('tcpdf/tcpdf.php');
global $CFG, $DB, $PAGE, $COURSE, $USER;
require_once($CFG->dirroot.'/blocks/mycoursestatus/classes/module.php');
$PAGE->requires->css('/blocks/mycoursestatus/styles.css');

$courseid = required_param('id', PARAM_INT);
$PAGE->set_url(new moodle_url('/blocks/mycoursestatus/report_pdf.php', array('id' => $courseid)));

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
    $context = context_system::instance();
              // A:get_context_instance(CONTEXT_SYSTEM) turns into context_system::instance().
    $PAGE->set_blocks_editing_capability('moodle/my:configsyspages');
    $header = $course->fullname;
} else {
    /* We are trying to view or edit our own My Moodle page i.e., admin part.*/
    $userid = $USER->id;
    $context = get_context_instance(CONTEXT_USER, $USER->id);
    $PAGE->set_blocks_editing_capability('moodle/my:manageblocks');
    $header = $course->fullname;
}

$pdf = new TCPDF;
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$table = "";
$pdf->setPageOrientation($orientation = 'L', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8',
                         $diskcache = false, $pdfa = false);
$pdf->AddPage();

$banner = '<img src="pix/banner.jpg" />';
$pdf->writeHTML($banner, true, false, false, false, '');

$heading = '<div style="text-align:center;font-size:1em;color:#084b8a;">Grade Report</div>';
$pdf->writeHTML($heading, true, false, false, false, '');

$table .= '<ul></ul>';

$table .= '<ul>
              <table style="font-size:0.85em;" cellpadding="5px">
                <tr><td width="70%">Name : '.$USER->firstname.'&nbsp;'.$USER->lastname.'</td>
                    <td width="30%">Course : '.$course->fullname.' </td></tr>
                <tr><td width="70%">Email : '.$USER->email.'</td>
                    <td width="30%">Shortname : '.$course->shortname.'</td></tr>
              </table>
           </ul>';

$imga = '<img src="'.$CFG->wwwroot.'/blocks/mycoursestatus/pix/arrow-menu.gif" style="width:8px;height:8px;"/>';
$modreport = new module($CFG, $DB);
$table .= '<ul>
             <table style="font-size:0.85em;" cellpadding="5px">
              <tr><td>'. get_string('ccond', 'block_mycoursestatus').'</td></tr>';
if ($modreport->course_conds()) {
                      $table .= '<tr><td>'.$imga. '&nbsp;'. get_string('cconda', 'block_mycoursestatus').'</td></tr>
                                 <tr><td>'. $modreport->course_conds().'</td></tr>';
} else {
    $table .= '<tr><td>'.$imga.'&nbsp;'. get_string('cconda', 'block_mycoursestatus').'</td></tr>';
}
$table .= '<tr><td>'.$imga. '&nbsp;'.get_string('ccondg', 'block_mycoursestatus'). '<b>'.$modreport->course_grade().
           '</b></td></tr>';
if ($modreport->self_mod()) {
                      $table .= '<tr><td>'.$imga. '&nbsp;'. get_string('cconds', 'block_mycoursestatus').'</td></tr>
                                 <tr><td>'. $modreport->self_mod().'</td></tr>';
} else {
      $table .= '<tr><td>'.$imga. '&nbsp;'. get_string('cconds', 'block_mycoursestatus').'</td></tr>';
}

               $table .= '<tr><td>'.$imga. '&nbsp;'. get_string('cmod', 'block_mycoursestatus').'</td></tr>
                          <tr><td>'.$imga. '&nbsp;'. get_string('cmod_grade', 'block_mycoursestatus').'</td></tr>
            </table>';

// Activity Modules.
$table .= '<table style="border:1px solid #dee2e6;font-size:0.90em;" cellpadding="10px">
              <tr style="text-align:center;background-color:#dee2e6;font-weight:bold;">
                <th style="text-align:center;width:50%;">Modules</th>
                <th style="text-align:center;width:50%;">Grade points</th>
              </tr>';

                    $graded = $DB->get_recordset_sql('SELECT gi.itemname,
                                                     gg.finalgrade
                                                     FROM {grade_items} gi JOIN {grade_grades} gg ON gi.id = gg.itemid
                                                     WHERE gi.itemtype IN ("mod")
                                                     AND gg.finalgrade IS NOT NULL
                                                     AND gi.courseid = '.$courseid.'
                                                     AND gg.userid = '.$USER->id.'');
                    foreach ($graded as $gd) {
                        $table .= '<tr style="font-size:0.90em;">
                                    <td style="text-align:left;border:1px solid #dee2e6;width:50%;">'.$gd->itemname.'</td>
                                    <td style="text-align:center;border:1px solid #dee2e6;width:50%;">'.$gd->finalgrade.'</td>
                                  </tr>';
                    }

                    $table .= '</table>';
                    $table .= '<table style="font-size:0.85em;" cellpadding="5px" width="100%"><tr><td class="condA">'.$imga.
                                '&nbsp;'. get_string('cmod_notgrade', 'block_mycoursestatus').'</td></tr></table>';

                    // Resource Modules.
                    $table .= '<table style="border:1px solid #dee2e6;font-size:0.90em;" cellpadding="10px">
                                <tr style="text-align:center;background-color:#dee2e6;font-weight:bold;">
                                 <th style="text-align:center;width:50%;">Modules</th>
                                 <th style="text-align:center;width:50%;">Status</th>
                                </tr>';

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
                            $table .= '<tr style="font-size:0.90em;">
                                        <td style="border:1px solid #dee2e6;width:50%;">'.$cminfo->get_formatted_name().'</td>
                                        <td style="text-align:center;border:1px solid #dee2e6;width:50%;">Viewed </td>
                                       </tr>';
                        }
                    }
                    $table .= '</table>';
                    $table .= '</ul>';
                    $pdf->setCellPaddings('4', '4', '4', 0);
                    $pdf->writeHTMLCell(0, 0, 0, 80, $table, 0, 0, false, false, 'C', true);
                    ob_end_clean();
                    $pdf->Output();
