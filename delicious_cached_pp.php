<?php
/*
Plugin Name: del.icio.us cached++
Plugin URI: http://wordpress.org/extend/plugins/delicious-cached/
Description: Outputs del.icio.us bookmarks. Uses Wordpress built-in MagpieRSS to retrieve and to cache.
Version: 1.3a3
Author: João Craveiro
Author URI: http://www.jcraveiro.com/
*/

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
    $feedLocation = "http://feeds.delicious.com/v2/rss/".$username.'/';

    $feedContent = @fetch_rss($feedLocation);
    $feedItems = $feedContent->items;
    $output = '';
    
    for ($iter = 0 ; $iter < $count && $iter < sizeOf($feedItems) ; $iter++) {
          // The bookmarked URI
        $linkLink = htmlspecialchars($feedItems[$iter]['about']);
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


function widget_deliciouspp_init() {
	if (!function_exists('register_sidebar_widget')) return;

	function widget_deliciouspp($args) {
		
		extract($args);

		$options = get_option('widget_deliciouspp');
		$title = $options['title'];
		
		echo $before_widget . $before_title . $title . $after_title;
		echo '<ul>';
		delicious_pp(
		    $options['username'],
		    $options['count'],
		    $options['extended'],
		    $options['tags'],
		    $options['before'],
		    $options['after'],
		    $options['beforeExtended'],
		    $options['afterExtended'],
		    $options['beforeTags'],
		    $options['betweenTags'],
		    $options['afterTags']
		    );
		echo '</ul>';
		echo $after_widget;
	}

	function widget_deliciouspp_control() {
		$options = get_option('widget_deliciouspp');
		if ( !is_array($options) )
			$options = array(
			'title'=>'del.icio.us',
			'username'=>'',
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
		if ( $_POST['deliciouspp-submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['deliciouspp-title']));
			$options['username'] = strip_tags(stripslashes($_POST['deliciouspp-username']));
			$options['count'] = intval(strip_tags(stripslashes($_POST['deliciouspp-count'])));
			// TBD: update other options
			update_option('widget_deliciouspp', $options);
		}

		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$username = htmlspecialchars($options['username'], ENT_QUOTES);
		$numitems = htmlspecialchars($options['count'], ENT_QUOTES);
		
		echo '<p style="text-align:right;"><label for="deliciouspp-title">Title: <input style="width: 200px;" id="deliciouspp-title" name="deliciouspp-title" type="text" value="'.$title.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="deliciouspp-username">Username: <input style="width: 200px;" id="deliciouspp-username" name="deliciouspp-username" type="text" value="'.$username.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="deliciouspp-count">Link count: <input style="width: 200px;" id="deliciouspp-count" name="deliciouspp-count" type="text" value="'.$numitems.'" /></label></p>';		
		echo '<input type="hidden" id="deliciouspp-submit" name="deliciouspp-submit" value="1" />';
	}		

	register_sidebar_widget('Delicious Cached++', 'widget_deliciouspp');
	register_widget_control('Delicious Cached++', 'widget_deliciouspp_control', 300, 100);
}

add_action('plugins_loaded', 'widget_deliciouspp_init');

?>
