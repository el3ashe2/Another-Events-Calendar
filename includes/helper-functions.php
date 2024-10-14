<?php

/**
 * Helper functions.
 *
 * @link          https://github.com/el3ashe2/Another-Events-Calendar
 * @since         1.0.0
 *
 * @package       another-events-calendar
 * @subpackage    another-events-calendar/includes
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Get the list of WP Pages.
 *
 * @since    1.0.0
 * @return   array     $pages    Array of WP pages.
 */
function aec_get_pages() {

	$wp_pages = get_pages();
	
	$pages = array();
	$pages[-1] = __( 'Select Page', 'another-events-calendar' );
	if( $wp_pages ) {
		foreach( $wp_pages as $page ) {
			$pages[ $page->ID ] = $page->post_title;
		}
	}
	
	return $pages;
			
}

/** 
 * Get current address bar URL.
 *
 * @since    1.0.0
 *
 * @return   string    Current Page URL.
 */
function aec_get_current_url() {

    $current_url = ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" ) ? "https://" : "http://";
    $current_url .= $_SERVER["SERVER_NAME"];
    if( $_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443" ) {
        $current_url .= ":".$_SERVER["SERVER_PORT"];
    }
    $current_url .= $_SERVER["REQUEST_URI"];
	
    return $current_url;
	
}

/**
 * Find days between 2 dates.
 *
 * @since    1.0.0
 *
 * @param    string    $start    Start date.
 * @param    string    $end      End date.
 * @return   array     $dates    Array of date strings.
 */
function aec_get_dates_from_range( $start, $end ) {
 
    $interval = new DateInterval('P1D'); 

    $real_end = new DateTime( $end ); 
    $real_end->add( $interval );

    $period = new DatePeriod( new DateTime( $start ), $interval, $real_end );
	
	$dates = array();
    foreach( $period as $date ) { 
       	$dates[] = $date->format('Y-m-d'); 
    }

    return $dates;
		
}	

/**
 * Get AEC categories list.
 *
 * @since    1.0.0
 *
 * @param    int       $parent    Term ID.
 * @return   string    $html      AEC Categories List.
 */
function aec_list_categories( $parent = 0 ) {

	$general_settings    = get_option( 'aec_general_settings' );
	$categories_settings = get_option( 'aec_categories_settings' );	
		
	$show_events_count = empty( $categories_settings['show_events_count'] ) ? 0 : 1;
	$show_past_events  = empty( $general_settings['show_past_events'] )     ? 0 : 1;

	$args = array(
    	'hide_empty'   => empty( $categories_settings['hide_empty_categories'] ) ? false : true,
		'orderby'  	   => $categories_settings['orderby'],
		'order'  	   => $categories_settings['order'],
		'parent'       => $parent,
		'hierarchical' => 0
	);
	$terms = get_terms( 'aec_categories', $args );
	
	$li = array();
	foreach( $terms as $term ) {		
		$count = aec_get_events_count_by_category( $term->term_id, $show_past_events );
		$child_terms = aec_list_categories( $term->term_id );
		if( $show_events_count ) {
			$li[] = sprintf( '<li><a href="%s">%s (%d)</a>%s</li>', aec_category_page_link( $term ) , $term->name, $count, $child_terms );
		} else {
			$li[] = sprintf( '<li><a href="%s">%s </a>%s</li>', aec_category_page_link( $term ) , $term->name,  $child_terms );
		}
	}
	
	$html = '';
	if( count( $li ) ) {
		$html = '<ul>'.implode( '', $li ).'</ul>';
	};
	
	return $html;
	
}

/**
 * Get Events count in the given AEC category ID.
 *
 * @since    1.0.0
 *
 * @param    int    $term_id             Term ID.
 * @param    bool   $show_past_events    If true, include the count of past events.
 * @return   int                         Number of Events.
 */
