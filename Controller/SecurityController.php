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

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\CustomerBundle\Form\Type\CustomerLoginType;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends FrontendController
{
    public function __construct(
        protected AuthenticationUtils $authenticationUtils,
        protected FormFactoryInterface $formFactory,
        protected ShopperContextInterface $shopperContext,
    ) {
    }

    public function loginAction(Request $request): Response
    {
        if ($this->shopperContext->hasCustomer()) {
            return $this->redirectToRoute('coreshop_index');
        }

        $lastError = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $form = $this->formFactory->createNamed('', CustomerLoginType::class);

        $renderLayout = $this->getParameterFromRequest($request, 'renderLayout', true);

        $viewWithLayout = $this->templateConfigurator->findTemplate('Security/login.html');
        $viewWithoutLayout = $this->templateConfigurator->findTemplate('Security/_login-form.html');

        return $this->render($renderLayout ? $viewWithLayout : $viewWithoutLayout, [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'last_error' => $lastError,
            'target' => $this->getParameterFromRequest($request, 'target', null),
            'failure' => $this->getParameterFromRequest($request, 'failure', null),
        ]);
    }

    public function checkAction(Request $request): void
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall.');
    }

    public function logoutAction(Request $request): void
    {
        throw new \RuntimeException('You must configure the logout path to be handled by the firewall.');
    }
}
