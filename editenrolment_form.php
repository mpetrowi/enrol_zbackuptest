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
 * Testing custom user enrollment field
 *
 * @package    enrol_backuptest
 * @copyright  2014 Matt Petro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class enrol_backuptest_user_enrolment_form extends moodleform {
    function definition() {
        global $CFG, $DB;

        $mform = $this->_form;
        $ue     = $this->_customdata['ue'];

        $mform->addElement('textarea', 'comment', "Custom enrol comment", array('optional' => true));

        $mform->addElement('hidden', 'ue');
        $mform->setType('ue', PARAM_INT);

        $this->add_action_buttons();

        $this->set_data(array(
            'ue' => $ue->id,
            'comment' => $ue->comment,
        ));
    }
}
