<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Console;

use Boolfly\GiaoHangNhanh\Model\Service\Helper\SubjectReader;
use Boolfly\IntegrationBase\Model\Service\Command\CommandPoolInterface;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateRegionCommand
 *
 * @package Boolfly\GiaoHangNhanh\Console
 */
class GenerateRegionCommand extends Command
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * GeneratingRegionData constructor.
     * @param ResourceConnection $resourceConnection
     * @param RegionFactory $regionFactory
     * @param CommandPoolInterface $commandPool
     * @param string|null $name
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        RegionFactory $regionFactory,
        CommandPoolInterface $commandPool,
        $name = null
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->regionFactory = $regionFactory;
        $this->commandPool = $commandPool;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription('Generate region data.');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandResult = $this->commandPool->get('get_districts')->execute([]);
        $data = SubjectReader::readDistricts($commandResult->get());

        if ($data) {
            $output->writeln('<info>Generating data. Please wait...</info>');
            foreach ($data as $item) {
                $provinceId = $item['ProvinceID'];
                $districtId = $item['DistrictID'];
                $region = $this->regionFactory->create()
                    ->loadByCode($provinceId, 'VN');

                $this->insertData(
                    'boolfly_giaohangnhanh_district',
                    [
                        'district_id' => $districtId,
                        'province_id' => $provinceId,
                        'district_name' => $item['DistrictName']
                    ],
                    ['col' => 'district_id', 'val' => $districtId]
                );

                if (!$region->getId()) {
                    $this->insertData(
                        'directory_country_region',
                        [
                            'country_id' => 'VN',
                            'code' => $provinceId,
                            'default_name' => $item['ProvinceName']
                        ]
                    );
                }
            }
            $output->writeln('<info>Generate data successfully.</info>');
        } else {
            $output->writeln('<error>Generating data was interrupted. Please try again!</error>');
        }
    }

    /**
     * @param string $tableName
     * @param array $data
     * @param array $pairOfColAndVal
     */
    private function insertData($tableName, $data, $pairOfColAndVal = [])
    {
        if (!$this->checkRecordExist($tableName, $pairOfColAndVal)) {
            $this->resourceConnection->getConnection()->insert(
                $this->resourceConnection->getTableName($tableName),
                $data
            );
        }
    }


    /**
     * @param string $tableName
     * @param array $pairOfColAndVal
     * @return bool
     */
    private function checkRecordExist($tableName, $pairOfColAndVal = [])
    {
        $checkingFlag = false;

        if ($pairOfColAndVal) {
            $connection = $this->resourceConnection->getConnection();
            $sql = $connection->select()->from(
                ['districtTable' => $this->resourceConnection->getTableName($tableName)],
                $pairOfColAndVal['col']
            )->where($pairOfColAndVal['col'] . ' = ?', $pairOfColAndVal['val']);

            $rows = $connection->fetchAll($sql);

            if (count($rows)) {
                $checkingFlag = true;
            }
        }

        return $checkingFlag;
    }
}
