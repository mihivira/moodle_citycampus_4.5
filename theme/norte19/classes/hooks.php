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
 * Hook callbacks for theme_norte19.
 *
 * @package   theme_norte19
 * @copyright 2025 Miguel Villegas <mihivira@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Hook callbacks for theme_norte19.
 */
class theme_norte19_hooks {

    /**
     * Callback for the before_standard_head_html_generation hook.
     *
     * @param \core\hook\output\before_standard_head_html_generation $hook
     */
    public static function before_standard_head_html_generation(
    \core\hook\output\before_standard_head_html_generation $hook
): void {
    global $USER, $PAGE;
    
    // Only process if this is our theme
    if ($PAGE->theme->name !== 'norte19') {
        return;
    }
    
    $brand = theme_norte19_get_user_brand($USER->id);
    if ($brand) {
        $primary = get_config('theme_norte19', $brand . 'color') ?: '#0056b3';
        $background = get_config('theme_norte19', $brand . 'backcolor') ?: '#ffffff';
        $navbar = get_config('theme_norte19', $brand . 'navbarcolor') ?: $primary;
        $sitecolor = get_config('theme_norte19', $brand . 'sitecolor') ?: '#0056b3';
        
        $css = "
            /* Colores forzados para marca: {$brand} */
            :root {
                --brand-primary: {$primary};
                --brand-background: {$background};
                --brand-navbar: {$navbar};
            }
            
            body {
                background-color: {$background} !important;
            }
            
            .bg-primary {
                background-color: {$primary} !important;
            }
            
            .navbar.bg-primary {
                background-color: {$navbar} !important;
            }
            
            .btn-primary {
                background-color: {$primary} !important;
                border-color: darken({$primary}, 10%) !important;
            }
            
            .btn-primary:hover {
                background-color: darken({$primary}, 10%) !important;
                border-color: darken({$primary}, 15%) !important;
            }
            
            a {
                color: {$sitecolor} !important;
            }

            a.hybtn {
                color: #ffffff !important;
            }
            
            a:hover {
                color: darken({$primary}, 15%) !important;
            }
            
            .text-primary {
                color: {$primary} !important;
            }
            
            .border-primary {
                border-color: {$primary} !important;
            }
            
            .header-area, .footer-header-underline, .hybtn, .item-icon i {
                border-color:  {$sitecolor} !important;
            }

            .hybtn, button.active, .icon-box-content, .item-icon i, .theme-bg, .course-inner::after, .project-inner::after, .social-links li:hover, #scrollUp, .scrollUp {
                background-color: {$sitecolor} !important;
            }

            .block-text-box {
                background: {$sitecolor} !important;
                
            }

            .block09design1 .fdb-touch {
                border-top: solid 0.3125rem {$sitecolor}    !important;
            }

            .block09design1 .fdb-box {
                border: 1px solid  {$sitecolor} !important;
            }

            .navbar-light .nav-link.active {
                color: {$sitecolor} !important;
            }

            .hytext, .main-menu li.active a, a:hover, .main-menu li a:hover, .footer-menu li a:hover, .hybtn:hover, .icon-icon i, .item-icon i:hover, .video-img-thumbnail a:hover, .blog-tags a:hover, .blog-btn .read-more-btn:hover, .author-content a:hover, .address-line p a:hover {
                color: {$sitecolor} !important;
            }
            .btn-primary {
                
                border-color: {$sitecolor} !important;
            }
            .moremenu .nav-link.active {
                border-bottom-color: {$sitecolor} !important;
            }

            .navbar-brand {
                background-color: {$sitecolor} !important;
            }

            .navbar.fixed-top .navbar-brand .logo {
                max-height: 55px !important;
            }

            .navbar.fixed-top {
                height: 59px !important;
            }
        ";
        
        // DIFERENTES MÉTODOS POSIBLES SEGÚN LA VERSIÓN DE MOODLE
        if (method_exists($hook, 'add_html')) {
            // Método más nuevo
            $hook->add_html("<style>{$css}</style>");
        } else if (method_exists($PAGE->requires, 'css_inline')) {
            // Método tradicional
            $PAGE->requires->css_inline($css);
        } else {
            // Fallback: agregar directamente al output
            echo "<style>{$css}</style>";
        }
    }
}
}