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

class TCPDF_FILTERS {

    /**
     * Define a list of available filter decoders.
     * @private
     */
    private $available.filters = array('ASCIIHexDecode', 'ASCII85Decode', 'LZWDecode', 'FlateDecode', 'RunLengthDecode');

    /**
     * Get a list of available decoding filters.
     * @return (array) Array of available filter decoders.
     * @public
     * @since 1.0.000 (2011-05-23)
     */
    public function getavailablefilters() {
        return $this->available.filters;
    }

    /**
     * Decode data using the specified filter type.
     * @param $filter (string) Filter name.
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @public
     */
    public function decodefilter($filter, $data) {
        switch ($filter) {
            case 'ASCIIHexDecode': {
                return $this->decodefilterasciihexdecode($data);
                break;
            }
            case 'ASCII85Decode': {
                return $this->decodefilterascii85decode($data);
                break;
            }
            case 'LZWDecode': {
                return $this->decodefilterlzwdecode($data);
                break;
            }
            case 'FlateDecode': {
                return $this->decodefilterflatedecode($data);
                break;
            }
            case 'RunLengthDecode': {
                return $this->decodefilterrunlengthdecode($data);
                break;
            }
            case 'CCITTFaxDecode': {
                return $this->decodefilterccittfaxdecode($data);
                break;
            }
            case 'JBIG2Decode': {
                return $this->decodefilterjbig2decode($data);
                break;
            }
            case 'DCTDecode': {
                return $this->decodefilterdctdecode($data);
                break;
            }
            case 'JPXDecode': {
                return $this->decodefilterjpxdecode($data);
                break;
            }
            case 'Crypt': {
                return $this->decodefiltercrypt($data);
                break;
            }
            default: {
                return decodefilterstandard($data);
                break;
            }
        }
    }

    // FILTERS (PDF 32000-2008 - 7.4 Filters).

    /**
     * Standard
     * Default decoding filter (leaves data unchanged).
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @public
     * @since 1.0.000 (2011-05-23)
     */
    public function decodefilterstandard($data) {
        return $data;
    }

    /**
     * ASCIIHexDecode
     * Decodes data encoded in an ASCII hexadecimal representation, reproducing the original binary data.
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @public
     * @since 1.0.000 (2011-05-23)
     */
    public function decodefilterasciihexdecode($data) {
        // Intialize string to return.
        $decoded = '';
        // All white-space characters shall be ignored.
        $data = preg_replace('/[\s]/', '', $data);
        // Check for EOD character: GREATER-THAN SIGN (3Eh).
        $eod = strpos($data, '>');
        if ($eod !== false) {
            // Remove EOD and extra data (if any).
            $data = substr($data, 0, $eod);
            $eod = true;
        }
        // Get data length.
        $data.length = strlen($data);
        if (($data.length % 2) != 0) {
            // Odd number of hexadecimal digits.
            if ($eod) {
                // EOD shall behave as if a 0 (zero) followed the last digit.
                $data = substr($data, 0, -1).'0'.substr($data, -1);
            } else {
                $this->error('decodeASCIIHex: invalid code');
            }
        }
        // Check for invalid characters.
        if (preg_match('/[^a-fA-F\d]/', $data) > 0) {
            $this->error('decodeASCIIHex: invalid code');
        }
        // Get one byte of binary data for each pair of ASCII hexadecimal digits.
        $decoded = pack('H*', $data);
        return $decoded;
    }

