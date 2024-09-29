<?php namespace ProcessWire;

/**
 * 2018-2019 Netcarver.
 *
 */

class StreetAddress
{
    /**
     * Store initial wakeup values to allow detection of changed fields.
     */
    protected $snapshot = [];

    /**
     * Address fields
     */
    public $recipient = '';
    public $organization = '';
    public $street_address = '';
    public $street_address_2 = '';
    public $street_address_3 = '';
    public $locality = '';
    public $dependent_locality = '';
    public $admin_area = '';
    public $postal_code = '';
    public $sorting_code = '';
    public $country = '';
    public $country_iso = '';
    public $origin_iso = '';

    /**
     * Consrtuctor can optionally take an array of address lines.
     */
    public function __construct(string $value = '{}')
    {
        $lines = json_decode($value, TRUE);

        // Make sure address data uses lowercase keys...
        $lines = array_change_key_case($lines, CASE_LOWER);

        // Merge in defaults
        $lines = array_merge(self::$address_lines, $lines);
        if (!empty($lines)) {
            foreach ($lines as $key => $value) {
                $this->$key = $value;
            }
        }
    }


    public function getValue()
    {
        $vars = get_object_vars($this);
        unset($vars['snapshot']);
        unset($vars['country']);
        /* unset($vars['append_destination_iso']); */
        /* unset($vars['destination_country_fmt']); */
        return $vars;
    }

    /**
     * Convenience Format Methods...
     */
    public function formatMultiHtml($format_overrides = [])
    {
        return $this->format(true, false, $format_overrides);
    }

    public function formatSingleHtml($line_glue = ', ', $format_overrides = [])
    {
        return $this->format(true, $line_glue, $format_overrides);
    }

    public function formatMultiPlain($format_overrides = [])
    {
        return $this->format(false, false, $format_overrides);
    }

    public function formatSinglePlain($line_glue = ', ', $format_overrides = [])
    {
        return $this->format(false, $line_glue, $format_overrides);
    }

    public function formatSingle($html = false, $line_glue = ', ', $format_overrides = [])
    {
        return $this->format($html, $line_glue, $format_overrides);
    }


    /**
     * Format address
     *
     * @param bool $html Controls formatting for HTML. If true, HTML wrappers will be used and output will be escaped.
     * @param string|bool $line_glue False => Multiline output. A string acts as glue for joining the address lines.
     * @param array $format_overrides Allows optional overrides of the formatting metadata. Most useful field is probably
     * the 'upper' string. To prevent output from uppercasing fields use ['upper' => '']
     *
     * @see Contents of formats.php
     * @see StreetAddress::formatLines()
     */
    public function format($html = false, $line_glue = false, $format_overrides = [])
    {
        $vars = $this->getValue();
        /* $vars = get_object_vars($this); */
        /* unset($vars['snapshot']); */
        /* unset($vars['country']); */
        return self::formatLines($vars, $html, $line_glue, $format_overrides);
    }




    /**
     * Magic method to convert to a formatted string - non HTML.
     */
    public function __toString()
    {
        if (!empty($this->form_builder)) {
            $value = $this->getValue();
            unset($value['form_builder']);
            $value = array_filter($value, function($v) { return $v !== ''; }); // Remove empty fields as not needed.
            $result = json_encode($value);
            if (false === $result)
                return "";
            return $result;
        }
        return $this->formatSingle();
    }



    /**
     * Check the address is valid.
     */
    public function validate()
    {
        $vars = $this->getValue();
        return self::validateLines($vars);
    }



    /**
     * Take a snapshot of the fields to allow change detection later on.
     */
    public function snapshot()
    {
        $snapshot = $this->getValue();
        unset($snapshot['origin_iso']);
        $this->snapshot = $snapshot;
        return $this;
    }


    /**
     * Check if a field has changed since the last snapshot.
     */
    public function isChanged($field)
    {
        return (@$this->snapshot[$field] != $this->$field);
    }


