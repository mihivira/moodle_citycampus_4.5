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
 * Custom editadvanced page. Based in user\editadvanced.php
 *
 * @package     local_hoteles_city_dashboard
 * @category    string
 * @copyright   2019 Subitus <contacto@subitus.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/profileform_hoteles.php');
// require_once($CFG->libdir.'/gdlib.php');
require_once($CFG->libdir.'/adminlib.php');
// require_once($CFG->dirroot.'/user/editadvanced_form.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/user/lib.php');
// require_once($CFG->dirroot.'/webservice/lib.php');
// require_once($CFG->dirroot.'/lib/formslib.php');

$formulario_enviado = false;
$id     = optional_param('id', -1, PARAM_INT);    // User id; -1 if creating new user.
$page_params = "id=" . $id;
$suspenduser = optional_param('suspenduser', -1, PARAM_INT);
$shouldSuspend = $suspenduser != -1;
$creating_user = false;
$user_is_suspended = false;

if($id == -1){
    $creating_user = true;
}

/**
 * Revisión de permisos de usuario
 */
if(!(local_hoteles_city_dashboard_user_has_access(LOCAL_HOTELES_CITY_DASHBOARD_ALTA_BAJA_USUARIOS, '', false)
 || local_hoteles_city_dashboard_user_has_access(LOCAL_HOTELES_CITY_DASHBOARD_LISTADO_TODOS_LOS_USUARIOS, '', false))){
    print_error('Usted no tiene permiso para acceder a esta sección');
}

// $page_params = array('id' => $id);
if($suspenduser != -1){
    $page_params .= '&suspenduser=1';
}
$current_url = $url = $CFG->wwwroot . '/local/hoteles_city_dashboard/administrar_usuarios.php?'. $page_params;
$PAGE->set_url($current_url);

global $DB;

$systemcontext = context_system::instance();
// $id     = optional_param('id', $USER->id, PARAM_INT);    // User id; -1 if creating new user.
$course = optional_param('course', SITEID, PARAM_INT);   // Course id (defaults to Site).
$returnto = optional_param('returnto', null, PARAM_ALPHA);  // Code determining where to return to after save.


$course = $DB->get_record('course', array('id' => $course), '*', MUST_EXIST);

if (!empty($USER->newadminuser)) {
    // Ignore double clicks, we must finish all operations before cancelling request.
    ignore_user_abort(true);

    $PAGE->set_course($SITE);
    $PAGE->set_pagelayout('maintenance');
} else {
    if ($course->id == SITEID) {
        require_login();
        $PAGE->set_context(context_system::instance());
    } else {
        require_login($course);
    }
    $PAGE->set_pagelayout('admin');
}

if ($course->id == SITEID) {
    $coursecontext = context_system::instance();   // SYSTEM context.
} else {
    $coursecontext = context_course::instance($course->id);   // Course context.
}
$systemcontext = context_system::instance();

if ($id == -1) {
    // Creating new user.
    $user = new stdClass();
    $user->id = -1;
    $user->auth = 'manual';
    $user->confirmed = 1;
    $user->deleted = 0;
    $user->timezone = '99';
    // require_capability('moodle/user:create', $systemcontext);
    // admin_externalpage_setup('addnewuser', '', array('id' => -1));
} else {
    // Editing existing user.
    // require_capability('moodle/user:update', $systemcontext);
    $user = $DB->get_record('user', array('id' => $id), '*', MUST_EXIST);
    $user_is_suspended = $user->suspended;
    $PAGE->set_context(context_user::instance($user->id));
    $PAGE->navbar->includesettingsbase = true;
    if ($user->id != $USER->id) {
        $PAGE->navigation->extend_for_user($user);
    } else {
        if ($node = $PAGE->navigation->find('myprofile', navigation_node::TYPE_ROOTNODE)) {
            $node->force_open();
        }
    }

    if(!local_hoteles_city_dashboard_user_has_all_permissions()){
        $institutions = local_hoteles_city_dashboard_get_institutions();
        if(!in_array($user->institution, $institutions)){ // Una institución que no pertenece al usuario
            print_error('Usted no tiene permiso de acceder a esta sección');
        }
    }

}

// Remote users cannot be edited.
if ($user->id != -1 and is_mnet_remote_user($user)) {
    redirect($CFG->wwwroot . "/user/view.php?id=$id&course={$course->id}");
}

