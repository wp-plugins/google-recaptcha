<?php
/**
 * @package nhanweb_google_recaptcha
 */
/*
Plugin Name: Google Recaptcha
Plugin URI: http://nhanweb.com/
Description: Google Recaptcha is a way of spam protecting your website from bots but making it easy for humans to submit the form by simply recreating a checkbox for users to click. To use Google NoCaptcha ReCaptcha for your website you must first register your domain with the ReCaptcha API and get the site API key and the site secret API key.
Version: 1.0.0
Author: babyinternet
Author URI: http://nhanweb.com/
License: GPLv2 or later
*/

//Admin Panel
add_action('admin_menu', 'nhanweb_google_recaptcha_setting_menu');

function nhanweb_google_recaptcha_setting_menu() {
    if (function_exists('add_options_page')) {
        add_options_page( __('Google reCAPTCHA',''), __('Google reCAPTCHA',''), 8, basename(__FILE__), 'nhanweb_google_recaptcha_panel');
    }
}
function nhanweb_google_recaptcha_panel(){
    if($_POST['nhanweb_save']){
        $nw_opts = $_POST['nhanweb_grecaptcha'];
        $nw_opts["recaptcha_api_key"] = $_POST['nhanweb_recaptcha_api_key'];
        $nw_opts["recaptcha_api_secret"] =  $_POST['nhanweb_recaptcha_api_secret'];
        update_option('nhanweb_google_recaptcha',$nw_opts);
    }
    
    $recapt_options = get_option('nhanweb_google_recaptcha');
    ?>
    <h2>Google ReCAPTCHA</h2>
    <p>To use Google NoCaptcha ReCaptcha for your website you must first <a href="https://www.google.com/recaptcha/admin" target="_blank">register your domain with the ReCaptcha API</a> and get the site API key and the site secret API key. When you have registered your website with the ReCaptcha API you can then collect the site key and the secret key, which you will need to use on your website to process the request.</p>
    <p>If you have any problem or need help, please contact <a href="http://vnwebmaster.com">Webmaster Viet Nam forum</a></p>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo basename(__FILE__); ?>">
    <h3><img src="<?php echo plugin_dir_url(__FILE__); ?>icon_l.gif" /> Google ReCAPTCHA Setting </h3>
    <?php
    if($recapt_options['recaptcha_api_key']=='' || $recapt_options['recaptcha_api_secret']==''){
        echo '<div id="message" class="error"><p><strong>you must first <a href="https://www.google.com/recaptcha/admin" target="_blank">register your domain with the ReCaptcha API</a> and get the site API key and the site secret API key.</strong></p></div>';
    }
    ?>
    <table width="100%" cellspacing="2" cellpadding="5" class="editform">
            <tr valign="top"> 
                <td scope="row">ReCaptcha API</td> 
                <td>API key <input id="nhanweb_recaptcha_api_key" size="20" value="<?php echo $recapt_options['recaptcha_api_key']; ?>" name="nhanweb_recaptcha_api_key"/> secret API key<input id="nhanweb_recaptcha_api_secret" size="20" value="<?php echo $recapt_options['recaptcha_api_secret']; ?>" name="nhanweb_recaptcha_api_secret"/></td> 
            </tr>   
            <tr valign="top"> 
                <td width="30%" scope="row">Enable reCAPTCHA for:</td>
                <td>
                    <label><input name="nhanweb_grecaptcha[]" type="checkbox" value="comment" <?php if(in_array('comment',$recapt_options)) {echo "checked";}; ?> /> Comment forms</label> <br />
                    <label><input name="nhanweb_grecaptcha[]" type="checkbox" value="login" <?php if(in_array('login',$recapt_options)) {echo "checked";} ?> /> Login forms</label> <br />
                    <label><input name="nhanweb_grecaptcha[]" type="checkbox" value="lost_pass" <?php if(in_array('lost_pass',$recapt_options)) {echo "checked";} ?> /> Lost password forms</label> <br />
                    <label><input name="nhanweb_grecaptcha[]" type="checkbox" value="register" <?php if(in_array('register',$recapt_options)) {echo "checked";} ?> /> New User forms</label>
                </td> 
            </tr>
           
             <tr valign="top"> 
                <td width="30%" scope="row"></td> 
                <td scope="row">            
                    <input type="submit" name="nhanweb_save" value="Save" class="button-primary" />
                </td> 
            </tr>
        </table>
  </form>
  <h3>Link:</h3>
<p><a href="http://nhanweb.com">Support Blog</a> - <a href="http://vnwebmaster.com">Support Forum</a></p>
    <?php

}

function nhanweb_google_recaptcha_uninstall(){
    delete_option('nhanweb_google_recaptcha');
}
function nhanweb_google_recaptcha_install(){
    add_option('nhanweb_google_recaptcha', array());
}
register_deactivation_hook(__FILE__, 'nhanweb_google_recaptcha_uninstall');
register_activation_hook(__FILE__, 'nhanweb_google_recaptcha_install');

//Google recaptcha plugin
function nhanweb_enqueue_scripts()
{
wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js' );
wp_enqueue_script( 'recaptcha-display-buttons', plugin_dir_url(__FILE__) . 'nhanweb_recaptcha.js', array('jquery'));
wp_enqueue_style( 'recaptcha-hide-buttons', plugin_dir_url(__FILE__) . 'nhanweb_recaptcha.css');
}
function nhanweb_display_recaptcha()
{
    $recapt_options = get_option('nhanweb_google_recaptcha');
	nhanweb_enqueue_scripts();
    echo '<div class="g-recaptcha" data-sitekey='.$recapt_options['recaptcha_api_key'].' data-callback="recaptcha_callback"></div>';
}
function nhanweb_verify_captcha( $parameter = true )
{
    if( isset( $_POST['g-recaptcha-response'] ) )
    {
        $recapt_options = get_option('nhanweb_google_recaptcha');
        $response = wp_remote_retrieve_body( wp_remote_get( "https://www.google.com/recaptcha/api/siteverify?secret=".$recapt_options['recaptcha_api_secret']."&response=" .$_POST['g-recaptcha-response'] ) );
        if( $response["success"] )
        {
            return $parameter;
        }
    }

    return false;
}
$recapt_options = get_option('nhanweb_google_recaptcha');
//Login Page
if(in_array('login', $recapt_options)){
    add_action( 'login_form', 'nhanweb_display_recaptcha' );
    add_filter( 'wp_authenticate_user', 'nhanweb_verify_captcha' );
}

//New User Registration
if(in_array('register', $recapt_options)){
   add_action( 'register_form', 'nhanweb_display_recaptcha');
    add_action( 'register_post', 'nhanweb_verify_captcha');
}


//Lost Password Form
if(in_array('lost_pass', $recapt_options)){
   add_action( 'lostpassword_form', 'nhanweb_display_recaptcha' );
   add_action( 'lostpassword_post', 'nhanweb_verify_captcha' );
}


//Comment Form
if(in_array('comment', $recapt_options)){
    add_action( 'comment_form', 'nhanweb_display_recaptcha' );
    add_filter( 'preprocess_comment', 'nhanweb_verify_captcha' );
}
