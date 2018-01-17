<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\CoreBundle\Form\Type\Order\PaymentType;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderController extends FrontendController
{
    /**
     * @param Request $request
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function reviseAction(Request $request)
    {
        $token = $request->get('token');
        $payment = null;

        /** @var OrderInterface $order */
        $order = $this->getOrderRepository()->findOneBy(['token' => $token]);

        if (!$order instanceof OrderInterface) {
            throw new NotFoundHttpException();
        }

        if ($request->query->has('paymentId')) {
            $paymentObject = $this->getPaymentRepository()->find($request->query->get('paymentId'));
            if ($paymentObject instanceof PaymentInterface) {
                $payment = $paymentObject;
            }
        }

        $form = $this->getFormFactory()->createNamed('', PaymentType::class, $order, [
            'store' => $order->getStore(),
        ]);

        if ($request->isMethod('post')) {
            $form = $form->handleRequest($request);
            if ($form->isValid()) {
                $order = $form->getData();
                $order->save();

                return $this->redirectToRoute('coreshop_order_revise_pay', ['token' => $token]);
            }
        }

        $args = [
            'order'   => $order,
            'payment' => $payment,
            'form'    => $form->createView()
        ];

        return $this->renderTemplate('CoreShopFrontendBundle:Order:revise.html.twig', $args);
    }

    /**
     * @return OrderRepositoryInterface
     */
    protected function getOrderRepository()
    {
        return $this->get('coreshop.repository.order');
    }

    /**
     * @return RepositoryInterface
     */
    private function getPaymentRepository()
    {
        return $this->get('coreshop.repository.payment');
    }

    /**
     * @return FormFactoryInterface
     */
    protected function getFormFactory()
    {
        return $this->get('form.factory');
    }
}


