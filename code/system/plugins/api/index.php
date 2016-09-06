<?php

require_once 'api_plugin.php';

// init handle
SJB_Event::handle('moduleManagerCreated', array('ApiPlugin', 'init'));

// Incoming events handle
SJB_Event::handle('incomingApiCommand', array('ApiPlugin', 'apiHandler'));