    /**
     * ASCII85Decode
     * Decodes data encoded in an ASCII base-85 representation, reproducing the original binary data.
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @public
     * @since 1.0.000 (2011-05-23)
     */
    public function decodefilterascii85decode($data) {
        // Intialize string to return.
        $decoded = '';
        // All white-space characters shall be ignored.
        $data = preg_replace('/[\s]/', '', $data);
        // Remove start sequence 2-character sequence <~ (3Ch)(7Eh).
        if (strpos($data, '<~') !== false) {
            // Remove EOD and extra data (if any).
            $data = substr($data, 2);
        }
        // Check for EOD: 2-character sequence ~> (7Eh)(3Eh).
        $eod = strpos($data, '~>');
        if ($eod !== false) {
            // Remove EOD and extra data (if any).
            $data = substr($data, 0, $eod);
        }
        // Data length.last.pos.
        $data.length = strlen($data);
        // Check for invalid characters.
        if (preg_match('/[^\x21-\x75,\x74]/', $data) > 0) {
            $this->error('decodeASCII85: invalid code');
        }
        // Z sequence.
        $zseq = chr(0).chr(0).chr(0).chr(0);
        // Position inside a group of 4 bytes (0-3).
        $group.pos = 0;
        $tuple = 0;
        $pow85 = array((85 * 85 * 85 * 85), (85 * 85 * 85), (85 * 85), 85, 1);
        $last.pos = ($data.length - 1);
        // For each byte.
        for ($i = 0; $i < $data.length; ++$i) {
            // Get char value.
            $char = ord($data[$i]);
            if ($char == 122) {
                if ($group.pos == 0) {
                    $decoded .= $zseq;
                } else {
                    $this->error('decodeASCII85: invalid code');
                }
            } else {
                // The value represented by a group of 5 characters should never be greater than 2^32 - 1.
                $tuple += (($char - 33) * $pow85[$group.pos]);
                if ($group.pos == 4) {
                    $decoded .= chr($tuple >> 24).chr($tuple >> 16).chr($tuple >> 8).chr($tuple);
                    $tuple = 0;
                    $group.pos = 0;
                } else {
                    ++$group.pos;
                }
            }
        }
        if ($group.pos > 1) {
            $tuple += $pow85[($group.pos - 1)];
        }
        // Last tuple (if any).
        switch ($group.pos) {
            case 4: {
                $decoded .= chr($tuple >> 24).chr($tuple >> 16).chr($tuple >> 8);
                break;
            }
            case 3: {
                $decoded .= chr($tuple >> 24).chr($tuple >> 16);
                break;
            }
            case 2: {
                $decoded .= chr($tuple >> 24);
                break;
            }
            case 1: {
                $this->error('decodeASCII85: invalid code');
                break;
            }
        }
        return $decoded;
    }

    /**
     * LZWDecode.
     * Decompresses data encoded using the LZW (Lempel-Ziv-Welch) adaptive compression method, reproducing the
       original text or binary data.
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @public
     * @since 1.0.000 (2011-05-23).
     */
    public function decodefilterlzwdecode($data) {
        // Intialize string to return.
        $decoded = '';
        // Data length.
        $data.length = strlen($data);
        // Convert string to binary string.
        $bitstring = '';
        for ($i = 0; $i < $data.length; ++$i) {
            $bitstring .= sprintf('%08b', ord($data{$i}));
        }
        // Get the number of bits.
        $data.length = strlen($bitstring);
        // Initialize code length in bits.
        $bitlen = 9;
        // Initialize dictionary index.
        $dix = 258;
        // Initialize the dictionary (with the first 256 entries).
        $dictionary = array();
        for ($i = 0; $i < 256; ++$i) {
            $dictionary[$i] = chr($i);
        }
        // Previous val.
        $prev.index = 0;
        // While we encounter EOD marker (257), read code_length bits.
        while (($data.length > 0) AND (($index = bindec(substr($bitstring, 0, $bitlen))) != 257)) {
            // Remove read bits from string.
            $bitstring = substr($bitstring, $bitlen);
            // Update number of bits.
            $data.length -= $bitlen;
            if ($index == 256) { // Clear-table marker.
                // Reset code length in bits.
                $bitlen = 9;
                // Reset dictionary index.
                $dix = 258;
                $prev.index = 256;
                // Reset the dictionary (with the first 256 entries).
                $dictionary = array();
                for ($i = 0; $i < 256; ++$i) {
                    $dictionary[$i] = chr($i);
                }
            } else if ($prev.index == 256) {
                // First entry.
                $decoded .= $dictionary[$index];
                $prev.index = $index;
            } else {
                // Check if index exist in the dictionary.
                if ($index < $dix) {
                    // Index exist on dictionary.
                    $decoded .= $dictionary[$index];
                    $dic.val = $dictionary[$prev.index].$dictionary[$index]{0};
                    // Store current index.
                    $prev.index = $index;
                } else {
                    // Index do not exist on dictionary.
                    $dic.val = $dictionary[$prev.index].$dictionary[$prev.index]{0};
                    $decoded .= $dic.val;
                }
                // Update dictionary.
                $dictionary[$dix] = $dic.val;
                ++$dix;
                // Change bit length by case.
                if ($dix == 2047) {
                    $bitlen = 12;
                } else if ($dix == 1023) {
                    $bitlen = 11;
                } else if ($dix == 511) {
                    $bitlen = 10;
                }
            }
        }
        return $decoded;
    }

