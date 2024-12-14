<?php

namespace App\Command;

use App\Data\Roles;
use App\Entity\Board;
use App\Entity\Permission;
use App\Entity\Post;
use App\Entity\Role;
use App\Entity\Topic;
use App\Entity\User;
use App\Helper\PermissionHelper;
use App\Repository\BoardRepository;
use App\Repository\PermissionRepository;
use App\Repository\PostRepository;
use App\Repository\RoleRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'seed:posts',
    description: 'Create mock data.',
)]
class SeedPostsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserRepository              $userRepository,
        private readonly TopicRepository              $topicRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $faker = Factory::create();

        $users = $this->userRepository->findAll();

        if (empty($users)) {
            $io->error('Create users first.');
            return Command::FAILURE;
        }

        $topics = $this->topicRepository->findAll();

        if (empty($topics)) {
            $io->error('Create topic first.');
            return Command::FAILURE;
        }

        $tempObjets = [];
        $batch = 5;
        foreach ($users as $key => $user) {
            for ($i = 0; $i < rand(5, 50); $i++) {
                $topic = $topics[rand(0, count($topics)-1)];
                $post = new Post();
                $post->setBody($faker->text);
                $user->addPost($post);
                $post->setAuthor($user);
                $topic->addPost($post);
                $post->setTopic($topic);
                $this->entityManager->persist($post);

                $tempObjets[] = $post;
            }

            if ($key % $batch == 0 && $key > 1) {
                $io->info('Flushing a batch of posts...');
                $this->entityManager->flush();
                $batch=+$batch;

                foreach($tempObjets as $tempObject) {
                    $this->entityManager->detach($tempObject);
                }

                $tempObjets = null;
                gc_enable();
                gc_collect_cycles();
            }

            $io->success("{$i} Created posts for {$user->getUsername()}");
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        return Command::SUCCESS;
    }
}