function aec_get_events_count_by_category( $term_id, $show_past_events ) {

	$args = array(
		'post_type'      => 'aec_events',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'tax_query' 	 => array(
        	array(
				'taxonomy'   => 'aec_categories',
    			'hide_empty' => false,
				'terms'	 	 => $term_id
			)
		)
	);
	
	$meta_queries = array();

	if( 0 === $show_past_events ) { 
		$meta_queries[] = array(
			'relation' => 'OR',
			array(
				'key'     => 'start_date_time',
				'value'	  => current_time('mysql'),
				'compare' => '>=',
				'type'    => 'DATETIME'
			),
			array(
				'key'     => 'end_date_time',
				'value'	  => current_time('mysql'),
				'compare' => '>=',
				'type'    => 'DATETIME'
			)
		);
	} 
		
	$count_meta_queries = count( $meta_queries );
	if( $count_meta_queries ) {
		$args['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( array( 'relation' => 'AND' ), $meta_queries ) : array( $meta_queries );
	}
	
	$events = get_posts( $args );
	
	return count( $events );
	
}

/**
 * Sanitize Amount
 *
 * Returns a sanitized amount by stripping out thousands separators.
 *
 * @since 1.0.0
 *
 * @param    string    $amount    Price amount to format.
 * @return   string    $amount    Newly sanitized amount.
 */
function aec_sanitize_amount( $amount ) {

	$is_negative = false;
	
	$currency_settings = get_option( 'aec_currency_settings' );
	$currency = ! empty( $currency_settings[ 'currency' ] ) ? $currency_settings[ 'currency' ] : 'USD';
	$thousands_sep = ! empty( $currency_settings[ 'thousands_separator' ] ) ? $currency_settings[ 'thousands_separator' ] : ',';
	$decimal_sep = ! empty( $currency_settings[ 'decimal_separator' ] ) ? $currency_settings[ 'decimal_separator' ] : '.';

	// Sanitize the amount
	if( $decimal_sep == ',' && false !== ( $found = strpos( $amount, $decimal_sep ) ) ) {
		if( ( $thousands_sep == '.' || $thousands_sep == ' ' ) && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
			$amount = str_replace( $thousands_sep, '', $amount );
		} else if( empty( $thousands_sep ) && false !== ( $found = strpos( $amount, '.' ) ) ) {
			$amount = str_replace( '.', '', $amount );
		}

		$amount = str_replace( $decimal_sep, '.', $amount );
	} else if( $thousands_sep == ',' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
		$amount = str_replace( $thousands_sep, '', $amount );
	}

	if( $amount < 0 ) {
		$is_negative = true;
	}

	$amount = preg_replace( '/[^0-9\.]/', '', $amount );
	$decimals = aec_currency_decimal_count( 2, $currency );
	$amount = number_format( (double) $amount, $decimals, '.', '' );

	if( $is_negative ) {
		$amount *= -1;
	}

	return apply_filters( 'aec_sanitize_amount', $amount );
	
}

/**
 * Returns a nicely formatted amount.
 *
 * @since 1.0.0
 *
 * @param    string    $amount      Price amount to format
 * @param    string    $decimals    Whether or not to use decimals. Useful when set to false for non-currency numbers.
 * @return   string    $amount      Newly formatted amount or Price Not Available
 */
function aec_format_amount( $amount, $decimals = true ) {

	$currency_settings = get_option( 'aec_currency_settings' );
	$currency = ! empty( $currency_settings[ 'currency' ] ) ? $currency_settings[ 'currency' ] : 'USD';
	$thousands_sep = ! empty( $currency_settings[ 'thousands_separator' ] ) ? $currency_settings[ 'thousands_separator' ] : ',';
	$decimal_sep = ! empty( $currency_settings[ 'decimal_separator' ] ) ? $currency_settings[ 'decimal_separator' ] : '.';

	// Format the amount
	if( $decimal_sep == ',' && false !== ( $sep_found = strpos( $amount, $decimal_sep ) ) ) {
		$whole = substr( $amount, 0, $sep_found );
		$part = substr( $amount, $sep_found + 1, ( strlen( $amount ) - 1 ) );
		$amount = $whole . '.' . $part;
	}

	// Strip , from the amount (if set as the thousands separator)
	if( $thousands_sep == ',' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
		$amount = str_replace( ',', '', $amount );
	}

	// Strip ' ' from the amount (if set as the thousands separator)
	if( $thousands_sep == ' ' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
		$amount = str_replace( ' ', '', $amount );
	}

	if( empty( $amount ) ) {
		$amount = 0;
	}

	if( $decimals ) {
		$decimals  = aec_currency_decimal_count( 2, $currency );
	} else {
		$decimals = 0;
	}
	
	$formatted = number_format( $amount, $decimals, $decimal_sep, $thousands_sep );

	return apply_filters( 'aec_format_amount', $formatted, $amount, $decimals, $decimal_sep, $thousands_sep );
	
}

/**
 * Set the number of decimal places per currency
 *
 * @since    1.0.0
 *
 * @param    int       $decimals    Number of decimal places.
 * @param    string    $currency    Payment currency.
 * @return   int       $decimals
*/
function aec_currency_decimal_count( $decimals = 2, $currency = 'USD' ) {

	switch( $currency ) {
		case 'RIAL' :
		case 'JPY' :
		case 'TWD' :
		case 'HUF' :
			$decimals = 0;
			break;
	}

	return apply_filters( 'aec_currency_decimal_count', $decimals, $currency );
	
}

/**
 * Get the directory's set currency
 *
 * @since    1.0.0
 * @return   string    The currency code.
 */
function aec_get_currency() {

	$currency_settings = get_option( 'aec_currency_settings' );
	$currency = ! empty( $currency_settings[ 'currency' ] ) ? $currency_settings[ 'currency' ] : 'USD';
	
	return strtoupper( $currency );
	
}

/**
 * Given a currency determine the symbol to use. If no currency given, site default is used.
 * If no symbol is determine, the currency string is returned.
 *
 * @since    1.0.0
 *
 * @param    string    $currency    The currency string.
 * @return   string                 The symbol to use for the currency.
 */
function aec_currency_symbol( $currency = '' ) {

	switch( $currency ) {
		case "GBP" :
			$symbol = '&pound;';
			break;
		case "BRL" :
			$symbol = 'R&#36;';
			break;
		case "EUR" :
			$symbol = '&euro;';
			break;
		case "USD" :
		case "AUD" :
		case "NZD" :
		case "CAD" :
		case "HKD" :
		case "MXN" :
		case "SGD" :
			$symbol = '&#36;';
			break;
		case "JPY" :
			$symbol = '&yen;';
			break;
		default :
			$symbol = $currency;
			break;
	}

	return apply_filters( 'aec_currency_symbol', $symbol, $currency );
	
}

/**
 * Formats the currency display.
 *
 * @since    1.0.0
 *
 * @param    string    $price       Paid Amount.
 * @return   array     $currency    Currencies displayed correctly.
 */
function aec_currency_filter( $price = '' ) {

	$currency_settings = get_option( 'aec_currency_settings' );
	$currency = ! empty( $currency_settings[ 'currency' ] ) ? $currency_settings[ 'currency' ] : 'USD';
	$position = $currency_settings['position'];

	$negative = $price < 0;

	if( $negative ) {
		$price = substr( $price, 1 ); // Remove proceeding "-" -
	}

	$symbol = aec_currency_symbol( $currency );

	if( $position == 'before' ) {
	
		switch( $currency ) {
			case "GBP" :
			case "BRL" :
			case "EUR" :
			case "USD" :
			case "AUD" :
			case "CAD" :
			case "HKD" :
			case "MXN" :
			case "NZD" :
			case "SGD" :
			case "JPY" :
				$formatted = $symbol . $price;
				break;
			default :
				$formatted = $currency . ' ' . $price;
				break;
		}
		
		$formatted = apply_filters( 'aec_' . strtolower( $currency ) . '_currency_filter_before', $formatted, $currency, $price );
		
	} else {
	
		switch( $currency ) {
			case "GBP" :
			case "BRL" :
			case "EUR" :
			case "USD" :
			case "AUD" :
			case "CAD" :
			case "HKD" :
			case "MXN" :
			case "SGD" :
			case "JPY" :
				$formatted = $price . $symbol;
				break;
			default :
				$formatted = $price . ' ' . $currency;
				break;
		}
		
		$formatted = apply_filters( 'aec_' . strtolower( $currency ) . '_currency_filter_after', $formatted, $currency, $price );
		
	}

	if( $negative ) {
		// Prepend the mins sign before the currency sign
		$formatted = '-' . $formatted;
	}

	return $formatted;
	
}

/**
 * Generate permalink for a category page.
 *
 * @since    1.0.0
 *
 * @param    object    $term    The term object.
 * @return   string             Term link.
 */
function aec_category_page_link( $term ) {

	$page_settings = get_option( 'aec_page_settings' );

	if( $page_settings['category'] > 0 ) {
		$link = get_permalink( $page_settings['category'] );
	
		if( '' != get_option( 'permalink_structure' ) ) {
    		$link = user_trailingslashit( trailingslashit( $link ) . $term->slug );
  		} else {
    		$link = add_query_arg( 'aec_category', $term->slug, $link );
  		}
	} else {
		$link = get_term_link( $term, 'aec_categories' );
	}
  
	return $link;
	
}

/**
 * Generate permalink for a calendar page.
 *
 * @since    1.0.0
 *
 * @return   string    Calendar page URL.
 */
function aec_calendar_page_link(  ) {

	$page_settings = get_option( 'aec_page_settings' );
	
	$link = '';
	
	if( $page_settings['calendar'] > 0 ) {
		$link = get_permalink( $page_settings['calendar'] );
	}
		  
	return $link;
	
}

/**
 * Generate permalink for the events page.
 *
 * @since    1.0.0
 *
 * @return   string    Events page URL.
 */
function aec_events_page_link() {

	$page_settings = get_option( 'aec_page_settings' );
	
	$link = '';
	 
	if( $page_settings['events'] > 0 ) {
		$link = get_permalink( $page_settings['events'] );
	}	
  
	return $link;
	
}

/**
 * Generate permalink for the search page.
 *
 * @since    1.0.0
 *
 * @return   string    Search results page URL.
 */
function aec_search_page_link() {

	$page_settings = get_option( 'aec_page_settings' );
	
	$link = '';
	
	if( $page_settings['search'] > 0 ) {
		$link = get_permalink( $page_settings['search'] );
	}
	
  	return $link;
	
}



/**
 * Generate permalink for a tag page.
 *
 * @since    1.0.0
 *
 * @param    object    $term    The term object.
 * @return   string             Term link.
 */
function aec_tag_page_link( $term ) {

	$page_settings = get_option( 'aec_page_settings' );

	if( $page_settings['tag'] > 0 ) {
		$link = get_permalink( $page_settings['tag'] );
	
		if( '' != get_option( 'permalink_structure' ) ) {
    		$link = user_trailingslashit( trailingslashit( $link ) . $term->slug );
  		} else {
    		$link = add_query_arg( 'aec_tag', $term->slug, $link );
  		}
	}  else {
		$link = get_term_link( $term, 'aec_tags' );
	}
  
	return $link;
	
}


/**
 * Generate permalink for a venue page.
 *
 * @since    1.0.0
 *
 * @param    int       $venue_id    Venue ID.
 * @return   string                 Venue page URL.
 */
function aec_venue_page_link( $venue_id ) {

	$page_settings = get_option( 'aec_page_settings' );
	
	$link = '';
	if( $page_settings['venue'] > 0 ) {
		$link = get_permalink( $page_settings['venue'] );
		$post = get_post( $venue_id ); 
		$slug = $post->post_name; 
    			
		if( '' != get_option( 'permalink_structure' ) ) {
    		$link = user_trailingslashit( trailingslashit( $link ) . $slug );
  		} else {
    		$link = add_query_arg( 'aec_venue', $slug, $link );
  		}
	} 
	
  	return $link;
	
}

/**
 * Generate permalink for a organizer page.
 *
 * @since    1.0.0
 *
 * @param    int       $organizer_id    Organizer ID.
 * @return   string                 	Organizer page URL.
 */
function aec_organizer_page_link( $organizer_id ) {

	$page_settings = get_option( 'aec_page_settings' );
	
	$link = '';
	
	if( $page_settings['organizer'] > 0 ) {
		$link = get_permalink( $page_settings['organizer'] );		
		$post = get_post( $organizer_id ); 
		$slug = $post->post_name;
		
		if( '' != get_option( 'permalink_structure' ) ) {
    		$link = user_trailingslashit( trailingslashit( $link ) . $slug );
  		} else {
    		$link = add_query_arg( 'aec_organizer', $slug, $link );
  		}
	} 
	
  	return $link;
	
}

/**
 * Display the event address.
 *
 * @since    1.0.0
 *
 * @param    object    $venue_id    Venue ID.
 */
function the_aec_address( $venue_id ) {

	$meta = array();

	if( $address = get_post_meta( $venue_id, 'address', true ) ) {
		$meta[] = $address;
	}
	
	if( $city = get_post_meta( $venue_id, 'city', true ) ) {
		$meta[] = $city;
	}
	
	if( $country = get_post_meta( $venue_id, 'country', true ) ) {
		$countries = aec_get_countries();
		$meta[] = $countries[ $country ];
	}
	
	if( $state = get_post_meta( $venue_id, 'state', true ) ) {
		$meta[] = $state;
	}
	
	if( $pincode = get_post_meta( $venue_id, 'pincode', true ) ) {
		$meta[] = $pincode;
	}
	
	if( $phone = get_post_meta( $venue_id, 'phone', true ) ) {
		$meta[] = sprintf( '<abbr title="%s">%s:</abbr> %s', __( 'Phone', 'another-events-calendar' ), __( 'P', 'another-events-calendar' ), $phone );
	}
	
	if( $website = get_post_meta( $venue_id, 'website', true ) ) {
		$meta[] =  sprintf( '<a href="%s" target="_blank">%s</a>', $website, $website );
	}
	
	$meta = array_unique( $meta );
	
	if( count( $meta ) ) {
		echo '<address>';
		
		if( ! get_query_var('aec_venue') ) { 
			printf( '<p class="aec-margin-bottom"><strong><a href="%s">%s</a></strong></p>', aec_venue_page_link( $venue_id ), get_the_title( $venue_id ) );
		}
			
		echo implode( '<br>', $meta );
		
		echo '</address>';
	}
				
}

/**
 * Get current page number.
 *
 * @since    1.0.0
 *
 * @return    int    $paged    The current page number.
 */
function aec_get_page_number() {

	global $paged;
	
	if( get_query_var('paged') ) {
    	$paged = get_query_var('paged');
	} else if( get_query_var('page') ) {
    	$paged = get_query_var('page');
	} else {
		$paged = 1;
	}
    	
	return absint( $paged );
		
}

/**
  * Removes an item or list from the query string.
  *
  * @since    1.0.0
  *
  * @param    string|array    $key                Query key or keys to remove.
  * @param    bool|string     $query Optional.    When false uses the $_SERVER value. Default false.
  * @return   string                              New URL query string.
  */
function aec_remove_query_arg( $key, $query = false ) {

	if( is_array( $key ) ) { // removing multiple keys
		foreach( $key as $k ) {
			$query = str_replace( '#038;', '&', $query );
			$query = add_query_arg( $k, false, $query );
		}
		
		return $query;
	}
		
	return add_query_arg( $key, false, $query );
	
}

/**
 * Display paginated links for event pages.
 *
 * @since    1.0.0
 *
 * @param    int    $numpages     The total amount of pages.
 * @param    int    $pagerange    How many numbers to either side of current page.
 * @param    int    $paged        The current page number.
 */
function the_aec_pagination( $numpages = '', $pagerange = '', $paged = '' ) {

	if( empty( $pagerange ) ) {
    	$pagerange = 2;
  	}

  	/**
   	 * This first part of our function is a fallback
     * for custom pagination inside a regular loop that
     * uses the global $paged and global $wp_query variables.
     * 
     * It's good because we can now override default pagination
     * in our theme, and use this function in default quries
     * and custom queries.
     */
   	global $paged;
   
  	if( empty( $paged ) ) {
    	$paged = 1;
  	}
	
  	if( $numpages == '' ) {
    	global $wp_query;
    	
		$numpages = $wp_query->max_num_pages;
    	if( ! $numpages ) {
        	$numpages = 1;
    	}
  	}

  	/** 
   	 * We construct the pagination arguments to enter into our paginate_links
   	 * function. 
   	 */
	$arr_params = array( 'view', 'cat', 'venue', 'date', 'from', 'to' );
	
	$base = aec_remove_query_arg( $arr_params, get_pagenum_link(1) ) . '%_%';
		
	if( ! get_option( 'permalink_structure' ) || isset( $_GET['aec'] ) ) {
		$prefix = strpos( $base, '?' ) ? '&' : '?';
    	$format = $prefix.'paged=%#%';
    } else {
		$prefix = ( '/' == substr( $base, -1 ) ) ? '' : '/';
    	$format = $prefix.'page/%#%';
    } 
	
  	$pagination_args = array(
    	'base'         => $base,
    	'format'       => $format,
    	'total'        => $numpages,
    	'current'      => $paged,
    	'show_all'     => false,
    	'end_size'     => 1,
    	'mid_size'     => $pagerange,
    	'prev_next'    => true,
    	'prev_text'    => __( '&laquo;' ),
    	'next_text'    => __( '&raquo;' ),
    	'type'         => 'array',
    	'add_args'     => false,
    	'add_fragment' => ''
  	);

  	$paginate_links = paginate_links( $pagination_args );

  	if( $paginate_links ) {
		echo "<div class='row text-center aec-no-margin'>";
		
		echo "<div class='text-muted'>";
		printf( __( "Page %d of %d", 'another-events-calendar' ), $paged, $numpages );
		echo "</div>";
		
		echo "<ul class='pagination'>"; 		   	
		foreach( $paginate_links as $key => $page_link ) {
		
			if( strpos( $page_link, 'current' ) !== false ) {
			 	echo '<li class="active">'.$page_link.'</li>';
			} else {
				echo '<li>'.$page_link.'</li>';
			}
			
		}
   		echo "</ul>";
		
		echo "</div>";
  	}
	
}

/**
 * Display the socialshare buttons.
 *
 * @since    1.0.0
 */
function the_aec_socialshare_buttons() {

	global $post;

	$page_settings        = get_option( 'aec_page_settings' );
	$socialshare_settings = get_option( 'aec_socialshare_settings' );
	
	$content = '';
	$page    = '';
	
	if( is_singular('aec_events') ) {
	
		$page = 'event_detail';
		
	} else {
	
		if( $page_settings['categories'] == $post->ID ) {
			$page = 'categories';
		}
		
		if( in_array( $post->ID, array( $page_settings['calendar'], $page_settings['events'], $page_settings['category'], $page_settings['tag'], $page_settings['venue'], $page_settings['organizer'],  $page_settings['search'] ) ) ) {
			$page = 'event_archives';
		}
		
	}	
	
	if( isset( $socialshare_settings['pages'] ) && in_array( $page, $socialshare_settings['pages'] ) ) {
		
		// Vars
		$site_title = urlencode( get_bloginfo('name') );
    	$permalink = urlencode( aec_get_current_url() );
		$title = urlencode( get_the_title() );
		$post_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID  ), 'full' ); 
		
		// If current page = single category page
		if( $post->ID == $page_settings['category'] ) {
			if( $slug = get_query_var( 'aec_category' ) ) {
				$term = get_term_by( 'slug', $slug, 'aec_categories' );

				$title = $term->name;
			}
		}
				
		// If current page = single tag page
		if( $post->ID == $page_settings['tag'] ) {
			if( $slug = get_query_var( 'aec_tag' ) ) {
				$term = get_term_by( 'slug', $slug, 'aec_tags' );

				$title = $term->name;
			}
		}
				
		// If current page = single venue page
		if( $post->ID == $page_settings['venue'] ) {
			if( $slug = get_query_var( 'aec_venue' ) ) {
				$page = get_page_by_path( $slug, OBJECT, 'aec_venues' );
			
				$title = $page->post_title;	
			}
		}
				
		// If current page = single organizer page
		if( $post->ID == $page_settings['organizer'] ) {
			if( $slug = get_query_var( 'aec_organizer' ) ) {
				$page = get_page_by_path( $slug, OBJECT, 'aec_organizers' );
			
				$title = $page->post_title;	
				$post_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $page->ID  ), 'full' ); 
			}
		}	
		
		$thumbnail = '';
		if( ! empty( $post_thumbnail ) ) { 
			$thumbnail = urlencode( $post_thumbnail[0] );
		}		

		// Construct sharing URL without using any script
		$facebook_url  = 'https://www.facebook.com/sharer/sharer.php?u='.$permalink;
		$twitter_url   = 'https://twitter.com/intent/tweet?text='.$title.'&amp;url='.$permalink.'&amp;via='.$site_title;
		$google_url    = 'https://plus.google.com/share?url='.$permalink;
		$linkedin_url  = 'https:///www.linkedin.com/shareArticle?url='.$permalink.'&amp;text='.$title;
		$pinterest_url = 'https://pinterest.com/pin/create/button/?url='.$permalink.'&amp;media='.$thumbnail.'&amp;description='.$title;

		// Add sharing button at the end of page/page content
		$meta = array();

		if( in_array( 'facebook', $socialshare_settings['services'] ) ) {
			$meta[] = sprintf( '<a class="aec-social-btn aec-facebook" href="%s" target="_blank">%s</a>', $facebook_url, __( 'Facebook','another-events-calendar' ) );
		}
	
		if( in_array( 'twitter', $socialshare_settings['services'] ) ) {
			$meta[] = sprintf( '<a class="aec-social-btn aec-twitter" href="%s" target="_blank">%s</a>', $twitter_url, __( 'Twitter','another-events-calendar' ) );
		}
		
		if( in_array( 'gplus', $socialshare_settings['services'] ) ) {
			$meta[] = sprintf( '<a class="aec-social-btn aec-googleplus" href="%s" target="_blank">%s</a>', $google_url,  __( 'Google+','another-events-calendar' ) );
		}
		
		if( in_array( 'linkedin', $socialshare_settings['services'] ) ) {
			$meta[] = sprintf( '<a class="aec-social-btn aec-linkedin" href="%s" target="_blank">%s</a>', $linkedin_url, __( 'Linkedin','another-events-calendar' ) );
		}
		
		if( in_array( 'pinterest', $socialshare_settings['services'] ) ) {
			$meta[] = sprintf( '<a class="aec-social-btn aec-pinterest" href="%s" target="_blank">%s</a>',  $pinterest_url, __( 'Pin It','another-events-calendar' ) );
		}
		
		if( count( $meta ) ) {
			$content .= '<div class="aec-social">';
			$content .= implode( '', $meta );
			$content .= '</div>';
		}
		
	}
	
	echo $content;
	
}
	
