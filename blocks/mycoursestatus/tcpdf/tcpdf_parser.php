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
require_once(dirname(__FILE__).'/tcpdf_filters.php');

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

/**
 * @class TCPDF_PARSER
 * This is a PHP class for parsing PDF documents.<br>
 * @package com.tecnick.tcpdf
 * @brief This is a PHP class for parsing PDF documents..
 * @version 1.0.001
 */
class TCPDF_PARSER {

    /**
     * Raw content of the PDF document.
     * @private
     */
    private $pdfdata = '';

    /**
     * XREF data.
     * @protected
     */
    protected $xref = array();

    /**
     * Array of PDF objects.
     * @protected
     */
    protected $objects = array();

    /**
     * Class object for decoding filters.
     * @private
     */
    private $filterdecoders;

    /**
     * Parse a PDF document an return an array of objects.
     * @param $data (string) PDF data to parse.
     * @public
     * @since 1.0.000 (2011-05-24)
     */
    public function __construct($data) {
        if (empty($data)) {
            $this->error('Empty PDF data.');
        }
        $this->pdfdata = $data;
        // Get length.
        $pdflen = strlen($this->pdfdata);
        // Initialize class for decoding filters.
        $this->filterdecoders = new TCPDF_FILTERS();
        // Get xref and trailer data.
        $this->xref = $this->getxrefdata();
        // Parse all document objects.
        $this->objects = array();
        foreach ($this->xref['xref'] as $obj => $offset) {
            if (!isset($this->objects[$obj])) {
                $this->objects[$obj] = $this->getindirectobject($obj, $offset, true);
            }
        }
        // Release some memory.
        unset($this->pdfdata);
        $this->pdfdata = '';
    }

    /**
     * Return an array of parsed PDF document objects.
     * @return (array) Array of parsed PDF document objects.
     * @public
     * @since 1.0.000 (2011-06-26)
     */
    public function getparseddata() {
        return array($this->xref, $this->objects);
    }

    /**
     * Get xref (cross-reference table) and trailer data from PDF document data.
     * @param $offset (int) xref offset (if know).
     * @param $xref (array) previous xref array (if any).
     * @return Array containing xref and trailer data.
     * @protected
     * @since 1.0.000 (2011-05-24)
     */
    protected function getxrefdata($offset=0, $xref=array()) {
        if ($offset == 0) {
            // Find last startxref.
            if (preg_match_all('/[\r\n]startxref[\s]*[\r\n]+([0-9]+)[\s]*[\r\n]+%%EOF/i', $this->pdfdata, $matches,
                PREG_SET_ORDER, $offset) == 0) {
                $this->error('Unable to find startxref');
            }
            $matches = array_pop($matches);
            $startxref = $matches[1];
        } else {
            // Get the first xref at the specified offset.
            if (preg_match('/[\r\n]startxref[\s]*[\r\n]+([0-9]+)[\s]*[\r\n]+%%EOF/i', $this->pdfdata, $matches,
                PREG_OFFSET_CAPTURE, $offset) == 0) {
                $this->error('Unable to find startxref');
            }
            $startxref = $matches[1][0];
        }
        // Check xref position.
        if (strpos($this->pdfdata, 'xref', $startxref) != $startxref) {
            $this->error('Unable to find xref');
        }
        // Extract xref data (object indexes and offsets).
        $xoffset = $startxref + 5;
        // Initialize object number.
        $obj.num = 0;
        $offset = $xoffset;
        while (preg_match('/^([0-9]+)[\s]([0-9]+)[\s]?([nf]?)/im', $this->pdfdata, $matches, PREG_OFFSET_CAPTURE,
               $offset) > 0) {
            $offset = (strlen($matches[0][0]) + $matches[0][1]);
            if ($matches[3][0] == 'n') {
                // Create unique object index: [object number]_[generation number].
                $index = $obj.num.'_'.intval($matches[2][0]);
                // Check if object already exist.
                if (!isset($xref['xref'][$index])) {
                    // Store object offset position.
                    $xref['xref'][$index] = intval($matches[1][0]);
                }
                ++$obj.num;
                $offset += 2;
            } else if ($matches[3][0] == 'f') {
                ++$obj.num;
                $offset += 2;
            } else {
                // Object number (index).
                $obj.num = intval($matches[1][0]);
            }
        }
        // Get trailer data.
        if (preg_match('/trailer[\s]*<<(.*)>>[\s]*[\r\n]+startxref[\s]*[\r\n]+/isU', $this->pdfdata, $matches,
            PREG_OFFSET_CAPTURE, $xoffset) > 0) {
            $trailer.data = $matches[1][0];
            if (!isset($xref['trailer'])) {
                // Get only the last updated version.
                $xref['trailer'] = array();
                // Parse trailer.data.
                if (preg_match('/Size[\s]+([0-9]+)/i', $trailer.data, $matches) > 0) {
                    $xref['trailer']['size'] = intval($matches[1]);
                }
                if (preg_match('/Root[\s]+([0-9]+)[\s]+([0-9]+)[\s]+R/i', $trailer.data, $matches) > 0) {
                    $xref['trailer']['root'] = intval($matches[1]).'_'.intval($matches[2]);
                }
                if (preg_match('/Encrypt[\s]+([0-9]+)[\s]+([0-9]+)[\s]+R/i', $trailer.data, $matches) > 0) {
                    $xref['trailer']['encrypt'] = intval($matches[1]).'_'.intval($matches[2]);
                }
                if (preg_match('/Info[\s]+([0-9]+)[\s]+([0-9]+)[\s]+R/i', $trailer.data, $matches) > 0) {
                    $xref['trailer']['info'] = intval($matches[1]).'_'.intval($matches[2]);
                }
                if (preg_match('/ID[\s]*[\[][\s]*[<]([^>]*)[>][\s]*[<]([^>]*)[>]/i', $trailer.data, $matches) > 0) {
                    $xref['trailer']['id'] = array();
                    $xref['trailer']['id'][0] = $matches[1];
                    $xref['trailer']['id'][1] = $matches[2];
                }
            }
            if (preg_match('/Prev[\s]+([0-9]+)/i', $trailer.data, $matches) > 0) {
                // Get previous xref.
                $xref = $this->getxrefdata(intval($matches[1]), $xref);
            }
        } else {
            $this->error('Unable to find trailer');
        }
        return $xref;
    }

