CONTENTS OF THIS FILE
---------------------
   
 * Introduction
 * Requirements
 * Recommended modules
 * Installation
 * Configuration
 * Troubleshooting
 * FAQ
 * Maintainers

INTRODUCTION
------------

This Watchdog Delete Filter module offers useful filters to selectively 
delete entries in the Watchdog provided by the Drupal core's Dblog module.
At the present time, two filters are provided: Type and Severity.

 * Why this module?

  The Dblog module from Drupal core provides an UI to delete ALL the 
  watchdog entries in the database (admin/reports/dblog/confirm), 
  without any kind of filtering. In some installations, there are
  repetitive errors and/or super-verbose modules that fill up the watchdog,
  displacing other significant errors. This module lets the user to clean
  the watchdog from these annoying entries, reducing the dblog table size.
  It is specially useful to clean dblog tables configured to keep more 
  than 100000 entries.

  This module will be deprecated if someday the watchdog entries will be
  made Drupal entities, since modules like Views Bulk Operations (VBO) 
  will then handle the watchdog cleaning operations more powerfully. See
  https://www.drupal.org/project/views_bulk_operations/issues/2994794


 * How it works?

   This module overrides the form (admin/reports/dblog/confirm) provided by 
   the core Dblog module, adding the type and the severity filters.

 * Drupal 9 readiness

 	 This module has been checked and passed the tests for correctness
 	 and Drupal 9 deprecation errors provided by Drupal Check PHPStan tool:
 	 https://github.com/mglaman/drupal-check

 * For a full description of the module, visit the project page:
   https://www.drupal.org/project/watchdog_delete_filter

 * To submit bug reports and feature suggestions, or track changes:
   https://www.drupal.org/project/issues/watchdog_delete_filter


REQUIREMENTS
------------

This module requires the following module:

 * Dblog (from Drupal core)


RECOMMENDED MODULES
-------------------

 * Chosen (https://www.drupal.org/project/chosen):
   Chosen uses the Chosen jQuery plugin to make your multi <select> elements 
   more user-friendly.


INSTALLATION
------------

Install the Watchdog Delete Filter module as you would normally install a 
contributed Drupal module. Visit https://www.drupal.org/node/1897420 for further 
information.


CONFIGURATION
-------------

No configuration required. Just visit admin/reports/dblog/confirm to start
using the module.


SIMILAR MODULES
---------------

 * Watchdog Prune (https://www.drupal.org/project/watchdog_prune):
   It allows you to selectively delete watchdog entries based on criteria, like 
   age. The module lets you configure the cleaning process as a cron job.  
   Conversely, Watchdog Delete Filter is a simpler module: it does not handle 
   entries age and it requires manual intervention for cleaning.

MAINTAINERS
-----------

Current maintainers:

 * Gonzalo Torrevejano (interdruper) - https://www.drupal.org/u/interdruper

This module was created and sponsored by Interdruper, a Madrid (Spain) based
company that provide specialist consulting, auditing and development services 
in Enterprise Drupal.

 * Interdruper - https://www.interdruper.com/
