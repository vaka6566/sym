<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Utils\CategoryTreeFrontPage;
use App\Entity\Category;
use App\Entity\Video;

class FrontController extends AbstractController
{
    #[Route('/', name: 'main_page')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }

    ##[Route('/login', name: 'login')]
    #public function login(AuthenticationUtils $helper): Response
    #{
        #return $this->render('front/login.html.twig', ['error' => $helper->getLastAuthenticationError()]);
    #}

    ##[Route('/logout', name: 'logout', methods:["GET"])]
    #public function logout() : void
    #{
        #throw new \Exception('This should never be reached!');
    #}

    #[Route('/video_list/category/{categoryname}, {id}/{page}', name: 'video_list')]
    public function videoList($id, $page=1, CategoryTreeFrontPage $categories, ManagerRegistry $doctrine, Request $request): Response
    {
        $ids = $categories->getChildIds($id);
        array_push($ids, $id);

        $videos = $doctrine->getRepository(Video::class)
        ->findByChildIds($ids ,$page, $request->get('sortby'));

        $categories->getCategoryListAndParent($id);
        return $this->render('front/video_list.html.twig',[
            'subcategories' => $categories,
            'videos'=>$videos
        ]);
    }

    #[Route('/video_details', name: 'video_details')]
    public function videoDetails(): Response
    {
        return $this->render('front/video_details.html.twig');
    }

    #[Route('/search_results/{page}', name: 'search_results', methods:'GET')]
    public function searchResults($page=1, ManagerRegistry $doctrine, Request $request): Response
    {
        $videos = null;
        $query = null;

        if($query = $request->get('query'))
        {
            $videos = $doctrine->getRepository(Video::class)
            ->findByTitle($query, $page, $request->get('sortby'));

            if(!$videos->getItems()) $videos = null;
        }
       
        return $this->render('front/search_results.html.twig',[
            'videos' => $videos,
            'query' => $query,
        ]);
    }

    #[Route('/pricing', name: 'pricing')]
    public function pricing(): Response
    {
        return $this->render('front/pricing.html.twig');
    }

    #[Route('/register', name: 'register')]
    public function register(): Response
    {
        return $this->render('front/register.html.twig');
    }

    

    #[Route('/payment', name: 'payment')]
    public function payment(): Response
    {
        return $this->render('front/payment.html.twig');
    }

    public function mainCategories(ManagerRegistry $registry): Response
    {
        $categories = $registry->getRepository(Category::class)->findBy(['parent'=>null],['name'=>'ASC']);
        return $this->render('front/_main_categories.html.twig', ['categories'=>$categories]);
    }
}
