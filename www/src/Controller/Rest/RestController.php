<?php

namespace App\Controller\Rest;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RestController extends AbstractFOSRestController implements ClassResourceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Rest\Get("/products")
     */
    public function getProducts(): View
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        $product = array();
        foreach ($products as $value) {
            $product [] = [$value->getId(), $value->getName(), $value->getPrice()];
        }
        $data = json_encode($product);
        return View::create($data, Response::HTTP_OK,[]);

    }

    /**
     * @Rest\Get("/products/{product_id}")
     */
    public function getProduct($product_id): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($product_id);
        $normalizer = [new ObjectNormalizer()];
        $encoder = [new JsonEncoder()];
        $serializer = new Serializer($normalizer,$encoder);

        $serializedProduct = $serializer->serialize($product,'json',[
            'ignored_attributes' => ['category'],
           'circular_reference_handler' => function ($object) {
            return $object->getId();
           }
        ]);
        return new Response($serializedProduct, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Rest\Post("/products/new")
     */
    public function postProduct(Request $request)
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $data = json_decode($request->getContent(),true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {

            $product->setName($request->get('name'));
            $product->setPrice($request->get('price'));
            $product->setDateOfCreation(new \DateTime());
            $product->setDateOfLastModification(new \DateTime());

            $this->entityManager->persist($product);
            $this->entityManager->flush();
            return $this->handleView($this->view(['status' => 'ok'],Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($form->getErrors()));
    }

    /**
     * @Rest\Put("/products/{product_id}")
     */
    public function putProduct(int $product_id, Request $request): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($product_id);

        $form = $this->createForm(ProductType::class, $product);
        $data = json_decode($request->getContent(),true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setName($request->get('name'));
            $product->setPrice($request->get('price'));
            $product->setDateOfLastModification(new \DateTime());

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->handleView($this->view($product,Response::HTTP_OK));
        }
        return $this->handleView($this->view($form->getErrors()));
    }

    /**
     * @Rest\Delete("/products/{product_id}")
     */
    public function deleteProduct(int $product_id)
    {
        $product = $this->entityManager->getRepository(Product::class)->find($product_id);

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->handleView($this->view(null,Response::HTTP_NO_CONTENT));
    }
}