    /**
     * Get object type, raw value and offset to next object
     * @param $offset (int) Object offset.
     * @return array containing object type, raw value and offset to next object
     * @protected
     * @since 1.0.000 (2011-06-20)
     */
    protected function getrawobject($offset=0) {
        $objtype = ''; // Object type to be returned.
        $objval = ''; // Object value to be returned.
        // Skip initial white space chars: \x00 null (NUL), \x09 horizontal tab (HT), \x0A line feed (LF),
        // \x0C form feed (FF), \x0D carriage return (CR), \x20 space (SP).
        $offset += strspn($this->pdfdata, "\x00\x09\x0a\x0c\x0d\x20", $offset);
        // Get first char.
        $char = $this->pdfdata{$offset};
        // Get object type.
        switch ($char) {
            case '%': { // X25 PERCENT SIGN.
                // Skip comment and search for next token.
                $next = strcspn($this->pdfdata, "\r\n", $offset);
                if ($next > 0) {
                    $offset += $next;
                    return $this->getrawobject($this->pdfdata, $offset);
                }
                break;
            }
            case '/': { // X2F SOLIDUS
                // Name object.
                $objtype = $char;
                ++$offset;
                if (preg_match('/^([^\x00\x09\x0a\x0c\x0d\x20\s\x28\x29\x3c\x3e\x5b\x5d\x7b\x7d\x2f\x25]+)/',
                    substr($this->pdfdata, $offset, 256), $matches) == 1) {
                    $objval = $matches[1]; // Unescaped value.
                    $offset += strlen($objval);
                }
                break;
            }
            case '(':   // X28 LEFT PARENTHESIS.
            case ')': { // X29 RIGHT PARENTHESIS.
                // Literal string object.
                $objtype = $char;
                ++$offset;
                $strpos = $offset;
                if ($char == '(') {
                    $open.bracket = 1;
                    while ($open.bracket > 0) {
                        if (!isset($this->pdfdata{$strpos})) {
                            break;
                        }
                        $ch = $this->pdfdata{$strpos};
                        switch ($ch) {
                            case '\\': { // REVERSE SOLIDUS (5Ch) (Backslash).
                                // Skip next character.
                                ++$strpos;
                                break;
                            }
                            case '(': { // LEFT PARENHESIS (28h).
                                ++$open.bracket;
                                break;
                            }
                            case ')': { // RIGHT PARENTHESIS (29h).
                                --$open.bracket;
                                break;
                            }
                        }
                        ++$strpos;
                    }
                    $objval = substr($this->pdfdata, $offset, ($strpos - $offset - 1));
                    $offset = $strpos;
                }
                break;
            }
            case '[':   // X5B LEFT SQUARE BRACKET.
            case ']': { // X5D RIGHT SQUARE BRACKET.
                // Array object.
                $objtype = $char;
                ++$offset;
                if ($char == '[') {
                    // Get array content.
                    $objval = array();
                    do {
                        // Get element.
                        $element = $this->getrawobject($offset);
                        $offset = $element[2];
                        $objval[] = $element;
                    } while ($element[0] != ']');
                    // Remove closing delimiter.
                    array_pop($objval);
                }
                break;
            }
            case '<':   // X3C LESS-THAN SIGN.
            case '>': { // X3E GREATER-THAN SIGN.
                if (isset($this->pdfdata{($offset + 1)}) AND ($this->pdfdata{($offset + 1)} == $char)) {
                    // Dictionary object.
                    $objtype = $char.$char;
                    $offset += 2;
                    if ($char == '<') {
                        // Get array content.
                        $objval = array();
                        do {
                            // Get element.
                            $element = $this->getrawobject($offset);
                            $offset = $element[2];
                            $objval[] = $element;
                        } while ($element[0] != '>>');
                        // Remove closing delimiter.
                        array_pop($objval);
                    }
                } else {
                    // Hexadecimal string object.
                    $objtype = $char;
                    ++$offset;
                    if (($char == '<') AND (preg_match('/^([0-9A-Fa-f]+)[>]/iU', substr($this->pdfdata, $offset),
                         $matches) == 1)) {
                        $objval = $matches[1];
                        $offset += strlen($matches[0]);
                    }
                }
                break;
            }
            default: {
                if (substr($this->pdfdata, $offset, 6) == 'endobj') {
                    // Indirect object.
                    $objtype = 'endobj';
                    $offset += 6;
                } else if (substr($this->pdfdata, $offset, 4) == 'null') {
                    // Null object.
                    $objtype = 'null';
                    $offset += 4;
                    $objval = 'null';
                } else if (substr($this->pdfdata, $offset, 4) == 'true') {
                    // Boolean true object.
                    $objtype = 'boolean';
                    $offset += 4;
                    $objval = 'true';
                } else if (substr($this->pdfdata, $offset, 5) == 'false') {
                    // Boolean false object.
                    $objtype = 'boolean';
                    $offset += 5;
                    $objval = 'false';
                } else if (substr($this->pdfdata, $offset, 6) == 'stream') {
                    // Start stream object.
                    $objtype = 'stream';
                    $offset += 6;
                    if (preg_match('/^[\r\n]+(.*)[\r\n]*endstream/isU', substr($this->pdfdata, $offset),
                        $matches) == 1) {
                        $objval = $matches[1];
                        $offset += strlen($matches[0]);
                    }
                } else if (substr($this->pdfdata, $offset, 9) == 'endstream') {
                    // End stream object.
                    $objtype = 'endstream';
                    $offset += 9;
                } else if (preg_match('/^([0-9]+)[\s]+([0-9]+)[\s]+R/iU', substr($this->pdfdata, $offset, 33),
                          $matches) == 1) {
                    // Indirect object reference.
                    $objtype = 'ojbref';
                    $offset += strlen($matches[0]);
                    $objval = intval($matches[1]).'_'.intval($matches[2]);
                } else if (preg_match('/^([0-9]+)[\s]+([0-9]+)[\s]+obj/iU', substr($this->pdfdata, $offset, 33),
                          $matches) == 1) {
                    // Object start.
                    $objtype = 'ojb';
                    $objval = intval($matches[1]).'_'.intval($matches[2]);
                    $offset += strlen ($matches[0]);
                } else if (($numlen = strspn($this->pdfdata, '+-.0123456789', $offset)) > 0) {
                    // Numeric object.
                    $objtype = 'numeric';
                    $objval = substr($this->pdfdata, $offset, $numlen);
                    $offset += $numlen;
                }
                break;
            }
        }
        return array($objtype, $objval, $offset);
    }

