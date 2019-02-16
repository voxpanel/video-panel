<?php
$api_xml = @simplexml_load_file("http://localhost/api/".query_string('1')."");

header('Content-type: text/javascript');

print_r(json_encode($api_xml));

?>