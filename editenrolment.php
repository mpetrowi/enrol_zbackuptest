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
 * User enrolment edit script for testing custom enrollment plugin.
 *
 * @package    enrol_backuptest
 * @copyright  2014 Matt Petro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once("$CFG->dirroot/enrol/backuptest/editenrolment_form.php"); // Forms for this page.

$ueid   = required_param('ue', PARAM_INT);

$ue = $DB->get_record('user_enrolments', array('id' => $ueid), '*', MUST_EXIST);
$instance = $DB->get_record('enrol', array('id'=>$ue->enrolid), '*', MUST_EXIST);
$user = $DB->get_record('user', array('id'=>$ue->userid), '*', MUST_EXIST);
$custom = $DB->get_record('enrol_backuptest_enrolment', array('enrolmentid'=>$ue->id), '*', IGNORE_MISSING);

// The URL of the enrolled users page for the course.
$usersurl = new moodle_url('/enrol/users.php', array('id' => $instance->courseid));

// Obviously.
require_login($instance->courseid);
// The user must be able to manage enrolments within the course.
require_capability('enrol/manual:manage', context_course::instance($instance->courseid, MUST_EXIST));

$url = new moodle_url('/enrol/backuptest/editenrolment.php', array('ue'=>$ueid));

if (!$custom) {
    $custom = new stdClass();
    $custom->enrolmentid = $ue->id;
    $custom->comment = '';
    $custom->id = $DB->insert_record('enrol_backuptest_enrolment', $custom);
}

$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
navigation_node::override_active_url($usersurl);

// Get the enrolment edit form.
$ue->comment = $custom->comment;
$mform = new enrol_backuptest_user_enrolment_form($url, array('ue'=>$ue));
$mform->set_data($PAGE->url->params());

if ($mform->is_cancelled()) {
    redirect($usersurl);

} else if ($data = $mform->get_data()) {
    $custom->comment = $data->comment;
    $DB->update_record('enrol_backuptest_enrolment', $custom);
    redirect($usersurl);
}

$fullname = fullname($user);
$title = get_string('editenrolment', 'core_enrol');

$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add($title);
$PAGE->navbar->add($fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($fullname);
$mform->display();
echo $OUTPUT->footer();
