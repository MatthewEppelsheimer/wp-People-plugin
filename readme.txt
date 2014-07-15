=== People === 
Contributors: Kevin Lenihan, mattheweppelsheimer
Tags: people, post type, Rocket Lift
Requires at least: 3.5.1
Tested up to: 3.9.1
Stable: 1.0.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Creates a post type for people and provides useful filters to easily adapt the plugin to suite your needs.

   
== Description ==

People lets you manage and display individuals and lists of people on your site. It is intended for displaying bio pages for staff members on a company or organization's website. It is built with flexibility in mind. It is easy for web developers to add more fields (e.g. adding a link to a social network profile) and to use as the foundation for a custom Customer Relationship Management (CRM) or contacts database.

The plugin adds a 'people' post type with standard fields (email, phone, etc.) for a basic staff bio page, and also allows advanced users to easily adapt the plugin to suite their needs.

By default this plugin supports these fields for a Person:

* Name (Uses WordPress's existing title field)
* Title (Title as in the person's job title, not the title of the post type)
* Email
* Full Bio (Uses WordPress's existing content field)
* Brief Bio (Uses WordPress's existing excerpt field)
* Featured Image (Uses WordPress's existing functionality for thumbnails)
* Order: Number used to sort the people (`0` = High Priority, `10` = Low Priority)


= Available Functions =

People makes the following functions available for use in custom themes:

* `People_Post_Type::get_person()` (or, if you don't like classes, `people_get_person()` does the same thing). This returns an array containing all the relevant data about a person. NOTE: must be used from within the loop.
* `People_Post_Type::list_people( $args, $callback )` (or if you still don't like classes, `people_list_people( $args, $callback )` will work). This returns a string of html code for rendering all the people that meet the criteria of WP_Query( $args ).
* `People_Post_Type::render_single_person()` (or if you are bent on not using classes, use `people_render_single_person()` ).
	This returns a string of html code for rendering a single person. useful in `single.php` or, even better, `single-people.php`. 

People makes these filter and action hooks that allow you to modify the plugin's behavior without editing its files, eliminating the fear of updating. This is just a list of the hooks with a brief description of what they are for. See below for examples of use.

* `people_atts`: used to add a field to the array returned by `People_Post_Type::get_person()`
* `people_item_callback`: used to set a user defined default for how to render an person in a list of people.
* `people_single_callback`: used to set a user defined default for how to render a single person
* `people_create_metaboxes`: Used to add a custom metabox
* `people_title_metabox_render`: used to add fields to the title metabox
* `people_email_metabox_render`: used to add fields to the email metabox


= Available Shortcodes =

* `[people]` : display the list of people


== Installation ==

1. Upload `people-post-type/` to the `/wp-content/plugins/` directory
2. Activate the plugin, 'People', through the 'Plugins' menu in WordPress


== Adding your own Metabox ==

Note: Adding Metaboxes does require familiarality with WordPress coding and should only be done by those who are comfortable with filter and action hooks. Examples for how to create metaboxes are in `people-post-type/lib/defaults.php`

Here is the basic template:

```
add_action( 'people_create_metaboxes', 
	function() {
		// replace the ~...~ with your info
		add_meta_box( '~metabox_id~', __( '~Metabox Display Name~', 'people' ), 'render_people_~metabox_name~_metabox', 'people', 'normal', 'high' );
	}
);
   
function render_people_~metabox_name~_metabox() {
	// add code to render actual html
 }

add_action( 'save_post',
	function( $post_id ) {
		if ( 'people' == get_post_type( $post_id ) ){
			// add the code to save your new field(s)
		}
	}
);

add_filter( 'people_atts',
	function( $arr, $id ) {
		$arr['~meta_field_name~'] = ~meta_field_value~;
		return $arr;
	},
	2,
	2
);
```


== Using the Shortcode `[people]` ==

You can simply use the shortcode `[people]` to generate a list of all people using the default display function. 

== Modifying HTML Output ==

If this you want to display a person differently because you don't like the style of the default display or if you added meta fields that you want to display, add a filter to 'people_item_callback' that returns a string of html code for rendering a person within a list. 

```
add_filter('people_item_callback', 
	function(){
		global $post;
		
		$person = People_Post_Type::get_person(); // use this line to get an array of useful information about the person, currently in the loop
		//NOTE: The thumbnail is not included in array returned from People_Post_Type::get_person(), to allow you to specify the size of the thumbnail you want.
		
		// html code for a single person
		// IMPORTANT: must return html as string. DO NOT echo html
			// Echoing the html will cause the shortcode to print the list of people in, potentially, the wrong location.
		/*
			Bad Examples:
				1. ?> <div class="person"> <?php echo $person['name']; ?> </div> <?php
				2. echo '<div class="person">' . $person['name'] . '</div>';
		*/
		//Good Example:
		$out = "<div class='person'><div class='person-photo'>";
		$out .= get_the_post_thumbnail( $post->ID );
		$out .= "</div>
			<h2><span class='person-name'>" . $person['name'] . "</span></h2>
			<p class='person-meta'><span class='person-title title'>" . $person['title'] . "</span></p>
			<p class='person-contact'><a href=\"mailto:" . $person['email'] . "\" class='email'>" . $person['email'] . "</a></p>
		</div>
		<div class='person-long-bio'>" . $person['full_bio'] . "</div>";
		
		return $out;
	}
);
```
