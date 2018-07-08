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
 * Change quiz passwords by course
 *
 * @package    tool_coursesummary
 * @copyright  2017 Colin Bernard {@link http://bclearningnetwork.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');


/**
 * Returns list of courses with passwords from database
 * @return array
 */
function tool_coursesummary_get_courses() {
    global $DB;

    $courses = array();

    $sql = 'SELECT {course}.fullname
            FROM {quiz}, {course}
            WHERE {quiz}.course={course}.id 
            GROUP BY {course}.fullname';

    $rows = $DB->get_records_sql($sql);


    foreach ($rows as $row) {
        array_push($courses, $row->fullname);
    }

    return $courses;
}

/**
 * Apply a password to quizzes in a course.
 * @param $course
 * @param $password
 * @return void
 */
function tool_coursesummary_set_quiz_passwords($course, $password, $updateblank = false) {
    global $DB;

    // update in database
    $params = array(2);
    $params[0] = $password;
    $params[1] = $course;
    $sql = 'UPDATE {quiz}, {course} 
            SET password=? 
            WHERE {course}.fullname=? 
            AND {course}.id={quiz}.course';

    // check if blank passwords should NOT be updated
    if (!$updateblank) {
        $sql .= ' AND password<>"" AND password IS NOT NULL';
    }

    $DB->execute($sql, $params);
}

/**
 * Get course string from select list ID
 * @param $id
 * @return course string
 */
function tool_coursesummary_get_course_string($id) {
    $courses = tool_coursesummary_get_courses();
    $courses = array_reverse($courses, true);
    $courses[''] = get_string('choosedots');
    $courses = array_reverse($courses, true);

    return $courses[$id];
}