<?php

/**
 * Load dependencies
 */

require_once __DIR__."/vendor/autoload.php";

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface as II;
use Symfony\Component\Console\Output\OutputInterface as OI;
use Symfony\Component\Yaml\Yaml;
use Hoffmanager\Scanner;
use PerrysLambda\ArrayList;
use Hoffmanager\MusicFile;


/**
 * Load config
 */

$conffile = __DIR__."/config.yml";

if(!is_file($conffile))
{
    echo "Config not found.\n";
    exit(1);
}

$conf = Yaml::parse(file_get_contents($conffile));

if(!isset($conf['hoffmanager']))
{
    echo "No valid config.\n";
    exit(1);
}

$hoffconf = $conf['hoffmanager'];
unset($conffile, $conf);


/**
 * Create application
 */

$app = new Application();

$app->register('scan')->setCode(function(II $in, OI $out) use($hoffconf)
{
    $cfile = $hoffconf['cachefile'];
    $scanner = new Scanner($hoffconf['musicsources'], $hoffconf['extensions']);

    $files = $scanner->scanall();

    $out->writeln("Scan found ".$files->length()." files.");
    $out->writeln("Saved cache as ".$cfile);

    $data = array('hoffmanager'=>array('cache'=>$files->select(function($v) { return $v->getReal(); })->where(function($v) { return !empty($v); })->toArray()));
    file_put_contents($cfile, json_encode($data));
})
->setDescription('Scan for music files and create cache');


$app->register('mkplaylist')->setCode(function(II $in, OI $out) use($hoffconf)
{
    $cfile = $hoffconf['cachefile'];

    if(!is_file($cfile))
    {
        $out->writeln('Cachefile not found.');
        exit(1);
    }

    $cachedata = json_decode(file_get_contents($cfile), true);
    $files = ArrayList::asType('\Hoffmanager\MusicFile', $cachedata['hoffmanager']['cache']);

    // Random offset in file list
    $skip = $files->count()-$hoffconf['playlistcount'];
    if($skip>0)
    {
        $skip = mt_rand(0, $skip);
    }
    else
    {
        $skip = 0;
    }

    // Sort by entropy and select x files from random offset
    $playfiles = $files
        ->order('getEntropy')->toList()
        ->skip($skip)
        ->take($hoffconf['playlistcount'])
        ->select('toString')
        ->toArray();

    $out->writeln("Took ".count($playfiles)." files");

    // save as m3u file
    $text = join("\n", $playfiles);
    file_put_contents($hoffconf['playlist'], $text);

    $out->writeln("Saved as ".$hoffconf['playlist']);
})
->setDescription('Create random playlist');


/**
 * Run app
 */

$app->run();
