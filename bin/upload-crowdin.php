<?php
$post_params = [];
$projectIdentifier = 'opencaching';
$projectKey = getenv('crowdinApiKey');
$request_url = 'https://api.crowdin.com/api/project/'.$projectIdentifier.'/update-file?key='.$projectKey;

$post_params['files[constants.en.yml]'] = curl_file_create(__DIR__.'/../htdocs/app/Resources/translation_source/constants.en.yml');
$post_params['files[messages.en.yml]'] = curl_file_create(__DIR__.'/../htdocs/app/Resources/translation_source/messages.en.yml');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $request_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);

$result = curl_exec($ch);
curl_close($ch);

echo $result;
