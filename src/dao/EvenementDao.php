<?php

namespace App\Dao;

use App\Model\Evenement;

class EvenementDao extends BaseDao
{

    private $participantDao;

    public function __construct()
    {
        parent::__construct();
        $this->participantDao = new ParticipantDao();
    }

    public function findAll()
    {
        $query = $this->db->prepare("SELECT e.*, ep.participant_id 
                                        FROM evenement e 
                                        LEFT JOIN evenement_participant ep ON ep.evenement_id = e.id");
        $query->execute();
        /** @var array<App\Model\Evenement> $evenements */
        $evenements = $query->fetchAll(\PDO::FETCH_CLASS, 'App\Model\Evenement');
        $finalEvevements = array();
        /** @var Evenement $evenement */
        foreach ($evenements as $evenement) {
            if (isset($finalEvevements[$evenement->getId()])) {
                /** @var Evenement $e */
                $e = $finalEvevements[$evenement->getId()];
                $e->appendParticipant($this->participantDao->findById($evenement->getParticipantId()));
            } else {
                $evenement->appendParticipant($this->participantDao->findById($evenement->getParticipantId()));;
                $finalEvevements[$evenement->getId()] = $evenement;
            }
        }
        return array_values($finalEvevements);

    }

    public function findById($id)
    {
        $query = $this->db->prepare("SELECT e.*, ep.participant_id 
FROM evenement e 
LEFT JOIN evenement_participant ep ON ep.evenement_id = e.id
WHERE e.id=?");
        $query->execute(array($id));
        /** @var array<App\Model\Evenement> $evenements */
        $evenements = $query->fetchAll(\PDO::FETCH_CLASS, 'App\Model\Evenement');
        $finalEvevements = array();
        /** @var Evenement $evenement */
        foreach ($evenements as $evenement) {
            if (isset($finalEvevements[$evenement->getId()])) {
                /** @var Evenement $e */
                $e = $finalEvevements[$evenement->getId()];
                $e->appendParticipant($this->participantDao->findById($evenement->getParticipantId()));
            } else {
                $evenement->appendParticipant($this->participantDao->findById($evenement->getParticipantId()));;
                $finalEvevements[$evenement->getId()] = $evenement;
            }
        }
        return array_values($finalEvevements)[0];

    }


    /**
     * @param Evenement $evenement
     * @return bool
     */
    public function create($evenement)
    {
        $query = $this->db->prepare("
            INSERT INTO evenement(nom,date_creation,lieu,date_debut,date_fin,content,doc) 
            VALUES(?,Now(),?, STR_TO_DATE(?, '%Y-%m-%d'), STR_TO_DATE(?, '%Y-%m-%d'),?,?)
        ");

        $res = $query->execute([
            $evenement->getNom(),
            $evenement->getLieu(),
            $evenement->getDateDebut(),
            $evenement->getDateFin(),
            $evenement->getContent(),
            $evenement->getDoc(),
        ]);

        var_dump($query->errorInfo());
        return $res;
    }

    /**
     * @param Evenement $evenement
     * @return bool
     */
    public function update($evenement)
    {
        $query = $this->db->prepare("
          UPDATE evenement 
          SET nom=?,  
              lieu=?, 
              date_debut=STR_TO_DATE(?, '%Y-%m-%d'), 
              date_fin=STR_TO_DATE(?, '%Y-%m-%d'),
              content=?,
              doc=?
          WHERE id=?");


        $res = $query->execute([
            $evenement->getNom(),
            $evenement->getLieu(),
            $evenement->getDateDebut(),
            $evenement->getDateFin(),
            $evenement->getContent(),
            $evenement->getDoc(),
            $evenement->getId(),
        ]);

        var_dump($query->errorInfo());

        return $res;
    }

    public function delete($evenementId)
    {

        $query = $this->db->prepare("delete from evenement where id=?");
        $queryParticipants = $this->db->prepare("delete from evenement_participant where evenement_id=?");

        $query->execute(array($evenementId));
        $queryParticipants->execute(array($evenementId));

    }

    public function addParticipant($evenementId, $participantId)

    {
        $query = $this->db->prepare("insert into evenement_participant(evenement_id,participant_id) values(?,?)");

        return $query->execute(array($evenementId, $participantId));


    }


}
