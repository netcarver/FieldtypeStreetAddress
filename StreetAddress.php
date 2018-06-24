<?php namespace ProcessWire;

/**
 *
 *
 */

class StreetAddress
{
    /**
     * Store initial wakeup values to allow detection of changed fields.
     *
     * @var $snapshot array
     */
    protected $snapshot = [];


    /**
     * Consrtuctor can optionally take an array of address lines.
     */
    public function __construct(array $lines = [])
    {
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


    /**
     * Format this address as HTML.
     */
    public function formatMultiHtml()
    {
        return $this->format(true);
    }

    public function formatSingleHtml($line_glue = ', ')
    {
        return $this->format(true, $line_glue);
    }

    public function formatMultiPlain()
    {
        return $this->format(false);
    }

    public function formatSinglePlain($line_glue = ', ')
    {
        return $this->format(false, $line_glue);
    }


    /**
     * Format this address - optionally as HTML.
     */
    public function format(bool $html = false, $line_glue = false)
    {
        $vars = get_object_vars($this);
        unset($vars['snapshot']);
        unset($vars['country']);
        return self::formatLines($vars, $html, $line_glue);
    }



    /**
     * Format address to a single line of text.
     */
    public function formatSingle($html = false, $line_glue = ', ')
    {
        return $this->format($html, $line_glue);
    }



    /**
     * Magic method to convert to a formatted string - non HTML.
     */
    public function __toString()
    {
        return $this->format();
    }



    /**
     * Check the address is valid.
     */
    public function validate()
    {
        return self::validateLines(get_object_vars($this));
    }



    /**
     * Take a snapshot of the fields to allow change detection later on.
     */
    public function snapshot()
    {
        $snapshot = get_object_vars($this);
        unset($snapshot['snapshot']);
        unset($snapshot['country']);
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
     * Passes each address line to a definable callback function to prepare it for output.
     */
    public function prepareLines(callable $callback, array $data = [])
    {
        $vars = get_object_vars($this);
        unset($vars['snapshot']);
        unset($vars['country']);

        if (count($vars)) {
            foreach ($vars as $key => $value) {
                $newvalue = $callback($value, $key, $data);
                if ($newvalue != $value) {
                    $this->$key = $newvalue;
                }
            }
        }
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
     * Used when formatting an address...
     */
    protected static $address_mapping = [
        'S' => 'admin_area',
        'C' => 'locality',          // city
        'N' => 'recipient',         // name
        'O' => 'organization',      // organization
        'D' => 'dependent_locality',
        'Z' => 'postal_code',
        'X' => 'sorting_code',
        'A' => 'street_address',
        'R' => 'country',
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
     * @var array
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
            if ($country_iso === 'us' || $country_iso === 'ph') {
                self::remapAddressField('zip', 'postal_code', $data);
                self::remapAddressField('zipcode', 'postal_code', $data);
                self::remapAddressField('zip_code', 'postal_code', $data);
            } else if ($country_iso === 'ie') {
                self::remapAddressField('eircode', 'postal_code', $data);
            } else {
                self::remapAddressField('postcode', 'postal_code', $data);
            }
        }
        switch ($country_iso) {
        case 'nl':
            self::remapAddressField('postcode', 'postal_code', $data);
            break;

        case 'br':
            self::remapAddressField('cep', 'postal_code', $data);
            break;

        case 'in':
            self::remapAddressField('pincode', 'postal_code', $data);
            self::remapAddressField('pin_code', 'postal_code', $data);
            break;

        case 'gb':
            self::remapAddressField('postcode', 'postal_code', $data);
            break;

        case 'de':
        case 'at':
        case 'li':
            self::remapAddressField('plz', 'postal_code', $data);
            break;

        case 'it':
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
        $address_country_iso = $data['country_iso'];
        $origin_iso          = $data['origin_iso'];
        $format_info         = array_merge(self::getFormat($data['country_iso']), $format_info);
        $data                = self::remapAddressFields($format_info, $data);
        $upper               = isset($format_info['upper']) ? $format_info['upper'] : '';
        $formatted_address   = $format_info['fmt'];

        // Setup the inter-line glue
        if (!is_bool($line_glue)) {
            $glue = $line_glue;
        } else {
            $glue = $html ? '<br>' : "\n";
        }

        if (isset($data['origin_iso']) && ($address_country_iso !== $data['origin_iso'])) {
            // This is an international address - add the country to the format if it is not already present...
            $pos = strpos($formatted_address, '%R');
            if (false === $pos) {
                $formatted_address .= '%n%R';
            }

            // Use the country_iso field to define the destination country field.
            $country = @$format_info['name'];
            $data['country'] = $country;
        }


        // Replace formatted address elements with items from the data as needed.
        foreach (self::$address_mapping as $id => $key) {
            $value    = trim(strip_tags($data[$key]));
            $value    = str_replace(['&apos;', '&#039;'], "'", $value);
            $is_upper = (false !== stripos($upper, $id));

            if ('Z' === $id) {
                $value = self::sanitizePostalCode($value, $data['country_iso']);
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
     * @param string $country
     * @return mixed
     */
    public static function getFormat($country_iso)
    {
        $international_layout = '%N%n%O%n%A%n%C, %S %Z %R';

        if (self::$country_formats === null) {
            self::setFormats(include(__DIR__ . '/formats.php'));
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
        $required    = $format_info['require'];

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
