<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Giftcard;

use Aheadworks\Giftcard\Api\Data\CodeGenerationSettingsInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\CodeGenerationSettingsInterface;
use Aheadworks\Giftcard\Model\Config;
use Aheadworks\Giftcard\Model\ResourceModel\Validator\GiftcardIsUnique;
use Aheadworks\Giftcard\Model\Source\Giftcard\CodeFormat;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;

/**
 * Class CodeGenerator
 *
 * @package Aheadworks\Giftcard\Model\Giftcard
 */
class CodeGenerator
{
    /**
     * @var int
     */
    const CODE_GENERATION_ATTEMPTS = 1000;

    /**
     * @var int
     */
    const DEFAULT_CODE_LENGTH = 4;

    /**
     * @var []
     */
    private $codeParameters = [];

    /**
     * @var GiftcardIsUnique
     */
    private $giftcardIsUniqueValidator;

    /**
     * @var CodeGenerationSettingsInterfaceFactory
     */
    private $codeGenerationSettingsFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param GiftcardIsUnique $giftcardIsUniqueValidator
     * @param CodeGenerationSettingsInterfaceFactory $codeGenerationSettingsFactory
     * @param Config $config
     * @param string[] $codeParameters
     */
    public function __construct(
        GiftcardIsUnique $giftcardIsUniqueValidator,
        CodeGenerationSettingsInterfaceFactory $codeGenerationSettingsFactory,
        Config $config,
        $codeParameters = []
    ) {
        $this->giftcardIsUniqueValidator = $giftcardIsUniqueValidator;
        $this->codeGenerationSettingsFactory = $codeGenerationSettingsFactory;
        $this->config = $config;
        $this->codeParameters = $codeParameters;
    }

    /**
     * Generate Gift Card code
     *
     * @param CodeGenerationSettingsInterface|null $codeGenerationSettings
     * @param int $websiteId
     * @return string[]
     * @throws LocalizedException
     */
    public function generate($codeGenerationSettings, $websiteId)
    {
        if (!$codeGenerationSettings) {
            $codeGenerationSettings = $this->getGenerationSettingsByDefault($websiteId);
        }
        $codes = [];
        $qty = $codeGenerationSettings->getQty();
        while ($qty > 0) {
            $attempt = 0;
            do {
                if ($attempt >= self::CODE_GENERATION_ATTEMPTS) {
                    throw new LocalizedException(__('Unable to create Gift Card code'));
                }
                $code = $this->generateCode($codeGenerationSettings);
                $attempt++;
            } while (!$this->giftcardIsUniqueValidator->validate($code));
            $codes[] = $code;
            $qty--;
        }
        return $codes;
    }

    /**
     * Generate code
     *
     * @param CodeGenerationSettingsInterface $codeGenerationSettings
     * @return string
     */
    private function generateCode($codeGenerationSettings)
    {
        $format = $codeGenerationSettings->getFormat();
        if (empty($format)) {
            $format = CodeFormat::ALPHANUMERIC;
        }

        $code = '';
        $length = max(self::DEFAULT_CODE_LENGTH, (int)$codeGenerationSettings->getLength());
        $charset = $this->getCharset($format);
        $charsetLength = strlen($charset);
        $delimiterAtEvery = max(0, (int)$codeGenerationSettings->getDelimiterAtEvery());
        $delimiter = $this->getDelimiter($codeGenerationSettings->getDelimiter());
        for ($i = 0; $i < $length; $i++) {
            $symbol = $charset[Random::getRandomNumber(0, $charsetLength - 1)];
            if (($delimiterAtEvery > 0) && (($i % $delimiterAtEvery) === 0) && ($i !== 0)) {
                $symbol = $delimiter . $symbol;
            }
            $code .= $symbol;
        }
        return trim($codeGenerationSettings->getPrefix()) . $code . trim($codeGenerationSettings->getSuffix());
    }

    /**
     * Retrieve generation settings by default
     *
     * @param int $websiteId
     * @return CodeGenerationSettingsInterface
     */
    private function getGenerationSettingsByDefault($websiteId)
    {
        /** @var CodeGenerationSettingsInterface $codeGenerationSettings */
        $codeGenerationSettings = $this->codeGenerationSettingsFactory->create();
        $codeGenerationSettings
            ->setQty(1)
            ->setLength($this->config->getGiftcardCodeLength($websiteId))
            ->setFormat($this->config->getGiftcardCodeFormat($websiteId))
            ->setPrefix($this->config->getGiftcardCodePrefix($websiteId))
            ->setSuffix($this->config->getGiftcardCodeSuffix($websiteId))
            ->setDelimiterAtEvery($this->config->getGiftcardCodeDashAtEvery($websiteId));

        return $codeGenerationSettings;
    }

    /**
     * Retrieve charset by format
     *
     * @param string $format
     * @return string
     */
    private function getCharset($format)
    {
        return $this->codeParameters['charset'][$format];
    }

    /**
     * Retrieve delimiter
     *
     * @param string|null $delimiter
     * @return string
     */
    private function getDelimiter($delimiter)
    {
        if (!empty($delimiter)) {
            return $delimiter;
        }
        return $this->codeParameters['delimiter'];
    }
}
