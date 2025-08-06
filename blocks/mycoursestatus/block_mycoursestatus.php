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
require_once('classes/module.php');

/**
 * Displays a student course module report.
 */
class block_mycoursestatus extends block_base
{
    /**
     * Set the initial properties for the block
     */
    public function init() {
        global $CFG;
        $this->title = get_string('mycoursestatus', 'block_mycoursestatus');
    }

    /**
     * Gets the content for this block by grabbing it from $this->page
     *
     * @return object $this->content
     */
    public function get_content() {
        global $CFG, $DB, $COURSE, $USER, $PAGE;
        if ($this->content !== null) {
             return $this->content;
        }
        $this->content = new stdClass;
        $context = context_course::instance($COURSE->id);
        if (isloggedin() and !has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM)) and
           !has_capability ('moodle/course:update', $context)) {
            // Course::OUT.
            $this->content->text .= html_writer::start_tag('div', array('class' => 'content'));
            $actcompletion = new module($CFG, $DB);
            if ($DB->get_record('course', array('format' => 'site', 'id' => $COURSE->id))) {
                $this->content->text .= html_writer::div($actcompletion->enrolled_course(), 'cnot');
                // Course::IN.
            } else {
                if (((bool)$actcompletion->conda() + (bool)$actcompletion->condb() + (bool)$actcompletion->condc()) > 1) {
                    $this->content->text .= html_writer::div(get_string('condB', 'block_mycoursestatus').'
                    <a href="'.$CFG->wwwroot.'/blocks/mycoursestatus/readme.txt" target="_blank">Readme</a>', 'nostat');
                } else {
                    if ($actcompletion->conda()) {
                        $this->content->text .= $actcompletion->incourse_actcomp();
                    } else if ($actcompletion->condb()) {
                        $this->content->text .= $actcompletion->incourse_coursegr();
                    } else if ($actcompletion->condc()) {
                        $this->content->text .= $actcompletion->incourse_self();
                    } else {
                        $this->content->text .= html_writer::div(get_string('condA', 'block_mycoursestatus').
                        '<a href="'.$CFG->wwwroot.'/blocks/mycoursestatus/readme.txt" target="_blank">Readme</a>', 'nostat');
                    }
                }
            }
            $this->content->text .= html_writer::end_tag('div', array('class' => 'content'));
        }
        return $this->content;
    }
}