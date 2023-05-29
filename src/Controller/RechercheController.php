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
        echo '<script>console.log("test");</script>';

        $evaluationsFilePath = '../data/evaluation.json';
        echo '<script>console.log("'.$evaluationsFilePath.'");</script>';
        echo '<script>console.log("'.file_exists($evaluationsFilePath).'");</script>';

        if (file_exists($evaluationsFilePath)) {
            $evaluationsContent = file_get_contents($evaluationsFilePath);
            echo '<script>console.log('.json_encode($evaluationsContent).');</script>';

            // Décoder le contenu JSON en tableau associatif
            $evaluations = json_decode($evaluationsContent, true);
            echo '<script>console.log('.json_encode($evaluations).');</script>';

            // Créer un tableau associatif pour stocker les moyennes et le nombre de votants par article
            $articleData = [];

            foreach ($evaluations as $evaluation) {
                $articleId = $evaluation['idArticle'];
                $average = $evaluation['average'];
                $nbUsersVotes = $evaluation['nbUsersVotes'];

                // Stocker les données dans le tableau associatif
                $articleData[$articleId] = [
                    'average' => $average,
                    'nbUsersVotes' => $nbUsersVotes,
                ];
            }
        } else {
            // Le fichier evaluations.json n'existe pas
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

    /*public function test() {
       
        // Chargement du contenu du fichier JSON
        $evaluationJson = file_get_contents('evaluation.json');
        $evaluationData = json_decode($evaluationJson, true);

        // Passer les évaluations à la vue recherche.html.twig
        return $this->render('recherche.html.twig', [
            'articles' => $articles,
            'evaluationData' => $evaluationData,
        ]);
    }*/


       /**
     * @Route("/recherche", name="recherche")
     */
   /* public function evaluationArticles(SerializerInterface $serializer): Response
    {
        $query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a");
		$articles = $query->getResult();

        $jsonContent = file_get_contents('evaluation.json');
        $evaluations = $serializer->deserialize($jsonContent, 'array', 'json');

        return $this->render('recherche.html.twig', [
            'evaluations' => $evaluations,
            'articles' => $articles,
        ]);
    }*/
}
