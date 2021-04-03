<?php

namespace App\Result;

use App\Entity\Client;
use App\Entity\Recommendation;
use Assert\Assert;

class ClientSummaryResult
{
    private array $summary = [];

    /**
     * @param Client $client
     * @param Recommendation[]  $recommendations
     */
    public function __construct(private Client $client, private array $recommendations)
    {
        foreach ($recommendations as $recommendation) {
            Assert::that($recommendation)->isInstanceOf(Recommendation::class);
            Assert::that($recommendation->getClient())->eq($client);

            $this->summary[$recommendation->getStatus()] = ($this->summary[$recommendation->getStatus()] ?? 0) + 1;
        }
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return Recommendation[]
     */
    public function getRecommendations(): array
    {
        return $this->recommendations;
    }

    public function getSummary(): array
    {
        return $this->summary;
    }
}
