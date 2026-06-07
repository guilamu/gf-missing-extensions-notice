<?php
/**
 * Plugin Name:       Gravity Forms - Missing Extension Notice
 * Plugin URI:        https://github.com/guilamu/gf-missing-extensions-notice
 * Description:       Display a warning when a Gravity Forms form requires a missing extension (GF add-on, Gravity Perks, or other).
 * Version:           1.0.0
 * Requires at least: 5.6
 * Requires PHP:      7.4
 * Tested up to:      6.8
 * Author:            Guilamu
 * License:           AGPLv3
 * License URI:       https://www.gnu.org/licenses/agpl-3.0.html
 * Text Domain:       gf-miss-ext
 * Domain Path:       /languages
 * Update URI:        https://github.com/guilamu/gf-missing-extensions-notice/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GF_MISS_EXT_VERSION', '1.0.0' );
define( 'GF_MISS_EXT_FILE', __FILE__ );
define( 'GF_MISS_EXT_DIR', plugin_dir_path( __FILE__ ) );
define( 'GF_MISS_EXT_URL', plugin_dir_url( __FILE__ ) );

// Include the GitHub auto-updater
require_once GF_MISS_EXT_DIR . 'includes/class-github-updater.php';

class GF_Missing_Extension_Notice {

	private static $markers       = null;
	private static $feed_checkers = null;

	public static function load_textdomain() {
		load_plugin_textdomain( 'gf-miss-ext', false, dirname( plugin_basename( GF_MISS_EXT_FILE ) ) . '/languages' );
	}

	public static function get_markers() {
		if ( self::$markers !== null ) {
			return self::$markers;
		}

		$markers = array(
			'uid'                       => array(
				'name'  => 'GP Unique ID',
				'type'  => 'field_type',
				'class' => 'GP_Unique_ID',
			),
			'form'                      => array(
				'name'  => 'GP Nested Forms',
				'type'  => 'field_type',
				'class' => 'GP_Nested_Forms',
			),
			'chainedselect'             => array(
				'name'  => 'GP Chained Selects',
				'type'  => 'field_type',
				'class' => array( 'GP_Chained_Selects', 'GFChainedSelects' ),
			),
			'signature'                 => array(
				'name'  => 'GP Signature',
				'type'  => 'field_type',
				'class' => array( 'GP_Signature', 'GFSignature' ),
			),

			'gppa-'                     => array(
				'name'  => 'GP Populate Anything',
				'type'  => 'key_prefix',
				'class' => 'GP_Populate_Anything',
			),
			'gpec-'                     => array(
				'name'  => 'GP eCommerce Fields',
				'type'  => 'key_prefix',
				'class' => 'GP_Ecommerce_Fields',
			),
			'gp-conditional-pricing_'   => array(
				'name'  => 'GP Conditional Pricing',
				'type'  => 'key_prefix',
				'class' => 'GP_Conditional_Pricing',
			),
			'gpnf-'                     => array(
				'name'  => 'GP Nested Forms',
				'type'  => 'key_prefix',
				'class' => 'GP_Nested_Forms',
			),
			'gpls-'                     => array(
				'name'  => 'GP Limit Submissions',
				'type'  => 'key_prefix',
				'class' => 'GP_Limit_Submissions',
			),
			'gpdtc-'                    => array(
				'name'  => 'GP Date Time Calculator',
				'type'  => 'key_prefix',
				'class' => 'GP_Date_Time_Calculator',
			),
			'gp-preview-submission_'    => array(
				'name'  => 'GP Preview Submission',
				'type'  => 'key_prefix',
				'class' => 'GP_Preview_Submission',
			),
			'gfpdf_form_settings'       => array(
				'name'  => 'Gravity PDF',
				'type'  => 'key_contains',
				'class' => 'GFPDF_Core',
			),
		);

		self::$markers = apply_filters( 'gf_missing_extension_markers', $markers );
		return self::$markers;
	}

	public static function get_feed_checkers() {
		if ( self::$feed_checkers !== null ) {
			return self::$feed_checkers;
		}

		$checkers = array(

			// Official Gravity Forms add-ons
			'gravityformsactivecampaign'   => array( 'name' => 'ActiveCampaign',     'class' => 'GF_ActiveCampaign' ),
			'gravityformsauthorizenet'     => array( 'name' => 'Authorize.Net',      'class' => 'GF_AuthorizeNet' ),
			'gravityformscampaignmonitor'  => array( 'name' => 'Campaign Monitor',   'class' => 'GF_CampaignMonitor' ),
			'gravityformscleverreach'      => array( 'name' => 'CleverReach',        'class' => 'GF_CleverReach' ),
			'gravityformsconstantcontact'  => array( 'name' => 'Constant Contact',   'class' => 'GF_ConstantContact' ),
			'gravityformscoupons'          => array( 'name' => 'Coupons',            'class' => 'GFCoupons' ),
			'gravityformsdropbox'          => array( 'name' => 'Dropbox',            'class' => 'GF_Dropbox' ),
			'gravityformsemma'             => array( 'name' => 'Emma',               'class' => 'GF_Emma' ),
			'gravityformsfreshbooks'       => array( 'name' => 'FreshBooks',         'class' => 'GF_FreshBooks' ),
			'gravityformsgetresponse'      => array( 'name' => 'GetResponse',        'class' => 'GF_GetResponse' ),
			'gravityformshelpscout'        => array( 'name' => 'Help Scout',         'class' => 'GF_HelpScout' ),
			'gravityformshubspot'          => array( 'name' => 'HubSpot',            'class' => 'GF_HubSpot' ),
			'gravityformsicontact'         => array( 'name' => 'iContact',           'class' => 'GF_iContact' ),
			'gravityformsinfusionsoft'     => array( 'name' => 'Infusionsoft',       'class' => 'GF_Infusionsoft' ),
			'gravityformsmadmimi'          => array( 'name' => 'Mad Mimi',           'class' => 'GF_MadMimi' ),
			'gravityformsmailchimp'        => array( 'name' => 'Mailchimp',          'class' => 'GF_MailChimp' ),
			'gravityformsmollie'           => array( 'name' => 'Mollie',             'class' => 'GF_Mollie' ),
			'gravityformspaypal'           => array( 'name' => 'PayPal',             'class' => 'GFPayPal' ),
			'gravityformspolls'            => array( 'name' => 'Polls',              'class' => 'GFPolls' ),
			'gravityformsquiz'             => array( 'name' => 'Quiz',               'class' => 'GFQuiz' ),
			'gravityformssendgrid'         => array( 'name' => 'SendGrid',           'class' => 'GF_SendGrid' ),
			'gravityformssignature'        => array( 'name' => 'Signature',          'class' => 'GFSignature' ),
			'gravityformsslack'            => array( 'name' => 'Slack',              'class' => 'GF_Slack' ),
			'gravityformssquare'           => array( 'name' => 'Square',             'class' => 'GF_Square' ),
			'gravityformsstripe'           => array( 'name' => 'Stripe',             'class' => 'GF_Stripe' ),
			'gravityformssurvey'           => array( 'name' => 'Survey',             'class' => 'GFSurvey' ),
			'gravityformstrello'           => array( 'name' => 'Trello',             'class' => 'GF_Trello' ),
			'gravityformstwilio'           => array( 'name' => 'Twilio',             'class' => 'GF_Twilio' ),
			'gravityformsuserregistration' => array( 'name' => 'User Registration',  'class' => 'GFUserRegistration' ),
			'gravityformswebhooks'         => array( 'name' => 'Webhooks',           'class' => 'GF_Webhooks' ),
			'gravityformszapier'           => array( 'name' => 'Zapier',             'class' => 'GF_Zapier' ),
			'gravityforms2checkout'        => array( 'name' => '2Checkout',          'class' => 'GF_TwoCheckout' ),

			// Gravity Perks (Gravity Wiz)
			// Note: Most perks below do NOT use GF feeds. They are included so that if
			// a feed slug matches a perk name, we can resolve it to a human-readable
			// label and verify installation via class_exists().
			'gp-address-autocomplete'         => array( 'name' => 'GP Address Autocomplete',      'class' => 'GP_Address_Autocomplete' ),
			'gp-advanced-calculations'        => array( 'name' => 'GP Advanced Calculations',     'class' => 'GP_Advanced_Calculations' ),
			'gp-advanced-phone'               => array( 'name' => 'GP Advanced Phone',            'class' => 'GP_Advanced_Phone' ),
			'gp-auto-lists'                   => array( 'name' => 'GP Auto Lists',                'class' => 'GP_Auto_Lists' ),
			'gp-auto-login'                   => array( 'name' => 'GP Auto Login',                'class' => 'GP_Auto_Login' ),
			'gp-blocklist'                    => array( 'name' => 'GP Blocklist',                 'class' => 'GP_Blocklist' ),
			'gp-bootstrap'                    => array( 'name' => 'GP Bootstrap',                 'class' => 'GP_Bootstrap' ),
			'gp-bulk-actions'                 => array( 'name' => 'GP Bulk Actions',              'class' => 'GP_Bulk_Actions' ),
			'gp-chained-selects'              => array( 'name' => 'GP Chained Selects',           'class' => 'GP_Chained_Selects' ),
			'gp-conditional-pricing'          => array( 'name' => 'GP Conditional Pricing',       'class' => 'GP_Conditional_Pricing' ),
			'gp-copy-cat'                     => array( 'name' => 'GP Copy Cat',                  'class' => 'GP_Copy_Cat' ),
			'gp-date-time-calculator'         => array( 'name' => 'GP Date Time Calculator',      'class' => 'GP_Date_Time_Calculator' ),
			'gp-easy-confirmation'            => array( 'name' => 'GP Easy Confirmation',         'class' => 'GP_Easy_Confirmation' ),
			'gp-easy-passthrough'             => array( 'name' => 'GP Easy Passthrough',          'class' => 'GP_Easy_Passthrough' ),
			'gp-ecommerce-fields'             => array( 'name' => 'GP eCommerce Fields',          'class' => 'GP_Ecommerce_Fields' ),
			'gp-email-validator'              => array( 'name' => 'GP Email Validator',           'class' => 'GP_Email_Validator' ),
			'gp-enhanced-ux'                  => array( 'name' => 'GP Enhanced UX',               'class' => 'GP_Enhanced_UX' ),
			'gp-entry-blocks'                 => array( 'name' => 'GP Entry Blocks',              'class' => 'GP_Entry_Blocks' ),
			'gp-entry-csv'                    => array( 'name' => 'GP Entry CSV',                 'class' => 'GP_Entry_CSV' ),
			'gp-export'                       => array( 'name' => 'GP Export',                    'class' => 'GP_Export' ),
			'gp-file-renamer'                 => array( 'name' => 'GP File Renamer',              'class' => 'GP_File_Renamer' ),
			'gp-form-editor'                  => array( 'name' => 'GP Form Editor',               'class' => 'GP_Form_Editor' ),
			'gp-google-address-autocomplete'  => array( 'name' => 'GP Google Address Autocomplete', 'class' => 'GP_Google_Address_Autocomplete' ),
			'gp-limit-choices'                => array( 'name' => 'GP Limit Choices',             'class' => 'GP_Limit_Choices' ),
			'gp-limit-dates'                  => array( 'name' => 'GP Limit Dates',               'class' => 'GP_Limit_Dates' ),
			'gp-limit-submissions'            => array( 'name' => 'GP Limit Submissions',         'class' => 'GP_Limit_Submissions' ),
			'gp-live-preview'                 => array( 'name' => 'GP Live Preview',              'class' => 'GP_Live_Preview' ),
			'gp-mailchimp'                    => array( 'name' => 'GP Mailchimp',                 'class' => 'GP_Mailchimp' ),
			'gp-multi-page-navigation'        => array( 'name' => 'GP Multi-page Navigation',     'class' => 'GP_Multi_Page_Navigation' ),
			'gp-nested-forms'                 => array( 'name' => 'GP Nested Forms',              'class' => 'GP_Nested_Forms' ),
			'gp-notify'                       => array( 'name' => 'GP Notify',                    'class' => 'GP_Notification' ),
			'gp-number-format'                => array( 'name' => 'GP Number Format',             'class' => 'GP_Number_Format' ),
			'gp-pay-per-word'                 => array( 'name' => 'GP Pay Per Word',              'class' => 'GP_Pay_Per_Word' ),
			'gp-populate-anything'            => array( 'name' => 'GP Populate Anything',         'class' => 'GP_Populate_Anything' ),
			'gp-post-content-merge-tags'      => array( 'name' => 'GP Post Content Merge Tags',   'class' => 'GP_Post_Content_Merge_Tags' ),
			'gp-preview-submission'           => array( 'name' => 'GP Preview Submission',        'class' => 'GP_Preview_Submission' ),
			'gp-qr-code'                      => array( 'name' => 'GP QR Code',                   'class' => 'GP_QR_Code' ),
			'gp-random-fields'                => array( 'name' => 'GP Random Fields',             'class' => 'GP_Random_Fields' ),
			'gp-read-only'                    => array( 'name' => 'GP Read Only',                 'class' => 'GP_Read_Only' ),
			'gp-reload-form'                  => array( 'name' => 'GP Reload Form',               'class' => 'GP_Reload_Form' ),
			'gp-save-and-continue'            => array( 'name' => 'GP Save & Continue',           'class' => 'GP_Save_And_Continue' ),
			'gp-signature'                    => array( 'name' => 'GP Signature',                 'class' => 'GP_Signature' ),
			'gp-styles'                       => array( 'name' => 'GP Styles',                    'class' => 'GP_Styles' ),
			'gp-subdirectory-uploader'        => array( 'name' => 'GP Subdirectory Uploader',     'class' => 'GP_Subdirectory_Uploader' ),
			'gp-terms-of-service'             => array( 'name' => 'GP Terms of Service',          'class' => 'GP_Terms_Of_Service' ),
			'gp-unique-id'                    => array( 'name' => 'GP Unique ID',                 'class' => 'GP_Unique_ID' ),
		);

		self::$feed_checkers = apply_filters( 'gf_missing_extension_feed_checkers', $checkers );
		return self::$feed_checkers;
	}

	public static function get_missing_extensions( $form ) {
		$form = self::ensure_full_form( $form );
		$missing = array();

		if ( is_array( $form ) && ! empty( $form['fields'] ) ) {
			foreach ( self::get_markers() as $marker => $info ) {
				if ( self::is_check_passed( $info ) ) {
					continue;
				}

				if ( self::form_matches_marker( $form, $marker, $info ) ) {
					$missing[ $marker ] = $info['name'];
				}
			}

			$field_missing = self::get_missing_field_type_extensions( $form );
			foreach ( $field_missing as $key => $name ) {
				if ( isset( $missing[ $key ] ) ) {
					continue;
				}

				$missing[ $key ] = $name;
			}
		}

		$feed_missing = self::get_missing_feed_extensions( $form );
		foreach ( $feed_missing as $key => $name ) {
			$missing[ $key ] = $name;
		}

		return $missing;
	}

	/**
	 * Normalize form and load full data from GFAPI if fields are missing.
	 *
	 * Unlike normalize_form() which only coerces type, this method attempts
	 * to fetch the complete form (with fields) from the database.
	 *
	 * @param mixed $form Form array, object, or partial.
	 * @return array|null Full form array or null.
	 */
	private static function ensure_full_form( $form ) {
		$form = self::normalize_form( $form );
		if ( ! is_array( $form ) ) {
			return null;
		}

		if ( ! empty( $form['fields'] ) ) {
			return $form;
		}

		if ( ! class_exists( 'GFAPI' ) ) {
			return $form;
		}

		$form_id = self::get_form_id( $form );
		if ( $form_id <= 0 ) {
			return $form;
		}

		$full_form = GFAPI::get_form( $form_id );
		if ( is_array( $full_form ) && ! empty( $full_form['fields'] ) ) {
			return $full_form;
		}

		return $form;
	}

	private static function form_matches_marker( $form, $marker, $info ) {
		$type = isset( $info['type'] ) ? $info['type'] : '';

		switch ( $type ) {
			case 'field_type':
				foreach ( $form['fields'] as $field ) {
					if ( self::get_saved_field_type( $field ) === $marker ) {
						return true;
					}
				}
				return false;

			case 'key_prefix':
				return self::structure_has_key_match( $form, $marker, 'prefix' );

			case 'key_contains':
				return self::structure_has_key_match( $form, $marker, 'contains' );

			default:
				return false;
		}
	}

	private static function structure_has_key_match( $data, $needle, $mode ) {
		if ( is_object( $data ) ) {
			$data = (array) $data;
		}

		if ( ! is_array( $data ) ) {
			return false;
		}

		foreach ( $data as $key => $value ) {
			if ( is_string( $key ) ) {
				if ( 'prefix' === $mode && 0 === strpos( $key, $needle ) ) {
					return true;
				}

				if ( 'contains' === $mode && false !== strpos( $key, $needle ) ) {
					return true;
				}
			}

			if ( self::structure_has_key_match( $value, $needle, $mode ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Coerce a form value to an array (object → array, array → pass-through).
	 *
	 * This does NOT load missing data from GFAPI. For that, use ensure_full_form().
	 *
	 * @param mixed $form Form array or object.
	 * @return array|null
	 */
	private static function normalize_form( $form ) {
		if ( is_array( $form ) ) {
			return $form;
		}

		if ( is_object( $form ) ) {
			return (array) $form;
		}

		return null;
	}

	private static function get_form_id( $form ) {
		if ( is_numeric( $form ) ) {
			return (int) $form;
		}

		if ( is_array( $form ) && isset( $form['id'] ) ) {
			return (int) $form['id'];
		}

		if ( is_object( $form ) && isset( $form->id ) ) {
			return (int) $form->id;
		}

		return 0;
	}

	private static function get_missing_field_type_extensions( $form ) {
		$form = self::normalize_form( $form );
		if ( ! is_array( $form ) || empty( $form['fields'] ) ) {
			return array();
		}

		$registered_field_types = self::get_registered_field_types();
		if ( empty( $registered_field_types ) ) {
			return array();
		}

		// Skip field types already covered by a marker to avoid duplicate warnings
		// (e.g. marker 'uid' => 'GP Unique ID' vs generic 'Extension for field type "uid"').
		$marker_field_types = array();
		foreach ( self::get_markers() as $marker => $info ) {
			if ( isset( $info['type'] ) && 'field_type' === $info['type'] ) {
				$marker_field_types[ $marker ] = true;
			}
		}

		$missing = array();
		foreach ( $form['fields'] as $field ) {
			$field_type = self::get_saved_field_type( $field );
			if ( '' === $field_type || isset( $registered_field_types[ $field_type ] ) ) {
				continue;
			}

			if ( isset( $marker_field_types[ $field_type ] ) ) {
				continue;
			}

			$missing[ $field_type ] = sprintf(
				/* translators: %s: Gravity Forms field type slug. */
				esc_html__( 'Extension for field type "%s"', 'gf-miss-ext' ),
				$field_type
			);
		}

		return $missing;
	}

	private static function get_registered_field_types() {
		$types = array();

		if ( class_exists( 'GF_Fields' ) && method_exists( 'GF_Fields', 'get_all' ) ) {
			$fields = GF_Fields::get_all();
			if ( is_array( $fields ) ) {
				foreach ( $fields as $field ) {
					$field_type = self::get_saved_field_type( $field );
					if ( '' === $field_type ) {
						continue;
					}

					$types[ $field_type ] = true;
				}
			}
		}

		return apply_filters( 'gf_missing_extension_registered_field_types', $types );
	}

	private static function get_saved_field_type( $field ) {
		if ( is_object( $field ) ) {
			if ( isset( $field->type ) && '' !== (string) $field->type ) {
				return (string) $field->type;
			}

			if ( isset( $field->inputType ) && '' !== (string) $field->inputType ) {
				return (string) $field->inputType;
			}

			if ( method_exists( $field, 'get_input_type' ) ) {
				$field_type = (string) $field->get_input_type();
				if ( '' !== $field_type ) {
					return $field_type;
				}
			}
		}

		if ( is_array( $field ) ) {
			if ( isset( $field['type'] ) && '' !== (string) $field['type'] ) {
				return (string) $field['type'];
			}

			if ( isset( $field['inputType'] ) && '' !== (string) $field['inputType'] ) {
				return (string) $field['inputType'];
			}
		}

		return '';
	}

	public static function get_missing_feed_extensions( $form ) {
		$form = self::normalize_form( $form );
		if ( ! is_array( $form ) || empty( $form['id'] ) ) {
			return array();
		}

		if ( ! class_exists( 'GFAPI' ) ) {
			return array();
		}

		$checkers          = self::get_feed_checkers();
		$active_feed_slugs = self::get_active_feed_slugs();

		$feeds = GFAPI::get_feeds( null, (int) $form['id'] );
		if ( is_wp_error( $feeds ) || empty( $feeds ) || ! is_array( $feeds ) ) {
			return array();
		}

		$missing = array();
		foreach ( $feeds as $feed ) {
			if ( ! is_array( $feed ) ) {
				continue;
			}
			$slug = isset( $feed['addon_slug'] ) ? $feed['addon_slug'] : '';
			if ( '' === $slug ) {
				continue;
			}

			if ( isset( $checkers[ $slug ] ) ) {
				$info = $checkers[ $slug ];
				if ( self::is_check_passed( $info ) ) {
					continue;
				}

				$missing[ $slug ] = $info['name'];
				continue;
			}

			if ( isset( $active_feed_slugs[ $slug ] ) ) {
				continue;
			}

			$missing[ $slug ] = sprintf(
				/* translators: %s: Gravity Forms add-on feed slug. */
				esc_html__( 'Extension for feed "%s"', 'gf-miss-ext' ),
				$slug
			);
		}

		return $missing;
	}

	private static function get_active_feed_slugs() {
		$slugs = array();

		if ( ! class_exists( 'GFAddOn' ) || ! method_exists( 'GFAddOn', 'get_registered_addons' ) ) {
			return apply_filters( 'gf_missing_extension_active_feed_slugs', $slugs );
		}

		$addons = GFAddOn::get_registered_addons();
		if ( ! is_array( $addons ) ) {
			return apply_filters( 'gf_missing_extension_active_feed_slugs', $slugs );
		}

		foreach ( $addons as $addon ) {
			$instance = self::resolve_addon_instance( $addon );
			if ( ! is_object( $instance ) ) {
				continue;
			}

			$slug = self::get_addon_slug( $instance );
			if ( '' === $slug ) {
				continue;
			}

			$slugs[ $slug ] = true;
		}

		return apply_filters( 'gf_missing_extension_active_feed_slugs', $slugs );
	}

	private static function resolve_addon_instance( $addon ) {
		if ( is_object( $addon ) ) {
			return $addon;
		}

		if ( ! is_string( $addon ) || ! class_exists( $addon ) ) {
			return null;
		}

		if ( method_exists( $addon, 'get_instance' ) ) {
			return call_user_func( array( $addon, 'get_instance' ) );
		}

		return null;
	}

	private static function get_addon_slug( $addon ) {
		if ( method_exists( $addon, 'get_slug' ) ) {
			$slug = (string) $addon->get_slug();
			if ( '' !== $slug ) {
				return $slug;
			}
		}

		if ( isset( $addon->_slug ) && '' !== (string) $addon->_slug ) {
			return (string) $addon->_slug;
		}

		if ( isset( $addon->slug ) && '' !== (string) $addon->slug ) {
			return (string) $addon->slug;
		}

		return '';
	}

	private static function is_check_passed( $info ) {
		$check = isset( $info['check'] ) ? $info['check'] : 'class_exists';

		switch ( $check ) {
			case 'function_exists':
				$func = isset( $info['function'] ) ? $info['function'] : '';
				if ( '' === $func ) {
					return true;
				}
				return function_exists( $func );

			case 'is_plugin_active':
				$plugin = isset( $info['plugin'] ) ? $info['plugin'] : '';
				if ( '' === $plugin ) {
					return true;
				}
				if ( ! function_exists( 'is_plugin_active' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}
				return is_plugin_active( $plugin );

			case 'class_exists':
			default:
				$class = isset( $info['class'] ) ? $info['class'] : '';
				if ( '' === $class ) {
					return true;
				}
				if ( is_array( $class ) ) {
					foreach ( $class as $c ) {
						if ( class_exists( $c ) ) {
							return true;
						}
					}
					return false;
				}
				return class_exists( $class );
		}
	}

	private static function get_form_from_request() {
		if ( ! class_exists( 'GFAPI' ) ) {
			return null;
		}

		$id = self::get_requested_form_id();
		if ( $id <= 0 ) {
			return null;
		}
		$form = GFAPI::get_form( $id );
		return is_array( $form ) ? $form : null;
	}

	private static function get_requested_page() {
		if ( ! isset( $_GET['page'] ) ) {
			return '';
		}

		return sanitize_key( wp_unslash( $_GET['page'] ) );
	}

	private static function get_requested_form_id() {
		if ( ! isset( $_GET['id'] ) ) {
			return 0;
		}

		return (int) wp_unslash( $_GET['id'] );
	}

	public static function enqueue_admin_assets( $hook ) {
		if ( 'toplevel_page_gf_edit_forms' !== $hook ) {
			return;
		}

		$form_id = self::get_requested_form_id();
		if ( $form_id <= 0 ) {
			return;
		}

		wp_enqueue_style(
			'gf-missing-extensions-notice-admin',
			plugins_url( 'assets/css/admin.css', GF_MISS_EXT_FILE ),
			array(),
			GF_MISS_EXT_VERSION
		);

		wp_enqueue_script(
			'gf-missing-extensions-notice-admin',
			plugins_url( 'assets/js/admin.js', GF_MISS_EXT_FILE ),
			array(),
			GF_MISS_EXT_VERSION,
			true
		);

		$marker_states = array();
		foreach ( self::get_markers() as $marker => $info ) {
			$marker_states[] = array(
				'marker' => $marker,
				'name'   => $info['name'],
				'type'   => isset( $info['type'] ) ? $info['type'] : '',
				'active' => self::is_check_passed( $info ),
			);
		}

		$config = array(
			'markers'              => $marker_states,
			'registeredFieldTypes' => array_values( array_keys( self::get_registered_field_types() ) ),
			// Feed-based missing extensions only. Marker-based detection (field types,
			// key prefixes) is handled client-side by admin.js using window.form.
			'serverMissing'        => array_values( array_unique( self::get_missing_feed_extensions( array( 'id' => $form_id ) ) ) ),
			'noticeLabelSingular'  => __( 'Missing extension:', 'gf-miss-ext' ),
			'noticeLabelPlural'    => __( 'Missing extensions:', 'gf-miss-ext' ),
			'fieldTypeLabel'       => __( 'Extension for field type "%s"', 'gf-miss-ext' ),
			'usedByField'          => __( '(used by field %s)', 'gf-miss-ext' ),
			'usedByFields'         => __( '(used by fields: %s)', 'gf-miss-ext' ),
		);

		wp_localize_script(
			'gf-missing-extensions-notice-admin',
			'GFMissingExtensionsNoticeConfig',
			$config
		);
	}


}

add_action( 'plugins_loaded', array( 'GF_Missing_Extension_Notice', 'load_textdomain' ) );
add_action( 'admin_enqueue_scripts', array( 'GF_Missing_Extension_Notice', 'enqueue_admin_assets' ) );

register_deactivation_hook( GF_MISS_EXT_FILE, function () {
	delete_transient( 'gf_missing_extensions_notice_release' );
} );

// Register with Guilamu Bug Reporter.
add_action( 'plugins_loaded', function () {
	if ( class_exists( 'Guilamu_Bug_Reporter' ) ) {
		Guilamu_Bug_Reporter::register( array(
			'slug'        => 'gf-missing-extensions-notice',
			'name'        => 'Gravity Forms - Missing Extension Notice',
			'version'     => GF_MISS_EXT_VERSION,
			'github_repo' => 'guilamu/gf-missing-extensions-notice',
		) );
	}
}, 20 );

// Add "Report a Bug" link to the plugins list.
add_filter( 'plugin_row_meta', function ( $links, $file ) {
	if ( plugin_basename( GF_MISS_EXT_FILE ) !== $file ) {
		return $links;
	}

	if ( class_exists( 'Guilamu_Bug_Reporter' ) ) {
		$links[] = sprintf(
			'<a href="#" class="guilamu-bug-report-btn" data-plugin-slug="gf-missing-extensions-notice" data-plugin-name="%s">%s</a>',
			esc_attr__( 'Gravity Forms - Missing Extension Notice', 'gf-miss-ext' ),
			esc_html__( '🐛 Report a Bug', 'gf-miss-ext' )
		);
	} else {
		$links[] = sprintf(
			'<a href="%s" target="_blank">%s</a>',
			'https://github.com/guilamu/guilamu-bug-reporter/releases',
			esc_html__( '🐛 Report a Bug (install Bug Reporter)', 'gf-miss-ext' )
		);
	}

	return $links;
}, 10, 2 );
