<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\SanPham;
use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Form\UserType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomepageController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/feature', name: 'feature_product')]
    public function viewdetail(EntityManagerInterface $em): Response
    {
        $query = $em->createQuery('SELECT sp FROM App\Entity\SanPham sp');
        $lSp = $query->getResult();
                                
        return $this->render('homepage/sanpham.html.twig', [
            "data"=>$lSp
        ]);
    }
    #[Route('/category', name: 'app_category')]
    public function viewCate(EntityManagerInterface $em): Response
    {
        $query = $em->createQuery('SELECT category FROM App\Entity\Category category');
        $category = $query->getResult();
                                
        return $this->render('homepage/category.html.twig', [
            'category' => $category,
        ]);
    }
    #[Route('/feature/{id}', name: 'app_detailsp')]
    public function detail(EntityManagerInterface $em, int $id): Response
    {
        $sp = $em->find(SanPham::class, $id);

        return $this->render('homepage/detail.html.twig', [
            'data' => $sp,
        ]);
    }
    
    #[Route('/', name: 'homepage')]
    public function cate(EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $em->createQuery('SELECT category FROM App\Entity\Category category');
        $category = $query->getResult();

        $query = $em->createQuery('SELECT sp FROM App\Entity\SanPham sp');
        $lSp = $query->getResult();

        $pagination = $paginator->paginate(
            $lSp, 
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('homepage/index.html.twig', [
            'category' => $category,
            'data' => $pagination,
        ]);
    }

    #[Route('/category/{id}', name: 'app_productofcate')]
    public function viewCategoryProducts(EntityManagerInterface $em, int $id, Request $request): Response
    {
        $cate = $em->find(Category::class, $id);
        $lSp = $cate->getSanPhams();
        return $this->render('homepage/sanpham.html.twig', [
            "data"=>$lSp
        ]);
    }

    #[Route('/profile', name: 'profile')]
    public function profile()
    {
        $user = $this->getUser();

        return $this->render('homepage/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/edit', name: 'edit_profile')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $user->setFirstName($request->request->get('first_name'));
            $user->setLastName($request->request->get('last_name'));
            $user->setAddress($request->request->get('address'));
            $user->setPhonenumber($request->request->get('phone_number'));
            $entityManager->flush();
        
            return $this->redirectToRoute('profile');
        }

        return $this->render('homepage/settings.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/search', name: 'search_products')]
    public function search(Request $request): Response
    {
        $keyword = $request->query->get('keyword');

        $products = $this->entityManager->getRepository(SanPham::class)->findByKeyword($keyword);

        return $this->render('homepage/sanpham.html.twig', ['data' => $products]);
    }
    #[Route('/search-api',methods: ['GET'],name:'search_api')]
    public function apiSearch(Request $request)
    {
        $keyword = $request->query->get('keyword');

        $products = $this->entityManager->getRepository(SanPham::class)
        ->findByKeywordWithLimit($keyword);
        $results = [];

        foreach($products as $product)
        {
            $results[] = [
                'id'=>$product->getId(),
                'name'=>$product->getName(),
                'photo'=>$product->getPhoto(),
                'price'=>$product->getPrice(),
            ];
        }
       return new JsonResponse($results);
       
    }
}
