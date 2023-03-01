<?php

/**
 * Custom theme class
 *
 * @package Wordpress
 * @since 1.0
 */

$cust_theme_settings = new Cust_Theme_Options();

class Cust_Theme_Options
{

    private $sections;
    private $checkboxes;
    private $settings;

    /**
     * Construct
     *
     * @since 1.0
     */
    public function __construct()
    {

        // This will keep track of the checkbox options for the validate_settings function.
        $this->checkboxes = array();
        $this->settings = array();
        $this->get_settings();

        $this->sections['general'] = __('General');
        $this->sections['alertbar'] = __('Alert Bar');
        $this->sections['codes'] = __('Codes');
        $this->sections['examples'] = __('Examples');
        //$this->sections['reset'] = __('Reset to Defaults');
        //$this->sections['about'] = __('About');

        add_action('admin_menu', array(
            &$this,
            'add_pages'
        ));
        add_action('admin_init', array(
            &$this,
            'register_settings'
        ));

        if (!get_option('cust_theme_options')) $this->initialize_settings();
    }

    /**
     * Add options page
     *
     * @since 1.0
     */
    public function add_pages()
    {

        $admin_page = add_menu_page(__('Theme Options'), __('Theme Options'), 'manage_options', 'cust-theme-options', array(
            &$this,
            'display_page'
        ));

        add_action('admin_print_scripts-' . $admin_page, array(
            &$this,
            'scripts'
        ));
        add_action('admin_print_styles-' . $admin_page, array(
            &$this,
            'styles'
        ));
    }

    /**
     * Create settings field
     *
     * @since 1.0
     */
    public function create_setting($args = array())
    {

        $defaults = array(
            'id' => 'default_field',
            'title' => __('Default Field'),
            'desc' => __('This is a default description.'),
            'std' => '',
            'type' => 'text',
            'section' => 'general',
            'choices' => array(),
            'label_for' => '',
            'class' => ''
        );

        extract(wp_parse_args($args, $defaults));

        $field_args = array(
            'id' => $id,
            'desc' => $desc,
            'std' => $std,
            'type' => $type,
            'choices' => $choices,
            'label_for' => $id,
            'class' => $class
        );

        if ($type == 'checkbox') $this->checkboxes[] = $id;

        add_settings_field($id, $title, array(
            $this,
            'display_setting'
        ), 'cust-theme-options', $section, $field_args);
    }

    /**
     * Display options page
     *
     * @since 1.0
     */
    public function display_page()
    {

        echo '<div class="wrap">
		<div class="icon32" id="icon-options-general"></div>
		<h2>' . __('Theme Options') . '</h2>';

        if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == true) echo '<div class="updated fade"><p>' . __('Theme options updated.') . '</p></div>';

        echo '<form action="options.php" method="post">';

        settings_fields('cust_theme_options');

        echo '<div class="ui-tabs">
				<ul class="ui-tabs-nav">';

        foreach ($this->sections as $section_slug => $section) echo '<li><a id="' . $section . '"href="#' . $section_slug . '">' . $section . '</a></li>';

        echo '</ul>';
        do_settings_sections($_GET['page']);

        echo '</div>';

        echo '<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . __('Save Changes') . '" /></p></form>';

        echo '<script type="text/javascript">
					jQuery(document).ready(function($) {

						$("a").click(function(){
						$(".submit").show();
					});

					$("#About").click(function(){
						$(".submit").hide();
					});

					var sections = [];';

        foreach ($this->sections as $section_slug => $section) echo "sections['$section'] = '$section_slug';";

        echo 'var wrapped = $(".wrap h2").wrap("<div class=\"ui-tabs-panel\">");
							wrapped.each(function() {
								$(this).parent().append($(this).parent().nextUntil("div.ui-tabs-panel"));
							});

							$(".ui-tabs-panel").each(function(index) {
								$(this).attr("id", sections[$(this).children("h2").text()]);
								if (index > 0)
								$(this).addClass("ui-tabs-hide");
							});

							$(".ui-tabs").tabs({
								fx: { opacity: "toggle", duration: "fast" }
							});

							$("input[type=text], textarea").each(function() {
								if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "")
									$(this).css("color", "#999");
							});

							$("input[type=text], textarea").focus(function() {
								if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "") {
									$(this).val("");
									$(this).css("color", "#000");
								}
							}).blur(function() {
								if ($(this).val() == "" || $(this).val() == $(this).attr("placeholder")) {
									$(this).val($(this).attr("placeholder"));
									$(this).css("color", "#999");
								}
							});

