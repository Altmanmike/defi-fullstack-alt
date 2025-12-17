<?php

namespace App\Entity;

use App\Repository\StationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StationRepository::class)]
class Station
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $longname = null;
    
    /**
     * @var Collection<int, RouteSegment>
     */
    #[ORM\OneToMany(targetEntity: RouteSegment::class, mappedBy: 'startStation')]
    private Collection $routeSegments;    

    public function __construct()
    {
        $this->routeSegments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLongname(): ?string
    {
        return $this->longname;
    }

    public function setLongname(string $longname): static
    {
        $this->longname = $longname;

        return $this;
    }
    
    /**
     * @return Collection<int, RouteSegment>
     */
    public function getRouteSegments(): Collection
    {
        return $this->routeSegments;
    }

    public function addRouteSegment(RouteSegment $routeSegment): static
    {
        if (!$this->routeSegments->contains($routeSegment)) {
            $this->routeSegments->add($routeSegment);
            $routeSegment->setStartStation($this);
        }

        return $this;
    }

    public function removeRouteSegment(RouteSegment $routeSegment): static
    {
        if ($this->routeSegments->removeElement($routeSegment)) {
            // set the owning side to null (unless already changed)
            if ($routeSegment->getStartStation() === $this) {
                $routeSegment->setStartStation(null);
            }
        }

        return $this;
    }    
}