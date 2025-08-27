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
namespace theme_norte19\output;
use moodle_url;
use html_writer;
use get_string;

/**
 * Parent theme: boost
 *
 * @package  theme_norte19
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \core_renderer {

    /**
     * See if this is the first view of the current cm in the session if it has fake blocks.
     *
     * (We track up to 100 cms so as not to overflow the session.)
     * This is done for drawer regions containing fake blocks so we can show blocks automatically.
     *
     * @return boolean true if the page has fakeblocks and this is the first visit.
     */
    public function firstview_fakeblocks(): bool {
        global $SESSION;

        $firstview = false;
        if ($this->page->cm) {
            if (!$this->page->blocks->region_has_fakeblocks('side-pre')) {
                return false;
            }
            if (!property_exists($SESSION, 'firstview_fakeblocks')) {
                $SESSION->firstview_fakeblocks = [];
            }
            if (array_key_exists($this->page->cm->id, $SESSION->firstview_fakeblocks)) {
                $firstview = false;
            } else {
                $SESSION->firstview_fakeblocks[$this->page->cm->id] = true;
                $firstview = true;
                if (count($SESSION->firstview_fakeblocks) > 100) {
                    array_shift($SESSION->firstview_fakeblocks);
                }
            }
        }
        return $firstview;
    }

   /**
     * Override the logo rendering to use brand-specific logos
     */
    public function logo($maxwidth = 300, $maxheight = 100) {
        global $USER;
        
        $brand = theme_norte19_get_user_brand($USER->id);
        $logourl = theme_norte19_get_logo_url($brand);

        if (empty($logourl)) {
            return parent::logo($maxwidth, $maxheight);
        }

        return $logourl;
    }
    
    /**
     * Override navbar brand to include brand-specific logo
     */
    public function navbar_brand() {
        global $USER;
        
        $brand = theme_norte19_get_user_brand($USER->id);
        $logourl = theme_norte19_get_logo_url($brand);
        
        if (empty($logourl)) {
            return parent::navbar_brand();
        }
        
        $sitename = format_string($this->page->course->fullname ?? fullname($USER));
        
        return html_writer::link(
            new moodle_url('/'),
            html_writer::img($logourl, $sitename, [
                'class' => 'navbar-brand-logo',
                'style' => 'max-height: 50px; width: auto;',
                'data-brand' => $brand ?: 'default'
            ]),
            ['class' => 'navbar-brand d-flex align-items-center']
        );
    }
    
    /**
     * Get the brand name for the current user
     */
    public function get_brand_name() {
        global $USER;
        
        $brand = theme_norte19_get_user_brand($USER->id);
        if ($brand) {
            $marcas = theme_norte19_get_configured_brands();
            return $marcas[$brand] ?? $brand;
        }
        
        return null;
    }

   
}
