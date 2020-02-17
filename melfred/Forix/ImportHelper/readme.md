**Update Product Data:** `forix:import --entity=update_product --behavior=append --file=product_update_box_10_04_2017.csv`

A - CUSTOMIZE CSV:

1. In Excel, clear CSV header to match attribute field, using this formula:

=SUBSTITUTE(TRIM(SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(LOWER(IF(LEFT(A1,1)="_",MID(A1,2,LEN(A1)),A1)),"_ø",""),"@",""),"_§",""),"_-",""),"/","_"), "]",""),"[",""),"(",""),")",""),"recommended_application_",""),"shrink_tubing_",""),"=",""),"a²s",""),"accessory breaking capacity ",""),"i²t-value",""),"  "," "),"-",""))," ","_")

2. Add column to CSV: child_products_sku, used_attribute, is_in_stock, attribute_set (before category) 

3. Clone image column to gallery

4. Save as CSV.

5. Copy to CSV file to server in var/importexport

6. CLEAR CACHE:

B - IMPORT PROCESS

7. Run import helpper command:
php bin/magento forix:process --func=import --file=CSV_filename


9. Custom export fields: open Forix/ImportHelper/Model/Export/Product/AbstractType.php ; edit left side of $requestNew variable to match with new site product attributes name.
For example: in CSV is master_size so array will be ['size' => 'master_size'], OR
"categories"=>"category"

10. Run Export command:
php bin/magento forix:process --func=export

11. We have 2 export files in var/importexport folder. The file product_xxxxxx.csv is simple products. The file configurable_xxxxxx.csv is configurable products.

12. Go to BE, System -> MageBees -> Import Products; then import simple product first. After that import Configurable products.
---------------------------------- Process Files
bin/magento forix:process --func=import --file="Adapters.csv"
bin/magento forix:process --func=import --file="Bits & Blades.csv"
bin/magento forix:process --func=import --file="Drive Chucks & Sub-Savers.csv"
bin/magento forix:process --func=import --file="Fastback.csv"
bin/magento forix:process --func=import --file="Jaws.csv"
bin/magento forix:process --func=import --file="Locators.csv"
bin/magento forix:process --func=import --file="Pullers.csv"
bin/magento forix:process --func=import --file="Quick Disconnect & Collars.csv"
bin/magento forix:process --func=import --file="Reamers.csv"
bin/magento forix:process --func=import --file="Replacement Parts & Accessories.csv"
bin/magento forix:process --func=import --file="Swivels.csv"
bin/magento forix:process --func=import --file="Transmitter Housings.csv"


----------------------------------- Import Command line
* bin/magento forix:import --entity=melfred_catalog_product  --based=catalog_product --behavior=append --file="catalog_product_Adapters_2018-11-27.csv"
bin/magento forix:import --entity=melfred_catalog_product  --based=catalog_product --behavior=append --file="catalog_product_Reamers_2018-11-27.csv"
bin/magento forix:import --entity=melfred_catalog_product  --based=catalog_product --behavior=append --file="catalog_product_Locators_2018-11-27.csv"
bin/magento forix:import --entity=melfred_catalog_product  --based=catalog_product --behavior=append --file="catalog_product_Jaws_2018-11-27.csv"
bin/magento forix:import --entity=melfred_catalog_product  --based=catalog_product --behavior=append --file="catalog_product_TransmitterHousings_2018-11-27.csv"
bin/magento forix:import --entity=melfred_catalog_product  --based=catalog_product --behavior=append --file="catalog_product_DriveChucks_2018-11-27.csv"
bin/magento forix:import --entity=melfred_catalog_product  --based=catalog_product --behavior=append --file="catalog_product_Pullers_2018-11-27.csv"
bin/magento forix:import --entity=melfred_catalog_product  --based=catalog_product --behavior=append --file="catalog_product_Replacement_2018-11-27.csv"
bin/magento forix:import --entity=melfred_catalog_product  --based=catalog_product --behavior=append --file="catalog_product_BitBlade_2018-11-30.csv"
bin/magento forix:import --entity=melfred_catalog_product  --based=catalog_product --behavior=append --file="catalog_product_Swivels_2018-11-27.csv"
bin/magento forix:import --entity=melfred_catalog_product  --based=catalog_product --behavior=append --file="catalog_product_QuickDisconnect_2018-11-27.csv"
bin/magento forix:import --entity=melfred_catalog_product  --based=catalog_product --behavior=append --file="catalog_product_FastBack_2018-11-27.csv"

=== Import Attribute --
bin/magento forix:import --entity=melfred_product_attributes --behavior=append --file="catalog_product_Reamers_2018-11-27.csv"
bin/magento forix:import --entity=melfred_product_attributes --behavior=append --file="catalog_product_Locators_2018-11-27.csv"
bin/magento forix:import --entity=melfred_product_attributes --behavior=append --file="catalog_product_Jaws_2018-11-27.csv"
bin/magento forix:import --entity=melfred_product_attributes --behavior=append --file="catalog_product_TransmitterHousings_2018-11-27.csv"
bin/magento forix:import --entity=melfred_product_attributes --behavior=append --file="catalog_product_DriveChucks_2018-11-27.csv"
bin/magento forix:import --entity=melfred_product_attributes --behavior=append --file="catalog_product_Pullers_2018-11-27.csv"
bin/magento forix:import --entity=melfred_product_attributes --behavior=append --file="catalog_product_Adapters_2018-11-27.csv"
bin/magento forix:import --entity=melfred_product_attributes --behavior=append --file="catalog_product_Replacement_2018-11-27.csv"
bin/magento forix:import --entity=melfred_product_attributes --behavior=append --file="catalog_product_BitBlade_2018-11-27.csv"
bin/magento forix:import --entity=melfred_product_attributes --behavior=append --file="catalog_product_FastBack_2018-11-27.csv"
bin/magento forix:import --entity=melfred_product_attributes --behavior=append --file="catalog_product_Swivels_2018-11-27.csv"
bin/magento forix:import --entity=melfred_product_attributes --behavior=append --file="catalog_product_QuickDisconnect_2018-11-27.csv"

---------------------- 
Luu y:
--
-- FastBack (Co product bi loi, configurable co product thuoc nhieu file)

--file="catalog_product_Locators_2018-11-27.csv"
1. Imported resource (image) in rows: 2, 10, 12, 15, 23, 29, 30, 35, 36, 46

--file="catalog_product_Pullers_2018-11-27.csv"
1. Imported resource (image) in rows: 14

--file="catalog_product_Adapters_2018-11-27.csv"
1. Imported resource (image) in rows: 21, 193, 194, 197

--file="catalog_product_BitBlade_2018-11-27.csv"
1. Imported resource (image) in rows: 35, 67, 68, 129

--file="catalog_product_Jaws_2018-11-27.csv"
1. Imported resource (image) in rows: 5

--file="catalog_product_Replacement_2018-11-27.csv"
1. Imported resource (image) in rows: 15, 54, 57, 148, 194, 196, 201, 202, 235, 241

--file="catalog_product_Swivels_2018-11-27.csv"
1. Imported resource (image) in rows: 46, 63

--file="catalog_product_TransmitterHousings_2018-11-27.csv"
1. Attribute with code "mb_transmitter_type" is not super in rows: 51, 52, 53, 55

--file="catalog_product_Jaws_2018-11-27.csv"
1. Imported resource (image) in rows: 5
