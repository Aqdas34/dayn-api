<?php

namespace App\Model\Request;

use App\Enum\DebtCollectionConfirmationStatus;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ConfirmDebtRequestDto
{
    public function __construct(
        #[Assert\Choice(
            callback: [self::class, 'getCases'],
            message: "The status must be either 'accepted', 'rejected' or 'cancelled'."
        )]
        public string  $status,
        #[SerializedName('confirmation_status_message')]
        public ?string $confirmationStatusMessage = null,
    )
    {
    }

    public static function getCases(): array
    {
        $cases = [];
        foreach (DebtCollectionConfirmationStatus::cases() as $case) {
            $cases[] = $case->value;
        }

        return $cases;
    }
}