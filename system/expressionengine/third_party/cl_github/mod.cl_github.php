<?php
class Cl_github
{
	protected $client;
	
	public function __construct() 
	{
		$this->EE =& get_instance();
		$this->EE->load->model('Cl_github_settings_model');
		$this->EE->load->helper('module');
		
		require_once 'libraries/php-github-api/vendor/autoload.php';
		$this->client = new Github\Client();
		$this->client->authenticate(
			$this->EE->Cl_github_settings_model->get('api_access_token'), 
			'',
			Github\Client::AUTH_URL_TOKEN);
	}
	
	public function repo_contents_archive() 
	{
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$archive_format = $this->EE->TMPL->fetch_param('archive_format', 'zipball');
		$ref = $this->EE->TMPL->fetch_param('ref');
		$comparison_trigger = $this->EE->TMPL->fetch_param('comparison_trigger');

		$archive_extension = ($archive_format == "zipball") ? '.zip' : '.tar.gz';
		
		$response = $this->client->api('repo')->contents()->archive($owner, $repo, $archive_format, $ref);

		$this->EE->output->set_header('Content-Type: application/octet-stream');
		$this->EE->output->set_header('Content-Disposition: attachment; filename="' . $repo . '-' . $ref . $archive_extension . '"');
		$this->EE->output->set_header('Content-Length: ' . strlen($response));
		$this->EE->TMPL->template_type = 'cp_asset';

		return $response;
	}
	
	public function issues() 
	{
	    // fetch tag parameters
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$state = $this->EE->TMPL->fetch_param('state');
		$tag_prefix = $this->EE->TMPL->fetch_param('tag_prefix');
		$milestone_number = $this->EE->TMPL->fetch_param('milestone_number', FALSE);
		$labels = $this->EE->TMPL->fetch_param('labels', FALSE);
		
		// make options array
		$options = array();
		if ($state) $options['state'] = $state;
		if ($milestone_number) $options['milestone_number'] = $milestone_number;
		if ($labels) $options['labels'] = $labels;
	    
	    // call the api
		$data = $this->client->api('issue')->all($owner, $repo, $options);
		
		if (empty($data)) return $this->EE->TMPL->no_results();

		$data = array_map("cl_convert_hashmaps_to_sequences", $data);
		$data = cl_prefix_array_keys($tag_prefix, $data);

		return $this->EE->TMPL->parse_variables(
			$this->EE->TMPL->tagdata, 
			$data
		);
	}
	
	public function milestones() 
	{
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$state = $this->EE->TMPL->fetch_param('state');
		$tag_prefix = $this->EE->TMPL->fetch_param('tag_prefix');

		$data = $this->client->api('issue')->milestones()->all($owner, $repo, array('state' => $state));
		
		if (empty($data)) return $this->EE->TMPL->no_results();

		$data = array_map("cl_convert_hashmaps_to_sequences", $data);
		$data = cl_prefix_array_keys($tag_prefix, $data);

		return $this->EE->TMPL->parse_variables(
			$this->EE->TMPL->tagdata, 
			$data
		);
	}

	public function issue_comments() 
	{
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$tag_prefix = $this->EE->TMPL->fetch_param('tag_prefix');
		$number = $this->EE->TMPL->fetch_param('number');

		$data = $this->client->api('issue')->comments()->all($owner, $repo, $number);
		
		if (empty($data)) return $this->EE->TMPL->no_results();

		$data = array_map("cl_convert_hashmaps_to_sequencess", $data);
		$data = cl_prefix_array_keys($tag_prefix, $data);
		
		return $this->EE->TMPL->parse_variables(
			$this->EE->TMPL->tagdata, 
			$data
		);
	}
	
	public function issue_events() 
	{
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$tag_prefix = $this->EE->TMPL->fetch_param('tag_prefix');
		$number = $this->EE->TMPL->fetch_param('number');

		$data = $this->client->api('issue')->events()->all($owner, $repo, $number);
		
		if (empty($data)) return $this->EE->TMPL->no_results();

		$data = array_map("cl_convert_hashmaps_to_sequences", $data);
		$data = cl_prefix_array_keys($tag_prefix, $data);
		
		return $this->EE->TMPL->parse_variables(
			$this->EE->TMPL->tagdata, 
			$data
		);
	}
	
	public function commit() 
	{
		$commit['sha'] = '';
		$commit['commit']['message'] = '';
		
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$tag_prefix = $this->EE->TMPL->fetch_param('tag_prefix');
		$sha = $this->EE->TMPL->fetch_param('sha');
		
		$data = $this->client->api('repo')->commits()->show($owner, $repo, $sha);
		
		if (empty($data)) return $this->EE->TMPL->no_results();

		$data = cl_convert_hashmaps_to_sequences($data);	
		$data = cl_prefix_array_keys($tag_prefix, $data);
	
		return $this->EE->TMPL->parse_variables(
			$this->EE->TMPL->tagdata, 
			array($data)
		);
	}
	
	public function contents()
	{
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$tag_prefix = $this->EE->TMPL->fetch_param('tag_prefix');
		$path = $this->EE->TMPL->fetch_param('path');
		$encode_ee_tags = $this->EE->TMPL->fetch_param('encode_ee_tags', 'yes');
		
		$contents = $this->client->api('repo')->contents()->show($owner, $repo, $path);
		
		$data = cl_prefix_array_keys($tag_prefix, $contents);
		$data[$tag_prefix.'content'] = $this->EE->functions->encode_ee_tags(base64_decode($data[$tag_prefix.'content']));
		if ($encode_ee_tags == "yes") $data[$tag_prefix.'content'] = $this->EE->functions->encode_ee_tags($data[$tag_prefix.'content']);

		
		if (empty($contents)) return $this->EE->TMPL->no_results();
		
		return $this->EE->TMPL->parse_variables(
			$this->EE->TMPL->tagdata, 
			array($data)
		);
	}
	
	public function repository() 
	{
		$owner = $this->EE->TMPL->fetch_param('owner');
		$repo = $this->EE->TMPL->fetch_param('repo');
		$tag_prefix = $this->EE->TMPL->fetch_param('tag_prefix');
		
		$data = $this->client->api('repo')->show($owner, $repo);
		
		if (empty($data)) return $this->EE->TMPL->no_results();

		$data = cl_convert_hashmaps_to_sequences($data);	
		$data = cl_prefix_array_keys($tag_prefix, $data);
	
		return $this->EE->TMPL->parse_variables(
			$this->EE->TMPL->tagdata, 
			array($data)
		);
	}
}