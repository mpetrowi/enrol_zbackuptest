<?php

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class enrol_backuptest_edit_form extends moodleform {

    function definition() {
        global $CFG, $DB;

        $mform  = $this->_form;

        list($instance, $plugin, $course) = $this->_customdata;
        $coursecontext = context_course::instance($course->id);

        $mform->addElement('text', 'term', 'Term.  This is stored in the enrol_backuptest_termmap table and should backup and restore with the course.');
        $mform->setType('term', PARAM_TEXT);

        $roles = get_assignable_roles($coursecontext);
        $roles[0] = get_string('none');
        $roles = array_reverse($roles, true);
        $mform->addElement('select', 'customint1', 'Role.  This is stored in customint1 and should be annotated on backup and restored with the course.', $roles);

        $mform->addElement('hidden', 'courseid', null);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        if ($instance->id) {
            $this->add_action_buttons(true);
        } else {
            $this->add_action_buttons(true, get_string('addinstance', 'enrol'));
        }

        $this->set_data($instance);
    }
}
