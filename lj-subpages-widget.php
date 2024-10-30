<?php
/*
Plugin Name: LJ Subpages Widget
Plugin URI: http://www.thelazysysadmin.net/software/wordpress-plugins/lj-subpages-widget/
Description: LJ Subpages Widget allows you to display a menu listing subpages from a chosen page
Author: Jon Smith
Version: 1.3
Author URI: http://www.thelazysysadmin.net/
*/

class LJSubpagesWidget extends WP_Widget {
  private $defaults = array(
                          'parentid' => -1,
                          'usecurrentparent' => 0,
                          'useparenttitle' => 1,
                          'customtitle' => '',
                          'showsubpages' => 1,
                          'linkparentintitle' => 0,
                          'customcssforparentlink' => ''
                        );  
  
  function LJSubpagesWidget() {
    $options = array(
                  'classname' => 'LJSubpagesWidget',
                  'description' => 'LJSubpagesWidget Description'
                );
    $this->WP_Widget('LJSubpagesWidget', 'LJ Subpages Widget', $options);             
    add_filter( 'plugin_action_links', array(&$this, 'plugin_action_links'), 10, 2 );
  }
  
  function widget($args, $instance) {
    extract($args, EXTR_SKIP);
    
    if ($instance['usecurrentparent'] == 1) {
      if (is_page()) {
        $parentid = get_the_ID(); 
      } else {
        return;
      }
    } else {
      $parentid = $instance['parentid'];
    }
    
    if ($instance['useparenttitle'] == 1) {
      $details = get_post($parentid);
      $title = $details->post_title;
    } else {
      $title = $instance['customtitle'];
    }
    
    if ($instance['customcssforparentlink'] != '') {
        $customcss = $instance['customcssforparentlink'];
    } else {
        $customcss = '';
    }
    
    if ($instance['linkparentintitle'] == 1) {
        $title = '<a href="'.get_permalink($parentid).'" style="'.$customcss.'">'.$title.'</a>';
    }

    $output = wp_list_pages("title_li=0&sort_column=menu_order&echo=0&child_of=".$parentid);
    
    if (stripos($output, "Start LJCustomMenuLinks Ver") !== false) {
      $before = substr($output, 0, stripos($output, '<!-- Start LJCustomMenuLinks'));
      $after = substr($output, stripos($output, '<!-- End LJCustomMenuLinks -->') + 30, strlen($output));
      $output = $before.$after;
    }
    
    if (strlen($output) > 0) {
      echo $before_widget;
      echo $before_title.$title.$after_title;
      echo "<ul>\n";
      echo $output;
      echo "</ul>\n";
          
      echo $after_widget;
    }
  }
  
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['parentid'] = $new_instance['parentid'];
    $instance['usecurrentparent'] = $new_instance['usecurrentparent'];
    $instance['useparenttitle'] = $new_instance['useparenttitle'];
    $instance['customtitle'] = $new_instance['customtitle'];
    $instance['showsubpages'] = $new_instance['showsubpages'];
    $instance['linkparentintitle'] = $new_instance['linkparentintitle'];
    $instance['customcssforparentlink'] = $new_instance['customcssforparentlink'];
    
