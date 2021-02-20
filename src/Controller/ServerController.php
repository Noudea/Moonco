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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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

            $serverMovies = $server->getMovies()->toArray();
            dump($serverMovies);
            dump($user->getServers()->toArray());
            dump($server);

            $userServers = $user->getServers()->toArray();
            return $this->render('server/index.html.twig', [
                'controller_name' => 'ServerController',
                'movies' => $serverMovies,
                'userServers' => $userServers,
                'server' => $server,
            ]);

        } else {
            //je gere ici si le serv n'existe pas ou que l'utilisateur n'a pas l'accès
        }
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request,EntityManagerInterface $entityManager, UserInterface $user)
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
        $userServers = $user->getServers()->toArray();
        return $this->render(
            "server/create.html.twig",
            [
                'controller_name' => 'ServerController Create Route',
                "serverForm" => $serverForm->createView(),
                'userServers' => $userServers
            ]
        );
    }

    /**
     * @Route("/{id}/addMovie", name="addMovie")
     */
    public function addMovie(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, Server $server, UserInterface $user) {

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
            $server->addMovie($movie);
            $entityManager->persist($movie);
            $entityManager->flush(); // ajoute en BDD
            dump($movie);
        }

        $userServers = $user->getServers()->toArray();
        return $this->render(
            "server/addMovie.html.twig",
            [
                'controller_name' => 'ServerController Create Route',
                "movieForm" => $movieForm->createView(),
                'userServers' => $userServers
            ]
        );
    }

    /**
     * @Route("/{id}/movie/{movieId}", name="movie")
     * @ParamConverter("movie", options={"id" = "movieId"})
     */
    public function video(Request $request,Server $server,Movie $movie) {
        
        $file = $movie->getLink();
        $response = new BinaryFileResponse($file);
        $response->headers->set('Content-Type', 'video/mp4');
        BinaryFileResponse::trustXSendfileTypeHeader();


        // $response->setContentDisposition(
        //     ResponseHeaderBag::DISPOSITION_INLINE,
        //     $file
        // );
        $response->prepare($request);
        $response->send();
        return $movie;
    }

}
