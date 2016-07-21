/**
 * JavaScript code for com.joineryhq.relationshipjobtitle extension.
 */
CRM.$(document).ready(function($){
  // Monitor relationships table for re-drawing by DataTables; we can't simply
  // use $.ready() here, because DataTables re-draws the table on every page
  // load, after (or as part of) $.ready().
  CRM.$('table.crm-contact-relationship-selector-current').one( 'draw.dt', function (e, settings) {
    for (i in CRM.vars.relationshipjobtitle.relationship_job_titles) {
      var trId = 'relationship-'+ i
      $(this).find('tr#' + trId + '>td.crm-contact-relationship-type div.relationshipjobtitle-jobtitle').remove();
      $(this).find('tr#' + trId + '>td.crm-contact-relationship-type').append('<div class="relationshipjobtitle-jobtitle"><em>(' + CRM.vars.relationshipjobtitle.relationship_job_titles[i] + ')</em></div>')
    }
    // Empty the relationship_job_titles object, now that it has been used.
    // Otherwise, the stale contents of this object may populate job titles
    // incorrectly when the page is "loaded" via AJAX, e.g., when editing
    // a relationship.
    CRM.vars.relationshipjobtitle.relationship_job_titles = {}
  });
});
