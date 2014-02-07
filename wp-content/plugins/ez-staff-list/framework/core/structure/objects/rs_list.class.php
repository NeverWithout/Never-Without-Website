<?php
/**
* Record Set List Object class for contructing tabular lists
* @package		phpOpenFW
* @subpackage	Objects
* @author		Christian J. Clark
* @copyright	Copyright (c) Christian J. Clark
* @license		http://www.gnu.org/licenses/gpl-2.0.txt
* @version		Started: 1-27-2006 Updated: 1-25-2012
*/

//***************************************************************
/**
 * Record Set List Class
 * @package		phpOpenFW
 * @subpackage	Objects
 */
//***************************************************************
class rs_list
{
	//*********************************************************************
	// Class variables
	//*********************************************************************
	/**
	* @var array An array that describes how to layout the list
	**/
	private $data_outline;
	
	/**
	* @var array The record set to build the list from
	**/
	private $record_set;
	
	/**
	* @var integer Number of columns in a recordset
	**/
	private $cols;
	
	/**
	* @var string Label to be used for the list
	**/
	private $label;
	
	/**
	* @var string The XML derived from the record set
	**/
	private $xml;
	
	/**
	* @var string The XSL template to be used during transformation
	**/
	private $xsl_template;
	
	/**
	* @var string The CSS id of the table
	**/
	private $table_id;
	
	/**
	* @var string The CSS class of the table
	**/
	private $table_class;
	
	/**
	* @var bool Display the column headers (TRUE - On, FALSE - Off)
	**/
	private $display_col_headers;
	
	/**
	* @var integer Maximum number of rows to display
	**/
	private $max_rows;

	/**
	* @var string Show more link
	**/
	private $more_link;
	
	/**
	* @var string Multi-part Cell Separator
	**/
	private $separator;
	
	/**
	* @var string Result set format (table or LDAP)
	**/
	private $result_format;

	/**
	* @var array Record set attributes
	**/
	private $rs_attrs;
	
	/**
	* @var String Message Displayed upon empty record set.
	**/
	private $empty_msg;
	
	/**
	* @var String Column to group by.
	**/
	private $group_by;
	
	/**
	* @var Array Column Header Values (Key => Val).
	**/
	private $group_by_vals;
	
	/**
	* @var Array Record Set Keys.
	**/
	private $rs_keys;

	/**
	* @var Bool Escape Data (CDATA) Default = true.
	**/
	private $escape_data;

	
	//****************************************************************************
	/**
	* rs_list contstructor function
	* @param array an associative array that describes the field layout of the list
	* [format: "field" => "label"]
	* @param array the record set to be displayed
	**/
	//****************************************************************************
	public function __construct($do, $rs)
	{
		$this->data_outline = $do;
		$this->record_set = $rs;
		if (count($this->record_set) > 0) {
            $first_key = key($this->record_set); 
            $this->cols = count($this->record_set[$first_key]);
        }
		else { $this->cols = 0; }
		$this->display_col_headers = true;
		$this->max_rows = -1;
		$this->show_more = false;
		$this->separator = "<br/>\n";
		$this->result_format = 'table';
		$this->empty_msg = '[!] No results found.';
		$this->group_by = false;
		$this->group_by_vals = false;
		$this->xml = '';
		$this->rs_keys = (is_array($this->record_set)) ? (array_keys($this->record_set)) : (false);
		$this->escape_data = true;

		// Default Template
		if (isset($_SESSION['frame_path'])) {
			$this->xsl_template = $_SESSION['frame_path'] . '/default_templates/rs_list.xsl';
		}
	}

	//****************************************************************************
	/**
	* Set XSL template function
	* If this is not set, the raw XML will be printed
	* @param string The file path to the stylesheet
	**/
	//****************************************************************************
	public function set_xsl($stylesheet) { $this->xsl_template = $stylesheet; }

	//****************************************************************************
	/**
	* Set the label to be used for the list
	* @param string List Label
	**/
	//****************************************************************************
	public function label($label) { $this->label = $label; }

	//****************************************************************************
	/**
	* Set the CSS id and class of the table
	* @param string CSS id of the table
	* @param string CSS class of the table
	**/
	//****************************************************************************
	public function identify($id, $class = '')
	{
		if (!empty($id)) { $this->table_id = $id; }
		if (!empty($class)) { $this->table_class = $class; }
	}

