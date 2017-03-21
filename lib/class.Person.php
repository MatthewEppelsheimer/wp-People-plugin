<?php
/**
 * Model class representing a single Person
 */

class Person {
	/**
	 * @var int ID of the corresponding People post
	 */
	private $ID;

	/**
	 * @var object WP_Query object containing just the corresponding People post
	 */
	private $person;

	/**
	 * @var string The person's name
	 * Not stored during instantiation; only stored if looked up later.
	 */
	private $name;

	/**
	 * @var array Array of post meta for the corresponding People post
	 */
	private $meta;

	/**
	 * @var string Prefix for post meta values
	 */
	private $meta_prefix;

	/**
	 * @var array Array of titles associated with the person.
	 * Not stored during instantiation; only stored if looked up later.
	 */
	private $titles;

	/**
	 * @var array Array of email addresses associated with the person.
	 * Not stored during instantiation; only stored if looked up later.
	 */
	private $emails;

	/**
	 * @var array Array of phone number data associated with the person
	 *
	 * Specifically, an array of arrays, to support phone number / extension field pairs
	 * Not stored during instantiation; only stored if looked up later.
	 */
	private $phone_numbers;

	/**
	 * @var array Array of social media profiles associated with the person
	 * Not stored during instantiation; only stored if looked up later.
	 */
	private $social_media_profiles;

	/**
	 * @var object WP_User object associated with person, if any.
	 * Not stored during instantiation; only stored if looked up later.
	 */
	private $user;

	/**
	 * Instantiate the class
	 *
	 * @param int $id post ID corresponding to the People post (default: null)
	 *
	 * @return null if there is no corresponding People post; otherwise, a WP_Query object
	 */
	public function __construct( $id = null ) {
		if ( empty( $id ) ) {
			return null;
		}

		$this->ID = $id;
		$this->person = People_Post_Type::query_people( array( 'p' => $id ) );
		$this->meta = get_post_meta( $id, '' );
		$this->meta_prefix = '_' . RLI_PEOPLE_PREFIX;

		return $this->person;
	}

	/**
	 * Get the person's name
	 *
	 * @return string The person's name
	 */
	public function get_name() {
		if ( isset( $this->name ) ) {
			return $this->name;
		}

		$name = get_the_title( $this->ID );

		$this->name = $name;

		return $name;
	}

	/**
	 * Get the person's featured image
	 *
	 * @param string $size The registered image size to crop to
	 *
	 * @return string The thumbnail's markup
	 */
	public function get_thumbnail( $size = 'thumbnail' ) {
		$attachment_id = get_post_thumbnail_id( $this->ID );
		$thumbnail = get_post_thumbnail_without_dimensions( $attachment_id, $size );

		return $thumbnail;
	}

	/**
	 * Get the URL for the person's featured image
	 *
	 * @param string $size The registered image size to crop to
	 *
	 * @return string The thumbnail's URL
	 */
	public function get_thumbnail_url( $size = 'thumbnail' ) {
		$thumbnail_url = get_the_post_thumbnail_url( $this->ID, $size );

		return $thumbnail_url;
	}

	/**
	 * Get the person's bio
	 *
	 * @return string The bio content
	 */
	public function get_bio() {
		$bio = $this->person->post->post_content;

		return $bio;
	}

	/**
	 * Get the person's short bio
	 *
	 * Use the exerpt, falling back to truncated post content if it doesn't exist.
	 *
	 * @param int   $character_limit    Optional number of characters the excerpt can't exceed
	 *                                  Pass `false` for no limit
	 *
	 * @return string
	 */
	public function get_short_bio( $character_limit = 140 ) {
		$short_bio = $this->person->post->post_excerpt;

		if ( empty( $short_bio ) ) {
			$short_bio = apply_filters( 'the_excerpt', self::get_bio() );
		}

		$suffix = '';
		if ( $character_limit ) {
			if ( strlen( $short_bio ) > $character_limit ) {
				$suffix = '...';
			}

			$short_bio = substr( $short_bio, 0, $character_limit );
		}

		$output = $short_bio . $suffix;

		return $output;
	}

	/**
	 * Get a person's post meta value, given a meta key
	 *
	 * Accepts a $short_key string to abstract post_meta prefixes from users.
	 *
	 * @param string $short_key The desired post_meta key value, without prefix
	 *
	 * @uses private var meta_prefix for post_meta key prefix
	 *
	 * @return bool|null|mixed    Returns null if not passed a $short_key.
	 *                            Returns false if there is no value for the given key
	 *                            Otherwise, returns value stored for the key.
	 */
	public function get_meta( $short_key = '' ) {
		if ( '' == $short_key ) {
			return null;
		}

		$needs_prefix = true;

		// If $short_key begins with RLI_PEOPLE_PREFIX or '_',
		// then treat it as already prefixed
		if ( 0 === strpos( $short_key, RLI_PEOPLE_PREFIX ) ) {
			$needs_prefix = false;
		} elseif ( 0 === strpos( $short_key, '_' ) ) {
			$needs_prefix = false;
		}

		if ( $needs_prefix ) {
			$key = $this->meta_prefix . $short_key;
		} else {
			$key = $short_key;
		}

		if ( empty( $this->meta[$key] ) ) {
			return false;
		}

		return $this->meta[$key];
	}

