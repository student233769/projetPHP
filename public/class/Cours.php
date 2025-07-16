<?php

require_once 'Ressource.php';

class Cours {
    private int $id;
    private string $titre;
    private string $bloc;
    private string $section;
    private array $ressources;

    public function __construct(string $titre, string $bloc, string $section, int $id = 0) {
        $this->titre = $titre;
        $this->bloc = $bloc;
        $this->section = $section;
        $this->id = $id;
        $this->ressources = [];
    }

    public function getId(): int {
        return $this->id;
    }

    public function getTitre(): string {
        return $this->titre;
    }

    public function getBloc(): string {
        return $this->bloc;
    }

    public function getSection(): string {
        return $this->section;
    }

    public function getRessources(): array {
        return $this->ressources;
    }

    public function setTitre(string $titre): void {
        $this->titre = $titre;
    }

    public function setBloc(string $bloc): void {
        $this->bloc = $bloc;
    }

    public function setSection(string $section): void {
        $this->section = $section;
    }

    public function addRessource(Ressource $ressource): void {
        if ($ressource->getCoursId() === $this->id) {
            $this->ressources[] = $ressource;
        }
    }

    public function __toString(): string {
        return "{$this->titre} ({$this->bloc}, {$this->section})";
    }
}
