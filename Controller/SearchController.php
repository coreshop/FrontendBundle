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

use CoreShop\Bundle\FrontendBundle\Form\Type\SearchType;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends FrontendController
{
    public function widgetAction(Request $request): Response
    {
        $form = $this->createSearchForm();

        return $this->render($this->templateConfigurator->findTemplate('Search/_widget.html'), [
            'form' => $form->createView(),
        ]);
    }

    public function searchAction(Request $request): Response
    {
        $form = $this->createSearchForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $text = $formData['text'];
            $page = $this->getParameterFromRequest($request, 'page', 1);
            $itemsPerPage = 10;

            $query = [
                'name LIKE ?',
                'description LIKE ?',
                'shortDescription LIKE ?',
                'sku LIKE ?',
            ];
            $queryParams = [
                '%' . $text . '%',
                '%' . $text . '%',
                '%' . $text . '%',
                '%' . $text . '%',
                '%' . $this->container->get(StoreContextInterface::class)->getStore()->getId() . '%',
            ];

            $list = $this->get('coreshop.repository.product')->getList();
            $list->setCondition('active = 1 AND (' . implode(' OR ', $query) . ') AND stores LIKE ?', $queryParams);

            $paginator = $this->getPaginator()->paginate(
                $list,
                $page,
                $itemsPerPage,
            );

            return $this->render($this->templateConfigurator->findTemplate('Search/search.html'), [
                'paginator' => $paginator,
                'searchText' => $text,
            ]);
        }

        return $this->redirectToRoute('coreshop_index');
    }

    protected function createSearchForm(): FormInterface
    {
        return $this->get('form.factory')->createNamed('coreshop', SearchType::class, null, [
            'action' => $this->generateUrl('coreshop_search'),
            'method' => 'GET',
        ]);
    }

    protected function getPaginator(): PaginatorInterface
    {
        return $this->get(PaginatorInterface::class);
    }
}
