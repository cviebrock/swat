<?php

require_once 'Swat/SwatWizardNavigation.php';

/**
 * A proto-type test wizard navigation class that shows navigation buttons to
 * switch steps.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatWizardNavigationSteps extends SwatWizardNavigation {

	private $step_buttons;
	
	public function getNextStep()
	{
		$return = null;

		for ($i = 0; $i < $this->parent->getStepCount(); $i++) {
			$this->step_buttons[$i] = new SwatButton('nav_step'.$i);
			$this->step_buttons[$i]->process();
			$this->step_buttons[$i]->title = $this->parent->getStepTitle($i);
			if ($this->step_buttons[$i]->clicked)
				$return = $i;
		}

		return $return;
	}

	public function display()
	{
		if (!$this->parent instanceof SwatWizardForm)
			throw new SwatException('SwatWizardNavigation: Must be a child '.
				'of a SwatWizardForm');
	
		$div = new SwatHtmlTag('div');
		$div->style = 'float:right;';
		$div->open();
		
		for ($i = 0; $i < $this->parent->getStepCount(); $i++)
			if ($i == $this->parent->step)
				echo $this->step_buttons[$i]->title.'<br />';
			elseif ($i < $this->parent->step || $i == ($this->parent->step + 1)) {
				$this->step_buttons[$i]->display();
				echo '<br />';
			}
	
		$div->close();
	}
}

?>
