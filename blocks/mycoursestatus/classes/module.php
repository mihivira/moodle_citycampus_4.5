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

defined('MOODLE_INTERNAL') || die();
global $CFG, $DB, $PAGE, $COURSE, $USER;
require_once($CFG->dirroot . '/config.php');
require_login();

/**
 * Intial class module to define all the functions.
 */
class module
{
    /**
     * $cfg is a private variable for class module.
     */
    /**
     * $db is a private variable for class module.
     */
    private $cfg, $db;

    /**
     * method name to call an object.
     */
    public function __construct($cfg, $db) {
        $this->cfg = $cfg;
        $this->db = $db;
    }

    /**
     * Condition A => Condiiton: Activity Completion.
     *
     * @return object $outcome.
     */
    public function incourse_actcomp() {
        global $COURSE, $USER;
        $coua = html_writer::start_tag('div', array('class' => 'c1')).
                html_writer::div(get_string('cname', 'block_mycoursestatus'), 'c2').
                html_writer::div($COURSE->fullname, 'c3').
                html_writer::end_tag('div', array('class' => 'c1'));
        $critmodcompmod = $this->db->get_recordset_sql('SELECT cc.course,
                                                       (SELECT count(moduleinstance)
                                                        FROM {course_completion_criteria}
                                                        WHERE criteriatype=4
                                                        AND course=cc.course) AS "criteriamods",
                                                       (SELECT count(cmc.coursemoduleid)
                                                        FROM {course_modules_completion} cmc
                                                        JOIN {course_completion_criteria} ccc ON
                                                              cmc.coursemoduleid = ccc.moduleinstance
                                                        WHERE ccc.criteriatype=4
                                                        AND cmc.userid='.$USER->id.'
                                                        AND ccc.course = cc.course
                                                        AND cmc.completionstate BETWEEN 1 AND 3) AS "completedmods"
                                                        FROM {course_completion_criteria} cc
                                                        WHERE cc.criteriatype=4
                                                        AND cc.course='.$COURSE->id.'
                                                        GROUP BY course');
        foreach ($critmodcompmod as $cmcm) {
            $width = round(($cmcm->completedmods / $cmcm->criteriamods) * 100);
            $outcome = '<div class="m1">
                          <div class="m2">Modules:</div>
                          <div class="m3"><div class="mydiv2" style="width:'.$width.'%">'.$width.'% </div></div>
                        </div>
                        <div class="m1">
                          <div class="m2"></div>
                          <div class="out">'.$cmcm->completedmods.' out of '.$cmcm->criteriamods.' </div>
                        </div>';

            if ($cmcm->completedmods == $cmcm->criteriamods) {
                $completed = html_writer::start_tag('div', array('class' => 'm1')).
                             html_writer::div('', 'm2').
                             html_writer::div('Completed', 'completed').
                             html_writer::end_tag('div', array('class' => 'm1'));

                $viewreport = html_writer::start_tag('div', array('class' => 'm1')).
                              html_writer::div('', 'm2').
                              html_writer::div('<a
                              href="'.$this->cfg->wwwroot.'/blocks/mycoursestatus/report.php?id='.$COURSE->id.'" target="_blank">
                              View Report</a>', 'vreport').
                              html_writer::end_tag('div', array('class' => 'm1'));
            }
        }
        return  $coua.$outcome.$outof.$completed.$viewreport;
    }

    /**
     * Condition B => Course grade.
     *
     * @return object $outcome.
     */
    public function incourse_coursegr() {
        global $COURSE, $USER;
        $coua = html_writer::start_tag('div', array('class' => 'cc1')).
                html_writer::div(get_string('cname', 'block_mycoursestatus'), 'cc2').
                html_writer::div($COURSE->fullname, 'cc3').
                html_writer::end_tag('div', array('class' => 'cc1'));
        $critgrdcompgrd = $this->db->get_recordset_sql('SELECT c.id,
                                                       (SELECT ccc.gradepass
                                                        FROM {course_completion_criteria} ccc
                                                        WHERE ccc.criteriatype=6
                                                        AND ccc.course=c.id) AS "criteriagrade",
                                                        (SELECT gg.finalgrade
                                                        FROM {grade_grades} gg
                                                        JOIN {grade_items} gi ON gi.id = gg.itemid
                                                        JOIN {user} u ON  u.id = gg.userid
                                                        JOIN {course} c ON c.id = gi.courseid
                                                        WHERE gi.itemtype = "course"
                                                        AND gg.finalgrade IS NOT NULL
                                                        AND u.id = '.$USER->id.'
                                                        AND gi.courseid= '.$COURSE->id.') AS "coursegrade"
                                                        FROM {course} c
                                                        WHERE c.id= '.$COURSE->id.'');
        foreach ($critgrdcompgrd as $cgcg) {
            $outcomeone = '<div class="cc1">
                            <div class="cc2">Criteria Grade: </div>
                            <div class="cc3"><b>'.$cgcg->criteriagrade.'</b></div>
                           </div>';

            $outcometwo = '<div class="cc1">
                            <div class="cc2">Course Grade: </div>
                            <div class="cc3"><b>'.$cgcg->coursegrade.'</b></div>
                           </div>';

            if ($cgcg->coursegrade >= $cgcg->criteriagrade) {
                $completed = html_writer::start_tag('div', array('class' => 'm1')).
                             html_writer::div('', 'm2').
                             html_writer::div('Completed', 'completed').
                             html_writer::end_tag('div', array('class' => 'm1'));

                $viewreport = html_writer::start_tag('div', array('class' => 'm1')).
                              html_writer::div('', 'm2').
                              html_writer::div('<a
                              href="'.$this->cfg->wwwroot.'/blocks/mycoursestatus/report.php?id='.$COURSE->id.'" target="_blank">
                              View Report</a>', 'vreport').
                              html_writer::end_tag('div', array('class' => 'm1'));
            }
        }
        return $coua.$outcomeone.$outcometwo.$completed.$viewreport;
    }

    /**
     * Condiiton C => Condition: Self Completion.
     *
     * @return object $outcome.
     */
    public function incourse_self() {
        global $COURSE, $USER;
        $coua = html_writer::start_tag('div', array('class' => 'c1')).
                html_writer::div(get_string('cname', 'block_mycoursestatus'), 'c2').
                html_writer::div($COURSE->fullname, 'c3').
                html_writer::end_tag('div', array('class' => 'c1'));
        $nocond = $this->db->get_recordset_sql('SELECT c.id,
                                                (SELECT count(cm.id)
                                                FROM {course_modules} cm
                                                WHERE cm.course = c.id
                                                AND cm.deletioninprogress=0
                                                AND cm.completionview=1) AS "coursemods",
                                                (SELECT count(cmc.coursemoduleid)
                                                FROM {course_modules_completion} cmc
                                                JOIN {course_modules} cm ON cmc.coursemoduleid = cm.id
                                                WHERE cmc.userid = '.$USER->id.'
                                                AND cm.course = c.id
                                                AND cm.deletioninprogress=0
                                                AND cmc.completionstate BETWEEN 1 AND 3) AS "compmods"
                                                FROM {course} c
                                                WHERE c.id='.$COURSE->id.'');
        foreach ($nocond as $ncd) {
            $width = $width = round(($ncd->compmods / $ncd->coursemods) * 100);
            $outcome = '<div class="m1">
                          <div class="m2">Modules:</div>
                          <div class="m3"><div class="mydiv2" style="width:'.$width.'%">'.$width.'% </div></div>
                        </div>
                        <div class="m1">
                          <div class="m2"></div>
                          <div class="out">'.$ncd->compmods.' out of '.$ncd->coursemods.'</div>
                        </div>';

            if ($ncd->compmods == $ncd->coursemods) {
                $completed = html_writer::start_tag('div', array('class' => 'm1')).
                             html_writer::div('', 'm2').
                             html_writer::div('Completed', 'completed').
                             html_writer::end_tag('div', array('class' => 'm1'));

                $viewreport = html_writer::start_tag('div', array('class' => 'm1')).
                              html_writer::div('', 'm2').
                              html_writer::div('<a
                              href="'.$this->cfg->wwwroot.'/blocks/mycoursestatus/report.php?id='.$COURSE->id.'" target="_blank">
                              View Report</a>', 'vreport').
                              html_writer::end_tag('div', array('class' => 'm1'));

            }
        }
        return $coua.$outcome.$completed.$viewreport;
    }

    /**
     * Condition: Activity Completion (View Report).
     *
     * @return object $result.
     */
    public function course_conds() {
        global $COURSE;
        $modinfo = get_fast_modinfo($COURSE->id);
        foreach ($cms = $modinfo->get_cms() as $cminfo) {
            $conds = $this->db->get_records_sql('SELECT ccc.moduleinstance
                                                 FROM {course_completion_criteria} ccc JOIN {course_modules} cm
                                                         ON (cm.id = ccc.moduleinstance AND
                                                 cm.course=ccc.course)
                                                 WHERE ccc.criteriatype=4
                                                 AND ccc.course = '.$COURSE->id.'
                                                 AND cm.deletioninprogress=0
                                                 AND cm.deletioninprogress = '.$cminfo->deletioninprogress.'
                                                 AND ccc.moduleinstance='.$cminfo->id.'');
            foreach ($conds as $cn) {
                $result .= html_writer::div('<img src="'.$this->cfg->wwwroot.'/blocks/mycoursestatus/pix/more.png"
                                              style="padding:0px 4px 3px 0px;"/>'.$cminfo->get_formatted_name());
            }
        }
        return $result;
    }

    /**
     * Condition: Course grade (View Report).
     *
     * @return object $result.
     */
    public function course_grade() {
        global $COURSE;
        $grade = $this->db->get_recordset_sql('SELECT gradepass
                                               FROM {course_completion_criteria}
                                               WHERE criteriatype=6
                                               AND course = '.$COURSE->id.'');
        foreach ($grade as $g) {
            $result .= $g->gradepass;
        }
        return $result;
    }

    /**
     * Condition: Self Completion (View Report).
     *
     * @return object $result.
     */
    public function self_mod() {
        global $COURSE;
        $modinfo = get_fast_modinfo($COURSE->id);
        foreach ($cms = $modinfo->get_cms() as $cminfo) {
            $selfmod = $this->db->get_recordset_sql('SELECT DISTINCT(cm.id) AS "modid"
                                                      FROM {course_modules} cm
                                                      LEFT JOIN {course_completion_criteria} ccc ON
                                                                (cm.id = ccc.moduleinstance AND cm.course=ccc.course)
                                                      WHERE ccc.moduleinstance IS NULL
                                                      AND cm.deletioninprogress = 0
                                                      AND cm.completionview = 1
                                                      AND cm.deletioninprogress = '.$cminfo->deletioninprogress.'
                                                      AND cm.id ='.$cminfo->id.'
                                                      AND cm.course = '.$COURSE->id.'');
            foreach ($selfmod as $sm) {
                $result .= html_writer::div('<img src="'.$this->cfg->wwwroot.'/blocks/mycoursestatus/pix/more.png"
                                              style="padding:0px 4px 3px 0px;"/>'.$cminfo->get_formatted_name());
            }
        }
        return $result;
    }

    /**
     * Block condition A.
     *
     * @return object $result.
     */
    public function conda() {
        global $COURSE;
        $criteriaact = $this->db->get_records('course_completion_criteria', array('criteriatype' => '4', 'course' => $COURSE->id));
        foreach ($criteriaact as $ca) {
            $result .= $ca->moduleinstance;
        }
        return $result;
    }

    /**
     * Block condition B.
     *
     * @return object $result.
     */
    public function condb() {
        global $COURSE;
        $criteriagr = $this->db->get_records('course_completion_criteria', array('criteriatype' => '6', 'course' => $COURSE->id));
        foreach ($criteriagr as $cg) {
            $result .= $cg->course;
        }
        return $result;
    }

    /**
     * Block condition C.
     *
     * @return object $result.
     */
    public function condc() {
        global $COURSE;
        $self = $this->db->get_recordset_sql('SELECT DISTINCT(cm.id) AS "modid"
                                              FROM {course_modules} cm
                                              LEFT JOIN {course_completion_criteria} ccc ON
                                                        (cm.id = ccc.moduleinstance AND cm.course=ccc.course)
                                              WHERE ccc.moduleinstance IS NULL
                                              AND cm.deletioninprogress = 0
                                              AND cm.completionview = 1
                                              AND cm.course = '.$COURSE->id.'');
        foreach ($self as $s) {
            $result .= $s->modid;
        }
        return $result;
    }

    /**
     * Student enrolled courses.
     *
     * @return object $result.
     */
    public function enrolled_course() {
        global $COURSE, $USER;
        $encc = $this->db->get_recordset_sql('SELECT DISTINCT u.id AS userid,
                                                              c.id AS course,
                                                              c.fullname
                                              FROM {user} u
                                              JOIN {user_enrolments} ue ON ue.userid = u.id
                                              JOIN {enrol} e ON e.id = ue.enrolid
                                              JOIN {role_assignments} ra ON ra.userid = u.id
                                              JOIN {context} ct ON (ct.id = ra.contextid AND ct.contextlevel = 50)
                                              JOIN {course} c ON (c.id = ct.instanceid AND e.courseid = c.id)
                                              JOIN {role} r ON (r.id = ra.roleid AND r.id=5)
                                              WHERE e.status = 0
                                              AND u.suspended = 0 AND u.deleted = 0
                                              AND (ue.timeend = 0 OR ue.timeend > NOW())
                                              AND ue.status = 0
                                              AND u.id ='.$USER->id.'');
        foreach ($encc as $ec) {
            $result .= html_writer::div('<img src="'.$this->cfg->wwwroot.'/blocks/mycoursestatus/pix/arrow-menu.gif"
                                          style="padding:0px 4px 3px 0px;"/>'.
                                         $ec->fullname.
                                        '<a href="'.$this->cfg->wwwroot.'/blocks/mycoursestatus/report.php?id='.$ec->course.'"
                                          target="_blank" style="font-size:9px;color:#FE642E;">
                                          '.get_string('reportc', 'block_mycoursestatus').'</a>'
                                        );
        }
        return $result;
    }
}
