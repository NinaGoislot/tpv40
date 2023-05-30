<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Catalogue\Article;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Form\Extension\Core\Type\RatingType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DescriptionArticleController extends AbstractController {

    private $entityManager;
	private $logger;
	
	private $panier;
	
	public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)  {
		$this->entityManager = $entityManager;
		$this->logger = $logger;
	}
    
     /**
     * @Route("/descriptionArticle", name="descriptionArticle")
     */
    public function descriptionArticleAction(Request $request) {
        $evaluationsFilePath = '../data/evaluation.json';


		$session = $request->getSession() ;
		if (!$session->isStarted())
			$session->start() ;	
		$article = $this->entityManager->getReference("App\Entity\Catalogue\Article", $request->query->get("id"));

        if (file_exists($evaluationsFilePath)) {
            $evaluationsContent = file_get_contents($evaluationsFilePath);

            // Décoder le contenu JSON en tableau associatif
            $evaluations = json_decode($evaluationsContent, true);

            // Créer un tableau associatif pour stocker les moyennes et le nombre de votants par article
            $articleData = [];

            foreach ($evaluations as $evaluation) {
                foreach ($evaluation as $articleId => $data) {
                    $articleId = $data['idArticle'];
                    $average = $data['average'];
                    $nbUsersVotes = $data['nbUsersVotes'];
                    $userRating = $data['userRating'];
                    $hasVoted = $data['hasVoted'];
                }

                // Stocker les données dans le tableau associatif
                $articleData[$articleId] = [
                    'average' => round($average,2),
                    'nbUsersVotes' => $nbUsersVotes,
                    'userRating' => $userRating,
                    'hasVoted' => $hasVoted,
                ];
            }
        } else {
            // Le fichier evaluations.json n'existe pas
            $articleData = [];
        }
	
		return $this->render('description.article.html.twig', [
            'article' => $article,
             'articleData' => $articleData,
        ]);
    }  
    
      /**
     * @Route("/evaluationArticle", name="evaluationArticle", methods={"POST"})
     */
    public function evaluationArticleAction(Request $request): Response {
        $evaluationsFilePath = '../data/evaluation.json';
        $articleId = $request->request->get('articleId');
        $rating = $request->request->get('rating');
    
        $article = $this->entityManager->getReference("App\Entity\Catalogue\Article", $articleId);
    
        // Charger les évaluations du fichier JSON
        $evaluations = json_decode(file_get_contents($evaluationsFilePath), true);
    
        $evaluationIndex = null;

        if (file_exists($evaluationsFilePath)) {
        
            foreach ($evaluations as $index => $evaluation) {
                if (isset($evaluation[$articleId])) {
                    $evaluationIndex = $index;
                    break;
                }
            }

            var_dump ($evaluationIndex !== null);
            var_dump ($evaluationIndex);
        
            if ($evaluationIndex !== null) {
                $articleEvaluation = $evaluations[$evaluationIndex][$articleId];
                $currentAverage = $articleEvaluation['average'];
                $totalUsers = $articleEvaluation['nbUsersVotes'] + 1;
                $hasVoted = $articleEvaluation['hasVoted'];

                
                var_dump ($currentAverage);
            } else {
                $currentAverage = 0;
                $totalUsers = 0;
                $hasVoted = false;
            }
            $newAverageRating = round((($currentAverage * ($totalUsers - 1)) + $rating) / $totalUsers, 2);
            var_dump ($newAverageRating);
        
            // Mettre à jour les valeurs
            $evaluations[$evaluationIndex][$articleId] = [
                'idArticle' => $articleId,
                'average' => $newAverageRating,
                'nbUsersVotes' => $totalUsers,
                'userRating' => $rating,
                'hasVoted' => true,
            ];

            unlink($evaluationsFilePath);
        
            $jsonContent = json_encode($evaluations);
            file_put_contents($evaluationsFilePath, $jsonContent);
        }
        
        return $this->render('description.article.html.twig', [
            'article' => $article,
            'articleData' => $evaluations[$evaluationIndex],
        ]);
    }
}
