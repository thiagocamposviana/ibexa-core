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
class EmailAddressIntegrationTest extends BaseIntegrationTest
{
    /**
     * Get name of tested field type.
     *
     * @return string
     */
    public function getTypeName()
    {
        return 'ezemail';
    }

    /**
     * Get handler with required custom field types registered.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Handler
     */
    public function getCustomHandler()
    {
        $fieldType = new FieldType\EmailAddress\Type();
        $fieldType->setTransformationProcessor($this->getTransformationProcessor());

        return $this->getHandler(
            'ezemail',
            $fieldType,
            new Legacy\Content\FieldValue\Converter\EmailAddressConverter(),
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
            // The ezstring field type does not have any special field definition
            // properties
            ['fieldType', 'ezemail'],
            [
                'fieldTypeConstraints',
                new Content\FieldTypeConstraints(
                    [
                        'validators' => [
                            'EmailAddressValidator' => [],
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
                'data' => 'admin@link.invalid',
                'externalData' => null,
                'sortKey' => 'admin@link.invalid',
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
                'data' => 'no-spam@example.com',
                'externalData' => null,
                'sortKey' => 'no-spam@example.com',
            ]
        );
    }
}

class_alias(EmailAddressIntegrationTest::class, 'eZ\Publish\SPI\Tests\FieldType\EmailAddressIntegrationTest');