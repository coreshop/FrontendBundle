<?php
declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\FrontendBundle\TemplateConfigurator;

class TemplateConfigurator implements TemplateConfiguratorInterface
{
    public function __construct(
        private string $bundleName,
        private string $templateSuffix,
    ) {
    }

    public function findTemplate($templateName): string
    {
        return sprintf('@%s/%s.%s', $this->bundleName, $templateName, $this->templateSuffix);
    }
}
