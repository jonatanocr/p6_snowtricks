<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use DateInterval;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordEncoder;

    public const REFERENCE = 'user';

    /**
     * AppFixture constructor.
     * @param UserPasswordHasherInterface $passwordEncoder
     */
    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        // create 2 users
        for ($i = 1; $i < 3; $i++) {
            ${"user$i"} = new User();
            ${"user$i"}->setUsername('username' . $i);
            ${"user$i"}->setEmail('fake_email_' . $i . '@gmail.com');
            ${"user$i"}->setPassword(
                $this->passwordEncoder->hashPassword(${"user$i"}, 'password')
            );
            ${"user$i"}->setVerified(1);
            $hash = $random_hex = bin2hex(random_bytes(18));
            ${"user$i"}->setHash($hash);
            $manager->persist(${"user$i"});
        }

        // create 4 categories
        for ($i = 0; $i < 4; $i++) {
            ${"category$i"} = new Category();
            switch ($i) {
                case 0:
                    ${"category$i"}->setName('Grab');
                    break;
                case 1:
                    ${"category$i"}->setName('Rotation');
                    break;
                case 2:
                    ${"category$i"}->setName('Flip');
                    break;
                case 3:
                    ${"category$i"}->setName('Slide');
                    break;
            }
            $manager->persist(${"category$i"});
        }

        // create 10 tricks
        for ($i = 0; $i < 10; $i++) {
            ${"trick$i"} = new Trick();
            switch ($i) {
                case 0:
                    ${"trick$i"}->setAuthor(${"user1"});
                    ${"trick$i"}->setName('Mute');
                    ${"trick$i"}->setCreatedDate(new \DateTime());
                    ${"trick$i"}->setDescription('Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.');
                    ${"trick$i"}->setCategory(${"category0"});
                    break;
                case 1:
                    ${"trick$i"}->setAuthor(${"user1"});
                    ${"trick$i"}->setName('180');
                    ${"trick$i"}->setCreatedDate(new \DateTime());
                    ${"trick$i"}->setDescription('Désigne un demi-tour, soit 180 degrés d\'angle');
                    ${"trick$i"}->setCategory(${"category1"});
                    break;
                case 2:
                    ${"trick$i"}->setAuthor(${"user1"});
                    ${"trick$i"}->setName('Front flip');
                    ${"trick$i"}->setCreatedDate(new \DateTime());
                    ${"trick$i"}->setDescription('Rotation en avant');
                    ${"trick$i"}->setCategory(${"category2"});
                    break;
                case 3:
                    ${"trick$i"}->setAuthor(${"user1"});
                    ${"trick$i"}->setName('Nose slide');
                    ${"trick$i"}->setCreatedDate(new \DateTime());
                    ${"trick$i"}->setDescription('Slide avec l\'avant de la planche sur la barre');
                    ${"trick$i"}->setCategory(${"category3"});
                    break;
                case 4:
                    ${"trick$i"}->setAuthor(${"user1"});
                    ${"trick$i"}->setName('Indy');
                    ${"trick$i"}->setCreatedDate(new \DateTime());
                    ${"trick$i"}->setDescription('Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.');
                    ${"trick$i"}->setCategory(${"category0"});
                    break;
                case 5:
                    ${"trick$i"}->setAuthor(${"user2"});
                    ${"trick$i"}->setName('360');
                    ${"trick$i"}->setCreatedDate(new \DateTime());
                    ${"trick$i"}->setDescription('Trois six pour un tour complet.');
                    ${"trick$i"}->setCategory(${"category1"});
                    break;
                case 6:
                    ${"trick$i"}->setAuthor(${"user2"});
                    ${"trick$i"}->setName('Back flip');
                    ${"trick$i"}->setCreatedDate(new \DateTime());
                    ${"trick$i"}->setDescription('Rotation en arrière.');
                    ${"trick$i"}->setCategory(${"category2"});
                    break;
                case 7:
                    ${"trick$i"}->setAuthor(${"user2"});
                    ${"trick$i"}->setName('Tail slide');
                    ${"trick$i"}->setCreatedDate(new \DateTime());
                    ${"trick$i"}->setDescription('Slide avec l\'arrière de la planche sur la barre.');
                    ${"trick$i"}->setCategory(${"category3"});
                    break;
                case 8:
                    ${"trick$i"}->setAuthor(${"user2"});
                    ${"trick$i"}->setName('Stalefish');
                    ${"trick$i"}->setCreatedDate(new \DateTime());
                    ${"trick$i"}->setDescription('Saisie de la carre backside de la planche entre les deux pieds avec la main arrière.');
                    ${"trick$i"}->setCategory(${"category0"});
                    break;
                case 9:
                    ${"trick$i"}->setAuthor(${"user2"});
                    ${"trick$i"}->setName('1080');
                    ${"trick$i"}->setCreatedDate(new \DateTime());
                    ${"trick$i"}->setDescription('Appelé aussi big foot pour trois tours complet.');
                    ${"trick$i"}->setCategory(${"category1"});
                    break;
            }
            $manager->persist(${"trick$i"});
        }

        // create 7 comments
        $j = 10;
        for ($i = 0; $i < 7; $i++) {
            $date_comment = new \DateTime();
            $date_comment->modify('-'.$j.' day');
            $j--;
            $comment = new Comment();
            switch ($i) {
                case 0:
                    $comment->setAuthor(${"user1"});
                    $comment->setTrick(${"trick0"});
                    $comment->setCreatedDate($date_comment);
                    $comment->setContent('Comment ' . $i . ' for trick 1');
                    break;
                case 1:
                    $comment->setAuthor(${"user2"});
                    $comment->setTrick(${"trick0"});
                    $comment->setCreatedDate($date_comment);
                    $comment->setContent('Comment ' . $i . ' for trick 1');
                    break;
                case 2:
                    $comment->setAuthor(${"user1"});
                    $comment->setTrick(${"trick0"});
                    $comment->setCreatedDate($date_comment);
                    $comment->setContent('Comment ' . $i . ' for trick 1');
                    break;
                case 3:
                    $comment->setAuthor(${"user2"});
                    $comment->setTrick(${"trick0"});
                    $comment->setCreatedDate($date_comment);
                    $comment->setContent('Comment ' . $i . ' for trick 1');
                    break;
                case 4:
                    $comment->setAuthor(${"user2"});
                    $comment->setTrick(${"trick0"});
                    $comment->setCreatedDate($date_comment);
                    $comment->setContent('Comment ' . $i . ' for trick 1');
                    break;
                case 5:
                    $comment->setAuthor(${"user1"});
                    $comment->setTrick(${"trick0"});
                    $comment->setCreatedDate($date_comment);
                    $comment->setContent('Comment ' . $i . ' for trick 1');
                    break;
                case 6:
                    $comment->setAuthor(${"user1"});
                    $comment->setTrick(${"trick1"});
                    $comment->setCreatedDate($date_comment);
                    $comment->setContent('Comment 1 for trick 2');
                    break;
            }
            $manager->persist($comment);
        }

        $manager->flush();
    }
}
