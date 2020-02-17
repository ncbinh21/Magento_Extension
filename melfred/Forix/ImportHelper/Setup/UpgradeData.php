<?php

namespace Forix\ImportHelper\Setup;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * Class UpgradeData
 * @package Amasty\Shopby\Setup
 */
class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var \Amasty\Storelocator\Model\LocationFactory
     */
    protected $locationFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * UpgradeData constructor.
     * @param \Magento\Framework\App\State $state
     * @param \Amasty\Storelocator\Model\LocationFactory $locationFactory
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \Amasty\Storelocator\Model\LocationFactory $locationFactory
    ) {
        $this->state = $state;
        $this->locationFactory = $locationFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
            $localtions = $this->dataLocation();
            foreach ($localtions as $location) {
                $this->saveLocation($location);
            }
        }

        //update data
	    if (version_compare($context->getVersion(), '1.4.1', '<')) {

        	$data = $this->updateSchedule();
			foreach ($data as $id => $schedule) {
				$setup->getConnection()->update(
					$setup->getTable('amasty_amlocator_location'),
					[
						'schedule' => $schedule
					],
					$setup->getConnection()->quoteInto('id = ?', $id)
				);
			}
	    }
        
    }

    /**
     * @param array $locations
     */
    private function saveLocation(array $locations)
    {
        $location = $this->locationFactory->create();
        $location->addData($locations);
        $location->save();
    }

    /**
     * @return array
     */
    private function dataLocation()
    {
        return [
            [   //store 1
                'name' => 'Tarheel Contractors Supply, Inc.',
                'country' => 'AF',
                'city' => 'Rock Hill',
                'zip' => '29730',
                'address' => '162 Porter Road',
                'status' => 1,
                'lat' => 34.91394090,
                'lng' => -81.00001350,
                'position' => 0,
                'state' => 'South Carolina',
                'phone' => '803-329-9200',
                'website' => 'https://www.tcsupply.net',
                'store' => ',0,',
                'schedule' => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
                'show_schedule' => 0,
                'contact_area' => 'AL,MS,N. FL',
                'contact' => 'Harold Peeples',
                'toll_free_phone' => '800-218-0492',
                'office_phone' => '803-329-9200',
                'fax' => '803-329-9202'
            ],
            [   //store 2
                'name' => 'EGW Utility Solutions',
                'country' => 'AF',
                'city' => 'Carrollton',
                'zip' => '75006',
                'address' => '1408 Hutton Drive',
                'status' => 1,
                'lat' => 32.94840000,
                'lng' => -96.91586390,
                'position' => 0,
                'state' => 'Texas',
                'phone' => '972-323-0140',
                'email' => 'sales@tuscousa.com',
                'store' => ',0,',
                'schedule' => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
                'show_schedule' => 0,
                'contact_area' => 'N. TX,OK',
                'contact' => 'Mike Magee'
            ],
            [   //store 3
                'name' => 'Underground Supply Solutions',
                'country' => 'US',
                'city' => 'Spring',
                'zip' => '77389',
                'address' => '4223 Spring Stuebner Road',
                'status' => 1,
                'lat' => 30.08297770,
                'lng' => -95.48355740,
                'position' => 0,
                'state' => 'Texas',
                'phone' => '713-818-5112',
                'store' => ',0,',
                'schedule' => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
                'show_schedule' => 0,
                'contact_area' => 'S. TX,LA',
                'contact' => 'Derek Briggs'
            ],
            [   //store 4
                'name' => 'HDD Parts Plus',
                'country' => 'US',
                'city' => 'Aurora',
                'zip' => '80011',
                'address' => '765 Telluride Street Suite B12',
                'status' => 1,
                'lat' => 39.72761710,
                'lng' => -104.78371500,
                'position' => 0,
                'state' => 'Colorado',
                'store' => ',0,',
                'schedule' => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
                'show_schedule' => 0,
                'contact_area' => 'CO,ND,SD,NE'
            ],
            [   //store 5
                'name' => 'FS3, Inc.',
                'country' => 'US',
                'city' => 'Annandale',
                'zip' => '55302',
                'address' => '9030 64th Street NW',
                'status' => 1,
                'lat' => 45.24668800,
                'lng' => -94.07780900,
                'position' => 0,
                'state' => 'Minnesota',
                'website' => 'https://www.fs3inc.biz',
                'store' => ',0,',
                'schedule' => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
                'show_schedule' => 0,
                'contact_area' => 'MN',
                'contact' => 'Marty Ferguson',
                'office_phone' => '320-274-7223',
                'fax' => '320-274-7205'
            ],
            [   //store 6
                'name' => 'Atlantic Supply',
                'country' => 'US',
                'city' => 'Jacksonville',
                'zip' => '32256',
                'address' => '6919 Distribution Ave. South Unit #1',
                'status' => 0,
                'lat' => 30.16335590,
                'lng' => -81.53140050,
                'position' => 0,
                'state' => 'Florida',
                'email' => 'jallen@atlanticsupply.com',
                'website' => 'https://www.AtlanticSupply.com',
                'store' => ',0,',
                'schedule' => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
                'show_schedule' => 0,
                'contact_area' => 'N. FL',
                'contact' => 'James Allen',
                'toll_free_phone' => '888-260-5584'
            ],
            [   //store 7
                'name' => 'Atlantic Supply',
                'country' => 'US',
                'city' => 'Montgomery',
                'zip' => '36116',
                'address' => '5400 Perimeter Pkwy Court',
                'status' => 1,
                'lat' => 32.29937780,
                'lng' => -86.20663100,
                'position' => 0,
                'state' => 'Alabama',
                'website' => 'https://www.AtlanticSupply.com',
                'store' => ',0,',
                'schedule' => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
                'show_schedule' => 0,
                'contact_area' => 'AL,MS',
                'contact' => 'Paul Elkenberry',
                'toll_free_phone' => '866-917-3447'
            ],
            [   //store 8
                'name' => 'B & B Supply Inc.',
                'country' => 'US',
                'city' => 'Salt Lake City',
                'zip' => '84104',
                'address' => '1497 South 700 West',
                'status' => 1,
                'lat' => 40.73656530,
                'lng' => -111.91060920,
                'position' => 0,
                'state' => 'Utah',
                'phone' => '801-201-8748',
                'email' => 'pete@bbsupplyinc.com',
                'website' => 'https://www.tcsupply.net',
                'store' => ',0,',
                'schedule' => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
                'show_schedule' => 0,
                'contact_area' => 'UT,ID,AZ,NV',
                'contact' => 'Pete Brannon',
                'contact_district' => 'UT',
                'contact_two' => 'Harold Peeples',
                'contact_district_two' => 'AZ',
                'contact_phone_two' => '803-329-9200',
                'contact_email_two' => 'email_2@gmail.com',
                'toll_free_phone' => '805-201-6531',
                'office_phone' => '801-981-5254',
                'fax' => '803-329-9202'
            ],
            [   //store 9
                'name' => 'Whole Solutions',
                'country' => 'US',
                'city' => 'Mineral Ridge',
                'zip' => '44440',
                'address' => '1217 Salt Springs Road',
                'status' => 0,
                'lat' => 41.16194300,
                'lng' => -80.77455990,
                'position' => 0,
                'state' => 'Ohio',
                'store' => ',0,',
                'schedule' => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
                'show_schedule' => 0,
                'contact_area' => 'OH,WV,MI,IN,IL,WI,W. PA'
            ],
            [   //store 10
                'name' => 'UPSCO, Inc.',
                'country' => 'US',
                'city' => 'Moravia',
                'zip' => '13118',
                'address' => '67 Central Street',
                'status' => 1,
                'lat' => 42.71230000,
                'lng' => -76.42695400,
                'position' => 0,
                'state' => 'New York',
                'website' => 'https://www.upscoinc.com',
                'store' => ',0,',
                'schedule' => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
                'show_schedule' => 0,
                'contact_area' => 'ME,MD,DC,NH,VT,MA,RI,CT,NY,NJ,DE,E. PA',
                'contact' => 'Gary Lash',
                'office_phone' => '315-497-1070',
                'fax' => '315-497-0223'
            ],
            [   //store 11
                'name' => 'Blick Industrial Limited',
                'country' => 'NZ',
                'city' => 'Te, Rapa',
                'zip' => '3200',
                'address' => '21 Kahu Crescent',
                'status' => 0,
                'lat' => -37.74240040,
                'lng' => 175.22476440,
                'position' => 0,
                'state' => 'Hamilton',
                'phone' => '07 849 2366',
                'website' => 'https://www.blick.co.nz',
                'store' => ',0,',
                'schedule' => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
                'show_schedule' => 0,
                'contact_area' => 'NEW ZEALAND',
                'contact' => 'Tim Babbage',
                'office_phone' => '07 849 2366'
            ],
            [   //store 12
                'name' => 'Tecvalco West LTD',
                'country' => 'CA',
                'city' => 'North Battleford',
                'zip' => 'S9S 2X5',
                'address' => '100 Canola Ave',
                'status' => 1,
                'lat' => 52.74255620,
                'lng' => -108.25967250,
                'position' => 0,
                'state' => 'Saskatchewan',
                'website' => 'https://www.tecvalco.com',
                'store' => ',0,',
                'schedule' => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
                'show_schedule' => 0,
                'contact_area' => 'CANADA',
                'toll_free_phone' => '866-317-0131',
                'fax' => '306-445-2812'
            ],
            [   //store 13
                'name' => 'Tecvalco East LTD',
                'country' => 'CA',
                'city' => 'Niagara Falls',
                'zip' => 'L2J0E4',
                'address' => '3481 Stanley Ave.',
                'status' => 0,
                'lat' => 43.12274490,
                'lng' => -79.08732120,
                'position' => 0,
                'state' => 'Ontario',
                'website' => 'https://www.tecvalco.com',
                'store' => ',0,',
                'schedule' => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
                'show_schedule' => 0,
                'contact_area' => 'CANADA',
                'contact' => 'Chek Cheav',
                'office_phone' => '905-353-0101',
                'fax' => '905-353-8778'
            ],
            [   //store 14
                'name' => 'Heartland Construction Equipment',
                'country' => 'US',
                'city' => 'Slater',
                'zip' => '50244',
                'address' => '102 Marshall Street',
                'status' => 1,
                'lat' => 41.88348000,
                'lng' => -93.68232000,
                'position' => 0,
                'state' => 'Iowa',
                'website' => 'https://www.hceweb.com',
                'store' => ',0,',
                'schedule' => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
                'show_schedule' => 0,
                'contact_area' => 'IA',
                'office_phone' => '515-685-2818',
                'fax' => '515-685-3113'
            ],
            [   //store 15
                'name' => 'Galloway Group',
                'country' => 'US',
                'city' => 'Fort Myers',
                'zip' => '33912',
                'address' => '5840 Youngquist Road',
                'status' => 1,
                'lat' => 26.51043000,
                'lng' => -81.85638000,
                'position' => 0,
                'state' => 'Florida',
                'phone' => '239-481-7448',
                'store' => ',0,',
                'schedule' => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
                'show_schedule' => 0,
                'contact_area' => 'SOUTHERN & CENTRAL FL',
                'toll_free_phone' => '800-481-7448'
            ],
            [   //store 16
                'name' => 'Melfred Borzall',
                'country' => 'US',
                'city' => 'Santa Maria',
                'zip' => '93455',
                'address' => '2712 Airpark Drive',
                'status' => 1,
                'lat' => 34.90349000,
                'lng' => -120.45264000,
                'position' => 0,
                'state' => 'California',
                'phone' => '805-739-0118',
                'email' => 'sales@melfredborzall.com',
                'website' => 'https://www.melfredborzall.com',
                'store' => ',0,',
                'schedule' => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
                'show_schedule' => 0,
                'contact_area' => 'CA, AZ, OR, WA, MT, WY, KY, AR, HI, AK, KS, MO',
                'toll_free_phone' => '800-558-7500'
            ],
        ];
    }

    private function updateSchedule() {
    	return [
    		1 => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
		    2 => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
            3 => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
			4 => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
			5 => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
			6 => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
			7 => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
			8 => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
			9 => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
			10 => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
			11 => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
			12 => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
			13 => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
			14 => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
			15 => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
			16 => '{"monday":{"from":["00","00"],"to":["00","00"]},"tuesday":{"from":["00","00"],"to":["00","00"]},"wednesday":{"from":["00","00"],"to":["00","00"]},"thursday":{"from":["00","00"],"to":["00","00"]},"friday":{"from":["00","00"],"to":["00","00"]},"saturday":{"from":["00","00"],"to":["00","00"]},"sunday":{"from":["00","00"],"to":["00","00"]}}',
	    ];
    }

}