if ($user->id != $USER->id and is_siteadmin($user) and !is_siteadmin($USER)) {  // Only admins may edit other admins.
    print_error('useradmineditadmin');
}

if (isguestuser($user->id)) { // The real guest user can not be edited.
    print_error('guestnoeditprofileother');
}

if ($user->deleted) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('userdeleted'));
    echo $OUTPUT->footer();
    die;
}

// Load user preferences.
useredit_load_preferences($user);

// Load custom profile fields data.
profile_load_data($user);

// User interests.
$user->interests = core_tag_tag::get_item_tags_array('core', 'user', $id);

if ($user->id !== -1) {
    $usercontext = context_user::instance($user->id);
    $editoroptions = array(
        'maxfiles'   => EDITOR_UNLIMITED_FILES,
        'maxbytes'   => $CFG->maxbytes,
        'trusttext'  => false,
        'forcehttps' => false,
        'context'    => $usercontext
    );

    $user = file_prepare_standard_editor($user, 'description', $editoroptions, $usercontext, 'user', 'profile', 0);
} else {
    $usercontext = null;
    // This is a new user, we don't want to add files here.
    $editoroptions = array(
        'maxfiles' => 0,
        'maxbytes' => 0,
        'trusttext' => false,
        'forcehttps' => false,
        'context' => $coursecontext
    );
}

// Prepare filemanager draft area.
$draftitemid = 0;
$filemanagercontext = $editoroptions['context'];
$filemanageroptions = array('maxbytes'       => $CFG->maxbytes,
                             'subdirs'        => 0,
                             'maxfiles'       => 1,
                             'accepted_types' => 'web_image');
file_prepare_draft_area($draftitemid, $filemanagercontext->id, 'user', 'newicon', 0, $filemanageroptions);
$user->imagefile = $draftitemid;
$mform = new profileform_hoteles($current_url, array(
    'editoroptions' => $editoroptions,
    'filemanageroptions' => $filemanageroptions,
    'user' => $user));

echo $OUTPUT->header();
$specialScript = "";