							$(".wrap h2, .wrap table").show();

							// This will make the "warning" checkbox class really stand out when checked.
							// I use it here for the Reset checkbox.
							$(".warning").change(function() {
								if ($(this).is(":checked"))
									$(this).parent().css({"background" : "#f4cccc","color" : "#fff !important", "fontWeight" : "bold"});
								else
									$(this).parent().css({"background" : "none","color" : "inherit", "fontWeight" : "normal"});
							});

						});

					</script>
				</div>';
    }

    /**
     * Description for section
     *
     * @since 1.0
     */
    public function display_section()
    {
    }

    /**
     * Description for About section
     *
     * @since 1.0
     */
    public function display_about_section()
    {
        // This displays on the "About" tab. Echo regular HTML here, like so:
        echo ('
			<table>
		        <tbody>
		                <tr>
		                        <td>Add some copy/directions here to help users with the Custom Theme settings.</td>
		                </tr>
		                <tr>
		                        <td>If you have a documentation page you can direct them to a <a href="#" target="" >Custom Options Documentation</a> page.</td>
		                </tr>
		        </tbody>
			</table>');
        //echo '<p>Copyright 2023 info@inverseparadox.com</p>';

    }
    /**
     * HTML output for text field
     *
     * @since 1.0
     */
    public function display_setting($args = array())
    {

        extract($args);

        $options = get_option('cust_theme_options');

        if (!isset($options[$id]) && $type != 'checkbox') $options[$id] = $std;
        elseif (!isset($options[$id])) $options[$id] = 0;

        $field_class = '';
        if ($class != '') $field_class = ' ' . $class;

        switch ($type) {

            case 'heading':
                echo '</td></tr><tr><td colspan="2" style="padding-left: 0 !important;"><h4>' . $desc . '</h4>';
                break;

            case 'checkbox':

                echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="cust_theme_options[' . $id . ']" value="1" ' . checked($options[$id], 1, false) . ' /> <label for="' . $id . '">' . $desc . '</label>';

                break;

            case 'select':
                echo '<select class="select' . $field_class . '" name="cust_theme_options[' . $id . ']">';

                foreach ($choices as $value => $label) echo '<option value="' . esc_attr($value) . '"' . selected($options[$id], $value, false) . '>' . $label . '</option>';

                echo '</select>';

                if ($desc != '') echo '<br /><span class="description">' . $desc . '</span>';

                break;

            case 'radio':
                $i = 0;
                foreach ($choices as $value => $label) {
                    echo '<input class="radio' . $field_class . '" type="radio" name="cust_theme_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr($value) . '" ' . checked($options[$id], $value, false) . '> <label for="' . $id . $i . '">' . $label . '</label>';
                    if ($i < count($options) - 1) echo '<br />';
                    $i++;
                }

                if ($desc != '') echo '<br /><span class="description">' . $desc . '</span>';

                break;

            case 'textarea':
                echo '<textarea class="' . $field_class . '" id="' . $id . '" name="cust_theme_options[' . $id . ']" placeholder="' . $std . '" rows="5" cols="30">' . format_for_editor($options[$id]) . '</textarea>';

                if ($desc != '') echo '<br /><span class="description">' . $desc . '</span>';

                break;

            case 'password':
                echo '<input class="regular-text' . $field_class . '" type="password" id="' . $id . '" name="cust_theme_options[' . $id . ']" value="' . esc_attr($options[$id]) . '" />';

                if ($desc != '') echo '<br /><span class="description">' . $desc . '</span>';

                break;

            case 'text':
                //default:
                echo '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="cust_theme_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr($options[$id]) . '" />';

                if ($desc != '') echo '<br /><span class="description">' . $desc . '</span>';

                break;

            case 'date':
                //default:
                echo '<input class="date-pick' . $field_class . '" type="text" id="' . $id . '" name="cust_theme_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr($options[$id]) . '" />';

                if ($desc != '') echo '<br /><span class="description">' . $desc . '</span>';

                break;

            case 'upload':
                //default:
                if (($options[$id] != '') && ($options[$id] != $std)) :
                    $image = wp_get_attachment_image_url($options[$id], 'thumbnail');  ?>
                    <a href="#" class="image-upload">
                        <img src="<?php echo esc_url($image) ?>" />
                    </a>
                    <a href="#" class="image-remove">Remove image</a>
                    <input class="upload-url <?= $field_class ?>" type="hidden" id="<?= $id ?>" name="cust_theme_options[<?= $id ?>]" value="<?= esc_attr($options[$id]) ?>" />

                <?php else : ?>
                    <a href="#" class="button image-upload">Upload image</a>
                    <a href="#" class="image-remove" style="display:none">Remove image</a>
                    <input class="upload-url <?= $field_class ?>" type="hidden" id="<?= $id ?>" name="cust_theme_options[<?= $id ?>]" placeholder="<?= $std ?>" value="<?= esc_attr($options[$id]) ?>" />
                <?php endif; ?>
<?php break;
        }
    }

    /**
     * Settings and defaults
     *
     * @since 1.0
     */
    public function get_settings()
    {

        /* Global Settings
             ===========================================*/


        // Return an array of all available posts/pages

        if (!function_exists('select_posts')) {
            function select_posts($type = 'post')
            {

                $posts = get_posts(
                    array(
                        'post_type' => $type, // select post type post
                        'numberposts' => -1,
                    )
                );

                $post_array['0'] = 'Select Post/Page';
                foreach ($posts as $post) {
                    $post_array[$post->ID] = $post->post_title;
                }

                return $post_array;
            }
        }

        $today = date('m/d/Y');

        // General Section

        $this->settings['address_heading'] = array(
            'section' => 'general',
            'title' => '', // Not used for headings.
            'desc' => 'Contact Information',
            'type' => 'heading'
        );

        $this->settings['org_address'] = array(
            'title' => __('Address'),
            'type' => 'text',
            'desc' => __(''),
            'section' => 'general'
        );

        $this->settings['org_address2'] = array(
            'title' => __('Address 2'),
            'type' => 'text',
            'desc' => __(''),
            'section' => 'general'
        );

        $this->settings['org_city'] = array(
            'title' => __('City'),
            'type' => 'text',
            'desc' => __(''),
            'section' => 'general'
        );

        $this->settings['org_state'] = array(
            'title' => __('State'),
            'type' => 'text',
            'desc' => __(''),
            'section' => 'general'
        );

        $this->settings['org_zip'] = array(
            'title' => __('Postal Code'),
            'type' => 'text',
            'desc' => __(''),
            'section' => 'general'
        );

        $this->settings['org_country'] = array(
            'title' => __('Country'),
            'type' => 'text',
            'desc' => __(''),
            'section' => 'general',
            'std' => 'USA'
        );

        $this->settings['org_phone'] = array(
            'title' => __('Phone'),
            'type' => 'text',
            'desc' => __(''),
            'section' => 'general'
        );

        $this->settings['org_fax'] = array(
            'title' => __('Fax'),
            'type' => 'text',
            'desc' => __(''),
            'section' => 'general'
        );

        $this->settings['org_email'] = array(
            'title' => __('Email'),
            'type' => 'text',
            'desc' => __(''),
            'section' => 'general'
        );

        $this->settings['org_url'] = array(
            'title' => __('Web Address'),
            'type' => 'text',
            'desc' => __(''),
            'section' => 'general'
        );

        $this->settings['copyright_text'] = array(
            'title' => __('Copyright Text'),
            'desc' => __('Enter the copyright text to be displyed in the footer. Do not include date'),
            'std' => 'All rights reserved',
            'type' => 'textarea',
            'section' => 'general'
        );

        /* Alert Bar
						==========================================*/
        $this->settings['display_alertbar'] = array(
            'section' => 'alertbar',
            'title' => __('Display Alert Banner'),
            'desc' => __('Check this box if you would like show an alert banner.'),
            'type' => 'checkbox',
            'std' => 1
            // Set to 1 to be checked by default, 0 to be unchecked by default.
        );

        $this->settings['alertbar_id'] = array(
            'title' => __('Alert Bar Cookie'),
            'type' => 'text',
            'desc' => __('Change the name of this cookie when the Alert Bar is changed. NOTE: no spaces, the default is alertAccept.'),
            'section' => 'alertbar',
            'std' => 'alertAccept'
        );

        $this->settings['alertbar_cookie_time'] = array(
            'title' => __('Cookie Length'),
            'type' => 'text',
            'desc' => __('How many days should the user be able to hide the Alert Bar?'),
            'section' => 'alertbar',
            'std' => '7'
        );

        $this->settings['alertbar_copy'] = array(
            'section' => 'alertbar',
            'title' => __('Alert Bar Copy'),
            'desc' => __('The text displayed in the cookie bar.'),
            'type' => 'textarea',
            'std' => '',
        );

        $this->settings['alertbar_link'] = array(
            'section' => 'alertbar',
            'title' => __('Alert Page'),
            'desc' => __('Select the page for cookie policy'),
            'type' => 'select',
            'std' => '',
            'choices' => select_posts(array('post', 'page')),
        );

        $this->settings['alertbar_link_text'] = array(
            'title' => __('CTA Text'),
            'type' => 'text',
            'desc' => __(''),
            'section' => 'alertbar'
        );

        /* Reset
             ===========================================*/

        $this->settings['reset_theme'] = array(
            'section' => 'reset',
            'title' => __('Reset theme options'),
            'type' => 'checkbox',
            'std' => 0,
            'class' => 'warning', // Custom class for CSS
            'desc' => __('Check this box and click "Save Changes" below to reset theme options to their defaults.')
        );

        /* Codes
             ===========================================*/

        $this->settings['header_code'] = array(
            'section' => 'codes',
            'title' => __('Header Scripts'),
            'desc' => __('These scripts apperar just before the &lt;/head&gt; tag. EG: Google Analytics, TypeKit '),
            'type' => 'textarea',
            'std' => '',
        );

        $this->settings['body_code'] = array(
            'section' => 'codes',
            'title' => __('Body Scripts'),
            'desc' => __('These scripts appear just after &lt;body&gt; tag.'),
            'type' => 'textarea',
            'std' => '',
        );

        /* Examples
        ================================================*/

        $this->settings['example_text'] = array(
            'title'   => __('Example Text Input'),
            'desc'    => __('This is a description for the text input.'),
            'std'     => 'Default value',
            'type'    => 'text',
            'section' => 'general'
        );


        $this->settings['example_textarea'] = array(
            'title'   => __('Example Textarea Input'),
            'desc'    => __('This is a description for the textarea input.'),
            'std'     => 'Default value',
            'type'    => 'textarea',
            'section' => 'general'
        );

        $this->settings['date1'] = array(
            'title'   => __('Example DatePicker Input'),
            'desc'    => __('This is a description for the date picker.'),
            'std'     => $today,
            'type'    => 'date',
            'section' => 'examples'
        );

        $this->settings['example_checkbox'] = array(
            'section' => 'examples',
            'title'   => __('Example Checkbox'),
            'desc'    => __('This is a description for the checkbox.'),
            'type'    => 'checkbox',
            'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
        );

        $this->settings['example_radio'] = array(
            'section' => 'examples',
            'title'   => __('Example Radio'),
            'desc'    => __('This is a description for the radio buttons.'),
            'type'    => 'radio',
            'std'     => '',
            'choices' => array(
                'choice1' => 'Choice 1',
                'choice2' => 'Choice 2',
                'choice3' => 'Choice 3',
            )
        );

        $this->settings['example_heading'] = array(
            'section' => 'examples',
            'title'   => '', // Not used for headings.
            'desc'    => 'Example Heading',
            'type'    => 'heading'
        );

        $this->settings['page_example'] = array(
            'section' => 'examples',
            'title' => __('Select page example'),
            'desc' => __('Select'),
            'type' => 'select',
            'std' => '',
            'choices' => select_posts(array('page')), // include any post types or pages in the array
        );

        $this->settings['post_example'] = array(
            'section' => 'examples',
            'title' => __('Select post example'),
            'desc' => __('Select'),
            'type' => 'select',
            'std' => '',
            'choices' => select_posts(array('post')), // include any post types or pages in the array
        );

        $this->settings['ex_image'] = array(
            'title' => __('Example Image'),
            'desc' => __('Add the image url or click "Upload"'),
            'std' => 'Example Image',
            'type' => 'upload',
            'section' => 'examples',
        );

        $this->settings['example_color'] = array(
            'title' => __('Example Color'),
            'type' => 'text',
            'desc' => __(''),
            'class' => 'color',
            'section' => 'examples'
        );
    }

    /**
     * Initialize settings to their default values
     *
     * @since 1.0
     */
    public function initialize_settings()
    {

        $default_settings = array();
        foreach ($this->settings as $id => $setting) {
            if ($setting['type'] != 'heading') $default_settings[$id] = $setting['std'];
        }

        update_option('cust_theme_options', $default_settings);
    }

    /**
     * Register settings
     *
     * @since 1.0
     */
    public function register_settings()
    {

        register_setting('cust_theme_options', 'cust_theme_options', array(
            &$this,
            'validate_settings'
        ));

        foreach ($this->sections as $slug => $title) {
            if ($slug == 'about') add_settings_section($slug, $title, array(
                &$this,
                'display_about_section'
            ), 'cust-theme-options');
            else add_settings_section($slug, $title, array(
                &$this,
                'display_section'
            ), 'cust-theme-options');
        }

        $this->get_settings();

        foreach ($this->settings as $id => $setting) {
            $setting['id'] = $id;
            $this->create_setting($setting);
        }
    }

    /**
     * Scripts for the theme options page
     *
     * @since 1.0
     */
    public function scripts()
    {
        if (is_admin()) {

            wp_print_scripts('jquery-ui-tabs');
            wp_enqueue_script('colorPicker.js', get_stylesheet_directory_uri() . '/inc/theme-options/js/colorPicker.js'); //color picker for hex value fields
            wp_enqueue_script('datePicker.js', get_stylesheet_directory_uri() . '/inc/theme-options/js/datePicker.js'); //date picker.
            wp_enqueue_script('iniFunctions.js', get_stylesheet_directory_uri() . '/inc/theme-options/js/iniFunctions.js', array(
                'jquery'
            ), null, false); //date picker.
            //Media Uploader scripts
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
            wp_register_script('cust-upload', get_stylesheet_directory_uri() . '/inc/theme-options/js/uploader.js', array(
                'jquery',
                'media-upload',
                'thickbox'
            ));
            wp_enqueue_script('cust-upload');
        }
    }

    /**
     * Styling for the theme options page
     *
     * @since 1.0
     */
    public function styles()
    {
        if (is_admin()) {

            wp_register_style('cust-theme-admin-options',  get_stylesheet_directory_uri() . '/inc/theme-options/css/cust-theme-options.css');
            wp_register_style('cust-theme-datepicker',  get_stylesheet_directory_uri() . '/inc/theme-options/css/datePicker.css');
            wp_register_style('cust-color-picker',  get_stylesheet_directory_uri() . '/inc/theme-options/css/colorPicker.css');
            wp_enqueue_style('cust-theme-admin-options');
            wp_enqueue_style('cust-theme-datepicker');
            wp_enqueue_style('cust-color-picker');
            wp_enqueue_style('thickbox');
        }
    }

    /**
     * Validate settings
     *
     * @since 1.0
     */
    public function validate_settings($input)
    {

        if (!isset($input['reset_theme'])) {
            $options = get_option('cust_theme_options');

            foreach ($this->checkboxes as $id) {
                if (isset($options[$id]) && !isset($input[$id])) unset($options[$id]);
            }

            return $input;
        }
        return false;
    }
}

function cust_theme_option($option)
{
    $options = get_option('cust_theme_options');
    if (isset($options[$option])) return $options[$option];
    else return false;
}

function load_media_files()
{
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'load_media_files');

?>