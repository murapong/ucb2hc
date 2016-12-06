<?php
$url = 'YOUR HIPCHAT ENTPOINT';

// Get ’X-UnityCloudBuild-Event' http header
$event = $_SERVER['HTTP_X_UNITYCLOUDBUILD_EVENT'];

switch ($event) {
    case 'ProjectBuildQueued':
        $color = 'yellow';
        $title = 'Build queued';
        break;
    case 'ProjectBuildStarted':
        $color = 'yellow';
        $title = 'Build started';
        break;
    case 'ProjectBuildRestarted':
        $color = 'gray';
        $title = 'Build restarted';
        break;
    case 'ProjectBuildSuccess':
        $color = 'green';
        $title = 'Build success';
        break;
    case 'ProjectBuildFailure':
        $color = 'red';
        $title = 'Build failed';
        break;
    case 'ProjectBuildCanceled':
        $color = 'gray';
        $title = 'Build canceled';
        break;
    default:
        $color = 'red';
        $title = 'Unexpected event';
        break;
}

$json_string = file_get_contents('php://input');
$obj = json_decode($json_string);

$project_name = $obj->projectName;
$dashboard_project = $obj->links->dashboard_url->href . $obj->links->dashboard_project->href;
$build_target_name = $obj->buildTargetName;
$dashboard_log = $obj->links->dashboard_url->href . $obj->links->dashboard_log->href;
$build_number = $obj->buildNumber;
$platform = $obj->platform;
$started_by = $obj->startedBy;

// format message
$message = <<<EOM
<table>
<tr>
  <td>
    <b>$title</b><br>
    <a href="$dashboard_project">$project_name</a> - <a href="$dashboard_log">#$build_number</a><br>
  </td>
  <td>
    <b>Target</b><br>
    $build_target_name
  </td>
</tr>
<tr>
  <td>
    <b>Platform</b><br>
    $platform
  </td>
  <td>
    <b>Started By</b><br>
    $started_by
  </td>
</tr>
</table>
EOM;

$data = array(
    'color' => $color,
    "message" => $message,
    'notify' => true,
    'message_format' => 'html',
);

$content = http_build_query($data);
$options = array('http' => array(
    'method' => 'POST',
    'content' => $content
));

$contents = file_get_contents($url, false, stream_context_create($options));
