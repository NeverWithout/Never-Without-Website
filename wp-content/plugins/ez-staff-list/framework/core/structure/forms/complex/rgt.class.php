<?php
/**
* A class for constructing a Radio Group from a Table (RGT)
*
* @package		phpOpenFW
* @subpackage	Form_Engine
* @author 		Christian J. Clark
* @copyright	Copyright (c) Christian J. Clark
* @license		http://www.gnu.org/licenses/gpl-2.0.txt
* @version 		Started: 9-21-2005 Updated: 12-15-2011
**/

//**************************************************************************
/**
 * Radio Group from Table Class
 * @package		phpOpenFW
 * @subpackage	Form_Engine
 */
//**************************************************************************
class rgt
{
	/**
	* @var string Name of the Radio buttons
	**/
	private $name;			// Name of the select
	private $data_src;		// Data Source
	private $strsql;		// SQL string to query
	private $checked_val;	// Checked Value
	private $style;			// Group Style: inline, newline (default)
	
	//*************************************************************************
	// Constructor Function
	//*************************************************************************
	public function __construct($name, $data_src, $strsql, $key, $val)
	{
		$this->name = $name;
		$this->data_src = $data_src;
		$this->strsql = $strsql;
		$this->opt_key = $key;
		$this->opt_val = $val;
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
	// Set the selected value
	//*************************************************************************
	public function checked_value($value)
	{
		$this->checked_val = $value;
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
	* RGT class render function
	**/
	//*************************************************************************
	// Construct and output the RGT.
	//*************************************************************************
	public function render()
	{
		$data = new data_trans($this->data_src);
		$data->data_query($this->strsql);
		$result = $data->data_assoc_result();
		
		foreach ($result as $row) {
			$tmp_radio = new radio($this->name, $row[$this->opt_key]);
			if (isset($this->checked_val)) {
				if ($this->checked_val == $row[$this->opt_key]) { $tmp_radio->set_attribute('checked', 'checked'); }
			}
			$tmp_radio->render();
			print '&nbsp;' . $row[$this->opt_val];
			if ($this->style == 'newline') { print '<br/>'; }
			print "\n";
		}
	}	

}

?>
