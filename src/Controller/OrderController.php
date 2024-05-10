<?php

namespace App\Controller;

use App\Cart\CartManager;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\SanPham;
use App\Form\OrderFormType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OrderController extends AbstractController
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    #[Route('/order', name: 'app_order')]
    public function index(EntityManagerInterface $em, Request $req): Response
    {
        $order = new Order();
        $message = $req->query->get('message');
        $form = $this->createForm(OrderFormType::class, $order);
        $form->handleRequest($req);
        $session = $req->getSession();
        $cart_manager = $session->get('cart', new CartManager());

        if ($form->isSubmitted() && $form->isValid()) {
            $order = $form->getData();
            $em->getConnection()->beginTransaction();
            try {
                $order->setTotalPrice($cart_manager->getAmount());
                $order->setStatus(false);
                $em->persist($order);
                $cart_items = $cart_manager->getItems();
                foreach ($cart_items as $key => $cart_item) {
                    $orderItem = new OrderItem();
                    $product = $em->find(SanPham::class,$cart_item->getProduct()->getId());
                    $orderItem->setProduct($product);
                    $orderItem->setQuantity($cart_item->quantity);
                    $orderItem->setPrice($cart_item->getProduct()->getPrice());
                    $orderItem->setOrderRef($order);
                    $em->persist($orderItem);
                    $em->flush();
                }
                $em->flush();
                $em->getConnection()->commit();
                $session->set('cart', new CartManager());
            } catch (Exception $e) {
                $em->getConnection()->rollBack();
                return new RedirectResponse($this->urlGenerator->generate('app_order',["message"=>"Error! Unable to create order"]));
            }
            return new RedirectResponse($this->urlGenerator->generate('homepage',["message"=>"Successful order creation"]));
        }

        return $this->render('order/index.html.twig', [
            'order_form' => $form->createView(),
            'cart_manager' => $cart_manager,
            'message' => $message
        ]);
    }
}