    return $instance;
  }
  
  function form($instance) {
    $instance = wp_parse_args( (array) $instance, $this->defaults);
    $parentid = $instance['parentid'];
    $usecurrentparent = $instance['usecurrentparent'];
    $useparenttitle = $instance['useparenttitle'];
    $customtitle = $instance['customtitle'];
    $showsubpages = $instance['showsubpages'];
    $linkparentintitle = $instance['linkparentintitle'];
    $customcssforparentlink = $instance['customcssforparentlink'];
?>
  <p>
    <label for="<?php echo $this->get_field_id('usecurrentparent'); ?>">Use current page as parent:</label><br />
    <input type="radio" id="<?php echo $this->get_field_id('usecurrentparent'); ?>" name="<?php echo $this->get_field_name('usecurrentparent'); ?>" value="1" <?php if ($instance['usecurrentparent'] == 1) { echo "checked='checked'"; } ?>>Yes
    <input type="radio" id="<?php echo $this->get_field_id('usecurrentparent'); ?>" name="<?php echo $this->get_field_name('usecurrentparent'); ?>" value="0" <?php if ($instance['usecurrentparent'] == 0) { echo "checked='checked'"; } ?>>No
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('parentid'); ?>">Parent Page<?php if ($instance['usecurrentparent'] == 1) { echo " (<i>Using current page as parent this setting is ignored</i>)"; } ?>:
      <?php
        wp_dropdown_pages(array('selected' => $parentid, 'name' => $this->get_field_name('parentid'), 'sort_column'=> 'menu_order, post_title'));
      ?>
    </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('useparenttitle'); ?>">Use Parent Title<?php if ($instance['usecurrentparent'] == 1) { echo " (<i>Using current page as parent this setting is recommended to be set to yes</i>)"; } ?>:</label><br />
    <input type="radio" id="<?php echo $this->get_field_id('useparenttitle'); ?>" name="<?php echo $this->get_field_name('useparenttitle'); ?>" value="1" <?php if ($instance['useparenttitle'] == 1) { echo "checked='checked'"; } ?>>Yes
    <input type="radio" id="<?php echo $this->get_field_id('useparenttitle'); ?>" name="<?php echo $this->get_field_name('useparenttitle'); ?>" value="0" <?php if ($instance['useparenttitle'] == 0) { echo "checked='checked'"; } ?>>No
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('customtitle'); ?>">Custom Title:
      <input class="widefat" id="<?php echo $this->get_field_id('customtitle'); ?>" name="<?php echo $this->get_field_name('customtitle'); ?>" type="text" value="<?php echo attribute_escape($customtitle); ?>" />
    </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('linkparentintitle'); ?>">Link to Title Page:</label><br />
    <input type="radio" id="<?php echo $this->get_field_id('linkparentintitle'); ?>" name="<?php echo $this->get_field_name('linkparentintitle'); ?>" value="1" <?php if ($instance['linkparentintitle'] == 1) { echo "checked='checked'"; } ?>>Yes
    <input type="radio" id="<?php echo $this->get_field_id('linkparentintitle'); ?>" name="<?php echo $this->get_field_name('linkparentintitle'); ?>" value="0" <?php if ($instance['linkparentintitle'] == 0) { echo "checked='checked'"; } ?>>No
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('customcssforparentlink'); ?>">Custom Inline CSS for Title Link:
      <input class="widefat" id="<?php echo $this->get_field_id('customcssforparentlink'); ?>" name="<?php echo $this->get_field_name('customcssforparentlink'); ?>" type="text" value="<?php echo attribute_escape($customcssforparentlink); ?>" />
    </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('showsubpages'); ?>">Show Subpages:</label><br />
    <input type="radio" id="<?php echo $this->get_field_id('showsubpages'); ?>" name="<?php echo $this->get_field_name('showsubpages'); ?>" value="1" <?php if ($instance['showsubpages'] == 1) { echo "checked='checked'"; } ?>>Yes
    <input type="radio" id="<?php echo $this->get_field_id('showsubpages'); ?>" name="<?php echo $this->get_field_name('showsubpages'); ?>" value="0" <?php if ($instance['showsubpages'] == 0) { echo "checked='checked'"; } ?>>No
  </p>
  <p>
    <div align="right">
    <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8735770">
    <img src="https://www.paypal.com/en_AU/i/btn/btn_donate_SM.gif" border="0" alt="PayPal - The safer, easier way to pay online." /></a>
    </div>
  </p>
<?php
  }

  function plugin_action_links( $links, $file ) {
    static $this_plugin;
    
    if( empty($this_plugin) )
      $this_plugin = plugin_basename(__FILE__);

    if ( $file == $this_plugin )
      $links[] = '<a href="' . admin_url( 'widgets.php' ) . '">Widgets</a>';

    return $links;
  }
  
}

add_action('widgets_init', create_function('', 'return register_widget("LJSubpagesWidget");'));

?>