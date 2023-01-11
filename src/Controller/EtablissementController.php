<?php

namespace App\Controller;



use App\Repository\EtablissementRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtablissementController extends AbstractController
{
    private EtablissementRepository $etablissementRepository;
    private UserRepository $userRepository;

    /**
     * @param EtablissementRepository $etablissementRepository
     */
    public function __construct(EtablissementRepository $etablissementRepository, UserRepository $userRepository)
    {
        $this->etablissementRepository = $etablissementRepository;
        $this->userRepository = $userRepository;
    }


    #[Route('/etablissements', name: 'app_etablissements')]
    public function getEtablissements(Request $request,PaginatorInterface $paginator): Response
    {
        $etablissements = $paginator->paginate(
            $this->etablissementRepository->findBy(["actif" => true],["nom" => "ASC",]), /* query NOT result */
            $request->query->getInt('page', 1),12);

        return $this->render('etablissement/index.html.twig', [
            'etablissements' => $etablissements,

        ]);
    }

    #[Route('/etablissements/{slug}', name: 'app_etablissements_slug')]
    public function getDetailsEtablissement(Request $request,$slug): Response
    {

        return $this->render('etablissement/etablissement.html.twig', [
            'etablissement' => $this->etablissementRepository->findOneBy(["slug" => $slug]),

        ]);
    }


    #[Route('/etablissements/favori/{slug}', name: 'app_etablissements_favori')]
    public function getFavoriEtablissement(EntityManagerInterface $entityManager, Request $request,$slug): Response
    {

        $user = $this->userRepository->find($this->getUser());
        $etablissement = $this->etablissementRepository->findOneBy(["slug" => $slug]);

        if ($user->getFavoris()->contains($etablissement)){
            $user->removeFavori($etablissement);
        } else {
            $user->addFavori($etablissement);
        }
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute("app_etablissements_slug",["slug" => $slug]);

    }

    #[Route('/etablissements/favoris', name: 'app_etablissements_favoris',priority: 1)]
    public function getFavorisEtablissement(Request $request,PaginatorInterface $paginator): Response
    {

        /*$userEmail = $this->getUser()->getUserIdentifier();
        $user = $this->userRepository->find(["email" => $userEmail]);
        $etablissements = $this->getUser();
        $etablissements = $this->
        */


        $etablissements = $paginator->paginate(
            //$this->userRepository->find($user)->getFavoris(),
            $this->etablissementRepository->findBy(["actif" => true],["nom" => "ASC",]),
            $request->query->getInt('page', 1),12);

        return $this->render('etablissement/etablissementsFavoris.html.twig', [
            'etablissements' => $etablissements,

        ]);


    }




}
