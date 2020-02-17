select sku, error_list from forix_rawdata where error_list <> '' and file_name = 'Adapters.csv';
select sku, image, error_list from forix_rawdata where error_list <> '' and file_name = 'Bits & Blades.csv';
select sku, error_list from forix_rawdata where error_list <> '' and file_name = 'Drive Chucks & Sub-Savers.csv';
select sku, error_list from forix_rawdata where error_list <> '' and file_name = 'FastBack.csv';
select sku, error_list from forix_rawdata where error_list <> '' and file_name = 'Jaws.csv';
select sku, image, error_list from forix_rawdata where error_list <> '' and file_name = 'Locators.csv';
select sku, error_list from forix_rawdata where error_list <> '' and file_name = 'Pullers.csv';
select sku, error_list from forix_rawdata where error_list <> '' and file_name = 'Quick Disconnects & Collars.csv';
select sku, error_list from forix_rawdata where error_list <> '' and file_name = 'Reamers.csv';
select sku, image, error_list from forix_rawdata where error_list <> '' and file_name = 'Replacement Parts & Accessories.csv';
select sku, error_list from forix_rawdata where error_list <> '' and file_name = 'Swivels.csv';
select sku, error_list from forix_rawdata where error_list <> '' and file_name = 'Transmitter Housings.csv';


select sku, weight_lbs, file_name, image, product_type, error_list from forix_rawdata where error_list <> ''
-------------------- Xu Ly sau
select sku, reamer_cutting_size from forix_rawdata where file_name = 'Reamers.csv'
-- delete from forix_rawdata where file_name = '1_FastBack.csv';

UPDATE forix_rawdata SET image = replace(image,'Bits & Blades/Bits & Blades','Bits & Blades') where file_name = 'Bits & Blades.csv';

UPDATE forix_rawdata SET gallery = replace(gallery,'Bits & Blades/Bits & Blades','Bits & Blades') where file_name = 'Bits & Blades.csv';

delete from forix_rawdata where file_name = 'Swivels.csv';

select file_name, relation_attributes, error_list from forix_rawdata where file_name = 'FastBack.csv';

select file_name, configurable_variations, configurable_variation_labels from forix_rawdata where sku = 'FASTBACKC';

select categories from forix_rawdata group by categories

select rawdata_id, attribute_set from forix_rawdata group by attribute_set
delete from  forix_rawdata where file_name = 'FastBack.csv';
UPDATE forix_rawdata SET categories = replace(categories,'Su-Savers','Sub-Savers') where categories like '%Drive Chucks & Su-Savers%'

UPDATE forix_rawdata SET attribute_set = replace(attribute_set,'Bits and Blades','Bits & Blades')
-- Pullers, Adapters Has product Group


select sku,
sku_parent,
name,
product_type,
categories,
product_line,
visibility,
attribute_set,
badge,
description,
features,
small_description,
weight_lbs,
qty,
stock_status,
backorders,
tax_class,
price,
special_price,
redesigned_date,
redesigned_description,
downloads,
related_articles,
oem,
rig_models,
reamer_rear_connection_option,
swivel_capacity,
swivel_connection,
swivel_thread,
swivel_type,
relation_attributes,
image,
image_label,
gallery,
gallery_label,
meta_title,
meta_description,
meta_keyword,
related_sku,
crosssell_sku,
upsell_sku,
redesigned,
configurable_variations,
configurable_variation_labels,
custom_options,
bundle_values,
associated_skus,
url_key,
store_view_code,
_product_websites
from forix_rawdata where file_name = 'Swivels.csv'



select * from forix_rawdata where error_list <> ''


skuMINI-LINK-304,mb_bit_type=Borzall Blade,mb_blade_cutting_size=2.5"|skuEC35-FB42-305,mb_bit_type=Eagle Claw,mb_blade_cutting_size=3.5"|skuEC42-FB50-305,mb_bit_type=Eagle Claw,mb_blade_cutting_size=4.25"|skuEC50-FB60-310,mb_bit_type=Eagle Claw,mb_blade_cutting_size=5"
sku=00503-205-QS
|skuMINI-LINK-304,mb_blade_cutting_size=2.5"
|skuEC50-FB60-310,mb_blade_cutting_size=5"
|skuEC35-FB42-305,mb_blade_cutting_size=3.5"
|skuQL-310
|skuQL-305
|skuEC42-FB50-305,mb_blade_cutting_size=4.25"