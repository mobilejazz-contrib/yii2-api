<?php

	include "config.php";

	$deploy_script = '/app/deploy/deploy-api ' . $env;
	$log_file = '/app/deploy/deploy-api.log';
	$pid_file = '/app/deploy/deploy-api.pid';

	$update = false;

	// Parse data from Bitbucket hook payload
	$payload = null;
	if (isset($_POST))
	{
		if (isset($_POST['payload']))
			$payload = json_decode ($_POST['payload']);
		else
			$payload = json_decode ($_POST);
	}

	if (is_null ($payload) || empty($payload->push)  || empty($payload->push->changes) || empty($payload->push->changes->new))
	{
		// When merging and pushing to bitbucket, the commits array will be empty.
		// In this case there is no way to know what branch was pushed to, so we will do an update.
		$update = true;
	}
	else
	{
        $branch = $payload->push->changes->new->name;
		if ($branch === $repo_branch)
		{
			$update = true;
		}
	}

	if ($update)
	{
		echo " Deploy start at: " . date ('m/d/Y h:i:s a');
		// execute deploy script
		$cmd = 'sudo ' . $deploy_script;
		exec (sprintf ("%s > %s 2>&1 & echo $! >> %s", $cmd, $log_file, $pid_file));
	}
?>
