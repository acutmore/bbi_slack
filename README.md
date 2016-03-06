# Bitbucket Issues - Slack Integration Server
PHP server for interacting with Bitbucket issues from Slack

### Install
`composer install`

### Setup
1. Host files on web server with php >= 5.5
2. Create new Slack commands pointing to the endpoints (files beginning with 'api_')
3. Add Slack command token and Bitbucket details to CONFIG.php

### API with examples Slack Command usage
#### api_find_issue.php
`\findissue audio bug`  - Returns the open issues containing 'audio bug' in the title

#### api_find_my_issues.php
`\myissues i am [Bitbucket username]` then `\myissues css` - Returns the open issues with css in the title assigned to that Bitbucket username.

### Extras
- An empty search will match all issue titles
- Prepending a search with '*' will return all issues, not just the open ones
- Appending 'Page X' to a search will return additional pages of results


### Acknowledgements
Uses http://gentlero.bitbucket.org/bitbucket-api/
