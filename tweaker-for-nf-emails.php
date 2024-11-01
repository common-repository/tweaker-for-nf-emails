<?php

/**
 * Plugin Name: Tweaker for Ninja Forms emails Premium monthly
 * Plugin URI: https://wordpress.org/plugins/tweaker-for-nf-emails/
 * Description: Let's tweak those raw emails
 * Version: 1.0.1
 * Author: RebootNow
 * @package tweakerForNFemails
 */

if ( !function_exists( 'tfne_fs' ) ) {
    // Create a helper function for easy SDK access.
    function tfne_fs()
    {
        global  $tfne_fs ;
        
        if ( !isset( $tfne_fs ) ) {
            // Activate multisite network integration.
            if ( !defined( 'WP_FS__PRODUCT_11594_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_11594_MULTISITE', true );
            }
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $tfne_fs = fs_dynamic_init( array(
                'id'             => '11594',
                'slug'           => 'tweaker-for-nf-emails',
                'type'           => 'plugin',
                'public_key'     => 'pk_223d340431c6da45a2bbed1db8f1f',
                'is_premium'     => false,
                'premium_suffix' => 'Premium',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                'days'               => 30,
                'is_require_payment' => false,
            ),
                'menu'           => array(
                'slug'    => 'admin-page-tweaker-for-nf-emails',
                'support' => false,
                'parent'  => array(
                'slug' => 'options-general.php',
            ),
            ),
                'is_live'        => true,
            ) );
        }
        
        return $tfne_fs;
    }
    
    // Init Freemius.
    tfne_fs();
    // Signal that SDK was initiated.
    do_action( 'tfne_fs_loaded' );
}

defined( 'ABSPATH' ) or die( "What are you doing here you silly human!" );