if ($mform->is_cancelled()) {
    // Display a message or redirect somewhere.
    if(local_hoteles_city_dashboard_is_gerente_general()){
      redirect(new moodle_url($CFG->wwwroot.'/local/hoteles_city_dashboard/usuarios.php?type=4'));
    }
    redirect(new moodle_url($CFG->wwwroot.'/local/hoteles_city_dashboard/usuarios.php'));
} else if($usernew = $mform->get_data()){
    $userformdata = $mform->get_data();
    $userpost = $_POST;
    if(!isset($userformdata->department)){
        if(isset($userpost['department'])){
            $usernew->department = $userpost['department'];
        }
    }
    if(isset($usernew->suspended)){
        $user_is_suspended = $usernew->suspended;
    }
    $formulario_enviado = true;
    // _log($usernew);
    $usercreated = false;

    if (empty($usernew->auth)) {
        // User editing self.
        $authplugin = get_auth_plugin($user->auth);
        unset($usernew->auth); // Can not change/remove.
    } else {
        $authplugin = get_auth_plugin($usernew->auth);
    }

    $usernew->timemodified = time();
    $createpassword = false;

    if ($usernew->id == -1) { // Crear nuevo usuario
        unset($usernew->id);
        $createpassword = !empty($usernew->createpassword);
        unset($usernew->createpassword);
        $usernew = file_postupdate_standard_editor($usernew, 'description', $editoroptions, null, 'user', 'profile', null);
        $usernew->mnethostid = $CFG->mnet_localhost_id; // Always local user.
        $usernew->confirmed  = 1;
        $usernew->timecreated = time();
        if ($authplugin->is_internal()) {
            if ($createpassword or empty($usernew->newpassword)) {
                $usernew->password = '';
            } else {
                $usernew->password = hash_internal_user_password($usernew->newpassword);
            }
        } else {
            $usernew->password = AUTH_PASSWORD_NOT_CACHED;
        }
        $usernew->id = user_create_user($usernew, false, false);

        if (!$authplugin->is_internal() and $authplugin->can_change_password() and !empty($usernew->newpassword)) {
            if (!$authplugin->user_update_password($usernew, $usernew->newpassword)) {
                // Do not stop here, we need to finish user creation.
                debugging(get_string('cannotupdatepasswordonextauth', '', '', $usernew->auth), DEBUG_NONE);
            }
        }
        $usercreated = true;
    } else { // Editar usuario ya existente
        if(isset($usernew->suspended)){
            $user_is_suspended = $usernew->suspended;
        }
        $allowed_fields = get_config('local_hoteles_city_dashboard', 'userformdefaultfields');
        if(empty($allowed_fields)){
            $allowed_fields = array();
        }else{
            $allowed_fields = explode(',', $allowed_fields);
        }
        if(in_array('description', $allowed_fields)){
            $usernew = file_postupdate_standard_editor($usernew, 'description', $editoroptions, $usercontext, 'user', 'profile', 0);
        }
        // Pass a true old $user here.
        if (!$authplugin->user_update($user, $usernew)) {
            // Auth update failed.
            print_error('cannotupdateuseronexauth', '', '', $user->auth);
        }
        user_update_user($usernew, false, false);

        // Set new password if specified.
        if (!empty($usernew->newpassword)) {
            if ($authplugin->can_change_password()) {
                if (!$authplugin->user_update_password($usernew, $usernew->newpassword)) {
                    print_error('cannotupdatepasswordonextauth', '', '', $usernew->auth);
                }
                unset_user_preference('create_password', $usernew); // Prevent cron from generating the password.

                if (!empty($CFG->passwordchangelogout)) {
                    // We can use SID of other user safely here because they are unique,
                    // the problem here is we do not want to logout admin here when changing own password.
                    \core\session\manager::kill_user_sessions($usernew->id, session_id());
                }
                if (!empty($usernew->signoutofotherservices)) {
                    webservice::delete_user_ws_tokens($usernew->id);
                }
            }
        }

        // Force logout if user just suspended.
        if (isset($usernew->suspended) and $usernew->suspended and !$user->suspended) {
            \core\session\manager::kill_user_sessions($user->id);
        }
    }

    $usercontext = context_user::instance($usernew->id);

    // Update preferences.
    useredit_update_user_preference($usernew);

    // Update tags.
    if (/*empty($USER->newadminuser) &&*/ isset($usernew->interests)) {
        useredit_update_interests($usernew, $usernew->interests);
    }

    // if(get_config('local_hoteles_city_dashboard', 'userformimage')){
    //     // Update user picture.
    //     // if (empty($USER->newadminuser)) {
    //         core_user::update_picture($usernew, $filemanageroptions);
    //     // }
    // }

    // Update mail bounces.
    useredit_update_bounces($user, $usernew);

    // Update forum track preference.
    useredit_update_trackforums($user, $usernew);

    // Save custom profile fields data.
    profile_save_data($usernew);

    // Reload from db.
    $usernew = $DB->get_record('user', array('id' => $usernew->id));

    if ($createpassword) {
        setnew_password_and_mail($usernew);
        unset_user_preference('create_password', $usernew);
        set_user_preference('auth_forcepasswordchange', 1, $usernew);
    }

    // Trigger update/create event, after all fields are stored.
    if ($usercreated) {
        \core\event\user_created::create_from_userid($usernew->id)->trigger();
    } else {
        \core\event\user_updated::create_from_userid($usernew->id)->trigger();
    }

    if ($user->id == $USER->id) {
        // Override old $USER session variable.
        foreach ((array)$usernew as $variable => $value) {
            if ($variable === 'description' or $variable === 'password') {
                // These are not set for security nad perf reasons.
                continue;
            }
            $USER->$variable = $value;
        }
        // Preload custom fields.
        profile_load_custom_fields($USER);

        if (!empty($USER->newadminuser)) {
            unset($USER->newadminuser);
            // Apply defaults again - some of them might depend on admin user info, backup, roles, etc.
            admin_apply_default_settings(null, false);
            // Admin account is fully configured - set flag here in case the redirect does not work.
            unset_config('adminsetuppending');
            // Redirect to admin/ to continue with installation.
            // redirect($current_url);
            // redirect("$CFG->wwwroot/$CFG->admin/");
        } else if (empty($SITE->fullname)) {
            // Somebody double clicked when editing admin user during install.
            // redirect($current_url);
            // redirect("$CFG->wwwroot/$CFG->admin/");
        } else {
            if ($returnto === 'profile') {
                if ($course->id != SITEID) {
                    $returnurl = new moodle_url('/user/view.php', array('id' => $user->id, 'course' => $course->id));
                } else {
                    $returnurl = new moodle_url('/user/profile.php', array('id' => $user->id));
                }
            } else {
                $returnurl = new moodle_url('/user/preferences.php', array('userid' => $user->id));
            }
            // redirect($current_url);
            // redirect($returnurl);
        }
        redirect($current_url);
    } else {
        // \core\session\manager::gc(); // Remove stale sessions.
        // redirect($current_url);
        // redirect("$CFG->wwwroot/$CFG->admin/user.php");
        if(local_hoteles_city_dashboard_is_gerente_general()){
          redirect(new moodle_url($CFG->wwwroot.'/local/hoteles_city_dashboard/usuarios.php?type=4'));
        }
        redirect(new moodle_url($CFG->wwwroot.'/local/hoteles_city_dashboard/usuarios.php'));
    }
    // Never reached..
}else{
    if($shouldSuspend){
        $specialScript = "
        <script>
            element = document.getElementById('id_suspended');
            if(element !== null){
                element.checked = !element.checked;
                console.log('Suspendiendo usuarios');
            }
        </script>";
    }else{
        if(!$creating_user){

            $specialScript = "
      <script>
          element = document.getElementById('id_suspended');
          if(element !== null){
              element.style.display = 'none';
              element.parentNode.parentNode.style.display = 'none';
              console.log('Ocultando check de suspension');
          }
          $('#id_profile_field_marca,#id_profile_field_Puestomarca,#id_firstname,#id_lastname').css('display','none');
          if(isAdmin){
            $('#id_profile_field_marca,#id_profile_field_Puestomarca,#id_firstname,#id_lastname').css('display','block');
          }
      </script>";
        } else {

            $specialScript = "
      <script>
          element = document.getElementById('id_suspended');
          if(element !== null){
              element.style.display = 'none';
              element.parentNode.parentNode.style.display = 'none';
              console.log('Ocultando check de suspension');
          }
          if(isGG){
            $('#id_profile_field_Region').html('');
            $('#id_profile_field_Region').append($('<option>').val(region).text(region));
            $('#id_profile_field_marca,#id_profile_field_Puestomarca,#id_profile_field_Region').css('display','none');
          }
      </script>";
        }

    }
}
// $streditmyprofile = get_string('editmyprofile');
$PAGE->set_title('Administración de usuarios hoteles city');
$userfullname = fullname($user, true);
if($user_is_suspended){
    $userfullname .= " (suspendido)";
}
echo $OUTPUT->heading($userfullname);
if($user_is_suspended){ // Si el usuario está suspendido, se necesita el permiso para verlo
    local_hoteles_city_dashboard_user_has_access(LOCAL_HOTELES_CITY_DASHBOARD_CAMBIO_USUARIOS, "Este usuario ({$userfullname}) fue suspendido, actualmente no tiene el permiso para editarlo");
}
$mform->display();

