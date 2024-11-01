<?php
/**
 * Plugin Name: Tweaker for Ninja Forms emails
 * Plugin URI: 
 * Description: Let's tweak those raw emails
 * Version: 1.0
 * Author: Tom Ford
 * Author URI: 
 * @package tweakerForNFemails
 */



class TweakerForNFemailsDeactivate {

    public static function deactivate() 
    {
        flush_rewrite_rules();  
    }
}