if ( !class_exists( 'TweakerForNFemails' ) ) {
    class TweakerForNFemails
    {
        public  $plugin ;
        function __construct()
        {
            // get plugin path
            $this->plugin = plugin_basename( __FILE__ );
        }
        
        function register()
        {
            // include scripts
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_dependency_files' ) );
            // backend
            // add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_dependency_files' ) ); // frontend
            // the settings page in the dashboard menu
            add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
            // the setting link in the plugins page
            add_filter( 'plugin_action_links_' . $this->plugin, array( $this, 'settings_link' ) );
            // plugin_action_links_NAME_OF_MY_PLUGIN
            // the email body tweaking
            add_filter(
                'ninja_forms_action_email_message',
                array( $this, 'tweak_email_message' ),
                10,
                3
            );
        }
        
        // Let's start working on it
        function tweak_email_message( $message, $data, $action_settings )
        {
            
            if ( isset( $data['fields'] ) && !empty($data['fields']) ) {
                // render style
                $form_tweaker_render_style = "list";
                // table or list (table not implemented yet)
                $htmlString = '';
                // debug
                // $htmlString .= json_encode($data) . "<hr>";
                // list style
                
                if ( $form_tweaker_render_style == "list" ) {
                    // let's build the table string
                    $htmlString .= '<table>';
                    // let's give numbers to questions - initializing
                    $form_tweaker_question_number = 1;
                    // parsing the form fields one by one
                    foreach ( $data['fields'] as $field ) {
                        if ( !isset( $field['value'] ) || empty($field['value']) ) {
                            continue;
                        }
                        // the field properties offered by Ninja Forms hook
                        $fieldType = $field['type'];
                        $fieldLabel = ( isset( $field['settings']['label'] ) ? $field['settings']['label'] : '' );
                        $fieldValue = $field['value'];
                        // flag (highlight) fields with yellow if needed
                        // okay color is white
                        $form_tweaker_highlightcolor = "#ffffff";
                        // white
                        // check if excluded id
                        // check if we need to highlight this question/answer combination
                        
                        if ( $this->shouldWeHighlight( $fieldType, $fieldLabel, $fieldValue ) ) {
                            $form_tweaker_highlightcolor = "#ffff00";
                            // yellow
                        }
                        
                        // display the question (label)
                        // or hide label if fieldType is html
                        
                        if ( strtolower( $fieldType ) != strtolower( "html" ) ) {
                            // conditionaltr background color
                            $htmlString .= "<tr bgcolor=" . $form_tweaker_highlightcolor . " style='background-color=" . $form_tweaker_highlightcolor . ";'>";
                            // add numbering prefix if the settings option is 1 (yes)
                            
                            if ( esc_html( get_option( 'ninja_tweaker_option_add_numbers' ) ) == 1 ) {
                                $htmlString .= "<td>" . $form_tweaker_question_number . ". " . $fieldLabel . "</td>";
                            } else {
                                // or no number prefix
                                $htmlString .= "<td>" . $fieldLabel . "</td>";
                            }
                            
                            $htmlString .= "</tr>";
                        }
                        
                        // display the answer (the value)
                        // no initial spacing if fieldType is html
                        if ( strtolower( $fieldType ) == strtolower( "html" ) ) {
                            $spacer_before_value = "";
                        }
                        // conditionaltr background color
                        $htmlString .= "<tr bgcolor=" . $form_tweaker_highlightcolor . " style='background-color=" . $form_tweaker_highlightcolor . ";'>";
                        $htmlString .= "<td>";
                        $spacer_before_value = "&nbsp;&nbsp;&nbsp;&nbsp;";
                        // echo the single value or the array of values
                        // sanitisation & validation handled already by Ninja Forms (we are in a hook)
                        
                        if ( is_array( $fieldValue ) ) {
                            foreach ( $fieldValue as $a ) {
                                $htmlString .= $spacer_before_value . $a . "</br>";
                            }
                        } else {
                            $htmlString .= $spacer_before_value . $fieldValue;
                        }
                        
                        $htmlString .= "</td>";
                        $htmlString .= "</tr>";
                        // increase question counter if not html field
                        if ( strtolower( $fieldType ) != strtolower( "html" ) ) {
                            $form_tweaker_question_number++;
                        }
                    }
                    $htmlString .= '</table>';
                }
            
            } else {
                $htmlString = '';
            }
            
            // this will be sent as the HTML email body, so let's escape even if Ninja Forms will probably do it again
            // return the modified message
            return wp_kses_post( $htmlString );
        }
        
        // SLAVE FUNCTIONS HERE
        function shouldWeHighlight( $myfieldtype, $mylabel, $myvalue )
        {
            // 1). skip if the field type is html
            if ( $myfieldtype == "html" ) {
                return false;
            }
            // 2). check if the question should be highlighted if the answer is "yes"
            // $yes_flags_array = array("Have any new problems occurred", "Have you found anything", "Are there any issues");
            $yes_flags_array_field_string = esc_html( get_option( 'ninja_tweaker_option_yes_text_flags' ) );
            // first check if the field is not empty and then convert to array
            
            if ( strlen( $yes_flags_array_field_string ) > 0 ) {
                // let's convert any commas to semicolumns first, just in case the user separated the ids with commas
                $yes_flags_array_field_string = str_replace( ",", ";", $yes_flags_array_field_string );
                $yes_flags_array = explode( ";", $yes_flags_array_field_string );
                if ( is_array( $yes_flags_array ) ) {
                    // check if the question contains flagging words
                    if ( $this->isPieceOfStringInArray( $mylabel, $yes_flags_array ) ) {
                        
                        if ( strtolower( $myvalue ) == strtolower( "yes" ) ) {
                            // if highlighted then DON'T continue with other rules
                            return true;
                        } else {
                            return false;
                        }
                    
                    }
                }
            }
            
            // 3). check if the answer is no, but skip if it's array
            // $negative_flags_array = array("no", "fail", "poor");
            $negative_flags_array_field_string = esc_html( get_option( 'ninja_tweaker_option_negative_flags' ) );
            // first check if the field is not empty and then convert to array
            
            if ( strlen( $negative_flags_array_field_string ) > 0 ) {
                // let's convert any commas to semicolumns first, just in case the user separated the ids with commas
                $negative_flags_array_field_string = str_replace( ",", ";", $negative_flags_array_field_string );
                $negative_flags_array = explode( ";", $negative_flags_array_field_string );
                // check if the answer contains flagging words
                if ( is_array( $negative_flags_array ) ) {
                    
                    if ( is_array( $myvalue ) ) {
                        // don't check in multiselects
                        return false;
                    } else {
                        if ( $this->isStringInArray( trim( $myvalue ), $negative_flags_array ) ) {
                            // if highlighted then DON'T continue with other rules
                            return true;
                        }
                    }
                
                }
            }
            
            // no highlighting rule passed, then return no highlight for this question/answer combination
            return false;
        }
        
        function isPieceOfStringInArray( $str, array $arr )
        {
            // validated for array before the call, as it only accepts arrays and it would crash
            foreach ( $arr as $a ) {
                if ( stripos( strtolower( $str ), strtolower( $a ) ) !== false ) {
                    return true;
                }
            }
            return false;
        }
        
        function isStringInArray( $str, array $arr )
        {
            // validated for array before the call, as it only accepts arrays and it would crash
            foreach ( $arr as $a ) {
                if ( strtolower( $str ) == strtolower( $a ) ) {
                    return true;
                }
            }
            return false;
        }
        
        // ANY PLUGIN STANDARD
        function enqueue_dependency_files()
        {
            wp_enqueue_style( 'mypluginstyle', plugins_url( '/assets/css/style.css', __FILE__ ) );
            wp_enqueue_script( 'mypluginscript', plugins_url( '/assets/script.js', __FILE__ ) );
        }
        
        function add_admin_pages()
        {
            // add_menu_page('Forms Email Tweaker', 'Tweaker for NF emails', 'manage_options', 'admin-page-tweaker-for-nf-emails', array($this, 'admin_index_page'), 'dashicons-buddicons-pm', 110);
            add_submenu_page(
                'options-general.php',
                'Tweaker for Ninja Forms emails',
                'Tweaker for Ninja Forms emails',
                'manage_options',
                'admin-page-tweaker-for-nf-emails',
                array( $this, 'admin_index_page' ),
                110
            );
            // add the setting fields
            add_action( 'admin_init', array( $this, 'registeringSettings' ) );
        }
        
        function admin_index_page()
        {
            // require template
            require_once plugin_dir_path( __FILE__ ) . 'templates/admin-page.php';
        }
        
        function settings_link( $links )
        {
            // add custom settings links in the all-plugins page
            $settings_link = "<a href='options-general.php?page=admin-page-tweaker-for-nf-emails'>Settings page</a>";
            array_push( $links, $settings_link );
            return $links;
        }
        
        function registeringSettings()
        {
            register_setting( 'ninja_tweaker_options', 'ninja_tweaker_option_add_numbers' );
            register_setting( 'ninja_tweaker_options', 'ninja_tweaker_option_yes_text_flags' );
            register_setting( 'ninja_tweaker_options', 'ninja_tweaker_option_negative_flags' );
            register_setting( 'ninja_tweaker_options', 'ninja_tweaker_option_exclude_by_id' );
        }
    
    }
    // class end
}

// if-class-exists end
$tweakerForNFemails = new TweakerForNFemails();
$tweakerForNFemails->register();
// activation
require_once plugin_dir_path( __FILE__ ) . 'inc/tweaker-for-nf-emails-activate.php';
register_activation_hook( __FILE__, array( 'TweakerForNFemailsActivate', 'activate' ) );
// deactivation
require_once plugin_dir_path( __FILE__ ) . 'inc/tweaker-for-nf-emails-deactivate.php';
register_deactivation_hook( __FILE__, array( 'TweakerForNFemailsDeactivate', 'deactivate' ) );