<?php

namespace Forix\CatalogImport\Plugin\Model;

class ImportPlugin
{
    protected $dir;

    public function __construct(
    \Magento\Framework\Filesystem\DirectoryList $dir
    ) {
        $this->dir = $dir;
    }
    public function beforeRunSchedule(\Magento\ScheduledImportExport\Model\Import $import, \Magento\ScheduledImportExport\Model\Scheduled\Operation $operation)
    {
        if ($operation->getEntityType() ==  'ftp_sage_100_update_product_info') {
            $this->dir->getRoot();
            $result = '';
            if(isset($operation->getFileInfo()['file_path']) && isset($operation->getFileInfo()['file_name'])) {
                $result = $this->dir->getRoot() . '/' . $operation->getFileInfo()['file_path'] . '/' . $operation->getFileInfo()['file_name'];
            }
            if (file_exists($result)) {
                $fileRead = fopen($result, 'r');

                $newfile = str_replace('.csv', '_temp.csv', $result);
                $fileWrite = fopen($newfile, "a");

                $count = 0;
                while (!feof($fileRead)) {
                    $array = fgetcsv($fileRead);
                    if (is_array($array) && !empty($array)) {
                        if(!in_array('item_code' , $array) && $count == 0) {
                            $addHeader = array('item_code', 'standard_unit_price', 'total_quantity_on_hand');
                            fputcsv($fileWrite, $addHeader, ',');
                        }
                        $arrayFix = array_slice($array, 0, 3);
                        fputcsv($fileWrite, $arrayFix, ',');
                    }
                    $count++;
                }
                fclose($fileRead);
                fclose($fileWrite);
                rename($newfile, $result);
            }
        }
        if ($operation->getEntityType() ==  'melfred_create_tracking_number') {
            $this->dir->getRoot();
            $result = '';
            if(isset($operation->getFileInfo()['file_path']) && isset($operation->getFileInfo()['file_name'])) {
                $result = $this->dir->getRoot() . '/' . $operation->getFileInfo()['file_path'] . '/' . $operation->getFileInfo()['file_name'];
            }
            if (file_exists($result)) {
                $fileRead = fopen($result, 'r');

                $newfile = str_replace('.csv', '_temp.csv', $result);
                $fileWrite = fopen($newfile, "a");
                $check = true;
                while (!feof($fileRead)) {
                    $array = fgetcsv($fileRead, 0 ,"\t");
                    if (is_array($array) && isset($array[0])) {
                        if(substr_count($array[0], '|') == 2) {
                            $check = false;
                            break;
                        }
//                        $changeArray[0] = trim(preg_replace('/\s+/', '|', $array[0]));
//                        $changeArray[0] = str_replace(',', '.', $changeArray[0]);
//                        if(substr_count($changeArray[0], '|') == 2) {
//                            fputcsv($fileWrite, $changeArray);
//                        }
                        $arrayFix = array_slice($array, 0, 3);
                        fputcsv($fileWrite, $arrayFix, '|');
                    }
                }
                fclose($fileRead);
                fclose($fileWrite);
                if($check) {
                    rename($newfile, $result);
                }
                if(file_exists($newfile)) {
                    unlink($newfile);
                }
            }
        }

        if ($operation->getEntityType() ==  'advanced_pricing') {
            $this->dir->getRoot();
            $result = '';
            if(isset($operation->getFileInfo()['file_path']) && isset($operation->getFileInfo()['file_name'])) {
                $result = $this->dir->getRoot() . '/' . $operation->getFileInfo()['file_path'] . '/' . $operation->getFileInfo()['file_name'];
            }
            if (file_exists($result)) {
                $fileRead = fopen($result, 'r');

                $newfile = str_replace('.csv', '_temp.csv', $result);
                $fileWrite = fopen($newfile, "a");

                $count = 0;
                $map = [
                    'C' => 'Stocking Distributor',
                    'D' => 'Resellers Level 1',
                    'E' => 'Resellers Level 2',
                    'A' => 'General',
                    'X' => 'VIP'
                ];
                $check = true;
                while (!feof($fileRead)) {
                    $array = fgetcsv($fileRead);
                    if (is_array($array) && !empty($array)) {
                        if(!in_array('sku' , $array) && $count == 0) {
                            $addHeader = array('sku', 'tier_price_website', 'tier_price_customer_group', 'tier_price_qty', 'tier_price', 'tier_price_value_type');
                            fputcsv($fileWrite, $addHeader, ',');
                        }
                        if($array && count($array) == 5 && isset($array[1]) && isset($map[$array[1]])) {
                            $arrayCustom[0] = $array[0];
                            $arrayCustom[1] = 'base';
                            $arrayCustom[2] = $map[$array[1]];
                            $arrayCustom[3] = $array[2];
                            $arrayCustom[4] = $array[4];
                            $arrayCustom[5] = 'Fixed';
                            $arrayFix = array_slice($arrayCustom, 0, 6);
                            fputcsv($fileWrite, $arrayFix, ',');
                        }
                        if(count($array) == 6) {
                            $check = false;
                        }
                    }
                    $count++;
                }
                fclose($fileRead);
                fclose($fileWrite);
                if($check) {
                    rename($newfile, $result);
                }
                if(file_exists($newfile)) {
                    unlink($newfile);
                }
            }
        }
        return [$operation];
    }
}