<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Core\Model\OrderInterface;
use Pimcore\Http\Request\Resolver\EditmodeResolver;
use Symfony\Component\HttpFoundation\Request;

class MailController extends FrontendController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mailAction(Request $request)
    {
        return $this->render($this->templateConfigurator->findTemplate('Mail/mail.html'));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function orderConfirmationAction(Request $request)
    {
        $order = $request->get('object');
        $viewParameters = [];

        if (!$this->get(EditmodeResolver::class)->isEditmode($request) && $order instanceof OrderInterface) {
            $viewParameters['order'] = $order;
        }

        return $this->render($this->templateConfigurator->findTemplate('Mail/order-confirmation.html'), $viewParameters);
    }
}
