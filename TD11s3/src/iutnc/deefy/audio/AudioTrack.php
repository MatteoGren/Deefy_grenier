<?php
namespace iutnc\deefy\audio;

class AudioTrack
{
    public function __construct(protected string $title, protected int $duration = 0)
    {
    }

    public function __get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        throw new \iutnc\deefy\exception\InvalidPropertyNameException("Unknown property $name");
    }

    // getter explicite pour la durée
    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
