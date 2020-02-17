<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 08/08/2018
 * Time: 10:41
 */

namespace Forix\Configurable\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use \Forix\Configurable\Model\Attribute\Source\AttributeTemplate;
/**
 * Class SwatchAttributeCodes for getting codes of swatch attributes.
 */
class RadioSwatchAttributeCodes
{
    /**
     * @var string
     */
    private $cacheKey;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * Key is attribute_id, value is attribute_code
     *
     * @var array
     */
    private $swatchAttributeCodes;

    /**
     * @var array
     */
    private $cacheTags;
    protected $attributeTemplate;

    /**
     * SwatchAttributeList constructor.
     *
     * @param CacheInterface $cache
     * @param ResourceConnection $resourceConnection
     * @param string $cacheKey
     * @param array $cacheTags
     */
    public function __construct(
        CacheInterface $cache,
        ResourceConnection $resourceConnection,
        AttributeTemplate $attributeTemplate,
        $cacheKey,
        array $cacheTags
    )
    {
        $this->cache = $cache;
        $this->resourceConnection = $resourceConnection;
        $this->cacheKey = $cacheKey;
        $this->cacheTags = $cacheTags;
        $this->attributeTemplate = $attributeTemplate;
    }

    /**
     * Returns list of known swatch attribute codes. Check cache and database.
     * Key is attribute_id, value is attribute_code
     *
     * @return array
     */
    public function getCodes()
    {
        if ($this->swatchAttributeCodes === null) {
            $swatchAttributeCodesCache = $this->cache->load($this->cacheKey);
            if (false === $swatchAttributeCodesCache) {
                $swatchAttributeCodes = $this->getSwatchAttributeCodes();
                $this->cache->save(json_encode($swatchAttributeCodes), $this->cacheKey, $this->cacheTags);
            } else {
                $swatchAttributeCodes = json_decode($swatchAttributeCodesCache, true);
            }
            $this->swatchAttributeCodes = $swatchAttributeCodes;
        }

        return $this->swatchAttributeCodes;
    }

    /**
     * Returns list of known swatch attributes.
     *
     * Returns a map of id and code for all EAV attributes with swatches
     *
     * @return array
     */
    private function getSwatchAttributeCodes()
    {
        $type = [
	        AttributeTemplate::RADIO_WITH_SWATCH,
	        AttributeTemplate::RAIDO_OPTION_ONLY
        ];
    	$select = $this->resourceConnection->getConnection()->select()
            ->from(
                ['a' => $this->resourceConnection->getTableName('eav_attribute')],
                [
                    'attribute_id'    => 'a.attribute_id',
                    'attribute_code'  => 'a.attribute_code',
	                'option_template' => 'a.option_template'
                ]
            )
		    ->where('a.option_template IN (?)', $type);
		    /*
		    ->where(
                'a.option_template = ?',
                new \Zend_Db_Expr(AttributeTemplate::RADIO_WITH_SWATCH)
            );
		    */
        $result = $this->resourceConnection->getConnection()->fetchPairs($select);
        return $result;
    }
}
