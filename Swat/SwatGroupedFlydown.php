<?php

require_once 'Swat/SwatTreeFlydown.php';

/**
 * A tree flydown input control that displays flydown options in optgroups
 *
 * The tree for a grouped flydown may be at most 3 levels deep including the
 * root node.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatGroupedFlydown extends SwatTreeFlydown
{
	// {{{ public function setTree()

	/**
	 * Sets the tree to use for display
	 *
	 * The tree for a grouped flydown may be at most 3 levels deep including
	 * the root node.
	 *
	 * @param SwatDataTreeNode $tree the tree to use for display.
	 *
	 * @throws SwatException if the tree more than 3 levels deep.
	 */
	public function setTree(SwatTreeFlydownNode $tree)
	{
		$this->checkTree($tree);
		parent::setTree($tree);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this grouped flydown
	 *
	 * Displays this flydown as a XHTML select. Level 1 tree nodes are
	 * displayed as optgroups if their value is null, they have children and
	 * they are not dividers.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$selected = false;

		// tree is copied for display so we can add a blank node if show_blank
		// is true
		$display_tree = $this->getDisplayTree();
		$count = count($display_tree) - 1;

		// only show a select if there is more than one option
		if ($count > 1) {

			$select_tag = new SwatHtmlTag('select');
			$select_tag->name = $this->id;
			$select_tag->id = $this->id;
			$select_tag->class = $this->getCSSClassString();

			$select_tag->open();


			foreach ($display_tree->getChildren() as $child)
				$this->displayNode($child, 1);

			$select_tag->close();

		} elseif ($count == 1) {
			// get first and only element
			$option = reset($display_tree->getChildren())->getOption();
			$this->displaySingle($option);
		}
	}

	// }}}
	// {{{ protected function checkTree()

	/**
	 * Checks a tree to ensure it is valid for a grouped flydown
	 *
	 * @param SwatTreeFlydownNode the tree to check.
	 *
	 * @throws SwatException if the tree is not valid for a grouped flydown.
	 */
	protected function checkTree(SwatTreeFlydownNode $tree, $level = 0)
	{
		if ($level > 2)
			throw new SwatException('SwatGroupedFlydown tree must not be '.
				'more than 3 levels including the root node.');

		$children = &$tree->getChildren();
		foreach ($children as $child)
			$this->checkTree($child, $level + 1);
	}

	// }}}
	// {{{ protected function displayNode()

	/**
	 * Displays a grouped tree flydown node and its child nodes
	 *
	 * Level 1 tree nodes are displayed as optgroups if their value is null,
	 * they have children and they are not dividers.
	 *
	 * @param SwatTreeFlydownNode $node the node to display.
	 * @param integer $level the current level of the tree node.
	 * @param array $path an array of values representing the tree path to
	 *                     this node.
	 */
	protected function displayNode(SwatTreeFlydownNode $node, $level = 0,
		$path = array())
	{
		$children = $node->getChildren();
		$flydown_option = clone $node->getOption();
		$option_tag->value = serialize($flydown_option->value);
		$path[] = $flydown_option->value;

		if ($level == 1 && count($children) > 0 &&
			$flydown_option->value === null &&
			!($flydown_option instanceof SwatFlydownDivider)) {

			$optgroup_tag = new SwatHtmlTag('optgroup');
			$optgroup_tag->label = $flydown_option->title;
			$optgroup_tag->open();
			foreach($node->getChildren() as $child_node)
				$this->displayNode($child_node, $level + 1, $path);

			$optgroup_tag->close();
		} else {
			$flydown_option->value = $path;
			$option_tag = new SwatHtmlTag('option');

			if ($flydown_option instanceof SwatFlydownDivider) {
				$option_tag->disabled = 'disabled';
				$option_tag->class = 'swat-flydown-option-divider';
			} else {
				$option_tag->removeAttribute('disabled');
				$option_tag->removeAttribute('class');
			}

			if ($this->value === $flydown_option->value &&
				$selected === false &&
				!($flydown_option instanceof SwatFlydownDivider)) {

				$option_tag->selected = 'selected';
				$selected = true;
			} else {
				$option_tag->removeAttribute('selected');
			}

			$option_tag->setContent($flydown_option->title);
			$option_tag->display();

			foreach($children as $child_node)
				$this->displayNode($child_node, $level + 1, $path);
		}
	}

	// }}}
	// {{{ protected function buildDisplayTree()

	/**
	 * Builds this grouped flydown's display tree by copying nodes from this
	 * grouped flydown's tree
	 *
	 * @param SwatTreeFlydownNode $tree the source tree node to build from.
	 * @param SwatTreeFlydownNode $parent the destination parent node to add
	 *                                     display tree nodes to.
	 */
	protected function buildDisplayTree(SwatTreeFlydownNode $tree,
		SwatTreeFlydownNode $parent)
	{
		$flydown_option = $tree->getOption();
		$new_node = new SwatTreeFlydownNode($flydown_option->value,
			$flydown_option->title);

		$parent->addChild($new_node);
		foreach ($tree->getChildren() as $child)
			$this->buildDisplayTree($child, $new_node);
	}

	// }}}
	// {{{ protected function getDisplayTree()

	/**
	 * Gets the display tree of this grouped flydown
	 *
	 * The display tree is copied from this grouped flydown's tree. If
	 * {@link SwatGroupedFlydown::$show_blank} is true, a blank node is
	 * inserted at the beginning of the display tree.
	 *
	 * @see setTree()
	 */
	protected function getDisplayTree()
	{
		$display_tree = new SwatTreeFlydownNode(null, 'root');
		if ($this->show_blank)
			$display_tree->addChild(
				new SwatTreeFlydownNode(null, $this->blank_title));

		foreach ($this->tree->getChildren() as $child)
			$this->buildDisplayTree($child, $display_tree);

		return $display_tree;
	}

	// }}}
}

?>