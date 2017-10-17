<?php
$postParams = [];
$projectIdentifier = 'opencaching';
$projectKey = getenv('crowdinApiKey');
$requestUrl = 'https://api.crowdin.com/api/project/' . $projectIdentifier . '/update-file?key=' . $projectKey;

$postParams['files[constants.en.yml]'] = curl_file_create(__DIR__.'/../htdocs/app/Resources/translation_source/constants.en.yml');
$postParams['files[messages.en.yml]'] = curl_file_create(__DIR__.'/../htdocs/app/Resources/translation_source/messages.en.yml');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $requestUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);

$result = curl_exec($ch);
curl_close($ch);

echo $result;
