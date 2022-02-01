# Bootstrap 4 theme

## Features:

* Includes Bootstrap 4 CDN (4.0 to 4.3)
* Includes Bootstrap 4 breakpoints
* Bootstrap controls within user interface
* No subtheme mode (unless template override required)

## SASS compilation:

* SASS compilation is no longer in the theme.
* Use [Bootstrap4 Tools](https://www.drupal.org/project/bootstrap4_tools) module

## Installation

### Using composer

`composer require drupal/bootstrap4`

## Subtheme

* If you require subtheme (usually if you want to override templates), 
    see [subtheme docs](_SUBTHEME/README.md).

* You can create subtheme by running `bash bin/subtheme.sh [name] [path]`,
    e.g. `bash bin/subtheme.sh b4subtheme ..`
