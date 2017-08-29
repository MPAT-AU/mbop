<?php
/*
Plugin Name: MBOP remover
Plugin URI: /mbop
Description: Delete current user meta's 'meta-box-order_page'
Version: 0.2
Author: Jean-Philippe Ruijs
Author URI: https://github.com/jeanphilipperuijs/
License: GPL2
Text Domain: mbop-remover
Domain Path: /languages
*/
load_plugin_textdomain('mbop-remover', false, basename( dirname( __FILE__ ) ) . '/languages' );

class MBOP
{
    const PK = 'mbop_submitted';
    const MK = 'meta-box-order_page';
    private $rt = 2;

    function deleteMetaBoxOrderPage()
    {
        $current_user = wp_get_current_user();
        echo '<div id="deleteMetaBoxOrderPage">
<h2>'._e('Page fixer','mbop-remover').'</h2>
<h3>'.$current_user->display_name.'\'s '.$this::MK.'</h3>';
        $uid = $current_user->ID;
        $jsu = json_encode($current_user);
        $mbop  = get_user_meta($uid, $this::MK);
        $jso = json_encode($mbop);
    
        if (isset( $_POST[$this::PK] )) {
            $this->head();
            echo '<body>
<p>'.$this::MK.' deleted</p>
<p>refreshing in '.$this->rt. ' seconds</p>';
            echo $this->ta($jso);
            delete_user_meta($uid, $this::MK);
        } else {
            $this->html_form_code($jsu, $current_user);
        }
        echo '
</div>
</body>';
    }

    function template()
    {
        
        $url_path = trim($_SERVER['REQUEST_URI'], '/');
        if (substr($url_path,-4) === 'mbop' ) {
            echo "<html>\n";
            do_shortcode('[mbop_remover_sc]');
            echo "</html>\n";
            exit();
        }
    }    

    function head()
    {
        echo '<meta http-equiv="refresh" content="'.$this->rt.'">';
    }
    
    function html_form_code($jsu, $current_user)
    {
        echo '
<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">
<input type="text" readonly name="'.$this::PK.'" value="'.$this::MK.'">
<input id="dts" type="submit" name="'.$this::PK.'" value="'.__('Delete', 'mbop-remover').'">';
        echo $this->ta($this->getjso());
echo '</form>
<label for="dts">'.
__('This will remove the','mbop-remover').' "'.$this::MK.'" '.
__('value for user','mbop-remover').' "'.$current_user->display_name.'" ('.$current_user->user_email.') '.
__('which is generated when having opened a page','mbop-remover').'</label>';
    }
    function getum()
    {
        $current_user = wp_get_current_user();
        $uid = $current_user->ID;
        return get_user_meta($uid, $this::MK);
    }
    
    function ta($jso)
    {
        return '<div id="ta">
<textarea cols="80" rows="24">
'.$jso.'
</textarea>
</div>
';
    }

    function getjso()
    {
        $mbop  = $this->getum();
        return json_encode($mbop);
    }
}
$m = new MBOP();

add_action('wp_loaded', array(&$m,'template'));
add_shortcode('mbop_remover_sc', array(&$m,'deleteMetaBoxOrderPage'));
