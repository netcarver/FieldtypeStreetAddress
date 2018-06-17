<?php namespace ProcessWire;

/**
 *
 * DONE
 * ----
 * Apply uppercase rules.
 * Detect if country field is required (address country != origin country).
 * Allow state/locality/postcode alternative keys.
 * Add postcode checking.
 * Add custom field mapping.
 * Add required fields validation.
 * Add snapshots.
 * Add change detection of fields.
 *
 *
 * WIP
 * ---
 *
 *
 * TODO
 * ----
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
    public function formatHtml()
    {
        return self::formatLines(get_object_vars($this), true);
    }



    /**
     * Format this address - optionally as HTML.
     */
    public function format(bool $html = false)
    {
        return self::formatLines(get_object_vars($this), $html);
    }



    /**
     * Format address to a single line of text.
     */
    public function formatSingle($glue = ', ')
    {
        $multiline = $this->format();
        $single = str_replace("\n", $glue, $multiline);
        return $single;
    }



    /**
     * Magic method to convert to a formatted string - non HTML.
     */
    public function __toString()
    {
        return $this->formatSingle();
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
        $snapshot       = get_object_vars($this);
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


    // =================================================================================================================

    /**
     *
     */
    protected static $country_formats;


    /**
     * Set to false to exclude addition of country field.
     *
     * If you are formatting addresses that might involve international post, please set this to the ISO code of the
     * country of origin. This will force the addition of the destination country to the formatted address - if it is
     * not already present in the destination country's 'fmt' field.
     *
     * @see Address::setCountryOfOriginISO()
     */
    protected static $origin_country_iso = false;


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
        'locality'           => '', // city/postal town
        'dependent_locality' => '', // sub-district
        'admin_area'         => '', // state/county
        'postal_code'        => '',
        'sorting_code'       => '',
        'country'            => '',
        'country_iso'        => '',
    ];



    public static function getAddressFieldNames()
    {
        return array_keys(self::$address_lines);
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
        $country = $data['country_iso'];
        $lang    = @$info['lang'];
        if ('en' === $lang) {
            if ($country === 'us' || $country === 'ph') {
                self::remapAddressField('zip', 'postal_code', $data);
                self::remapAddressField('zipcode', 'postal_code', $data);
                self::remapAddressField('zip_code', 'postal_code', $data);
            } else if ($country === 'ie') {
                self::remapAddressField('eircode', 'postal_code', $data);
            } else {
                self::remapAddressField('postcode', 'postal_code', $data);
            }
        }
        switch ($country) {
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

        // TODO Move this from static to dynamic property?

        // Apply any custom remappings...
        if (!empty(self::$remappings)) {
            foreach (self::$remappings as $from => $to) {
                self::remapAddressField($from, $to, $data);
            }
        }

        return $data;
    }



    /**
     *
     */
    /* public static function formatHtml(array $data) */
    /* { */
    /*     return self::format($data, true); */
    /* } */



    /**
     * Format the given address data into an address string.
     *
     * @param array $data A set of named address strings.
     * @param bool  $html true => wrap address elements in HTML markup that includes microformat classes...
     * @return string The formatted address.
     */
    protected static function formatLines(array $data, $html = false)
    {
        // Make sure address data uses lowercase keys...
        $data = array_change_key_case($data, CASE_LOWER);

        // Merge in defaults
        $data = array_merge(self::$address_lines, $data);

        // Load country option
        $address_country   = $data['country_iso'];
        $format_info       = self::getFormat($data['country_iso']);
        $data              = self::remapAddressFields($format_info, $data);
        $upper             = @$format_info['upper'];
        $formatted_address = $format_info['fmt'];

        if (self::$origin_country_iso && ($address_country !== self::$origin_country_iso)) {
            // This is an international address - add the country to the format if it is not already present...
            $pos = strpos($formatted_address, '%R');
            if (false === $pos) {
                $formatted_address .= '%n%R';
            }

            // handle case where country field is missing, but country_iso is defined.
            if (empty($data['country'])) {
                $country = @$format_info['name'];
                $data['country'] = $country;
            }
        }


        // Replace formatted address elements with items from the data as needed.
        foreach (self::$address_mapping as $id => $key) {
            $value = $data[$key];

            if ('Z' === $id) {
                $value = self::sanitizePostalCode($value, $data['country_iso']);
            }

            // Make sure the fields marked as "upper" in the google feed are converted to uppercase.
            if ($upper && false !== stripos($upper, $id)) {
                $value = mb_convert_case($value, MB_CASE_UPPER, 'utf-8');
            }

            // Skip over the address_2 entry (for now)
            if ($key == 'street_address_2') continue;

            // Deal with it here instead...
            if ($key == 'street_address') {
                $value = $value . ($data['street_address_2'] ? ($html ? '<br>' : '%n') . $data['street_address_2'] : '');
            }

            // HMTL gets the postal address microformat wrapping spans...
            if ($html === true && $value) {
                $value = "<span" . self::getItemProp($key) . ">{$value}</span>";
            }

            $formatted_address = str_replace("%{$id}", $value, $formatted_address);
        }

        // Insert newlines where needed...
        $formatted_address = trim(str_replace('%n', "\n", $formatted_address));

        // Clean up runs of multiple newlines...
        $formatted_address = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $formatted_address);

        return $formatted_address;
    }



    /**
     * @param string $country
     * @return mixed
     */
    public static function getFormat($country)
    {
        if (self::$country_formats === null) {
            self::setFormats(include(__DIR__ . '/formats.php'));
        }

        // Ensure it's upper cased
        $country = strtoupper($country);

        // Return international format for missing
        if (array_key_exists($country, self::$country_formats) === false) {
            return ['fmt' => '%N%n%O%n%A%n%C, %S %Z %R'];
        }

        return self::$country_formats[$country];
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
     * Sets the default country code.
     */
    public static function setDefaultCountryISO($iso = 'gb')
    {
        $address_lines['country_iso'] = strtolower((string) $iso);
    }


    /**
     * Gets the default country code.
     */
    public static function getDefaultCountryISO()
    {
        return self::$address_lines['country_iso'];
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


    public static function setCountryOfOriginISO($iso)
    {
        self::$origin_country_iso = $iso;
    }


    public static function getCountryOfOriginISO()
    {
        return self::$origin_country_iso;
    }
}
