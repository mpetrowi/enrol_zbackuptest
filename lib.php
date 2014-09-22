<?php

/**
 * MDL-47323  backuptest enrol plugin to demonstrate backup and restore.
 *
 * This plugin does nothing by itself.  It demonstrates enrol backup/restore.
 *
 * Settings:
 *
 *   Role id - This is stored in customint1, and annotated on backup.
 *   Term - This string is stored in the enrol_backuptest_termmap table should backup/restore with the enrol instance.
 *
 * @author Matt Petro
 */

class enrol_backuptest_plugin extends enrol_plugin {

    public function get_newinstance_link($courseid) {
        return new moodle_url('/enrol/backuptest/edit.php', array('courseid'=>$courseid));
    }

    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;
        $context = context_course::instance($instance->courseid);
        $icons = array();
        if (has_capability('enrol/manual:config', $context)) {
            $editlink = new moodle_url("/enrol/backuptest/edit.php", array('courseid'=>$instance->courseid, 'id'=>$instance->id));
            $icons[] = $OUTPUT->action_icon($editlink, new pix_icon('t/edit', get_string('edit'), 'core',
                    array('class' => 'iconsmall')));
        }
        return $icons;
    }

    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/manual:config', $context);
    }

    /**
     * Backup execution step hook.
     *
     * @param backup_enrolments_execution_step $step
     * @param stdClass $enrol
     */
    public function backup_execution(backup_enrolments_execution_step $step, stdClass $enrol) {
        // annotate customint1 as a role
        $step->annotate_id('role', $enrol->customint1);
    }

    public function restore_instance(restore_enrolments_structure_step $step, stdClass $data, $course, $oldid) {
        global $DB, $CFG;

        $data->customint1 = $step->get_mappingid('role', $data->customint1, null);

        $instanceid = $this->add_instance($course, (array)$data);
        $step->set_mapping('enrol', $oldid, $instanceid);
    }


}
