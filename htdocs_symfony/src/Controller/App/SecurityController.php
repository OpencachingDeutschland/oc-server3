<?php

namespace Oc\Controller\App;

use Doctrine\DBAL\DBALException;
use Oc\Entity\UserEntity;
use Oc\Form\UserRegistrationForm;
use Oc\Repository\CountriesRepository;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordsNotFoundException;
use Oc\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 *
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    : Response {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/register", name="security_register")
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     * @param CountriesRepository $countriesRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     *
     * @return Response
     * @throws RecordsNotFoundException
     * @throws DBALException
     * @throws RecordAlreadyExistsException
     */
    public function registerNewUser(
        AuthenticationUtils $authenticationUtils,
        Request $request,
        CountriesRepository $countriesRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository
    )
    : Response {
        $user = new UserEntity();
        $userRegistrationForm =
            $this->createForm(UserRegistrationForm::class, $user, ['countryList' => $countriesRepository->fetchCountryList($request->getLocale())]);
        $registrationError = '';

        $userRegistrationForm->handleRequest($request);
        if ($userRegistrationForm->isSubmitted() && $userRegistrationForm->isValid()) {
            $user->activationCode = $userRepository->generateActivationCode();
            $user->firstname = isset($user->firstname) ? trim($user->firstname) : '';
            $user->lastname = isset($user->lastname) ? trim($user->lastname) : '';
            $user->password = $passwordEncoder->encodePassword($user, $userRegistrationForm->get('plainPassword')->getData());

            $user = $userRepository->create($user);

            // TODO: Rolle zuweisen, Email schicken... aber nur, wenn User erfolgreich angelegt wurde..
            //            $user-> = 'ROLE_USER';

            $this->sendActivationEmail($user->userId);

            return $this->render('security/registerDone.html.twig', ['user' => $user]);
        } else {
            $registrationError = $authenticationUtils->getLastAuthenticationError();
        }

        return $this->render('security/register.html.twig', [
            'userRegistrationForm' => $userRegistrationForm->createView(),
            'registrationError' => $registrationError
        ]);
    }

    /**
     * Send Email with activation code.
     *
     * @param int $user_id
     *
     * @return void
     */
    private function sendActivationEmail(int $user_id)
    : void {
        // TODO: sende Email.. blah

    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
    }
}