/**
 * Get Event Date.
 *
 * @since    1.0.0
 */
function aec_get_event_date( $post_id ) {

	$all_day_event   = get_post_meta( $post_id, 'all_day_event', true );
	$start_date_time = get_post_meta( $post_id, 'start_date_time', true );
	$end_date_time   = get_post_meta( $post_id, 'end_date_time', true );
	
	// If All Day Event
	if( $all_day_event ) {
	
		$formatted_date = date_i18n( get_option( 'date_format' ), strtotime( $start_date_time ) );
		
		// If there is end date
		if( '0000-00-00 00:00:00' != $end_date_time ) {
			// Find the time difference between start date and end date
			$datetime1 = new DateTime( $start_date_time );
			$datetime2 = new DateTime( $end_date_time );
			$interval = $datetime1->diff( $datetime2 );
			$day_diff = $interval->format('%a');
		
			// If there is day difference
			if( $day_diff > 0 ) {
				$formatted_date = $formatted_date . ' - ' .date_i18n( get_option( 'date_format' ), strtotime( $end_date_time ) );
			}
		}
		
	} else {
	
		$formatted_date = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $start_date_time ) );
	
		// If there is end date
		if( '0000-00-00 00:00:00' != $end_date_time ) {
			// Find the time difference between start date and end date
			$datetime1 = new DateTime( $start_date_time );
			$datetime2 = new DateTime( $end_date_time );
			$interval = $datetime1->diff( $datetime2 );
			$day_diff = $interval->format('%a');
		
			// If day difference less than 1 day
			if( $day_diff < 1 ) {
				$formatted_date = $formatted_date . ' - ' .date_i18n( get_option( 'time_format' ), strtotime( $end_date_time ) );
			}
		
			// Else
			else {
				$formatted_date = $formatted_date . ' - ' .date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $end_date_time ) );
			}
		}
		
	}	
	
	return $formatted_date;

}

