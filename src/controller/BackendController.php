<?php

namespace App\Controller;

use App\Dao\EvenementDao;
use App\Dao\Inscription;
use App\Dao\ParticipantDao;
use App\Dao\Review;
use App\Dao\UserDao;
use App\Model\Evenement;
use App\Model\Participant;
use App\Service\AuthenticationService;
use App\Service\FileService;

class BackendController
{
    private $evenementDao;
    private $twig;
    private $participantDao;
    private $authenticationService;
    private $userDao;
    private $fileService;

    public function __construct($twig)
    {
        $this->evenementDao = new EvenementDao();
        $this->twig = $twig;
        $this->participantDao = new ParticipantDao();
        $this->authenticationService = new AuthenticationService();
        $this->userDao = new UserDao();
        $this->fileService = new FileService();

    }

    public function home()
    {
        $userId = $this->authenticationService->getConnectedUser();
        if (!$userId) {
            header('Location: /login');
            die();
        }
        $user = $this->userDao->findById($userId);
        $evenements = $this->evenementDao->findAll();

        echo $this->twig->render('admin/home.html.twig', array("evenements" => $evenements, 'user' => $user));
    }

    public function manageParticipantsDisplay($evenementId)
    {
        $userId = $this->authenticationService->getConnectedUser();
        if (!$userId) {
            header('Location: /login');
            die();
        }

        $participants = $this->participantDao->findAllByEvenementId($evenementId);

        echo $this->twig->render('admin/manageParticipantsDisplay.html.twig', array("participants" => $participants, "evenementId" => $evenementId));
    }


    public function newEvenementDisplay()

    {
        $userId = $this->authenticationService->getConnectedUser();
        if (!$userId) {
            header('Location: /login');
            die();
        }
        echo $this->twig->render('admin/newEvenement.html.twig');
    }

    public function newEvenement($formData, $files)
    {
        $userId = $this->authenticationService->getConnectedUser();
        if (!$userId) {
            header('Location: /login');
            die();
        }

        $file = $files->get("doc");
        $filename = null;
        if($file['size'] > 0){
            $extension = $this->fileService->getExtension($file['type']);
            $filename = '/uploads/' . uniqid() . $extension;
            move_uploaded_file($file['tmp_name'], __DIR__ . '/..' . $filename);
        }
        $evenement = new Evenement();
        $evenement->setNom($formData->get("nom"));
        $evenement->setLieu($formData->get("lieu"));
        $evenement->setDateCreation($formData->get("date_creation"));
        $evenement->setDateDebut($formData->get('date_debut'));
        $evenement->setDateFin($formData->get('date_fin'));
        $evenement->setContent($formData->get('content'));
        $evenement->setDoc($filename);

        $this->evenementDao->create($evenement);
        header("Location: /admin");
        die();
        //unlink($filename) --> supprimer le fichier
    }

    public function updateEvenementDisplay($id)

    {
        $userId = $this->authenticationService->getConnectedUser();
        if (!$userId) {
            header('Location: /login');
            die();
        }

        $evenement = $this->evenementDao->findById($id);

        echo $this->twig->render('admin/updateEvenement.html.twig', array("evenement" => $evenement));


    }

    public function updateEvenement($id, $formData, $files)
    {
        $userId = $this->authenticationService->getConnectedUser();
        if (!$userId) {
            header('Location: /login');
            die();
        }
        /** @var Evenement $evenement */
        $evenement = $this->evenementDao->findById($id);

        // On supprime le fichier
        $doc = $evenement->getDoc();
        if(isset($doc)){
            $filePath = getcwd() . '/src' . $evenement->getDoc();
            unlink($filePath);
        }

        // On ajoute le nouveau fichier
        $file = $files->get("doc");
        $extension = $this->fileService->getExtension($file['type']);
        $filename = '/uploads/' . uniqid() . $extension;
        move_uploaded_file($file['tmp_name'], __DIR__ . '/..' . $filename);

        $evenement->setId($id);
        $evenement->setNom($formData->get("nom"));
        $evenement->setLieu($formData->get("lieu"));
        $evenement->setDateCreation($formData->get("date_creation"));
        $evenement->setDateDebut($formData->get('date_debut'));
        $evenement->setDateFin($formData->get('date_fin'));
        $evenement->setContent($formData->get('content'));
        $evenement->setDoc($filename);
        $this->evenementDao->update($evenement);
        header("Location: /admin");
        die();

    }

    public function deleteEvenement($evenementId)

    {
        $userId = $this->authenticationService->getConnectedUser();
        if (!$userId) {
            header('Location: /login');
            die();
        }
        /** @var Evenement $evenement */
        $evenement = $this->evenementDao->findById($evenementId);
        $doc = $evenement->getDoc();
        if(isset($doc)){
            $filePath = getcwd() . '/src' . $evenement->getDoc();
            unlink($filePath);
        }
        $this->evenementDao->delete($evenementId);

        header("Location: /admin");
        die();
    }

    public function deleteEvenementParticipant($participantId, $evenementId)

    {
        $userId = $this->authenticationService->getConnectedUser();
        if (!$userId) {
            header('Location: /login');
            die();
        }

        $participant = $this->participantDao->deleteEvenementParticipant($participantId, $evenementId);


        header("Location: /participants/" . $evenementId);
        die();
    }

    public function updateParticipant($id, $formData, $evenementId)

    {
        $userId = $this->authenticationService->getConnectedUser();
        if (!$userId) {
            header('Location: /login');
            die();
        }

        $participant = new Participant();
        $participant->setId($id);
        $participant->setNom($formData->get("nom"));
        $participant->setPrenom($formData->get("prenom"));
        $participant->setDateNaissance($formData->get("date_naissance"));
        $participant->setSexe($formData->get("sexe"));
        $participant->setTelephone($formData->get("telephone"));
        $participant->setMail($formData->get("mail"));
        $participant->setProfession($formData->get("profession"));
        $participant->setLieuTravail($formData->get("lieu_travail"));

        $this->participantDao->update($participant);
        header("Location: /participants/" . $evenementId);
        die();

    }

    public function updateParticipantDisplay($evenementId, $id)

    {
        $userId = $this->authenticationService->getConnectedUser();
        if (!$userId) {
            header('Location: /login');
            die();
        }

        $participant = $this->participantDao->findById($id);


        echo $this->twig->render('admin/updateParticipant.html.twig', array("participant" => $participant, "evenementId" => $evenementId));


    }

    public function logout()

    {
        $this->authenticationService->logout();
        header('Location: /login');
        die();


    }
}
