<?php

class Ressource {
    private int $id;
    private DateTime $dateAjout;
    private string $titre;
    private string $type;
    private ?DateTime $dateValidationAjout;
    private string $etat;
    private ?string $cheminRelatif;
    private int $coursId;
    private string $coursTitre;
    private string $personneId;
    private ?string $auteurNom = null;
    private ?string $auteurPrenom = null;

    

    public function __construct(string $titre,string $type,int $coursId,string $personneId,string $etat = 'EN_ATTENTE',?string $cheminRelatif = null,DateTime|string|null $dateValidationAjout = null,DateTime|string|null $dateAjout = null,int $id = 0) {
        $this->titre = $titre;
        $this->type = $type;
        $this->coursId = $coursId;
        $this->personneId = $personneId;
        $this->etat = $etat;
        $this->cheminRelatif = $cheminRelatif;
        $this->dateAjout = $this->parseDate($dateAjout ?? new DateTime());
        $this->dateValidationAjout = $dateValidationAjout ? $this->parseDate($dateValidationAjout) : null;
        $this->id = $id;
    }
    private function parseDate(DateTime|string $value): DateTime {
        return $value instanceof DateTime ? $value : new DateTime($value);
    }
    public function getDateAjout(): DateTime {
        return $this->dateAjout;
    }
    public function getDateValidationAjout(): ?DateTime {
        return $this->dateValidationAjout;
    }
    public function getTitre(): string { return $this->titre; }
    public function getType(): string { return $this->type; }
    public function getCoursId(): int { return $this->coursId; }
    public function getPersonneId(): string { return $this->personneId; }
    public function getEtat(): string { return $this->etat; }
    public function getEstDejaLue(): bool { return $this->estDejaLue; }
    public function getCheminRelatif(): ?string { return $this->cheminRelatif; }
    public function getId(): int { return $this->id; }
    public function getAuteurNom(): ?string { return $this->auteurNom; }
    public function getAuteurPrenom(): ?string { return $this->auteurPrenom; }
    public function getCoursTitre(): ?string { return $this->coursTitre; }
    public function setCoursTitre(string $nomCours) {$this->coursTitre = $nomCours; }
    public function setAuteurNom(string $nom): void { $this->auteurNom = $nom; }
    public function setAuteurPrenom(string $prenom): void { $this->auteurPrenom = $prenom; }
    public function setAuteurFromPersonne(Personne $p): void {
        $this->auteurNom = $p->getNom();
        $this->auteurPrenom = $p->getPrenom();
    }
    public function formatDateAjout(string $format = 'Y-m-d H:i'): string {
        return $this->dateAjout->format($format);
    }
    public function formatDateValidation(string $format = 'Y-m-d H:i'): ?string {
        return $this->dateValidationAjout ? $this->dateValidationAjout->format($format) : null;
    }
    public function __toString(): string {
        $auteur = "par {$this->auteurPrenom} {$this->auteurNom}";
        return "{$this->titre} ({$this->type}) - {$this->etat} $auteur, ajout le " . $this->formatDateAjout();
    }
}