    /**
     * Check if this instance is empty or has at least a partial value.
     *
     * The country, origin_iso and country_iso are ignored in this check. In reality, we want values like
     * street_address, locality and postal_code to be populated.
     */
    public function isEmpty()
    {
        $vars = $this->getValue();
        /* $vars = get_object_vars($this); */
        /* unset($vars['snapshot']); */
        /* unset($vars['country']); */
        unset($vars['country_iso']);
        unset($vars['origin_iso']);
        /* unset($vars['append_destination_iso']); */
        /* unset($vars['destination_country_fmt']); */

        $num_set_fields = 0;

        foreach ($vars as $value) {
            if (!empty($value)) $num_set_fields++;
        }
        $is_empty = 0 === $num_set_fields;
        return $is_empty;
    }

    /**
     * Passes each address line to a definable callback function to prepare it for output.
     */
    public function prepareLines($callback, array $data = [])
    {
        if (!is_callable($callback)) return $this;
        $vars = $this->getValue();

        if (count($vars)) {
            foreach ($vars as $key => $value) {
                //$newvalue = $callback($value, $key, $data);
                $newvalue = call_user_func($callback, $value, $key, $data);
                if ($newvalue != $value) {
                    $this->$key = $newvalue;
                }
            }
        }

        return $this;
    }



    // =================================================================================================================

    /**
     *
     */
    protected static $country_formats;


    /**
     * If set, entries in the remappings array allow you to specify custom names for supplied address fields
     * beyond those used by google's i18n feed.
     *
     * For example, if your system has a field called 'rue' that you wish to use for the street address field, you would
     * add the following line to your remappings...
     *
     * 'rue' => 'street_address',
     *
     * In the US, if you wish to use 'city' instead of the standard 'locality' field add...
     *
     * 'city' => 'locality',
     *
     * Note, these mappings are done in addition to some standard remappings for any locale-dependent hints and known
     * postcodes.
     *
     * These remappings are applied prior to formatting a supplied address.
     *
     * @see function remapAddressFields();
     * @see function setFieldMappings();
     * @see function getFieldMappings();
     */
    protected static $remappings = [];


    public static function setFieldMappings(array $mappings)
    {
        self::$remappings = $mappings;
    }


    public static function getFieldMappings()
    {
        return self::$remappings;
    }


    /**
     * Google's address field shortcut letters. Used when formatting an address.
     */
    protected static $address_mapping = [
        'A' => 'street_address',
        'C' => 'locality',
        'D' => 'dependent_locality',
        'N' => 'recipient',
        'O' => 'organization', // Yes, dealing with American spelling from Google.
        'R' => 'country',
        'S' => 'admin_area',
        'X' => 'sorting_code',
        'Z' => 'postal_code',
    ];


    public static function getAddressMappings()
    {
        return self::$address_mapping;
    }


    /**
     * Itemprops for generating HTML with PostalAddress schema markup.
     */
    protected static $itemprops = [
        'admin_area'         => 'addressRegion',
        'locality'           => 'addressLocality',
        'recipient'          => 'name',
        'organization'       => 'affiliation',
        'dependent_locality' => '',
        'postal_code'        => 'postalCode',
        'sorting_code'       => '',
        'street_address'     => 'streetAddress',
        'country'            => 'addressCountry',
    ];



    /**
     * Scratch for values.
     */
    protected static $address_lines = [
        'recipient'          => '',
        'organization'       => '',
        'street_address'     => '',
        'street_address_2'   => '',
        'street_address_3'   => '',
        'locality'           => '', // usually city/postal town
        'dependent_locality' => '', // usually sub-district
        'admin_area'         => '', // usually state/county
        'postal_code'        => '',
        'sorting_code'       => '',
        'country'            => '', // Internal use
        'country_iso'        => '',
        'origin_iso'         => '', // Internal use
    ];



    public static function getAddressFieldNames($all=false)
    {
        $lines = self::$address_lines;
        if (!$all) {
            // Remove 'internal' keys
            unset($lines['country']);
            unset($lines['origin_iso']);
        }
        $keys = array_keys($lines);
        return $keys;
    }


