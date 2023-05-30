<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Psr\Log\LoggerInterface;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Serializer\SerializerInterface;

use App\Entity\Catalogue\Livre;
use App\Entity\Catalogue\Musique;
use App\Entity\Catalogue\Piste;

class RechercheController extends AbstractController
{
	private $entityManager;
	private $logger;
	
	public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)  {
		$this->entityManager = $entityManager;
		$this->logger = $logger;
	}
	
    /**
     * @Route("/afficheRecherche", name="afficheRecherche")
     */
    public function afficheRechercheAction(Request $request)
    {
        $query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a");
        $articles = $query->getResult();

        $evaluationsFilePath = '../data/evaluation.json';

        if (file_exists($evaluationsFilePath)) {
            $evaluationsContent = file_get_contents($evaluationsFilePath);

            // Décoder le contenu JSON en tableau associatif
            $evaluations = json_decode($evaluationsContent, true);

            $articleData = [];

            foreach ($evaluations as $evaluation) {
                foreach ($evaluation as $articleId => $data) {
                $average = $data['average'];
                $nbUsersVotes = $data['nbUsersVotes'];
                $articleData[$articleId] = [
                    'average' => $average,
                    'nbUsersVotes' => $nbUsersVotes,
                ];
            }
            }
        } else {
            $articleData = [];
        }

        return $this->render('recherche.html.twig', [
            'articles' => $articles,
            'articleData' => $articleData,
        ]);
    }

    /**
     * @Route("/afficheRecherche/phonecase", name="phonecase")
     */
    public function affichePhoneCase(Request $request)
    {
        $query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a WHERE a.category='phonecase'");
        $articles = $query->getResult();

        $evaluationsFilePath = '../data/evaluation.json';

        if (file_exists($evaluationsFilePath)) {
            $evaluationsContent = file_get_contents($evaluationsFilePath);

            // Décoder le contenu JSON en tableau associatif
            $evaluations = json_decode($evaluationsContent, true);
            
            $articleData = [];

            foreach ($evaluations as $evaluation) {
                foreach ($evaluation as $articleId => $data) {
                    $average = $data['average'];
                    $nbUsersVotes = $data['nbUsersVotes'];
                    $articleData[$articleId] = [
                        'average' => $average,
                        'nbUsersVotes' => $nbUsersVotes,
                    ];
                }
            }
        } else {
        
            $articleData = [];
        }

        return $this->render('recherche.html.twig', [
            'articles' => $articles,
            'articleData' => $articleData,
        ]);
    }

     /**
     * @Route("/afficheRecherche/externalbattery", name="externalbattery")
     */
    public function afficheExternalBattery(Request $request)
    {
        $query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a WHERE a.category='external battery'");
        $articles = $query->getResult();

        $evaluationsFilePath = '../data/evaluation.json';

        if (file_exists($evaluationsFilePath)) {
            $evaluationsContent = file_get_contents($evaluationsFilePath);

            // Décoder le contenu JSON en tableau associatif
            $evaluations = json_decode($evaluationsContent, true);
            
            $articleData = [];

            foreach ($evaluations as $evaluation) {
                foreach ($evaluation as $articleId => $data) {
                    $average = $data['average'];
                    $nbUsersVotes = $data['nbUsersVotes'];
                    $articleData[$articleId] = [
                        'average' => $average,
                        'nbUsersVotes' => $nbUsersVotes,
                    ];
                }
            }
        } else {
        
            $articleData = [];
        }

        return $this->render('recherche.html.twig', [
            'articles' => $articles,
            'articleData' => $articleData,
        ]);
    }

     /**
     * @Route("/afficheRecherche/collaboration", name="collaboration")
     */
    public function afficheCollaboration(Request $request)
    {
        $query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a WHERE a.category='Collaboration'");
        $articles = $query->getResult();

        $evaluationsFilePath = '../data/evaluation.json';

        if (file_exists($evaluationsFilePath)) {
            $evaluationsContent = file_get_contents($evaluationsFilePath);

            // Décoder le contenu JSON en tableau associatif
            $evaluations = json_decode($evaluationsContent, true);
            
            $articleData = [];

            foreach ($evaluations as $evaluation) {
                foreach ($evaluation as $articleId => $data) {
                    $average = $data['average'];
                    $nbUsersVotes = $data['nbUsersVotes'];
                    $articleData[$articleId] = [
                        'average' => $average,
                        'nbUsersVotes' => $nbUsersVotes,
                    ];
                }
            }
        } else {
        
            $articleData = [];
        }

        return $this->render('recherche.html.twig', [
            'articles' => $articles,
            'articleData' => $articleData,
        ]);
    }


    
	
    /**
     * @Route("/afficheRechercheParMotCle", name="afficheRechercheParMotCle")
     */
    public function afficheRechercheParMotCleAction(Request $request)
    {
        echo 'test 2';
		//$query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a "
		//										  ." where a.titre like :motCle");
		//$query->setParameter("motCle", "%".$request->query->get("motCle")."%") ;
		$query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a "
												  ." where a.titre like '%".addslashes($request->query->get("motCle"))."%'");
		$articles = $query->getResult();
		return $this->render('recherche.html.twig', [
            'articles' => $articles,
        ]);
    }
}
