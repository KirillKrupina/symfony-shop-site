<?php

namespace App\Command;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(name: 'app:add-user', description: 'Create user')]
class AddUserCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $userPasswordHasher;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;


    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepository,
        string $name = null
    )
    {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', 'email', InputArgument::REQUIRED, 'Email')
            ->addOption('password', 'password', InputArgument::REQUIRED, 'Password')
            ->addOption('isAdmin', '', InputArgument::OPTIONAL, 'If set the user is created as an administrator', 0);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        /*
         * The Stopwatch component provides a consistent way to measure
         * execution time of certain parts of code so that you don't
         * constantly have to parse microtime by yourself.
         */
        $stopWatch = new Stopwatch();
        $stopWatch->start('add-user-command');

        $email = $input->getOption('email');
        $password = $input->getOption('password');
        $isAdmin = $input->getOption('isAdmin');

        $io->title('Add User Command');
        $io->text([
            'Enter information'
        ]);

        if (!$email) {
            $email = $io->ask('Email');
        }
        if (!$password) {
            $password = $io->askHidden('Password (hidden)');
        }
        if (!$isAdmin) {
            $question = new Question('Is admin? (yes/no)', 'no');
            $answer = $io->askQuestion($question);
            $isAdmin = $answer === 'yes' ? 1 : 0;
            $isAdmin = boolval($isAdmin);
        }

        try {
            $user = $this->createUser($email, $password, $isAdmin);
        } catch (RuntimeException $exception) {
            $io->comment($exception->getMessage());
            return Command::FAILURE;
        }

        $successMsg = printf(
            '%s was success created: %s',
            $isAdmin ? 'Administrator' : 'User',
            $email
        );
        $io->success($successMsg);

        $event = $stopWatch->stop('add-user-command');
        $stopWatchMsg = sprintf(
            'New user\'s id: %s / Elapsed time: %.2f ms / Consumed memory: %.2f MB',
            $user->getId(),
            $event->getDuration(),
            $event->getMemory() / 1000 / 1000
        );
        $io->comment($stopWatchMsg);

        return Command::SUCCESS;
    }

    /**
     * @param string $email
     * @param string $password
     * @param bool $isAdmin
     * @return User
     */
    private function createUser(string $email, string $password, bool $isAdmin): User
    {
        $isUserExist = $this->userRepository->findOneBy(['email' => $email]);

        if ($isUserExist) {
            throw new RuntimeException('User already exist');
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles([$isAdmin ? 'ROLE_ADMIN' : 'ROLE_USER']);
        $encodedPassword = $this->userPasswordHasher->hashPassword($user,$password);
        $user->setPassword($encodedPassword);
        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
