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
 * Definición de trabajos agendados
 *
 * @package     local_hoteles_city_dashboard
 * @category    dashboard
 * @copyright   2023 Miguel Villegas <contacto@sdvir.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Handles upgrading instances of this block.
 *
 * @param int $oldversion
 * @param object $block
 */
function xmldb_local_hoteles_city_dashboard_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if($oldversion < 2023120702){
        $table = new xmldb_table('dashboard_region');

        // Conditionally launch create table for local_aprende_offer_member.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $table = new xmldb_table('dashboard_region_ins');

        // Conditionally launch create table for local_aprende_offer_member.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $table = new xmldb_table('dashboard_cache');

        // Conditionally launch create table for local_aprende_offer_member.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $table = new xmldb_table('dashboard_log');

        // Conditionally launch create table for local_aprende_offer_member.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
    }

    if($oldversion < 2024103004){
        $table = new xmldb_table('local_hc_dashboard_tasks');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null);
        $table->add_field('reporttype', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null);
        $table->add_field('status', XMLDB_TYPE_TEXT, '20', null, XMLDB_NOTNULL, null, 'pending');
        $table->add_field('filename', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null);
        $table->add_field('fileid', XMLDB_TYPE_CHAR, '255', null, null, null);
        $table->add_field('data', XMLDB_TYPE_TEXT, '255', null, null, null);
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('updated_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_aprende_offer_member.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);

            
        }

        upgrade_plugin_savepoint(true, 2024103004, 'local', 'hoteles_city_dashboard');
    }

    


    return true;
}
