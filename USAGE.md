Usage
===============

Installation
-----

Copy `/third_party/cl_github` into `/system/expressionengine/third_party`

Tags
-----

### {exp:cl_github:repo_contents_archive}

Gets the contents of a repository archive.

#### Parameters

+ owner (required)

  Repository owner.

+ repo (required)

  Repository to get archive from.

+ archive_format (default: zipball)

  Archive format to get. Either `zipball` or `tarball`.

+ ref (default: master)

  Valid Git reference (sha, tag, etc.)
