/**
 * JavaScript code for com.joineryhq.relationshipjobtitle extension.
 */
CRM.$(document).ready(function($){
  // pageRunId:
  // The list of job titles is keyed to a unique identifier for each page load.
  // This is required because on AJAX page loads, CRM_Core_Resources::singleton()->addVars()
  // may or may not correctly update the values of objects in CRM.vars. Rather
  // than storing job titles in something like CRM.vars.relationshipjobtitle.relationshipJobTitles,
  // and then trying to modify the values of that object, we store job titles
  // in someting like CRM.vars.relationshipjobtitle.relationshipJobTitles.UNIQUE,
  // where UNIQUE is a unique string per page load. The current value of UNIQUE
  // can be found in CRM.vars.relationshipjobtitle.pageRunId.  This way, each
  // new page load creates a new property of the CRM.vars.relationshipjobtitle.relationshipJobTitles
  // object, and we don't care if it leaves the old property in place.
  var pageRunId = CRM.vars.relationshipjobtitle.pageRunId

  // Monitor relationships table for re-drawing by DataTables; we can't simply
  // use $.ready() here, because DataTables re-draws the table on every page
  // load, after (or as part of) $.ready().
  CRM.$('table.crm-contact-relationship-selector-current').on( 'draw.dt', function (e, settings) {
    var jobTitles = CRM.vars.relationshipjobtitle.relationshipJobTitles[pageRunId]
    for (i in jobTitles) {
      // For CiviCRM 4.6
      if (CRM.vars.relationshipjobtitle.civiMinorVersion == '4.6') {
        var tdSelector = 'td.crm-contact-relationship-type';
        var trId = 'relationship-'+ i;
      }
      // For CiviCRM 4.7
      else if (CRM.vars.relationshipjobtitle.civiMinorVersion == '4.7') {
        var tdSelector = 'td:first-child';
        var trId = i;
      }
      $(this).find('tr#' + trId + '>' + tdSelector + ' div.relationshipjobtitle-jobtitle').remove();
      $(this).find('tr#' + trId + '>'+ tdSelector).append('<div class="relationshipjobtitle-jobtitle"><em>(' + jobTitles[i] + ')</em></div>')
    }
  });
});
