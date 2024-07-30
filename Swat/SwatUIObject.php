<?php

/**
 * A base class for Swat user-interface elements.
 *
 * TODO: describe our conventions on how CSS classes and XHTML ids are
 * displayed.
 *
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatUIObject extends SwatObject
{
    /**
     * The object which contains this object.
     *
     * @var ?SwatUIObject
     */
    public ?SwatUIObject $parent;

    /**
     * Visible.
     *
     * Whether this UI object is displayed. All UI objects should respect this.
     *
     * @see SwatUIObject::isVisible()
     */
    public bool $visible = true;

    /**
     * A user-specified array of CSS classes that are applied to this
     * user-interface object.
     *
     * See the class-level documentation for SwatUIObject for details on how
     * CSS classes and XHTML ids are displayed on user-interface objects.
     *
     * @var array<string>
     */
    public array $classes = [];

    /**
     * A user-specified key-value array of data attributes that are applied
     * to this user-interface object.
     *
     * @var array<string>
     */
    public array $data_attributes = [];

    /**
     * A set of HTML head entries needed by this user-interface element.
     *
     * Entries are stored in a data object called {@link SwatHtmlHeadEntry}.
     * This property contains a set of such objects.
     */
    protected SwatHtmlHeadEntrySet $html_head_entry_set;

    public function __construct()
    {
        $this->html_head_entry_set = new SwatHtmlHeadEntrySet();
    }

    /**
     * Adds a stylesheet to the list of stylesheets needed by this
     * user-interface element.
     *
     * @throws SwatException
     */
    public function addStyleSheet(string $stylesheet): void
    {
        if ($this->html_head_entry_set === null) {
            throw new SwatException(
                sprintf(
                    "Child class '%s' did not " .
                        'instantiate a HTML head entry set. This should be done in  ' .
                        'the constructor either by calling parent::__construct() or ' .
                        'by creating a new HTML head entry set.',
                    get_class($this),
                ),
            );
        }

        $this->html_head_entry_set->addEntry(
            new SwatStyleSheetHtmlHeadEntry($stylesheet),
        );
    }

    /**
     * Adds a JavaScript include to the list of JavaScript includes needed
     * by this user-interface element.
     *
     * @throws SwatException
     */
    public function addJavaScript(string $java_script): void
    {
        if ($this->html_head_entry_set === null) {
            throw new SwatException(
                sprintf(
                    "Child class '%s' did not " .
                        'instantiate a HTML head entry set. This should be done in  ' .
                        'the constructor either by calling parent::__construct() or ' .
                        'by creating a new HTML head entry set.',
                    get_class($this),
                ),
            );
        }

        $this->html_head_entry_set->addEntry(
            new SwatJavaScriptHtmlHeadEntry($java_script),
        );
    }

    /**
     *  Adds an external JavaScript URI to the list of JavaScript includes needed
     *  by this user-interface element.
     *
     * @throws SwatException
     */
    public function addExternalJavaScript(string $url): void
    {
        if ($this->html_head_entry_set === null) {
            throw new SwatException(
                sprintf(
                    "Child class '%s' did not " .
                        'instantiate a HTML head entry set. This should be done in  ' .
                        'the constructor either by calling parent::__construct() or ' .
                        'by creating a new HTML head entry set.',
                    get_class($this),
                ),
            );
        }

        $this->html_head_entry_set->addEntry(
            new SwatExternalJavaScriptHtmlHeadEntry($url),
        );
    }

    /**
     * Adds a comment to the list of HTML head entries needed by this
     * user-interface element.
     *
     * @throws SwatException
     */
    public function addComment(string $comment): void
    {
        if ($this->html_head_entry_set === null) {
            throw new SwatException(
                sprintf(
                    "Child class '%s' did not " .
                        'instantiate a HTML head entry set. This should be done in  ' .
                        'the constructor either by calling parent::__construct() or ' .
                        'by creating a new HTML head entry set.',
                    get_class($this),
                ),
            );
        }

        $this->html_head_entry_set->addEntry(
            new SwatCommentHtmlHeadEntry($comment),
        );
    }

    public function addInlineScript(string $script): void
    {
        $this->inline_scripts->add($script);
    }

    /**
     * Gets the first ancestor object of a specific class.
     *
     * Retrieves the first ancestor object in the parent path that is a
     * descendant of the specified class name.
     *
     * @param class-string $class_name class name to look for
     *
     * @return ?SwatUIObject the first ancestor object or null if no matching ancestor
     *                       is found
     *
     * @see SwatUIParent::getFirstDescendant()
     */
    public function getFirstAncestor(string $class_name): ?SwatUIObject
    {
        if (!class_exists($class_name)) {
            return null;
        }

        if ($this->parent === null) {
            return null;
        }

        if ($this->parent instanceof $class_name) {
            return $this->parent;
        }

        return $this->parent->getFirstAncestor($class_name);
    }

    /**
     * Gets the SwatHtmlHeadEntry objects needed by this UI object.
     *
     * If this UI object is not visible, an empty set is returned to reduce
     * the number of required HTTP requests.
     *
     * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
     *                              this UI object
     */
    public function getHtmlHeadEntrySet(): SwatHtmlHeadEntrySet
    {
        if ($this->isVisible()) {
            return new SwatHtmlHeadEntrySet($this->html_head_entry_set);
        }

        return new SwatHtmlHeadEntrySet();
    }

    /**
     * Gets the SwatHtmlHeadEntry objects that MAY be needed by this UI object.
     *
     * Even if this object is not displayed, all the resources that may be
     * required to display it are returned.
     */
    public function getAvailableHtmlHeadEntrySet(): SwatHtmlHeadEntrySet
    {
        return new SwatHtmlHeadEntrySet($this->html_head_entry_set);
    }

    /**
     * Gets whether this UI object is visible.
     *
     * Looks at the visible property of the ancestors of this UI object to
     * determine if this UI object is visible.
     *
     * @see SwatUIObject::$visible
     */
    public function isVisible(): bool
    {
        if ($this->parent instanceof SwatUIObject) {
            return $this->visible && $this->parent->isVisible();
        }

        return $this->visible;
    }

    /**
     * Gets this object as a string.
     *
     * @see SwatObject::__toString()
     *
     * @return string this object represented as a string
     */
    public function __toString(): string
    {
        // prevent recursion up the widget tree for UI objects
        $parent = $this->parent;
        $this->parent = get_class($parent);

        return parent::__toString();
        // set parent back again
        $this->parent = $parent;
    }

    /**
     * Performs a deep copy of the UI tree starting with this UI object.
     *
     * To perform a shallow copy, use PHP's clone keyword.
     *
     * @param ?string $id_suffix optional. A suffix to append to copied UI
     *                           objects in the UI tree. This can be used to
     *                           ensure object ids are unique for a copied UI
     *                           tree. If not specified, UI objects in the
     *                           returned copy will have identical ids to the
     *                           original tree. This can cause problems if both
     *                           the original and copy are displayed during the
     *                           same request.
     *
     * @return SwatUIObject a deep copy of the UI tree starting with this UI
     *                      object. The returned UI object does not have a
     *                      parent and can be inserted into another UI tree.
     */
    public function copy(?string $id_suffix = ''): SwatUIObject
    {
        $copy = clone $this;
        $copy->parent = null;

        return $copy;
    }

    /**
     * Gets the array of CSS classes that are applied to this user-interface
     * object.
     *
     * User-interface objects aggregate the list of user-specified classes and
     * may add static CSS classes of their own in this method.
     *
     * @return array<string> the array of CSS classes that are applied to this
     *                       user-interface object
     *
     * @see SwatUIObject::getCSSClassString()
     */
    protected function getCSSClassNames(): array
    {
        return $this->classes;
    }

    /**
     * @return array<string>
     */
    protected function getDataAttributes(): array
    {
        $data = [];

        foreach ($this->data_attributes as $key => $value) {
            $data["data-{$key}"] = $value;
        }

        return $data;
    }

    /**
     * Gets inline JavaScript used by this user-interface object.
     *
     * @return string inline JavaScript used by this user-interface object
     */
    protected function getInlineJavaScript(): string
    {
        return '';
    }

    /**
     * Gets the string representation of this user-interface object's list of
     * CSS classes.
     *
     * @return ?string the string representation of the CSS classes that are
     *                 applied to this user-interface object. If this object
     *                 has no CSS classes, null is returned rather than a blank
     *                 string.
     *
     * @see SwatUIObject::getCSSClassNames()
     */
    final protected function getCSSClassString(): ?string
    {
        $class_names = $this->getCSSClassNames();
        if (count($class_names) === 0) {
            return null;
        }

        return implode(' ', $class_names);
    }

    /**
     * Generates a unique id for this UI object.
     *
     * Gets a unique id that may be used for the id property of this UI object.
     * Each time this method id called, a new unique identifier is generated, so
     * you should only call this method once and set it to a property of this
     * object.
     *
     * @return string a unique identifier for this UI object
     */
    final protected function getUniqueId(): string
    {
        // Because this method is not static, this counter will start at zero
        // for each class.
        static $counter = 0;

        $counter++;

        return $this::class . $counter;
    }
}
