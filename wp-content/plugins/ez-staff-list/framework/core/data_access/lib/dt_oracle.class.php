<?php

/**
* Data Transaction / Oracle Plugin
* A Oracle plugin to the (data_trans) class
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
 * dt_oracle Class
 * @package		phpOpenFW
 * @subpackage	Database_Tools
 * @access		private
 */
//***************************************************************
class dt_oracle extends dt_structure
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

			// Port
			if (!$this->port) { $this->port = 1521; }
	
			// Connection String
			if ($this->conn_str !== false) {
				$db_params = $this->conn_str;
			}
			else {
				$db_params = "(DESCRIPTION =(ADDRESS = (PROTOCOL = TCP)(HOST = {$this->server})(PORT = {$this->port}))\n";
				$db_params .= "(CONNECT_DATA = (SERVER = DEDICATED)(SERVICE_NAME = )(INSTANCE_NAME = )))";
			}
	
			// Connect
			if ($this->persistent) { $this->handle = oci_pconnect($this->user, $this->pass, $db_params); }
	        else { $this->handle = oci_connect($this->user, $this->pass, $db_params); }

	        // Check for and print error
	        if ($oracle_error=oci_error()) {
	        	$this->print_error($oracle_error);
	        	$this->handle = false;
				return false;
	        }

			// Keep track of the number of connections we create
			$this->increment_counters();
		}

		// Flag Connection as Open       
        $this->conn_open = true;

		// Start Transaction?
		if (!$this->auto_commit && !$this->trans_started) { $this->start_trans(); }

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
			if ($this->handle) { return oci_close($this->handle); }
		}
		return true;
	}

	/**
	* Executes a query based on the data source type
	* @param mixed Oracle: SQL Statement
	**/
	//*************************************************************************
	// Execute a query and store the record set
	//*************************************************************************
	public function query($query)
	{
		// Check for Open Connection
		if (!$this->is_open()) { return false; }
		$this->curr_query = $query;

		// Parse Query
		$this->rsrc_id = oci_parse($this->handle, $query);

		// Execute Query
		if (!$this->auto_commit) { @oci_execute($this->rsrc_id, OCI_DEFAULT); }
		else { @oci_execute($this->rsrc_id); }

        // Check for error
        $is_error = $this->check_and_print_error($this->rsrc_id);
		if ($is_error) { return false; }

		// Fetch Associative Record Set
        if ($this->rsrc_id && gettype($this->rsrc_id) != 'boolean') {
            $this->num_rows = oci_num_rows($this->rsrc_id);
            $this->num_fields = oci_num_fields($this->rsrc_id);
            if ($this->num_fields) {
	            while ($row = @oci_fetch_assoc($this->rsrc_id)) {
	                array_push($this->result, $row);
	            }
			}
        }

        // Last Insert ID
        $this->last_id = NULL;

        // Check for error
        $is_error = $this->check_and_print_error($this->rsrc_id);
		if ($is_error) { return false; }
		else { return true; }
	}
	
	//*************************************************************************
	/**
	* Check and Print Database Error
	**/
	//*************************************************************************
	// Check and Print Database Error
	//*************************************************************************
	private function check_and_print_error($rsrc=false)
	{
		if (!$rsrc) { return false; }
		else if ($error = oci_error($rsrc)) {
			$this->print_error($error);
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
	private function print_error($error=false)
	{
		if (!$error) { return false; }
		else {
			// Check if this is web environment or not
			$msg_break = (isset($_SERVER)) ? ("<br/>\n") : ("\n");
			$error_msg = 'Oracle Error: ' . $msg_break;
			$error_msg .= '[Code] -> ' . $error['code'] . $msg_break;
			$error_msg .= '[Message] -> ' . $error['message'] . $msg_break;
			//$error_msg .= '[SQL] -> ' . $error['sqltext'] . $msg_break;
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
		return true;
	}

    //*************************************************************************
	/**
	* Internal Auto Commit Function
	**/
	//*************************************************************************
    protected function _auto_commit($curr, $new)
    {
		if (!$curr && $new) { $this->commit(false); }
		if (!$new && !$this->trans_started) { $this->start_trans(); }
    	else { $this->trans_started = false; }
		return true;
    }

	//*************************************************************************
	/**
	* Internal Commit Function
	**/
	//*************************************************************************
	protected function _commit() { return oci_commit($this->handle); }

	//*************************************************************************
	/**
	* Internal Rollback Function
	**/
	//*************************************************************************
	protected function _rollback() { return oci_rollback($this->handle); }

    //*************************************************************************
	/**
	* Prepare Function
	* @param string SQL Statement
	**/
	//*************************************************************************
    public function prepare($stmt=false)
    {
    	if ($this->stmt) { oci_free_statement($this->stmt); }
    	$this->stmt = oci_parse($this->handle, $stmt);
    	$this->curr_query = $stmt;

        // Check for error
        $is_error = $this->check_and_print_error($this->handle);
		if ($is_error) { return false; }
		else { return true; }
    }

    //*************************************************************************
	/**
	* Execute Function
	* @param string SQL Statement
	**/
	//*************************************************************************
    public function execute($bind_params=false)
    {
    	if (!$this->stmt) { return false; }
    	if (!is_array($bind_params)) {
    		$this->gen_error('Binding parameters must be passed as an array.');
    		return false;
    	}
    	$this->bind_params = $bind_params;

		// Bind Parameters
		extract($bind_params);
		foreach ($bind_params as $key => $val) {
			oci_bind_by_name($this->stmt, $key, $$key);
		}

        // Check for error
        $is_error = $this->check_and_print_error($this->stmt);
		if ($is_error) { return false; }

		// Execute Query
		if (!$this->auto_commit) { $exec_status = @oci_execute($this->stmt, OCI_DEFAULT); }
		else { $exec_status = @oci_execute($this->stmt); }
    	if (!$exec_status) {

	        // Check for error
	        $is_error = $this->check_and_print_error($this->stmt);
			if ($is_error) { return false; }

    		$this->gen_error('Query execution failed.');
    		return false;
    	}

		// Fetch Associative Record Set
        if ($this->stmt && gettype($this->stmt) != 'boolean') {
          	$this->num_fields = oci_num_fields($this->stmt);
			if ($this->num_fields) {
	            while ($row = @oci_fetch_assoc($this->stmt)) {
	                array_push($this->result, $row);
	            }
	            $this->num_rows = count($this->result);
			}

	        // Check for error
	        $is_error = $this->check_and_print_error($this->stmt);
			if ($is_error) { return false; }
        }

        // Last Insert ID
        $this->last_id = NULL;

        // Check for error
        $is_error = $this->check_and_print_error($this->stmt);
		if ($is_error) { return false; }
		else { return true; }

    }

	//*************************************************************************
	/**
	* Shutdown function
	**/
	//*************************************************************************
	public function shutdown()
	{
		if ($this->stmt) { oci_free_statement($this->stmt); }
	}

	//*************************************************************************
	/**
	* Get Combined Query function
	**/
	//*************************************************************************
	public function get_combined_query($query, $bind_params)
	{
		if (!is_array($bind_params)) { return false; }
		$num_params = count($bind_params);
		if ($num_params > 0) {
			foreach ($bind_params as $key => $param) {
				$key = ':' . $key;
				$param = "'{$param}'";
				$pos = strpos($query, $key);
				if ($pos === false) { continue; }
				$query = substr_replace($query, $param, $pos, strlen($key));
			}
		}
		return $query;
	}
}

?>
