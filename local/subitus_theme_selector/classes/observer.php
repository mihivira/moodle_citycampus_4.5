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

defined('MOODLE_INTERNAL') || die();

/**
 * Event observer for mod_forum.
 */
class local_subitus_theme_selector_observer {
    public static function user_loggedin(\core\event\user_loggedin $event){
        $userid = $event->userid;
        self::assignProfiles($userid);
    }

    public static function user_loggedinas(\core\event\user_loggedinas $event){
      $userid = $event->userid;
      self::assignProfiles($userid);
    }

    public static function user_updated(\core\event\user_updated $event){
      $userid = $event->userid;
      self::assignProfiles($userid);
    }

    public static function assignProfiles($userid){
        global $DB;
        global $SESSION;
        // $user = $DB->get_record('user', array('id' => $userid), 'id,theme');
        $profiles = self::getThemeSelectorProfiles();
        foreach($profiles as $profile){
            $coincidencia = mb_strtoupper($profile->text);
            $data = $DB->get_record_sql("SELECT data FROM {user_info_data} WHERE fieldid = {$profile->field} AND userid = {$userid}");
            if($data != false){
                if( mb_strtoupper($data->data) == $coincidencia){
                    $SESSION->theme = $profile->theme;
                }
            }
        }
    }

    public static function getThemeSelectorProfiles(){
        $configs = get_config('local_subitus_theme_selector');
        $configs = (array) $configs; // Cast stdClass to array
        $keys = array_keys($configs);
        $perfiles = array();
        foreach ($keys as $key) {
            if( strpos($key, 'name_') !== false ){
                $profileFields = 'name,field,text,theme,status';
                $numero = substr($key, strpos($key, '_') + 1);
                $perfilTemporal = array();
                foreach (explode(',', $profileFields) as $f) {
                    $temporalKey = $f.'_'.$numero;
                    $perfilTemporal[$f] = $configs[$temporalKey];
                }
                if($perfilTemporal['status'] == 1){ // Solo los perfiles activos
                    $profileFields = 'name,field,text,theme';
                    $hay_vacio = false;
                    foreach(explode(',', $profileFields) as $f){
                        if($perfilTemporal[$f] == ''){
                            $hay_vacio = true;
                        }
                    }
                    if( ! $hay_vacio ){
                        $perfilTemporal = (object) $perfilTemporal;
                        array_push($perfiles, $perfilTemporal);
                    }
                }
            }
        }
        error_log(print_r($perfiles, true));
        return $perfiles;
    }
}
