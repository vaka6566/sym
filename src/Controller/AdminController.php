<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Utils\CategoryTreeAdminOptionList;
use App\Utils\CategoryTreeAdminList;
use App\Form\CategoryType;
use App\Entity\Category;

#[Route("/admin")]
class AdminController extends AbstractController
{
    #[Route("/", name:"admin")]
    public function index(): Response
    {
        return $this->render('admin/my_profile.html.twig');
    }

    #[Route("/su/categories", name:"categories", methods:["GET","POST"])]
    public function categories(CategoryTreeAdminList $categories, Request $request, ManagerRegistry $doctrine): Response
    {
        $categories->getCategoryList($categories->buildTree());

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $is_invalid = null;
        if($this->saveCategory($category, $form, $request, $doctrine))
        {
            return $this->redirectToRoute('categories');
        }
        elseif($request->isMethod('post'))
        {
            $is_invalid = ' is-invalid';
        }
        return $this->render('admin/categories.html.twig',[
            'categories'=>$categories->categorylist,
            'form'=>$form->createView(),
            'is_invalid'=>$is_invalid
        ]);
    }

    #[Route("/su/edit-category/{id}", name:"edit_category", methods:["GET","POST"])]
    public function editCategory(Category $category, Request $request, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $is_invalid = null;

        if($this->saveCategory($category, $form, $request, $doctrine))
        {
            return $this->redirectToRoute('categories');
        }
        elseif($request->isMethod('post'))
        {
            $is_invalid = ' is-invalid';
        }

        return $this->render('admin/edit_category.html.twig',[
            'category' => $category,
            'form' => $form->createView(),
            'is_invalid' => $is_invalid
        ]);
    }

    #[Route("/su/delete-category/{id}", name:"delete_category")]
    public function deleteCategory(Category $category, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($category);
        $entityManager->flush();
        return $this->redirectToRoute('categories');
    }

    #[Route("/videos", name:"videos")]
    public function videos(): Response
    {
        return $this->render('admin/videos.html.twig');
    }

    #[Route("/su/upload-video", name:"upload_video")]
    public function uploadVideo(): Response
    {
        return $this->render('admin/upload_video.html.twig');
    }

    #[Route("/su/users", name:"users")]
    public function users(): Response
    {
        return $this->render('admin/users.html.twig');
    }

    public function getAllCategories(CategoryTreeAdminOptionList $categories, $editedCategory = null): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $categories->getCategoryList($categories->buildTree());
        return $this->render('admin/_all_categories.html.twig',[
            'categories'=>$categories,
            'editedCategory'=>$editedCategory
        ]);
    }

    private function saveCategory($category, $form, $request, $doctrine)
    {
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $category = $form->getData();
            #$category->setName($request->request->get('category')['name']);
            $entityManager = $doctrine->getManager();
            $repository = $entityManager->getRepository(Category::class);
            $parent_id = $request->request->all()['category']['parent'];
            if ($parent_id > 0) {
                $parent = $repository->find($parent_id);
            }
            else
            {
                $parent = null;
            }
            $category->setParent($parent);
            
            $entityManager->persist($category);
            $entityManager->flush();

            return true;
        }
        return false;
    }
}
