<?php
/**
* A class for constructing a Radio Group from Array (RGA)
*
* @package		phpOpenFW
* @subpackage	Form_Engine
* @author 		Christian J. Clark
* @copyright	Copyright (c) Christian J. Clark
* @license		http://www.gnu.org/licenses/gpl-2.0.txt
* @version 		Started: 9-22-2005 Updated: 12-15-2011
**/

//**************************************************************************
/**
 * Radio Group from Array Class
 * @package		phpOpenFW
 * @subpackage	Form_Engine
 */
//**************************************************************************
class rga
{
	/**
	* @var string Name of the Radio buttons
	**/
	private $name;			// Name :)
	
	/**
	* @var array An associative array of radio buttons where each element is in the form ["value => "desc"]
	**/
	private $buttons;		// Array: [value] => [desc]
	
	/**
	* @var mixed The value of the checked element
	**/
	private $checked_value;	// Checked button
	
	/**
	* @var string The display style for the group of radio buttons (inline, newline (default))
	**/
	private $style;			// Group Style: inline, newline (default)
	
	/**
	* RGA constructor function
	* @param string Name of the radio buttons
	* @param array A properly formatted array representing the radio buttons
	**/
	//*************************************************************************
	// Constructor Function
	//*************************************************************************
	public function __construct($name, $buttons)
	{
		$this->name = $name;
		$this->buttons = is_array($buttons) ? ($buttons) : (array());
		$this->style = 'newline';
	}
	
	//*************************************************************************
	// String Conversion Function
	//*************************************************************************
	public function __toString()
	{
		ob_start();
		$this->render();
		return ob_get_clean();
	}

	/**
	* Set the value of the checked radio button
	* @param mixed The value of the checked radio button
	**/
	//*************************************************************************
	// Set the checked values
	//*************************************************************************
	public function checked_value($checked)
	{
		$this->checked_value = $checked;
	}

	/**
	* Set the display style of radio button group
	* @param string The display style of the radio button group (inline, newline (default))
	**/	
	//*************************************************************************
	// Set the style
	//*************************************************************************
	public function style($style)
	{
		if (strtolower($style) == 'inline' || strtolower($style) == 'newline') {
			$this->style = strtolower($style);
		}
	}	
	
	/**
	* RGA class render function
	**/
	//*************************************************************************
	// Construct and output the RGA.
	//*************************************************************************
	public function render()
	{
		foreach($this->buttons as $value => $desc) {
			$is_checked = false;
			if (isset($this->checked_value) && $this->checked_value == $value) {
				$is_checked = true;
			}
			$r = new radio($this->name, $value, $is_checked);
			print $r . '&nbsp;' . $desc;
			if ($this->style == 'newline') { print '<br/>'; }
		}			
	}	

}

?>
