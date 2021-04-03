<?php

namespace App\Command;

use App\Entity\Client;
use App\Entity\Recommendation;
use App\Entity\RecommendationStatuses;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FillRecommendationsCommand extends Command
{
    protected static $defaultName = 'app:fill-recommendations';
    private const FILE_PATH = '/var/www/data/recommendations.xls';

    private const STATUSES_BY_COLOR = [
        Color::COLOR_RED => RecommendationStatuses::OUT_OF_RANGE,
        'FF00B050' => RecommendationStatuses::IN_DEMAND,
        'FF008000' => RecommendationStatuses::IN_DEMAND,
        Color::COLOR_YELLOW => RecommendationStatuses::PRICE_NOT_FIT,
        'FF00B0F0' => RecommendationStatuses::GUESSED,
        'FF00CCFF' => RecommendationStatuses::GUESSED,
        'FFFF6600' => RecommendationStatuses::ANALOGS_IN_DEMAND,
    ];

    private Connection $connection;
    private EntityManagerInterface $entityManager;

    public function __construct(
        Connection $connection,
        EntityManagerInterface $entityManager,
        string $name = null
    )
    {
        parent::__construct($name);
        $this->connection = $connection;
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->entityManager->transactional(function() {
            $this->handle();
        });

        return Command::SUCCESS;
    }

    private function handle(): void
    {
        $reader = new Xls();
        $spreadsheet = $reader->load(self::FILE_PATH);
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getRowDimensions() as $row) {
            if ($row->getRowIndex() === 1) {
                continue;
            }

            $clientName = $sheet->getCell(sprintf('A%d', $row->getRowIndex()))->getValue();
            $bought = (int) $sheet->getCell(sprintf('B%d', $row->getRowIndex()))->getValue();
            $client = new Client($clientName, null, $bought);
            $this->entityManager->persist($client);

            $sheet->getColumnIterator()->resetStart();
            $iterator = $sheet->getColumnIterator('C', 'V');
            while ($iterator->valid()) {
                $column = $iterator->current();
                $coordinate = sprintf('%s%d', $column->getColumnIndex(), $row->getRowIndex());
                $product = $sheet->getCell($coordinate)->getValue();
                $color = $sheet->getStyle($coordinate)->getFill()->getStartColor()->getARGB();
                $status = self::STATUSES_BY_COLOR[$color] ?? RecommendationStatuses::NONE;
                $this->entityManager->persist(new Recommendation($product, $status, $client));
                $iterator->next();
            }
        }
    }
}
