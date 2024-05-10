<?php

namespace App\Controller;

use App\Cart\CartManager;
use App\Entity\SanPham;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CartController extends AbstractController
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }
    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $cart_manager = $session->get('cart', new CartManager());
        return $this->render('cart/index.html.twig', [
            "cart_manager"=>$cart_manager
        ]);
    }

    #[Route('/cart/add/{product_id}', name: 'app_cart_add', methods: 'post')]
    public function add(int $product_id, Request $request, EntityManagerInterface $em): Response
    {
        $product = $em->find(SanPham::class, $product_id);
        $session = $request->getSession();
        $cart_manager = $session->get('cart', new CartManager());
        $cart_manager->addItem($product, 1);
        $session->set('cart', $cart_manager);
        return new RedirectResponse($this->urlGenerator->generate('app_cart'));
    }
    #[Route('/cart/remove/{product_id}', name: 'app_cart_remove')]
    public function remove(int $product_id, Request $request, EntityManagerInterface $em): Response
    {
        $product = $em->find(SanPham::class, $product_id);
        $session = $request->getSession();
        $cart_manager = $session->get('cart', new CartManager());
        $cart_manager->removeItem($product);
        $session->set('cart', $cart_manager);
        return new RedirectResponse($this->urlGenerator->generate('app_cart'));
    }

    #[Route('/cart/update/', name: 'app_cart_update')]
    public function update(Request $request, EntityManagerInterface $em): Response
    {
        $product_id = $request->request->get('product_id');
        $quantity = $request->request->get('qty');
        $product = $em->find(SanPham::class, $product_id);
        $session = $request->getSession();
        $cart_manager = $session->get('cart', new CartManager());
        $cart_manager->updateItem($product, $quantity);
        $session->set('cart', $cart_manager);
        return new RedirectResponse($this->urlGenerator->generate('app_cart'));
    }
}
