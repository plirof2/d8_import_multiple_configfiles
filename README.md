# d8_import_multiple_configfiles
Drupal 8 Module to load multiple .yml config files (based on config_devel8.x-1.x-dev https://www.drupal.org/project/config_devel ) module )




# Instructions
## Export full site configuration
- First got here : http://localhost/drupal8/admin/config/development/configuration/full/export
- and press "EXPORT"
- This will create a compressed file with your current site configuration.
- From the compressed file, export the .yml files which contains the configuration you want.
- IF you want, you can remove the line UUID (eg uuid: 0f185d3c-27f9-4a75-bf64-64924e3eda79) from all your files

## Import using this module
- Install and activate this module
- Put your .yml files in the folder /module/<this_module_name>/import_these
- Enter the filenames you want to import in the "import" text area
- "tick" the checkbox under the text area
- Refresh drupal 8 page
- If all went ok your config is imported
- IMPORTANT : uncheck the checkbox. If you leave it checked it will re-read the file in each drupal 8 site reload.


