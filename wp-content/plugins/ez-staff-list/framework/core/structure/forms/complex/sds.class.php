<?php
/**
* A class for constructing a Simple Date Select (SDS)
*
* @package		phpOpenFW
* @subpackage	Form_Engine
* @author 		Christian J. Clark
* @copyright	Copyright (c) Christian J. Clark
* @license		http://www.gnu.org/licenses/gpl-2.0.txt
* @version 		Started: 9-23-2005 Updated: 12-15-2011
**/

//**************************************************************************
/**
 * Simple Date Select Class
 * @package		phpOpenFW
 * @subpackage	Form_Engine
 */
//**************************************************************************
class sds
{
	private $year_nm;		// Name of year field
	private $month_nm;		// Name of month field
	private $day_nm;		// name of day field
	private $start_year;	// First value of year drop down
	private $end_year;		// Last value of year drop down
	private $selected_date;	// look at the name of the variable, it's obvious :)
	private $add_blank;
	
	//*************************************************************************
	// Constructor Function
	//*************************************************************************
	public function __construct($y_nm, $m_nm, $d_nm, $add_blank=false)
	{
		$this->day_nm = $d_nm;
		$this->month_nm = $m_nm;
		$this->year_nm = $y_nm;
		$this->add_blank = $add_blank;
		$this->start_year = date('Y') - 5;
		$this->end_year = date('Y') + 5;
		//$this->selected_date = date('Y') . '-' . date('m') . '-' . date('d');
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
	// Set the selected date
	//*************************************************************************
	public function year_range($st_dt, $end_dt)
	{
		$this->start_year = $st_dt;
		$this->end_year = $end_dt;
	}

	//*************************************************************************
	// Set the selected date
	//*************************************************************************
	public function selected_date($in_date)
	{
		$this->selected_date = $in_date;
	}
	
	//*************************************************************************
	// Set the selected date
	//*************************************************************************
	public function selected($in_date)
	{
		$tmp_time = strtotime($in_date);
		print $tmp_time;
		$this->selected_date = date('Y-m-d', $tmp_time);
		print $this->selected_date;
		//$this->selected_date = $in_date;
	}
	
	//*************************************************************************
	// Construct and output the SDS.
	//*************************************************************************
	public function render()
	{
		$month_abbrev = array(1 => 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

		// Selected Date
		if (isset($this->selected_date) && $this->selected_date) {
			$year = substr($this->selected_date, 0, 4);
			$month = substr($this->selected_date, 5, 2);
			$day = substr($this->selected_date, 8, 2);
		}

		// Month
		if (!empty($this->month_nm)) {
			$tmp_arr = array();
			$selected = '';
			for ($m=1; $m<=12; $m++) {
				$m_val = (strlen($m) == 1) ? ("0$m") : ($m);
				$tmp_arr[$m_val] = $month_abbrev[$m];
				if(isset($month) && $m == $month) { $selected = $m_val; }
			}
			$ssa_tmp = new ssa($this->month_nm, $tmp_arr);
			if ($this->add_blank) { $ssa_tmp->add_blank(); }
			$ssa_tmp->selected_value($selected);
			$ssa_tmp->render();
		}

		// Day
		if (!empty($this->day_nm)) {
			$tmp_arr = array();
			$selected = '';
			for ($m=1; $m<=31; $m++) {
				if(isset($day) && $m == $day) { $selected = $m; }
				$tmp_arr[$m] = $m;
			}
			$ssa_tmp = new ssa($this->day_nm, $tmp_arr);
			if ($this->add_blank) { $ssa_tmp->add_blank(); }
			$ssa_tmp->selected_value($selected);
			$ssa_tmp->render();
		}

		// Year
		if (!empty($this->year_nm)) {
			$tmp_arr = array();
			$selected = '';
			for ($m = $this->start_year; $m <= $this->end_year; $m++) {
				if(isset($year) && $m == $year) { $selected = $m; }
				$tmp_arr[$m] = $m;
			}
			$ssa_tmp = new ssa($this->year_nm, $tmp_arr);
			if ($this->add_blank) { $ssa_tmp->add_blank(); }
			$ssa_tmp->selected_value($selected);
			$ssa_tmp->render();
		}
	}	

}

?>
