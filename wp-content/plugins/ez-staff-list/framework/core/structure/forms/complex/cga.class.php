<?php
/**
* A class for constructing a Checkbox Group from Array (CGA)
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
 * Checkbox Group from Array Class
 * @package		phpOpenFW
 * @subpackage	Form_Engine
 */
//**************************************************************************
class cga
{
	private $checkboxes;	// Array of Array([Name], [Value], [Desc])
	private $attrs;			// Attributes of the checkboxes
	private $checked;		// Checked items
	private $style;			// Group Style: inline, newline (default)
	
	//*************************************************************************
	// Constructor Function
	//*************************************************************************
	public function __construct($checkboxes)
	{
		$this->checkboxes = $checkboxes;
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

	//*************************************************************************
	// Set the checked values
	//*************************************************************************
	public function checked_value($checked)
	{
		if (is_array($checked)) { $this->checked = $checked; }
	}
	
	//*************************************************************************
	// Set the checked values
	//*************************************************************************
	public function checked($checked)
	{
		if (is_array($checked)) { $this->checked = $checked; }
	}
	
	//*************************************************************************
	// Set the style
	//*************************************************************************
	public function style($style)
	{
		if (strtolower($style) == 'inline' || strtolower($style) == 'newline') {
			$this->style = strtolower($style);
		}
	}
	
	//*************************************************************************
	// Construct and output the CGA.
	//*************************************************************************
	public function render()
	{
		foreach($this->checkboxes as $checkbox) {
			$is_checked = false;
			if (isset($this->checked[$checkbox[0]]) && $this->checked[$checkbox[0]] == 1) { $is_checked = true; }
			$c = new checkbox($checkbox[0], $checkbox[1], $is_checked);
			print $c;
			if (isset($checkbox[2])) { print '&nbsp;' . $checkbox[2]; }
			if ($this->style == 'newline') { print '<br/>'; }
		}			
	}	

}

?>
