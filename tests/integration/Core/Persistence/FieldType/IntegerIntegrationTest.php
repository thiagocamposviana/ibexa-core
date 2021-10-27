<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Persistence\FieldType;

use Ibexa\Core\Persistence\Legacy;
use Ibexa\Core\FieldType;
use Ibexa\Contracts\Core\Persistence\Content;

/**
 * Integration test for legacy storage field types.
 *
 * This abstract base test case is supposed to be the base for field type
 * integration tests. It basically calls all involved methods in the field type
 * ``Converter`` and ``Storage`` implementations. Fo get it working implement
 * the abstract methods in a sensible way.
 *
 * The following actions are performed by this test using the custom field
 * type:
 *
 * - Create a new content type with the given field type
 * - Load create content type
 * - Create content object of new content type
 * - Load created content
 * - Copy created content
 * - Remove copied content
 *
 * @group integration
 */
class IntegerIntegrationTest extends BaseIntegrationTest
{
    /**
     * Get name of tested field type.
     *
     * @return string
     */
    public function getTypeName()
    {
        return 'ezinteger';
    }

    /**
     * Get handler with required custom field types registered.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Handler
     */
    public function getCustomHandler()
    {
        $fieldType = new FieldType\Integer\Type();
        $fieldType->setTransformationProcessor($this->getTransformationProcessor());

        return $this->getHandler(
            'ezinteger',
            $fieldType,
            new Legacy\Content\FieldValue\Converter\IntegerConverter(),
            new FieldType\NullStorage()
        );
    }

    /**
     * Returns the FieldTypeConstraints to be used to create a field definition
     * of the FieldType under test.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\FieldTypeConstraints
     */
    public function getTypeConstraints()
    {
        return new Content\FieldTypeConstraints();
    }

    /**
     * Get field definition data values.
     *
     * This is a PHPUnit data provider
     *
     * @return array
     */
    public function getFieldDefinitionData()
    {
        return [
            // The ezint field type does not have any special field definition
            // properties
            ['fieldType', 'ezinteger'],
            [
                'fieldTypeConstraints',
                new Content\FieldTypeConstraints(
                    [
                        'validators' => [
                            'IntegerValueValidator' => [
                                'minIntegerValue' => false,
                                'maxIntegerValue' => false,
                            ],
                        ],
                    ]
                ),
            ],
        ];
    }

    /**
     * Get initial field value.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\FieldValue
     */
    public function getInitialValue()
    {
        return new Content\FieldValue(
            [
                'data' => 42,
                'externalData' => null,
                'sortKey' => 42,
            ]
        );
    }

    /**
     * Get update field value.
     *
     * Use to update the field
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\FieldValue
     */
    public function getUpdatedValue()
    {
        return new Content\FieldValue(
            [
                'data' => 23,
                'externalData' => null,
                'sortKey' => 23,
            ]
        );
    }
}

class_alias(IntegerIntegrationTest::class, 'eZ\Publish\SPI\Tests\FieldType\IntegerIntegrationTest');