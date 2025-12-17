<?php

namespace App\Entity;

use App\Repository\RouteSegmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RouteSegmentRepository::class)]
class RouteSegment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'routeSegments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Station $startStation = null;

    #[ORM\ManyToOne(inversedBy: 'routeSegments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Station $endStation = null;

    #[ORM\Column]
    private ?float $distance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartStation(): ?Station
    {
        return $this->startStation;
    }

    public function setStartStation(?Station $startStation): static
    {
        $this->startStation = $startStation;

        return $this;
    }

    public function getEndStation(): ?Station
    {
        return $this->endStation;
    }

    public function setEndStation(?Station $endStation): static
    {
        $this->endStation = $endStation;

        return $this;
    }

    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function setDistance(float $distance): static
    {
        $this->distance = $distance;

        return $this;
    }
}