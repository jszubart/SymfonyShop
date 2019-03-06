<?php
namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\Model\ChangePassword;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;

class AccountController extends AbstractController
{

    /**
     * @Route("/account", name = "account")
     */
    public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $changePasswordModel = new ChangePassword();
        $form = $this->createForm(ChangePasswordType::class, $changePasswordModel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $request->request->get('change_password')['oldPassword'];
                if ($passwordEncoder->isPasswordValid($user, $oldPassword)) {
                    $password = $form->get('plainPassword')->getData();
                    $newPassword = $passwordEncoder->encodePassword($user, $password);
                    $user->setPassword($newPassword);
                    $em->persist($user);
                    $em->flush();

                    $this->addFlash(
                        'notice',
                        'Password changed successfully'
                    );
            }
                return $this->redirectToRoute('homepage');

        }

        return $this->render('account.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}