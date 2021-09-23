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
 * @package    block_quiz_attempts_list
 * @date       20/09/2021
 * @author     rdelvaux@cblue.be
 * @copyright  2021, CBlue SPRL, support@cblue.be
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * display_attempts block renderer.
 *
 * @package    block_quiz_attempts_list
 * @date       20/09/2021
 * @author     rdelvaux@cblue.be
 * @copyright  2021, CBlue SPRL, support@cblue.be
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_quiz_attempts_list_renderer extends plugin_renderer_base {

    /**
     * @param $quiz
     * @return string
     * @throws coding_exception
     */
    public function display_attempts($quiz) {
        global $CFG;

        $output = html_writer::start_tag('ul', ['class' => 'list']);
        foreach ($quiz as $q) {
            $output .= html_writer::start_tag('li', ['class' => 'font-weight-bold ']);
            $output .= html_writer::tag('p', $q->name);
            if (empty($q->attempts)) {
                $output .= html_writer::tag('p', get_string('attempts_list_empty', 'block_quiz_attempts_list'));
            } else {
                foreach ($q->attempts as $attempt) {
                    $output .= html_writer::link(
                        $CFG->wwwroot . '/mod/quiz/report/export/a2pdf.php?attempt=' . $attempt->id . '&inline=1&pagemode=1',
                        get_string('attempt', 'block_quiz_attempts_list', $attempt),
                        ['target' => '_blank']
                    );
                    $output .= html_writer::tag('br', '');
                }
            }
            $output .= html_writer::end_tag('li');
        }
        $output .= html_writer::end_tag('ol');

        return $output;
    }

}