	//****************************************************************************
	/**
	* Turn the display headers property on/off
	* @param bool Turn the display headers property on/off
	**/
	//****************************************************************************
	public function display_headers($bool)
	{
		if ($bool) { $this->display_col_headers = true; }
		else { $this->display_col_headers = false; }
	}

	//****************************************************************************
	/**
	* Set the maximum number of rows to display
	* @param integer Set the maximum number of rows to display
	**/
	//****************************************************************************
	public function set_max_rows($num)
	{
		$num += 0;
		$this->max_rows = $num;
	}

	//****************************************************************************
	/**
	* Add a link to show more results
	* @param bool Add a link to show more results
	**/
	//****************************************************************************
	public function show_more($more_link) { $this->more_link = $more_link; }

	//****************************************************************************
	/**
	* Set Multi-part Cell Separator
	* @param string Separator String
	**/
	//****************************************************************************
	public function set_separator($string)
	{
		$this->separator = $string;
		settype($this->separator, 'string');
	}

	//****************************************************************************
	/**
	* Set the result set format
	* @param string Result Set format
	**/
	//****************************************************************************
	public function set_result_format($format)
	{
		$this->result_format = $format;
		settype($this->result_format, 'string');
		$this->result_format = strtolower($this->result_format);
	}

	//***************************************************************************************
	/**
	* Turn off xsl transformation
	**/
	//***************************************************************************************	
	public function no_xsl() { $this->xsl_template = ''; }
	
	//***************************************************************************************
	/**
	* Set Empty Message
	**/
	//***************************************************************************************	
	public function empty_message($msg)
	{
		if (gettype($msg) == 'string') { $this->empty_msg = $msg; }
	}
	
	//***************************************************************************************
	/**
	* Set Group By
	**/
	//***************************************************************************************	
	public function group_by($col, $col_vals='')
	{
		$first_index = ($this->rs_keys) ? ($this->rs_keys[0]) : (0);
		if (isset($this->record_set[$first_index]) && array_key_exists($col, $this->record_set[$first_index])) {
			$this->group_by = $col;
			$this->xsl_template = $_SESSION['frame_path'] . '/default_templates/table_group.xsl';
		}
		else if (count($this->record_set) > 0) {
			trigger_error("Error: [RS_LIST]::group_by(): '$col' is not a vaild column in the current record set!");
		}
		if (gettype($col_vals) == 'array') { $this->group_by_vals = $col_vals; }
	}
	
	//****************************************************************************
	/**
	* Escape Cell Data with XML "CDATA"
	* @param bool Escape = True (Default), Do Not Escape = False
	**/
	//****************************************************************************
	public function escape_data($bool) { $this->escape_data = ($bool) ? (true) : (false); }

	//****************************************************************************
	/**
	* Set a row attribute
	* @param mixed Row Key
	* @param string Attribute
	* @param mixed Value
	**/
	//****************************************************************************
	public function set_row_attr($row, $attr, $val, $overwrite=false)
	{
		if (!empty($attr)) {
			if (isset($this->rs_attrs[$row]['attributes'][$attr]) && !$overwrite) {
				$this->rs_attrs[$row]['attributes'][$attr] .= " $val";
			}
			else {
				$this->rs_attrs[$row]['attributes'][$attr] = $val;
			}
		}
		else { trigger_error('Error: [RS_LIST]::set_row_attr: Empty Attribute!'); }
	}

	//****************************************************************************	
	/**
	* Set a cell attribute
	* @param mixed Row Key
	* @param mixed Cell Key
	* @param string Attribute
	* @param mixed Value
	**/
	//****************************************************************************
	public function set_cell_attr($row, $cell, $attr, $val, $overwrite=false)
	{
		if (!empty($attr)) {
			if (isset($this->rs_attrs[$row]['cells'][$cell]['attributes'][$attr]) && !$overwrite) {
				$this->rs_attrs[$row]['cells'][$cell]['attributes'][$attr] .= " $val";
			}
			else {
				$this->rs_attrs[$row]['cells'][$cell]['attributes'][$attr] = $val;
			}
		}
		else { trigger_error('Error: [RS_LIST]::set_cell_attr(): Empty Attribute!'); }
	}

