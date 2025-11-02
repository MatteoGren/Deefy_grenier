<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\AudioList;
use iutnc\deefy\audio\PodcastTrack;

class AudioListRenderer implements RenderInterface
{
    public function __construct(private AudioList $audioList)
    {
    }

    public function render(int $selector = RenderInterface::COMPACT): string
    {
        $html = "<h2>Playlist : {$this->audioList->getName()}</h2>";
        $html .= "<p>Nombre de pistes : {$this->audioList->getTrackCount()} | Durée totale : {$this->audioList->getDuration()} s</p>";

        $html .= "<ul>";

        foreach ($this->audioList->getTracks() as $track) {
            if ($track instanceof \iutnc\deefy\audio\PodcastTrack) {
                $html .= "<li><strong>{$track->title}</strong> — {$track->author} ({$track->duration} s)</li>";
            } else {
                $html .= "<li><strong>{$track->title}</strong> ({$track->duration} s)</li>";
            }
        }


        $html .= "</ul>";

        return $html;
    }
}
