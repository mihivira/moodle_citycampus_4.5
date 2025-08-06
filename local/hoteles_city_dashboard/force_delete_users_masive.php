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
 * Plugin strings are defined here.
 *
 * @package     local_hoteles_city_dashboard
 * @category    dashboard
 * @copyright   2019 Subitus <contacto@subitus.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

global $DB;

if (is_siteadmin()){
  $context_system = context_system::instance();
require_login();
$PAGE->set_url($CFG->wwwroot . "/local/hoteles_city_dashboard/force_delete_users_masive.php");
$PAGE->set_context($context_system);
$PAGE->set_pagelayout('admin');
$pluginname = "local_hoteles_city_dashboard";
$PAGE->set_title(get_string('pluginname', $pluginname));
$PAGE->set_heading('Borrado masivo de usuarios');
echo $OUTPUT->header();
//$PAGE->requires->js('/local/hoteles_city_dashboard/js/modal_delete_users.js');


//include simplehtml_form.php
require_once(__DIR__ . '/form_delete_users.php');

//Instantiate simplehtml_form
$mform = new delete_user_form();

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
} else if ($fromform = $mform->get_data()) {
  //echo $fromform->timelastaccess;
//$DB->set_debug(true);
$users_to_delete = $DB->get_records_sql('SELECT id, email, username, deleted, suspended, lastaccess, timemodified FROM {user}  WHERE deleted = ? and suspended = ? AND timemodified < ? LIMIT 50', array(0,1,$fromform->timelastaccess));

$users = [];
foreach ($users_to_delete as $user) {
    $users[] = [
        'id' => $user->id,
        'email' => $user->email,
        'username' => $user->username,
        'deleted' => $user->deleted,
        'suspended' => $user->suspended,
        'lastaccess' => $user->lastaccess,
        'timemodified' => $user->timemodified
    ];
}
$tot_users = count($users);

echo $OUTPUT->render_from_template('local_hoteles_city_dashboard/delete_users', [
  'users' => $users,
  'userstojson' => json_encode($users),
  'tot_users' => $tot_users,
  'wroot' => $CFG->wwwroot
  ]
  );

  /*
echo "Se eliminaran ".$tot_users. " usuarios <br>";
$cont=1;
  foreach ($users_to_delete as $user) {
    echo "Usuario ".$user->email.", ".$cont." de ".$tot_users." ---->";
    if(!del_user($user)){
      echo "Error al borrar usuario: <br>";
      print_r($user);
    } else {
      echo "Eliminado <br>";
    }
    $cont++;
    //sleep(1);
    flush();

  }
*/

} else {
  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed

  $mform->display();
}

} else {
  $homeurl = new moodle_url('/');
  redirect($homeurl);
}



echo $OUTPUT->footer();
