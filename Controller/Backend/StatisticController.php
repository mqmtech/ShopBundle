<?php

namespace MQM\ShopBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * @Route("/admin/estadisticas")
 */
class StatisticController extends Controller
{
    /**
     * @Route("/mas_vendidos", name="TKShopBackendStatMostSold")
     * @Template()
     */
    public function mostSoldProductsAction()
    {
        $paginationManager = $this->get('mqm_pagination.pagination_manager');
        $sortManager = $this->createSortManager();
        $mostViewed = $this->getStatisticManager()->findMostSoldProducts($sortManager, $paginationManager);
        return array(
            'statistics' => $mostViewed,
            'sortManager' => $sortManager->switchMode(),
        );
    }

    /**
     * @Route("/mas_vistos", name="TKShopBackendStatMostViewed")
     * @Template()
     */
    public function mostViewedProductsAction()
    {
        $paginationManager = $this->get('mqm_pagination.pagination_manager');
        $sortManager = $this->createSortManager();
        $mostViewed = $this->getStatisticManager()->findMostViewedProducts($sortManager, $paginationManager);
        return array(
            'statistics' => $mostViewed,
            'sortManager' => $sortManager->switchMode(),
        );
    }

    /**
     * @return \MQM\StatisticBundle\Model\StatisticManagerInterface
     */
    private function getStatisticManager()
    {
        return $this->get('mqm_statistic.statistic_manager');
    }

    private function createSortManager()
    {
        $sortManager = $this->get('mqm_sort.sort_manager');
        $sortManager->addSort('name', 'name', 'Producto')
            ->addSort('referencia', 'sku', 'Referencia')
            ->addSort('categoria', 'categoryName', 'Categoria')
            ->addSort('marca', 'brandName', 'Marca', 'DESC', array('default' => true))
            ->addSort('visitas', array('name' => 'counter', 'ignoreEntityAlias' => true), 'Visitas', 'DESC', array('default' => true))
            ->init();

        return $sortManager;
    }
}
