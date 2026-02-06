<?php

namespace App\Entity;

use App\Repository\ForgotPasswordTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForgotPasswordTokenRepository::class)]
class ForgotPasswordToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    private ?string $username = null;

    #[ORM\Column(length: 10)]
    private ?string $otpCode = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?int $expiresInMinutes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getOtpCode(): ?string
    {
        return $this->otpCode;
    }

    public function setOtpCode(string $otpCode): static
    {
        $this->otpCode = $otpCode;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getExpiresInMinutes(): ?int
    {
        return $this->expiresInMinutes;
    }

    public function setExpiresInMinutes(int $expiresInMinutes): static
    {
        $this->expiresInMinutes = $expiresInMinutes;

        return $this;
    }
}
