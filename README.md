# wordpress-custom-theme-options

This is a heavily modified and updated version of:
WordPess Tabbed Theme Options Page by Bradley Stookey (Origibnally created by Justin Whall)
https://github.com/bstookey/wordpress-custom-theme-options/tree/develop

== How to use? ==
This can be used as a plugin or....

1. Copy the theme-options folder and all containing CSS, JS files to to you theme directory.
2. Include your theme options class file in your functions.php file "require_once ( \_\_DIR\_\_ . '/theme-options/cust-options.php' );"
3. Initiate the class in your functions file $theme_options = new Cust_Theme_Options();
4. Make sure file paths are all correct within your theme options file to supporting scripts and style sheets.

== Notes? ==

1. Upload inputs use the built in thickbox and media uploader scripts
2. Any input with the class "color" will call JScolor script. See the get_settings color arrays.
