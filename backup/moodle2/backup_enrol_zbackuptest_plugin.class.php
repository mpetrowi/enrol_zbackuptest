<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Provides the information to backup test enrol instances
 */
class backup_enrol_zbackuptest_plugin extends backup_enrol_plugin {

    protected function define_enrol_plugin_structure() {

        // Define the virtual plugin element with the condition to fulfill
        $plugin = $this->get_plugin_element(null, '../../enrol', $this->pluginname);

        // Create one standard named plugin element (the visible container)
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // connect the visible container ASAP
        $plugin->add_child($pluginwrapper);

        $termmaps = new backup_nested_element('termmaps');

        // Now create the enrol own structures
        $termmap = new backup_nested_element('termmap', array('id'), array('term'));

        // Now the own termmap tree
        $pluginwrapper->add_child($termmaps);
        $termmaps->add_child($termmap);

        // set source to populate the data
        $termmap->set_source_table('enrol_zbackuptest_termmap',
                array('enrolid'  => backup::VAR_PARENTID,
                      'courseid' => backup::VAR_COURSEID));

        return $plugin;
    }
}
