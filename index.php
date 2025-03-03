<?php
  $ttl = 86400 * 365; //cache timeout in seconds
  $x = intval($_GET['x']);
  $y = intval($_GET['y']);
  $z = intval($_GET['z']);

  if (isset($_GET['r'])) {
    $r = strip_tags($_GET['r']);
  } else {
    $r = 'osma';
  }

  switch ($r) {
    case 'arcgis':
      $r = 'arcgis';
      break;
    case 'here':
      $r = 'here';
      break;
    case 'google':
      $r = 'google';
      break;
    case 'google_sat':
      $r = 'google_sat';
      break;
    case 'google_terrain':
      $r = 'google_terrain';
      break;
    case 'google_hybrid':
      $r = 'google_hybrid';
      break;
    case 'osma':
    default:
       $r = 'osma';
       break;
  }

  $file = "tiles/$r/$z/$x/$y.png";
  $img = null;
  $tries = 0;
  if (!is_file($file) || filemtime($file) < time()-(86400*30)) {
    do {
      $server = array();
      switch ($r) {
        case 'arcgis':
          $server[] = 'services.arcgisonline.com';
          $url = 'http://'.$server[array_rand($server)];
          $url .= "/arcgis/rest/services/World_Imagery/MapServer/tile/".$z."/".$y."/".$x;
          break;
        case 'here':
          $server[] = 'maps.hereapi.com';
          $url = 'https://'.$server[array_rand($server)];
          $url .= "/v3/base/mc/".$z."/".$x."/".$y."/png8?style=explore.day&apiKey=REPLACEWITHYOUROWNAPIKEY"; // TODO: fill in your API key and desired language
          break;
        case 'google':
          $server[] = 'mt0.google.com/vt';
          $url = 'http://'.$server[array_rand($server)];
          $url .= "/lyrs=m&hl=de&x=".$x."&y=".$y."&z=".$z;
          break;
        case 'google_sat':
          $server[] = 'mt0.google.com/vt';
          $url = 'http://'.$server[array_rand($server)];
          $url .= "/lyrs=s&hl=de&x=".$x."&y=".$y."&z=".$z;
          break;
        case 'google_hybrid':
          $server[] = 'mt0.google.com/vt';
          $url = 'http://'.$server[array_rand($server)];
          $url .= "/lyrs=y&hl=de&x=".$x."&y=".$y."&z=".$z;
          break;
        case 'google_terrain':
          $server[] = 'mt0.google.com/vt';
          $url = 'http://'.$server[array_rand($server)];
          $url .= "/lyrs=p&hl=de&x=".$x."&y=".$y."&z=".$z;
          break;
        case 'osma':
        default:
          $server[] = 'a.tile.openstreetmap.org';
          $url = 'http://'.$server[array_rand($server)];
          $url .= "/".$z."/".$x."/".$y.".png";
          break;
      }
      @mkdir(dirname($file), 0755, true);

      $options = array('http'=>array('header' => "User-Agent:MapTileProxy/1.0\r\n"));
      $ctx = stream_context_create($options);
      
      $image = file_get_contents($url,false,$ctx);

      if ($image) {
        $fp = fopen($file, "w");
        fwrite($fp, $image);
        fclose($fp);
      }

      if ($tries++ > 5)
        exit();

    } while (!$image);
  } else {
    $image = file_get_contents($file);
  }

  $exp_gmt = gmdate("D, d M Y H:i:s", time() + $ttl) ." GMT";
  $mod_gmt = gmdate("D, d M Y H:i:s", filemtime($file)) ." GMT";

  header("Expires: " . $exp_gmt);
  header("Last-Modified: " . $mod_gmt);
  header("Cache-Control: public, max-age=" . $ttl);
  header('Content-Type: image/png');

  echo $image;
