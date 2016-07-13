/**
 * JavaScript code for com.joineryhq.relationshipjobtitle extension.
 */

CRM.$(document).ready(function($){
  // Monitor relationships tables for re-drawing by DataTables
  CRM.$('table.crm-contact-relationship-selector-past, table.crm-contact-relationship-selector-current').on( 'draw.dt', function (e, settings) {
    // Only perform these actions for the "Current" relationships table.
    if ($(this).hasClass('crm-contact-relationship-selector-current')) {
      for (i in CRM.vars.relationshipjobtitle.relationship_job_titles) {
        var trId = 'relationship-'+ i
        $(this).find('tr#' + trId + '>td.crm-contact-relationship-type').append('<div><em>(' + CRM.vars.relationshipjobtitle.relationship_job_titles[i] + ')</em></div>')
      }
    }
  });
});
