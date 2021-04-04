<?php

namespace App\Entity;

use App\Repository\RecommendationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecommendationRepository::class)
 * @ORM\Table(
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="recommendation_unique",
 *            columns={"client_id", "product"})
 *    }
 * )
 */
class Recommendation
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
    private string $product;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private string $status;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Client $client;


    public function __construct(
        string $product,
        string $status,
        Client $client
    )
    {
        RecommendationStatuses::validate($status);

        $this->product = $product;
        $this->status  = $status;
        $this->client  = $client;
    }

    public function update(string $status): void
    {
        RecommendationStatuses::validate($status);

        $this->status = $status;
    }

    public function __toString(): string
    {
        return sprintf(
            'Recommendation #%d for client #%d of product "%s"',
            $this->id,
            $this->client->getId(),
            $this->product
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProduct(): string
    {
        return $this->product;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
