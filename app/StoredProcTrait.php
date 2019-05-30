<?php 

namespace App;

use Illuminate\Support\Facades\Log;
use DB;

trait StoredProcTrait {

	use SPProcedures;

	protected $sp_errors = [];

	//protected $sp_declared_variables = [];

	//protected $sp_default_variable = ['return_value' => 'int'];

	protected $sp_options = [];

	protected $sp_no_count_on = false;

	protected $sp_ret_key = 'return_value';

	protected $sp_exec = '';

	protected $sp_data = [];

	protected $sp_query = [];

	protected $sp_query_string = '';
	
	/**
    *--------------------------------------------------------------------------
    * Data 
    *--------------------------------------------------------------------------
    */
	public function setProcedureData($data) {
		$this->sp_data = (array)$data;
		return $this;
	}
	public function getProcedureData() {
		return $this->sp_data;
	}
	public function resetProcedureData() {
		return $this->sp_data = [];
	}
	/**
    *--------------------------------------------------------------------------
    * Options 
    *--------------------------------------------------------------------------
    */
	public function resetProcedureOptions() {
		$this->sp_options = [];
		return $this;
	}
	public function addProcedureOption($option, $value) {
		if (is_array($option)) 
			foreach((array)$option as $key => $value)
				$this->addProcedureOption($key, $value);
		else $this->sp_options[$option] = $value;
		return $this;
	}
	public function removeProcedureOption($option) {
		if (isset($this->sp_options[$options])) unset($this->sp_options[$options]);
		return $this;
	}
	public function getProcedureOptions() {
		return $this->sp_options;
	}
	public function hasProcedureOptions() {
		return !empty($this->sp_options);
	}

	/**
    *--------------------------------------------------------------------------
    * Procedure 
    *--------------------------------------------------------------------------
    */
    public function hasProcedure($procedure) {
		return in_array($procedure, $this->getProcedures());
    }
	public function setProcedure($procedure) {
		$this->sp_exec = $procedure;
		return $this;
	}
	public function setReturnValueHolder($key) {
		$this->sp_ret_key = $key;
		return $this;
	}
	public function getProcedure() {
		return $this->sp_exec;
	}
	public function getReturnValueHolder() {
		return $this->sp_ret_key;
	}

	public function getProcedures() {
		return isset($this->sp_procedures) ? (array)$this->sp_procedures : [];
	}


	/**
    *--------------------------------------------------------------------------
    * Procedure Query holder
    *--------------------------------------------------------------------------
    */
	public function getSPQuery() {
		return $this->sp_query ?: $this->buildSP()->sp_query;
	}
	/**
	 * binds all SP query parts
	 *
	 * @param procedure
	 * @param data
	 */
	protected function buildSP($procedure=null, $data=[]) {
		if (is_array($procedure)) $this->setProcedureData($procedure);
		else if (is_string($procedure)) $this->setProcedure($procedure);
		if ($data) $this->setProcedureData($data);

		$this->_bindProcOptions()
			 ->_bindProcExecAndArguments();
		return $this;
	}

	/**
	 * @return multiple result
	 */
	public function getSPResults($debug=false) {
		$return = $this->__getSPResult(true, $debug);
		return $return ?: [];
	}

	/**
	 * @return single result
	 */
	public function getSPResult($debug=false) {
		return $this->__getSPResult(false, $debug);
	}

	/**
	 * Execute Method 
	 * 
	 * @return void
	 */
	protected function __getSPResult($multiple=false, $debug=false) {

		$this->sp_errors = [];

		try {
			$method = $multiple ? 'extractSPResults' : 'extractSPResult';
			
			// bind data here but do we set SELECT 3rd param to false ?
			$query_str = $this->__spString();
			foreach((array)$this->getProcedureData() as $i){
				$pos = strpos($query_str, '?');
				if($pos !== false){
					if($i == null)
						$query_str = substr($query_str, 0, $pos-1).'null'.substr($query_str, $pos+2, strlen($query_str));
					else
						$query_str = substr($query_str, 0, $pos).$i.substr($query_str, $pos+1, strlen($query_str));
				}
			}
			$query  = DB::Select($query_str, (array)$this->getProcedureData());
			$return = call_user_func_array([$this, $method], [$query]);
			
			$this->flushSP();

			return $return;
		} catch (Exception $e) {
			// flush sp every after query is executed otherwise succeding queries may fail
			$this->flushSP();

			// debug for development stage
			return $debug ? dd($e) : false;
		}
	}

	/**
	 * Cleans all SP query variables 
	 * 
	 * @return void
	 */
	public function flushSP() {
		$this->sp_declared_variables = $this->sp_options = $this->sp_data = $this->sp_query = [];
		$this->sp_query_string = $this->sp_exec = '';
		return $this;
	}

