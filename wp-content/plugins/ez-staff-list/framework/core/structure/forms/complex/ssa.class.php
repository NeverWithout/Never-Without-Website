<?php
/**
* A class for constructing Simple Selects from Array (SSA)
*
* @package		phpOpenFW
* @subpackage	Form_Engine
* @author 		Christian J. Clark
* @copyright	Copyright (c) Christian J. Clark
* @license		http://www.gnu.org/licenses/gpl-2.0.txt
* @version 		Started: 9-19-2005 Updated: 12-15-2011
**/

//***************************************************************
// Contributions by Lucas Hoezee ( http://thecodify.com/ )
// 5/10/2011
//***************************************************************

//**************************************************************************
/**
 * Simple Select from Array Class
 * @package		phpOpenFW
 * @subpackage	Form_Engine
 */
//**************************************************************************
class ssa extends element
{
	private $select_vals;	// Values of the select
	private $select_value;	// Selected Value
	private $blank;			// Blank Select Value array
	
	//*************************************************************************
	// Constructor Function
	//*************************************************************************
	public function __construct($name, $val_arr)
	{
		$this->attributes = array();
		$this->element = 'select';
		$this->attributes['name'] = $name;
		$this->blank = array();
		$this->select_vals = $val_arr;
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
	// Set the selected value
	//*************************************************************************
	public function selected_value($value)
	{
		$this->select_value = $value;
		settype($this->selected_value, 'string');
	}
	
	//*************************************************************************
	// Add a Blank or Default Select Option
	//*************************************************************************
	public function add_blank($value='', $desc='') { $this->blank[] = array($value, $desc); }
	
	//*************************************************************************
	// Construct and output the SSA.
	//*************************************************************************
	public function render($buffer=false)
	{
		ob_start();
		settype($this->select_value, 'string');
		
		// Added "Blank" Options
		foreach ($this->blank as $bv) {
			$o_attrs = array('value' => $bv[0]);
			if (isset($this->select_value)) {
                settype($bv[0], 'string');
				if ($this->select_value === $bv[0]) { $o_attrs['selected'] = 'selected'; }
			}
			$o = new gen_element('option', $bv[1], $o_attrs);
			$o->force_endtag(1);
			$o->render();
		}
		
		// Options
		$opt_group = null;
		foreach ($this->select_vals as $key => $value) {
			$o_attrs = array('value' => $key);
			if (is_array($value)) { 
				$tmp_val_arr = $value;
				$value = (isset($tmp_val_arr[0])) ? ($tmp_val_arr[0]) : (''); 
				if (isset($tmp_val_arr[1]) && $tmp_val_arr[1] !== $opt_group) {
					$opt_group = $tmp_val_arr[1];
					print new gen_element('optgroup', '', array('label' => $tmp_val_arr[1]));
				} 
			}
			if (isset($this->select_value)) {
                settype($key, 'string');
				if ($this->select_value === $key) { $o_attrs['selected'] = 'selected'; }
			}
			$o = new gen_element('option', $value, $o_attrs);
			$o->force_endtag(1);
			$o->render();
		}
		
		$this->inset_val .= ob_get_clean();
		parent::render($buffer);
	}	

}

?>
