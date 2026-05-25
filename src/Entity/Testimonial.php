<?php

namespace App\Entity;

use App\Repository\TestimonialRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestimonialRepository::class)]
#[ORM\Table(name: 'testimonial')]
class Testimonial
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $author = '';

    #[ORM\Column(length: 255)]
    private string $role = '';

    #[ORM\Column(type: 'text')]
    private string $quote = '';

    #[ORM\Column]
    private int $rating = 5;

    #[ORM\Column]
    private bool $isFeatured = false;

    /** Position on homepage (1–3), null if not featured */
    #[ORM\Column(nullable: true)]
    private ?int $featuredPosition = null;

    public function getId(): ?int { return $this->id; }

    public function getAuthor(): string { return $this->author; }
    public function setAuthor(string $author): static { $this->author = $author; return $this; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): static { $this->role = $role; return $this; }

    public function getQuote(): string { return $this->quote; }
    public function setQuote(string $quote): static { $this->quote = $quote; return $this; }

    public function getRating(): int { return $this->rating; }
    public function setRating(int $rating): static { $this->rating = max(1, min(5, $rating)); return $this; }

    public function isFeatured(): bool { return $this->isFeatured; }
    public function setIsFeatured(bool $isFeatured): static { $this->isFeatured = $isFeatured; return $this; }

    public function getFeaturedPosition(): ?int { return $this->featuredPosition; }
    public function setFeaturedPosition(?int $featuredPosition): static { $this->featuredPosition = $featuredPosition; return $this; }
}
