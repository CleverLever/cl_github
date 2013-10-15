<?php
class Cl_github_api
{
	protected $client;
	
	public function __construct() 
	{
		$this->EE =& get_instance();
		$this->EE->load->model('Cl_github_api_settings_model');
		$this->EE->load->helper('module');
		
		require_once 'libraries/php-github-api/vendor/autoload.php';
		$this->client = new Github\Client();
		$this->client->authenticate(
			$this->EE->Cl_github_api_settings_model->get('api_access_token'), 
			'',
			Github\Client::AUTH_URL_TOKEN);
	}
	
	/**
	 * User Repos
	 * 
	 * Lists repositories for the specified user.
	 *
	 * @return void
	 * @author Chris LeBlanc
	 */
	public function user_repos()
	{
		$user = $this->EE->TMPL->fetch_param('user');
		$type = $this->EE->TMPL->fetch_param('type');
		$sort = $this->EE->TMPL->fetch_param('sort');
		$direction = $this->EE->TMPL->fetch_param('direction');
		$var_prefix = $this->EE->TMPL->fetch_param('var_prefix');
	
		// make options array
		$options = array();
		if ($type) $options['type'] = $type;
		if ($sort) $options['sort'] = $sort;
		if ($direction) $options['direction'] = $direction;

	    // call the api		
		$response = $this->client->api('user')->repositories($owner, $options);

		$data = array_map("cl_convert_hashmaps_to_sequences", $response);
		$data = cl_prefix_array_keys($var_prefix, $data);
		
		if (empty($data)) return $this->EE->TMPL->no_results();	

		return $this->EE->TMPL->parse_variables(
			$this->EE->TMPL->tagdata, 
			$data
		);
	}
	
	public function repo_contents_archive() 
	{
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$archive_format = $this->EE->TMPL->fetch_param('archive_format', 'zipball');
		$ref = $this->EE->TMPL->fetch_param('ref');

		$archive_extension = ($archive_format == "zipball") ? '.zip' : '.tar.gz';
		
		$response = $this->client->api('repo')->contents()->archive($owner, $repo, $archive_format, $ref);

		$this->EE->output->set_header('Content-Type: application/octet-stream');
		$this->EE->output->set_header('Content-Disposition: attachment; filename="' . $repo . '-' . $ref . $archive_extension . '"');
		$this->EE->output->set_header('Content-Length: ' . strlen($response));
		$this->EE->TMPL->template_type = 'cp_asset';

		return $response;
	}
	
	public function repo_downloads() 
	{
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$var_prefix = $this->EE->TMPL->fetch_param('var_prefix');

		$response = $this->client->api('repo')->downloads()->all($owner, $repo);

		$data = array_map("cl_convert_hashmaps_to_sequences", $response);
		$data = cl_prefix_array_keys($var_prefix, $data);
		
		if (empty($data)) return $this->EE->TMPL->no_results();	
		
		return $this->EE->TMPL->parse_variables(
			$this->EE->TMPL->tagdata, 
			$data
		);
	}
	
	public function repo_tags() 
	{
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$sort = ($this->EE->TMPL->fetch_param('sort') == 'asc') ? SORT_ASC : SORT_DESC;
		$limit = $this->EE->TMPL->fetch_param('limit', FALSE);
		$var_prefix = $this->EE->TMPL->fetch_param('var_prefix');

		$response = $this->client->api('repo')->tags($owner, $repo);
		
		$names = array();
		foreach ($response as $key => $row) { $names[$key] = $row['name']; }
		array_multisort($names, $sort, $response);
		
		if ($limit) { $response = array_slice($response, 0, $limit); } 

		$data = array_map("cl_convert_hashmaps_to_sequences", $response);
		$data = cl_prefix_array_keys($var_prefix, $data);
		
		if (empty($data)) return $this->EE->TMPL->no_results();	

		return $this->EE->TMPL->parse_variables(
			$this->EE->TMPL->tagdata, 
			$data
		);
	}
	
	public function repo_issues() 
	{
	    // fetch tag parameters
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$milestone = $this->EE->TMPL->fetch_param('milestone', FALSE);
		$state = $this->EE->TMPL->fetch_param('state');
		$labels = $this->EE->TMPL->fetch_param('labels', FALSE);
		$var_prefix = $this->EE->TMPL->fetch_param('var_prefix');
		
		// make options array
		$options = array();
		if ($milestone) $options['milestone'] = $milestone;
		if ($state) $options['state'] = $state;
		if ($labels) $options['labels'] = $labels;
	    
	    // call the api
		$response = $this->client->api('issue')->all($owner, $repo, $options);

		$data = array_map("cl_convert_hashmaps_to_sequences", $response);
		$data = cl_prefix_array_keys($var_prefix, $data);
		
		if (empty($data)) return $this->EE->TMPL->no_results();	

		return $this->EE->TMPL->parse_variables(
			$this->EE->TMPL->tagdata, 
			$data
		);
	}
	
