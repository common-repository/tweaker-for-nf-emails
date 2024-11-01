<?php

/**
 * Plugin Name: Tweaker for Ninja Forms emails
 * Plugin URI: https://wordpress.org/plugins/tweaker-for-nf-emails/
 * Description: Let's tweak those raw emails
 * Version: 1.0.1
 * Author: RebootNow
 * @package tweakerForNFemails
 */
// check for illegitimate file call
if ( !defined( 'ABSPATH' ) && !current_user_can( 'manage_options' ) ) {
    exit;
}
$add_numbers = esc_html( get_option( 'ninja_tweaker_option_add_numbers' ) );
$yes_text_flags = esc_html( get_option( 'ninja_tweaker_option_yes_text_flags' ) );
$negative_flags = esc_html( get_option( 'ninja_tweaker_option_negative_flags' ) );
$exclude_by_id = esc_html( get_option( 'ninja_tweaker_option_exclude_by_id' ) );
?>

<div class="wrap">
    <div class="main_content">

        
        <br>
        <br>
        <header>
           <h1> </h1>
        </header>
        
        <?php 
// tweak the display of saving update and error messages
//settings_errors();
?>
                           
                        

        <form method="post" action="options.php">
            <?php 
// call our settings group (prepares hidden input fields that manage the submission)
settings_fields( 'ninja_tweaker_options' );
do_settings_sections( 'ninja_tweaker_options' );
?>


       

            <hr>




            <div class="postbox" style="padding: 6px;">
                <h1><b>Welcome to Tweaker for Ninja-Forms emails</b></h1>
                <p>turn your raw Ninja Forms submission-emails into easy to read information</p>
            </div>

            <div class="postbox" style="padding: 6px;">
                <h3 class="hndle"><label for="title">General settings</label></h3>
                <div class="inside">
                    <table class="form-table">
                        <tbody>
                            <tr valign="top">
                                <th scope="row">   
                                    Prefix fields with numbers
                                </th>
                                <td>    
                                    <input type="checkbox" id="add_numbers" name="ninja_tweaker_option_add_numbers" value="1" <?php 
checked( 1, $add_numbers );
?>>
                                    <label style="padding-right:10px" >this option will add numbering to each field.</label>
                                            
                                    <div>
                                        <p class="description">numbers help to find certain questions in submissions with many questions.</p>         
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="postbox" style="padding: 6px;">
                <h3 class="hndle"><label for="title">Rules</label></h3>
                <div class="inside">
                    <table class="form-table">
                        <tbody>
                            <tr valign="top">
                                <th scope="row">   
                                Highlight if the answer is Yes
                                </th>
                                <td>
                                <input type="text" id="yes_text_flags" name="ninja_tweaker_option_yes_text_flags" value="<?php 
echo  esc_html( $yes_text_flags ) ;
?>" class="large-text" /> 
                                    <label style="padding-right:10px" class="update-message notice inline notice-warning notice-alt">separate with semicolumns if more than one value</label>
                                            
                                    <div>
                                        <p class="description">words or succession of words in a question that should highlight a question if the answer to it is YES</p>
                                        <p class="description">if the answer is YES to a question that contains your string(s), then the questions will be highlighted.</p>
                                        <p class="description">* example of string for here: <strong>are there new issues;do you need help;have you noticed</strong></p>         
                                    </div>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">   
                                Answers that will highlight
                                </th>
                                <td>
                                    <input type="text" id="negative_flags" name="ninja_tweaker_option_negative_flags" value="<?php 
echo  esc_html( $negative_flags ) ;
?>" class="large-text" />
                                    <label style="padding-right:10px" class="update-message notice inline notice-warning notice-alt">separate with semicolumns if more than one value</label>
                                            
                                    <div>
                                        <p class="description">if an answer matches exactly one of the ones that you define here, then the question will be highlighted.</p>
                                        <p class="description">* example of string for here: <strong>failed;no;poor</strong></p>         
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
			
			
			
			<?php 
// marketing - motivate to buy

if ( tfne_fs()->is_not_paying() ) {
    ?>
					
					<div class="postbox" style="padding: 6px; background-color: ocean;">
						<div class="inside">
							<h3 class="fs-notice success"><label for="title">MORE POWERFUL FEATURES IN THE PREMIUM VERSION</label></h3>
							<h3>for just £0.99/month</h3>
							<ul>
								<li>Exclude forms by id</li>
							</ul>
							<h3><a href="<?php 
    echo  tfne_fs()->get_upgrade_url() ;
    ?>">Upgrade Now!</a></h3>
						</div>
					</div>
					
			<?php 
}

?>
			
			
			<!-- gold -->
			<?php 
?>



            <span class="float-right"><?php 
submit_button( 'Save settings' );
?></span>
   

        </form>   




 


    </div>
</div>

<hr>