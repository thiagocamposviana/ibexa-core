<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\RepositoryInstaller\Installer;

/**
 * Interface Installer.
 *
 * Simple SQL based installer interface for eZ Platform 1.0, will be replaced by a new interface in the future that
 * uses API/SPI (via future import/export functionality) to support cluster and several different storage engines.
 * Such change will also move responsibility of repository init (base schema and minimal data) to storage engine
 * so this is not in installers. Further info: https://issues.ibexa.co/browse/EZP-25368
 */
interface Installer
{
    /**
     * Handle inserting of schema.
     */
    public function importSchema();

    /**
     * Handle inserting of sql dump, sql dump should ideally be in ISO SQL format.
     */
    public function importData();

    /**
     * Handle optional import of binary files to var folder.
     */
    public function importBinaries();
}
