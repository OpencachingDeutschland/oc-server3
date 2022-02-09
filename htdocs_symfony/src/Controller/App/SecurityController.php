<?php

namespace Oc\Controller\App;

use Exception;
use Oc\Controller\Backend\MailerController;
use Oc\Entity\UserEntity;
use Oc\Form\UserActivationForm;
use Oc\Form\UserRegistrationForm;
use Oc\Repository\CountriesRepository;
use Oc\Repository\Exception\RecordsNotFoundException;
use Oc\Repository\UserRepository;
use Oc\Repository\UserRolesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 *
 */
class SecurityController extends AbstractController
{
    /** @var int */
    public $authenticationUtils;

    /** @var UserRepository */
    public $userRepository;

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @param UserRepository $userRepository
     */
    public function __construct(AuthenticationUtils $authenticationUtils, UserRepository $userRepository)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/login", name="security_login")
     */
    public function login()
    : Response
    {
        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
    }

    /**
     * Manage requests to register a new user
     *
     * @param MailerController $mailerController
     * @param Request $request
     * @param CountriesRepository $countriesRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @param UserRolesRepository $userRolesRepository
     *
     * @return Response
     * @throws RecordsNotFoundException
     * @throws TransportExceptionInterface
     * @Route("/register", name="security_register")
     */
    public function registerNewUser(
        MailerController $mailerController,
        Request $request,
        CountriesRepository $countriesRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository,
        UserRolesRepository $userRolesRepository
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

            // TODO: aktivieren.. :-}
            try {
                $user = $userRepository->create($user);

                $userRolesRepository->grantRole($user->userId, 'ROLE_USER');

                $mailerController->sendActivationEmail($user->username, $user->email, $user->activationCode);
            } catch (Exception $e) {
                return $this->render('security/register.html.twig', [
                    'userRegistrationForm' => $userRegistrationForm->createView(),
                    'registrationError' => $registrationError
                ]);
            }

            return $this->render('security/registerDone.html.twig', ['user' => $user, 'mailSent' => true]);
        } else {
            $registrationError = $this->authenticationUtils->getLastAuthenticationError();
        }

        return $this->render('security/register.html.twig', [
            'userRegistrationForm' => $userRegistrationForm->createView(),
            'registrationError' => $registrationError
        ]);
    }

    /**
     * Activation of a new user account via URL (e.g. provided by activation email)
     *
     * @param string $activationCode
     * @param string $email
     *
     * @return Response
     *
     * @Route("/automaticAccountActivation/{activationCode}&{email}", name="security_automatic_account_activation")
     */
    public function automaticActivateAccount(string $activationCode, string $email)
    : Response {
        $form = $this->createForm(UserActivationForm::class);

        $activationStatus = $this->accountActivationUpdateDB($activationCode, $email);

        return $this->render('security/accountActivation.html.twig', [
            'userActivationForm' => $form->createView(),
            'emailActivationStatus' => $activationStatus
        ]);
    }

    /**
     * Activation of a new user account via website (activation code and email have to be entered manually)
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/accountActivation/", name="security_account_activation")
     */
    public function activateAccountViaWebsite(Request $request)
    : Response {
        $form = $this->createForm(UserActivationForm::class);
        $activationStatus = 'new';

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $inputData = $form->getData();
            $activationStatus = $this->accountActivationUpdateDB($inputData['activationCode'], $inputData['email']);
        }

        return $this->render('security/accountActivation.html.twig', [
            'userActivationForm' => $form->createView(),
            'emailActivationStatus' => $activationStatus
        ]);
    }

    /**
     * Update of activation information in database
     *
     * @param string $activationCode
     * @param string $email
     *
     * @return string
     */
    private function accountActivationUpdateDB(string $activationCode, string $email)
    : string {
        $emailActivationStatus = 'fail';

        try {
            $fetchedUser = $this->userRepository->fetchOneBy(['email' => $email]);

            if (!empty($fetchedUser)) {
                if ($fetchedUser->activationCode == $activationCode) {
                    $fetchedUser->isActive = true;
                    $fetchedUser->activationCode = '';
                    try {
                        $this->userRepository->update($fetchedUser);
                        $emailActivationStatus = 'success';
                    } catch (Exception $e) {
                    }
                }
            }
        } catch (Exception $e) {
        }

        return $emailActivationStatus;
    }
}