//if(local_hoteles_city_dashboard_is_gerente_general()){
  ?>
  <script>
  var isGG, isAdmin = 0;
  var region,marca = '';
  isGG  = <?php
  if(local_hoteles_city_dashboard_is_gerente_general()){
      echo local_hoteles_city_dashboard_is_gerente_general();
  } else {
    echo 0;
  }
 ?>;
  isAdmin = <?php
  if(is_siteadmin()){
    echo is_siteadmin();
  } else {
    echo 0;
  }
  ?>;
  region = '<?php
  if($USER->profile['Region'] != null || $USER->profile['Region'] != undefined){
    echo $USER->profile['Region'];
  } else {
    echo "";
  }
    ?>';
    marca ='<?php
    if($USER->profile['marca'] != null || $USER->profile['marca'] != undefined){
      echo $USER->profile['marca'];
    } else {
      echo "";
    }
?>';
//  $("#id_profile_field_marca,#id_profile_field_puestomarca,#id_firstname,#id_lastname").css('display','none');



  </script>
  <?php
//}

/* if($creating_user){ // Rellenado de formulario
    echo "<script src='user.js'></script>";
}
echo "<script src='llenar_cohorte.js'></script>"; */

$PAGE->requires->js_call_amd('local_hoteles_city_dashboard/user_form_handler', 'init', []);

echo $specialScript;
?>

<?php
echo $OUTPUT->footer();
