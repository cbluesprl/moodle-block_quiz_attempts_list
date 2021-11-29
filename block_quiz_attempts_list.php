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
 * Renderer for block attempts_list.
 *
 * @package    block_quiz_attempts_list
 * @copyright  2021 rdelvaux@cblue.be
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 *  attempts_list block renderer on course.
 *
 * @package    block_quiz_attempts_list
 * @copyright  2021 rdelvaux@cblue.be
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_quiz_attempts_list extends block_base {

    /**
     * @throws coding_exception
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_quiz_attempts_list');
    }

    /**
     * @return bool[]
     */
    public function applicable_formats() {
        return ['course-view' => true];
    }

    /**
     * @return stdClass
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function get_content() {
        global $CFG, $DB, $USER;

        if ($this->content !== null) {
            return $this->content;
        }
        if (empty($this->instance)) {
            return $this->content;
        }

        require_once($CFG->dirroot . '/course/lib.php');

        $this->content = new stdClass();
        $this->content->text = html_writer::tag('p', get_string('attempts_list_empty', 'block_quiz_attempts_list'));
        $this->content->footer = '';

        if (!isloggedin() ||
            empty($this->page) ||
            empty($this->page->course->id) ||
            $this->page->course->id === SITEID
        ) {
            return $this->content;
        }

        $quizmodule = $DB->get_record('modules', ['name' => 'quiz']);
        if (empty($quizmodule)) {
            return $this->content;
        }

        $quizids = [];
        foreach (get_fast_modinfo($this->page->course->id)->cms as $cm) {
            if ($cm->module === $quizmodule->id && $cm->uservisible === true) {
                $quizids[] = $cm->instance;
            }
        }
        if (empty($quizids)) {
            return $this->content;
        }

        $quiz = [];

        [$quizidssql, $params] = $DB->get_in_or_equal($quizids);
        $params[] = $USER->id;
        $rs = $DB->get_recordset_sql(
            "SELECT
                q.id AS quiz_id, q.name AS quiz_name,
                qa.id AS attempt_id, qa.attempt AS attempt_attempt, qa.timefinish AS attempt_timefinish,
                qa.sumgrades AS grade_grade, gi.grademax AS grade_max
            FROM {quiz} q
            JOIN {quiz_attempts} qa ON qa.quiz = q.id
            JOIN {grade_items} gi ON gi.iteminstance = q.id AND gi.itemtype = 'mod' AND gi.itemmodule = 'quiz'
            WHERE q.id $quizidssql AND qa.userid = ? AND qa.state = 'finished' AND qa.preview = 0
            ORDER BY qa.timefinish DESC",
            $params
        );
        foreach ($rs as $r) {
            if (empty($quiz[$r->quiz_id])) {
                $quiz[$r->quiz_id] = new stdClass();
                $quiz[$r->quiz_id]->name = $r->quiz_name;
                $quiz[$r->quiz_id]->attempts = [];
            }
            if (count($quiz[$r->quiz_id]->attempts) < 3) {
                $quiz[$r->quiz_id]->attempts[$r->attempt_id] = new stdClass();
                $quiz[$r->quiz_id]->attempts[$r->attempt_id]->id = $r->attempt_id;
                $quiz[$r->quiz_id]->attempts[$r->attempt_id]->attempt = $r->attempt_attempt;
                $quiz[$r->quiz_id]->attempts[$r->attempt_id]->date =
                    userdate($r->attempt_timefinish, get_string("strftimedatetime"));
                $quiz[$r->quiz_id]->attempts[$r->attempt_id]->grade =
                    number_format((float) $r->grade_grade, 2) .
                    ' / ' .
                    number_format((float) $r->grade_max, 2);
            }
        }
        $rs->close();

        if (!empty($quiz)) {
            $this->content->text = ($this->page->get_renderer('block_quiz_attempts_list'))
                ->display_attempts($quiz);
        }

        return $this->content;
    }
}
