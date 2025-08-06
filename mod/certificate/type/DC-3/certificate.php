<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
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
 * letter_non_embedded certificate type
 *
 * @package    mod
 * @subpackage certificate
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from view.php
}

$pdf = new PDF($certificate->orientation, 'pt', 'Letter', true, 'UTF-8', false);

$pdf->SetTitle($certificate->name);
$pdf->SetProtection(array('modify'));
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();

// Define variables
// Landscape
if ($certificate->orientation == 'L') {
    $newx = 195;
	$x = 28;
    $y = 125;
    $sealx = 590;
    $sealy = 425;
    $sigx = 130;
    $sigy = 440;
    $custx = 133;
    $custy = 440;
    $wmarkx = 100;
    $wmarky = 90;
    $wmarkw = 600;
    $wmarkh = 420;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 792;
    $brdrh = 612;
    $codey = 485;
} else { // Portrait
	$x = 80;
    $y = 164;
    $sealx = 440;
    $sealy = 590;
    $sigx = 85;
    $sigy = 580;
    $custx = 88;
    $custy = 580;
    $wmarkx = 78;
    $wmarky = 130;
    $wmarkw = 450;
    $wmarkh = 480;
    $brdrx = 10;
    $brdry = 10;
    $brdrw = 594;
    $brdrh = 771;
    $codey = 660;
}


// Add images and lines
certificate_print_image($pdf, $certificate, CERT_IMAGE_BORDER, $brdrx, $brdry, $brdrw, $brdrh);
certificate_draw_frame_letter($pdf, $certificate);
// Set alpha to semi-transparency
$pdf->SetAlpha(0.1);
certificate_print_image($pdf, $certificate, CERT_IMAGE_WATERMARK, $wmarkx, $wmarky, $wmarkw, $wmarkh);
$pdf->SetAlpha(1);
certificate_print_image($pdf, $certificate, CERT_IMAGE_SEAL, $sealx, $sealy, '', '');
certificate_print_image($pdf, $certificate, CERT_IMAGE_SIGNATURE, $sigx, $sigy, '', '');

// Add text
$pdf->SetTextColor(0, 0, 120);
//certificate_print_text($pdf, $x, $y, 'C', 'Helvetica', '', 30, get_string('title', 'certificate'));
$pdf->SetTextColor(0, 0, 0);
certificate_print_text($pdf, $x, $y + 10, 'L', 'Helvetica', '', 14, $USER->lastname.' '.$USER->firstname);
certificate_print_text($pdf, $x + 235, $y + 40, 'L', 'Helvetica', '', 12, '07.2- ALIMENTACIÓN Y HOSPEDAJE.');
certificate_print_text($pdf, $x, $y + 70, 'L', 'Helvetica', '', 12, $USER->department);
//certificate_print_text($pdf, $x, $y + 155, '', 'Helvetica', '', 20, get_string('statement', 'certificate'));
certificate_print_text($pdf, $x, $y + 216, 'L', 'Helvetica', '', 14, $course->fullname);
certificate_print_text($pdf, $x, $y + 308, 'L', 'Helvetica', '', 14, 'Modalidad E-Learning.');
//certificate_print_text($pdf, $x + 183, $y + 239, 'L', 'Helvetica', '', 19.5, implode("\n",str_split(certificate_get_startdate($certificate, $certrecord, $course))));
certificate_print_text($pdf, $x + 333, $y + 239, 'L', 'Helvetica', '', 21, implode("\n",str_split(certificate_get_date($certificate, $certrecord, $course))));
switch ($course->fullname)
{
	case 'Ama de llaves':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '2000 Servicios');
	break;
	case 'Cadena de Suministros':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '3000 Administración, contabilidad y economía');
	break;
	case 'City Access 2.0':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '2000 Servicios');
	break;
	case 'City Premios':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '4000 Comercialización');
	break;
	case 'Confía':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '9000 Participación Social');
	break;
	case 'Contabilidad':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '3000 Administración, contabilidad y economía');
	break;
	case 'CRM de Servicios':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '2000 Servicios');
	break;
	case 'CxC Hoteles':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '3000 Administración, contabilidad y economía');
	break;
	case 'Embajadores de Marca':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '2000 Servicios');
	break;
	case 'Front2Go':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '2000 Servicios');
	break;
	case 'Gastos de Capital':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '3000 Administración, contabilidad y economía');
	break;
	case 'Gestión':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '3000 Administración, contabilidad y economía');
	break;
	case 'Mantenimiento':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '5000 Mantenimiento y reparación');
	break;
	case 'Manual de seguros':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '6000 Seguridad');
	break;
	case 'Recepción':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '2000 Servicios');
	break;
	case 'Venta Consultiva':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '4000 Comercialización');
	break;
	case 'Ventas':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '4000 Comercialización');
	break;
	case 'Todo lo que importa':
	certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '2000 Servicios');
	break;
	
}
//certificate_print_text($pdf, $x, $y + 273, 'L', 'Helvetica', '', 14, '2000 Servicios');
//$pdf->MultiCell(0, 32, certificate_get_date($certificate, $certrecord, $course), 0 , 'C', 'false');
//certificate_print_text($pdf, $x, $y + 283, 'C', 'Helvetica', '', 20, certificate_get_grade($certificate, $course));
certificate_print_text($pdf, $x, $y + 341, 'C', 'Helvetica', '', 14, certificate_get_outcome($certificate, $course));
if ($certificate->printhours) {
    certificate_print_text($pdf, $x, $y + 243, 'L', 'Helvetica', '', 14, $certificate->printhours);
}
certificate_print_text($pdf, $x, $codey, 'C', 'Helvetica', '', 10, certificate_get_code($certificate, $certrecord));
$i = 0;
if ($certificate->printteacher) {
    $context = context_module::instance($cm->id);
    if ($teachers = get_users_by_capability($context, 'mod/certificate:printteacher', '', $sort = 'u.lastname ASC', '', '', '', '', false)) {
        foreach ($teachers as $teacher) {
            $i++;
            certificate_print_text($pdf, $sigx, $sigy + ($i * 12), 'L', 'Times', '', 12, fullname($teacher));
        }
    }
}

certificate_print_text($pdf, $custx, $custy, 'L', null, null, null, $certificate->customtext);
?>
