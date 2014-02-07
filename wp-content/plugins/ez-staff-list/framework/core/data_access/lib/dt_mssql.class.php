<?php

/**
* Data Transaction / Microsoft SQL Server Plugin
* A Microsoft SQL Server plugin to the (data_trans) class
* @package		phpOpenFW
* @subpackage 	Database_Tools
* @author 		Christian J. Clark
* @copyright	Copyright (c) Christian J. Clark
* @license		http://www.gnu.org/licenses/gpl-2.0.txt
* @access		private
* @version 		Started/Copied: 7-28-2009 updated: 3-21-2012
*/

//***************************************************************
/**
 * dt_mssql Class
 * @package		phpOpenFW
 * @subpackage	Database_Tools
 * @access		private
 */
//***************************************************************
class dt_mssql extends dt_structure
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

			// Setup Connection Parameters
			$host = $this->server;
			if (!empty($this->port)) { $host .= ':' . $this->port; }
			if (!empty($this->instance)) { $host .= '\\' . $this->instance; }
	
			// Attempt Connection
	        if ($this->persistent) { $this->handle = mssql_pconnect($host, $this->user, $this->pass); }
	        else { $this->handle = mssql_connect($host, $this->user, $this->pass); }
		}

        if (!$this->handle) {
            $this->connection_error('MSSQL: There was an error connecting to the database server.');
            $this->handle = false;
            return false;
        }
        else {
			// Select Database
        	$select_db_ok = @mssql_select_db($this->source, $this->handle);

			if (!$select_db_ok) {
				$msg = "Unable to select database '$this->source'. Either the database does not exist";
				$msg .= ' or the database for this data source is configured incorrectly.';
				$this->gen_error($msg);
				mssql_close($this->handle);
				$this->handle = false;
				return false;
			}

			// Keep track of the number of connections we create
			$this->increment_counters();
		}

		// Flag Connection as Open       
        $this->conn_open = true;

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
			if ($this->handle) { return mssql_close($this->handle); }
		}
		return true;
	}

	/**
	* Executes a query based on the data source type
	* @param mixed MsSQL: SQL Statement
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
        $this->rsrc_id = mssql_query($query);
        if ($this->rsrc_id && gettype($this->rsrc_id) != 'boolean') {
            $this->num_rows = mssql_num_rows($this->rsrc_id);
            while($row=mssql_fetch_assoc($this->rsrc_id)){
                array_push($this->result, $row);
            }
        }

        // Last Insert ID
        $this->last_id = null;

        // Error Reporting
        if (!$this->rsrc_id) {
        	$msg = "There was an error performing the query [ $query ].";
        	$this->gen_error($msg);
        	return false;
        }
        return true;
	}

}

?>
