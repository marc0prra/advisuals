<?php

namespace App\Entity;

use App\Repository\SiteSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Singleton — toujours 1 seule ligne (id = 1).
 * Contient le contenu éditable de la page À propos
 * et du bloc À propos de la page d'accueil.
 */
#[ORM\Entity(repositoryClass: SiteSettingsRepository::class)]
#[ORM\Table(name: 'site_settings')]
class SiteSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* ── Page About ─────────────────────────── */

    #[ORM\Column(type: 'text')]
    private string $aboutBioIntro = '';

    #[ORM\Column(type: 'text')]
    private string $aboutBio1 = '';

    #[ORM\Column(type: 'text')]
    private string $aboutBio2 = '';

    #[ORM\Column(length: 500)]
    private string $aboutQuote = '';

    #[ORM\Column(length: 500)]
    private string $aboutPortraitPath = '';

    /* ── Bloc À propos — Accueil ─────────────── */

    #[ORM\Column(length: 255)]
    private string $homeAboutTitle = '';

    #[ORM\Column(type: 'text')]
    private string $homeAboutText1 = '';

    #[ORM\Column(type: 'text')]
    private string $homeAboutText2 = '';

    #[ORM\Column(length: 500)]
    private string $homeAboutImagePath = '';

    #[ORM\Column(length: 500)]
    private string $homeAboutQuote = '';

    public function getId(): ?int { return $this->id; }

    public function getAboutBioIntro(): string { return $this->aboutBioIntro; }
    public function setAboutBioIntro(string $v): static { $this->aboutBioIntro = $v; return $this; }

    public function getAboutBio1(): string { return $this->aboutBio1; }
    public function setAboutBio1(string $v): static { $this->aboutBio1 = $v; return $this; }

    public function getAboutBio2(): string { return $this->aboutBio2; }
    public function setAboutBio2(string $v): static { $this->aboutBio2 = $v; return $this; }

    public function getAboutQuote(): string { return $this->aboutQuote; }
    public function setAboutQuote(string $v): static { $this->aboutQuote = $v; return $this; }

    public function getAboutPortraitPath(): string { return $this->aboutPortraitPath; }
    public function setAboutPortraitPath(string $v): static { $this->aboutPortraitPath = $v; return $this; }

    public function getHomeAboutTitle(): string { return $this->homeAboutTitle; }
    public function setHomeAboutTitle(string $v): static { $this->homeAboutTitle = $v; return $this; }

    public function getHomeAboutText1(): string { return $this->homeAboutText1; }
    public function setHomeAboutText1(string $v): static { $this->homeAboutText1 = $v; return $this; }

    public function getHomeAboutText2(): string { return $this->homeAboutText2; }
    public function setHomeAboutText2(string $v): static { $this->homeAboutText2 = $v; return $this; }

    public function getHomeAboutImagePath(): string { return $this->homeAboutImagePath; }
    public function setHomeAboutImagePath(string $v): static { $this->homeAboutImagePath = $v; return $this; }

    public function getHomeAboutQuote(): string { return $this->homeAboutQuote; }
    public function setHomeAboutQuote(string $v): static { $this->homeAboutQuote = $v; return $this; }
}
