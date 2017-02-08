# d8_import_multiple_configfiles
Drupal 8 Module to load multiple .yml config files in a working drupal 
(this module is based on config_devel_import_multiple8.x-1.x-dev https://www.drupal.org/project/config_devel_import_multiple )




# Instructions
## Export full site configuration
- (in your "source" drupal) First go here : http://localhost/drupal8/admin/config/development/configuration/full/export
- and press "EXPORT"
- This will create a compressed file with your current site configuration.
- From the compressed file, export the .yml files which contains the configuration(s) you want.
- If you want, you can remove the line UUID (eg uuid: 0f185d3c-27f9-4a75-bf64-64924e3eda79) from all your files

## Import using this module
- Put your .yml files in the folder (drupal8-REAL path)/module/**this_module_name>**/import_these
- Install and activate this module
- Navigate to eg http://localhost/drupal8/admin/config/development/**config_devel_import_multiple**  (issue config_devel_import_multiple - should change name in the future)
- Enter the filenames you want to import in the "import" text area
- press "save configuration"
- "tick" the checkbox under the text area
- press "save configuration"
- Refresh drupal 8 page (eg by pressing F5 in your browser)
- If all went ok your config is imported
- ****"untick" the checkbox under the text area ***
- press "save configuration"

IMPORTANT : uncheck the checkbox. The last step is VERY IMPORTANT!!! If you leave it checked it will re-read the file in each drupal 8 site reload.

