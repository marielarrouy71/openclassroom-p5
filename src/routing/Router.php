<?php

namespace App\Routing;

use App\Controller\BackendController;
use App\Controller\FrontendController;
use Klein\Klein;
use MimeType\MimeType;


class Router
{

    private $klein;
    private $frontendController;
    private $backendController;


    public function __construct($twig)
    {
        $this->klein = new Klein();
        $this->frontendController = new FrontendController($twig);
        $this->backendController = new BackendController($twig);
    }

    public function run()
    {
        $this->klein->respond('GET', '/', function () {
            return $this->frontendController->home();
        });

        $this->klein->respond('GET', '/evenements/[:evenementId]', function ($request) {
            return $this->frontendController->detailEvenement($request->evenementId);
        });

        $this->klein->respond('POST', '/evenements/[:evenementId]/inscription', function ($request) {
            return $this->frontendController->inscriptionEvenement($request->evenementId, $request->paramsPost());
        });

        $this->klein->respond('POST', '/evenements/[:evenementId]/review', function ($request) {
            return $this->frontendController->reviewEvenement($request->evenementId, $request->paramsPost());
        });

        $this->klein->respond('GET', '/admin', function () {
            return $this->backendController->home();
        });

        $this->klein->respond('GET', '/participants/[:evenementId]', function ($request) {
            return $this->backendController->manageParticipantsDisplay($request->evenementId);
        });

        $this->klein->respond('GET', '/newEvenement', function () {
            return $this->backendController->newEvenementDisplay();
        });

        $this->klein->respond('POST', '/newEvenement', function ($request) {
            return $this->backendController->newEvenement($request->paramsPost(), $request->files());
        });

        $this->klein->respond('GET', '/updateEvenement/[:id]', function ($request) {
            return $this->backendController->updateEvenementDisplay($request->id);
        });

        $this->klein->respond('POST', '/updateEvenement/[:id]', function ($request) {
            return $this->backendController->updateEvenement($request->id, $request->paramsPost(), $request->files());
        });

        $this->klein->respond('POST', '/deleteEvenement/[:id]', function ($request) {
            return $this->backendController->deleteEvenement($request->id);
        });

        $this->klein->respond('GET', '/evenements/[:id]/inscription', function ($request) {
            return $this->frontendController->inscriptionEvenementDisplay($request->id);
        });


        $this->klein->respond('POST', '/deleteParticipant/[:evenementId]/[:participantId]', function ($request) {
            return $this->backendController->deleteEvenementParticipant($request->participantId, $request->evenementId);
        });

        $this->klein->respond('POST', '/updateParticipant/[:id]', function ($request) {
            return $this->backendController->updateParticipant($request->id, $request->paramsPost(), $_GET["evenementId"]);
        });
        $this->klein->respond('GET', '/updateParticipant/[:id]', function ($request) {

            return $this->backendController->updateParticipantDisplay($_GET["evenementId"], $request->id);
        });

        $this->klein->respond('GET', '/evenements', function () {
            return $this->frontendController->evenements();
        });

        $this->klein->respond('GET', '/login', function () {
            return $this->frontendController->loginDisplay();
        });

        $this->klein->respond('POST', '/login', function ($request) {
            return $this->frontendController->login($request->paramsPost());
        });

        $this->klein->respond('GET', '/logout', function ($request) {
            return $this->backendController->logout();
        });


        $this->klein->respond('/uploads/[*]', function ($request, $response, $service, $app) {
            $filename = getcwd() . '/src' . $request->pathname();
            $response->header('Content-Type', MimeType::getType($filename));
            return file_get_contents($filename);
        });

        /*  $type = \MimeType\MimeType::getType('my-file.pdf');*/
        $this->klein->dispatch();
    }

}
