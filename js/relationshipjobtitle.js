/**
 * JavaScript code for com.joineryhq.relationshipjobtitle extension.
 */

CRM.$(document).ready(function($){
  // Monitor relationships tables for re-drawing by DataTables
  CRM.$('table.crm-contact-relationship-selector-past, table.crm-contact-relationship-selector-current').on( 'draw.dt', function (e, settings) {
    // Only perform these actions for the "Current" relationships table.
    if ($(this).hasClass('crm-contact-relationship-selector-current')) {
      // Loop through all rows in the table
      CRM.$(settings.nTable).find('tbody tr').each(function(idx, tr){
        // Perform this action on the row with the correct id attribute.
        if (tr.id == 'relationship-'+ CRM.vars.relationshipjobtitle.current_employer_relationship_id) {
          // Append the job title in the "Relationship" column.
          CRM.$(tr).find('td.crm-contact-relationship-type').append('<div><em>(' + CRM.vars.relationshipjobtitle.job_title + ')</em></div>');
        }
      })
    }
  });
});
