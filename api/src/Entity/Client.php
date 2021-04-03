<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private ?string $phone;

    /**
     * @ORM\Column(type="text")
     */
    private int $boughtProductsCount;

    public function __construct(
        string $name,
        ?string $phone,
        int $boughtProductsCount
    )
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->boughtProductsCount = $boughtProductsCount;
    }


    public function __toString(): string
    {
        return sprintf('Client %d named "%s"', $this->id, $this->name);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getBoughtProductsCount(): int
    {
        return $this->boughtProductsCount;
    }
}
