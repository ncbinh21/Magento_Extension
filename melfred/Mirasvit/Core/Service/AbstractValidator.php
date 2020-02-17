<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-core
 * @version   1.2.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Core\Service;


use Mirasvit\Core\Api\Service\ValidatorInterface;

abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * Keyword used for validation methods.
     */
    const TEST_METHOD_KEY = 'test';

    /**
     * Executes every method beginning with the 'test' keyword.
     *
     * {@inheritdoc}
     */
    public function validate()
    {
        $result = [];
        foreach (get_class_methods($this) as $method) {
            if (substr($method, 0, strlen(self::TEST_METHOD_KEY)) === self::TEST_METHOD_KEY) {
                try {
                    $result[] = call_user_func([$this, $method]);
                } catch (\Exception $e) {
                    $results[] = [self::FAILED, "Test '$method'", $e->getMessage()];
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleName()
    {
        $classArray = explode('\\', get_class($this));

        return $classArray[0] . '_' . $classArray[1];
    }
}
