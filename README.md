# Relationship Job Title

Install this extension to display, on the Contact Relationships tab, the contact's Job Title together with the current employer.


## Optional configuration

No configuration is required.

Just install and go.  

Really.


But if you want to be special, try defining these configurations to civicrm.settings.php:

### limit_to_current_employer
Boolean; default TRUE.

By default, this extension will only show the job title for a relationship if
the organization is the individual's current employer, since that's usually the
expected meaning of the Job Title field. But if you want to force the job title
to appear on all current employer/employee relationships for an individual, set
this to TRUE, like so:
```php
global $civicrm_setting;
$civicrm_setting['com.joineryhq.relationshipjobtitle']['limit_to_current_employer'] = FALSE;
```

### relationship_type_ids
Array; default array([ID of employer/employee relationship type])

By default, this extension only shows the job title on employer/employee
relationships. But if you want to show it for other relationship types (for
example, maybe you have a "Contractor for" relationship that you keep separate
from employer/employee), use this configuration. You'll need to find the
system IDs of the relationship types you want this to work for (including
employer/employee if you want to keep using that). So for example, say you have
these relevant relationship type IDs:

* Employer/employee: 5
* Contractor for/Has contractor: 23

The you would add this to civicrm.settings.php:
```php
global $civicrm_setting;
$civicrm_setting['com.joineryhq.relationshipjobtitle']['relationship_type_ids'] = array (
  5,   // Employee
  23,  // Contractor
);
```
