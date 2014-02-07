<?php

/**
* Data Transaction / PostgreSQL Plugin
* A PostgreSQL plugin to the (data_trans) class
* @package		phpOpenFW
* @subpackage 	Database_Tools
* @author 		Christian J. Clark
* @copyright	Copyright (c) Christian J. Clark
* @license		http://www.gnu.org/licenses/gpl-2.0.txt
* @access		private
* @version 		Started: 2-1-2007 updated: 3-22-2012
*/

//***************************************************************
/**
 * dt_pgsql Class
 * @package		phpOpenFW
 * @subpackage	Database_Tools
 * @access		private
 */
//***************************************************************
class dt_pgsql extends dt_structure
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
			if (!$this->port) { $this->port = 5432; }
			$str_conn = "host=$this->server port=$this->port dbname=$this->source user=$this->user password=$this->pass";
	
			if ($this->persistent) { $this->handle = pg_pconnect($str_conn); }
	        else { $this->handle = pg_connect($str_conn); }
	
	        if (!$this->handle) {
	            $this->connection_error(pg_last_error());
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
			if ($this->handle) { return pg_close($this->handle); }
		}
		return true;
	}
	
	/**
	* Executes a query based on the data source type
	* @param mixed PostgreSQL: SQL Statement
	**/
	//*************************************************************************
	// Execute a query and store the record set
	//*************************************************************************
	public function query($query)
	{
		// Check for Open Connection
		if (!$this->is_open()) { return false; }
		$this->curr_query = $query;

        $this->rsrc_id = pg_query($this->handle, $query);
        if ($this->rsrc_id) {
            $this->num_rows = pg_num_rows($this->rsrc_id);
            while($row=pg_fetch_assoc($this->rsrc_id)){
                array_push($this->result, $row);
            }
        }

        // Last Insert ID
        if (count($this->result) == 1 && count($this->result[0]) == 1 && key($this->result[0]) == 'id') {
            $this->last_id = $this->result[0]['id'];
        }

        // Check for error
		if ($this->check_and_print_error()) { return false; }
		else { return true; }
	}

	//*************************************************************************
	/**
	* Check and Print Database Error
	**/
	//*************************************************************************
	// Check and Print Database Error
	//*************************************************************************
	private function check_and_print_error()
	{
		if ($error=pg_last_error()) {
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
			$error_msg = 'PostgreSQL Error: ' . $msg_break;
			$error_msg .= '[Error] -> ' . $error . $msg_break;
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
		return (pg_query("begin;")) ? (true) : (false);
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
	protected function _commit() { return pg_query('commit;'); }

	//*************************************************************************
	/**
	* Internal Rollback Function
	**/
	//*************************************************************************
	protected function _rollback() { return pg_query('rollback;'); }

    //*************************************************************************
	/**
	* Prepare Function
	* @param string SQL Statement
	**/
	//*************************************************************************
    public function prepare($stmt=false)
    {
    	$result = pg_prepare($this->handle, '', $stmt);
    	$this->curr_query = $stmt;

        // Error Reporting
        if ($this->check_and_print_error()) { return false; }
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
    	if (!is_array($bind_params)) {
    		$this->gen_error('Binding parameters must be passed as an array.');
    		return false;
    	}
    	$this->bind_params = $bind_params;

		// Execute Query
		$this->rsrc_id = @pg_execute($this->handle, '', $bind_params);
    	if (!$this->rsrc_id) {

	        // Check for error
	        $is_error = $this->check_and_print_error();
			if ($is_error) { return false; }

    		$this->gen_error('Query execution failed.');
    		return false;
    	}
		else{
            $this->num_rows = pg_num_rows($this->rsrc_id);
            while($row=pg_fetch_assoc($this->rsrc_id)){
                array_push($this->result, $row);
            }
        }

        // Last Insert ID
        if (count($this->result) == 1 && count($this->result[0]) == 1 && key($this->result[0]) == 'id') {
            $this->last_id = $this->result[0]['id'];
        }
        else { $this->last_id = NULL; }

        // Check for error
        $is_error = $this->check_and_print_error();
		if ($is_error) { return false; }
		else { return true; }

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
				$key = '$' . (string)($key + 1);
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
