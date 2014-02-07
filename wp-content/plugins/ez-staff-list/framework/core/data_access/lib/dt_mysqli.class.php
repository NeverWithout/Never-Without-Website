<?php

/**
* Data Transaction / MySQL Improved (mysqli) Plugin
* A MySQLi plugin to the (data_trans) class
* @package		phpOpenFW
* @subpackage 	Database_Tools
* @author 		Christian J. Clark
* @copyright	Copyright (c) Christian J. Clark
* @license		http://www.gnu.org/licenses/gpl-2.0.txt
* @access		private
* @version 		Started: 6-3-2009 updated: 3-22-2012
*/

//***************************************************************
/**
 * dt_mysqli Class
 * @package		phpOpenFW
 * @subpackage	Database_Tools
 * @access		private
 */
//***************************************************************
class dt_mysqli extends dt_structure
{

    /**
	* Opens a connection to the specified data source based on the data source type
	**/
	//*************************************************************************
	// Make a connection to the given source and store the handle
	//*************************************************************************
	public function open()
	{
		if (!$this->handle) {
			if (!$this->port) { $this->port = 3306; }
			if ($this->persistent) { $this->server = 'p:' . $this->server; }
	        $this->handle = @new mysqli($this->server, $this->user, $this->pass, $this->source, $this->port);

	        if ($this->handle->connect_errno) {
	        	$this->connection_error($this->handle->connect_error, $this->handle->connect_errno);
	        	$this->handle = false;
				return false;
	        }

			// Keep track of the number of connections we create
			$this->increment_counters();
		}

		// Flag Connection as Open
        $this->conn_open = true;

		// Start Transaction and Turn off Auto Commit?
        if (!$this->auto_commit && !$this->trans_started) {
        	$this->handle->autocommit($this->auto_commit);
        	$this->start_trans();
        }

        return true;
	}
	
	/**
	* Closes a connection to the specified data source based on the data source type
	**/
	//*************************************************************************
	// Close the connection to the data source
	//*************************************************************************
    public function close()
	{
		$this->conn_open = false;
		if (!$this->reuse_connection) {
			if ($this->conn_open) { return $this->handle->close(); }
		}
		return true;
	}

	/**
	* Executes a query based on the data source type
	* @param mixed MySQL: SQL Statement
	**/
	//*************************************************************************
	// Execute a query and store the record set
	//*************************************************************************
	public function query($query)
	{
		// Check for Open Connection
		if (!$this->is_open()) { return false; }
		$this->curr_query = $query;

		// Execute Query
        $this->rsrc_id = $this->handle->query($query);
        if ($this->rsrc_id && gettype($this->rsrc_id) != 'boolean') {
            $this->num_rows = $this->rsrc_id->num_rows;
            while($row = $this->rsrc_id->fetch_assoc()){
                array_push($this->result, $row);
            }
        }

        // Last Insert ID
        $this->last_id = $this->handle->insert_id;

        // Check for error
        $is_error = $this->check_and_print_error($this->handle);
		if ($is_error) { return false; }
		else { return $this->handle->affected_rows; }
	}

	//*************************************************************************
	/**
	* Check for Database Error
	**/
	//*************************************************************************
	// Check and Print Database Error
	//*************************************************************************
	private function check_and_print_error($rsrc=false)
	{
		if (!$rsrc) { return false; }
		else if ($rsrc->errno) {
			$this->print_error($rsrc->errno, $rsrc->error);
			return true;
		}
		else { return false; }
	}

	//*************************************************************************
	/**
	* Check for Database Statement Error
	**/
	//*************************************************************************
	// Check and Print Database Statement Error
	//*************************************************************************
	private function check_and_print_stmt_error($stmt=false)
	{
		if (!$stmt) { return false; }
		else if ($stmt->errno) {
			$this->print_error($stmt->errno, $stmt->error);
			return true;
		}
		else { return false; }
	}

	//*************************************************************************
	/**
	* Print Database Error
	**/
	//*************************************************************************
	// Print Database Error
	//*************************************************************************
	private function print_error($errno=false, $error=false, $is_stmt=false)
	{
		if (!$errno || !$error) { return false; }
		else {
			// Check if this is web environment or not
			$msg_break = (isset($_SERVER)) ? ("<br/>\n") : ("\n");
			if ($is_stmt) { $error_msg = 'MySQL Statement Error: ' . $msg_break; }
			else { $error_msg = 'MySQL Error: ' . $msg_break; }
			$error_msg .= '[Code] -> ' . $errno . $msg_break;
			$error_msg .= '[Message] -> ' . $error . $msg_break;
			$this->gen_error($error_msg);
			return true;
		}
	}

	//*************************************************************************
	/**
	* Start a new Database Transaction
	**/
	//*************************************************************************
	protected function _start_trans()
	{
		$st_status = $this->query('START TRANSACTION');
		return ($st_status !== false) ? (true) : (false);
	}

