<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
       $product = new Product();

       $this->denyAccessUnlessGranted('isUser', $product);

       $form = $this->createForm(ProductType::class, $product);
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid())
       {
           $entityManager = $this->getDoctrine()->getManager();
           $files = $request->files->get('product')['images'];
           /** @var UploadedFile $file */
           foreach ($files as $file)
           {
               $image = new Images();
               $filename = md5(uniqid()).'.'.$file->guessClientExtension();
               $file->move($this->getParameter('uploads_directory'), $filename);

               $image->setName($filename);
               $image->setPath('/assets/uploads/' . $filename);
               $image->setProduct($product);
               $image->setDateOfCreation(new \DateTime());
               $image->setMain(0);
               $product->setUser($this->getUser());
               $product->setImages($image);


               $entityManager->persist($image);
           }

           $product->setDateOfCreation(new \DateTime());
           $product->setDateOfLastModification(new \DateTime());

           $entityManager->persist($product);
           $entityManager->flush();

            $this->addFlash(
                'success',
                'New product has been added!'
            );

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        $this->denyAccessUnlessGranted('isUser', $product);

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST","DELETE"})
     */
    public function edit(Request $request, Product $product): Response
    {
        $this->denyAccessUnlessGranted('edit', $product);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $files = $request->files->get('product')['images'];

            /** @var UploadedFile $file */
            foreach ($files as $file)
            {
                $image = new Images();
                $filename = md5(uniqid()).'.'.$file->guessClientExtension();
                $file->move($this->getParameter('uploads_directory'), $filename);

                $image->setName($filename);
                $image->setPath('/assets/uploads/' . $filename);
                $image->setProduct($product);
                $image->setDateOfCreation(new \DateTime());
                $image->setMain(0);
                $product->setImages($image);

                $entityManager->persist($image);
            }

            $product->setDateOfLastModification(new \DateTime());

            $this->denyAccessUnlessGranted('edit',$product);

            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash(
                'info',
                'Product has been edited!'
            );

            return $this->redirectToRoute('product_index', [
                'id' => $product->getId(),
            ]);
        }
        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product): Response
    {
        $this->denyAccessUnlessGranted('edit', $product);

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash(
                'danger',
                'Product has been deleted!'
            );
        }
        return $this->redirectToRoute('product_index');
    }

    /**
     * @Route("/{id}/main", name="main_image", methods={"GET"})
     */
    public function mainImage(Request $request, Product $product): Response
    {
        $main_id = $request->query->get('insert');
        $entityManager = $this->getDoctrine()->getManager();

            foreach ($product->getImages() as $value) {
                if ($value->getId() == $main_id) {
                    $value->setMain(1);
                } else
                    $value->setMain(0);
            }

            $entityManager->persist($product);
            $entityManager->flush();

        return $this->redirectToRoute('product_edit', [
            'id' => $product->getId(),
            ]);
    }

    /**
     * @Route("/{id}/order", name="image_order", methods={"POST"})
     */
    public function imageOrder(Request $request,Product $product): Response
    {
        $array = explode(",",$request->request->get('ids'));
        $position = 1;
        $entityManager = $this->getDoctrine()->getManager();

        foreach ($array as $id) {
            foreach ($product->getImages() as $value) {
              if ($value->getId() == $id) {
                    $value->setPosition($position);
                   $position++;
                }
           }
       }
        $entityManager->persist($product);
        $entityManager->flush();

        return $this->redirectToRoute('product_edit', [
            'id' => $product->getId(),
        ]);
    }
}
