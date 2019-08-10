<?php


$platforms = [1,2,3,4];
$doc = file_get_contents("input.log");
$users = explode("\n", $doc);
$resultArr = [];
$collectiblesTrueState = [ 0,1,2,4,8,16,32,64]; 

$data;

foreach ($users  as $name) {
    $user = [];
    $user['name'] = $name;

    foreach ($platforms as $platform){
        $url = "https://www.bungie.net/Platform/Destiny2/SearchDestinyPlayer/$platform/".rawurlencode($user['name'])."/";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json, text/plain, */*',
            'Referer: https://www.d2checklist.com/4/Mcthump%2311890/collections/tree/1528930164',
            'Sec-Fetch-Mode: cors',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36',
            'X-API-Key: a313d9ebfcd741688d76ab8e9549f322',
        ));

        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result,true);

        if($result['Response'][0]['membershipId']) {
            $user['id'] = $result['Response'][0]['membershipId'];
            $user['platform'] = $platform;
            break;
        }
    }


    $url = "https://www.bungie.net/Platform/Destiny2/".$user['platform']."/Profile/".$user['id']."/?components=Collectibles";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  FALSE);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json, text/plain, */*',
        'Referer: https://www.d2checklist.com/4/Mcthump%2311890/collections/tree/1528930164',
        'Sec-Fetch-Mode: cors',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36',
        'X-API-Key: a313d9ebfcd741688d76ab8e9549f322',
    ));


    $result = curl_exec($ch);
    curl_close($ch);
    print_r($result);
    if(preg_match('#\"3260604717\"\:\{\"state\"\:(\d+)\}#usi', $result,$m)){
        if(in_array($m[1], $collectiblesTrueState))
            $user['Not Forgotten'] = 'has';
        else
            $user['Not Forgotten'] = 'no';
    } else
        $user['Not Forgotten'] = 'no';
    $data .= $user['name']." ".$user['Not Forgotten'].PHP_EOL;
    $resultArr[] = $user;
}

file_put_contents('output.log', $data);
