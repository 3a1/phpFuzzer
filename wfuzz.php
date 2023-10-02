<?php

ini_set('memory_limit', '-1');

//require __DIR__ . '/list.txt';


function domain_TRIM() {
    global $target;
    $plugin = str_replace("www.", "", $target);
    $plugin = explode('.', $plugin);
    $plugin = $plugin[0];
    $plugin = str_replace("http://", "", $plugin);
    $plugin = str_replace("https://", "", $plugin);
    $plugin = str_replace(":80", "", $plugin);
    $plugin = str_replace(":443", "", $plugin);
    return $plugin;
}

function domain_FULL() {
    global $target;
    $plugin = str_replace("www.", "", $target);
    $plugin = str_replace(":80", "", $plugin);
    $plugin = str_replace(":443", "", $plugin);
    $plugin = str_replace("http://", "", $plugin);
    $plugin = str_replace("https://", "", $plugin);
    $plugin = str_replace("/", "", $plugin);
    return $plugin;
}

function domain_FULL_nondot() {
    global $target;
    $plugin = str_replace("www.", "", $target);
    $plugin = str_replace(":80", "", $plugin);
    $plugin = str_replace(":443", "", $plugin);
    $plugin = str_replace("http://", "", $plugin);
    $plugin = str_replace("https://", "", $plugin);
    $plugin = str_replace("/", "", $plugin);
    $plugin = str_replace(".", "", $plugin);
    return $plugin;
}

function curl_me($url){
    $curl = curl_init();
    if (!$curl) die("Error");
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_USERAGENT, 'WithLove');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    
    $html = curl_exec($curl);
    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    return array(
        'html' => $html,
        'code' => $statusCode
    );
}

function main() {

    $list = file('list.txt');
    $fuzz = file('fuzz.txt');

    foreach ($list as $target) {
        $target = trim($target);

        if (substr_count($target, '/') > 3) {
            $thirdSlashPos = strpos($target, '/', strpos($target, '/', strpos($target, '/') + 1) + 1);
            $target = substr($target, 0, $thirdSlashPos);
        }

        if (substr($target, -1) !== '/') {
            $target .= '/';
        }

        $domain1 = domain_FULL($target);
        $domain2 = domain_TRIM($target);
        $domain3 = domain_FULL_nondot($target);

        foreach ($fuzz as $dir) {

            $dir = trim($dir);

            //$adminer = array("url" => "/adminer.php","true" => '?file=favicon.ico');

            if (substr_count($dir, '{domain_FULL}') > 0) {
                $dir = str_replace("{domain_FULL}", $domain1, $dir);
            }

            if (substr_count($dir, '{domain_TRIM}') > 0) {
                $dir = str_replace("{domain_TRIM}", $domain2, $dir);
            }

            if (substr_count($dir, '{domain_FULLud}') > 0) {
                $dir = str_replace("{domain_FULLud}", $domain3, $dir);
            }

            $req = $target . $dir;

            try {
                if (curl_me($req)["code"] == 200) { 
                    print $req . PHP_EOL;
                }
            } catch (Exception $error) {
                echo "Error: " . $error->getMessage() . PHP_EOL;
            }
        }
    }
}

main();


    /*
    $target_sql0 = $target .'/'.$domain1.'.sql';    $target_sql0 = str_replace("//", "/", $target_sql0);
    $target_sql1 = $target .'/'.$domain2.'.sql';    $target_sql1 = str_replace("//", "/", $target_sql1);
    $target_sql2 = $target .'/'.$domain3.'.sql';    $target_sql2 = str_replace("//", "/", $target_sql2);

    $target_zip0 = $target .'/'.$domain1.'.zip';    $target_zip0 = str_replace("//", "/", $target_zip0);
    $target_zip1 = $target .'/'.$domain2.'.zip';    $target_zip1 = str_replace("//", "/", $target_zip1);
    $target_zip2 = $target .'/'.$domain3.'.zip';    $target_zip2 = str_replace("//", "/", $target_zip2);

    $adminsql = $target .'/'.'admin'.'.sql';    $target_sql0 = str_replace("//", "/", $target_sql0);
    $backupsql = $target .'/'.'backup'.'.sql';    $target_sql1 = str_replace("//", "/", $target_sql1);
    $dumpsql = $target .'/'.'dump'.'.sql';    $target_sql1 = str_replace("//", "/", $target_sql1);
  
    $adminzip = $target .'/'.'admin'.'.zip';    $target_zip0 = str_replace("//", "/", $target_zip0);
    $backupzip = $target .'/'.'backup'.'.zip';    $target_zip1 = str_replace("//", "/", $target_zip1);
    $dumpzip = $target .'/'.'dump'.'.zip';    $target_zip1 = str_replace("//", "/", $target_zip1);


    if (curl_me($target_sql0)["code"] == 200) {
        print $target_sql0 . ' sql 60%' . PHP_EOL;
    }
    else {}
    */
