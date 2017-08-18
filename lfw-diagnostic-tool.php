<?php
/*
  Plugin Name: Diagnostic Tools for LFW
  Plugin URI: https://github.com/kurund/lfw-diagnostic-tool
  Description: This plugin gives recommendations based on the data provided by the end user
  Version: 1.0
  Author: Kurund Jalmi
  Author URI: http://kurund.com
 License: GPL2
 */

add_action( 'ninja_forms_after_submission', 'diagnostic_tool_calculations' );
function diagnostic_tool_calculations( $form_data ) {
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
    if ($student_score >= 65) {
      $student_level = 'High';
    }
    elseif ($student_score < 65 && $student_score >= 30) {
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
    if ($teacher_score >= 60) {
      $teacher_level = 'High';
    }
    elseif ($teacher_score < 60 && $teacher_score >= 25) {
      $teacher_level = 'Medium';
    }

    // calculate supplementary class teacher
    if (empty($submitted_data['t7'])) {
      $supp_teacher_level = 'Low';
    } elseif ($submitted_data['t5'] && $submitted_data['t6']) {
      $supp_teacher_level = 'High';
    } elseif (empty($submitted_data['t5']) && $submitted_data['t6']) {
      $supp_teacher_level = 'Medium';
    } elseif ($submitted_data['t5'] && empty($submitted_data['t6'])) {
      $supp_teacher_level = 'High';
    } elseif (empty($submitted_data['t5']) && empty($submitted_data['t6'])) {
      $supp_teacher_level = 'Low';
    } else {
      $supp_teacher_level = 'Medium';
    }

    $solution1 = $solution2 = $solution3 = $solution4 = 'No';

    if ($student_level === 'Low') {
      $solution1 = 'Yes';
      $solution2 = 'Yes';
    }

    if ($solution2 == 'Yes' and ($teacher_level == 'High' or $teacher_level == 'Medium')) {
      $solution3 = 'Yes';
    }

    if ($solution2 == 'Yes' and $teacher_level == 'Low') {
      $solution4 = 'Yes';
    }

    $success_message = "
    <table>
      <tr>
        <td colspan='3'><strong>Outcome</strong></td>
      </tr>
      <tr>
        <td>1.</td><td>Exposure for students (High, Medium, Low)</td><td>{$student_level}</td>
      </tr>
      <tr>
        <td>2.</td><td>Capability of supplementary Class Teachers (High, Medium, Low)</td><td>{$teacher_level}</td>
      </tr>
      <tr>
        <td>3.</td><td>Improvement potential for supplementary class teachers (High, Medium, Low)</td><td>{$supp_teacher_level}</td>
      </tr>
       <tr>
        <td colspan='3'>&nbsp;</td>
      </tr>
      <tr>
        <td colspan='3'><strong>Solution</strong></td>
      </tr>
      <tr>
        <td>1.</td><td>Do you require any external assistance</td><td>{$solution1}</td>
      </tr>
      <tr>
        <td>2.</td><td>Can LFW add value in your context?</td><td>{$solution2}</td>
      </tr>
      <tr>
        <td>a.</td><td>Expose the teacher to new techniques</td><td>{$solution3}</td>
      </tr>
      <tr>
        <td>b.</td><td>Enable teachers to teach English</td><td>{$solution4}</td>
      </tr>
    </table>
    ";

    set_transient('lfw_success_message', $success_message, 10);
  }

}