	/**
	 * Get a person's titles.
	 *
	 * Uses private property $titles when available; otherwise sets it.
	 *
	 * @uses Person->get_meta()
	 *
	 * @return bool|array An array of titles, or false if there are none.
	 */
	public function get_titles() {
		if ( isset( $this->titles ) ) {
			return $this->titles;
		}

		$titles = array();
		$titles_group = maybe_unserialize( $this->get_meta( 'group_title', false ) );
		// I'm confused why the 0 index is needed. Is CMB2 nesting 2 layers deep
		// for some good reason, or is local logic messing w/ the data?
		$titles_items = maybe_unserialize( $titles_group[0] );

		if ( ! empty( $titles_items ) ) {
			foreach ( $titles_items as $probably_serialized_item ) {
				$item = maybe_unserialize( $probably_serialized_item );

				if ( isset( $item['title'] ) ) {
					$titles[] = esc_html( $item['title'] );
				}
			}
		}

		if ( empty ( $titles ) ) {
			$titles = false;
		}

		$this->titles = $titles;

		return $titles;
	}

	/**
	 * Get a person's emails.
	 *
	 * Uses private property $emails when available; otherwise sets it.
	 *
	 * @uses Person->get_meta()
	 * @return array|bool An array of emails, or false if there are none.
	 */
	public function get_emails() {
		if ( isset( $this->emails ) ) {
			return $this->emails;
		}

		$emails = array();
		$emails_group = maybe_unserialize( $this->get_meta( 'group_email', false ) );
		// I'm confused why the 0 index is needed. Is CMB2 nesting 2 layers deep
		// for some good reason, or is local logic messing w/ the data?
		$email_items = maybe_unserialize( $emails_group[0] );

		if ( empty( $email_items ) ) {
			return false;
		}

		foreach ( $email_items as $probably_serialized_item ) {
			$item = maybe_unserialize( $probably_serialized_item );

			if ( isset( $item['email'] ) ) {
				$emails[] = esc_html( $item['email'] );
			}
		}

		if ( empty ( $emails ) ) {
			$emails = false;
		}

		$this->emails = $emails;

		return $emails;
	}

	function get_user() {
		if ( isset( $this->user ) ) {
			return $this->user;
		}

		$user_id_array = $this->get_meta( '_' . RLI_PEOPLE_PREFIX . 'user' );

		if ( $user_id_array ) {
			$user = new WP_User( $user_id_array[0] );

			$this->user = $user;

			return $user;
		}

		$this->user = false;

		return false;
	}

	/**
	 * Get a person's social media profiles
	 *
	 * Uses private property $social_media_profiles when available; otherwise sets it.
	 *
	 * @uses Person->get_meta()
	 * @return array|bool An array of emails, or false if there are none.
	 */
	public function get_social_media_profiles() {
		if ( isset( $this->social_media_profiles ) ) {
			return $this->social_media_profiles;
		}

		$social_media_profiles = array();
		$profiles_group = maybe_unserialize( $this->get_meta( 'group_social', false ) );
		$profile_items = maybe_unserialize( $profiles_group[0] );

		if ( empty( $profile_items ) ) {
			return false;
		}

		foreach ( $profile_items as $probably_serialized_item ) {
			$item = maybe_unserialize( $probably_serialized_item );

			if ( isset( $item['profile_url'] ) ) {
				$social_media_profiles[] = esc_html( $item['profile_url'] );
			}
		}

		if ( empty ( $social_media_profiles ) ) {
			$social_media_profiles = false;
		}

		$this->social_media_profiles = $social_media_profiles;

		return $social_media_profiles;
	}

	/**
	 * Get a person's phone number fields
	 *
	 * Returns an array of arrays, each grouping phone number fields (such as main number and
	 * extension). Uses private property $phone_numbers when available; otherwise sets it.
	 *
	 * @uses Person->get_meta()
	 *
	 * @return array|bool An array of arrays with phone number fields, or false if there are none.
	 */
	public function get_phone_numbers() {
		if ( isset( $this->phone_numbers ) ) {
			return $this->phone_numbers;
		}

		$phone_numbers = array();
		$phone_group = maybe_unserialize( $this->get_meta( 'group_phone', false ) );
		// I'm confused why the 0 index is needed. Is CMB2 nesting 2 layers deep
		// for some good reason, or is local logic messing w/ the data?
		$phone_items = maybe_unserialize( $phone_group[0] );

		if ( ! empty ( $phone_items ) ) {
			foreach ( $phone_items as $probably_serialized_item ) {
				$item = maybe_unserialize( $probably_serialized_item );

				$item_array = array();
				if ( isset( $item['phone'] ) ) {
					$item_array['phone'] = esc_html( $item['phone'] );
				}
				if ( isset( $item['extension'] ) ) {
					$item_array['extension'] = esc_html( $item['extension'] );
				}
				// @todo create a filter for extensibility here?
				$phone_numbers[] = $item_array;
			}
		}

		if ( empty ( $phone_numbers ) ) {
			$phone_numbers = false;
		}

		$this->phone_numbers = $phone_numbers;

		return $phone_numbers;
	}

}
