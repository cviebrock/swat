<?php

/**
 * Container for package wide static methods.
 *
 * @copyright 2005-2017 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class Swat
{
    /**
     * The gettext domain for Swat.
     *
     * This is used to support multiple locales.
     */
    public const GETTEXT_DOMAIN = 'swat';

    /**
     * Whether or not this package is initialized.
     */
    private static bool $is_initialized = false;

    /**
     * Don't allow instantiation of the Swat object.
     *
     * This class contains only static methods and should not be instantiated.
     */
    private function __construct() {}

    /**
     * Translates a phrase.
     *
     * This is an alias for {@link self::gettext()}.
     *
     * @param string $message the phrase to be translated
     *
     * @return string the translated phrase
     */
    public static function _(string $message): string
    {
        return self::gettext($message);
    }

    /**
     * Translates a phrase.
     *
     * This method relies on the php gettext extension and uses dgettext()
     * internally.
     *
     * @param string $message the phrase to be translated
     *
     * @return string the translated phrase
     */
    public static function gettext(string $message): string
    {
        return dgettext(self::GETTEXT_DOMAIN, $message);
    }

    /**
     * Translates a plural phrase.
     *
     * This method should be used when a phrase depends on a number. For
     * example, use ngettext when translating a dynamic phrase like:
     *
     * - "There is 1 new item" for 1 item and
     * - "There are 2 new items" for 2 or more items.
     *
     * This method relies on the php gettext extension and uses dngettext()
     * internally.
     *
     * @param string $singular_message the message to use when the number the
     *                                 phrase depends on is one
     * @param string $plural_message   the message to use when the number the
     *                                 phrase depends on is more than one
     * @param int    $number           the number the phrase depends on
     *
     * @return string the translated phrase
     */
    public static function ngettext(string $singular_message, string $plural_message, int $number): string
    {
        return dngettext(
            self::GETTEXT_DOMAIN,
            $singular_message,
            $plural_message,
            $number,
        );
    }

    public static function setupGettext(): void
    {
        bindtextdomain(self::GETTEXT_DOMAIN, __DIR__ . '/../locale');
        bind_textdomain_codeset(self::GETTEXT_DOMAIN, 'UTF-8');
    }

    /**
     * Displays the methods of an object.
     *
     * This is useful for debugging.
     *
     * @param object $object the object whose methods are to be displayed
     */
    public static function displayMethods(object $object): void
    {
        echo sprintf(self::_('Methods for class %s:'), get_class($object));
        echo '<ul>';

        foreach (get_class_methods(get_class($object)) as $method_name) {
            echo '<li>', $method_name, '</li>';
        }

        echo '</ul>';
    }

    /**
     * Displays the properties of an object.
     *
     * This is useful for debugging.
     *
     * @param object $object the object whose properties are to be displayed
     */
    public static function displayProperties(object $object): void
    {
        $class = $object::class;

        echo sprintf(self::_('Properties for class %s:'), $class);
        echo '<ul>';

        foreach (get_class_vars($class) as $property_name => $value) {
            $instance_value = $object->{$property_name};
            echo '<li>', $property_name, ' = ', $instance_value, '</li>';
        }

        echo '</ul>';
    }

    /**
     * Displays an object's properties and values recursively.
     *
     * Note: If the object being printed is a UI object then its parent property
     * is temporarily set to null to prevent recursing up the widget tree.
     *
     * @param mixed $object the object to display
     */
    public static function printObject(mixed $object): void
    {
        echo '<pre>' . print_r($object, true) . '</pre>';
    }

    /**
     * Displays inline JavaScript properly encapsulating the script in a CDATA
     * section.
     *
     * @param string $javascript the inline JavaScript to display
     */
    public static function displayInlineJavaScript(string $javascript): void
    {
        if ($javascript != '') {
            echo '<script type="text/javascript">',
            "\n//<![CDATA[\n",
            rtrim($javascript),
            "\n//]]>\n</script>";
        }
    }

    public static function init(): void
    {
        if (self::$is_initialized) {
            return;
        }

        self::setupGettext();

        self::$is_initialized = true;
    }
}

// Define a dummy dngettext() for when gettext is not available.
if (!function_exists('dngettext')) {
    /**
     * Dummy translation function performs a passthrough on string to be
     * translated.
     *
     * This function is for compatibility with PHP installations not using
     * gettext.
     *
     * @param string $domain           the translation domain; ignored
     * @param string $singular_message the singular form
     * @param string $plural_message   the plural form
     * @param int    $n                the number
     *
     * @return string <i>$singlar_message</i> if <i>$n</i> is one, otherwise
     *                <i>$plural_message</i>
     */
    function dngettext(string $domain, string $singular_message, string $plural_message, int $n): string
    {
        return $n === 1 ? $singular_message : $plural_message;
    }
}

// Define a dummy dgettext() for when gettext is not available.
if (!function_exists('dgettext')) {
    /**
     * Dummy translation function performs a passthrough on string to be
     * translated.
     *
     * This function is for compatibility with PHP installations not using
     * gettext.
     *
     * @param string $domain  the translation domain; ignored
     * @param string $message the string to be translated
     *
     * @return string <i>$messageid</i>
     */
    function dgettext(string $domain, string $message): string
    {
        return $message;
    }
}
