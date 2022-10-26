<?php

declare(strict_types=1);

namespace App\User\Command\SingUp;

use App\Infrastructure\AsService;
use App\Infrastructure\Flush;
use App\Infrastructure\Security\CreatePasswordHasher;
use App\User\Domain\User;
use App\User\Domain\UserEmail;
use App\User\Domain\UserId;
use App\User\Domain\UserPassword;
use App\User\Domain\UserRole;
use App\User\Domain\Users;
use App\User\Notification\NewPasswordMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\String\ByteString;

#[AsService]
final class SignUp
{
    public function __construct(
        private readonly Users $users,
        private readonly Flush $flush,
        private readonly MessageBusInterface $messageBus,
        private readonly CreatePasswordHasher $createPasswordHasher,
    ) {
    }

    public function __invoke(SignUpCommand $signUpCommand): void
    {
        $user = $this->users->findByEmail($signUpCommand->email);
        if ($user !== null) {
            throw new UserAlreadyExistException('Пользователь с таким email уже существует');
        }

        $userEmail = new UserEmail($signUpCommand->email);

        $plaintextPassword = ByteString::fromRandom(10)->toString();
        $password = new UserPassword($plaintextPassword, $this->createPasswordHasher);

        $user = new User(new UserId(), $userEmail, UserRole::User, $password);

        $this->users->add($user);

        ($this->flush)();

        $this->messageBus->dispatch(new NewPasswordMessage($plaintextPassword, $userEmail->value));
    }
}
