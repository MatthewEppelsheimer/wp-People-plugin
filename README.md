# The 'People' Plugin #

This is a WordPress plugin that creates a custom post type for "people", where each post of this type is a "person". This is useful for overloading the built-in WordPress "user" construct, and for making creating entities that might map to real-world people, but that don't have any WordPress user account.

## Installation ##

1. Install from [the WordPress.org plugin repository](TBD).
2. Activate the 'People' plugin via your site's `wp-admin/`.

## Usage ##

* Invoke with the `[people]` shortcode.
* Customize output by adding a filter to `people_item_callback` (see "Customizing Output", below)

For more usage information, see the `readme.txt` file, or [the plugin's page on WordPress.org](TBD).

## How to Contribute ##

Pull requests [on Github](https://github.com/rocketlift/wp-people-plugin/) are very welcome!

## Customizing Output ##

Override the default shortcode's HTML output by creating a new filter function hooked to the `people_item_callback` filter.

Here's an example:

```php
add_filter('people_item_callback', 'my_people_shortcode_output' );

function my_people_shortcode_output() {	
	// register global variables
	global $post;
	
	// Get an array of the person's data
	$person = People_Post_Type::get_person(); 

	// Set up individual variables of data
	$thumbnail = get_the_post_thumbnail( $post->ID );

	$name = $person['name'];

	$titles = $person['title'] // an array
	$title_html = '';
	foreach ( $titles as $title ) {
		$title_html .= "<span class='person-title title'>" . esc_html( $title ) . "</span>";
	}

	$emails = $person['email']; // an array
	$email_html = '';
	foreach ( $emails as $email ) {
		$email_html .= "<a href='mailto:" . esc_attr( $email ) . "' class='email'>" . esc_html( $email ) . "</a>";
	}

	$full_bio = $person['full_bio'];

	// Generate HTML for the person
	$out = "<div class='person'>";
		$out .= "<div class='person-photo'>$thumbnail</div>";
		$out .= "<h2><span class='person-name'>" . esc_html( $name ) . "</span></h2>";
		$out .= "<p class='person-meta'>$titles_html</p>";
		$out .= "<p class='person-contact'>$email_html</p>";
	$out .= "</div>";
	$out .= "<div class='person-long-bio'>" . esc_html( $full_bio ) . "</div>";

	// Return generated output	
	return $out;
}
);
```
