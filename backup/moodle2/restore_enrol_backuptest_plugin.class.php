<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/enrol/wisc/lib.php');

/**
 * Provides the information to restore test enrol instances
 */
class restore_enrol_backuptest_plugin extends restore_enrol_plugin {

    public function define_enrol_plugin_structure() {
        return array(
                new restore_path_element('termmap', $this->get_pathfor('/termmaps/termmap')),
        );
    }

    /**
     * Process the termmap element
     */
    public function process_termmap($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $enrolid = $this->get_new_parentid('enrol');

        if (!$enrolid) {
            return; // Enrol instance was not restored
        }
        $type = $DB->get_field('enrol', 'enrol', array('id'=>$enrolid));
        if ($type !== 'backuptest') {
            return; // Enrol was likely converted to manual
        }
        $data->enrolid = $enrolid;
        $data->courseid = $this->task->get_courseid();
        $newitemid = $DB->insert_record('enrol_backuptest_termmap', $data);
    }

}
