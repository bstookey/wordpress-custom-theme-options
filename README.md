# wordpress-custom-theme-options

This is a modified and updated version of:
WordPess Tabbed Theme Options Page by Justin Whall
https://github.com/justinwhall/WordPress-theme-options.git

== How to use? ==

1. Copy CSS, JS and theme options class files to to you theme directory. 
2. Include your theme options class file in your functions.php file "require_once ( __DIR__ . '/theme-options/cust-options.php' );"
3. Initiate the class in your functions file $theme_options = new Cust_Theme_Options();
4. Make sure file paths are all correct within your theme options file to supporting scripts and style sheets.

== Notes? ==

1. Upload inputs use the built in thickbox and media uploader scripts
2. Any input with the class "color" will call JScolor script. See the get_settings color arrays. 
