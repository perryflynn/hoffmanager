
This cmd script creates small random playlists of your music collection.

## Installation

You need [composer](https://getcomposer.org/) to install dependencies.

```
git clone https://github.com/perryflynn/hoffmanager.git
cd hoffmanager
php composer.phar install
```

## Configuration

Copy the `config_example.yml` to `config.yml`.

- `musicsources`: A list of all your music directories
- `cachefile`: Location of the generated cache file
- `playlist`: Location of the generated playlist
- `playlistcount`: Number of tracks in the generated playlist
- `extensions`: Valid file extensions for music files

## Basic usage

- `php -f cmd.php scan`: Refresh the cache
- `php -f cmd.php mkplaylist`: Generate a new playlist

## Use with VLC

I've created a small bash script that starts VLC after created a new playlist:

```
#!/bin/bash

# loop endless
while true; do

    # create a new playlist
    php -f ~/hoffmanager/cmd.php mkplaylist

    # open vlc with the generated playlist
    # enable http interface of vlc
    # no looping
    # no random playback
    # no repeating
    # exit after playlist playback
    # start playlist playback automatic
    vlc ~/current.m3u \
        --extraintf http \
        --http-host 127.0.0.1 \
        --http-port 8086 \
        --http-password verysecretpassword \
        --no-loop \
        --no-random \
        --no-repeat \
        --play-and-exit \
        --playlist-autostart

done
```

## Used software

- [perryflynn/PerrysLambda](https://github.com/perryflynn/PerrysLambda) for track selection and filesystem scanning
- [symfony/console](https://symfony.com/doc/master/components/console.html) for console argument parsing
- [VLC media player](https://symfony.com/doc/master/components/console.html) for m3u playlist playback
