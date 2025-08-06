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
 * Cache definitions for tool_userrestore.
 *
 * File         caches.php
 * Encoding     UTF-8
 *
 * @package     tool_userrestore
 * @copyright   2017 R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$definitions = array(
    'deletedusercache' => array(
        'mode' => cache_store::MODE_APPLICATION, // MUST be shared app wide cache.
        'simplekeys' => true,
        'simpledata' => false,
        'staticacceleration' => true,
        'canuselocalstore' => true,
    ),

);