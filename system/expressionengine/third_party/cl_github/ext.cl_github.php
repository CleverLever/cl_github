<?php
class Cl_github_ext 
{
	public $name = "Payment Fieldtype";
	public $version = "1.0.0";
	public $description = "";
	public $settings_exist = "y";
	public $docs_url = "http://cleverlever.co/addons/payment-fieldtype";
	
	private $settings;
	
	private $has_sent_email = FALSE;

	public function __construct() 
	{
		$this->EE =& get_instance();
		
		$this->EE->load->add_package_path(PATH_THIRD . 'cl_github/');
		$this->EE->load->helper('module_helper');
	}

	public function settings() { ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_github'); }
	
	/**
	 * Activate Extension
	 *
	 * Ran when the extension is installed.
	 *
	 * @return void
	 * @author Chris LeBlanc
	 */
	public function activate_extension() 
	{
		$callbacks = $this->_get_callbacks();

		foreach ( $callbacks as $method => $hook  )
		{
			$data = array('class'		=> __CLASS__,
				'method'	=> $method,
				'hook'		=> $hook,
				'settings'	=> serialize(array()),
				'priority'	=> 10,
				'version'	=> $this->version,
				'enabled'	=> 'y'
			);
			ee()->db->insert('extensions', $data);
		}
	}

	/**
	 * Disable Extension
	 *
	 * Ran when the extension is installed.
	 *
	 * @return void
	 * @author Chris LeBlanc
	 */
	public function disable_extension() 
	{
		ee()->db->delete('extensions', array('class' => __CLASS__));
	}
	

	public function entry_submission_end_callback($entry_id, $entry_metadata, $entry_data)
	{
		
	}
	
	public function entry_submission_ready_callback($entry_metadata, $entry_data, $entry_autosave) 
	{

	}
	
	/**
	 * Get Callbacks
	 * 
	 * Returns each callback and their corresponding hook.
	 *
	 * @return void
	 * @author Chris LeBlanc
	 */
	private function _get_callbacks() 
	{
		$callbacks = array();
		$methods =  get_class_methods($this);
		
		foreach ( $methods as $method )
		{
			if (cl_rstrpos($method, 'callback') !== FALSE) $callbacks[$method] = cl_rstr_replace("_callback", "", $method);
		}
		
		return $callbacks;
	}
	
}