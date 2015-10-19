<?php
	//Convert log file to html
    exec ("aha -f deploy-api.log > deploy-api.html");
    $data = file_get_contents( "deploy-api.html" ); // get the contents, and echo it out.

    //If the deployment is not finished, we autoreload the page
    if (strpos($data, "Deploy finished")==false)
    {
		//Send autoreload headers
		$url1=$_SERVER['REQUEST_URI'];
		header("Refresh: 3; URL=$url1");
	}

    echo $data;
?>