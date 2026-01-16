<?php

namespace App\Command;

use App\Repository\UtilisateurRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:test-password')]
class TestPasswordCommand extends Command
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UtilisateurRepository $utilisateurRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $admin = $this->utilisateurRepository->findOneBy(['email' => 'admin@karhabti.com']);
        
        if (!$admin) {
            $output->writeln('❌ Admin not found!');
            return Command::FAILURE;
        }

        $output->writeln('✅ Admin found: ' . $admin->getEmail());
        $output->writeln('Class: ' . get_class($admin));
        $output->writeln('Roles: ' . json_encode($admin->getRoles()));
        $output->writeln('Password hash: ' . substr($admin->getPassword(), 0, 50) . '...');
        
        $isPasswordValid = $this->passwordHasher->isPasswordValid($admin, 'admin123');
        $output->writeln('Password "admin123" valid: ' . ($isPasswordValid ? '✅ YES' : '❌ NO'));

        return Command::SUCCESS;
    }
}