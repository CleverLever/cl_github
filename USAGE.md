Usage
===============

Installation
-----

Copy `/third_party/cl_github` into `/system/expressionengine/third_party`.

Configuration
-----

You'll need to obtain a personal access token (for your user, not organization) [here](https://github.com/settings/applications). 
You can then add this to the configuration under `Add-ons -> GitHub -> Global Settings`.

Tags
-----

### {exp:cl_github:user_repos}

Lists repositories for the specified user.

#### Parameters

+ user (required)

  Repository user.

+ type (default: `all`)

  Type of repository to get. `all`, `owner`, or `member`.

+ sort (default: `full_name`)

  The attribute in which to sort. `created`, `updated`, `pushed`, or `full_name`

+ direction (default: when using `full_name`: `asc`, otherwise `desc`)

  Sort direction. `asc` or `desc`.

+ var_prefix

  Allows you to prefix the variables below to prevent name collisions. i.e. `downloads:` would produce the variable `{downloads:name}`

#### Variables

This tag allows full access to the JSON response from the API [located here](http://developer.github.com/v3/repos/#response).

### {exp:cl_github:repo_contents_archive}

Outputs the repo contents archive as a full http response.

#### Parameters

+ owner (required)

  Repository owner.

+ repo (required)

  Repository to get archive from.

+ archive_format (default: `zipball`)

  Archive format to get. Either `zipball` or `tarball`.

+ ref (default: `master`)

  Valid Git reference (sha, tag, etc.)

### {exp:cl_github:repo_downloads}

Lists package downloads available for a repository.

#### Parameters

+ owner (required)

  Repository owner.

+ repo (required)

  Repository.

+ var_prefix

  Allows you to prefix the variables below to prevent name collisions. i.e. `downloads:` would produce the variable `{downloads:name}`

#### Variables

This tag allows full access to the JSON response from the API [located here](http://developer.github.com/v3/repos/downloads/#response) as EE template tags.

### {exp:cl_github:repo_tags}

Lists tags for a given repository.

#### Parameters

+ owner (required)

  Repository owner.

+ repo (required)

  Repository.

+ sort (default: `asc`)

  Sorts the tag by it's name. Either `asc` or `desc`.

+ limit

  Limits the number of tags displayed.

+ var_prefix

  Allows you to prefix the variables below to prevent name collisions. i.e. `item:` would produce the variable `{item:name}`

#### Variables

This tag allows full access to the JSON response from the API [located here](http://developer.github.com/v3/repos/#response-8) as EE template tags.

### {exp:cl_github:repo_issues}

Lists issues for a given repo.

#### Parameters

+ owner (required)

  Repository owner.

+ repo (required)

  Repository.

+ milestone

  Either milestone number, `none` or `*`.

+ state (default: `open`)

  Issue's state. Either `open` or `closed`.

+ labels

  Shows only issues including one or more labels from a comma separated list of label names. i.e. `bug, enhancement`.

+ var_prefix

  Allows you to prefix the variables below to prevent name collisions. i.e. `item:` would produce the variable `{item:name}`

#### Variables

This tag allows full access to the JSON response from the API [located here](http://developer.github.com/v3/issues/#response-1) as EE template tags.

### {exp:cl_github:repo_milestones}

Lists milestones for a given repo.

#### Parameters

+ owner (required)

  Repository owner.

+ repo (required)

  Repository.

+ state (default: `open`)

  Milestone's state. Either `open` or `closed`.

+ sort (default: `due_date`)

  The attribute in which to sort. Either `due_date` or `completeness`.

+ direction (default: `desc`)

  Sort direction. `asc` or `desc`

+ reverse

  Reverse the results. This is useful if issues have identical due dates or completeness. My experience shows GitHub then sorts by milestone creation date.

+ var_prefix

  Allows you to prefix the variables below to prevent name collisions. i.e. `item:` would produce the variable `{item:name}`

#### Variables

This tag allows full access to the JSON response from the API [located here](http://developer.github.com/v3/issues/#response-1) as EE template tags.

### {exp:cl_github:repo_issue_comments}

Lists comments for a given issue.

#### Parameters

+ owner (required)

  Repository owner.

+ repo (required)

  Repository.

+ issue_number (required)

  Issue number.

+ var_prefix

  Allows you to prefix the variables below to prevent name collisions. i.e. `item:` would produce the variable `{item:name}`

#### Variables

This tag allows full access to the JSON response from the API [located here](http://developer.github.com/v3/issues/comments/#response) as EE template tags.

### {exp:cl_github:repo_commit}

Returns information about a specific commit.

#### Parameters

+ owner (required)

  Repository owner.

+ repo (required)

  Repository.

+ sha (required)

  SHA.

+ var_prefix

  Allows you to prefix the variables below to prevent name collisions. i.e. `item:` would produce the variable `{item:name}`

#### Variables

This tag allows full access to the JSON response from the API [located here](http://developer.github.com/v3/git/commits/#response) as EE template tags.


### {exp:cl_github:repo_path_contents}

Get information and contents of a file or directory.

#### Parameters

+ owner (required)

  Repository owner.

+ repo (required)

  Repository.

+ path (required)

  Path to a file or directory.

+ encode_ee_tags

  Encodes expression engine tags so they are not parsed. Either `yes` or `no`.

+ var_prefix

  Allows you to prefix the variables below to prevent name collisions. i.e. `item:` would produce the variable `{item:name}`

#### Variables

This tag allows full access to the JSON response from the API [located here](http://developer.github.com/v3/git/contents/#response-if-content-is-a-file) as EE template tags.

### {exp:cl_github:repo}

Get information about a specific repository.

#### Parameters

+ owner (required)

  Repository owner.

+ repo (required)

  Repository.

+ var_prefix

  Allows you to prefix the variables below to prevent name collisions. i.e. `item:` would produce the variable `{item:name}`

#### Variables

This tag allows full access to the JSON response from the API [located here](http://developer.github.com/v3/repos/#response-3) as EE template tags.

