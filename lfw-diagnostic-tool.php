<?php
/*
  Plugin Name: Diagnostic Tools for LFW
  Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
  Description: This plugin gives recommendations based on the data provided by the end user
  Version: 1.0
  Author: Kurund Jalmi
  Author URI: http://URI_Of_The_Plugin_Author
 License: GPL2
 */

add_action( 'ninja_forms_after_submission', 'diagnostic_tool_calculations' );
function diagnostic_tool_calculations( $form_data ){
  $submitted_data = array();
  // get the submitted values
  if (!empty($form_data['fields_by_key'])) {
    foreach($form_data['fields_by_key'] as $fld => $value) {
      $submitted_data[$fld] = $value['value'];
    }

    //print_r($submitted_data);
    // matrix for the calculation
    $calculation_matrix = array(
      's1' => 0.3,
      's2' => 0.1,
      's3' => 0.35,
      's4' => 0.15,
      's5' => 0.05,
      's6' => 0.05,
      't1' => 0.25,
      't2' => 0.1,
      't3' => 0.25,
      't4' => 0.4,
    );

    // calculate student score
    $student_score = 0;
    $student_fields = array('s1', 's2', 's3', 's4', 's5', 's6');
    foreach($student_fields as $key) {
      if (!empty($submitted_data[$key])){
        $student_score += 100 * $calculation_matrix[$key];
      }
    }

    // calculate student level
    $student_level = 'Low';
    if ($student_score > 55) {
      $student_level = 'High';
    }
    elseif ($student_score <= 55 && $student_score > 40) {
      $student_level = 'Medium';
    }

    // calculate teacher score
    $teacher_score = 0;
    $teacher_fields = array('t1', 't2', 't3', 't4');
    foreach($teacher_fields as $key) {
      if (!empty($submitted_data[$key])){
        $teacher_score += 100 * $calculation_matrix[$key];
      }
    }

    // calculate teacher level
    $teacher_level = 'Low';
    if ($teacher_score > 55) {
      $teacher_level = 'High';
    }
    elseif ($teacher_score <= 55 && $teacher_score > 40) {
      $teacher_level = 'Medium';
    }


  }

}