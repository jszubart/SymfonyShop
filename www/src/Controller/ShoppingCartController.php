<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/cart")
 */
class ShoppingCartController extends AbstractController
{

    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }


    /**
     * @Route("/", name="cart_index")
     */
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $products = $session->get('products');
        return $this->render('cart/shopping_cart.html.twig', [
            'products' => $products
        ]);
    }
    /**
     * @Route("/add/{id}", name = "cart_add" , methods={"GET"})
     */
    public function add(Request $request)
    {
        $product_id = $request->query->get('id');
        $product = $this->productRepository->find($product_id);
        $session = $request->getSession();
        if ($session->has('products')) {
            $products = $session->get('products');
            foreach ($products as $key => $productValue) {
                if ($product->getName() == $productValue['name']) {
                    $session->set('products', $products);
                    return $this->redirectToRoute('cart_index');
                }
            }
        } else {
                $products = [];
            }
                $newProduct = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice() ];

                array_push($products, $newProduct);

                $session->set('products', $products);

        $this->addFlash(
            'info',
            'Product has been added to cart!'
        );

        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/delete", name = "cart_delete" , methods={"POST"})
     */
    public function delete(Request $request)
    {
        $product_id = $request->request->get('remove_product');
        $product = $this->productRepository->find($product_id);
        $session = $request->getSession();

        if ($session->has('products')) {
            $products = $session->get('products');
            foreach ($products as $key => $productValue) {
                if ($product->getName() == $productValue['name']) {
                    unset($products[$key]);
                    $session->set('products', $products);

                    $this->addFlash(
                        'danger',
                        'Product has been removed from cart!'
                    );
                }
            }
        }
        return $this->redirectToRoute('cart_index');
    }
}