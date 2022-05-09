<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="category_list")
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        $data = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $data,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="category_edit")
     */
    public function edit(Request $request, EntityManagerInterface $manager, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            $this->addFlash('success', 'Category updated .');

            return $this->redirectToRoute('category_list');
        }

        return $this->render('category/add.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'title' => 'Edit category',
        ]);
    }

    /**
     * @Route("/add", name="category_add")
     */
    public function add(Request $request, EntityManagerInterface $manager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($category);
            $manager->flush();
            $this->addFlash('success', 'Category saved .');

            return $this->redirectToRoute('category_list');
        }

        return $this->render('category/add.html.twig', [
            'form' => $form->createView(),
            'title' => 'New category',
        ]);
    }

    /**
     * @Route("/remove/{id}", name="category_remove")
     */
    public function remove(): Response
    {
        return false;
    }
}
