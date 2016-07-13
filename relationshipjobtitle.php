<?php

require_once 'relationshipjobtitle.civix.php';

/**
 * Get the value of the given config setting.
 */
function _relationshipjobtitle_get_setting($name) {
  // If this is the  first time, prime a $settings array with the default values,
  // overridden with any values found by CRM_Core_BAO_Setting::getItem().
  static $settings = array();
  if (empty($settings)) {
    $defaults = array(
      'apply_all_employee_relationships' => FALSE,
    );

    $config = CRM_Core_Config::singleton();


    foreach ($defaults as $key => $value) {
      $config_value = CRM_Core_BAO_Setting::getItem('relationshipjobtitle', $key);
      if (!is_null($config_value)) {
        $settings[$key] = $config_value;
      }
    }
    $settings = array_replace_recursive($defaults, $settings);

    // If the setting is still unset, set it from CRM_Core_BAO_Setting::getItem().
    if (!array_key_exists($name, $settings)) {
      $settings[$name] = CRM_Core_BAO_Setting::getItem('relationshipjobtitle', $key);
    }
  }
  return $settings[$name];
}


/**
 * Implementation of hook_civicrm_pageRun
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_pageRun
 */
function relationshipjobtitle_civicrm_pageRun(&$page) {
  // Only take action on the Relationships tab.
  if ($page->getVar('_name') == 'CRM_Contact_Page_View_Relationship'){
    $relationship_job_titles = array();
    
    // Get relationship type ID.
    $api_params = array(
      'name_a_b' => 'Employee of',
      'sequential' => 1,
    );
    $relationship_type_result = civicrm_api3('RelationshipType', 'get', $api_params);
    if (empty($relationship_type_result['id'])) {
      CRM_Core_Error::debug_log_message('relationshipjobtitle: Unable to find "Employee of" relationship type.');
      return;
    }
    $relationship_type_id = $relationship_type_result['id'];

    // Get relevant contact details
    $api_params = array(
      'id' => $page->_contactId,
      'sequential' => 1,
      'return' => array(
        'current_employer_id',
        'job_title',
        'contact_type',
      ),
    );
    $contact_result = civicrm_api3('contact', 'get', $api_params);

    // If contact is Individual
    switch ($contact_result['values'][0]['contact_type']) {
      case 'Individual':
        // Only take action if the contact has both a current employer and a job title.
        if (!empty($contact_result['values'][0]['current_employer_id']) && !empty($contact_result['values'][0]['job_title'])) {
          // Get the ID of the current employee relationship.
          $api_params = array(
            'relationship_type_id' => $relationship_type_id,
            'contact_id_a' => $page->_contactId,
            'contact_id_b' => $contact_result['values'][0]['current_employer_id'],
            'is_active' => 1,
          );
          $relationship_result = civicrm_api3('relationship', 'get', $api_params);
          if (!empty($relationship_result['id'])) {
            // If the relationship is found, add the JavaScript file, and assign
            // relevant variables in JavaScript scope.
            $relationship_job_titles[$relationship_result['id']] = $contact_result['values'][0]['job_title'];
          }
        }
        break;

      case 'Organization':
        // Get all current employee relationships
        $api_params = array(
          'relationship_type_id' => $relationship_type_id,
          'is_active' => 1,
          'sequential' => 1,
        );
        $relationships_result = civicrm_api3('relationship', 'get', $api_params);
        foreach ($relationships_result['values'] as $value) {
          // Get individual contact for each relationship
          $api_params = array(
            'id' => $value['contact_id_a'],
            'sequential' => 1,
            'return' => array(
              'current_employer_id',
              'job_title',
              'contact_type',
            ),
          );
          $individual_result = civicrm_api3('contact', 'get', $api_params);
          // Ignore if organization is not current_employer_id
          if ($individual_result['values'][0]['current_employer_id'] == $page->_contactId && !empty($individual_result['values'][0]['job_title'])) {
            $relationship_job_titles[$value['id']] = $individual_result['values'][0]['job_title'];
          }
        }


        break;
    }

    if (!empty($relationship_job_titles)) {
      CRM_Core_Resources::singleton()->addScriptFile('com.joineryhq.relationshipjobtitle', 'js/relationshipjobtitle.js');
      $js_vars = array(
        'relationship_job_titles' => $relationship_job_titles,
      );
      CRM_Core_Resources::singleton()->addVars('relationshipjobtitle', $js_vars);
    }

  }
}

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function relationshipjobtitle_civicrm_config(&$config) {
  _relationshipjobtitle_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function relationshipjobtitle_civicrm_xmlMenu(&$files) {
  _relationshipjobtitle_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function relationshipjobtitle_civicrm_install() {
  return _relationshipjobtitle_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function relationshipjobtitle_civicrm_uninstall() {
  return _relationshipjobtitle_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function relationshipjobtitle_civicrm_enable() {
  return _relationshipjobtitle_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function relationshipjobtitle_civicrm_disable() {
  return _relationshipjobtitle_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function relationshipjobtitle_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _relationshipjobtitle_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function relationshipjobtitle_civicrm_managed(&$entities) {
  return _relationshipjobtitle_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function relationshipjobtitle_civicrm_caseTypes(&$caseTypes) {
  _relationshipjobtitle_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function relationshipjobtitle_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _relationshipjobtitle_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
