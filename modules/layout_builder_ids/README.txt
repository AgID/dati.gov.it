CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration

INTRODUCTION
------------

Layout Builder Ids allows site builders to enter and ID to be used with either a section or block in layout builder. A "style" is just a representation of one or more CSS classes that will be applied. This will then allow for things like anchor links and javascript to target very specific blocks and sections.

 * For a full description of the module, visit the project page:
   https://www.drupal.org/project/layout_builder_ids/

 * To submit bug reports and feature suggestions, or track changes:
   https://www.drupal.org/project/issues/layout_builder_ids/


REQUIREMENTS
------------

* This module requires, at minimum, Drupal 8.8.0.
* This module requires that layout builder from core be enabled.
* This module requires hook_event_dispatcher (1.x branch) to be enabled.


INSTALLATION
------------

 * Install as you would normally install a contributed Drupal module. Visit
   https://www.drupal.org/docs/8/extending-drupal-8/installing-drupal-8-modules
   for further information.

CONFIGURATION
-------------

When placing a block or section into a layout, this module will add an option for an ID, where ids follow the protocol of https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/id.