    /**
     * Get content of indirect object.
     * @param $obj.ref (string) Object number and generation number separated by underscore character.
     * @param $offset (int) Object offset.
     * @param $decoding (boolean) If true decode streams.
     * @return array containing object data.
     * @protected
     * @since 1.0.000 (2011-05-24)
     */
    protected function getindirectobject($obj.ref, $offset=0, $decoding=true) {
        $obj = explode('_', $obj.ref);
        if (($obj === false) OR (count($obj) != 2)) {
            $this->error('Invalid object reference: '.$obj);
            return;
        }
        $objref = $obj[0].' '.$obj[1].' obj';
        if (strpos($this->pdfdata, $objref, $offset) != $offset) {
            // An indirect reference to an undefined object shall be considered a reference to the null object.
            return array('null', 'null', $offset);
        }
        // Starting position of object content.
        $offset += strlen($objref);
        // Get array of object content.
        $objdata = array();
        $i = 0; // Object main index.
        do {
            // Get element.
            $element = $this->getrawobject($offset);
            $offset = $element[2];
            // Decode stream using stream's dictionary information.
            if ($decoding AND ($element[0] == 'stream') AND (isset($objdata[($i - 1)][0])) AND
               ($objdata[($i - 1)][0] == '<<')) {
                $element[3] = $this->decodestream($objdata[($i - 1)][1], substr($element[1], 1));
            }
            $objdata[$i] = $element;
            ++$i;
        } while ($element[0] != 'endobj');
        // Remove closing delimiter.
        array_pop($objdata);
        // Return raw object content.
        return $objdata;
    }