	public function repo_milestones() 
	{
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$state = $this->EE->TMPL->fetch_param('state');
		$var_prefix = $this->EE->TMPL->fetch_param('var_prefix');
		$sort = $this->EE->TMPL->fetch_param('sort');
		$direction = $this->EE->TMPL->fetch_param('direction');
		$reverse = $this->EE->TMPL->fetch_param('reverse');

		$response = $this->client->api('issue')->milestones()->all($owner, $repo, array('state' => $state, 'sort' => $sort, 'direction' => $direction));
		
		foreach($response as $key => $val) { $response[$key]['due_on'] = (!empty($val['due_on'])) ? strtotime($val['due_on']) : 0; } // convert due_on to unix timestamp
		if (!empty($reverse)) rsort($response); // useful if the api sort parameters aren't enough

		$data = array_map("cl_convert_hashmaps_to_sequences", $response);
		$data = cl_prefix_array_keys($var_prefix, $data);
		
		if (empty($data)) return $this->EE->TMPL->no_results();	

		return $this->EE->TMPL->parse_variables(
			$this->EE->TMPL->tagdata, 
			$data
		);
	}

	public function repo_issue_comments() 
	{
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$var_prefix = $this->EE->TMPL->fetch_param('var_prefix');
		$issue_number = $this->EE->TMPL->fetch_param('issue_number');

		$response = $this->client->api('issue')->comments()->all($owner, $repo, $issue_number);

		$data = array_map("cl_convert_hashmaps_to_sequencess", $response);
		$data = cl_prefix_array_keys($var_prefix, $data);
		
		if (empty($data)) return $this->EE->TMPL->no_results();	
		
		return $this->EE->TMPL->parse_variables(
			$this->EE->TMPL->tagdata, 
			$data
		);
	}
	
	public function repo_issue_events() 
	{
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$issue_number = $this->EE->TMPL->fetch_param('issue_number');
		$var_prefix = $this->EE->TMPL->fetch_param('var_prefix');

		$response = $this->client->api('issue')->events()->all($owner, $repo, $issue_number);

		$data = array_map("cl_convert_hashmaps_to_sequences", $response);
		$data = cl_prefix_array_keys($var_prefix, $data);

		if (empty($data)) return $this->EE->TMPL->no_results();	

		return $this->EE->TMPL->parse_variables(
			$this->EE->TMPL->tagdata, 
			$data
		);
	}
	
	public function repo_commit() 
	{
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$sha = $this->EE->TMPL->fetch_param('sha');
		$var_prefix = $this->EE->TMPL->fetch_param('var_prefix');
		
		$response = $this->client->api('repo')->commits()->show($owner, $repo, $sha);

		$data = cl_convert_hashmaps_to_sequences($response);	
		$data = cl_prefix_array_keys($var_prefix, $data);
	
		if (empty($data)) return $this->EE->TMPL->no_results();	

		return $this->EE->TMPL->parse_variables(
			$this->EE->TMPL->tagdata, 
			array($data)
		);
	}
	
	public function repo_path_contents()
	{
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$path = $this->EE->TMPL->fetch_param('path');
		$encode_ee_tags = $this->EE->TMPL->fetch_param('encode_ee_tags');
		$var_prefix = $this->EE->TMPL->fetch_param('var_prefix');
		
		$response = $this->client->api('repo')->contents()->show($owner, $repo, $path);
		
		$data = cl_prefix_array_keys($var_prefix, $response);
		$data[$var_prefix.'content'] = $this->EE->functions->encode_ee_tags(base64_decode($data[$var_prefix.'content']));
		if (!empty($encode_ee_tags)) $data[$var_prefix.'content'] = $this->EE->functions->encode_ee_tags($data[$var_prefix.'content']);

		if (empty($data)) return $this->EE->TMPL->no_results();
		
		return $this->EE->TMPL->parse_variables(
			$this->EE->TMPL->tagdata, 
			array($data)
		);
	}
	
	public function repo() 
	{
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$var_prefix = $this->EE->TMPL->fetch_param('var_prefix');
		
		$response = $this->client->api('repo')->show($owner, $repo);

		$data = cl_convert_hashmaps_to_sequences($response);	
		$data = cl_prefix_array_keys($var_prefix, $data);
		
		if (empty($data)) return $this->EE->TMPL->no_results();
	
		return $this->EE->TMPL->parse_variables(
			$this->EE->TMPL->tagdata, 
			array($data)
		);
	}
}