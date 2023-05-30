<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\ApaiIO;
use ApaiIO\Operations\Search as AmazonSearch;
use ApaiIO\Request\GuzzleRequestWithoutKeys;
use GuzzleHttp\Client;

use DeezerAPI\Search as DeezerSearch;

use App\Entity\Catalogue\Livre;
use App\Entity\Catalogue\ArticleCollaboration;
use App\Entity\Catalogue\Musique;
use App\Entity\Catalogue\Piste;
use App\Entity\Catalogue\Article;

class AppFixtures extends Fixture
{


    public function load(ObjectManager $manager): void
    {
		$evaluationsFilePath = './data/evaluation.json';

		if (file_exists($evaluationsFilePath)) {
			unlink($evaluationsFilePath);
		}

		if (count($manager->getRepository("App\Entity\Catalogue\Article")->findAll()) == 0) {
			$conf = new GenericConfiguration();
			$client = new Client();
			$request = new GuzzleRequestWithoutKeys($client);

			try {
				/*$conf
					->setCountry('de')
					->setAccessKey(AWS_API_KEY)
					->setSecretKey(AWS_API_SECRET_KEY)
					->setAssociateTag(AWS_ASSOCIATE_TAG);*/
				$conf
					->setCountry('fr')
					->setRequest($request) ;
			} catch (\Exception $e) {
				echo $e->getMessage();
			}
			$apaiIO = new ApaiIO($conf);

			$search = new AmazonSearch();
			$search->setCategory('Electronics');
			$keywords = 'external battery' ;
		
			
			$search->setKeywords($keywords);
			
			$search->setResponseGroup(array('Offers','ItemAttributes','Images'));

			//-------------------------------------------------------------------------------------------------------------
			//---------------------------------------- ARTCILES BATTERIE EXTERNE ------------------------------------------
			//-------------------------------------------------------------------------------------------------------------

			$formattedResponse = $apaiIO->runOperation($search);
			file_put_contents("amazonResponse.xml",$formattedResponse) ;
			$xml = simplexml_load_string($formattedResponse);
			if ($xml !== false) {
				foreach ($xml->children() as $child_1) {
					if ($child_1->getName() === "Items") {
						foreach ($child_1->children() as $child_2) {
							if ($child_2->getName() === "Item") {
								if ($child_2->ItemAttributes->ProductGroup->__toString() === "Electronic") {
									$entityElectronic = new Article();
									$entityElectronic->setId($child_2->ASIN);
									$entityElectronic->setTitre($child_2->ItemAttributes->Title);
									$entityElectronic->setPrix($child_2->OfferSummary->LowestNewPrice->Amount/100.0); 
									$entityElectronic->setDisponibilite(1);
									$entityElectronic->setImage($child_2->LargeImage->URL);
									$entityElectronic->setCategory($keywords);
									$manager->persist($entityElectronic);
									$manager->flush();
								}

								 // Génère des valeurs aléatoire pour la moyenne et le nombre de votant
								 $average = mt_rand(10, 50) / 10.0;
								 $nbUsersVotes = mt_rand(5, 200);
								 $idArticle = (string) $child_2->ASIN;
								 
								 $evaluation = [];
 
								 // Création du tableau data d'un article
								 $evaluation[$idArticle] = [
									 'idArticle' => $idArticle,
									 'average' => $average,
									 'nbUsersVotes' => $nbUsersVotes,
									 'userRating' => 0,
									 'hasVoted' => false
								 ];
 
								 // Ajout du tableau de l'article au tableau global
								 $evaluations[] = $evaluation;
							}
						}
					}
				}
				// Ajout du tableau global dans le fichier JSON
                $jsonContent = json_encode($evaluations);
                file_put_contents($evaluationsFilePath, $jsonContent);


			//-------------------------------------------------------------------------------------------------------------
			//----------------------------------------- ARTCILES DE COLLABORATION -----------------------------------------
			//-------------------------------------------------------------------------------------------------------------
				$entityCollaboration = new ArticleCollaboration();
				$entityCollaboration->setId("GEN202301");
				$entityCollaboration->setTitre("Coque Blanche Jeu Genshin Impact");
				$entityCollaboration->setEntreprise("miHoYo");
				$entityCollaboration->setPersonnage("Noelle");
				$entityCollaboration->setUnivers("Genshin Impact");
				$entityCollaboration->setCollection("Collaboration Genshin Impact 2023");
				$entityCollaboration->setPrix("14.90");
				$entityCollaboration->setDisponibilite(1);
				$entityCollaboration->setImage("/images/collaboration_genshin.jpg");
				$entityCollaboration->setCategory("Collaboration");
				$manager->persist($entityCollaboration);
				$entityCollaboration = new ArticleCollaboration();
				$entityCollaboration->setId("TIG2365942");
				$entityCollaboration->setTitre("Coque Huawei X20 lite");
				$entityCollaboration->setArtiste("Tigerfight");
				$entityCollaboration->setUnivers("illustration tigre végétation");
				$entityCollaboration->setPrix("8.99");
				$entityCollaboration->setDisponibilite(1);
				$entityCollaboration->setImage("/images/collaboration_tigerfight.webp");
				$entityCollaboration->setCategory("Collaboration");
				$manager->persist($entityCollaboration);
				$entityCollaboration = new ArticleCollaboration();
				$entityCollaboration->setId("1LUC458264");
				$entityCollaboration->setTitre("Coque Samsung dessin illustration");
				$entityCollaboration->setArtiste("LuckyMe");
				$entityCollaboration->setUnivers("illustration minimaliste");
				$entityCollaboration->setPrix("6.99");
				$entityCollaboration->setDisponibilite(1);
				$entityCollaboration->setImage("/images/collaboration_luckyme.webp");
				$entityCollaboration->setCategory("Collaboration");
				$manager->persist($entityCollaboration);
				$manager->flush();
			}


			//-------------------------------------------------------------------------------------------------------------
			//--------------------------------------- ARTCILES COQUES DE TELEPHONE ----------------------------------------
			//-------------------------------------------------------------------------------------------------------------

			$conf = new GenericConfiguration();
            $client = new Client();
            $request = new GuzzleRequestWithoutKeys($client);

            $evaluationData = [];

            try {
                $conf
                    ->setCountry('fr')
                    ->setRequest($request);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
            $apaiIO = new ApaiIO($conf);

            $search = new AmazonSearch();
            $search->setCategory('Electronics');
            $keywords = 'phonecase';

            $search->setKeywords($keywords);

            $search->setResponseGroup(array('Offers', 'ItemAttributes', 'Images'));

            $formattedResponse = $apaiIO->runOperation($search);
            file_put_contents("amazonResponse.xml", $formattedResponse);
            $xml = simplexml_load_string($formattedResponse);
            if ($xml !== false) {
                $evaluations = [];
                if (file_exists($evaluationsFilePath)) {
                    $jsonContent = file_get_contents($evaluationsFilePath);
                    if (!empty($jsonContent)) {
                        $evaluations = json_decode($jsonContent, true);
                    }
                }
                foreach ($xml->children() as $child_1) {
                    if ($child_1->getName() === "Items") {
                        foreach ($child_1->children() as $child_2) {
                            if ($child_2->getName() === "Item") {
                                if ($child_2->ItemAttributes->ProductGroup->__toString() === "Electronic") {
                                    $entityElectronic = new Article();
                                    $entityElectronic->setId($child_2->ASIN);
                                    $entityElectronic->setTitre($child_2->ItemAttributes->Title);
                                    $entityElectronic->setPrix($child_2->OfferSummary->LowestNewPrice->Amount / 100.0);
                                    $entityElectronic->setDisponibilite(1);
                                    $entityElectronic->setImage($child_2->LargeImage->URL);
									$entityElectronic->setCategory($keywords);
                                    $manager->persist($entityElectronic);
                                    $manager->flush();
                                }

                                // Génère des valeurs aléatoire pour la moyenne et le nombre de votant
                                $average = mt_rand(10, 50) / 10.0;
                                $nbUsersVotes = mt_rand(5, 200);
								$idArticle = (string) $child_2->ASIN;
								
								$evaluation = [];

                                // Création du tableau data d'un article
                                $evaluation[$idArticle] = [
                                    'idArticle' => $idArticle,
                                    'average' => $average,
                                    'nbUsersVotes' => $nbUsersVotes,
									'userRating' => 0,
									'hasVoted' => false
                                ];

                                // Ajout du tableau de l'article au tableau global
                                $evaluations[] = $evaluation;
                            }
                        }
                    }
                }

                // Ajout du tableau global dans le fichier JSON
                $jsonContent = json_encode($evaluations);
                file_put_contents($evaluationsFilePath, $jsonContent);
            }
        }
    }
}
