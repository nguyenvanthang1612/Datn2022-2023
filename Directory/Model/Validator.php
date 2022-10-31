<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Directory\Model;

use ErrorException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class Validator
{
    /**
     * Validator.
     *
     * @param AbstractModel $object
     *
     * @throws LocalizedException|ErrorException
     */
    public function validate($object)
    {
        if (!$object instanceof ValidatorInterface) {
            throw new ErrorException('Object must instance of ValidatorInterface.');
        }

        $resource = $object->getResource();
        $connection = $resource->getConnection();
        $conditions = [];

        foreach ($object->getRequiredUniqueFields() as $field) {
            if (empty($object->getData($field))) {
                throw new LocalizedException(__('Field \'%1\' is required.', $field));
            }

            $conditions[] = "e.{$field} = '{$object->getData($field)}'";
        }

        $query = $connection->select()->from(['e' => $resource->getMainTable()]);
        $query->where(implode(' OR ', $conditions));

        if ($object->getId()) {
            $query->where('e.' . $resource->getIdFieldName() . ' != ' . $object->getId());
        }

        $result = $connection->fetchOne($query);
        if (false !== $result) {
            throw new LocalizedException(__('Codes is exist.'));
        }
    }
}
