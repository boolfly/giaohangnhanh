<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Console;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Boolfly\GiaoHangNhanh\Model\DistrictProvider;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateRegionCommand extends Command
{
    /**
     * @var Service
     */
    private $service;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * @var DistrictProvider
     */
    private $districtProvider;

    /**
     * GeneratingRegionData constructor.
     * @param Service $service
     * @param ResourceConnection $resourceConnection
     * @param RegionFactory $regionFactory
     * @param DistrictProvider $districtProvider
     * @param string|null $name
     */
    public function __construct(
        Service $service,
        ResourceConnection $resourceConnection,
        RegionFactory $regionFactory,
        DistrictProvider $districtProvider,
        $name = null
    ) {
        $this->service = $service;
        $this->resourceConnection = $resourceConnection;
        $this->regionFactory = $regionFactory;
        $this->districtProvider = $districtProvider;
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
     * @throws NoSuchEntityException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = $this->districtProvider->getDistrictList();
        if (true === $this->service->checkResponse($response)) {
            $output->writeln('<info>Generating data. Please wait...</info>');
            $responseObject = $response['response_object'];
            $data = empty($responseObject->data) ? null : $responseObject->data;

            if ($data && is_array($data)) {
                foreach ($data as $item) {
                    $provinceId = $item->ProvinceID;
                    $region = $this->regionFactory->create()
                        ->loadByCode($provinceId, 'VN');

                    $this->insertData(
                        'boolfly_giaohangnhanh_district',
                        [
                            'district_id' => $item->DistrictID,
                            'province_id' => $provinceId,
                            'district_name' => $item->DistrictName
                        ]
                    );

                    if (!$region->getId()) {
                        $this->insertData(
                            'directory_country_region',
                            [
                                'country_id' => 'VN',
                                'code' => $provinceId,
                                'default_name' => $item->ProvinceName
                            ]
                        );
                    }
                }
                $output->writeln('<info>Generate data successfully.</info>');
            } else {
                $output->writeln('<error>Generating data was interrupted. Please try again!</error>');
            }
        }
    }

    /**
     * @param string $tableName
     * @param array $data
     */
    private function insertData($tableName, $data)
    {
        $this->resourceConnection->getConnection()->insert(
            $this->resourceConnection->getTableName($tableName),
            $data
        );
    }
}
