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
                $articleId = $evaluation['idArticle'];
                $average = $evaluation['average'];
                $nbUsersVotes = $evaluation['nbUsersVotes'];
                $userRating = $evaluation['userRating'];
                $hasVoted = $evaluation['hasVoted'];

                // Stocker les données dans le tableau associatif
                $articleData[$articleId] = [
                    'average' => $average,
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
        foreach ($evaluations as $index => $evaluation) {
            if ($evaluation['idArticle'] === $articleId) {
                $evaluationIndex = $index;
                break;
            }
        }
    
        if ($evaluationIndex !== null) {
            $currentAverage = $evaluations[$evaluationIndex]['average'];
            $totalUsers = $evaluations[$evaluationIndex]['nbUsersVotes'];
        } else {
            $currentAverage = 0;
            $totalUsers = 0;
        }
    
        $totalUsers++;
        $newAverageRating = (($currentAverage * $totalUsers) + $rating) / $totalUsers;
    
        // Mettre à jour les valeurs
        $evaluations[$evaluationIndex] = [
            'idArticle' => $articleId,
            'average' => $newAverageRating,
            'nbUsersVotes' => $totalUsers,
            'userRating' => $rating,
            'hasVoted' => true,
        ];
    
        $article->setHasVoted(true);
    
        $jsonContent = json_encode($evaluations);
        file_put_contents($evaluationsFilePath, $jsonContent);
    
        return $this->render('description.article.html.twig', [
            'article' => $article,
            'articleData' => $evaluations,
        ]);
    }
    

/**
 * @Route("/article/{id}/evaluation", name="article_evaluation", methods={"POST"})
 */
public function submitEvaluation(Request $request, $id): JsonResponse
{
    // Récupérer la note sélectionnée par l'utilisateur depuis la requête
    $userRating = $request->request->getInt('userRating');

    // Lire les données actuelles d'évaluation depuis le fichier evaluations.json
    $filesystem = new Filesystem();
    $evaluationsPath = '../data/evaluation.json';
    $evaluationData = json_decode($filesystem->read($evaluationsPath), true);

    // Mettre à jour les données d'évaluation en fonction de la nouvelle évaluation
    $evaluationData[$id]['average'] =
        ($evaluationData[$id]['average'] * $evaluationData[$id]['nbUserVotes'] + $userRating) /
        ($evaluationData[$id]['nbUserVotes'] + 1);
    $evaluationData[$id]['nbUserVotes'] += 1;

    // Sauvegarder les nouvelles données dans le fichier evaluations.json
    $filesystem->write($evaluationsPath, json_encode($evaluationData));

    // Retourner une réponse JSON indiquant que l'évaluation a été soumise avec succès
    return new JsonResponse(['success' => true]);
}



    /**
     * @Route("/evaluationArticletest", name="evaluationArticletest")
     */
    /*public function ajouterLigneAction(Request $request) {
        $articleId = $request->request->get('articleId');
        $rating = $request->request->get('rating');

        // Charger les évaluations du fichier JSON
        $evaluations = json_decode(file_get_contents('evaluation.json'), true);

        // Vérifier si l'article existe dans les évaluations
        if (isset($evaluations[$articleId])) {
            $currentAverage = $evaluations[$articleId]['average'];
            $totalUsers = $evaluations[$articleId]['nbUsersVotes'];
        } else {
            $currentAverage = 0;
            $totalUsers = 0;
        }

        $totalUsers++;
        $newAverageRating = (($currentAverage * $totalUsers) + $rating) / $totalUsers;

        // Mettre à jour les évaluations dans le tableau
        $evaluations[$articleId] = [
            'average' => $newAverageRating,
            'nbUsersVotes' => $totalUsers + 1,
            'hasVoted' => true,
            'userRating' => $rating
        ];

        if (file_exists($evaluationsFilePath)) {
			unlink($evaluationsFilePath);
		}

        $jsonContent = json_encode($evaluations);
        file_put_contents($evaluationsFilePath, $jsonContent);

		return $this->render('description.article.html.twig', [
            'articleData' => $this->$evaluations,
        ]);
    }*/



    

    /**
     * @Route("/article/{id}", name="description_article")
     */
    /*public function show(Request $request, SessionInterface $session, $id): Response {

        // Récupérer l'article à partir de l'ID
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        // Vérifier si l'article existe
        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }

        // Récupérer l'évaluation et le nombre de votes à partir du fichier JSON
        $evaluationsAverage = $this->getEvaluations();

        // Vérifier si l'utilisateur a déjà voté pour cet article
        $nbHasVoted = $this->getUserVoteStatus($session, $id);

        // Passer l'article, les évaluations et l'état du vote à la vue
        return $this->render('description.article.html.twig', [
            'article' => $article,
            'evaluationsAverage' => $evaluationsAverage,
            'nbHasVoted' => $nbHasVoted,
        ]);
    }*/

     /**
     * @Route("/article/{id}/vote", name="article_vote", methods={"POST"})
     */
    /*public function vote(Request $request, Article $article): JsonResponse {
        $evaluationData = file_get_contents('evaluation.json');
        $evaluationData = json_decode($evaluationData, true);

        // Recherche l'évaluation de l'article dans les données
        $evaluation = null;
        foreach ($evaluationData as $data) {
            if ($data['id'] === $article->getId()) {
                $evaluation = $data;
                break;
            }
        }

        if (!$evaluation) {
            // L'article n'a pas d'évaluation dans le fichier JSON
            return new JsonResponse(['error' => 'Evaluation not found'], 404);
        }

        $rating = $request->request->get('rating');
        if (!is_numeric($rating) || $rating < 0 || $rating > 5) {
            // La note de vote est invalide
            return new JsonResponse(['error' => 'Invalid rating'], 400);
        }

        // Mettre à jour l'évaluation avec le nouveau vote
        $newTotalVotes = $evaluation['nombreVotes'] + 1;
        $newAverageRating = (($evaluation['moyenne'] * $evaluation['nombreVotes']) + $rating) / $newTotalVotes;

        $evaluation['moyenne'] = round($newAverageRating, 1);
        $evaluation['nombreVotes'] = $newTotalVotes;

        // Mettre à jour les données dans le fichier JSON
        foreach ($evaluationData as &$data) {
            if ($data['id'] === $article->getId()) {
                $data = $evaluation;
                break;
            }
        }

        $jsonContent = json_encode($evaluationData);
        file_put_contents('evaluation.json', $jsonContent);

        return new JsonResponse(['success' => true]);
    }


    // Autres actions spécifiques à la page description.article.html.twig
    
   

    private function getUserVoteStatus(SessionInterface $session, $articleId) {
        // Vérifier si l'utilisateur a déjà voté pour cet article en utilisant la session
        $votedArticles = $session->get('voted_articles', []);
        return in_array($articleId, $votedArticles);
    }*/
}
