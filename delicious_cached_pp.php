<?php
/*
Plugin Name: del.icio.us cached++
Plugin URI: http://wordpress.org/extend/plugins/delicious-cached/
Description: Outputs del.icio.us bookmarks. Uses Wordpress built-in MagpieRSS to retrieve and to cache.
Version: 2.0
Author: João Craveiro
Author URI: http://www.jcraveiro.com/
*/

class Delicious_Cached extends WP_Widget {

  /**
   * Sets up the widgets name etc
   */
  public function __construct() {
    $widget_ops = array( 
      'classname' => 'delicious_cached',
      'description' => 'Delicious Cached++',
    );
    parent::__construct( 'delicious_cached', 'Delicious Cached++', $widget_ops );
  }

  /**
   * Outputs the content of the widget
   *
   * @param array $args
   * @param array $instance
   */
  public function widget( $args, $instance ) {
    // outputs the content of the widget

    extract($args);    
    echo $before_widget . $before_title . $instance['title'] . $after_title;

    echo '<ul>';
    $this->delicious_pp(
        !empty( $instance['sometag'] ) ? $instance['username'].'/'.$instance['sometag'] : $instance['username'],
        $instance['count'],
        $instance['extended'],
        $instance['tags'],
        $instance['before'],
        $instance['after'],
        $instance['beforeExtended'],
        $instance['afterExtended'],
        $instance['beforeTags'],
        $instance['betweenTags'],
        $instance['afterTags']
        );
    echo '</ul>';
    echo $after_widget;
  }

  /**
   * Outputs the options form on admin
   *
   * @param array $instance The widget options
   */
  public function form( $instance ) {

    if (empty($instance['sometag']) && !empty( $instance['title'] )) {
      $idx = strpos($instance['username'], '/', 1);
      if ($idx !== FALSE) {
        $exploded = explode('/', $instance['username'], 2);
        $instance['username'] = $exploded[0];
        $instance['sometag'] = $exploded[1];
      }
    }

    $title = !empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'delicious_cached' );
    $username = !empty( $instance['username'] ) ? $instance['username'] : __( 'username', 'delicious_cached' );
    $sometag = !empty( $instance['sometag'] ) ? $instance['sometag'] : __( 'sometag', 'delicious_cached' );
    $count = !empty( $instance['count'] ) ? $instance['count'] : 15;
    ?>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label>
      <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>"><?php _e( esc_attr( 'Username:' ) ); ?></label>
      <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'username' ) ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>">
    </p>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'sometag' ) ); ?>"><?php _e( esc_attr( 'Tag (optional):' ) ); ?></label>
      <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'sometag' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sometag' ) ); ?>" type="text" value="<?php echo esc_attr( $sometag ); ?>">
    </p>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php _e( esc_attr( 'Number of items:' ) ); ?></label>
      <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" step="1" min="1" value="<?php echo esc_attr( $count ); ?>">
    </p>  
    <?php
  }

  /**
   * Processing widget options on save
   *
   * @param array $new_instance The new options
   * @param array $old_instance The previous options
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array(
      'title'=>'del.icio.us',
      'username'=>'',
      'sometag'=>'',
      'count'=>15,
      'extended'=>1,
      'tags'=>0,
      'before'=>'<li>',
      'after'=>'</li>',
      'beforeExtended'=>'<p>',
      'afterExtended'=>'</p>',
      'beforeTags'=>'<p>',
      'betweenTags'=>', ',
      'afterTags'=>'</p>'
      );
    $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['username'] = ( !empty( $new_instance['username'] ) ) ? strip_tags( $new_instance['username'] ) : '';
    $instance['sometag'] = ( !empty( $new_instance['sometag'] ) ) ? strip_tags( $new_instance['sometag'] )  : '';
    $instance['count'] = ( !empty( $new_instance['count'] ) ) ? intval(strip_tags( $new_instance['count'] )) : 0;
    return $instance;
  }

  /*
  Arguments:
       $username - Your del.icio.us username
       $count - Maximum number of latest posts to display
       $extended - Whether/how to display or not the Extended field
          (0=no extended ; 1=extended)
       $tags - Number of tags to display per link
          (0=don't show tags ; >=1 = show, at most, 'n' tags)
       $before - Text to append before each item.
       $after - Text to append after each item.
       $beforeExtended - Text to append before each item's extended description.
       $afterExtended - Text to append after each item's extended description.
       $beforeTags - Text to append before each item's tags.
       $betweenTags - Text to separate tags.
       $afterTags - Text to append after each item's extended tags.
  */
  function delicious_pp(
      $username,
      $count=15,
      $extended=1,
      $tags=0,
      $before='<li>',
      $after='</li>',
      $beforeExtended='<p>',
      $afterExtended='</p>',
      $beforeTags='<p>',
      $betweenTags=' ',
      $afterTags='</p>'
      ) {
      require_once(ABSPATH . WPINC . '/rss-functions.php');
      $feedLocation = "http://feeds.del.icio.us/v2/rss/".$username;

      $feedContent = @fetch_rss($feedLocation);
      $feedItems = $feedContent->items;
      $output = '';
      
      for ($iter = 0 ; $iter < $count && $iter < sizeOf($feedItems) ; $iter++) {
            // The bookmarked URI
          $linkLink = htmlspecialchars($feedItems[$iter]['link']);
          // The text do display between the <a> and </a> tags
          $linkText = $feedItems[$iter]['title'];
          // Space-separated tags
          $linkTagsRaw = $feedItems[$iter]['dc']['subject'];
            // Link-ified tags, separated by the specified in $betweenTags
          $linkTags = ($tags > 0 && $linkTagsRaw) ?
                      $beforeTags.deliciousTagsMarkup($linkTagsRaw, $username, $tags, $betweenTags).$afterTags :
                      '';
          // Extended description
            $linkExtended=$feedItems[$iter]['description'];

            // If extended description is already to be shown or is empty,
            // the link title (TITLE attribute) will be the same as the link text.
            // Otherwise, extended will be the link title.
          if ($extended || !$feedItems[$iter]['description']) {
                $linkTitle = htmlentities($linkText,ENT_QUOTES,get_bloginfo('charset') );
          } else {
                $linkTitle = htmlentities($linkExtended,ENT_QUOTES,get_bloginfo('charset') );
          }
                              
            // Build the markup to display the extended description, except if
            // it is disabled or empty.
          if ($extended && $linkExtended) {
              $linkExtended = $beforeExtended.
                              $linkExtended.
                              $afterExtended;
          } else {
              $linkExtended = '';
          }

          // Add this item's markup to the final output
          $linkText = htmlspecialchars($linkText);
          $output .=  $before."<a href='$linkLink' title='$linkTitle'>$linkText</a>\n".
                          $linkExtended."\n".$linkTags.$after."\n";
      }
      
      echo $output;

  }

  // Turn a string of space-separated tags into a string of link-ified tags,
  // separated by what the user defines in $betweenTags.
  function deliciousTagsMarkup($tagsRaw, $username, $tags, $betweenTags) {
      $result = array();
      $tagsArray = explode(" ", $tagsRaw, $tags);
      for ($i = 0 ; $i < $tags && $i < sizeof($tagsArray) ; $i++) {
          $result[] = "<a href='http://del.icio.us/$username/$tagsArray[$i]' title='$tagsArray[$i] tag'>$tagsArray[$i]</a>";
      }
      return implode($betweenTags,$result);
  }
}

// register Foo_Widget widget
function register_delicious_cached_widget() {
    register_widget( 'Delicious_Cached' );
}
add_action( 'widgets_init', 'register_delicious_cached_widget' );

?>