    /**
     * Maps a locale-specific alternative field to one of the standard address fields...
     */
    protected static function remapAddressField($from_key, $to_key, &$data)
    {
        if (empty($from_key)) return;

        $from_key = mb_convert_case($from_key, MB_CASE_LOWER, 'utf-8');
        $original = @$data[$from_key];
        if (empty($original)) return; // Don't risk overwriting the $data[$to_key] field if there is no source data.

        $data[$to_key] = $data[$from_key];
    }


    /**
     * Allow country/language specific remappings for the state and locality fields.
     * Adds rules to capture common remappings for the postal_code field as well.
     */
    protected static function remapAddressFields($info, $data)
    {
        $out            = $data;
        $state_remap    = @$info['state_name_type'];
        $locality_remap = @$info['locality_name_type'];

        // Handle hint data from Google i18n feed...
        self::remapAddressField($state_remap, 'admin_area', $data);
        self::remapAddressField($locality_remap, 'locality', $data);


        // Handle common alternative spelling of 'organisation'...
        self::remapAddressField('organisation', 'organization', $data);


        // Handle alternatives to postal_code such as "postcode", "zip" etc.....
        $country_iso = $data['country_iso'];
        $lang        = @$info['lang'];
        if ('en' === $lang) {
            if ($country_iso === 'US' || $country_iso === 'PH') {
                self::remapAddressField('zip', 'postal_code', $data);
                self::remapAddressField('zipcode', 'postal_code', $data);
                self::remapAddressField('zip_code', 'postal_code', $data);
            } else if ($country_iso === 'IE') {
                self::remapAddressField('eircode', 'postal_code', $data);
            } else {
                self::remapAddressField('postcode', 'postal_code', $data);
            }
        }
        switch ($country_iso) {
        case 'NL':
            self::remapAddressField('postcode', 'postal_code', $data);
            break;

        case 'BR':
            self::remapAddressField('cep', 'postal_code', $data);
            break;

        case 'IN':
            self::remapAddressField('pincode', 'postal_code', $data);
            self::remapAddressField('pin_code', 'postal_code', $data);
            break;

        case 'GB':
            self::remapAddressField('postcode', 'postal_code', $data);
            break;

        case 'DE':
        case 'AT':
        case 'LI':
            self::remapAddressField('plz', 'postal_code', $data);
            break;

        case 'IT':
            self::remapAddressField('cap', 'postal_code', $data);
            break;
        }

        // Apply any custom remappings...
        if (!empty(self::$remappings)) {
            foreach (self::$remappings as $from => $to) {
                self::remapAddressField($from, $to, $data);
            }
        }

        return $data;
    }


    protected static $country_cache = [];


    /**
     *
     */
    public static function country($data, $format_info, $dest_iso, $origin_iso)
    {
        $modname      = "LibLocalisation";
        $can_localise = wire('modules')->isInstalled($modname);
        $country      = '';
        $locale       = '';
        $load         = !isset(self::$country_cache[$origin_iso]) || !is_array(self::$country_cache[$origin_iso]);

        // TODO remove use of '++' for appending destination country to output.
        // Would be better to keep "origin_iso" and "append_destination" settings separately.
        // This will allow localisation no matter what the append settings are.
        if ('++' === $origin_iso || !isset($format_info['destination_country_fmt']) || 0 == $format_info['destination_country_fmt'] || !$can_localise) {
            if (isset($format_info['name'])) {
                $list = [$dest_iso => $format_info['name']];
            }
        } else {
            if ($load) {
                $localisation = wire('modules')->get($modname);
                $locale = LibLocalisation::countryToLocale(strtolower($origin_iso));
                $localisation->setLocale($locale);
                $list = $localisation->country('');

                if (!isset($list) || !is_array($list) || !isset($list[$dest_iso])) {
                    $country_file = __DIR__."/countries.php";
                    if(is_readable($country_file)) {
                        $list = include($country_file);
                    }
                }
                self::$country_cache[$origin_iso] = $list;
            } else {
                $list = self::$country_cache[$origin_iso];
            }
        }

        if (isset($list[$dest_iso])) {
            $country = $list[$dest_iso];
        }

        if (isset($format_info['destination_country_fmt']) && 2 == $format_info['destination_country_fmt']) {
            $en_name = isset($format_info['name']) ? $format_info['name'] : '';
            if (!empty($en_name) && mb_strtoupper($en_name) != mb_strtoupper($country)) {
                $country .= " / " . $en_name;
            }
        }

        if (isset($format_info['append_destination_iso']) && 1 == $format_info['append_destination_iso']) {
            $country .= " ($dest_iso)";
        }

        return $country;
    }



