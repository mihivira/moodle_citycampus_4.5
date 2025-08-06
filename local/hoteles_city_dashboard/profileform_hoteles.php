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
 * Form for editing a user profile
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * Editado por Subitus <contacto@subitus.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    //  It must be included from a Moodle page.
}

require_once($CFG->dirroot.'/lib/formslib.php');
require_once(__DIR__ . '/lib.php');
// require_once($CFG->dirroot . '/user/profile/lib.php');

/**
 * Class profileform_hoteles. Código tomado de user\editadvanced_form.php para editar su funcionmiento
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profileform_hoteles extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $USER, $CFG, $COURSE;

        $mform = $this->_form;
        $editoroptions = null;
        $filemanageroptions = null;

        if (!is_array($this->_customdata)) {
            throw new coding_exception('invalid custom data for user_edit_form');
        }
        // $editoroptions = $this->_customdata['editoroptions'];
        // $filemanageroptions = $this->_customdata['filemanageroptions'];
        $user = $this->_customdata['user'];
        $userid = $user->id;

        // Accessibility: "Required" is bad legend text.
        $strgeneral  = get_string('general');
        $strrequired = get_string('required');

        // Add some extra hidden fields.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', core_user::get_property_type('id'));
        $mform->addElement('hidden', 'course', $COURSE->id);
        $mform->setType('course', PARAM_INT);

        // Print the required moodle fields first.
        $mform->addElement('header', 'moodle', $strgeneral);

        // Shared fields.
        custom_useredit_shared_definition($mform, $editoroptions, $filemanageroptions, $user);

        $mform->addElement('hidden', 'auth', 'manual');
        $mform->setType('auth', PARAM_TEXT);

        $mform->addElement('text', 'username', get_string('username'), 'size="30"');
        $mform->addHelpButton('username', 'username', 'auth');
        $mform->addRule('username', $strrequired, 'required');
        $mform->setType('username', PARAM_RAW);

        if($user->id != -1){
            $mform->addElement('advcheckbox', 'suspended', get_string('suspended', 'auth'));
            $mform->addHelpButton('suspended', 'suspended', 'auth');
        }

        if(!isset($_GET['suspenduser'])){
            $mform->addElement('text', 'newpassword', get_string('newpassword'), 'size="30"');
            $mform->addHelpButton('newpassword', 'newpassword');
            $mform->setType('newpassword', PARAM_TEXT);// core_user::get_property_type('password'));
        }else{
            $mform->addElement('date_selector', 'suspend_date', 'Fecha de ejecución de la baja');
        }

        // Next the customisable profile fields.
        custom_profile_definition($mform, $userid);

        if ($userid == -1) {
            $btnstring = get_string('createuser');
        } else {
            if(isset($_GET['suspenduser'])){
                if($user->suspended){ // Quitar suspensión
                    $btnstring = "Quitar suspensión";
                }else{ // Dejar suspensión
                    $btnstring = "Suspender usuario";
                }
            }else{
                $btnstring = "Actualizar";
            }
        }

        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', $btnstring);
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);

        //$this->add_action_buttons($cancel = true, $btnstring);
        //$this->add_action_buttons($cancel = true, $submitlabel=null);


        $this->set_data($user);
    }

    /**
     * Extend the form definition after data has been parsed.
     */
    public function definition_after_data() {
        global $USER, $CFG, $DB, $OUTPUT;

        $mform = $this->_form;

        // Trim required name fields.
        foreach (useredit_get_required_name_fields() as $field) {
            $mform->applyFilter($field, 'trim');
        }

        if ($userid = $mform->getElementValue('id')) {
            $user = $DB->get_record('user', array('id' => $userid));
        } else {
            $user = false;
        }

        if ($user and ($user->id == $USER->id or is_siteadmin($user))) {
            // Prevent self and admin mess ups.
            if ($mform->elementExists('suspended')) {
                $mform->hardFreeze('suspended');
            }
        }

        // Next the customisable profile fields.
        custom_profile_definition_after_data($mform, $userid);
    }

    /**
     * Validate the form data.
     * @param array $usernew
     * @param array $files
     * @return array|bool
     */
    public function validation($usernew, $files) {
        global $CFG, $DB;

        $usernew = (object)$usernew;
        $usernew->username = trim($usernew->username);

        $user = $DB->get_record('user', array('id' => $usernew->id));
        $err = array();

        if (!$user and !empty($usernew->createpassword)) {
            if ($usernew->suspended) {
                // Show some error because we can not mail suspended users.
                $err['suspended'] = get_string('error');
            }
        } else {
            if (!empty($usernew->newpassword)) {
                $errmsg = ''; // Prevent eclipse warning.
                if (!check_password_policy($usernew->newpassword, $errmsg)) {
                    $err['newpassword'] = $errmsg;
                }
            } else if (!$user) {
                $auth = get_auth_plugin($usernew->auth);
                if ($auth->is_internal()) {
                    // Internal accounts require password!
                    $err['newpassword'] = get_string('required');
                }
            }
        }

        if (empty($usernew->username)) {
            // Might be only whitespace.
            $err['username'] = get_string('required');
        } else if (!$user or $user->username !== $usernew->username) {
            // Check new username does not exist.
            if ($DB->record_exists('user', array('username' => $usernew->username, 'mnethostid' => $CFG->mnet_localhost_id))) {
                $err['username'] = get_string('usernameexists');
            }
            // Check allowed characters.
            if ($usernew->username !== core_text::strtolower($usernew->username)) {
                $err['username'] = get_string('usernamelowercase');
            } else {
                if ($usernew->username !== core_user::clean_field($usernew->username, 'username')) {
                    $err['username'] = get_string('invalidusername');
                }
            }
        }

        if (!$user or (isset($usernew->email) && $user->email !== $usernew->email)) {
            if (!validate_email($usernew->email)) {
                $err['email'] = get_string('invalidemail');
            } else if (empty($CFG->allowaccountssameemail)
                    and $DB->record_exists('user', array('email' => $usernew->email, 'mnethostid' => $CFG->mnet_localhost_id))) {
                $err['email'] = get_string('emailexists');
            }
        }

        // Next the customisable profile fields.
        $err += profile_validation($usernew, $files);

        if (count($err) == 0) {
            return true;
        } else {
            return $err;
        }
    }
}
