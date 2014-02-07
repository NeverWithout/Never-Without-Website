<?php
/**
* A class for performing javascript validation on forms
* 
* @package		phpOpenFW
* @subpackage	Form_Engine
* @author 		Christian J. Clark
* @copyright	Copyright (c) Christian J. Clark
* @license		http://www.gnu.org/licenses/gpl-2.0.txt
* @version 		Started: 11-16-2005 Updated: 3-20-2010
* @internal
*/

//**************************************************************************
/**
 * Javascript Validation Class
 * @package		phpOpenFW
 * @subpackage	Form_Engine
 */
//**************************************************************************
class jsv
{
	//*************************************************************************
	// Class variables
	//*************************************************************************
	/**
	* @var array An array of the javascript validations to be rendered
	**/
	private $valids;			// Array of validations to be rendered
	
	/**
	* @var string Form name
	**/
	private $form_name;			// Form name
	
	/**
	* @var string Validate function name
	**/
	private $validate_name;			// Validate function name
	
	/**
	* JSV constructor Function
	**/
	//*************************************************************************
	// Constructor Function
	//*************************************************************************
	public function __construct()
	{
		$this->valids = array();
		$this->validate_name = 'validate';
	}

	/**
	* Add validation for a field in the form
	* @param string Name of vield to validate
	* @param string Type of validation to perform on the field
	* @param string Message to display upon failed validation
	* @param string Name of second field if doing comparison validation (or other parameter)
	**/
	//*************************************************************************
	// Add Validation Function
	//*************************************************************************
	public function add_valid($field_name, $valid_type, $valid_txt, $field2_name='')
	{
		$valid_types = array();
		$valid_types['is_empty'] = '';
		$valid_types['is_not_empty'] = '';
		$valid_types['is_numeric'] = '';
		$valid_types['is_not_numeric'] = '';
		$valid_types['is_date'] = '';
		$valid_types['fields_match'] = '';
		$valid_types['fields_not_match'] = '';
		$valid_types['custom'] = '';
		$valid_types['checkbox_is_checked'] = '';
		$valid_types['radio_is_checked'] = '';
		
		if (!array_key_exists($valid_type, $valid_types)) {
			$valid_type = 'is_not_empty';
		}
		
		$value = "f.$field_name.value";
		$value2 = "f.$field2_name.value";
		
		switch ($valid_type) {
			case 'is_empty':
				// Validate that field is empty
				$expression = "$value != ''";
				break;
				
			case 'is_numeric':
				// Validate that field is numeric
				$expression = "!IsNumeric($value)";
				break;
				
			case 'is_not_numeric':
				// Validate that field is not numeric
				$expression = "IsNumeric($value)";
				break;
				
			case 'fields_match':
				// Validate that field1 and field2 match
				$expression = "$value != $value2";
				break;
				
			case 'fields_not_match':
				// Validate that field1 and field2 do not match
				$expression = "$value == $value2";
				break;
			
			case 'is_date':
				// Validate that field is a date
				$expression = "!isDate($value)";
				break;
				
			case 'custom':
				// Custom Validation Expression
				$expression = ($field_name != '') ? ($field_name) : ($field2_name);
				break;

			case 'checkbox_is_checked':
				// Validate that a Checkbox is checked
				$expression = 'f.' . $field_name . '.checked == false';
				break;

			case 'radio_is_checked':
				// Validate that a radio input is checked
				$num_elements = $field2_name + 0;
				$expression = '';
				for ($i = 0; $i < $num_elements; $i++) {
					if ($i > 0) { $expression .= ' && '; }
					$expression .= 'f.' . $field_name . "[$i].checked == false";
				}
				if ($num_elements <= 0) {
					$element_error = "($field_name:radio_is_checked) You must specify the number of elements in the radio group.";
				}
				break;
				
			default:
				// Validate that field is not empty
				$expression = "$value == ''";
				break;
		}
		
		if (isset($expression) && $expression != '') {
			array_push($this->valids, array($expression, $valid_txt));	
		}
		else if (isset($element_error)) {
			trigger_error("Error: [Form]::add_validation() -> $element_error");
		}
	}
	
	/**
	* Set Form Name Function
	* @param string Form Name
	**/
	//*************************************************************************
	// Set Form Name Function
	//*************************************************************************
	public function set_form_name($form_name)
	{
	   $this->form_name = $form_name;
	   settype($this->form_name, 'string');
	   $this->validate_name = $this->form_name . '_validate';
	}
	
	/**
	* JSV class render function
	**/
	//*************************************************************************
	// Render Function
	//*************************************************************************
	public function render($buffer=false)
	{
		if ($buffer) { ob_start(); }
		print "<script type=\"text/javascript\">\n";
		print "// validate() is a simple function that will validate user input\n";
		print "function " . $this->validate_name . "(f)\n";
		print "{\n";
		
		print "\n\t// Declare / Initialize variables\n";
		print "\tvar caption = ''\n";
		print "\tvar new_caption = '';\n";
		
		print "\n\t// Create validation checks.\n";
		foreach ($this->valids as $field_valid) {
			print "\tif ($field_valid[0])\n";
			print "\t{\n";
			print "\t\tcaption += \"\\n - $field_valid[1]\";\n"; 
			print "\t}\n";
		}
		
		print "\n\t// Check if we had any failed validations and display alerts as necessary.\n";
		print "\tif (caption != '')\n";
		print "\t{\n";
		print "\t\tnew_caption = \"The following errors were encountered:\\n\" + caption;\n";
		print "\t\talert(new_caption);\n";
		print "\t\treturn false;\n";
		print "\t}\n";		
		print "}\n";
		print "</script>\n";
		if ($buffer) { return ob_get_clean(); }
	}

}
?>