    /**
     * FlateDecode
     * Decompresses data encoded using the zlib/deflate compression method, reproducing the original text or
       binary data.
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @public
     * @since 1.0.000 (2011-05-23)
     */
    public function decodefilterflatedecode($data) {
        // Intialize string to return.
        $decoded = gzuncompress($data);
        if ($decoded === false) {
            $this->error('decodeFlate: invalid code');
        }
        return $decoded;
    }

    /**
     * RunLengthDecode
     * Decompresses data encoded using a byte-oriented run-length encoding algorithm.
     * @param $data (string) Data to decode.
     * @public
     * @since 1.0.000 (2011-05-23)
     */
    public function decodefilterrunlengthdecode($data) {
        // Intialize string to return.
        $decoded = '';
        // Data length.
        $data.length = strlen($data);
        $i = 0;
        while ($i < $data.length) {
            // Get current byte value.
            $byte = ord($data{$i});
            if ($byte == 128) {
                // A length value of 128 denote EOD.
                break;
            } else if ($byte < 128) {
                // If the length byte is in the range 0 to 127.
                // The following length + 1 (1 to 128) bytes shall be copied literally during decompression.
                $decoded .= substr($data, ($i + 1), ($byte + 1));
                // Move to next block.
                $i += ($byte + 2);
            } else {
                // If length is in the range 129 to 255.
                // The following single byte shall be copied 257 - length (2 to 128) times during decompression.
                $decoded .= str_repeat($data{($i + 1)}, (257 - $byte));
                // Move to next block.
                $i += 2;
            }
        }
        return $decoded;
    }

    /**
     * CCITTFaxDecode (NOT IMPLEMETED)
     * Decompresses data encoded using the CCITT facsimile standard, reproducing the original data (typically
       monochrome image data at 1 bit per pixel).
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @public
     * @since 1.0.000 (2011-05-23)
     */
    public function decodefilterccittfaxdecode($data) {
        return $data;
    }

    /**
     * JBIG2Decode (NOT IMPLEMETED)
     * Decompresses data encoded using the JBIG2 standard, reproducing the original monochrome (1 bit per pixel)
       image data (or an approximation of that data).
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @public
     * @since 1.0.000 (2011-05-23)
     */
    public function decodefilterjbig2decode($data) {
        return $data;
    }

    /**
     * DCTDecode (NOT IMPLEMETED)
     * Decompresses data encoded using a DCT (discrete cosine transform) technique based on the JPEG standard,
       reproducing image
       sample data that approximates the original data.
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @public
     * @since 1.0.000 (2011-05-23)
     */
    public function decodefilterdctdecode($data) {
        return $data;
    }

    /**
     * JPXDecode (NOT IMPLEMETED)
     * Decompresses data encoded using the wavelet-based JPEG2000 standard, reproducing the original image data.
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @public
     * @since 1.0.000 (2011-05-23)
     */
    public function decodefilterjpxdecode($data) {
        return $data;
    }

    /**
     * Crypt (NOT IMPLEMETED)
     * Decrypts data encrypted by a security handler, reproducing the data as it was before encryption.
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @public
     * @since 1.0.000 (2011-05-23)
     */
    public function decodefiltercrypt($data) {
        return $data;
    }

    // END FILTERS SECTION.

    /**
     * This method is automatically called in case of fatal error; it simply outputs the message and halts
       the execution.
     * @param $msg (string) The error message
     * @public
     * @since 1.0.000 (2011-05-23)
     */
    public function error($msg) {
        // Exit program and print error.
        die('<strong>TCPDF_FILTERS ERROR: </strong>'.$msg);
    }

} // END OF TCPDF_FILTERS CLASS.

