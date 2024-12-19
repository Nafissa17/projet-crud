<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Entity\Producteur;
use App\Form\SerieType;
use App\Repository\SerieRepository; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\FileException;

class SerieController extends AbstractController
{
    // Route pour afficher la liste des séries
    #[Route('/series', name: 'series_index')]
    public function index(SerieRepository $serieRepository): Response
    {
        $series = $serieRepository->findAll();

        return $this->render('serie/index.html.twig', [
            'series' => $series,
        ]);
    }

    // Route pour rechercher des séries avec des critères
    #[Route('/series/search', name: 'series_search')]
    public function search(Request $request, SerieRepository $serieRepository): Response
    {
        $criteria = $request->query->all();

        $queryBuilder = $serieRepository->createQueryBuilder('s');

        if (!empty($criteria['genre'])) {
            $queryBuilder->join('s.genres', 'g')
                         ->andWhere('g.nom = :genre')
                         ->setParameter('genre', $criteria['genre']);
        }

        if (!empty($criteria['nombreSaisons'])) {
            $queryBuilder->andWhere('s.nombreSaisons = :nombreSaisons')
                         ->setParameter('nombreSaisons', $criteria['nombreSaisons']);
        }

        if (!empty($criteria['producteur'])) {
            $queryBuilder->join('s.producteurs', 'p')
                         ->andWhere('p.nom = :producteur')
                         ->setParameter('producteur', $criteria['producteur']);
        }

        $series = $queryBuilder->getQuery()->getResult();

        return $this->render('serie/search.html.twig', [
            'series' => $series,
            'criteria' => $criteria,
        ]);
    }


    // Route pour créer une nouvelle série
    #[Route('/series/new', name: 'series_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $serie = new Serie();
        $form = $this->createForm(SerieType::class, $serie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion du fichier d'image
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
                $serie->setImage($newFilename);
            }

            // Gestion des producteurs
            $producteurNom = $form->get('producteurNom')->getData();
            if ($producteurNom) {
                $producteur = new Producteur();
                $producteur->setNom($producteurNom);
                $em->persist($producteur);
                $serie->addProducteur($producteur);
            }

            // Ajout des genres
            foreach ($form->get('genres')->getData() as $genre) {
                $serie->addGenre($genre);
            }

            $em->persist($serie);
            $em->flush();

            $this->addFlash('success', 'Série ajoutée avec succès !');

            return $this->redirectToRoute('series_index');
        }

        return $this->render('serie/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    // Route pour afficher une série en détail
    #[Route('/series/{id}', name: 'series_show', requirements: ['id' => '\d+'])]
    public function show(Serie $serie): Response
    {
        return $this->render('serie/show.html.twig', [
            'serie' => $serie,
        ]);
    }


    // Route pour éditer une série existante
    #[Route('/series/{id}/edit', name: 'series_edit', requirements: ['id' => '\\d+'])]
    public function edit(Serie $serie, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SerieType::class, $serie);
        $oldImage = $serie->getImage(); 

       
        if ($serie->getProducteurs()->count() > 0) {
            $producteurs = $serie->getProducteurs()->map(fn($producteur) => $producteur->getNom())->toArray();
            $form->get('producteurNom')->setData(implode(', ', $producteurs));
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Gestion de l'image
                /** @var UploadedFile $imageFile */
                $imageFile = $form->get('image')->getData();

                if ($imageFile) {
                    // Supprimer l'ancienne image
                    if ($oldImage) {
                        $oldImagePath = $this->getParameter('images_directory') . '/' . $oldImage;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    // Générer un nom unique pour la nouvelle image
                    $newFilename = uniqid() . '_' . time() . '.' . $imageFile->guessExtension();

                    // Déplacer la nouvelle image
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );

                    // Mettre à jour l'entité avec le nouveau nom d'image
                    $serie->setImage($newFilename);
                } elseif ($oldImage) {
                    // Si l'image n'a pas été modifiée, on conserve l'ancienne image
                    $serie->setImage($oldImage);
                }


                // Mise à jour des producteurs
                $producteurs = array_map('trim', explode(',', $form->get('producteurNom')->getData() ?? ''));
                foreach ($serie->getProducteurs() as $existingProducteur) {
                    if (!in_array($existingProducteur->getNom(), $producteurs)) {
                        $serie->removeProducteur($existingProducteur);
                    }
                }
                foreach ($producteurs as $producteurNom) {
                    if (!empty($producteurNom)) {
                        $existingProducteur = $serie->getProducteurs()
                            ->filter(fn($p) => strtolower($p->getNom()) === strtolower($producteurNom))
                            ->first();
                            
                        if (!$existingProducteur) {
                            $newProducteur = new Producteur();
                            $newProducteur->setNom($producteurNom);
                            $em->persist($newProducteur);
                            $serie->addProducteur($newProducteur);
                        }
                    }
                }

                // Mise à jour des genres
                foreach ($serie->getGenres() as $existingGenre) {
                    if (!$form->get('genres')->getData()->contains($existingGenre)) {
                        $serie->removeGenre($existingGenre);
                    }
                }
                foreach ($form->get('genres')->getData() as $newGenre) {
                    if (!$serie->getGenres()->contains($newGenre)) {
                        $serie->addGenre($newGenre);
                    }
                }

                $em->flush();
                $this->addFlash('success', 'Série modifiée avec succès !');
                return $this->redirectToRoute('series_index');

            } catch (FileException $e) {
                // En cas d'erreur lors du téléchargement de l'image
                $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image : ' . $e->getMessage());
            } catch (\Exception $e) {
                // En cas d'erreur générale
                $this->addFlash('error', 'Une erreur est survenue lors de la modification de la série : ' . $e->getMessage());
            }
        }

        return $this->render('serie/edit.html.twig', [
            'serie' => $serie,
            'form' => $form->createView(),
        ]);
    }

    

    // Route pour supprimer une série
    #[Route('/series/{id}/delete', name: 'series_delete', methods: ['POST'], requirements: ['id' => '\\d+'])]
    public function delete(Request $request, Serie $serie, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $serie->getId(), $request->request->get('_token'))) {
            $em->remove($serie);
            $em->flush();

            $this->addFlash('success', 'Série supprimée avec succès !');
        }

        return $this->redirectToRoute('series_index');
    }


    // Route pour supprimer l'image d'une série
    #[Route('/series/{id}/remove-image', name: 'series_remove_image')]
    public function removeImage(Serie $serie, EntityManagerInterface $em): Response
    {
        $oldImage = $serie->getImage();

        if ($oldImage) {
            // Supprimer l'image du dossier
            $oldImagePath = $this->getParameter('images_directory') . '/' . $oldImage;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }

            // Supprimer le nom de l'image dans l'entité
            $serie->setImage(null);
            $em->flush();

            $this->addFlash('success', 'Image supprimée avec succès !');
        }

        return $this->redirectToRoute('series_edit', ['id' => $serie->getId()]);
    }
}
