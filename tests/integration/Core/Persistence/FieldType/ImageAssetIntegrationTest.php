<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Persistence\FieldType;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Core\FieldType;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\ImageAssetConverter;
use Ibexa\Contracts\Core\Persistence\Content;

class ImageAssetIntegrationTest extends BaseIntegrationTest
{
    /**
     * {@inheritdoc}
     */
    public function getTypeName()
    {
        return FieldType\ImageAsset\Type::FIELD_TYPE_IDENTIFIER;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomHandler()
    {
        $contentService = $this->createMock(ContentService::class);
        $locationService = $this->createMock(LocationService::class);
        $contentTypeService = $this->createMock(ContentTypeService::class);
        $contentHandler = $this->createMock(Content\Handler::class);
        $configResolver = $this->createMock(ConfigResolverInterface::class);

        $mapper = new FieldType\ImageAsset\AssetMapper(
            $contentService,
            $locationService,
            $contentTypeService,
            $configResolver
        );

        $fieldType = new FieldType\ImageAsset\Type(
            $contentService,
            $contentTypeService,
            $mapper,
            $contentHandler
        );

        $fieldType->setTransformationProcessor($this->getTransformationProcessor());

        return $this->getHandler(
            'ezimageasset',
            $fieldType,
            new ImageAssetConverter(),
            new FieldType\NullStorage()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeConstraints()
    {
        return new Content\FieldTypeConstraints();
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldDefinitionData()
    {
        return [
            ['fieldType', 'ezimageasset'],
            ['fieldTypeConstraints', new Content\FieldTypeConstraints(['fieldSettings' => null])],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getInitialValue()
    {
        return new Content\FieldValue(
            [
                'data' => [
                    'destinationContentId' => 1,
                    'alternativeText' => null,
                ],
                'externalData' => null,
                'sortKey' => null,
            ],
            [
                'data' => [
                    'destinationContentId' => 1,
                    'alternativeText' => 'The alternative text',
                ],
                'externalData' => null,
                'sortKey' => null,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedValue()
    {
        return new Content\FieldValue(
            [
                'data' => [
                    'destinationContentId' => 2,
                    'alternativeText' => null,
                ],
                'externalData' => null,
                'sortKey' => null,
            ],
            [
                'data' => [
                    'destinationContentId' => 2,
                    'alternativeText' => 'The alternative text',
                ],
                'externalData' => null,
                'sortKey' => null,
            ]
        );
    }
}

class_alias(ImageAssetIntegrationTest::class, 'eZ\Publish\SPI\Tests\FieldType\ImageAssetIntegrationTest');