<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Console;

use Boolfly\GiaoHangNhanh\Api\Rest\Service\DistrictProviderInterface;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zend_Http_Client_Exception;

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
     * @var DistrictProviderInterface
     */
    private $districtProvider;

    /**
     * GeneratingRegionData constructor.
     * @param ResourceConnection $resourceConnection
     * @param RegionFactory $regionFactory
     * @param DistrictProviderInterface $districtProvider
     * @param string|null $name
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        RegionFactory $regionFactory,
        DistrictProviderInterface $districtProvider,
        $name = null
    ) {
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
     * @throws LocalizedException
     * @throws Zend_Http_Client_Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = $this->districtProvider->getDistrictList();

        if ($data) {
            $output->writeln('<info>Generating data. Please wait...</info>');
            foreach ($data as $item) {
                $provinceId = $item['ProvinceID'];
                $region = $this->regionFactory->create()
                    ->loadByCode($provinceId, 'VN');

                $this->insertData(
                    'boolfly_giaohangnhanh_district',
                    [
                        'district_id' => $item['DistrictID'],
                        'province_id' => $provinceId,
                        'district_name' => $item['DistrictName']
                    ]
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
     */
    private function insertData($tableName, $data)
    {
        $this->resourceConnection->getConnection()->insert(
            $this->resourceConnection->getTableName($tableName),
            $data
        );
    }
}