	//****************************************************************************
	/**
	* Set a column attribute
	* @param mixed Column Key
	* @param string Attribute
	* @param mixed Value
	**/
	//****************************************************************************
	public function set_col_attr($col, $attr, $val, $overwrite=false)
	{
		$first_index = ($this->rs_keys) ? ($this->rs_keys[0]) : (0);
		if (!empty($attr)) {
			if (isset($this->record_set[$first_index][$col])) {
				if (isset($this->rs_attrs['col_attrs'][$col][$attr]) && !$overwrite) {
					$this->rs_attrs['col_attrs'][$col][$attr] .= " $val";
				}
				else {
					$this->rs_attrs['col_attrs'][$col][$attr] = $val;
				}
			}
			else if (count($this->record_set) > 0) {
				trigger_error("Error: [RS_LIST]::set_col_attr(): '$col' is not a vaild column in the current record set!");
			}
		}
		else { trigger_error('Error: [RS_LIST]::set_col_attr(): Empty Attribute!'); }
	}

	//****************************************************************************
	/**
	* Render the rs_list
	**/
	//****************************************************************************
	public function render()
	{
		$order = array();
		
		// Start Table or Table Group
		if (!$this->group_by) { $this->start_table($order, $this->label); }
		else { $this->xml .= "<table_group>\n"; }
		
		$row_count = 0;
		$gbv = NULL;
		
		foreach ($this->record_set as $row_key => $row) {

            // Set row_key to string
            settype($row_key, 'string');
		
            // Skip LDAP "Count" Row
            if ($this->result_format == 'ldap' && $row_key == 'count') { continue; }
            
            // Start Group By Table (if group_by being used)
            if ($this->group_by && isset($row[$this->group_by]) && $gbv !== $row[$this->group_by]) {
            		// End Current Table
            		if (!is_null($gbv)) { $this->end_table($row_count); }
            		
            		// Start New Table
            		$table_label = isset($this->group_by_vals[$row[$this->group_by]]) ? ($this->group_by_vals[$row[$this->group_by]]) : ($row[$this->group_by]);
            		$this->start_table($order, $table_label);
            		$row_count = 0;
            		$gbv = $row[$this->group_by];
            }
            
            // Group By: Max Rows Reached
            if ($this->group_by && $this->max_rows != -1 && $row_count > $this->max_rows) { continue; }
            
            // Start Row
			$this->start_row($row_key, $row_count);
			
			// Row Cells
			$cell_count = 0;
			foreach ($order as $key => $val) {
			
				// Start Cell
				$this->start_cell($row_key, $val);
				
                // LDAP Record Set
                if ($this->result_format == 'ldap' && isset($row[$val])) {
                    foreach ($row[$val] as $part_index => $part_val) {
                        if (!$this->escape_data) { $part_val = str_replace('&', '&amp;', $part_val); }
				        if (is_numeric($part_index)) {
				        	if ($this->escape_data && $this->xsl_template != '') { $this->xml .= '<![CDATA['; }
				        	$this->xml .= "{$part_val}";
				        	if ($this->escape_data && $this->xsl_template != '') { $this->xml .= ']]>';	}
				        }
                    }
				}
				// Table Record Set
				else if ($this->result_format == 'table') {
				    if (!$this->escape_data) { $row[$val] = str_replace('&', '&amp;', $row[$val]); }
				    if ($this->escape_data && $this->xsl_template != '') { $this->xml .= '<![CDATA['; }
				    $this->xml .= "{$row[$val]}";
				    if ($this->escape_data && $this->xsl_template != '') { $this->xml .= ']]>';	}
				}
				
				$this->xml .= "</cell>\n";
				$cell_count++;
			}
			$this->xml .=  "\t\t</row>\n";
			$row_count++;
			if ($this->max_rows > 0 && $row_count > $this->max_rows && !isset($this->more_link)) { 
				if (!$this->group_by) { break; } 
			}
		}
		
		// End Table
		$this->end_table($row_count);

		// End Table Groups
		if ($this->group_by) {	$this->xml .= "</table_group>\n"; }

		// Perform XML Transformation
		$sxoe = (isset($_SESSION['show_xml_on_error']) && $_SESSION['show_xml_on_error'] == 1) ? (true) : (false);
		xml_transform($this->xml, $this->xsl_template, $sxoe);
	}