/**
 * Get Countries.
 *
 * @since    1.0.0
 *
 * @return   array    Countries List.
 */
function aec_get_countries() {

	$countries = array(
		"AF" => "Afghanistan",
		"AL" => "Albania",
		"DZ" => "Algeria",
		"AS" => "American Samoa",
		"AD" => "Andorra",
		"AO" => "Angola",
		"AI" => "Anguilla",
		"AQ" => "Antarctica",
		"AG" => "Antigua and Barbuda",
		"AR" => "Argentina",
		"AM" => "Armenia",
		"AW" => "Aruba",
		"AU" => "Australia",
		"AT" => "Austria",
		"AZ" => "Azerbaijan",
		"BS" => "Bahamas",
		"BH" => "Bahrain",
		"BD" => "Bangladesh",
		"BB" => "Barbados",
		"BY" => "Belarus",
		"BE" => "Belgium",
		"BZ" => "Belize",
		"BJ" => "Benin",
		"BM" => "Bermuda",
		"BT" => "Bhutan",
		"BO" => "Bolivia",
		"BA" => "Bosnia and Herzegovina",
		"BW" => "Botswana",
		"BV" => "Bouvet Island",
		"BR" => "Brazil",
		"BQ" => "British Antarctic Territory",
		"IO" => "British Indian Ocean Territory",
		"VG" => "British Virgin Islands",
		"BN" => "Brunei",
		"BG" => "Bulgaria",
		"BF" => "Burkina Faso",
		"BI" => "Burundi",
		"KH" => "Cambodia",
		"CM" => "Cameroon",
		"CA" => "Canada",
		"CT" => "Canton and Enderbury Islands",
		"CV" => "Cape Verde",
		"KY" => "Cayman Islands",
		"CF" => "Central African Republic",
		"TD" => "Chad",
		"CL" => "Chile",
		"CN" => "China",
		"CX" => "Christmas Island",
		"CC" => "Cocos [Keeling] Islands",
		"CO" => "Colombia",
		"KM" => "Comoros",
		"CG" => "Congo - Brazzaville",
		"CD" => "Congo - Kinshasa",
		"CK" => "Cook Islands",
		"CR" => "Costa Rica",
		"HR" => "Croatia",
		"CU" => "Cuba",
		"CY" => "Cyprus",
		"CZ" => "Czech Republic",
		"CI" => "Côte d’Ivoire",
		"DK" => "Denmark",
		"DJ" => "Djibouti",
		"DM" => "Dominica",
		"DO" => "Dominican Republic",
		"NQ" => "Dronning Maud Land",
		"DD" => "East Germany",
		"EC" => "Ecuador",
		"EG" => "Egypt",
		"SV" => "El Salvador",
		"GQ" => "Equatorial Guinea",
		"ER" => "Eritrea",
		"EE" => "Estonia",
		"ET" => "Ethiopia",
		"FK" => "Falkland Islands",
		"FO" => "Faroe Islands",
		"FJ" => "Fiji",
		"FI" => "Finland",
		"FR" => "France",
		"GF" => "French Guiana",
		"PF" => "French Polynesia",
		"TF" => "French Southern Territories",
		"FQ" => "French Southern and Antarctic Territories",
		"GA" => "Gabon",
		"GM" => "Gambia",
		"GE" => "Georgia",
		"DE" => "Germany",
		"GH" => "Ghana",
		"GI" => "Gibraltar",
		"GR" => "Greece",
		"GL" => "Greenland",
		"GD" => "Grenada",
		"GP" => "Guadeloupe",
		"GU" => "Guam",
		"GT" => "Guatemala",
		"GG" => "Guernsey",
		"GN" => "Guinea",
		"GW" => "Guinea-Bissau",
		"GY" => "Guyana",
		"HT" => "Haiti",
		"HM" => "Heard Island and McDonald Islands",
		"HN" => "Honduras",
		"HK" => "Hong Kong SAR China",
		"HU" => "Hungary",
		"IS" => "Iceland",
		"IN" => "India",
		"ID" => "Indonesia",
		"IR" => "Iran",
		"IQ" => "Iraq",
		"IE" => "Ireland",
		"IM" => "Isle of Man",
		"IL" => "Israel",
		"IT" => "Italy",
		"JM" => "Jamaica",
		"JP" => "Japan",
		"JE" => "Jersey",
		"JT" => "Johnston Island",
		"JO" => "Jordan",
		"KZ" => "Kazakhstan",
		"KE" => "Kenya",
		"KI" => "Kiribati",
		"KW" => "Kuwait",
		"KG" => "Kyrgyzstan",
		"LA" => "Laos",
		"LV" => "Latvia",
		"LB" => "Lebanon",
		"LS" => "Lesotho",
		"LR" => "Liberia",
		"LY" => "Libya",
		"LI" => "Liechtenstein",
		"LT" => "Lithuania",
		"LU" => "Luxembourg",
		"MO" => "Macau SAR China",
		"MK" => "Macedonia",
		"MG" => "Madagascar",
		"MW" => "Malawi",
		"MY" => "Malaysia",
		"MV" => "Maldives",
		"ML" => "Mali",
		"MT" => "Malta",
		"MH" => "Marshall Islands",
		"MQ" => "Martinique",
		"MR" => "Mauritania",
		"MU" => "Mauritius",
		"YT" => "Mayotte",
		"FX" => "Metropolitan France",
		"MX" => "Mexico",
		"FM" => "Micronesia",
		"MI" => "Midway Islands",
		"MD" => "Moldova",
		"MC" => "Monaco",
		"MN" => "Mongolia",
		"ME" => "Montenegro",
		"MS" => "Montserrat",
		"MA" => "Morocco",
		"MZ" => "Mozambique",
		"MM" => "Myanmar [Burma]",
		"NA" => "Namibia",
		"NR" => "Nauru",
		"NP" => "Nepal",
		"NL" => "Netherlands",
		"AN" => "Netherlands Antilles",
		"NT" => "Neutral Zone",
		"NC" => "New Caledonia",
		"NZ" => "New Zealand",
		"NI" => "Nicaragua",
		"NE" => "Niger",
		"NG" => "Nigeria",
		"NU" => "Niue",
		"NF" => "Norfolk Island",
		"KP" => "North Korea",
		"VD" => "North Vietnam",
		"MP" => "Northern Mariana Islands",
		"NO" => "Norway",
		"OM" => "Oman",
		"PC" => "Pacific Islands Trust Territory",
		"PK" => "Pakistan",
		"PW" => "Palau",
		"PS" => "Palestinian Territories",
		"PA" => "Panama",
		"PZ" => "Panama Canal Zone",
		"PG" => "Papua New Guinea",
		"PY" => "Paraguay",
		"YD" => "People's Democratic Republic of Yemen",
		"PE" => "Peru",
		"PH" => "Philippines",
		"PN" => "Pitcairn Islands",
		"PL" => "Poland",
		"PT" => "Portugal",
		"PR" => "Puerto Rico",
		"QA" => "Qatar",
		"RO" => "Romania",
		"RU" => "Russia",
		"RW" => "Rwanda",
		"RE" => "Réunion",
		"BL" => "Saint Barthélemy",
		"SH" => "Saint Helena",
		"KN" => "Saint Kitts and Nevis",
		"LC" => "Saint Lucia",
		"MF" => "Saint Martin",
		"PM" => "Saint Pierre and Miquelon",
		"VC" => "Saint Vincent and the Grenadines",
		"WS" => "Samoa",
		"SM" => "San Marino",
		"SA" => "Saudi Arabia",
		"SN" => "Senegal",
		"RS" => "Serbia",
		"CS" => "Serbia and Montenegro",
		"SC" => "Seychelles",
		"SL" => "Sierra Leone",
		"SG" => "Singapore",
		"SK" => "Slovakia",
		"SI" => "Slovenia",
		"SB" => "Solomon Islands",
		"SO" => "Somalia",
		"ZA" => "South Africa",
		"GS" => "South Georgia and the South Sandwich Islands",
		"KR" => "South Korea",
		"ES" => "Spain",
		"LK" => "Sri Lanka",
		"SD" => "Sudan",
		"SR" => "Suriname",
		"SJ" => "Svalbard and Jan Mayen",
		"SZ" => "Swaziland",
		"SE" => "Sweden",
		"CH" => "Switzerland",
		"SY" => "Syria",
		"ST" => "São Tomé and Príncipe",
		"TW" => "Taiwan",
		"TJ" => "Tajikistan",
		"TZ" => "Tanzania",
		"TH" => "Thailand",
		"TL" => "Timor-Leste",
		"TG" => "Togo",
		"TK" => "Tokelau",
		"TO" => "Tonga",
		"TT" => "Trinidad and Tobago",
		"TN" => "Tunisia",
		"TR" => "Turkey",
		"TM" => "Turkmenistan",
		"TC" => "Turks and Caicos Islands",
		"TV" => "Tuvalu",
		"UM" => "U.S. Minor Outlying Islands",
		"PU" => "U.S. Miscellaneous Pacific Islands",
		"VI" => "U.S. Virgin Islands",
		"UG" => "Uganda",
		"UA" => "Ukraine",
		"SU" => "Union of Soviet Socialist Republics",
		"AE" => "United Arab Emirates",
		"GB" => "United Kingdom",
		"US" => "United States",
		"ZZ" => "Unknown or Invalid Region",
		"UY" => "Uruguay",
		"UZ" => "Uzbekistan",
		"VU" => "Vanuatu",
		"VA" => "Vatican City",
		"VE" => "Venezuela",
		"VN" => "Vietnam",
		"WK" => "Wake Island",
		"WF" => "Wallis and Futuna",
		"EH" => "Western Sahara",
		"YE" => "Yemen",
		"ZM" => "Zambia",
		"ZW" => "Zimbabwe",
		"AX" => "Åland Islands",
	);
	
	return apply_filters( 'aec_countries', $countries );

}

/**
 * Get Country Name.
 *
 * @since    1.0.0
 *
 * @param    string    $key    AEC Country Key.
 * @return   string    	       Country Name.
 */
function aec_country_name( $key ) {

	$countries = aec_get_countries();
	return $countries[ $key ];
	
}