<?php


require('../../config.php');
require_once("$CFG->dirroot/enrol/zbackuptest/edit_form.php");

$courseid = required_param('courseid', PARAM_INT);
$instanceid = optional_param('id', 0, PARAM_INT);

$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);

require_login($course);
require_capability('moodle/course:enrolconfig', $context);
require_capability('enrol/manual:config', $context);  // just use manual access control

$PAGE->set_url('/enrol/zbackuptest/edit.php', array('courseid'=>$course->id, 'id'=>$instanceid));
$PAGE->set_pagelayout('admin');

$returnurl = new moodle_url('/enrol/instances.php', array('id'=>$course->id));
if (!enrol_is_enabled('zbackuptest')) {
    redirect($returnurl);
}

$enrol = enrol_get_plugin('zbackuptest');

if ($instanceid) {
    $instance = $DB->get_record('enrol', array('courseid'=>$course->id, 'enrol'=>'zbackuptest', 'id'=>$instanceid), '*', MUST_EXIST);
    $termmap = $DB->get_record('enrol_zbackuptest_termmap', array('enrolid'=>$instance->id));
    $instance->term = $termmap->term;
} else {
    // No instance yet, we have to add new instance.
    if (!$enrol->get_newinstance_link($course->id)) {
        redirect($returnurl);
    }
    navigation_node::override_active_url(new moodle_url('/enrol/instances.php', array('id'=>$course->id)));
    $instance = new stdClass();
    $instance->id         = null;
    $instance->courseid   = $course->id;
    $instance->enrol      = 'zbackuptest';
    $instance->customint1 = '';
}

$mform = new enrol_zbackuptest_edit_form(null, array($instance, $enrol, $course));

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $mform->get_data()) {
    if ($data->id) {
        $instance->name         = 'zbackuptest';
        $instance->status       = $data->status;
        $instance->customint1       = $data->customint1;
        $instance->timemodified = time();
        $DB->update_record('enrol', $instance);
    }  else {
        $data->id = $enrol->add_instance($course, array('name'=>'Backup test', 'status'=>$data->status, 'customint1'=>$data->customint1));
    }

    // Now add our auxiliary record with the term.
    // For simplicity, just delete and add.
    $termmap = new stdClass();
    $termmap->courseid = $course->id;
    $termmap->enrolid = $data->id;
    $termmap->term = $data->term;
    $DB->delete_records('enrol_zbackuptest_termmap', array('enrolid'=>$data->id));
    $DB->insert_record('enrol_zbackuptest_termmap', $termmap);

    redirect($returnurl);
}

$PAGE->set_heading($course->fullname);
$PAGE->set_title('Test');

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
