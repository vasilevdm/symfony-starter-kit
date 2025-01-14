<?php declare(strict_types=1);
echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

<?php echo $use_statements; ?>

/**
 * Ручка получения информации <?php echo $entity_classname."\n"; ?>
 */
#[IsGranted('ROLE_USER')]
#[Route('<?php echo $route_path; ?>')]
#[AsController]
final class InfoAction
{
    public function __invoke(<?php echo $entity_classname; ?> $entity): <?php echo $entity_classname."\n"; ?>
    {
        return $entity;
    }
}