    /**
     * Format the given address data into an address string.
     *
     * @param array $data A set of named address strings.
     * @param bool  $html true => wrap address elements in HTML markup that includes microformat classes...
     * @return string The formatted address.
     */
    protected static function formatLines(array $data, $html = false, $line_glue = false, $format_info = [])
    {
        $data                = array_change_key_case($data, CASE_LOWER);
        $format_info         = array_change_key_case($format_info, CASE_LOWER);
        $data                = array_merge(self::$address_lines, $data);
        $address_country_iso = strtoupper($data['country_iso']);
        $origin_iso          = strtoupper($data['origin_iso']);
        $format_info         = array_merge(self::getFormat($address_country_iso), $format_info);
        $data                = self::remapAddressFields($format_info, $data);
        $upper               = isset($format_info['upper']) ? $format_info['upper'] : '';
        $formatted_address   = $format_info['fmt'];

        // Setup the inter-line glue
        if (!is_bool($line_glue)) {
            $glue = $line_glue;
        } else {
            $glue = $html ? '<br>' : "\n";
        }

        if ($address_country_iso !== $origin_iso) {
            // This is an international address - add the country to the format if it is not already present...
            $pos = strpos($formatted_address, '%R');
            if (false === $pos) {
                $formatted_address .= '%n%R';
            }
            $upper .= "R"; // Make sure country is uppercase
            $country = self::country($data, $format_info, $address_country_iso, $origin_iso);
            $data['country'] = $country;
        }


        // Replace formatted address elements with items from the data as needed.
        foreach (self::$address_mapping as $id => $key) {
            $value    = trim(strip_tags($data[$key]));
            $value    = str_replace(['&apos;', '&#039;'], "'", $value);
            $is_upper = (false !== stripos($upper, $id));

            if ('Z' === $id) {
                $value = self::sanitizePostalCode($value, $address_country_iso);
            }

            // Make sure the fields marked as "upper" in the google feed are converted to uppercase.
            if ($is_upper) {
                $value = mb_convert_case($value, MB_CASE_UPPER, 'utf-8');
            }

            if ($html && $value) {
                $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'utf-8', false);
            }

            if ('A' === $id) {
                $value2 = $data['street_address_2'];
                $value3 = $data['street_address_3'];
                if ($is_upper) {
                    $value2 = mb_convert_case($value2, MB_CASE_UPPER, 'utf-8');
                    $value3 = mb_convert_case($value3, MB_CASE_UPPER, 'utf-8');
                }
                if ($html) {
                    $value2 = htmlspecialchars($value2, ENT_QUOTES | ENT_HTML5, 'utf-8', false);
                    $value3 = htmlspecialchars($value3, ENT_QUOTES | ENT_HTML5, 'utf-8', false);
                }

                $value = $value . ($value2 ? $glue . $value2 : '');
                $value = $value . ($value3 ? $glue . $value3 : '');
            }

            // HMTL gets the postal address microformat wrapping spans...
            if ($html && $value) {
                $value = "<span" . self::getItemProp($key) . ">{$value}</span>";
            }

            $formatted_address = str_replace("%{$id}", $value, $formatted_address);
        }

