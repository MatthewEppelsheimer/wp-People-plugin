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

### Overriding the Default Template ###

You can override the default HTML output for a list of People by creating a new template function and hooking it to the `people_item_callback` filter.

Here's an example:

```php
add_filter('people_item_callback', 'my_person_template' );

function my_person_template() {	
	// Get an object representing the person's data
	$person = People_Post_Type::get_person(); 

	// Set up individual variables of data
	$thumbnail = $person->get_thumbnail();

	$name = $person->get_name();

	// People supports multiple title per person, so `get_titles()` returns
	// an array. `people_render_titles()` loops over that array, rendering HTML.
	$titles = $person->get_titles();
	$title_html = people_render_titles( $titles );

	// Similarly, `get_emails()` also returns an array.
	$emails = $person->get_emails();
	$email_html = people_render_emails( $emails );
	
	$full_bio = $person->get_bio();

	// Generate HTML for the person
	$out = "<div class='person'>";
		$out .= "<div class='person-photo'>$thumbnail</div>";
		$out .= "<h2><span class='person-name'>" . esc_html( $name ) . "</span></h2>";
		$out .= "<p class='person-meta'>" . esc_html( $titles_html ) . "</p>";
		$out .= "<p class='person-contact'>" . esc_html( $email_html ) . "</p>";
	$out .= "</div>";
	$out .= "<div class='person-long-bio'>" . esc_html( $full_bio ) . "</div>";

	// Return generated output	
	return $out;
}
```
