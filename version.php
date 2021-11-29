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

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2021112900;
$plugin->requires = 2020061500; // Moodle 3.9.
$plugin->maturity = MATURITY_STABLE;
$plugin->release = '1.1';
$plugin->component = 'block_quiz_attempts_list';
$plugin->dependencies = [
    'quiz_export' => 2021110201
];