    /**
     * Get the content of object, resolving indect object reference if necessary.
     * @param $obj (string) Object value.
     * @return array containing object data.
     * @protected
     * @since 1.0.000 (2011-06-26)
     */
    protected function getobjectval($obj) {
        if ($obj[0] == 'objref') {
            // Reference to indirect object.
            if (isset($this->objects[$obj[1]])) {
                // This object has been already parsed.
                return $this->objects[$obj[1]];
            } else if (isset($this->xref[$obj[1]])) {
                // Parse new object.
                $this->objects[$obj[1]] = $this->getindirectobject($obj[1], $this->xref[$obj[1]], false);
                return $this->objects[$obj[1]];
            }
        }
        return $obj;
    }

    /**
     * Decode the specified stream.
     * @param $sdic (array) Stream's dictionary array.
     * @param $stream (string) Stream to decode.
     * @return array containing decoded stream data and remaining filters.
     * @protected
     * @since 1.0.000 (2011-06-22)
     */
    protected function decodestream($sdic, $stream) {
        // Get stream lenght and filters.
        $slength = strlen($stream);
        $filters = array();
        foreach ($sdic as $k => $v) {
            if ($v[0] == '/') {
                if (($v[1] == 'Length') AND (isset($sdic[($k + 1)])) AND ($sdic[($k + 1)][0] == 'numeric')) {
                    // Get declared stream length.
                    $declength = intval($sdic[($k + 1)][1]);
                    if ($declength < $slength) {
                        $stream = substr($stream, 0, $declength);
                        $slength = $declength;
                    }
                } else if (($v[1] == 'Filter') AND (isset($sdic[($k + 1)]))) {
                    // Resolve indirect object.
                    $objval = $this->getobjectval($sdic[($k + 1)]);
                    if ($objval[0] == '/') {
                        // Single filter.
                        $filters[] = $objval[1];
                    } else if ($objval[0] == '[') {
                        // Array of filters.
                        foreach ($objval[1] as $flt) {
                            if ($flt[0] == '/') {
                                $filters[] = $flt[1];
                            }
                        }
                    }
                }
            }
        }
        // Decode the stream.
        $remaining.filters = array();
        foreach ($filters as $filter) {
            if (in_array($filter, $this->filterdecoders->getAvailableFilters())) {
                $stream = $this->filterdecoders->decodeFilter($filter, $stream);
            } else {
                // Add missing filter to array.
                $remaining.filters[] = $filter;
            }
        }
        return array($stream, $remaining.filters);
    }

    /**
     * This method is automatically called in case of fatal error; it simply outputs the message and
       halts the execution.
     * @param $msg (string) The error message
     * @public
     * @since 1.0.000 (2011-05-23)
     */
    public function error($msg) {
        // Exit program and print error.
        die('<strong>TCPDF_PARSER error: </strong>'.$msg);
    }
} // END OF TCPDF_PARSER CLASS.

