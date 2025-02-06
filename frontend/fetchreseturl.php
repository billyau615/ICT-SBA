<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch Reset Password URL</title>
</head>
<?php
// Fetch the datetime and url from python script
$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query([]),
    ],
];
$context  = stream_context_create($options);
$reseturl = file_get_contents('http://localhost:5000/api/obtaintempdata', false, $context);
$reseturl = json_decode($reseturl, true);
$datetime = $reseturl['datetime'];
$url = $reseturl['url'];
$status = $reseturl['status'];
?>
<body>
    <h1>Fetch Reset Password URL</h1>
    <h2>Refresh the page to fetch the reset password URL.</h2>
    <button onclick="window.location.href='fetchreseturl.php'">Refresh</button>
    <p>Datetime: <?php echo $datetime; ?></p>
    <p>URL: <a href='<?php echo $url; ?>'><?php echo $url; ?></a></p>
    <p>Status: <?php echo $status; ?></p>
</body>
</html>