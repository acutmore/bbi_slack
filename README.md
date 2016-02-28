# Bitbucket Issues - Slack Integration Server
PHP server for interacting with Bitbucket issues from Slack

### Install
`composer install`

### Setup
1. Host files on web server with php >= 5.5
2. Create new Slack command pointing to server.com/path/find_issue.php
3. Add Slack command token and Bitbucket details to CONFIG.php

### Example Slack Command Usage
`\findissue audio bug page 2`  - Returns the second page of results for issues containing 'audio bug' in the title

### Acknowledgements
Uses http://gentlero.bitbucket.org/bitbucket-api/