        $formatted_address = trim(str_replace('%n', "\n", $formatted_address));
        $formatted_address = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $formatted_address); // \n2+ -> \n

        $formatted_address = str_replace("\n", $glue, $formatted_address);

        return $formatted_address;
    }



    /**
     */
    public static function getFormat($country_iso)
    {
        $international_layout = '%N%n%O%n%A%n%C, %S %Z %R';

        if (self::$country_formats === null) {
            $formats        = include_once(__DIR__.'/formats.php');
            $overrides      = false;
            $overrides_file = __DIR__.'/formats_overrides.php';

            if (file_exists($overrides_file)) {
                $overrides = include_once($overrides_file);
            }

            if (is_array($overrides)) {
                $formats = array_replace_recursive($formats, $overrides);
            }

            self::setFormats($formats);

        }

        $country_iso = strtoupper($country_iso);
        // Return international format for missing
        if (false === array_key_exists($country_iso, self::$country_formats)) {
            $format_info = ['fmt' => $international_layout];
        } else {
            $format_info = self::$country_formats[$country_iso];
            if (!isset($format_info['fmt'])) {
                $format_info['fmt'] = $international_layout;
            }
        }

        return $format_info;
    }



    public static function getBlankFormattedValue($iso)
    {
        $fmt = self::getFormat($iso);
        $fmt = $fmt['fmt'];
        foreach (self::$address_mapping as $id => $key) {
            $fmt = str_replace("%{$id}", '', $fmt);
        }
        $fmt = trim(str_replace('%n', "\n", $fmt));
        // Clean up runs of multiple newlines...
        $fmt = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $fmt);
        return $fmt;
    }


    public static function getPostalCodeRegex($iso)
    {
        $info    = self::getFormat($iso);
        $pcregex = @$info['zip'];

        return $pcregex;
    }

    public static function checkPostalCode($pc, $iso)
    {
        $regex = self::getPostalCodeRegex($iso);
        if (empty($regex)) return true;
        $result = preg_match("~$regex~", $pc, $m);
        $malformed = (0 === $result) || ($m[0] !== $pc);
        return !$malformed;
    }


    public static function sanitizePostalCode($pc, $iso)
    {
        $regex = self::getPostalCodeRegex($iso);
        if (empty($regex)) return $pc;
        $result = preg_match("~$regex~", $pc, $m);
        if (1 === $result) {
            return $m[0];
        }
        return '';
    }


    /**
     * Check the address meets google's requirements for an address of the given country.
     */
    protected static function validateLines(array $data)
    {
        $results = [
            'valid'             => true,
            'missing'           => [],
            'postal_code_valid' => true,
        ];

        // Check all required fields are present...
        $data = array_change_key_case($data, CASE_LOWER);

        // Merge in defaults
        $data = array_merge(self::$address_lines, $data);

        // Load country option
        $format_info = self::getFormat($data['country_iso']);
        $data        = self::remapAddressFields($format_info, $data);
        $required    = @$format_info['require'];
        if (empty($required)) {
            $required = 'AC'; // Inherit from ZZ default meta data.
        }

        if (!empty($data['postal_code'])) {
            $results['postal_code_valid'] = self::checkPostalCode($data['postal_code'], $data['country_iso']);
        }

        if (!empty($required)) {
            $required = str_split($required);
            foreach ($required as $key) {
                $addressline = self::$address_mapping[$key];
                $value = $data[$addressline];
                if (empty($value)) {
                    $results['valid']     = false;
                    $results['missing'][] = $addressline;
                    if ('postal_code' === $addressline) {
                        $results['postal_code_valid'] = false;
                    }
                }
            }
        }


        return $results;
    }



    /**
     * @param array|null $country_formats
     */
    public static function setFormats($country_formats)
    {
        self::$country_formats = $country_formats;
    }



    /**
     * @return array|null
     */
    public static function getFormats()
    {
        return self::$country_formats;
    }



    /**
     * @param string $key
     * @return string
     */
    protected static function getItemProp($key)
    {
        if ($prop = self::$itemprops[$key]) {
            return " itemprop=\"{$prop}\"";
        }

        return '';
    }
}
