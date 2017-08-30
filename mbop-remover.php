<?php
/*
Plugin Name: MBOP remover
Plugin URI: /mbop
Description: Delete current user meta's 'meta-box-order_page'
Version: 1.0.1
Author: Jean-Philippe Ruijs
Author URI: https://github.com/MPAT-eu
License: GPL2
Text Domain: mbop-remover
Domain Path: /languages
*/
class MBOP
{
    const PK = 'mbop_submitted';
    const MK = 'meta-box-order_page';
    private $rt = 2;

    function deleteMetaBoxOrderPage()
    {
        load_plugin_textdomain('mbop-remover', false, basename( dirname( __FILE__ ) ) . '/languages' );
        $current_user = wp_get_current_user();
        echo '<div id="deleteMetaBoxOrderPage">
<h2>'.__('Page fixer', 'mbop-remover').'</h2>
<h3>'.
/*.$current_user->display_name.'\'s '.*/
        sprintf(__('cleaner %1$s', 'mbop-remover'), $this::MK).'</h3>';
        $uid = $current_user->ID;
        $jsu = json_encode($current_user);
        $mbop  = get_user_meta($uid, $this::MK);
        $jso = json_encode($mbop);
    
        if (isset( $_POST[$this::PK] )) {
            $this->head();
            echo '<body>
<p><strong>'.
            $this::MK.__(' deleted', 'mbop-remover').', '.__('refreshing in ', 'mbop-remover').$this->rt. __(' seconds', 'mbop-remover').
            '</strong></p>';
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
        if (substr($url_path, -4) === 'mbop') {
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
        echo '<label for="dts">'.
        '<p>'.
        sprintf(__('This will remove the "%1$s" value for user "%2$s" (%3$s), which is generated when having opened a page', 'mbop-remover'),
        $this::MK,
        $current_user->display_name,
        $current_user->user_email).
        '</p>'.
'<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">
<input type="text" readonly name="'.$this::PK.'" value="'.$this::MK.'">
<input id="dts" type="submit" name="'.$this::PK.'" value="'.__('Delete', 'mbop-remover').'">';
        echo $this->ta($this->getjso());
        echo '</form>
        </label>';
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