	/**
	 * @param refresh 
	 * @return SP query string
	 */
	public function __spString($refresh=true) {
		if (!$refresh && $this->sp_query_string)
			return $this->sp_query_string;

		$query = $this->getSPQuery();
		$string[] = array_get($query, 'options');

		foreach(['args', 'outputs'] as $key) {
			if ($part = array_get($query, $key)){
				if(is_array($part)){
					foreach($part as $k=>$i){
						$part[$k] = "'".$i."'";
					}
				}
				$adjacent[] = is_array($part) ? implode(',', $part) : $part;
			}
		}
		if (isset($adjacent)){
			$adjacent = implode(', ', $adjacent);
			$string[] = array_get($query, 'exec'). str_replace("?", $adjacent, array_get($query, 'args_body'));
		}
		else $string[] = array_get($query, 'exec');
		$string[] = array_get($query, 'return_values');
		foreach($string as $k=>$i){
			if($i === null)
				unset($string[$k]);
		}
		$to_return = $this->sp_query_string = implode('; ', $string);
		$to_return .= count($string) == 1 ? ';' : '';
		return $to_return;
	}
	/**
	 * @return Options for SP string
	 */
	protected function _bindProcOptions() {
		foreach($this->getProcedureOptions() as $option => $value) 
				$options[] = "SET $option $value";
		if (isset($options)) $this->sp_query['options'] = implode('; ', $options);
		return $this;
	}

	/**
	 * @return Exec SP string with/without params notation
	 */
	protected function _bindProcExecAndArguments() {
		$this->sp_query['exec'] = "CALL {$this->getProcedure()}";
		$this->sp_query['args_body'] = "(?)";
		if ($args = $this->getProcedureData()) 
			$this->sp_query['args'] = array_filter(explode(' ', str_repeat('? ', count($args)))); 
		return $this;
	}

	/**
    *--------------------------------------------------------------------------
    * Method use for get single return stored procedures
    *--------------------------------------------------------------------------
    *
    * @param result Query result
    */
	protected function extractSPResult($result, $log=true) {	
		if (is_array($result)) $result = reset($result);

		$result = (object)$result;
		$return_value = isset($result->return_value) ? $result->return_value 
							: (isset($result->ErrorNumber) ? $result->ErrorNumber : null);
						
        if ($return_value >= 1)
        {
        	$code = !empty($result->ErrorNumber) ? $result->ErrorNumber : $return_value;
        	$message = !empty($result->ErrorMessage) ? $result->ErrorMessage : "No message";

        	$this->spError($code, $message, $log);

            return false;
        }
        else if ($return_value == 0 || $return_value == null)
        {
            return (object)$result;
        }

        return false;
	}

	/**
    *--------------------------------------------------------------------------
    * Method use for get multiple return stored procedures
    *--------------------------------------------------------------------------
    *
    * @param result Query result
    */
	protected function extractSPResults($results, $log=true) {
		/**
		 * Return value are always on the first index
		 * So we only check for the error if not then return the whole set	
		 * as describe in the example 2.11.1 which return multiple (array)policy types 
		 */
		return $results && $this->extractSPResult($results, $log) ? $results : [];
	}

	protected function spError($code, $message, $log=true){
		$this->sp_errors[] = [$code => $message]; 

		if ($log) {
			Log::error('Failed to execute query on '.$this->getProcedure().'. Error #: '.$code.', Error message: '. $message);
		}
	}

	public function getSpErrors() {
		return $this->sp_errors;
	}

	public function getLastSpError() {
		return last($this->sp_errors);
	}	

	/**
     * We catch magic method so that we don't have conflict with Eloquent 
     *
     * @see Mode/Eloquent 
     */
    public function __get($key) {
    	if (strpos($key, 'sp') !== 0) 
        	return $this->getAttribute($key);
    }

    /**
     * We catch magic method so that we don't have conflict with Eloquent 
     *
     * @see Mode/Eloquent 
     */
    public function __set($key, $value) {
    	if (strpos($key, 'sp') !== 0){
    		$this->setAttribute($key, $value);	
    	}
    }

    /**
     * We catch magic call method to dynamically run SP methods 
     *
     * @see sp_procedures
     * @param method procedure => suffix by _first to get single results
     * @param params Data|Declarations
     */
    public function __call($method, $params=[]) {
    	// method that are suffixed by _first
    	// means a single return procedure
    	$single = substr($method, -5) === 'first';
    	// cleans method if so
    	if ($single) $method = substr($method, 0, strlen($method) - 6);
    	if ($this->hasProcedure($method)) {
    		$query = $this->setProcedure($method);

    		// first argument must be data
    		if ($data = array_shift($params)) $query->setProcedureData($data);

    		return $single ? $query->getSPResult() : $query->getSPResults();
    	}
    }
}
