<?php
$url = getSetting('international_url', 'https://cloud.loveym.cloud');
header('Location: ' . $url);
exit;
