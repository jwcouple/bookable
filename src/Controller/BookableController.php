<?php

namespace App\Controller;
use App\Repository\AvatarRepository;
use App\Repository\BookRepository;
use App\Repository\FollowedBookRepository;
use App\Repository\GenreRepository;
use App\Repository\LibraryRepository;
use App\Repository\LikedGenreRepository;
use App\Repository\UserRepository;
use Safe\Exceptions\PcreException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\String\u;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class BookableController extends AbstractController
{
    #[Route('/settings')]
    public function settings(GenreRepository $genreRepository, UserRepository $userRepository): Response
    {
        $bookGenres = $genreRepository->findAll();
        // TODO: split twig templates into file format 'controllername/methodname.html.twig' -> example: 'bookable/settings.html.twig'
        $stylesheets = ['settings.css'];
        $javascripts = ['settings.js'];

        return $this->render('setting.html.twig',[
            'username' => 'test_user',
            'stylesheets' => $stylesheets,
            'javascripts' => $javascripts,
            'bookgenres' => $bookGenres
        ]);
    }

    #[Route('/book/{book_id}', name:"book")]
    public function book(BookRepository $bookRepository, $book_id = null): Response
    {
        $stylesheets = ['book.css'];
        $javascripts = ['book.js'];
        if($book_id) {
            $book = $bookRepository->findOneBy(['id' => $book_id]);
            try {
                $bookTitle = u(preg_replace("/\([^)]+\)/", "", $book->getTitle()))->title(true);
            } catch (PcreException $e) {
                $bookTitle = $e;
            }
            return $this->render('book.html.twig', [
                'bookTitle' => $bookTitle,
                'stylesheets' => $stylesheets,
                'javascripts' => $javascripts,
                'book' => $book
            ]);
        } else {
            return new Response('Error: no book title detected');
        }
    }

    #[Route('/book/{book_id}/vote', name: "book_vote", methods: ['POST'])]
    public function vote(BookRepository $bookRepository, Request $request, EntityManagerInterface $entityManager, $book_id = null) : Response
    {
        // TODO: set and check liked books to enable/disable like system
        $book = $bookRepository->findOneBy(['id' => $book_id]);
        $direction = $request->request->get('direction', 'up');
        if($direction === 'up') {
            $book->setLikes($book->getLikes() + 1);
        } else {
            $book->setLikes($book->getLikes() - 1);
        }

        $entityManager->flush();
        return $this->redirectToRoute("book", [
            'book_id' => $book_id
        ]);
    }

    #[Route("/welcome", name: "welcome")]
    public function Welcome(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $stylesheets = ['welcome.css'];
        $javascripts = ['welcome.js'];
        return $this->render('welcome.html.twig',[
            'title'=>'Welcome!',
            'controller_name' => 'BookableController',
            'last_username' => $lastUsername,
            'error'         => $error,
            'javascripts' => $javascripts,
            'stylesheets' => $stylesheets
        ]);
    }

    #[Route("/home/{userID}", name: "home")]
    public function Home(AuthorRepository $authorRepository, LikedGenreRepository $likedGenreRepository,
                         UserRepository $userRepository, GenreRepository$genreRepository, BookRepository $bookRepository,
                         FollowedBookRepository $followedBookRepository, $userID = null): Response
    {
        $stylesheets = ['homev2.css'];
        $javascripts = ['home.js'];
        if ($userID) {
            $user = $userRepository->findOneBy(['id' => $userID]);
            $books = $bookRepository->findAll();
            $genre_id = $likedGenreRepository->findBy(['user'=>$userID]);
            $genres = $genreRepository->findBy(['id'=>$genre_id, ]);
            $genre_books =  $bookRepository->findBy(['genre'=>$genre_id] );
            $followed = $followedBookRepository->findBy(['user'=>$userID]);
            $followed_authors = [];
            $followed_books = $bookRepository->findBy(['id'=>$followed]);
            foreach ($followed_books as $followed_book){
                $current_author = $followed_book->getAuthor();
                $followed_authors[] = $current_author;
            }
            $popular_books = $bookRepository->findPopular();


            shuffle($books);
            shuffle($genre_books);
            return $this->render('home.html.twig', [
                'title' => 'Home!',
                'stylesheets' => $stylesheets,
                'javascripts' => $javascripts,
                'user' => $user,
                'books' => $books,
                'genres' => $genres,
                'genre_books' => $genre_books,
                'followed_authors' => $followed_authors,
                'popular_books' => $popular_books
            ]);
        }
        else{
            return new Response('Error: no matches detected');
        }
    }

    #[Route("/profile/{userID}")]
    public function Profile(AvatarRepository $avatarRepository, UserRepository $userRepository, $userID = null): Response {
        $stylesheets = ['profile.css'];

        if($userID) {
            $user = $userRepository->findOneBy(['id' => $userID]);
            $avatar = $avatarRepository->find(['id'=> $user->getAvatar()]);
            return $this->render('profile.html.twig', [
                'user' => $user,
                'avatar' => $avatar,
                'stylesheets' => $stylesheets,

            ]);
        } else {
            return new Response('Error: no book title detected');
        }

    }

    #[Route("/browsing", name: "browsing")]
    public function browsing(): Response {
        $stylesheets = ['browsing.css'];
        return $this->render('browsing.html.twig',[
            'title'=>'Browser',
            'stylesheets' => $stylesheets]);
    }

}