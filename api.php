<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$musicDir = __DIR__ . '/music';
$scriptPath = rtrim(dirname($_SERVER['PHP_SELF']), '/');

function buildUrl(string $base, array $segs): string {
    return $base . '/music/' . implode('/', array_map('rawurlencode', $segs));
}

function listDir(string $dir): array {
    if (!is_dir($dir)) return [];
    return array_values(array_filter(
        scandir($dir),
        fn($f) => $f !== '.' && $f !== '..' && strpos($f, '.') !== 0
    ));
}

if (!is_dir($musicDir)) mkdir($musicDir, 0755, true);

$library = ['artists' => []];
$imageExts = ['jpg','jpeg','png','gif','webp'];
$coverNames = ['cover','folder','front','album','artwork'];

foreach (array_filter(listDir($musicDir), fn($d) => is_dir("$musicDir/$d")) as $artist) {
    $artistDir = "$musicDir/$artist";
    $artistData = ['name' => $artist, 'albums' => []];

    foreach (array_filter(listDir($artistDir), fn($d) => is_dir("$artistDir/$d")) as $album) {
        $albumDir = "$artistDir/$album";
        $albumData = ['name' => $album, 'artist' => $artist, 'cover' => null, 'tracks' => []];
        $allFiles = listDir($albumDir);

        // Find cover: prefer named files, fallback to any image
        foreach ($allFiles as $f) {
            $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
            $base = strtolower(pathinfo($f, PATHINFO_FILENAME));
            if (in_array($ext, $imageExts) && in_array($base, $coverNames)) {
                $albumData['cover'] = buildUrl($scriptPath, [$artist, $album, $f]);
                break;
            }
        }
        if (!$albumData['cover']) {
            foreach ($allFiles as $f) {
                if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $imageExts)) {
                    $albumData['cover'] = buildUrl($scriptPath, [$artist, $album, $f]);
                    break;
                }
            }
        }

        $mp3s = array_filter($allFiles, fn($f) => strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'mp3');
        natcasesort($mp3s);

        foreach (array_values($mp3s) as $i => $f) {
            $title = pathinfo($f, PATHINFO_FILENAME);
            $title = preg_replace('/^[\d]+[\s\.\-_]+/', '', $title);
            $title = trim($title) ?: pathinfo($f, PATHINFO_FILENAME);

            $albumData['tracks'][] = [
                'id'       => md5("$artist/$album/$f"),
                'title'    => $title,
                'filename' => $f,
                'artist'   => $artist,
                'album'    => $album,
                'cover'    => $albumData['cover'],
                'url'      => buildUrl($scriptPath, [$artist, $album, $f]),
                'trackNum' => $i + 1,
            ];
        }

        if ($albumData['tracks']) $artistData['albums'][] = $albumData;
    }

    if ($artistData['albums']) $library['artists'][] = $artistData;
}

usort($library['artists'], fn($a,$b) => strcasecmp($a['name'],$b['name']));
foreach ($library['artists'] as &$a) usort($a['albums'], fn($x,$y) => strcasecmp($x['name'],$y['name']));

echo json_encode($library, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
