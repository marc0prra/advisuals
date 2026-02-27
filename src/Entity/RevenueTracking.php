<?php

namespace App\Entity;

use App\Repository\RevenueTrackingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RevenueTrackingRepository::class)]
class RevenueTracking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $estimate_id = null;

    #[ORM\Column]
    private ?float $amount_total = null;

    #[ORM\Column]
    private ?float $share_adrien = null;

    #[ORM\Column]
    private ?float $share_marco = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $payment_date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstimateId(): ?int
    {
        return $this->estimate_id;
    }

    public function setEstimateId(int $estimate_id): static
    {
        $this->estimate_id = $estimate_id;

        return $this;
    }

    public function getAmountTotal(): ?float
    {
        return $this->amount_total;
    }

    public function setAmountTotal(float $amount_total): static
    {
        $this->amount_total = $amount_total;

        return $this;
    }

    public function getShareAdrien(): ?float
    {
        return $this->share_adrien;
    }

    public function setShareAdrien(float $share_adrien): static
    {
        $this->share_adrien = $share_adrien;

        return $this;
    }

    public function getShareMarco(): ?float
    {
        return $this->share_marco;
    }

    public function setShareMarco(float $share_marco): static
    {
        $this->share_marco = $share_marco;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeImmutable
    {
        return $this->payment_date;
    }

    public function setPaymentDate(\DateTimeImmutable $payment_date): static
    {
        $this->payment_date = $payment_date;

        return $this;
    }
}
