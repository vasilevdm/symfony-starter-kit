<?php

declare(strict_types=1);

namespace Dev\Maker\Doctrine;

use App\Infrastructure\AsService;
use Dev\Maker\CustomGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @internal
 *
 * Создает сущность и репозиторий в нужной директории - App\ModuleName\Domain, добавляет суффикс к репозиторию (s)
 * Используется только в наших Maker'ах
 */
final class EntityClassGeneratorForModule
{
    public function __construct(
        private readonly CustomGenerator $generator,
        private readonly DoctrineHelper $doctrineHelper,
    ) {
    }

    public function generateEntityClass(string $moduleName, ClassNameDetails $entityClassDetails, bool $generateRepositoryClass = true): string
    {
        $repoClassDetails = $this->generator->createClassNameDetails(
            $entityClassDetails->getRelativeName(),
            $moduleName.'\\Domain\\',
            's'
        );

        $tableName = $this->doctrineHelper->getPotentialTableName($entityClassDetails->getFullName());

        $useStatements = new UseStatementGenerator([
            Uuid::class,
            ['Doctrine\\ORM\\Mapping' => 'ORM'],
        ]);

        $entityPath = $this->generator->generateClass(
            $entityClassDetails->getFullName(),
            'domain/Entity.tpl.php',
            [
                'use_statements' => $useStatements,
                'repository_class_name' => $repoClassDetails->getShortName(),
                'api_resource' => false,
                'broadcast' => false,
                'should_escape_table_name' => $this->doctrineHelper->isKeyword($tableName),
                'table_name' => $tableName,
            ]
        );

        if ($generateRepositoryClass) {
            $this->generateRepository(
                $repoClassDetails->getFullName(),
                $entityClassDetails->getFullName(),
            );
        }

        return $entityPath;
    }

    public function generateRepository(string $repositoryClass, string $entityClass): void
    {
        $shortEntityClass = Str::getShortClassName($entityClass);
        $entityAlias = strtolower($shortEntityClass[0]);

        $passwordUserInterfaceName = UserInterface::class;

        if (interface_exists(PasswordAuthenticatedUserInterface::class)) {
            $passwordUserInterfaceName = PasswordAuthenticatedUserInterface::class;
        }

        $interfaceClassNameDetails = new ClassNameDetails($passwordUserInterfaceName, 'Symfony\Component\Security\Core\User');

        $useStatements = new UseStatementGenerator([
            AsService::class,
            EntityManagerInterface::class,
            Uuid::class,
        ]);

        $this->generator->generateClass(
            $repositoryClass,
            'domain/Repository.tpl.php',
            [
                'use_statements' => $useStatements,
                'entity_class_name' => $shortEntityClass,
                'entity_alias' => $entityAlias,
                'with_password_upgrade' => false,
                'password_upgrade_user_interface' => $interfaceClassNameDetails,
                'include_example_comments' => true,
            ]
        );
    }
}
