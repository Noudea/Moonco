<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Server;
use App\Form\MovieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ServerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/server", name="server")
 */
class ServerController extends AbstractController
{
    //regex only integrer to infinite
    /**
     * @Route("/{id}", name="id", requirements={"id"="[1-9][0-9]*"})
     */
    public function index(EntityManagerInterface $entityManager,Server $server,UserInterface $user): Response
    {
        // $server = $entityManager->getRepository(Server::class)->find($id);
        //on récupère l'utilisateur
        //verifie si l'utilisateur est bien inscrit au serveur et que le serveur existe
        if($server && $server->getUsers()->contains($user))
        {

            $serverMovies = $server->getMovies();
            dump($serverMovies);

            return $this->render('server/index.html.twig', [
                'controller_name' => 'ServerController',
            ]);

        } else {
            //je gere ici si le serv n'existe pas ou que l'utilisateur n'a pas l'accès
        }
        dump($server);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request,EntityManagerInterface $entityManager)
    {
        //creation de l'instance server
        $server = new Server();
        //creation du formulaire
        $serverForm = $this->createForm(ServerType::class, $server);
        //recuperes les données du formulaire
        $serverForm->handleRequest($request);
        if ($serverForm->isSubmitted() && $serverForm->isValid()) {
            if ($serverForm->isValid()) {
                //on récupere l'utilisateur
                $user = $this->getUser();
                $server->addAdmin($user);
                //on ajoute l'utilisateur en tant que admin dans le serveur
                $user->addAdminServer($server);
                //on ajoute au server l'utilisateur
                $server->addUser($user);
                
                $entityManager->persist($server);
                $entityManager->flush(); // ajoute en BDD
                dump($user->getAdminServers());
                
                //creer le dossier lié au server
                $filesystem = new Filesystem();
                $filesystem->mkdir("../files/".$server->getId(), 0777);
                $filesystem->mkdir("../files/" . $server->getId()."/movies", 0777);
                $filesystem->mkdir("../files/" . $server->getId() . "/series", 0777);
                dump($server->getId());
                //return $this->redirectToRoute('myfirst_template');
            } else {
                var_dump('not valid');
            }
        }

        return $this->render(
            "server/create.html.twig",
            [
                'controller_name' => 'ServerController Create Route',
                "serverForm" => $serverForm->createView()
            ]
        );
    }

    /**
     * @Route("/addMovie/{id}", name="addMovie")
     */
    public function addMovie(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, Server $server) {

        $movie = new Movie();
        $movieForm = $this->createForm(MovieType::class, $movie);
        $movieForm->handleRequest($request);

        if ($movieForm->isSubmitted()) {
            $movieFile = $movieForm->get('link')->getData();

            if ($movieFile) {
                $originalFilename = pathinfo($movieFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $movieFile->guessExtension();

                //Move the file to the directory where brochures are stored
                try {
                    $movieFile->move(
                        "../files/" . $server->getId() . "/movies",
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                // $product->setBrochureFilename($newFilename);
            }

            $movie->setLink("../files/" . $server->getId() . "/movies"."/". $newFilename);
            $movie->setRelation($server);
            $entityManager->persist($movie);
            $entityManager->flush(); // ajoute en BDD
            dump($movie);
        }
        return $this->render(
            "server/addMovie.html.twig",
            [
                'controller_name' => 'ServerController Create Route',
                "movieForm" => $movieForm->createView()
            ]
        );
    }

}
