<?php

namespace iutnc\deefy\audio;

class Playlist extends AudioList
{
    public ?int $id = null;


    public function addTrack(AudioTrack $track): void
    {
        if (!in_array($track, $this->tracks, true)) {
            $this->tracks[] = $track;
            $this->countTracks();
            $this->duration();
        }
    }


    public function addTracks(AudioTrack ...$tracks): void
    {
        foreach ($tracks as $track) {
            $this->addTrack($track);
        }
    }

    public function removeTrack(AudioTrack $track): void
    {
        $index = array_search($track, $this->tracks, true);
        if ($index !== false) {
            unset($this->tracks[$index]);
            $this->tracks = array_values($this->tracks);
        }

        $this->countTracks();
        $this->duration();
    }

    private function getIndexIfTrackExist(AudioTrack $track): int|false
    {
        return array_search($track, $this->tracks, true);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTracks(): array
    {
        return $this->tracks;
    }




}