    //*************************************************************************
	/**
	* Internal Auto Commit Function
	**/
	//*************************************************************************
    protected function _auto_commit($curr, $new)
    {
    	$ac_status = $this->handle->autocommit($this->auto_commit);
    	if ($ac_status) {
	    	if (!$new && !$this->trans_started) { $this->start_trans(); }
	    	else { $this->trans_started = false; }
	    	return true;
		}
		else { return false; }
    }

	//*************************************************************************
	/**
	* Internal Commit Function
	**/
	//*************************************************************************
	protected function _commit() { return $this->handle->commit(); }

	//*************************************************************************
	/**
	* Internal Rollback Function
	**/
	//*************************************************************************
	protected function _rollback() { return $this->handle->rollback(); }

    //*************************************************************************
	/**
	* Prepare Function
	* @param string SQL Statement
	**/
	//*************************************************************************
    public function prepare($stmt=false)
    {
    	$this->stmt = $this->handle->prepare($stmt);
    	$this->curr_query = $stmt;

		//----------------------------------------------
        // Check for error
		//----------------------------------------------
        $is_error = $this->check_and_print_error($this->handle);
		if ($is_error) { return false; }
		else { return true; }
    }

    //*************************************************************************
	/**
	* Execute Function
	* @param string SQL Statement
	* @param bool Lazy mode: If true, references of values will be created for you
	**/
	//*************************************************************************
    public function execute($bind_params=false)
    {
    	if (!$this->stmt) { return false; }
    	if ($bind_params && !is_array($bind_params)) {
    		$this->gen_error('Binding parameters must be passed as an array.');
    		return false;
    	}
    	$this->bind_params = $bind_params;

		//----------------------------------------------
		// Function Arguments
		//----------------------------------------------
		$arg_list = func_get_args();
		$lazy = (isset($arg_list[1]) && $arg_list[1]) ? (true) : (false);

		//----------------------------------------------
		// Bind Parameters
		//----------------------------------------------
		if ($bind_params) {

			// Are you feeling a bit lazy? Let's just make those values into references for you…
			if ($lazy || $this->get_opt('make_bind_params_refs')) {
				$tmp_bind_params = $bind_params;
				for ($i = 1; $i < count($bind_params); $i++) {
					$bind_params[$i] =& $tmp_bind_params[$i];
				}
			}

			// Call Bind Parameters Methid
	    	$bind_status = call_user_func_array(array($this->stmt, 'bind_param'), $bind_params);
	    	if (!$bind_status) {
	    		$this->gen_error('Binding of parameter data failed.');
	    		return false;
	    	}
		}

		//----------------------------------------------
		// Execute Query
		//----------------------------------------------
		$exec_status = $this->stmt->execute();
    	if (!$exec_status) {
    		$this->check_and_print_error($this->handle);
    		$this->gen_error('Query execution failed.');
    		return false;
    	}
    	else {
			// Last Insert ID
	        $this->last_id = $this->handle->insert_id;
    	}

		if ($meta_data = $this->stmt->result_metadata()) {
			$this->stmt->store_result();
			$this->num_rows = $this->stmt->num_rows;

	        $fields = array();
	        $row = array();
	        $count = 0;
	        while($field = $meta_data->fetch_field()) {
	            $fields[$count] = &$row[$field->name];
	            $count++;
	        }	       
	        call_user_func_array(array($this->stmt, 'bind_result'), $fields);

			while ($this->stmt->fetch()) {
			    //print_array($row);
			    $new_row = array();
			    foreach ($row as $key => $value){ $new_row[$key] = $value; }
			    array_push($this->result, $new_row);
			}
		}

		//----------------------------------------------
        // Check for error
		//----------------------------------------------
        $is_error = $this->check_and_print_stmt_error($this->stmt);
		if ($is_error) { return false; }
		else { return $this->handle->affected_rows; }
    }

	//*************************************************************************
	/**
	* Shutdown function
	**/
	//*************************************************************************
	public function shutdown()
	{
		if ($this->stmt) { $this->stmt->close(); }
	}

	//*************************************************************************
	/**
	* Get Combined Query function
	**/
	//*************************************************************************
	public function get_combined_query($query, $bind_params)
	{
		if (!is_array($bind_params)) { return false; }
		$num_params = count($bind_params) - 1;
		if ($num_params > 0) {
			for ($i = 1; $i <= $num_params; $i++) {
				$type_index = $i - 1;
				$type = (isset($bind_params[0][$type_index])) ? ($bind_params[0][$type_index]) : (false);
				$param = (isset($bind_params[$i])) ? ($bind_params[$i]) : (false);
				if (!$type || !$param) { continue; }
				if ($type == 's' || $type == 'b') { $param = "'{$param}'"; }
				$pos = strpos($query, '?');
				if ($pos === false) { continue; }
				$query = substr_replace($query, $param, $pos, 1);
			}
		}
		return $query;
	}

}

?>
