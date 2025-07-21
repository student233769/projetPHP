<?php

class Personne{
    private $matricule;
    private $mdp;
    private $nom;
    private $prenom;
    private $avatar;
    private $admin;

    function __construct($matricule = null, $mdp = null, $nom = null, $prenom= null,$avatar = '../profile_pict/buste.jpg', $admin=0)
    {
        if ($matricule != null) $this->matricule = $matricule;
        if ($mdp != null) $this->mdp = $mdp;
        if ($nom != null) $this->nom = $nom;
        if ($prenom != null) $this->prenom = $prenom;
        if ($avatar != null) $this->avatar = $avatar;
        if ($admin != null) $this->admin = $admin;

    }

    public function is_admin(){
        return $this->admin;
    }

    // Getters
    public function getMatricule() {
        return $this->matricule;
    }

    public function getMdp() {
        return $this->mdp;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getPrenom() {
        return $this->prenom;
    }

    public function getAvatar() {
        return $this->avatar;
    }

    public function getAdmin() {
        return $this->admin;
    }


}