<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
		$session = $request->getSession() ;
		if (!$session->isStarted())
			$session->start() ;	
		$article = $this->entityManager->getReference("App\Entity\Catalogue\Article", $request->query->get("id"));
	
		return $this->render('description.article.html.twig', [
            'article' => $article,
        ]);
    }

    /*public function showDescriptionArticle($id)
    {
        // Chargement du contenu du fichier JSON
        $evaluationJson = file_get_contents('evaluation.json');
        $evaluationData = json_decode($evaluationJson, true);

        // Recherche de l'évaluation correspondant à l'article spécifié par son ID
        $evaluation = null;
        foreach ($evaluationData as $item) {
            if ($item['idArticle'] === $id) {
                $evaluation = $item;
                break;
            }
        }

        // Passer l'évaluation à la vue description.article.html.twig
        return $this->render('description.article.html.twig', [
            'evaluation' => $evaluation,
        ]);
    }





    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)  {
		$this->entityManager = $entityManager;
	}

     /**
     * @Route("/descriptionArticle", name="descriptionArticle")
     */
    /*public function descriptionArticleAction(Request $request) {
        $query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a");
		$article = $query->getResult();
		return $this->render('description.article.html.twig', [
            'article' => $article,
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
