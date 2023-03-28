<?php

namespace App\Entity;

use App\Repository\ShortUriRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity(repositoryClass: ShortUriRepository::class)]
class ShortUri implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $uri = null;

    #[ORM\Column]
    private ?int $uri_crc = null;

    #[ORM\Column(length: 255)]
    private ?string $short_uri = null;

    #[ORM\Column]
    private ?int $short_uri_crc = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $last_used = null;

    #[ORM\Column(nullable: true)]
    private ?int $number_used = null;

    const DEFAULT_LENGTH = 6;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function getUriCrc(): ?int
    {
        return $this->uri_crc;
    }

    public function setUriCrc(int $uri_crc): self
    {
        $this->uri_crc = $uri_crc;

        return $this;
    }

    public function getShortUri(): ?string
    {
        return $this->short_uri;
    }

    public function setShortUri(string $short_uri): self
    {
        $this->short_uri = $short_uri;

        return $this;
    }

    public function getShortUriCrc(): ?int
    {
        return $this->short_uri_crc;
    }

    public function setShortUriCrc(int $short_uri_crc): self
    {
        $this->short_uri_crc = $short_uri_crc;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getLastUsed(): ?\DateTimeInterface
    {
        return $this->last_used;
    }

    public function setLastUsed(?\DateTimeInterface $last_used): self
    {
        $this->last_used = $last_used;

        return $this;
    }

    public function getNumberUsed(): ?int
    {
        return $this->number_used;
    }

    public function setNumberUsed(?int $number_used): self
    {
        $this->number_used = $number_used;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $result = [
            'uri' => $this->uri,
            'uri_crc32' => $this->uri_crc,
            'short_uri' => $this->short_uri,
            'short_uri_crc32' => $this->short_uri_crc,
            'status' => $this->status,
        ];

        if ($this->id) {
            $result['id'] = $this->id;
        }

        return $result;
    }
}
