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

namespace local_hoteles_city_dashboard;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/excellib.class.php');

class MoodleExcelWorkbookExtended extends \MoodleExcelWorkbook {

    /**
     * Guarda el archivo en el sistema de archivos de Moodle.
     *
     * @param array $file_record Arreglo con los metadatos del archivo (contexto, área, nombre, etc.)
     * @return stored_file|null Devuelve el archivo guardado o null en caso de fallo.
     */
    public function save_to_file($file_record) {
        global $CFG;

        // Captura la salida en un buffer
        ob_start();
        $this->close();  // Esto genera la salida del archivo
        $excel_content = ob_get_clean(); // Obtiene el contenido de Excel en formato binario

        // Guarda el archivo en el sistema de archivos de Moodle
        $fs = get_file_storage();
        $file = $fs->create_file_from_string($file_record, $excel_content);

        // Verifica si el archivo fue creado exitosamente
        if ($file) {
            return $file;  // Devuelve el archivo guardado
        } else {
            return null;   // Retorna null si hubo un error
        }
    }
}
