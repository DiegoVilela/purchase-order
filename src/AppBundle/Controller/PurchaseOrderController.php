<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Balance;
use AppBundle\Entity\PurchaseOrder;
use AppBundle\Entity\Supplier;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PurchaseOrderController extends Controller
{
    /**
     * @Route("/accounts", name="accounts")
     * @Method("GET")
     */
    public function accountsAction()
    {
        $accounts = $this->getDoctrine()->getRepository(Account::class)
            ->findBy([], ['number' => 'ASC']);

        return $this->render('purchase_order/accounts.html.twig', ['accounts' => $accounts]);
    }

    /**
     * @Route("/account/{id}", name="account")
     * @Method("GET")
     */
    public function accountShowAction(Account $account)
    {
        $balances = $this->getDoctrine()
            ->getRepository(Balance::class)
            ->findAllByAccount($account->getId());

        return $this->render('purchase_order/account_show.html.twig', [
            'account'  => $account,
            'balances' => $balances
        ]);
    }

    /**
     * @Route("/account/{id}/export", name="account_export")
     * @Method("GET")
     */
    public function accountExportAction(Account $account)
    {
        return $this->get('balances_manager')->exportAccount($account);
    }

    /**
     * @Route("/purchase-orders", name="purchase_orders")
     * @Method("GET")
     */
    public function purchaseOrderAction(Request $request)
    {
        $form = $this->createFormBuilder();
        $form->setMethod('GET');
        $form->add('settled', CheckboxType::class, [
            'label' => 'Add purchase orders completely settled.',
            'required' => false
        ]);
        $form = $form->getForm();

        $form->handleRequest($request);

        // Show completely settled purchase orders?
        $settled = $form->getData()['settled'];

        $purchaseOrders = $this->getDoctrine()->getRepository(PurchaseOrder::class)
            ->findAllWithSuppliers($settled);

        return $this->render('purchase_order/purchase_orders.html.twig', [
            'form'    => $form->createView(),
            'purchaseOrders' => $purchaseOrders
        ]);
    }

    /**
     * @Route("/purchase-order/{id}", name="purchase_order")
     * @Method("GET")
     */
    public function purchaseOrderShowAction(Request $request)
    {
        $purchaseOrderId = $request->attributes->get('id');

        $purchaseOrder = $this->getDoctrine()->getRepository(PurchaseOrder::class)
            ->findOneById($purchaseOrderId);

        $balances = $this->getDoctrine()->getRepository(Balance::class)
            ->findAllByPurchaseOrder($purchaseOrder->getId());

        return $this->render('purchase_order/purchase_order_show.html.twig', [
            'purchaseOrder' => $purchaseOrder,
            'balances'  => $balances
        ]);
    }

    /**
     * @Route("/suppliers", name="suppliers")
     * @Method("GET")
     */
    public function suppliersAction()
    {
        $suppliers = $this->getDoctrine()->getRepository(Supplier::class)->findAll();

        return $this->render('purchase_order/suppliers.html.twig', ['suppliers' => $suppliers]);
    }

    /**
     * @Route("/supplier/{id}", name="supplier")
     * @Method("GET")
     */
    public function supplierShowAction(Supplier $supplier)
    {
        return $this->render('purchase_order/supplier_show.html.twig', ['supplier' => $supplier]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     * @Route("/update", name="update")
     * @Method({"GET", "POST"})
     */
    public function updateAction(Request $request)
    {
        # http://symfony.com/doc/current/form/without_class.html
        $form = $this->createFormBuilder(array());
        for ($i = 0; $i < 4; $i++) {
            $form->add('file'.$i, FileType::class, ['required' => false]);
        }
        $form = $form->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $balancesManager = $this->get('app.utils.balances_manager');

            foreach ($form->getData() as $file) {
                if (!empty($file)) {
                    try {
                        $balancesManager->processFile($file);
                    } catch (\Throwable $exception) {
                        $this->addFlash('danger','Error: '.$exception->getMessage());
                    }
                }
            }
        }

        return $this->render('purchase_order/update.html.twig', ['form' => $form->createView()]);
    }
}
