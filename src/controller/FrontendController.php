<?php

namespace App\Controller;

//require_once("dao/EvenementDao.php");

use App\Dao\EvenementDao;
use App\Model\Evenement;
use App\Dao\ParticipantDao;
use App\Dao\UserDao;
use App\Model\Participant;
use App\Model\User;
use App\Service\AuthenticationService;


class FrontendController
{
    private $evenementDao;
    private $participantDao;
     private $userDao;
    private $authenticationService;
    private $twig;


    public function __construct($twig)
    {
        $this->evenementDao = new EvenementDao();
         $this->participantDao = new ParticipantDao();
         $this->userDao = new UserDao();
        $this->authenticationService = new AuthenticationService();
        $this->twig = $twig;
    }

    public function home()
    {

        $evenements = $this->evenementDao->findAll();

        echo $this->twig->render('home.html.twig', array('evenements' => $evenements));
    }

    public function detailEvenement($evenementId)
    {

        /** @var Evenement $evenement */
        $evenement = $this->evenementDao->findById($evenementId);
        $dateCloture = date_sub(date_create($evenement->getDateDebut()), new \DateInterval('P3D'));

        return $this->twig->render('detail.html.twig',
            array('evenement' => $evenement, 'dateCloture' => $dateCloture));
    }

    public function inscriptionEvenement($evenementId, $formData) {

        $participant=new Participant();
        $participant->setNom($formData->get("nom"));
        $participant->setPrenom($formData->get("prenom"));
         $participant->setDateNaissance(null);
         $participant->setSexe($formData->get("sexe"));
        $participant->setTelephone($formData->get("telephone"));
        $participant->setMail($formData->get("mail"));
        $participant->setProfession($formData->get("profession"));
        $participant->setLieuTravail($formData->get("lieu_travail"));

     $participantId= $this->participantDao->create($participant);

        $this->evenementDao->addParticipant($evenementId,$participantId);
       header("Location: /");
        die();
    }

      public function reviewEvenement($evenementId, $data) {

           $review = $this->reviewDao->findById($evenementId, $data);
         echo $this->twig->render('review.html.twig', array('evenement' => $evenement));


    }

     public function docEvenement($evenementId) {

          $evenement = $this->evenementDao->findById($evenementId);

        echo $this->twig->render('doc.html.twig', array('evenement' => $evenement));



    }

     public function inscriptionEvenementDisplay($evenementId) {

          $evenement = $this->evenementDao->findById($evenementId);

        echo $this->twig->render('inscription.html.twig',array('evenement' => $evenement));




    }

     public function evenements()
    {

        $evenements = $this->evenementDao->findAll();

        echo $this->twig->render('evenements.html.twig', array('evenements' => $evenements));
    }

     public function loginDisplay()
    {
        echo $this->twig->render('login.html.twig');
    }

    public function login($formData)
    {
        /** @var User $user */
        $user = $this->userDao->findByLogin($formData->login);
        if (password_verify($formData->password, $user->getPassword())) {
            $token = $this->authenticationService->createToken($user);
            $this->authenticationService->addTokenToCookie($token);
            header('Location: /admin');
            die();
        }
        header('Location: /login');
        die();
    }
}