	//***************************************************************************************
	/**
	* Start Table Function
	**/
	//***************************************************************************************	
	private function start_table(&$order, $label='')
	{
		$order_set = count($order);
	
		// Start Table Tag
		$this->xml .= '<table';
		if (isset($this->table_id) && !$this->group_by) { $this->xml .= " id=\"$this->table_id\""; }
		if (isset($this->table_class)) { $this->xml .= " class=\"$this->table_class\""; }
		$this->xml .= ">\n";
		
		// Table Label
		if (!empty($label)) { $this->xml .=  "\t<label>$label</label>\n"; }
		
		// Header
		if ($this->display_col_headers) {
			$this->xml .=  "\t<header>\n";
			$this->xml .=  "\t\t<row>\n";
			foreach ($this->data_outline as $key => $val) {
				$this->xml .=  "\t\t\t<cell>$val</cell>\n";
				if (!$order_set) { array_push($order, $key); }
			}
			$this->xml .=  "\t\t</row>\n";
			$this->xml .=  "\t</header>\n";
		}
		else if (!$order_set) {
			foreach ($this->data_outline as $key => $val) {
				array_push($order, $key);
			}
		}
		
		// Start Content
		$this->xml .= "\t<content>\n";
	}
	
	//***************************************************************************************
	/**
	* End Table Function
	**/
	//***************************************************************************************	
	private function end_table($row_count)
	{
		if (isset($this->more_link) && $row_count > $this->max_rows && $row_count > 0) {
			$remain_rows = $row_count - $this->max_rows - 1;
			$this->xml .= "\t\t<row class=\"more_link\">\n";
			$this->xml .= "\t\t\t<cell colspan=\"$this->cols\"><a href=\"$this->more_link\">( $remain_rows more... )</a></cell>\n";
			$this->xml .=  "\t\t</row>\n";
		}
		
		if ($row_count == 0) {
			$this->xml .= "\t\t<row>\n";
			$ecols = count($this->data_outline);
			$this->xml .= "\t\t\t<cell colspan=\"$ecols\">$this->empty_msg</cell>\n";
			$this->xml .=  "\t\t</row>\n";
		}
		
		$this->xml .=  "\t</content>\n";
		
		// Footer (eventually...)
		$this->xml .=  "\t<footer></footer>\n";
		
		// End Table
		$this->xml .=  "\t<count>" . $row_count . "</count>\n";
		$this->xml .= "</table>\n";
	}
	
	//***************************************************************************************
	/**
	* Start Row Function
	**/
	//***************************************************************************************	
	private function start_row($row_key, $row_count)
	{
		$this->xml .= "\t\t<row";
			
		// Alt Row
		if ($row_count % 2 == 1) { $this->set_row_attr($row_key, 'class', 'alt'); }
			
		// Max Rows
		if ($this->max_rows != -1 && $row_count > $this->max_rows) {
			$this->set_row_attr($row_key, 'class', 'hidden');
			$this->set_row_attr($row_key, 'style', 'display: none;');
		}
			
		// Set Row Attributes
		if (isset($this->rs_attrs[$row_key]['attributes'])) {
			foreach ($this->rs_attrs[$row_key]['attributes'] as $attr => $val) {
				$this->xml .= " $attr=\"$val\"";
			}
		}
			
		$this->xml .= ">\n";
	}
	
	//***************************************************************************************
	/**
	* Start Cell Function
	**/
	//***************************************************************************************	
	private function start_cell($row_key, $cell)
	{
		$this->xml .= "\t\t\t<cell";
		
		// Check for global Column Attributes
		if (isset($this->rs_attrs['col_attrs'][$cell])) {
			foreach ($this->rs_attrs['col_attrs'][$cell] as $col_attr => $col_val) {
				if (isset($this->rs_attrs[$row_key]['cells'][$cell]['attributes'][$col_attr])) {
					$this->rs_attrs[$row_key]['cells'][$cell]['attributes'][$col_attr] .= " $col_val";
				}
				else {
					$this->rs_attrs[$row_key]['cells'][$cell]['attributes'][$col_attr] = $col_val;
				}
			}
		}
		
		// Set Cell Attributes
		if (isset($this->rs_attrs[$row_key]['cells'][$cell])) {
			foreach ($this->rs_attrs[$row_key]['cells'][$cell]['attributes'] as $attr_key => $attr_val) {
				$this->xml .= " $attr_key=\"$attr_val\"";
			}
		}
				
		$this->xml .= '>';
	}
	
}

?>
